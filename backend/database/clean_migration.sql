-- ========================================
-- ASCOT Real Estate Assessment System
-- Clean Database Migration Script
-- ========================================

USE `easessment_db`;

-- ========================================
-- 1. CREATE AUDIT LOGS TABLE
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
  KEY `idx_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Audit trail for all system activities';

-- ========================================
-- 2. ADD FOREIGN KEY CONSTRAINT (ONLY IF NOT EXISTS)
-- ========================================
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.table_constraints 
                  WHERE constraint_name = 'fk_audit_logs_user' 
                  AND table_schema = 'easessment_db' 
                  AND table_name = 'audit_logs');

SET @sql = IF(@fk_exists = 0, 
  'ALTER TABLE `audit_logs` ADD CONSTRAINT `fk_audit_logs_user` FOREIGN KEY (`user_id`) REFERENCES `tblusers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
  'SELECT "Foreign key already exists" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ========================================
-- 3. INSERT SAMPLE DATA (ONLY IF TABLE IS EMPTY)
-- ========================================
INSERT INTO `audit_logs` (`user_id`, `action`, `resource_type`, `resource_id`, `details`, `ip_address`, `user_agent`, `created_at`)
SELECT 1, 'System Migration', 'System', NULL, 'Database migrated and audit logging system initialized', '127.0.0.1', 'Database Migration Script', NOW()
WHERE NOT EXISTS (SELECT 1 FROM `audit_logs` WHERE action = 'System Migration');

INSERT INTO `audit_logs` (`user_id`, `action`, `resource_type`, `resource_id`, `details`, `ip_address`, `user_agent`, `created_at`)
SELECT * FROM (
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
WHERE NOT EXISTS (SELECT 1 FROM audit_logs WHERE action = 'Login' AND resource_type = 'Authentication');

-- ========================================
-- 4. CREATE VIEW (DROP IF EXISTS FIRST)
-- ========================================
DROP VIEW IF EXISTS `vw_audit_logs`;
CREATE VIEW `vw_audit_logs` AS
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
-- 5. VERIFICATION QUERIES
-- ========================================
SELECT 'Migration Status' as status,
       (SELECT COUNT(*) FROM information_schema.tables 
        WHERE table_schema = 'easessment_db' 
        AND table_name = 'audit_logs') as audit_table_exists,
       (SELECT COUNT(*) FROM information_schema.table_constraints 
        WHERE constraint_name = 'fk_audit_logs_user' 
        AND table_schema = 'easessment_db') as foreign_key_exists,
       (SELECT COUNT(*) FROM audit_logs) as sample_logs_count;

-- Show summary
SELECT 'Migration Complete' as message,
       COUNT(*) as total_audit_logs,
       MIN(created_at) as oldest_log,
       MAX(created_at) as newest_log
FROM audit_logs;