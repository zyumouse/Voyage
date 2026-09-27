<?php
session_start();
require_once __DIR__ . '/schema.php';
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
$isLoggedIn = isset($_SESSION['user_id']);
$stops = [
    'A01: PSR-A',
    'S02: Permatang Damar Laut',
    'S03: Lapangan Terbang Antarabangsa Pulau Pinang',
    'S04: Sungai Tiram',
    'S05: FIZ South',
    'S06: FIZ North',
    'S07: Jalan Tengah',
    'S08: SPICE',
    'S09: Bukit Jambul',
    'S10: Sungai Nibong',
    'S11: Sungai Dua',
    'S12: Batu Uban',
    'S13: Jalan Universiti',
    'S14: Gelugor',
    'S15: Penang Waterfront',
    'S16: Jelutong East',
    'S17: Sungai Pinang',
    'S18: Bandar Sri Pinang',
    'S19: Macallum',
    'S20: Komtar',
    'S31: Penang Sentral'
];
$today = (new DateTime())->format('Y-m-d');
$maxDate = (new DateTime('+7 days'))->format('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
    <link rel="stylesheet" href="style.css">
    <title>Voyage - Booking</title>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
    <style>
        .auth-page.booking-page {
            min-height: calc(100vh - 84px);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 70px 20px 32px;
        }
        .booking-page .auth-card {
            width: min(1200px, 100%);
            padding: 36px 32px;
            text-align: left;
        }
        .booking-page .bookingContainer {
            padding: 0;
        }
        .booking-page .bookingTitle {
            margin: 0 0 8px;
        }
        .booking-page .bookingLog {
            gap: 24px;
            margin-top: 32px;
        }
        .booking-page .addBooking {
            margin-top: 32px;
        }
        .booking-page .payTitle {
            margin-bottom: 16px;
        }
        .route-search {
            margin: 26px 0 20px;
            padding: 24px;
            background: #ffffff;
            border: 1px solid rgba(77,64,255,0.16);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(92,88,173,0.08);
        }
        .route-search h2 {
            margin: 0 0 16px;
            font-size: 1.3rem;
            color: #161c3f;
        }
        .route-search-form {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 16px;
            align-items: end;
        }
        .route-search-form label {
            display: flex;
            flex-direction: column;
            gap: 8px;
            color: #1f2350;
            font-weight: 700;
        }
        .route-search-form select,
        .route-search-form input[type="date"],
        .route-search-form button {
            width: 100%;
            min-height: 46px;
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid #c8cbe8;
            font-size: 0.95rem;
            color: #161c3f;
            background: #ffffff;
            box-sizing: border-box;
            font-family: inherit;
            line-height: 1.5;
        }
        .route-search-form input[type="date"] {
            appearance: textfield;
            -webkit-appearance: textfield;
            -moz-appearance: textfield;
        }
        .route-search-form label[for="custom_ticket_date"] {
            grid-column: 1;
        }
        .route-search-form button {
            grid-column: 1 / -1;
            background: #3039ff;
            border-color: transparent;
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .route-search-form button:hover {
            transform: translateY(-1px);
            background: #202dce;
        }
        .route-notes {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 8px;
        }
        .route-notes .route-note {
            margin: 0;
            font-size: 0.86rem;
            color: #5a5f7d;
            line-height: 1.4;
        }
        .route-search .message-box {
            display: none;
        }
        @media (max-width: 720px) {
            .route-search-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>

    <div class="booking-page-content">
        <main class="hero-section booking-hero">
            <div class="hero-copy">
                <span class="hero-eyebrow">Smart Ticketing</span>
                <h1 class="hero-title">Plan your next journey with Voyage.</h1>
                <p>Choose your stations, confirm your route, and keep every active ticket in one clear place.</p>
                <div class="hero-actions">
                    <a class="primary-button" href="maps.php">View Route Map</a>
                    <a class="secondary-button" href="faq.php">Need Help?</a>
                </div>
            </div>
            <div class="hero-visual">
                <img src="./pics/bannerstuff.png" alt="Voyage booking experience">
            </div>
        </main>

    <section class="booking-page booking-workspace<?php echo $isLoggedIn ? ' is-logged-in' : ''; ?>" id="route-booking">
        <div class="booking-workspace-inner">
            <h1 class="auth-title">Your Trips</h1>
            <p class="auth-subtitle">Review your current bookings and book a custom route with your chosen origin and destination.</p>
            <div class="bookingContainer">

        <?php if (!$isLoggedIn): ?>
            <div class="bookingNotice">
                <p>Please <a class="gradient-link" href="login.html">log in</a> to create and view bookings.</p>
            </div>
        <?php else: ?>
            <?php
            $conn = new mysqli('localhost', 'root', '', 'ticket_system');
            if (!$conn->connect_error) {
                voyage_migrate_legacy_tables($conn);
                $conn->query("CREATE TABLE IF NOT EXISTS available_trips (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    origin VARCHAR(100) NOT NULL,
                    destination VARCHAR(100) NOT NULL,
                    ticket_date DATE NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )");
                $conn->query("CREATE TABLE IF NOT EXISTS ticket_records (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    trip_id INT NOT NULL DEFAULT 0,
                    user_id INT NOT NULL,
                    name VARCHAR(100) NOT NULL,
                    phone_number VARCHAR(30) NOT NULL,
                    origin VARCHAR(100) NOT NULL,
                    destination VARCHAR(100) NOT NULL,
                    card_number VARCHAR(50) NOT NULL,
                    qr_token CHAR(64) NULL,
                    ticket_date DATE NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )");
                $conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS trip_id INT NOT NULL DEFAULT 0");
                $conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS user_id INT NOT NULL DEFAULT 0");
                $conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS name VARCHAR(100) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS phone_number VARCHAR(30) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS card_number VARCHAR(50) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS origin VARCHAR(100) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS destination VARCHAR(100) NOT NULL DEFAULT ''");
                $conn->query("ALTER TABLE ticket_records ADD COLUMN IF NOT EXISTS ticket_date DATE NOT NULL DEFAULT '1970-01-01'");

                $stmt = $conn->prepare('SELECT id, qr_token, origin, destination, ticket_date, created_at FROM ticket_records WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY created_at DESC');
                $stmt->bind_param('i', $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
            }
            ?>
            <?php if (isset($_GET['success'])): ?>
                <dialog class="site-dialog" id="checkout-success-dialog" aria-labelledby="checkout-success-title">
                    <h2 id="checkout-success-title">Thank you!</h2>
                    <p>Your checkout is complete. Bon Voyage!</p>
                    <button type="button" id="checkout-success-close">Continue</button>
                </dialog>
                <script>
                    const checkoutSuccessDialog = document.getElementById('checkout-success-dialog');
                    checkoutSuccessDialog.showModal();
                    document.getElementById('checkout-success-close').addEventListener('click', () => checkoutSuccessDialog.close());
                    window.history.replaceState(null, '', window.location.pathname);
                </script>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="errorMessage"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></div>
            <?php endif; ?>

            <div class="route-search">
                <h2>Book your own route</h2>
                <form class="route-search-form" method="GET" action="checkout.php">
                    <label for="custom_origin">Origin
                        <select id="custom_origin" name="origin" required>
                            <option value="" disabled selected>Select origin</option>
                            <?php foreach ($stops as $stop): ?>
                                <option value="<?php echo htmlspecialchars($stop); ?>"><?php echo htmlspecialchars($stop); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label for="custom_destination">Destination
                        <select id="custom_destination" name="destination" required>
                            <option value="" disabled selected>Select destination</option>
                            <?php foreach ($stops as $stop): ?>
                                <option value="<?php echo htmlspecialchars($stop); ?>"><?php echo htmlspecialchars($stop); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div class="route-notes">
                        <p class="route-note">Ticket expiry date will be created automatically after purchase.</p>
                        <p class="route-note">Choose a route from our list of predefined stations. Tickets are valid for 24 hours from booking.</p>
                    </div>
                    <button type="submit">Book custom route</button>
                </form>
            </div>
            <div class="bookingLog">
                <?php if (!empty($result) && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $createdTimestamp = strtotime((string)$row['created_at']);
                            $createdDateTime = $createdTimestamp !== false ? date('d/m/Y H:i', $createdTimestamp) : 'Not recorded';
                            $expiryTimestamp = $createdTimestamp !== false
                                ? $createdTimestamp + 86400
                                : strtotime((string)$row['ticket_date']);
                        ?>
                                <?php
                                    $host = isset($_SERVER['HTTP_HOST']) && preg_match('/\A[a-z0-9.\-\[\]:]+\z/i', $_SERVER['HTTP_HOST'])
                                        ? $_SERVER['HTTP_HOST']
                                        : 'localhost';
                                    $scheme = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                                    $scriptDirectory = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
                                    $verificationUrl = $scheme . '://' . $host . $scriptDirectory . '/verify_ticket.php?token=' . rawurlencode((string)$row['qr_token']);
                                ?>
                                <div class="bookingLogItem">
                            <div>
                                <h2><?php echo htmlspecialchars($row['origin']); ?> &rarr; <?php echo htmlspecialchars($row['destination']); ?></h2>
                                <h4>Booked on <?php echo htmlspecialchars($createdDateTime); ?></h4>
                                <p class="bookingLogMeta">Expires: <?php echo $expiryTimestamp !== false ? htmlspecialchars(date('d/m/Y H:i', $expiryTimestamp)) : 'Not set'; ?></p>
                            </div>
                            <div>
                                <h3>Ticket status</h3>
                                <h4>Confirmed</h4>
                                <button class="booking-qr-button" type="button" data-verification-url="<?php echo htmlspecialchars($verificationUrl, ENT_QUOTES, 'UTF-8'); ?>" data-ticket-id="<?php echo (int)$row['id']; ?>">Show QR Ticket</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="bookingLogItem empty">
                        <p>No active tickets yet. Book a custom route now — tickets expire 24 hours after purchase.</p>
                    </div>
                <?php endif; ?>
            </div>

            <dialog class="site-dialog" id="ticket-qr-dialog" aria-label="Ticket QR code">
                <div class="ticket-qr-surface">
                    <div id="ticket-qr-code" aria-label="Ticket verification QR code"></div>
                </div>
                <button type="button" id="ticket-qr-close">Close</button>
            </dialog>
            <script src="./assets/vendor/qrcode.min.js"></script>
            <script>
                (function () {
                    const ticketQrDialog = document.getElementById('ticket-qr-dialog');
                    const qrCodeContainer = document.getElementById('ticket-qr-code');

                    document.querySelectorAll('.booking-qr-button').forEach(function (button) {
                        button.addEventListener('click', function () {
                            qrCodeContainer.replaceChildren();
                            if (typeof QRCode === 'undefined') {
                                qrCodeContainer.textContent = 'QR code could not be loaded. Check your connection and try again.';
                            } else {
                                new QRCode(qrCodeContainer, {
                                    text: button.dataset.verificationUrl,
                                    width: 224,
                                    height: 224,
                                    colorDark: '#17172b',
                                    colorLight: '#ffffff',
                                    correctLevel: QRCode.CorrectLevel.M
                                });
                            }
                            ticketQrDialog.showModal();
                        });
                    });

                    document.getElementById('ticket-qr-close').addEventListener('click', function () {
                        ticketQrDialog.close();
                    });
                })();
            </script>

        <?php endif; ?>
        </div>
    </div>
    </section>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
