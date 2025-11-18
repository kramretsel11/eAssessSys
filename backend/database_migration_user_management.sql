-- Add municipality field to tblusers table for user management
-- Execute this SQL script to update your database

USE easessment_db;

-- Add municipality column to tblusers table
ALTER TABLE `tblusers` 
ADD COLUMN `municipality` VARCHAR(50) DEFAULT NULL AFTER `address`;

-- Update existing users with a default municipality (Baler)
UPDATE `tblusers` SET `municipality` = 'BALER' WHERE `municipality` IS NULL;

-- Create new user types for municipality users and evaluators
INSERT INTO `tblusertypes` (`id`, `description`, `modules`) VALUES 
(3, 'Municipality User', '301,302'),
(4, 'Evaluator', '401,402')
ON DUPLICATE KEY UPDATE 
`description` = VALUES(`description`),
`modules` = VALUES(`modules`);

-- Create some sample municipality users for testing
-- Password for all test users is 'password123' (hashed with SHA1)
INSERT INTO `tblusers` (`username`, `password`, `firstName`, `lastName`, `middleName`, `suffix`, `sex`, `email`, `contact`, `address`, `municipality`, `userType`, `status`, `isDeleted`) VALUES
('baler_user', 'e38ad214943daad1d64c102faec29de4afe9da3d', 'Juan', 'Dela Cruz', 'Santos', '', 'Male', 'juan.baler@aurora.gov.ph', '09123456789', '123 Main St, Baler', 'BALER', 3, 1, 0),
('casiguran_user', 'e38ad214943daad1d64c102faec29de4afe9da3d', 'Maria', 'Garcia', 'Lopez', '', 'Female', 'maria.casiguran@aurora.gov.ph', '09234567890', '456 Center St, Casiguran', 'CASIGURAN', 3, 1, 0),
('dipaculao_eval', 'e38ad214943daad1d64c102faec29de4afe9da3d', 'Pedro', 'Santos', 'Cruz', '', 'Male', 'pedro.dipaculao@aurora.gov.ph', '09345678901', '789 Eval Ave, Dipaculao', 'DIPACULAO', 4, 1, 0)
ON DUPLICATE KEY UPDATE 
`password` = VALUES(`password`),
`firstName` = VALUES(`firstName`),
`lastName` = VALUES(`lastName`);

-- Show current users
SELECT u.id, u.username, u.firstName, u.lastName, u.municipality, ut.description as role 
FROM tblusers u 
LEFT JOIN tblusertypes ut ON u.userType = ut.id 
WHERE u.isDeleted = 0
ORDER BY u.municipality, u.userType;