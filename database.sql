CREATE DATABASE IF NOT EXISTS greenfield_academy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE greenfield_academy;

-- USERS (students + staff login)
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(120)        NOT NULL,
    email       VARCHAR(180)        NOT NULL UNIQUE,
    password    VARCHAR(255)        NOT NULL,  -- bcrypt hash
    role        ENUM('student','staff','admin') NOT NULL DEFAULT 'student',
    level       ENUM('early_years','o_level','a_level','university') DEFAULT NULL,
    student_id  VARCHAR(20)         UNIQUE,
    avatar      VARCHAR(255)        DEFAULT NULL,
    created_at  TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
);

-- DEPARTMENTS
CREATE TABLE departments (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)        NOT NULL,
    level       ENUM('early_years','o_level','a_level','university') NOT NULL,
    description TEXT,
    icon        VARCHAR(50)
);

-- COURSES
CREATE TABLE courses (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    department_id   INT             NOT NULL,
    code            VARCHAR(20)     NOT NULL UNIQUE,
    title           VARCHAR(150)    NOT NULL,
    description     TEXT,
    credits         TINYINT         DEFAULT 3,
    level           ENUM('early_years','o_level','a_level','university') NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);

-- ADMISSIONS ENQUIRIES
CREATE TABLE admissions (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    applicant_name  VARCHAR(120)    NOT NULL,
    email           VARCHAR(180)    NOT NULL,
    phone           VARCHAR(20),
    parent_name     VARCHAR(120)    DEFAULT NULL,
    parent_email    VARCHAR(180)    DEFAULT NULL,
    parent_phone    VARCHAR(20)     DEFAULT NULL,
    level_applying  ENUM('early_years','o_level','a_level','university') NOT NULL,
    selected_courses TEXT,
    message         TEXT,
    status          ENUM('pending','reviewed','accepted','declined') DEFAULT 'pending',
    submitted_at    TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
);

-- CONTACT MESSAGES
CREATE TABLE contact_messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120)    NOT NULL,
    email       VARCHAR(180)    NOT NULL,
    subject     VARCHAR(200),
    message     TEXT            NOT NULL,
    replied     TINYINT(1)      NOT NULL DEFAULT 0,
    sent_at     TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
);

-- ENROLLMENTS (which courses each student is taking)
CREATE TABLE enrollments (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    student_id   INT NOT NULL,
    course_id    INT NOT NULL,
    enrolled_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_enrollment (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES users(id)   ON DELETE CASCADE,
    FOREIGN KEY (course_id)  REFERENCES courses(id) ON DELETE CASCADE
);

-- NEWS & ANNOUNCEMENTS (used on home page)
CREATE TABLE news (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200)    NOT NULL,
    excerpt     TEXT,
    content     LONGTEXT,
    author_id   INT,
    image       VARCHAR(255),
    published_at DATE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ASSIGNMENTS
CREATE TABLE assignments (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200)    NOT NULL,
    description TEXT,
    level       ENUM('early_years','o_level','a_level','university') NOT NULL,
    due_date    DATE            NOT NULL,
    assigned_by INT,
    created_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL
);

-- TIMETABLE
CREATE TABLE timetables (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    level       ENUM('early_years','o_level','a_level','university') NOT NULL,
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
    start_time  TIME            NOT NULL,
    end_time    TIME            NOT NULL,
    subject     VARCHAR(150)    NOT NULL,
    teacher     VARCHAR(120),
    venue       VARCHAR(100),
    created_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
);

-- SEED DATA
-- Admin user  (password: Admin@1234)
INSERT INTO users (full_name, email, password, role, student_id) VALUES
('Dr. Grace Mwangi', 'admin@greenfield.ac.ke',
 '$2b$12$R4BOV/cFHV7XVC/KetIHXOL0Plq2tWYmClnOP6w8A1Ctl.no6rOhK', 'admin', 'ADM-001');

-- Sample teacher (password: Teacher@1234)
INSERT INTO users (full_name, email, password, role, student_id) VALUES
('Mr. Samuel Kiprotich', 'samuel@greenfield.ac.ke',
 '$2b$12$lMQdHnjON3HeGM7NlyTQvOLUZEWMDUG4NUi7CfjOhnpZ6zttNZMr2', 'staff', 'STF-001');

