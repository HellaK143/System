create database umic;
use umic;

-- Table: Entrepreneur
CREATE TABLE Entrepreneur (
    entrepreneur_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    student_id VARCHAR(20) UNIQUE,
    department VARCHAR(100),
    course VARCHAR(100),
    year_of_study INT,
    gender ENUM('male', 'female', 'other'),
    profile_picture VARCHAR(100),
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: Mentor
CREATE TABLE Mentor (
    mentor_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    expertise_area VARCHAR(100),
    phone VARCHAR(20),
    assigned_department VARCHAR(100)
);

-- Table: Auth_Logins
CREATE TABLE Auth_Logins (
    login_id INT AUTO_INCREMENT PRIMARY KEY,
    entrepreneur_id INT,
    mentor_id INT,
    status ENUM('success', 'failed') NOT NULL,
    login_time DATETIME NOT NULL,
    logout_time DATETIME,
    ip_address VARCHAR(45),
    device_info TEXT,
    session_token VARCHAR(255),
    two_factor_code VARCHAR(10),
    two_factor_status ENUM('pending', 'verified', 'failed'),
    FOREIGN KEY (entrepreneur_id) REFERENCES Entrepreneur(entrepreneur_id),
    FOREIGN KEY (mentor_id) REFERENCES Mentor(mentor_id)
);

-- Table: Startup_Idea
CREATE TABLE Startup_Idea (
    idea_id INT AUTO_INCREMENT PRIMARY KEY,
    entrepreneur_id INT NOT NULL,
    title VARCHAR(255),
    description TEXT,
    sector VARCHAR(100),
    stage VARCHAR(100),
    submission_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (entrepreneur_id) REFERENCES Entrepreneur(entrepreneur_id)
);

-- Table: Recruitment_Event
CREATE TABLE Recruitment_Event (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255),
    description TEXT,
    date DATE,
    location VARCHAR(255),
    status ENUM('upcoming', 'ongoing', 'completed')
);

-- Table: Application
CREATE TABLE Application (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    entrepreneur_id INT,
    event_id INT,
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('submitted', 'reviewed', 'accepted', 'rejected') DEFAULT 'submitted',
    FOREIGN KEY (entrepreneur_id) REFERENCES Entrepreneur(entrepreneur_id),
    FOREIGN KEY (event_id) REFERENCES Recruitment_Event(event_id)
);

-- Table: Mentorship_Assignment
CREATE TABLE Mentorship_Assignment (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    mentor_id INT,
    entrepreneur_id INT,
    start_date DATE,
    end_date DATE,
    status ENUM('active', 'completed', 'terminated'),
    FOREIGN KEY (mentor_id) REFERENCES Mentor(mentor_id),
    FOREIGN KEY (entrepreneur_id) REFERENCES Entrepreneur(entrepreneur_id)
);

-- Table: Innovation_Project
CREATE TABLE Innovation_Project (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    entrepreneur_id INT,
    title VARCHAR(255),
    objective TEXT,
    funding_status ENUM('not funded', 'partially funded', 'fully funded'),
    project_status ENUM('planned', 'in progress', 'completed', 'on hold'),
    start_date DATE,
    end_date DATE,
    FOREIGN KEY (entrepreneur_id) REFERENCES Entrepreneur(entrepreneur_id)
);


--inserting data--
-- Insert Entrepreneurs
INSERT INTO Entrepreneur (
    first_name, last_name, email, phone, student_id,
    department, course, year_of_study, gender, profile_picture, registration_date
)
VALUES 
('Steven', 'Kizza', 'Steven.Kizza@umu.ac.ug', '0701234567', 'STU001', 'ICT', 'Computer Science', 3, 'female', 'uploads/Steven.jpg', '2025-06-01 09:00:00'),
('nicholas', 'Nsubuga', 'nicholas.Nsubuga@umu.ac.ug', '0707654321', 'STU002', 'Business', 'Entrepreneurship', 2, 'male', 'uploads/nicholas.jpg', '2025-06-02 10:30:00');


-- Insert Mentors
INSERT INTO Mentor (full_name, email, expertise_area, phone, assigned_department)
VALUES 
('Dr. Grace Tumusiime', 'grace.t@umu.ac.ug', 'ICT Innovation', '0770001111', 'ICT'),
('Mr. Samuel Lwanga', 'samuel.l@umu.ac.ug', 'Business Development', '0772223333', 'Business');

-- Insert Auth_Logins
INSERT INTO Auth_Logins (entrepreneur_id, mentor_id, status, login_time, logout_time, ip_address, device_info, session_token, two_factor_code, two_factor_status)
VALUES 
(1, NULL, 'success', NOW(), NULL, '192.168.1.10', 'Mozilla/5.0 (Windows NT 10.0)', 'abc123session', '654321', 'verified'),
(NULL, 1, 'success', NOW(), NULL, '192.168.1.11', 'Chrome Mobile/91.0', 'xyz456session', '123456', 'verified');

-- Insert Startup_Idea
INSERT INTO Startup_Idea (entrepreneur_id, title, description, sector, stage)
VALUES 
(1, 'AgroSmart', 'A smart farming platform using sensors and mobile analytics.', 'Agriculture', 'idea'),
(2, 'YouthMarket', 'An online marketplace targeting student-created products.', 'E-Commerce', 'prototype');

-- Insert Recruitment_Event
INSERT INTO Recruitment_Event (event_name, description, date, location, status)
VALUES 
('UMIC Pitch Day 2025', 'An event for students to pitch their startup ideas.', '2025-07-01', 'Main Hall - UMU', 'upcoming'),
('Innovation Bootcamp', '5-day workshop on turning ideas into businesses.', '2025-06-15', 'Innovation Hub', 'upcoming');

-- Insert Application
INSERT INTO Application (entrepreneur_id, event_id, status)
VALUES 
(1, 1, 'submitted'),
(2, 2, 'submitted');

-- Insert Mentorship_Assignment
INSERT INTO Mentorship_Assignment (mentor_id, entrepreneur_id, start_date, end_date, status)
VALUES 
(1, 1, '2025-06-01', '2025-12-01', 'active'),
(2, 2, '2025-06-01', '2025-10-01', 'active');

-- Insert Innovation_Project
INSERT INTO Innovation_Project (entrepreneur_id, title, objective, funding_status, project_status, start_date, end_date)
VALUES 
(1, 'AgroSmart Sensor Kit', 'Develop low-cost IoT kits for smallholder farmers.', 'not funded', 'planned', '2025-06-10', '2025-12-31'),
(2, 'YouthMarket App', 'Create mobile app MVP for university students.', 'partially funded', 'in progress', '2025-05-01', '2025-11-01');
