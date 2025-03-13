CREATE DATABASE hope_for_strays;
USE hope_for_strays;

CREATE TABLE Users (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(255) NOT NULL,
    lname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    birthday date NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL,
    profile VARCHAR(255) DEFAULT '../assets/user_profile/default.jpg'
);

CREATE TABLE Shelter (
    shelterID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    contact_num VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL
);

CREATE TABLE Pet (
    petID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('dog', 'cat', 'other') NOT NULL,
    breed VARCHAR(255) NULL,
    size ENUM('small', 'medium', 'large') NOT NULL,
    age int,
	gender ENUM('male','female') NOT NULL,
    vaccination VARCHAR(255) DEFAULT 'none',
    medical_con VARCHAR(255) DEFAULT 'none',
    dietary_needs VARCHAR(255) DEFAULT 'none',
    status ENUM('adopted','pending','available') NOT NULL,
    profile VARCHAR(255) DEFAULT '../assets/adopt/default.jpg',
    shelterID INT,
    FOREIGN KEY (shelterID) REFERENCES Shelter(shelterID) ON DELETE SET NULL ON UPDATE CASCADE
);





CREATE TABLE Chatbot (
    chatID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NULL,
    message TEXT NOT NULL,
    response TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES User(userID) ON DELETE SET NULL

);
