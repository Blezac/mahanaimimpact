-- === database.sql ===

CREATE DATABASE IF NOT EXISTS mahanaim_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE mahanaim_db;

-- Events Table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    early_bird_deadline DATE NOT NULL,
    early_bird_price DECIMAL(10, 2) NOT NULL,
    regular_price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Registrations Table
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    business_name VARCHAR(255),
    ticket_type ENUM('vendor', 'attendee', 'sponsor') NOT NULL,
    amount_paid DECIMAL(10, 2) NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    transaction_ref VARCHAR(255) UNIQUE,
    qr_code VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mentors Table
CREATE TABLE IF NOT EXISTS mentors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    expertise VARCHAR(255) NOT NULL,
    experience TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sponsors Table
CREATE TABLE IF NOT EXISTS sponsors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    student_preference TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Data for Events
INSERT INTO events (title, location, event_date, early_bird_deadline, early_bird_price, regular_price, description) VALUES
('Mahanaim Trade Fair 2025', 'Lagos Convention Center', '2025-05-15', '2025-04-15', 15000.00, 25000.00, 'Join us for the biggest youth-focused trade fair of the year. Network, showcase, and grow your business.'),
('Financial Literacy Summit', 'Abuja Business Hub', '2025-06-20', '2025-05-20', 10000.00, 15000.00, 'Learn wealth creation, investment strategies, and financial freedom principles.'),
('Youth Entrepreneurship Expo', 'Port Harcourt Events Center', '2025-07-10', '2025-06-10', 12000.00, 20000.00, 'Empower your business journey with mentorship, funding opportunities, and networking.');

-- Sample Admin (password: admin123)
INSERT INTO admins (username, password, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@mahanaimnetwork.org');
