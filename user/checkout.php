<?php
require_once '../includes/user-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];
$error = '';

// Fetch cart items
$stmt = mysqli_prepare($conn, "SELECT tc.cart_id, tc.quantity, cam.camera_id, cam.camera_name, cam.price_per_day, cam.price_per_hour, cam.quantity AS stock
                                FROM tbl_cart tc
                                JOIN tbl_cameras cam ON tc.camera_id = cam.camera_id
                                WHERE tc.user_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $ci_cart_id, $ci_quantity, $ci_camera_id, $ci_camera_name, $ci_price_per_day, $ci_price_per_hour, $ci_stock);

$cart_items = array();
while (mysqli_stmt_fetch($stmt)) {
    $cart_items[] = array(
        'cart_id'        => $ci_cart_id,
        'quantity'       => $ci_quantity,
        'camera_id'      => $ci_camera_id,
        'camera_name'    => $ci_camera_name,
        'price_per_day'  => $ci_price_per_day,
        'price_per_hour' => $ci_price_per_hour,
        'stock'          => $ci_stock,
    );
}
mysqli_stmt_close($stmt);

// Redirect if cart is empty
if (count($cart_items) === 0) {
    header('Location: cart.php');
    exit;
}

// Fetch user's saved phone number to pre-fill the form
$user_stmt = mysqli_prepare($conn, "SELECT phone FROM tbl_users WHERE user_id = ?");
mysqli_stmt_bind_param($user_stmt, 'i', $user_id);
mysqli_stmt_execute($user_stmt);
mysqli_stmt_bind_result($user_stmt, $phone_number);
mysqli_stmt_fetch($user_stmt);
mysqli_stmt_close($user_stmt);

