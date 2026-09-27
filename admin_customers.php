<?php
session_start();
require_once __DIR__ . '/schema.php';
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: login.html');
    exit;
}

$tickets = [];
$databaseError = '';
$conn = new mysqli('localhost', 'root', '', 'ticket_system');
if ($conn->connect_error) {
    $databaseError = 'Ticket records are temporarily unavailable.';
} else {
    voyage_migrate_legacy_tables($conn);
    $result = $conn->query('SELECT id, user_id, qr_token, origin, destination, card_number, ticket_date, created_at FROM ticket_records ORDER BY created_at DESC, id DESC');
    if ($result) {
        while ($ticket = $result->fetch_assoc()) {
            $tickets[] = $ticket;
        }
    } else {
        $databaseError = 'Ticket records could not be loaded.';
    }
    $conn->close();
}

$now = time();
$activeTicketCount = 0;
foreach ($tickets as $index => $ticket) {
    $bookedTimestamp = strtotime((string)$ticket['created_at']);
    $expiryTimestamp = $bookedTimestamp !== false
        ? $bookedTimestamp + 86400
        : strtotime((string)$ticket['ticket_date'] . ' 23:59:59');
    $tickets[$index]['expiry_timestamp'] = $expiryTimestamp;
    $tickets[$index]['status'] = $expiryTimestamp !== false && $expiryTimestamp > $now ? 'active' : 'expired';
    if ($tickets[$index]['status'] === 'active') {
        $activeTicketCount++;
    }
}
$totalTicketCount = count($tickets);
$expiredTicketCount = $totalTicketCount - $activeTicketCount;
$escape = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
$host = isset($_SERVER['HTTP_HOST']) && preg_match('/\A[a-z0-9.\-\[\]:]+\z/i', $_SERVER['HTTP_HOST'])
    ? $_SERVER['HTTP_HOST']
    : 'localhost';
