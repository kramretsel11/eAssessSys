# Database Migration Instructions

## Step 1: Run the Complete Migration

Open your MySQL client (HeidiSQL, phpMyAdmin, or MySQL command line) and execute the complete migration script:

```sql
-- Navigate to your database folder and run:
source backend/database/complete_migration.sql;
```

Or if using HeidiSQL/phpMyAdmin, copy and paste the contents of `complete_migration.sql`

## Step 2: Verify Migration

After running the migration, verify it worked by running these queries:

```sql
-- Check if tables exist
SHOW TABLES LIKE '%audit_logs%';
SHOW TABLES LIKE '%tblusers%';

-- Check if foreign key was created properly
SHOW CREATE TABLE audit_logs;

-- Verify sample data
SELECT COUNT(*) as total_logs FROM audit_logs;
SELECT * FROM audit_logs LIMIT 5;
```

## Step 3: Test the Audit Log API

After the migration, test your audit log endpoints:

```bash
# Get audit logs
curl http://localhost/e_assessment/api/v1/misc/audit-logs

# Get audit log statistics  
curl http://localhost/e_assessment/api/v1/misc/audit-logs/statistics

# Export audit logs
curl http://localhost/e_assessment/api/v1/misc/audit-logs/export
```

## What the Migration Does:

1. **Creates/Updates tblusers table** with proper indexes
2. **Creates audit_logs table** with correct foreign key to tblusers.id
3. **Inserts sample audit log data** for demonstration
4. **Creates database views** for easier querying
5. **Creates stored procedures** for audit management
6. **Adds performance indexes** for better query performance

## Fixed Issues:

- ✅ Foreign key constraint now references `tblusers` instead of `users`
- ✅ Proper table structure with all required fields
- ✅ Sample data for testing
- ✅ Indexes for performance
- ✅ Views and procedures for maintenance

## Next Steps After Migration:

1. Update your AuditLogController.php to use correct table references (already done)
2. Test the frontend audit log component
3. Integrate audit logging into other controllers
4. Set up automated cleanup jobs for old audit logs