// Per-day and per-hour totals across the whole cart (used by JS calculator)
$total_price_per_day = 0;
$total_price_per_hour = 0;
foreach ($cart_items as $item) {
    $qty = min($item['quantity'], $item['stock']);
    $total_price_per_day += $item['price_per_day'] * $qty;
    $total_price_per_hour += $item['price_per_hour'] * $qty;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup_datetime = isset($_POST['pickup_datetime']) ? trim($_POST['pickup_datetime']) : '';
    $return_datetime = isset($_POST['return_datetime']) ? trim($_POST['return_datetime']) : '';
    $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';

    $now = date('Y-m-d H:i:s');

    if ($pickup_datetime === '' || $return_datetime === '' || $phone_number === '') {
        $error = 'Please fill in all fields.';
    } elseif (!ctype_digit($phone_number) || strlen($phone_number) !== 10) {
        $error = 'Please enter a valid 10-digit phone number.';
    } elseif ($pickup_datetime < $now) {
        $error = 'Pickup time cannot be in the past.';
    } elseif ($return_datetime <= $pickup_datetime) {
        $error = 'Return time must be after pickup time.';
    } elseif ((strtotime($return_datetime) - strtotime($pickup_datetime)) < 3600) {
        $error = 'Minimum rental duration is 1 hour.';
    } else {
        // Decide Hourly vs Daily automatically based on duration
        $duration = calculate_rental_duration($pickup_datetime, $return_datetime);
        $rental_type = $duration['type'];
        $total_hours = $duration['hours'];
        $total_days = $duration['days'];

        // Recalculate total from current DB prices (never trust client)
        $total_amount = 0;
        foreach ($cart_items as $item) {
            $qty = min($item['quantity'], $item['stock']);
            if ($rental_type === 'Hourly') {
                $total_amount += $item['price_per_hour'] * $qty * $total_hours;
            } else {
                $total_amount += $item['price_per_day'] * $qty * $total_days;
            }
        }

        $booking_number = generate_booking_number($conn);
        $pickup_date_only = date('Y-m-d', strtotime($pickup_datetime));
        $return_date_only = date('Y-m-d', strtotime($return_datetime));

        mysqli_query($conn, 'START TRANSACTION');

        try {
            $insert_booking = mysqli_prepare($conn, "INSERT INTO tbl_bookings (user_id, phone_number, booking_number, pickup_date, return_date, rental_type, total_days, total_hours, total_amount, booking_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
            mysqli_stmt_bind_param($insert_booking, 'issssssid', $user_id, $phone_number, $booking_number, $pickup_date_only, $return_date_only, $rental_type, $total_days, $total_hours, $total_amount);
            mysqli_stmt_execute($insert_booking);
            $booking_id = mysqli_insert_id($conn);
            mysqli_stmt_close($insert_booking);

            $insert_item = mysqli_prepare($conn, "INSERT INTO tbl_booking_items (booking_id, camera_id, price_per_day, price_per_hour, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $update_stock = mysqli_prepare($conn, "UPDATE tbl_cameras SET quantity = quantity - ? WHERE camera_id = ? AND quantity >= ?");

            foreach ($cart_items as $item) {
                $qty = min($item['quantity'], $item['stock']);
                if ($rental_type === 'Hourly') {
                    $subtotal = $item['price_per_hour'] * $qty * $total_hours;
                } else {
                    $subtotal = $item['price_per_day'] * $qty * $total_days;
                }
                mysqli_stmt_bind_param($insert_item, 'iiddid', $booking_id, $item['camera_id'], $item['price_per_day'], $item['price_per_hour'], $qty, $subtotal);
                mysqli_stmt_execute($insert_item);

                // Decrement stock, guarded against overselling under concurrent checkouts
                mysqli_stmt_bind_param($update_stock, 'iii', $qty, $item['camera_id'], $qty);
                mysqli_stmt_execute($update_stock);

                if (mysqli_stmt_affected_rows($update_stock) === 0) {
                    throw new Exception('Insufficient stock for ' . $item['camera_name']);
                }
            }
            mysqli_stmt_close($insert_item);
            mysqli_stmt_close($update_stock);

            // Clear the user's cart
            $clear_cart = mysqli_prepare($conn, "DELETE FROM tbl_cart WHERE user_id = ?");
            mysqli_stmt_bind_param($clear_cart, 'i', $user_id);
            mysqli_stmt_execute($clear_cart);
            mysqli_stmt_close($clear_cart);

            mysqli_commit($conn);

            header('Location: payment.php?booking_id=' . $booking_id);
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = 'Something went wrong while creating your booking. Please try again.';
        }
    }
}

$page_title = 'Checkout';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <h2 class="section-title">Checkout</h2>

    <?php if ($error !== ''): ?>
        <div class="form-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="checkout-summary">
        <?php foreach ($cart_items as $item): ?>
            <div class="checkout-row">
                <span><?php echo htmlspecialchars($item['camera_name']); ?> &times; <?php echo (int)min($item['quantity'], $item['stock']); ?></span>
                <span>&#8377;<?php echo number_format($item['price_per_day'], 2); ?>/day &middot; &#8377;<?php echo number_format($item['price_per_hour'], 2); ?>/hr</span>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="checkout.php" class="admin-form" id="checkoutForm">
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="tel" id="phone_number" name="phone_number"
                   value="<?php echo htmlspecialchars($phone_number); ?>"
                   pattern="[0-9]{10}" maxlength="10" title="Enter a valid 10-digit phone number" required>
        </div>

        <div class="form-group">
            <label for="pickup_datetime">Pickup Date &amp; Time</label>
            <input type="datetime-local" id="pickup_datetime" name="pickup_datetime" class="date-input" required>
        </div>

        <div class="form-group">
            <label for="return_datetime">Return Date &amp; Time</label>
            <input type="datetime-local" id="return_datetime" name="return_datetime" class="date-input" required>
        </div>

        <div class="booking-summary">
            <div class="summary-row">
                <span>Rental Type:</span>
                <span id="rentalType">-</span>
            </div>
            <div class="summary-row">
                <span>Duration:</span>
                <span id="duration">0</span>
            </div>
            <div class="summary-total">
                <span>Total Amount:</span>
                <span>&#8377;<span id="totalAmount">0</span></span>
            </div>
        </div>

        <button type="submit" class="btn-primary" id="confirmBtn" disabled>Confirm Booking</button>
    </form>
</div>

<script>
(function () {
    var pickupInput = document.getElementById('pickup_datetime');
    var returnInput = document.getElementById('return_datetime');
    var phoneInput = document.getElementById('phone_number');
    var rentalTypeDisplay = document.getElementById('rentalType');
    var durationDisplay = document.getElementById('duration');
    var amountDisplay = document.getElementById('totalAmount');
    var confirmBtn = document.getElementById('confirmBtn');
    var pricePerDay = <?php echo (float) $total_price_per_day; ?>;
    var pricePerHour = <?php echo (float) $total_price_per_hour; ?>;

    // Set min pickup to now, formatted for datetime-local
    var now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    pickupInput.min = now.toISOString().slice(0, 16);

    function isValidPhone(value) {
        return /^[0-9]{10}$/.test(value);
    }

    function updateSummary() {
        var pickup = new Date(pickupInput.value);
        var ret = new Date(returnInput.value);
        var diffMs = ret - pickup;
        var validDates = pickupInput.value && returnInput.value && diffMs >= (60 * 60 * 1000);
        var validPhone = isValidPhone(phoneInput.value);

        if (validDates) {
            var diffHours = Math.ceil((ret - pickup) / (1000 * 60 * 60));
            if (diffHours < 1) diffHours = 1;

            if (diffHours < 24) {
                rentalTypeDisplay.textContent = 'Hourly';
                durationDisplay.textContent = diffHours + ' hr(s)';
                amountDisplay.textContent = (diffHours * pricePerHour).toLocaleString('en-IN');
            } else {
                var days = Math.ceil(diffHours / 24);
                rentalTypeDisplay.textContent = 'Daily';
                durationDisplay.textContent = days + ' day(s)';
                amountDisplay.textContent = (days * pricePerDay).toLocaleString('en-IN');
            }
        } else {
            rentalTypeDisplay.textContent = '-';
            durationDisplay.textContent = '0';
            amountDisplay.textContent = '0';
        }

        confirmBtn.disabled = !(validDates && validPhone);
    }

    pickupInput.addEventListener('change', function () {
        returnInput.min = pickupInput.value;
        updateSummary();
    });
    returnInput.addEventListener('change', updateSummary);
    phoneInput.addEventListener('input', updateSummary);

    updateSummary();
})();
</script>

<?php require_once '../includes/footer.php'; ?>