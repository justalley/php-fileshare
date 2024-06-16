CREATE DATABASE IF NOT EXISTS file_sharing;
USE file_sharing;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user'
);

INSERT INTO users (id, username, email, password_hash, role) VALUES ('1', 'admin', 'admin@gmail.com', '$2y$10$cl5DaS10l18lRFPLyFGYLuQDCOrV6ILCYxZHwEDKijkfpmekSS8.C', 'admin');
INSERT INTO users (id, username, email, password_hash, role) VALUES ('2', 'user1', 'user1@gmail.com', '$2y$10$cl5DaS10l18lRFPLyFGYLuQDCOrV6ILCYxZHwEDKijkfpmekSS8.C', 'user');

CREATE TABLE IF NOT EXISTS file_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(255) NOT NULL,
    creator_id INT NOT NULL,
    FOREIGN KEY (creator_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(255) NOT NULL,
    uploaded_by INT,
    group_id INT,
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    FOREIGN KEY (group_id) REFERENCES file_groups(id)
);

CREATE TABLE user_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    group_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (group_id) REFERENCES file_groups(id)
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_id INT,
    user_id INT,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (file_id) REFERENCES files(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);