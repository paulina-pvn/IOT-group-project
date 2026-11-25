
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/db.php';

$databaseError = null;
$rooms = [];

try {
    $rows = db_rows(
        'SELECT r.name,
                r.seating_capacity AS capacity,
                r.videoprojector AS projector,
                (r.computers > 0) AS computers,
                round(s.temperature::numeric, 1) AS temperature,
                round(s.humidity::numeric, 0) AS humidity,
                round(s.co2::numeric, 0) AS co2,
                round(s.light::numeric, 0) AS light,
                round(s.sound::numeric, 0) AS noise
         FROM sensor_rooms r
         JOIN LATERAL (
             SELECT temperature, humidity, co2, light, sound
             FROM sensor_readings
             WHERE room_name = r.name
             ORDER BY measured_at DESC
             LIMIT 1
         ) s ON true
         ORDER BY r.name'
    );

    foreach ($rows as $row) {
        $rooms[] = [
            'name' => $row['name'],
            'capacity' => (int) $row['capacity'],
            'projector' => in_array($row['projector'], [true, 't', 'true', '1', 1], true),
            'computers' => in_array($row['computers'], [true, 't', 'true', '1', 1], true),
            'temperature' => (float) $row['temperature'],
            'humidity' => (float) $row['humidity'],
            'co2' => (float) $row['co2'],
            'light' => (float) $row['light'],
            'noise' => (float) $row['noise'],
        ];
    }
} catch (RuntimeException $exception) {
    $databaseError = $exception->getMessage();
}

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

        <?php if ($databaseError !== null): ?>
            <div class="empty-results">
                <h3>Database unavailable</h3>
                <p><?= htmlspecialchars($databaseError) ?></p>
            </div>
        <?php endif; ?>

        <p class="demo-notice">
            Measurements come from the database. The score is still a simple demo formula, not AHP.
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
