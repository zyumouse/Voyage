<?php
session_start();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;
$isLoggedIn = isset($_SESSION['user_id']);
$stops = [
    'A01: PSR-A',
    'S02: Permatang Damar Laut',
    'S03: Penang International Airport',
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
    'S16: East Jelutong',
    'S17: Sungai Pinang',
    'S18: Bandar Sri Pinang',
    'S19: Macallum',
    'S20: KOMTAR',
    'S31: Penang Sentral'];
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
    <title>Voyage - Maps</title>
    <link rel="icon" type="image/x-icon" href="./pics/Icon/voyage1.ico">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    <div class="maps-page-content">
        <main class="hero-section maps-hero">
            <div class="hero-copy">
                <span class="hero-eyebrow">Route Planning</span>
                <h1 class="hero-title">Explore the Voyage network with confidence.</h1>
                <p>Discover every stop on the line, check the route map, and choose a station for your next journey.</p>
                <div class="hero-actions">
                    <a class="primary-button" href="booking.php">Book a Ride</a>
                    <a class="secondary-button" href="faq.php">Need Help?</a>
                </div>
            </div>
            <div class="hero-visual">
                <img src="./pics/bannerstuff.png" alt="Voyage route planning">
            </div>
        </main>

        <section class="section-title maps-intro">
            <h2>Voyage Route Map</h2>
            <p>Explore the stops available on the Voyage line and plan your journey.</p>
        </section>

        <section class="maps-workspace">
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="success-banner">
                    <strong>Thank you!</strong> Your checkout is complete. Bon Voyage!
                </div>
            <?php endif; ?>
            <div class="map-and-list">
                <div class="map-column">
                    <img src="./pics/Groundbreaking-Alignment-map.png" alt="Groundbreaking Alignment" class="maps-image">
                    <div class="map-overlay" aria-hidden="true"></div>
                </div>
                <div class="list-column list-below">
                    <div class="profile-info">
                        <?php foreach ($stops as $index => $stop): ?>
                            <div class="profile-row stop-item" role="button" tabindex="0" aria-pressed="false" data-stop-index="<?php echo $index; ?>" data-x="50" data-y="50">
                                <?php if ($index === 2): ?>
                                    <img src="./pics/Icon/airport.png" alt="airport" class="stop-icon">
                                <?php else: ?>
                                    <img src="./pics/Icon/location.png.png" alt="location" class="stop-icon">
                                <?php endif; ?>
                                <span class="stop-label"><?php echo htmlspecialchars($stop); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="selected-stop" aria-live="polite">Select a station to highlight it on the map.</p>
                </div>
            </div>
            <script>
                (function(){
                    // Basic click-to-highlight: positions use percentage (data-x, data-y). Default 50/50.
                    const stopItems = document.querySelectorAll('.stop-item');
                    const overlay = document.querySelector('.map-overlay');

                    if (!overlay) return;

                    let marker = null;

                    function ensureMarker(){
                        if (marker) return marker;
                        marker = document.createElement('div');
                        marker.className = 'map-marker';
                        marker.innerHTML = '<div class="pulse"></div><div class="label"></div>';
                        overlay.appendChild(marker);
                        return marker;
                    }

                    const selectedStop = document.querySelector('.selected-stop');

                    function selectStop(item){
                            // highlight selected row
                            document.querySelectorAll('.stop-item').forEach(r=>{
                                r.classList.remove('selected');
                                r.setAttribute('aria-pressed', 'false');
                            });
                            item.classList.add('selected');
                            item.setAttribute('aria-pressed', 'true');

                            const x = parseFloat(item.getAttribute('data-x') || 50);
                            const y = parseFloat(item.getAttribute('data-y') || 50);
                            const stopIndex = item.getAttribute('data-stop-index') || '';
                            const label = item.textContent.trim();

                            if (selectedStop) {
                                selectedStop.textContent = 'Selected Station: ' + label;
                            }
                            item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                            const m = ensureMarker();
                            const labelEl = m.querySelector('.label');
                            labelEl.textContent = label;

                            // position marker using percentages
                            m.style.left = x + '%';
                            m.style.top = y + '%';
                            m.setAttribute('data-for', stopIndex);

                            // ensure visible
                            m.classList.add('visible');
                            // briefly animate
                            m.classList.remove('pulse-anim');
                            void m.offsetWidth;
                            m.classList.add('pulse-anim');
                    }

                    stopItems.forEach(item => {
                        item.addEventListener('click', function(){
                            selectStop(item);
                        });
                        item.addEventListener('keydown', function(event){
                            if (event.key === 'Enter' || event.key === ' ') {
                                event.preventDefault();
                                selectStop(item);
                            }
                        });
                    });
                })();
            </script>
        </section>
    </div>
    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
    