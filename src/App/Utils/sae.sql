-- Drop the annote database if it exists (with all its tables)
DROP DATABASE IF EXISTS annote;

-- Create the annote database fresh
CREATE DATABASE annote;
USE annote;

-- Create Users table
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (username),
    UNIQUE (email)
);

-- Create UserStatus table
CREATE TABLE UserStatus (
    user_id INT PRIMARY KEY,
    is_online BOOLEAN DEFAULT FALSE,
    last_active_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Conversation table
CREATE TABLE Conversation (
    conversation_id INT AUTO_INCREMENT PRIMARY KEY,
    user_1_id INT NOT NULL,
    user_2_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_1_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (user_2_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    UNIQUE (user_1_id, user_2_id),
    CHECK (user_1_id < user_2_id)
);

-- Create Message table
CREATE TABLE Message (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES Conversation(conversation_id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Annotation table
CREATE TABLE Annotation (
    annotation_id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    annotator_id INT NOT NULL,
    emotion ENUM('joie', 'colère', 'tristesse', 'surprise', 'dégoût', 'peur') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES Message(message_id) ON DELETE CASCADE,
    FOREIGN KEY (annotator_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    UNIQUE (message_id, annotator_id)
);