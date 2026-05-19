<?php
session_start();
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
    'S16: East Jelutong',
    'S17: Sungai Pinang',
    'S18: Bandar Sri Pinang',
    'S19: Macallum',
    'S20: Komtar',
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
    <div class="auth-page">
        <div class="auth-card" style="max-width:1200px; width:90%;">
            <h1 class="auth-title">Voyage Route Map</h1>
            <p class="auth-subtitle">Explore the stops available on the Voyage line and plan your journey.</p>
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
                            <div class="profile-row stop-item" data-stop-index="<?php echo $index; ?>" data-x="50" data-y="50">
                                <?php if ($index === 2): ?>
                                    <img src="./pics/Icon/airport.png" alt="airport" class="stop-icon">
                                <?php else: ?>
                                    <img src="./pics/Icon/location.png.png" alt="location" class="stop-icon">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($stop); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
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

                    stopItems.forEach(item => {
                        item.style.cursor = 'pointer';
                        item.addEventListener('click', function(){
                            // highlight selected row
                            document.querySelectorAll('.stop-item').forEach(r=>r.classList.remove('selected'));
                            this.classList.add('selected');

                            const x = parseFloat(this.getAttribute('data-x') || 50);
                            const y = parseFloat(this.getAttribute('data-y') || 50);
                            const stopIndex = this.getAttribute('data-stop-index') || '';
                            const label = this.textContent.trim();

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
                        });
                    });
                })();
            </script>
            <div class="auth-actions">
                <a class="auth-link" href="maps.php">Refresh map</a>
            </div>
        </div>
    </div>
</body>
</html>
    