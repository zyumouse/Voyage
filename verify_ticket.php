<?php
require_once __DIR__ . '/schema.php';

$ticket = null;
$verificationAvailable = true;
$token = isset($_GET['token']) && is_string($_GET['token']) ? $_GET['token'] : '';

if (preg_match('/\A[a-f0-9]{64}\z/i', $token) === 1) {
    $conn = new mysqli('localhost', 'root', '', 'ticket_system');
    if ($conn->connect_error) {
        $verificationAvailable = false;
    } else {
        voyage_migrate_legacy_tables($conn);
        $statement = $conn->prepare('SELECT id, origin, destination, ticket_date, created_at FROM ticket_records WHERE qr_token = ? LIMIT 1');
        if ($statement) {
            $statement->bind_param('s', $token);
            if ($statement->execute()) {
                $result = $statement->get_result();
                $ticket = $result ? $result->fetch_assoc() : null;
            } else {
                $verificationAvailable = false;
            }
            $statement->close();
        } else {
            $verificationAvailable = false;
        }
        $conn->close();
    }
}

$createdTimestamp = $ticket ? strtotime((string)$ticket['created_at']) : false;
$expiryTimestamp = $createdTimestamp !== false
    ? $createdTimestamp + 86400
    : ($ticket ? strtotime((string)$ticket['ticket_date'] . ' 23:59:59') : false);
$isValid = $verificationAvailable && $ticket && $expiryTimestamp !== false && $expiryTimestamp > time();
$escape = static function ($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
    <link rel="stylesheet" href="style.css">
    <title>Ticket Verification | Voyage</title>
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    <main class="ticket-verification-page">
        <section class="ticket-verification-card" aria-labelledby="ticket-verification-title">
            <p class="ticket-verification-eyebrow">Voyage ticket check</p>
            <?php if (!$verificationAvailable): ?>
                <div class="ticket-verification-status unavailable">Verification unavailable</div>
                <h1 id="ticket-verification-title">Try again later</h1>
                <p>We could not check this ticket right now.</p>
            <?php elseif (!$ticket): ?>
                <div class="ticket-verification-status invalid">Ticket not found</div>
                <h1 id="ticket-verification-title">Invalid ticket</h1>
                <p>This QR code does not match a ticket in the Voyage system.</p>
            <?php else: ?>
                <div class="ticket-verification-status <?php echo $isValid ? 'valid' : 'expired'; ?>">
                    <?php echo $isValid ? 'Valid ticket' : 'Expired ticket'; ?>
                </div>
                <h1 id="ticket-verification-title">Ticket #<?php echo (int)$ticket['id']; ?></h1>
                <p class="ticket-verification-route">
                    <?php echo $escape($ticket['origin']); ?>
                    <span aria-hidden="true">&rarr;</span>
                    <?php echo $escape($ticket['destination']); ?>
                </p>
                <p class="ticket-verification-expiry">
                    Expires <?php echo $expiryTimestamp !== false ? $escape(date('M j, Y \a\t H:i', $expiryTimestamp)) : 'at an unknown time'; ?>
                </p>
            <?php endif; ?>
        </section>
    </main>
    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>