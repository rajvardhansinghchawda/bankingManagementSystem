CREATE DATABASE bank_management;

USE bank_management;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    balance DECIMAL(10, 2) DEFAULT 0.00
);
