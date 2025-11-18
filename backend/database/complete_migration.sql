-- ========================================
-- ASCOT Real Estate Assessment System
-- Database Migration Script
-- ========================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `easessment_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */;
USE `easessment_db`;

-- ========================================
-- 1. USERS TABLE (tblusers)
-- ========================================
CREATE TABLE IF NOT EXISTS `tblusers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Username',
  `password` text NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `middleName` varchar(255) NOT NULL,
  `suffix` varchar(255) NOT NULL,
  `sex` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `userType` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `isDeleted` int NOT NULL DEFAULT '0',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_userType` (`userType`),
  KEY `idx_status` (`status`),
  KEY `idx_isDeleted` (`isDeleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insert default admin user if not exists
INSERT IGNORE INTO `tblusers` (`id`, `username`, `password`, `firstName`, `lastName`, `middleName`, `suffix`, `sex`, `email`, `contact`, `address`, `userType`, `status`, `isDeleted`, `createdAt`, `updatedAt`) VALUES
(1, 'admin', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Admin', 'User', 'System', '', 'Male', 'admin@ascot.gov.ph', '09876543212', 'Aurora Provincial Capitol', 1, 1, 0, NOW(), NOW());

-- ========================================
-- 2. USER TYPES TABLE (tblusertypes)
-- ========================================
CREATE TABLE IF NOT EXISTS `tblusertypes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `modules` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insert default user types if not exists
INSERT IGNORE INTO `tblusertypes` (`id`, `description`, `modules`) VALUES
(1, 'Administrator', '["dashboard","users","assessment_requests","certificates","reports","audit_logs","configuration"]'),
(2, 'Property Owner', '["dashboard","assessment_requests","track_requests"]');

-- ========================================
-- 3. AUDIT LOGS TABLE
-- ========================================
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT 'References tblusers.id',
  `action` varchar(100) NOT NULL COMMENT 'Action performed (Login, Create Request, etc.)',
  `resource_type` varchar(50) NOT NULL COMMENT 'Type of resource (Authentication, Assessment Request, etc.)',
  `resource_id` int DEFAULT NULL COMMENT 'ID of the affected resource',
  `details` text DEFAULT NULL COMMENT 'Additional details about the action',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP address of the user',
  `user_agent` varchar(500) DEFAULT NULL COMMENT 'Browser/client information',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the action occurred',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_resource` (`resource_type`, `resource_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_date` (`created_at`),
  CONSTRAINT `fk_audit_logs_user` FOREIGN KEY (`user_id`) REFERENCES `tblusers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Audit trail for all system activities';

-- ========================================
-- 4. INSERT SAMPLE AUDIT LOG DATA
-- ========================================
-- Note: Only insert if audit_logs table is empty to avoid duplicates
INSERT INTO `audit_logs` (`user_id`, `action`, `resource_type`, `resource_id`, `details`, `ip_address`, `user_agent`, `created_at`)
SELECT * FROM (
  SELECT 
    1 as user_id,
    'System Migration' as action,
    'System' as resource_type,
    NULL as resource_id,
    'Database migrated and audit logging system initialized' as details,
    '127.0.0.1' as ip_address,
    'Database Migration Script' as user_agent,
    NOW() as created_at
  UNION ALL
  SELECT 1, 'Login', 'Authentication', NULL, 'Admin user logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', NOW() - INTERVAL 1 HOUR
  UNION ALL
  SELECT 1, 'Create Request', 'Assessment Request', 1, 'New assessment request created for ARP-000001', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', NOW() - INTERVAL 2 HOUR
  UNION ALL
  SELECT 1, 'Approve Request', 'Assessment Request', 1, 'Assessment request approved with assessed value of ₱150,000', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', NOW() - INTERVAL 3 HOUR
  UNION ALL
  SELECT 1, 'Generate Certificate', 'Certificate', 1, 'Property Holding Certificate generated for approved request', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', NOW() - INTERVAL 4 HOUR
  UNION ALL
  SELECT 1, 'Update Market Value', 'Market Value', 1, 'Market value updated from ₱5,000 to ₱5,500 per sq.m for Residential properties in Aurora', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', NOW() - INTERVAL 5 HOUR
  UNION ALL
  SELECT 1, 'Export Report', 'Report', NULL, 'Assessment Summary Report exported with 156 records', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', NOW() - INTERVAL 6 HOUR
  UNION ALL
  SELECT 1, 'Delete Category', 'Category', 3, 'Property category "Vacant Lot" deleted from system', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', NOW() - INTERVAL 1 DAY
  UNION ALL
  SELECT 1, 'Update Assessment Level', 'Assessment Level', 2, 'Assessment level for Commercial properties updated to 30%', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', NOW() - INTERVAL 1 DAY
  UNION ALL
  SELECT 1, 'Reject Request', 'Assessment Request', 5, 'Assessment request rejected due to incomplete documentation', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', NOW() - INTERVAL 2 DAY
  UNION ALL
  SELECT 1, 'Backup Database', 'System', NULL, 'Database backup created successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', NOW() - INTERVAL 3 DAY
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM audit_logs LIMIT 1);

-- ========================================
-- 5. CREATE VIEWS FOR EASIER ACCESS
-- ========================================

-- View to join audit logs with user information
CREATE OR REPLACE VIEW `vw_audit_logs` AS
SELECT 
  al.id,
  al.user_id,
  CONCAT(tu.firstName, ' ', tu.lastName) as user_name,
  tu.username,
  al.action,
  al.resource_type,
  al.resource_id,
  al.details,
  al.ip_address,
  al.user_agent,
  al.created_at,
  DATE(al.created_at) as log_date,
  TIME(al.created_at) as log_time
FROM audit_logs al
LEFT JOIN tblusers tu ON al.user_id = tu.id
WHERE tu.isDeleted = 0
ORDER BY al.created_at DESC;

-- ========================================
-- 6. STORED PROCEDURES FOR AUDIT LOGGING
-- ========================================

DELIMITER //

-- Procedure to log activities easily
CREATE PROCEDURE `sp_log_activity`(
  IN p_user_id INT,
  IN p_action VARCHAR(100),
  IN p_resource_type VARCHAR(50),
  IN p_resource_id INT,
  IN p_details TEXT,
  IN p_ip_address VARCHAR(45),
  IN p_user_agent VARCHAR(500)
)
BEGIN
  INSERT INTO audit_logs (
    user_id, action, resource_type, resource_id, 
    details, ip_address, user_agent, created_at
  ) VALUES (
    p_user_id, p_action, p_resource_type, p_resource_id, 
    p_details, p_ip_address, p_user_agent, NOW()
  );
END //

-- Procedure to clean up old audit logs
CREATE PROCEDURE `sp_cleanup_audit_logs`(IN p_days INT)
BEGIN
  DECLARE affected_rows INT DEFAULT 0;
  
  DELETE FROM audit_logs 
  WHERE created_at < DATE_SUB(NOW(), INTERVAL p_days DAY);
  
  SET affected_rows = ROW_COUNT();
  
  -- Log the cleanup activity
  INSERT INTO audit_logs (
    user_id, action, resource_type, details, created_at
  ) VALUES (
    1, 'Cleanup Audit Logs', 'System', 
    CONCAT('Deleted ', affected_rows, ' audit log records older than ', p_days, ' days'),
    NOW()
  );
  
  SELECT affected_rows as deleted_records;
END //

-- Procedure to get audit statistics
CREATE PROCEDURE `sp_get_audit_stats`(IN p_days INT)
BEGIN
  DECLARE start_date DATE DEFAULT DATE_SUB(CURDATE(), INTERVAL p_days DAY);
  
  SELECT 
    -- Total logs in period
    (SELECT COUNT(*) FROM audit_logs WHERE DATE(created_at) >= start_date) as total_logs,
    
    -- Logs today
    (SELECT COUNT(*) FROM audit_logs WHERE DATE(created_at) = CURDATE()) as logs_today,
    
    -- Logs this week
    (SELECT COUNT(*) FROM audit_logs WHERE YEARWEEK(created_at) = YEARWEEK(NOW())) as logs_this_week,
    
    -- Most active user
    (SELECT CONCAT(tu.firstName, ' ', tu.lastName) 
     FROM audit_logs al 
     JOIN tblusers tu ON al.user_id = tu.id 
     WHERE DATE(al.created_at) >= start_date 
     GROUP BY al.user_id 
     ORDER BY COUNT(*) DESC 
     LIMIT 1) as most_active_user,
    
    -- Most common action
    (SELECT action 
     FROM audit_logs 
     WHERE DATE(created_at) >= start_date 
     GROUP BY action 
     ORDER BY COUNT(*) DESC 
     LIMIT 1) as most_common_action;
END //

DELIMITER ;

-- ========================================
-- 7. INDEXES FOR PERFORMANCE
-- ========================================

-- Additional indexes for better query performance
CREATE INDEX `idx_audit_user_date` ON `audit_logs` (`user_id`, `created_at`);
CREATE INDEX `idx_audit_action_date` ON `audit_logs` (`action`, `created_at`);
CREATE INDEX `idx_audit_resource_date` ON `audit_logs` (`resource_type`, `created_at`);

-- ========================================
-- 8. VERIFICATION QUERIES
-- ========================================

-- Check if migration was successful
SELECT 
  'Tables Created' as status,
  (SELECT COUNT(*) FROM information_schema.tables 
   WHERE table_schema = 'easessment_db' 
   AND table_name IN ('tblusers', 'tblusertypes', 'audit_logs')) as table_count,
  (SELECT COUNT(*) FROM tblusers WHERE id = 1) as admin_user_exists,
  (SELECT COUNT(*) FROM audit_logs) as sample_audit_logs;

-- Show audit log summary
SELECT 
  'Migration Complete' as message,
  COUNT(*) as total_audit_logs,
  MIN(created_at) as oldest_log,
  MAX(created_at) as newest_log
FROM audit_logs;

-- ========================================
-- MIGRATION COMPLETE
-- ========================================
-- 
-- This migration script creates:
-- 1. tblusers table with proper indexes
-- 2. tblusertypes table 
-- 3. audit_logs table with foreign key to tblusers
-- 4. Sample data for demonstration
-- 5. Views for easier access
-- 6. Stored procedures for audit management
-- 7. Performance indexes
-- 
-- Next steps:
-- 1. Update your AuditLogModel to use 'tblusers' instead of 'users'
-- 2. Test the audit logging endpoints
-- 3. Integrate with your frontend components
-- ========================================