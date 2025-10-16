-- init_db.sql
CREATE DATABASE IF NOT EXISTS busly CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE busly;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  api_token VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Routes table
CREATE TABLE IF NOT EXISTS routes (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    route_from VARCHAR(100) NOT NULL,
    route_to VARCHAR(100) NOT NULL,
    stops TEXT DEFAULT NULL, -- JSON array of stops, e.g., '["dhaka","tangail","sylhet"]'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_route (route_from, route_to),
) ENGINE=InnoDB;

-- Buses table (example)
-- Buses table referencing routes
CREATE TABLE IF NOT EXISTS buses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  route_id INT NOT NULL, -- FK to routes table
  bus_number VARCHAR(50) NOT NULL,
  bus_name VARCHAR(100) NOT NULL,
  bus_type ENUM('AC', 'Non-AC', 'Sleeper', 'Seater') NOT NULL DEFAULT 'Non-AC',
  total_seats INT NOT NULL DEFAULT 0,
  available_seats INT NOT NULL DEFAULT 0,
  fare DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  departure_datetime DATETIME NOT NULL,
  arrival_datetime DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bus_number (bus_number),
  INDEX idx_route_id (route_id),
  INDEX idx_departure (departure_datetime),
  FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- Reservations table
CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  bus_id INT NOT NULL,
  seat_number VARCHAR(20) NOT NULL,
  status ENUM('confirmed','cancelled') DEFAULT 'confirmed',
  reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (bus_id) REFERENCES buses(id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- Example routes insert
INSERT INTO routes (route_from, route_to, stops) VALUES
('dhaka', 'chittagong', '["dhaka","narayanganj","feni","chittagong"]'),
('dhaka', 'sylhet', '["dhaka","mymensingh","sylhet"]'),
('dhaka', 'rajshahi', '["dhaka","bogura","rajshahi"]'),
('chittagong', "cox's bazar", '["chittagong","cox’s bazar"]'),
('khulna', 'dhaka', '["khulna","jessore","dhaka"]'),
('sylhet', 'dhaka', '["sylhet","mymensingh","dhaka"]');



-- Insert Test buses (using route_id)
-- First, ensure routes have IDs
-- Example: assume routes inserted earlier have IDs 1 to 6 in the order inserted

INSERT INTO buses (
  route_id, bus_number, bus_name, bus_type,
  total_seats, available_seats,
  fare, departure_datetime, arrival_datetime
) VALUES
(1, 'BUS-101', 'Green Line Express', 'AC', 20, 20, 850.00, '2025-10-20 07:30:00', '2025-10-20 13:30:00'),
(2, 'BUS-102', 'Ena Deluxe', 'Non-AC', 20, 20, 700.00, '2025-10-20 09:00:00', '2025-10-20 14:00:00'),
(3, 'BUS-103', 'Hanif Enterprise', 'AC', 20, 20, 650.00, '2025-10-20 08:00:00', '2025-10-20 12:30:00'),
(4, 'BUS-104', 'Shohag Elite', 'Sleeper', 20, 20, 500.00, '2025-10-20 06:30:00', '2025-10-20 10:00:00'),
(5, 'BUS-105', 'Desh Travels', 'AC', 20, 20, 800.00, '2025-10-20 20:00:00', '2025-10-20 21:00:00'),
(6, 'BUS-106', 'Ena Transport', 'Non-AC', 20, 20, 700.00, '2025-10-20 18:30:00', '2025-10-20 00:30:00');

