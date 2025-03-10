CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    floor INT NOT NULL,
    room_number INT NOT NULL UNIQUE,
    capacity INT NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    status TINYINT DEFAULT 0, -- 0 = Available, 1 = Booked, 2 = Under Maintenance
    room_type_id INT,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE SET NULL
);
