<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Voyage - FAQ</title>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>

    <main class="hero-section">
        <div class="hero-copy">
            <span class="hero-eyebrow">FAQ</span>
            <h1 class="hero-title">Questions about booking, tickets, and travel with Voyage.</h1>
            <p>Find answers for ticket expiry, route planning, and checkout details all in one place.</p>
            <div class="hero-actions">
                <a class="primary-button" href="booking.php">Book a Ride</a>
                <a class="secondary-button" href="index.php">Back to Home</a>
            </div>
        </div>
        <div class="hero-visual">
            <img src="./pics/bannerstuff.png" alt="Voyage FAQ hero image">
        </div>
    </main>

    <section id="faq-section" class="faq-section">
        <div class="section-title">
            <h2>Frequently asked questions</h2>
            <p>Everything you need to know about booking, ticket expiry, and getting the most out of Voyage.</p>
        </div>
        <div class="faq-grid">
            <div class="faq-card">
                <button type="button" class="faq-summary" aria-expanded="false">How do I book a ride?<span class="faq-toggle-icon">+</span></button>
                <div class="faq-body" hidden>
                    <p>Select your departure and destination from the predefined stations, then confirm your trip at checkout.</p>
                </div>
            </div>
            <div class="faq-card">
                <button type="button" class="faq-summary" aria-expanded="false">When does my ticket expire?<span class="faq-toggle-icon">+</span></button>
                <div class="faq-body" hidden>
                    <p>Tickets automatically expire 24 hours after purchase. You do not need to choose an expiry date manually.</p>
                </div>
            </div>
            <div class="faq-card">
                <button type="button" class="faq-summary" aria-expanded="false">Where can I see my current bookings?<span class="faq-toggle-icon">+</span></button>
                <div class="faq-body" hidden>
                    <p>Log in and visit the booking page to review active tickets, the route details, and expiry information.</p>
                </div>
            </div>
            <div class="faq-card">
                <button type="button" class="faq-summary" aria-expanded="false">Can I ask for refunds?<span class="faq-toggle-icon">+</span></button>
                <div class="faq-body" hidden>
                    <p>No. Non-negotiable.</p>
                </div>
            </div>
            <div class="faq-card">
                <button type="button" class="faq-summary" aria-expanded="false">Who is the rat in that one image?<span class="faq-toggle-icon">+</span></button>
                <div class="faq-body" hidden>
                    <p>His name is LeRarTee.</p>
                </div>
            </div>
            <div class="faq-card">
                <button type="button" class="faq-summary" aria-expanded="false">How many tickets can I book at once?<span class="faq-toggle-icon">+</span></button>
                <div class="faq-body" hidden>
                    <p>Unlimited.</p>
                </div>
            </div>
            <div class="faq-card">
                <button type="button" class="faq-summary" aria-expanded="false">Why is the website not working?<span class="faq-toggle-icon">+</span></button>
                <div class="faq-body" hidden>
                    <p>Try refreshing.</p>
                </div>
            </div>
            <div class="faq-card">
                <button type="button" class="faq-summary" aria-expanded="false">Will there be any further updates to this website?<span class="faq-toggle-icon">+</span></button>
                <div class="faq-body" hidden>
                    <p>Coming soon... TM</p>
                </div>
            </div>
            <div class="faq-card">
                <button type="button" class="faq-summary" aria-expanded="false">Is my payment information secure?<span class="faq-toggle-icon">+</span></button>
                <div class="faq-body" hidden>
                    <p>Yes. Voyage uses secure checkout practices, and only the required card details are collected to complete your payment.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-panel">
        <div>
            <h2 class="gradientbottomtext">Still need help?</h2>
            <p>Reach out through our support channels or continue booking with confidence after reviewing the FAQs.</p>
        </div>
        <button class="primary-button" type="button">Contact Us</button>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const faqCards = document.querySelectorAll('.faq-card');
            faqCards.forEach(card => {
                const button = card.querySelector('.faq-summary');
                const body = card.querySelector('.faq-body');
                button.addEventListener('click', function () {
                    const expanded = button.getAttribute('aria-expanded') === 'true';
                    faqCards.forEach(other => {
                        const otherButton = other.querySelector('.faq-summary');
                        const otherBody = other.querySelector('.faq-body');
                        otherButton.setAttribute('aria-expanded', 'false');
                        otherBody.hidden = true;
                        other.classList.remove('open');
                    });
                    if (!expanded) {
                        button.setAttribute('aria-expanded', 'true');
                        body.hidden = false;
                        card.classList.add('open');
                    }
                });
            });
        });
    </script>
</body>
</html>
