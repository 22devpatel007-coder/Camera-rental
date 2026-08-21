<?php
/**
 * Calculate number of rental days between two dates (inclusive minimum of 1).
 *
 * @param string $pickup_date  Y-m-d format
 * @param string $return_date  Y-m-d format
 * @return int
 */
function calculate_rental_days($pickup_date, $return_date) {
    $pickup = strtotime($pickup_date);
    $return = strtotime($return_date);
    $days = ceil(($return - $pickup) / 86400);

    if ($days < 1) {
        $days = 1;
    }

    return (int)$days;
}

/**
 * Calculate rental duration and decide Hourly vs Daily pricing.
 * Under 24 hours = Hourly. 24 hours or more = Daily.
 *
 * @param string $pickup_datetime  Y-m-d H:i:s format
 * @param string $return_datetime  Y-m-d H:i:s format
 * @return array array('type' => 'Hourly'|'Daily', 'hours' => int, 'days' => int)
 */
function calculate_rental_duration($pickup_datetime, $return_datetime) {
    $pickup = strtotime($pickup_datetime);
    $return = strtotime($return_datetime);
    $total_hours = ceil(($return - $pickup) / 3600);

    if ($total_hours < 1) {
        $total_hours = 1;
    }

    if ($total_hours < 24) {
        return array('type' => 'Hourly', 'hours' => (int) $total_hours, 'days' => 0);
    }

    $total_days = (int) ceil($total_hours / 24);
    return array('type' => 'Daily', 'hours' => 0, 'days' => $total_days);
}

/**
 * Generate a unique booking number, e.g. BK-20260816-4821
 * Checks tbl_bookings to avoid collisions (retries on duplicate).
 *
 * @param mysqli $conn
 * @return string
 */
function generate_booking_number($conn) {
    do {
        $number = 'BK-' . date('Ymd') . '-' . mt_rand(1000, 9999);

        $stmt = mysqli_prepare($conn, "SELECT booking_id FROM tbl_bookings WHERE booking_number = ?");
        mysqli_stmt_bind_param($stmt, 's', $number);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
    } while ($exists);

    return $number;
}
?>