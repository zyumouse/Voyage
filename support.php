<?php
session_start();
require_once __DIR__ . '/validation.php';
$isSubmitted = !empty($_SESSION['support_form_submitted']);
unset($_SESSION['support_form_submitted']);
$formError = '';
$formData = [
    'name' => '',
    'email' => '',
    'topic' => '',
    'message' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($formData as $field => $value) {
        $submittedValue = $_POST[$field] ?? '';
        $formData[$field] = is_string($submittedValue) ? trim($submittedValue) : '';
    }

    if (in_array('', $formData, true)) {
        $formError = 'Please complete every field before sending your request.';
    } elseif (!voyage_is_valid_name($formData['name'])) {
        $formError = 'Enter a valid name using letters, spaces, apostrophes, periods, or hyphens.';
    } elseif (!voyage_is_valid_email($formData['email'])) {
        $formError = 'Please enter a valid email address.';
    } else {
        $_SESSION['support_form_submitted'] = true;
        header('Location: support.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
    <script src="form-validation.js" defer></script>
    <link rel="stylesheet" href="style.css">
    <title>Voyage - Customer Support</title>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>

    <div class="support-page-content">
        <main class="hero-section support-hero">
            <div class="hero-copy">
                <span class="hero-eyebrow">Customer Support</span>
                <h1 class="hero-title">Tell us what happened.</h1>
                <p>Send the Voyage team a message. We are here to keep your trip moving.</p>
                <div class="hero-actions">
                    <a class="primary-button" href="faq.php">Browse FAQs</a>
                    <a class="secondary-button" href="booking.php">Book a Ride</a>
                </div>
            </div>
            <div class="hero-visual support-hero-visual">
                <img src="./pics/bannerstuff.png" alt="Voyage customer support">
            </div>
        </main>

        <section class="support-form-section" id="support-form" aria-label="Support request form">
            <div class="support-form-intro">
                <p>Share enough detail for us to understand the issue. Please do not include sensitive information.</p>
                <div class="support-contact-note">
                    <strong>Before you send</strong>
                    <span>Check the FAQ for instant answers about ticket expiry and booking limits.</span>
                </div>
                <div class="support-response-meta" aria-label="Support response information">
                    <div>
                        <strong>Support hours</strong>
                        <span>Mon-Fri, 9:00-17:00</span>
                    </div>
                    <div>
                        <strong>Response time</strong>
                        <span>Usually within one business day</span>
                    </div>
                </div>
            </div>
            <div class="support-form-card site-form-card">
                <div class="support-form-heading">
                    <h3>Send a support request</h3>
                    <p>We will use your email to follow up on this request.</p>
                </div>
                <?php if ($formError !== ''): ?>
                    <div class="errorMessage" role="alert">
                        <?php echo htmlspecialchars($formError); ?>
                    </div>
                <?php endif; ?>
                <form class="auth-form support-form site-form" method="post" action="support.php">
                    <div class="support-form-contact-fields">
                        <div class="authField">
                            <label for="support-name">Name</label>
                            <input id="support-name" type="text" name="name" autocomplete="name" maxlength="100" pattern="[\p{L}\p{M}][\p{L}\p{M} .'\-]{0,99}" title="Use letters, spaces, apostrophes, periods, or hyphens." value="<?php echo htmlspecialchars($formData['name']); ?>" required>
                        </div>
                        <div class="authField">
                            <label for="support-email">Email</label>
                            <input id="support-email" type="email" name="email" autocomplete="email" maxlength="255" data-no-spaces value="<?php echo htmlspecialchars($formData['email']); ?>" required>
                        </div>
                    </div>
                    <div class="authField">
                        <label for="support-topic">What can we help with?</label>
                        <select id="support-topic" name="topic" required>
                            <option value="">Select a topic</option>
                            <option value="booking"<?php echo $formData['topic'] === 'booking' ? ' selected' : ''; ?>>Booking or route</option>
                            <option value="ticket"<?php echo $formData['topic'] === 'ticket' ? ' selected' : ''; ?>>Ticket or payment</option>
                            <option value="account"<?php echo $formData['topic'] === 'account' ? ' selected' : ''; ?>>Account access</option>
                            <option value="technical"<?php echo $formData['topic'] === 'technical' ? ' selected' : ''; ?>>Technical issue</option>
                        </select>
                    </div>
                    <div class="authField">
                        <label for="support-message">Message</label>
                        <textarea id="support-message" name="message" rows="5" required><?php echo htmlspecialchars($formData['message']); ?></textarea>
                    </div>
                    <button type="submit">Create Support Ticket</button>
                </form>
            </div>
        </section>
    </div>

    <?php if ($isSubmitted): ?>
        <dialog class="site-dialog" id="support-success-dialog" aria-labelledby="support-success-title">
            <h2 id="support-success-title">Message received</h2>
            <p>Thanks for contacting Voyage. Our support team will review your request.</p>
            <button type="button" id="support-success-close">Close</button>
        </dialog>
        <script>
            const successDialog = document.getElementById('support-success-dialog');
            successDialog.showModal();
            document.getElementById('support-success-close').addEventListener('click', () => successDialog.close());
        </script>
    <?php endif; ?>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>