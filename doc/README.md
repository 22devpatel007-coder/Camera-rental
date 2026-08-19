-first taks is if i upload(add),any document,md file , chat summary analyz first and then give me answer and 
-give me answer that i ask and don't give me to much big result in short don't waste token
code should e production ready and secure 
-before writing any big file take my permision
-if code has littile chnage then don't write full code 
-if error occure find form root and then slove and keep solution small if possible


# PHP 5.3 Compatibility Migration Plan

## Root cause
`mysqli_stmt_get_result()` needs the mysqlnd driver — not guaranteed on PHP 5.3-era WAMP. Replace with `mysqli_stmt_bind_result()` + `mysqli_stmt_fetch()` everywhere. This also forces fixing the `fetch_assoc(...)['key']` pattern (not 5.3-safe) since bind_result naturally avoids it.

## Files to check/fix (need current content for each)

**Root**
- `login.php` ✅ have it — uses `get_result`, needs fix

**Includes**
- `functions.php` ✅ have it — `generate_booking_number()` uses `store_result`/`num_rows`, no `get_result` — likely OK, will double check
- `database.php`, `config.php`, `session.php` — no DB fetch logic, skip
- `admin-auth.php`, `user-auth.php` — need to check (likely just session checks, but confirm no `get_result`)

**Admin**
- `dashboard.php` — need file
- `manage-cameras.php`, `add-camera.php`, `edit-camera.php`, `delete-camera.php` ✅ have delete — need add/edit/manage
- `manage-brands.php`, `add-brand.php`, `edit-brand.php`, `delete-brand.php` — need all
- `manage-customers.php` ✅ have it — uses `get_result`, needs fix
- `customer-details.php` — need file (not yet built per earlier check — confirm status)
- `manage-bookings.php` ✅ have it — uses `get_result` + dynamic `bind_param`, needs fix (two issues)
- `booking-details.php`, `update-booking-status.php`, `rent-status.php` — need files/status

**User**
- `browse-cameras.php`, `camera-details.php`, `cart.php` — need files
- `checkout.php` ✅ have it — uses `get_result` multiple times, needs fix
- `payment.php`, `booking-confirmation.php`, `my-bookings.php`, `cancel-booking.php` — need files
- `about.php`, `contact.php` — need files/status

## Order of work
1. Confirm which files above actually exist and are finished (vs. still pending) — quick status check first, no code yet.
2. Fix `login.php` (simplest, single-row template).
3. Fix `dashboard.php` (adds the `[...]['key']` pattern fix).
4. Fix remaining files one at a time, easiest → hardest, `checkout.php` and `manage-bookings.php` last (multi-row/dynamic-bind, most complex).
5. Final pass: search all files for leftover `get_result` or `)[` patterns to confirm nothing missed.

Should I go file by file asking you to paste each, or do you want to paste everything you have built so far in one batch so I can triage first?