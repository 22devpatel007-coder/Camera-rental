<?php
require_once '../includes/user-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$user_id = $_SESSION['user_id'];
$error = '';

// Handle add to cart (from camera-details.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['camera_id']) && !isset($_POST['action'])) {
    $camera_id = (int)$_POST['camera_id'];

    // Verify camera exists and is available
    $check = mysqli_prepare($conn, "SELECT camera_id, quantity, status FROM tbl_cameras WHERE camera_id = ?");
    mysqli_stmt_bind_param($check, 'i', $camera_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_bind_result($check, $ck_camera_id, $ck_quantity, $ck_status);
    $camera_found = mysqli_stmt_fetch($check);
    mysqli_stmt_close($check);
    $camera = $camera_found ? array('camera_id' => $ck_camera_id, 'quantity' => $ck_quantity, 'status' => $ck_status) : null;

    if ($camera && $camera['status'] === 'Available' && $camera['quantity'] > 0) {
        // Check if already in cart
        $existing = mysqli_prepare($conn, "SELECT cart_id, quantity FROM tbl_cart WHERE user_id = ? AND camera_id = ?");
        mysqli_stmt_bind_param($existing, 'ii', $user_id, $camera_id);
        mysqli_stmt_execute($existing);
        mysqli_stmt_bind_result($existing, $ex_cart_id, $ex_quantity);
        $cart_found = mysqli_stmt_fetch($existing);
        mysqli_stmt_close($existing);
        $cart_row = $cart_found ? array('cart_id' => $ex_cart_id, 'quantity' => $ex_quantity) : null;

        if ($cart_row) {
            // Already in cart, increase quantity (capped by stock)
            $new_qty = min($cart_row['quantity'] + 1, $camera['quantity']);
            $update = mysqli_prepare($conn, "UPDATE tbl_cart SET quantity = ? WHERE cart_id = ?");
            mysqli_stmt_bind_param($update, 'ii', $new_qty, $cart_row['cart_id']);
            mysqli_stmt_execute($update);
            mysqli_stmt_close($update);
        } else {
            $insert = mysqli_prepare($conn, "INSERT INTO tbl_cart (user_id, camera_id, quantity) VALUES (?, ?, 1)");
            mysqli_stmt_bind_param($insert, 'ii', $user_id, $camera_id);
            mysqli_stmt_execute($insert);
            mysqli_stmt_close($insert);
        }
    }

    if (isset($_POST['rent_now'])) {
        header('Location: checkout.php');
    } else {
        header('Location: cart.php');
    }
    exit;
}

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $cart_id = (int)$_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0) {
        // Make sure this cart row belongs to the logged-in user, and cap at stock
        $stmt = mysqli_prepare($conn, "SELECT tc.cart_id, cam.quantity AS stock
                                        FROM tbl_cart tc
                                        JOIN tbl_cameras cam ON tc.camera_id = cam.camera_id
                                        WHERE tc.cart_id = ? AND tc.user_id = ?");
        mysqli_stmt_bind_param($stmt, 'ii', $cart_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $ow_cart_id, $ow_stock);
        $row_found = mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        $row = $row_found ? array('cart_id' => $ow_cart_id, 'stock' => $ow_stock) : null;

        if ($row) {
            $quantity = min($quantity, $row['stock']);
            $update = mysqli_prepare($conn, "UPDATE tbl_cart SET quantity = ? WHERE cart_id = ?");
            mysqli_stmt_bind_param($update, 'ii', $quantity, $cart_id);
            mysqli_stmt_execute($update);
            mysqli_stmt_close($update);
        }
    }

    header('Location: cart.php');
    exit;
}

// Handle remove item
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $delete = mysqli_prepare($conn, "DELETE FROM tbl_cart WHERE cart_id = ? AND user_id = ?");
    mysqli_stmt_bind_param($delete, 'ii', $cart_id, $user_id);
    mysqli_stmt_execute($delete);
    mysqli_stmt_close($delete);

    header('Location: cart.php');
    exit;
}

// Fetch cart items with camera details
$stmt = mysqli_prepare($conn, "SELECT tc.cart_id, tc.quantity, cam.camera_id, cam.camera_name, cam.price_per_day, cam.image, cam.quantity AS stock
                                FROM tbl_cart tc
                                JOIN tbl_cameras cam ON tc.camera_id = cam.camera_id
                                WHERE tc.user_id = ?
                                ORDER BY tc.created_at DESC");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $r_cart_id, $r_quantity, $r_camera_id, $r_camera_name, $r_price_per_day, $r_image, $r_stock);

$cart_items = array();
$grand_total = 0;

while (mysqli_stmt_fetch($stmt)) {
    $subtotal = $r_price_per_day * $r_quantity;
    $grand_total += $subtotal;
    $cart_items[] = array(
        'cart_id'       => $r_cart_id,
        'quantity'      => $r_quantity,
        'camera_id'     => $r_camera_id,
        'camera_name'   => $r_camera_name,
        'price_per_day' => $r_price_per_day,
        'image'         => $r_image,
        'stock'         => $r_stock,
        'subtotal'      => $subtotal
    );
}
mysqli_stmt_close($stmt);

$page_title = 'My Cart';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <h2 class="section-title">My Cart</h2>

    <?php if (count($cart_items) === 0): ?>
        <p>Your cart is empty. <a href="browse-cameras.php">Browse cameras</a> to get started.</p>
    <?php else: ?>
        <div class="cart-list">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="<?php echo BASE_URL; ?>assets/images/cameras/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['camera_name']); ?>">

                    <div class="cart-item-info">
                        <h3><?php echo htmlspecialchars($item['camera_name']); ?></h3>
                        <p>&#8377;<?php echo number_format($item['price_per_day'], 2); ?> / day</p>
                    </div>

                    <form method="POST" action="cart.php" class="cart-item-qty">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                        <input type="number" name="quantity" value="<?php echo (int)$item['quantity']; ?>" min="1" max="<?php echo (int)$item['stock']; ?>">
                        <button type="submit" class="btn-secondary">Update</button>
                    </form>

                    <p class="cart-item-subtotal">&#8377;<?php echo number_format($item['subtotal'], 2); ?></p>

                    <a href="cart.php?remove=<?php echo $item['cart_id']; ?>" class="cart-item-remove" onclick="return confirm('Remove this item?');">Remove</a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <p class="cart-total">Total: &#8377;<?php echo number_format($grand_total, 2); ?></p>
            <a href="checkout.php" class="btn-primary">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>