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
$stopPositions = [
    'A01: PSR-A' => ['x' => 17, 'y' => 99],
    'S02: Permatang Damar Laut' => ['x' => 24, 'y' => 95],
    'S03: Penang International Airport' => ['x' => 35, 'y' => 85],
    'S04: Sungai Tiram' => ['x' => 34, 'y' => 80],
    'S05: FIZ South' => ['x' => 37, 'y' => 76],
    'S06: FIZ North' => ['x' => 40, 'y' => 72],
    'S07: Jalan Tengah' => ['x' => 39, 'y' => 68],
    'S08: SPICE' => ['x' => 32, 'y' => 64],
    'S09: Bukit Jambul' => ['x' => 42, 'y' => 60],
    'S10: Sungai Nibong' => ['x' => 47, 'y' => 59],
    'S11: Sungai Dua' => ['x' => 50, 'y' => 54],
    'S12: Batu Uban' => ['x' => 52, 'y' => 49],
    'S13: Jalan Universiti' => ['x' => 60, 'y' => 43],
    'S14: Gelugor' => ['x' => 56, 'y' => 38],
    'S15: Penang Waterfront' => ['x' => 65, 'y' => 32],
    'S16: East Jelutong' => ['x' => 64, 'y' => 23],
    'S17: Sungai Pinang' => ['x' => 68, 'y' => 19],
    'S18: Bandar Sri Pinang' => ['x' => 73, 'y' => 15],
    'S19: Macallum' => ['x' => 71, 'y' => 12],
    'S20: KOMTAR' => ['x' => 71, 'y' => 8],
    'S31: Penang Sentral' => ['x' => 101, 'y' => 21],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem("voyage-theme")||"dark";document.documentElement.classList.add(t+"-mode");if(document.body)document.body.classList.add(t+"-mode");else document.addEventListener("DOMContentLoaded",function(){document.body.classList.add(t+"-mode")});}catch(e){}})();</script>
    <script src="theme.js" defer></script>
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
            <h2>Penang LRT Mutiara Line</h2>
        </section>

        <section class="maps-workspace">
            <div class="map-and-list">
                <div class="map-column">
                    <div class="map-visual">
                        <img src="./pics/Groundbreaking-Alignment-map.png" alt="Groundbreaking Alignment" class="maps-image">
                        <div class="map-overlay" aria-hidden="true"></div>
                    </div>
                    <p class="map-source">Source: Gamuda Berhad</p>
                </div>
                <div class="list-column list-below">
                    <div class="profile-info">
                        <?php foreach ($stops as $index => $stop): ?>
                            <?php $position = $stopPositions[$stop] ?? ['x' => 50, 'y' => 50]; ?>
                            <div class="profile-row stop-item" role="button" tabindex="0" aria-pressed="false" data-stop-index="<?php echo $index; ?>" data-x="<?php echo $position['x']; ?>" data-y="<?php echo $position['y']; ?>">
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
    