$scheme = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
$scriptDirectory = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
    <title>Admin - Ticket Records</title>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    <main class="ticket-admin-page">
        <header class="ticket-admin-header">
            <div>
                <p class="ticket-admin-eyebrow">Admin / Operations</p>
                <h1>Ticket Records</h1>
                <p class="ticket-admin-intro">Review bookings, journeys, and ticket validity.</p>
            </div>
        </header>

        <section class="ticket-summary" aria-label="Ticket summary">
            <div class="ticket-summary-item">
                <span>All tickets</span>
                <strong><?php echo $totalTicketCount; ?></strong>
            </div>
            <div class="ticket-summary-item">
                <span>Active</span>
                <strong><?php echo $activeTicketCount; ?></strong>
            </div>
            <div class="ticket-summary-item">
                <span>Expired</span>
                <strong><?php echo $expiredTicketCount; ?></strong>
            </div>
        </section>

        <section class="ticket-records-panel" aria-label="Ticket records">
            <div class="ticket-records-toolbar">
                <div>
                    <p id="ticket-results-count" aria-live="polite">Showing <?php echo $totalTicketCount; ?> of <?php echo $totalTicketCount; ?> tickets</p>
                </div>
                <?php if ($databaseError === '' && $tickets): ?>
                    <div class="ticket-records-filters">
                        <label class="ticket-sr-only" for="ticket-search">Search ticket records</label>
                        <input id="ticket-search" type="search" placeholder="Search #TicketID or @CustomerID">
                        <label class="ticket-sr-only" for="ticket-status-filter">Filter by ticket status</label>
                        <select id="ticket-status-filter">
                            <option value="all">All</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($databaseError !== ''): ?>
                <div class="ticket-empty-state" role="alert"><?php echo $escape($databaseError); ?></div>
            <?php elseif (!$tickets): ?>
                <div class="ticket-empty-state">
                    <strong>No tickets recorded yet</strong>
                    <span>Completed bookings will appear here.</span>
                </div>
            <?php else: ?>
                <div class="ticket-table-scroll">
                    <table class="ticket-records-table">
                        <thead>
                            <tr>
                                <th scope="col">Ticket</th>
                                <th scope="col">Route</th>
                                <th scope="col">Booked</th>
                                <th scope="col">Expires</th>
                                <th scope="col">Credit Card Number</th>
                                <th scope="col">QR Code</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                                <?php
                                    $status = $ticket['status'];
                                    $bookedTimestamp = strtotime((string)$ticket['created_at']);
                                    $expiryTimestamp = $ticket['expiry_timestamp'];
                                ?>
                                <tr class="ticket-record-row" data-status="<?php echo $status; ?>" data-ticket-id="<?php echo $escape($ticket['id']); ?>" data-account-id="<?php echo $escape($ticket['user_id']); ?>">
                                    <td class="ticket-reference">
                                        <strong>#<?php echo $escape($ticket['id']); ?></strong>
                                        <span>Customer @<?php echo $escape($ticket['user_id']); ?></span>
                                    </td>
                                    <td class="ticket-journey">
                                        <span><?php echo $escape($ticket['origin']); ?></span>
                                        <span class="ticket-journey-arrow" aria-hidden="true">&rarr;</span>
                                        <span><?php echo $escape($ticket['destination']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($bookedTimestamp !== false): ?>
                                            <time datetime="<?php echo $escape(date('c', $bookedTimestamp)); ?>">
                                                <?php echo $escape(date('M j, Y', $bookedTimestamp)); ?>
                                                <span class="ticket-cell-subtext"><?php echo $escape(date('H:i', $bookedTimestamp)); ?></span>
                                            </time>
                                        <?php else: ?>
                                            <span class="ticket-cell-subtext">Not recorded</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($expiryTimestamp !== false): ?>
                                            <time datetime="<?php echo $escape(date('c', $expiryTimestamp)); ?>">
                                                <?php echo $escape(date('M j, Y', $expiryTimestamp)); ?>
                                                <span class="ticket-cell-subtext"><?php echo $escape(date('H:i', $expiryTimestamp)); ?></span>
                                            </time>
                                        <?php else: ?>
                                            <span class="ticket-cell-subtext">Not set</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo trim((string)$ticket['card_number']) !== '' ? $escape($ticket['card_number']) : 'Not recorded'; ?></td>
                                    <td>
                                        <?php if (!empty($ticket['qr_token'])): ?>
                                            <?php $verificationUrl = $scheme . '://' . $host . $scriptDirectory . '/verify_ticket.php?token=' . rawurlencode((string)$ticket['qr_token']); ?>
                                            <button class="booking-qr-button" type="button" data-verification-url="<?php echo $escape($verificationUrl); ?>" data-ticket-id="<?php echo (int)$ticket['id']; ?>">Show</button>
                                        <?php else: ?>
                                            <span class="ticket-cell-subtext">Not available</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="ticket-status ticket-status-<?php echo $status; ?>"><?php echo ucfirst($status); ?></span></td>
                                    <td>
                                        <a class="ticket-delete-link" href="delete.php?id=<?php echo rawurlencode((string)$ticket['id']); ?>" onclick="return confirm('Delete ticket #<?php echo (int)$ticket['id']; ?>? This cannot be undone.');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr id="ticket-filter-empty" hidden>
                                <td colspan="8" class="ticket-filter-empty-cell">No tickets match these filters.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>

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

    <script>
        (function () {
            const searchInput = document.getElementById('ticket-search');
            const statusFilter = document.getElementById('ticket-status-filter');
            const resultCount = document.getElementById('ticket-results-count');
            const emptyRow = document.getElementById('ticket-filter-empty');
            const rows = Array.from(document.querySelectorAll('.ticket-record-row'));

            if (!searchInput || !statusFilter || !resultCount || rows.length === 0) return;

            function filterTickets() {
                const query = searchInput.value.trim().toLowerCase();
                const prefix = query.charAt(0);
                const identifier = query.slice(1);
                const selectedStatus = statusFilter.value;
                let visibleCount = 0;

                rows.forEach(function (row) {
                    const matchesQuery = query === '' || (identifier !== '' && (
                        (prefix === '#' && row.dataset.ticketId.includes(identifier)) ||
                        (prefix === '@' && row.dataset.accountId.includes(identifier))
                    ));
                    const matchesStatus = selectedStatus === 'all' || row.dataset.status === selectedStatus;
                    row.hidden = !(matchesQuery && matchesStatus);
                    if (!row.hidden) visibleCount++;
                });

                if (emptyRow) emptyRow.hidden = visibleCount !== 0;
                resultCount.textContent = 'Showing ' + visibleCount + ' of ' + rows.length + ' tickets';
            }

            searchInput.addEventListener('input', filterTickets);
            statusFilter.addEventListener('change', filterTickets);
        })();
    </script>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
