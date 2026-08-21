<?php
require_once '../includes/user-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$user_id = $_SESSION['user_id'];
$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;

if ($booking_id <= 0) {
    header('Location: browse-cameras.php');
    exit;
}

// Verify booking belongs to this user
$stmt = mysqli_prepare($conn, 'SELECT booking_id, booking_number, total_amount, payment_method
    FROM tbl_bookings WHERE booking_id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'ii', $booking_id, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $b_id, $b_number, $b_total, $b_payment_method);

$booking = false;
if (mysqli_stmt_fetch($stmt)) {
    $booking = array(
        'booking_id'     => $b_id,
        'booking_number' => $b_number,
        'total_amount'   => $b_total,
        'payment_method' => $b_payment_method,
    );
}
mysqli_stmt_close($stmt);

if (!$booking) {
    header('Location: browse-cameras.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
    $allowed_methods = array('COD', 'Card', 'UPI');

    // Server only ever validates and stores the METHOD, never card/UPI details.
    // Card/UPI fields are simulated on the client and intentionally never sent
    // to this script's stored data — kept out of $_POST processing on purpose.
    if (!in_array($method, $allowed_methods, true)) {
        $error = 'Please select a valid payment method.';
    } else {
        $update = mysqli_prepare($conn, 'UPDATE tbl_bookings SET payment_method = ? WHERE booking_id = ? AND user_id = ?');
        mysqli_stmt_bind_param($update, 'sii', $method, $booking_id, $user_id);
        mysqli_stmt_execute($update);
        mysqli_stmt_close($update);

        header('Location: booking-confirmation.php?booking_id=' . $booking_id);
        exit;
    }
}

$page_title = 'Payment';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <h2 class="section-title">Payment</h2>

    <?php if ($error !== ''): ?>
        <div class="form-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="checkout-summary">
        <div class="checkout-row">
            <span>Booking Number</span>
            <span><?php echo htmlspecialchars($booking['booking_number']); ?></span>
        </div>
        <div class="checkout-row">
            <span>Amount Payable</span>
            <span>&#8377;<?php echo number_format($booking['total_amount'], 2); ?></span>
        </div>
    </div>

    <form method="POST" action="payment.php?booking_id=<?php echo (int) $booking_id; ?>" class="payment-form" id="paymentForm">
        <div class="payment-options">
            <label class="payment-option">
                <input type="radio" name="payment_method" value="COD" checked>
                <span>Cash on Pickup (COD)</span>
            </label>
            <label class="payment-option">
                <input type="radio" name="payment_method" value="Card">
                <span>Credit / Debit Card</span>
            </label>
            <label class="payment-option">
                <input type="radio" name="payment_method" value="UPI">
                <span>UPI</span>
            </label>
        </div>

        <div class="payment-fields" id="cardFields" style="display:none;">
            <div class="form-group">
                <label for="card_number">Card Number</label>
                <input type="text" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="off">
            </div>
            <div class="row-inputs">
                <div class="form-group">
                    <label for="card_expiry">Expiry (MM/YY)</label>
                    <input type="text" id="card_expiry" placeholder="MM/YY" maxlength="5" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="card_cvv">CVV</label>
                    <input type="text" id="card_cvv" placeholder="123" maxlength="3" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="payment-fields" id="upiFields" style="display:none;">
            <div class="form-group">
                <label for="upi_id">UPI ID</label>
                <input type="text" id="upi_id" placeholder="yourname@upi" autocomplete="off">
            </div>
        </div>

        <button type="submit" class="btn-primary" id="payBtn">Confirm Payment</button>
    </form>
</div>

<script>
(function () {
    var radios = document.querySelectorAll('input[name="payment_method"]');
    var cardFields = document.getElementById('cardFields');
    var upiFields = document.getElementById('upiFields');
    var cardNumber = document.getElementById('card_number');
    var cardExpiry = document.getElementById('card_expiry');
    var cardCvv = document.getElementById('card_cvv');
    var upiId = document.getElementById('upi_id');
    var form = document.getElementById('paymentForm');
    var payBtn = document.getElementById('payBtn');

    function toggleFields() {
        var selected = document.querySelector('input[name="payment_method"]:checked').value;
        cardFields.style.display = (selected === 'Card') ? 'block' : 'none';
        upiFields.style.display = (selected === 'UPI') ? 'block' : 'none';
    }

    for (var i = 0; i < radios.length; i++) {
        radios[i].addEventListener('change', toggleFields);
    }
    toggleFields();

    // Auto-format card number with spaces
    cardNumber.addEventListener('input', function () {
        var digits = this.value.replace(/\D/g, '').substring(0, 16);
        this.value = digits.replace(/(.{4})/g, '$1 ').trim();
    });

    // Auto-format expiry as MM/YY
    cardExpiry.addEventListener('input', function () {
        var digits = this.value.replace(/\D/g, '').substring(0, 4);
        if (digits.length >= 3) {
            this.value = digits.substring(0, 2) + '/' + digits.substring(2);
        } else {
            this.value = digits;
        }
    });

    cardCvv.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').substring(0, 3);
    });

    form.addEventListener('submit', function (e) {
        var selected = document.querySelector('input[name="payment_method"]:checked').value;

        if (selected === 'Card') {
            var digitsOnly = cardNumber.value.replace(/\s/g, '');
            if (digitsOnly.length !== 16 || !/^\d{2}\/\d{2}$/.test(cardExpiry.value) || cardCvv.value.length !== 3) {
                e.preventDefault();
                alert('Please enter valid card details.');
                return;
            }
        }

        if (selected === 'UPI') {
            if (!/^[\w.\-]+@[\w]+$/.test(upiId.value)) {
                e.preventDefault();
                alert('Please enter a valid UPI ID (e.g. name@bank).');
                return;
            }
        }

        // Simulated processing delay before actual form submit
        e.preventDefault();
        payBtn.disabled = true;
        payBtn.textContent = 'Processing...';
        setTimeout(function () {
            form.submit();
        }, 1200);
    });
})();
</script>

<?php require_once '../includes/footer.php'; ?>