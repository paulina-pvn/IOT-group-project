-- Small demo database matching src/pages/find-room.php.
-- Example measurements, not live sensor readings.

CREATE TABLE IF NOT EXISTS rooms (
    id          integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name        text NOT NULL UNIQUE,
    capacity    integer NOT NULL,
    projector   boolean NOT NULL,
    computers   boolean NOT NULL,
    temperature numeric(4,1) NOT NULL,
    humidity    integer NOT NULL,
    co2         integer NOT NULL,
    light       integer NOT NULL,
    noise       integer NOT NULL
);

INSERT INTO rooms (name, capacity, projector, computers, temperature, humidity, co2, light, noise)
VALUES
    ('Room 1', 30, true,  false, 22.1, 45,  650, 420, 38),
    ('Room 2', 50, true,  true,  25.3, 58, 1150, 310, 54),
    ('Room 3', 20, false, true,  21.5, 42,  590, 480, 35),
    ('Room 4', 40, true,  false, 23.2, 49,  800, 390, 43),
    ('Room 5', 60, true,  true,  26.1, 65, 1400, 270, 61)
ON CONFLICT (name) DO NOTHING;

-- Time series from the supplied sensor JSON. Room names are Room_1 … Room_5,
-- separate from the five demo cards in the rooms table above.

CREATE TABLE IF NOT EXISTS sensor_rooms (
    name                  text PRIMARY KEY,
    videoprojector        boolean NOT NULL,
    seating_capacity      integer NOT NULL,
    computers             integer NOT NULL DEFAULT 0,
    robots_for_training   integer NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS sensor_readings (
    room_name    text NOT NULL REFERENCES sensor_rooms (name),
    measured_at  timestamptz NOT NULL,
    temperature  double precision,
    humidity     double precision,
    co2          double precision,
    light        double precision,
    sound        double precision,
    voc          double precision,
    pm25         double precision,
    pm10         double precision,
    PRIMARY KEY (room_name, measured_at)
);
