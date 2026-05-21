CREATE DATABASE IF NOT EXISTS digital_internship CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE digital_internship;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'employer') NOT NULL,
    resume_path VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    headline VARCHAR(255) DEFAULT NULL,
    about TEXT DEFAULT NULL,
    skills TEXT DEFAULT NULL,
    experience TEXT DEFAULT NULL,
    education TEXT DEFAULT NULL,
    portfolio VARCHAR(255) DEFAULT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS internships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(255) DEFAULT 'Uncategorized',
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    stipend VARCHAR(255) DEFAULT 'Unpaid',
    requirements TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    internship_id INT NOT NULL,
    resume_path VARCHAR(255) NOT NULL,
    cover_letter TEXT,
    employer_message TEXT DEFAULT NULL,
    status ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE
);
