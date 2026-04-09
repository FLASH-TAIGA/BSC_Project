-- Flash Learning Database — Full Schema
-- Run this in phpMyAdmin (SQL tab) to reset and rebuild
-- ============================================================

CREATE DATABASE IF NOT EXISTS flashlearn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE flashlearn;

-- ============================================================
-- USERS
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('student','tutor') NOT NULL DEFAULT 'student',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TUTOR PROFILES
-- Extended info tutors fill in after signup
-- ============================================================
CREATE TABLE IF NOT EXISTS tutor_profiles (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT           NOT NULL UNIQUE,
    date_of_birth   DATE          DEFAULT NULL,
    qualification   ENUM('Certificate','Diploma','Bachelor','Master','PhD') DEFAULT 'Bachelor',
    subjects        VARCHAR(500)  DEFAULT '',       -- comma-separated
    bio             TEXT          DEFAULT NULL,
    hourly_rate     INT           NOT NULL DEFAULT 5000, -- XAF, auto-set by qualification
    photo_filename  VARCHAR(255)  DEFAULT NULL,
    is_approved     TINYINT(1)    DEFAULT 0,         -- admin must approve
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- SESSIONS
-- ============================================================
CREATE TABLE IF NOT EXISTS sessions (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT           NOT NULL,
    tutor_id        INT           NOT NULL,
    tutor_name      VARCHAR(100)  NOT NULL,
    subject         VARCHAR(100)  NOT NULL,
    session_date    DATE          NOT NULL,
    session_time    TIME          NOT NULL,
    mode            ENUM('online','onsite') NOT NULL DEFAULT 'online',
    online_platform ENUM('zoom','google_meet','whatsapp','telegram','other') DEFAULT NULL,
    platform_link   VARCHAR(500)  DEFAULT NULL,
    spot            VARCHAR(150)  DEFAULT NULL,
    amount_paid     INT           NOT NULL DEFAULT 5000,
    platform_fee    INT           NOT NULL DEFAULT 0,   -- 15% of amount_paid
    tutor_earnings  INT           NOT NULL DEFAULT 0,   -- 85% of amount_paid
    pay_method      ENUM('mtn','orange') DEFAULT 'mtn',
    status          ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id)   REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- MESSAGES (in-platform chat)
-- ============================================================
CREATE TABLE IF NOT EXISTS messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    sender_id   INT           NOT NULL,
    receiver_id INT           NOT NULL,
    message     TEXT          NOT NULL,
    is_read     TINYINT(1)    DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- MATERIALS
-- ============================================================
CREATE TABLE IF NOT EXISTS materials (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200)  NOT NULL,
    subject     VARCHAR(100)  NOT NULL,
    type        ENUM('Study Notes','Past Paper','Practice Exercises') DEFAULT 'Study Notes',
    filename    VARCHAR(255)  NOT NULL,
    uploaded_by VARCHAR(100)  NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- STUDENT PROFILES
-- ============================================================
CREATE TABLE IF NOT EXISTS student_profiles (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT           NOT NULL UNIQUE,
    date_of_birth   DATE          DEFAULT NULL,
    subjects        VARCHAR(500)  DEFAULT '',   -- comma-separated subjects with issues
    bio             TEXT          DEFAULT NULL,
    photo_filename  VARCHAR(255)  DEFAULT NULL,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- SAMPLE MATERIALS
-- ============================================================
INSERT IGNORE INTO materials (title, subject, type, filename, uploaded_by) VALUES
('Advanced Physics Vol 1',        'Physics',          'Study Notes', 'null.pdf', 'Prof. Richards'),
('Intro to Programming Exam Prep','Computer Science', 'Past Paper',  'PHP.pdf',  'Tutor Alex'),
('Microeconomics Summary',        'Economics',        'Study Notes', 'PHP.pdf',  'Dr. Sarah Smith');

-- ============================================================
-- SEED TUTORS
-- All passwords are: Tutor@1234
-- Hash generated with password_hash('Tutor@1234', PASSWORD_BCRYPT)
-- ============================================================
INSERT IGNORE INTO users (id, name, email, password, role) VALUES
(101, 'Dr. Amara Nkosi',      'amara.nkosi@flashlearn.edu',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(102, 'Prof. Jean-Paul Mbeki','jeanpaul.mbeki@flashlearn.edu',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(103, 'Dr. Fatima Ouedraogo', 'fatima.ouedraogo@flashlearn.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(104, 'Mr. Kwame Asante',     'kwame.asante@flashlearn.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(105, 'Dr. Chioma Eze',       'chioma.eze@flashlearn.edu',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(106, 'Prof. Samuel Tabi',    'samuel.tabi@flashlearn.edu',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(107, 'Ms. Aisha Diallo',     'aisha.diallo@flashlearn.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(108, 'Dr. Emmanuel Fon',     'emmanuel.fon@flashlearn.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(109, 'Prof. Grace Mensah',   'grace.mensah@flashlearn.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(110, 'Mr. Olivier Kamga',    'olivier.kamga@flashlearn.edu',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(111, 'Dr. Nadia Bello',      'nadia.bello@flashlearn.edu',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(112, 'Prof. Isaac Yeboah',   'isaac.yeboah@flashlearn.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(113, 'Ms. Celine Ngono',     'celine.ngono@flashlearn.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(114, 'Dr. Patrick Osei',     'patrick.osei@flashlearn.edu',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor'),
(115, 'Prof. Marie Tchamba',  'marie.tchamba@flashlearn.edu',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor');

INSERT IGNORE INTO tutor_profiles (user_id, date_of_birth, qualification, subjects, bio, hourly_rate, is_approved) VALUES
(101, '1982-03-15', 'PhD',      'Calculus, Linear Algebra, Differential Equations, Real Analysis, Numerical Methods, Statistics & Probability',
 'PhD in Applied Mathematics from University of Yaoundé I. 12 years teaching experience. Specialises in making abstract maths intuitive.', 10000, 1),

(102, '1975-07-22', 'PhD',      'Thermodynamics, Quantum Mechanics, Classical Mechanics, Electromagnetism, Optics, Nuclear Physics, Fluid Mechanics',
 'Professor of Physics with 18 years at the University of Buea. Published researcher in quantum optics. Patient and methodical teacher.', 10000, 1),

(103, '1988-11-05', 'Master',   'Organic Chemistry, Inorganic Chemistry, Physical Chemistry, Biochemistry, Analytical Chemistry, Chemical Engineering',
 'Master''s in Chemistry from University of Douala. Experienced lab instructor with a talent for simplifying complex reaction mechanisms.', 7000, 1),

(104, '1990-01-30', 'Bachelor', 'Introduction to Programming, Data Structures & Algorithms, Python, Java, C++, Web Development, Database Systems',
 'Software engineer and part-time tutor. Graduated from ENSP Yaoundé. Loves helping students write their first clean, working code.', 5000, 1),

(105, '1985-09-18', 'PhD',      'Molecular Biology, Cell Biology, Genetics, Microbiology, Immunology, Anatomy & Physiology, Ecology',
 'PhD in Biological Sciences. Former lecturer at University of Ngaoundéré. Passionate about life sciences and research methodology.', 10000, 1),

(106, '1978-04-12', 'PhD',      'Microeconomics, Macroeconomics, Econometrics, Development Economics, International Trade, Public Finance, Game Theory',
 'Professor of Economics with extensive research in African development economics. Makes economic theory relevant to real-world problems.', 10000, 1),

(107, '1993-06-25', 'Master',   'French Language, French Literature, Linguistics, Translation Studies, Academic Writing, Communication Skills',
 'Master''s in French Linguistics. Native speaker with 6 years tutoring experience. Helps students master grammar, expression and oral fluency.', 7000, 1),

(108, '1980-12-08', 'PhD',      'Civil Engineering, Structural Analysis, Fluid Mechanics, Soil Mechanics, Construction Management, Engineering Mathematics',
 'PhD in Civil Engineering from ENSP. Consulting engineer and educator. Bridges the gap between theory and real construction practice.', 10000, 1),

(109, '1987-02-14', 'Master',   'Financial Accounting, Management Accounting, Auditing, Taxation, Corporate Finance, Business Law, Entrepreneurship',
 'Certified accountant and Master''s holder in Finance. Helps students crack accounting exams and understand real business finance.', 7000, 1),

(110, '1991-08-03', 'Bachelor', 'History, Geography, Political Science, International Relations, Philosophy, Sociology, Anthropology',
 'Bachelor''s in Social Sciences. Passionate educator who connects historical events to modern-day realities in Africa and beyond.', 5000, 1),

(111, '1984-05-19', 'PhD',      'Electrical Engineering, Circuit Analysis, Digital Electronics, Signal Processing, Control Systems, Telecommunications, Embedded Systems',
 'PhD in Electrical Engineering. Research focus on renewable energy systems. Excellent at breaking down complex circuit problems step by step.', 10000, 1),

(112, '1979-10-27', 'PhD',      'Statistics, Probability Theory, Research Methods, Data Analysis, SPSS, R Programming, Machine Learning Fundamentals',
 'Professor of Statistics at a leading university. Helps students with dissertations, data analysis and understanding statistical inference.', 10000, 1),

(113, '1995-03-07', 'Bachelor', 'English Language, Literature in English, Creative Writing, Essay Writing, Public Speaking, IELTS & TOEFL Preparation',
 'English graduate and certified language instructor. Specialises in academic writing and helping francophone students excel in English.', 5000, 1),

(114, '1983-07-31', 'PhD',      'Law, Constitutional Law, Contract Law, Criminal Law, Human Rights Law, Commercial Law, Legal Research & Writing',
 'PhD in Law from University of Yaoundé II. Practising lawyer and academic. Helps law students master case analysis and legal argumentation.', 10000, 1),

(115, '1989-11-22', 'Master',   'Nursing, Pharmacology, Medical Terminology, Pathophysiology, Public Health, Epidemiology, Health Research Methods',
 'Master''s in Public Health. Registered nurse with 10 years clinical experience. Dedicated to helping health science students succeed.', 7000, 1);


-- ============================================================
-- MIGRATION: Add platform_fee and tutor_earnings to sessions
-- Run this if your sessions table was created before this update
-- ============================================================
ALTER TABLE sessions
    ADD COLUMN IF NOT EXISTS platform_fee   INT NOT NULL DEFAULT 0 AFTER amount_paid,
    ADD COLUMN IF NOT EXISTS tutor_earnings INT NOT NULL DEFAULT 0 AFTER platform_fee;

-- Backfill existing rows
UPDATE sessions SET
    platform_fee   = ROUND(amount_paid * 0.15),
    tutor_earnings = amount_paid - ROUND(amount_paid * 0.15)
WHERE platform_fee = 0 AND amount_paid > 0;
