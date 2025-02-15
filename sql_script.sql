CREATE DATABASE task_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE task_db;

CREATE TABLE population_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prefecture VARCHAR(255) NOT NULL,
    year INT NOT NULL,
    population INT NOT NULL
);