-- Sample student (password: Student@1234)
INSERT INTO users (full_name, email, password, role, level, student_id) VALUES
('James Otieno', 'james@greenfield.ac.ke',
 '$2b$12$y9oPnNc/4XT4nRSjaQV7DOAXslbxoy/4mK97Mtr6ulTpBOQglJR6', 'student', 'university', 'STU-2024-001');

-- Departments
INSERT INTO departments (name, level, description, icon) VALUES
('Early Childhood Education', 'early_years', 'Nurturing young minds through play-based learning', '🌱'),
('Sciences', 'o_level', 'Physics, Chemistry, Biology & Mathematics', '🔬'),
('Humanities', 'o_level', 'History, Geography, Literature & Languages', '📚'),
('Sciences & Technology', 'a_level', 'Advanced sciences, computing & engineering pathway', '⚗️'),
('Arts & Business', 'a_level', 'Economics, Business Studies, Fine Art & Drama', '🎨'),
('Faculty of Science', 'university', 'BSc programmes in Computing, Engineering & Natural Sciences', '🏛️'),
('Faculty of Business', 'university', 'BBA, Accounting, Finance & Entrepreneurship', '📊'),
('Faculty of Education', 'university', 'Bachelor of Education degrees – Primary & Secondary', '🎓');

-- Courses
INSERT INTO courses (department_id, code, title, description, credits, level) VALUES
(1, 'EY-101', 'Creative Arts & Play', 'Fostering creativity through art, music and structured play', 0, 'early_years'),
(1, 'EY-102', 'Numeracy Foundations', 'Building early number sense and pattern recognition', 0, 'early_years'),
(2, 'OL-PHY', 'Physics', 'Mechanics, waves, electricity and modern physics at IGCSE level', 0, 'o_level'),
(2, 'OL-CHEM', 'Chemistry', 'Atomic structure, reactions, organic chemistry', 0, 'o_level'),
(2, 'OL-BIO', 'Biology', 'Cell biology, genetics, ecology and human physiology', 0, 'o_level'),
(3, 'OL-ENG', 'English Language & Literature', 'Comprehension, composition, and set texts', 0, 'o_level'),
(3, 'OL-HIST', 'History', 'African and world history to IGCSE standard', 0, 'o_level'),
(4, 'AL-PHY', 'Advanced Physics', 'A-Level mechanics, fields, thermodynamics and quantum physics', 0, 'a_level'),
(4, 'AL-CS', 'Computer Science', 'Algorithms, programming, data structures and networks', 0, 'a_level'),
(5, 'AL-ECON', 'Economics', 'Micro & macro economics, global markets', 0, 'a_level'),
(5, 'AL-BUS', 'Business Studies', 'Marketing, finance, HR and strategy', 0, 'a_level'),
(6, 'UNI-CS101', 'Introduction to Programming', 'Python fundamentals, algorithms, problem solving', 3, 'university'),
(6, 'UNI-CS201', 'Data Structures & Algorithms', 'Arrays, trees, graphs, sorting, complexity', 3, 'university'),
(7, 'UNI-BBA101', 'Principles of Management', 'Classical and contemporary management theory', 3, 'university'),
(8, 'UNI-EDU101', 'Foundations of Education', 'Philosophy, history and sociology of education', 3, 'university');

-- Sample enrollment (James Otieno taking two university courses)
INSERT INTO enrollments (student_id, course_id) VALUES
(3, 12),
(3, 13);

-- News
INSERT INTO news (title, excerpt, published_at) VALUES
('2025 Academic Calendar Released', 'The new academic calendar for 2025/2026 is now available. Key dates include term openings, exams and graduation.', '2025-01-15'),
('National Science Fair Champions', 'Our A-Level students swept the podium at the National Science Fair held in Nairobi this month.', '2025-03-10'),
('New University Faculty of Technology Opens', 'We are proud to announce the opening of our expanded Faculty of Science & Technology with state-of-the-art labs.', '2025-05-01');