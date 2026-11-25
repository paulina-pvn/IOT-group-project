
<?php
// Temporary room data for frontend development.
// These values are examples, not live sensor readings.
ini_set('display_errors',1);
error_reporting(E_ALL);

$rooms = [
    [
        'name' => 'Room 1',
        'capacity' => 30,
        'projector' => true,
        'computers' => false,
        'temperature' => 22.1,
        'humidity' => 45,
        'co2' => 650,
        'light' => 420,
        'noise' => 38
    ],
    [
        'name' => 'Room 2',
        'capacity' => 50,
        'projector' => true,
        'computers' => true,
        'temperature' => 25.3,
        'humidity' => 58,
        'co2' => 1150,
        'light' => 310,
        'noise' => 54
    ],
    [
        'name' => 'Room 3',
        'capacity' => 20,
        'projector' => false,
        'computers' => true,
        'temperature' => 21.5,
        'humidity' => 42,
        'co2' => 590,
        'light' => 480,
        'noise' => 35
    ],
    [
        'name' => 'Room 4',
        'capacity' => 40,
        'projector' => true,
        'computers' => false,
        'temperature' => 23.2,
        'humidity' => 49,
        'co2' => 800,
        'light' => 390,
        'noise' => 43
    ],
    [
        'name' => 'Room 5',
        'capacity' => 60,
        'projector' => true,
        'computers' => true,
        'temperature' => 26.1,
        'humidity' => 65,
        'co2' => 1400,
        'light' => 270,
        'noise' => 61
    ]
];

// Simple illustrative score for testing the interface.
// The final AHP decision engine will replace this.
function demoScore($room) {
    $score = 100;

    $score -= abs($room['temperature'] - 22) * 5;
    $score -= abs($room['humidity'] - 45) * 0.3;
    $score -= max(0, $room['co2'] - 600) / 50;
    $score -= abs($room['light'] - 450) / 30;
    $score -= max(0, $room['noise'] - 35) * 0.5;

    return round(max(0, min(100, $score)));
}

$capacity = max(0, (int) ($_GET['capacity'] ?? 0));
$needsProjector = isset($_GET['projector']);
$needsComputers = isset($_GET['computers']);

$results = [];

foreach ($rooms as $room) {
    if ($room['capacity'] < $capacity) {
        continue;
    }

    if ($needsProjector && !$room['projector']) {
        continue;
    }

    if ($needsComputers && !$room['computers']) {
        continue;
    }

    $room['score'] = demoScore($room);
    $results[] = $room;
}

usort($results, function ($a, $b) {
    return $b['score'] <=> $a['score'];
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Find a Room — ComfortRoom</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header>
    <nav>
        <h2>ComfortRoom</h2>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="find-room.php">Find a Room</a>
        </div>
    </nav>
</header>

<main class="room-page">

    <h1>Find your ideal room</h1>
    <p class="page-description">
        Choose your requirements and compare available rooms.
    </p>

    <form method="GET" class="room-filters">
        <h2>Room requirements</h2>

        <div class="filter-field">
            <label for="capacity">Minimum capacity</label>
            <input
                type="number"
                id="capacity"
                name="capacity"
                min="0"
                max="1000"
                value="<?= $capacity ?>"
            >
        </div>

        <label class="checkbox-label">
            <input
                type="checkbox"
                name="projector"
                <?= $needsProjector ? 'checked' : '' ?>
            >
            Projector required
        </label>

        <label class="checkbox-label">
            <input
                type="checkbox"
                name="computers"
                <?= $needsComputers ? 'checked' : '' ?>
            >
            Computers required
        </label>

        <button type="submit" class="primary-button">
            Find Rooms
        </button>

        <a href="find-room.php" class="reset-link">
            Reset filters
        </a>
    </form>

    <section class="room-results">
        <h2>Recommended rooms</h2>

        <p class="demo-notice">
            Demo mode: example measurements and simplified scores.
            Live sensor data and AHP ranking are not connected yet.
        </p>

        <p class="results-count">
            <?= count($results) ?> matching room(s)
        </p>

        <?php if (empty($results)): ?>
            <div class="empty-results">
                <h3>No matching rooms</h3>
                <p>Try changing your requirements.</p>
            </div>
        <?php endif; ?>

        <div class="room-grid">
            <?php foreach ($results as $room): ?>
                <article class="room-card">

                    <div class="room-card-header">
                        <h3>
                            <?= htmlspecialchars($room['name']) ?>
                        </h3>
                        <span class="score">
                            <?= $room['score'] ?>/100
                        </span>
                    </div>

                    <p class="capacity">
                        Capacity: <?= $room['capacity'] ?> people
                    </p>

                    <div class="sensor-grid">
                        <div>
                            <small>Temperature</small>
                            <strong>
                                <?= $room['temperature'] ?> °C
                            </strong>
                        </div>

                        <div>
                            <small>Humidity</small>
                            <strong>
                                <?= $room['humidity'] ?>%
                            </strong>
                        </div>

                        <div>
                            <small>CO₂</small>
                            <strong>
                                <?= $room['co2'] ?> ppm
                            </strong>
                        </div>

                        <div>
                            <small>Light</small>
                            <strong>
                                <?= $room['light'] ?> lux
                            </strong>
                        </div>

                        <div>
                            <small>Noise</small>
                            <strong>
                                <?= $room['noise'] ?> dB
                            </strong>
                        </div>
                    </div>

                    <div class="facilities">
                        <span>
                            Projector:
                            <?= $room['projector'] ? 'Yes' : 'No' ?>
                        </span>
                        <span>
                            Computers:
                            <?= $room['computers'] ? 'Yes' : 'No' ?>
                        </span>
                    </div>

                </article>
            <?php endforeach; ?>
        </div>
    </section>

</main>

<footer>
    <p>ComfortRoom — IoT Group Project</p>
</footer>

</body>
</html>
