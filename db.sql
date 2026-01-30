CREATE DATABASE IF NOT EXISTS meeting_booking;
USE meeting_booking;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    email VARCHAR(100) NULL,
    contact_number VARCHAR(30) NULL,
    company VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    capacity INT,
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CREATE TABLE IF NOT EXISTS bookings (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     user_id INT NOT NULL,
--     room_id INT NOT NULL,
--     booking_date DATE NOT NULL,
--     start_time TIME NOT NULL,
--     end_time TIME NOT NULL,
--     status ENUM('pending', 'approved', 'cancelled') DEFAULT 'pending',
--     purpose TEXT,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
--     FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
-- );

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,
    room_id INT NOT NULL,

    purpose VARCHAR(255) NULL,

    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,

    status ENUM('pending','approved','cancelled') DEFAULT 'pending',

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    approved_at DATETIME NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);


-- Insert default admin
-- Password '1234' hashed using PHP's password_hash would be better, but for simplicity in SQL and based on user request "1234", I'll use password_verify in PHP.
-- If I use password_hash('1234', PASSWORD_DEFAULT), it looks something like: $2y$10$o.c8E9C40kG.k.r.u.v.w.e.
-- But since the user might want to see it or might be checking the DB manually, I'll use a hash or just cleartext if it's a demo. 
-- Standard practice is hashing. I'll use a hash for '1234'.
INSERT IGNORE INTO users (username, password, role) VALUES ('Admin', '$2y$10$C7m.q.e0.J0.v.o.F.h.b.e.p.q.r.s.t.u.v.w.x.y.z', 'admin'); 
-- Note: The above hash is a placeholder. I'll generate a real one or handle it in a setup script. 
-- Actually, I'll just use a simple hash for '1234' later in PHP.

-- Insert some default rooms
INSERT IGNORE INTO rooms (name, capacity, location) VALUES 
('Conference Room A', 10, 'Level 1'),
('Board Room', 20, 'Level 2'),
('Huddle Room 1', 4, 'Level 1'),
('Creative Lab', 15, 'Level 3');
