# Debug Guide - Storage Unit Management System

This guide provides information about debugging, logging, and troubleshooting the Storage Unit Management System.

## 📁 Log Files Location

All log files are stored in the `storage/logs/` directory:

```
storage/
└── logs/
    ├── app.log          # General application logs
    ├── database.log     # Database connection and query logs
    ├── auth.log         # Authentication and user-related logs
    ├── app.log.1        # Rotated log files (when size limit reached)
    ├── app.log.2        # Older rotated files
    └── ...
```

### Log File Details

| Log File | Purpose | Max Size | Max Files | Contains |
|----------|---------|----------|-----------|----------|
| `app.log` | General application events | 10MB | 5 | Info, warnings, errors, debug messages |
| `database.log` | Database operations | 5MB | 3 | Connection attempts, query errors, PDO exceptions |
| `auth.log` | Authentication events | 5MB | 3 | Login attempts, user registration, security events |

## 🔍 Log Levels

The logging system supports the following levels (in order of severity):

1. **EMERGENCY** - System is unusable
2. **ALERT** - Action must be taken immediately
3. **CRITICAL** - Critical conditions
4. **ERROR** - Runtime errors that do not require immediate action
5. **WARNING** - Exceptional occurrences that are not errors
6. **NOTICE** - Normal but significant condition
7. **INFO** - Interesting events
8. **DEBUG** - Detailed information for debugging

## 📖 Reading Logs

### View Recent Logs
```bash
# View the latest application logs
tail -f storage/logs/app.log

# View database logs
tail -f storage/logs/database.log

# View authentication logs
tail -f storage/logs/auth.log
```

### Search for Specific Errors
```bash
# Search for errors in all log files
grep -r "ERROR" storage/logs/

# Search for database connection issues
grep -r "Database connection" storage/logs/

# Search for authentication failures
grep -r "Login failed" storage/logs/
```

### View Logs with Timestamps
```bash
# View logs with line numbers and timestamps
cat -n storage/logs/app.log | tail -50
```

## 🛠️ Common Debugging Scenarios

### 1. Database Connection Issues

**Symptoms:**
- "Database connection failed" error
- Login/signup not working
- Items not loading

**Debug Steps:**
1. Check database logs:
   ```bash
   tail -f storage/logs/database.log
   ```

2. Verify database configuration in `config/app/config.php`:
   ```php
   'database' => [
       'host' => 'localhost',
       'port' => 3306,
       'database' => 'storage_unit',
       'username' => 'root',
       'password' => '',
       'charset' => 'utf8mb4',
   ],
   ```

3. Test database connection:
   ```bash
   mysql -u root -e "USE storage_unit; SHOW TABLES;"
   ```

4. Check if database exists:
   ```bash
   mysql -u root -e "SHOW DATABASES LIKE 'storage_unit';"
   ```

### 2. Authentication Issues

**Symptoms:**
- Login form not submitting
- "Invalid email or password" errors
- Session not persisting

**Debug Steps:**
1. Check authentication logs:
   ```bash
   tail -f storage/logs/auth.log
   ```

2. Verify user exists in database:
   ```bash
   mysql -u root -e "USE storage_unit; SELECT * FROM users;"
   ```

3. Check session configuration in PHP:
   ```bash
   php -i | grep session
   ```

### 3. File Upload Issues

**Symptoms:**
- Images not uploading
- "Upload failed" errors
- Missing images in items

**Debug Steps:**
1. Check upload directory permissions:
   ```bash
   ls -la public/uploads/
   chmod 755 public/uploads/
   ```

2. Check PHP upload settings:
   ```bash
   php -i | grep upload
   ```

3. Check file size limits in `config/app/config.php`

## 🔧 Logging Configuration

### Customizing Log Settings

Edit `config/app/config.php` to modify logging behavior:

```php
'logging' => [
    'default' => [
        'type' => 'file',
        'path' => 'storage/logs/app.log',
        'max_file_size' => 10485760, // 10MB
        'max_files' => 5,
    ],
    'database' => [
        'type' => 'file',
        'path' => 'storage/logs/database.log',
        'max_file_size' => 5242880, // 5MB
        'max_files' => 3,
    ],
    'auth' => [
        'type' => 'file',
        'path' => 'storage/logs/auth.log',
        'max_file_size' => 5242880, // 5MB
        'max_files' => 3,
    ],
],
```

### Adding Custom Loggers

The logging system is extensible. You can add custom loggers:

```php
// In your code
$logger = LoggerFactory::getLogger('custom');
$logger->info('Custom message', ['data' => $someData]);
```

## 🚨 Error Monitoring

### Real-time Monitoring
```bash
# Monitor all logs simultaneously
tail -f storage/logs/*.log

# Monitor only errors
tail -f storage/logs/*.log | grep -i error

# Monitor with timestamps
tail -f storage/logs/app.log | while read line; do echo "$(date): $line"; done
```

### Log Rotation
Logs are automatically rotated when they reach the maximum file size. Old logs are compressed and kept for the specified number of files.

## 🔍 Debug Mode

### Enable Debug Mode
Set `APP_DEBUG` to `true` in `config/app/config.php`:

```php
'app' => [
    'debug' => true,
    // ...
],
```

### Debug Information
When debug mode is enabled:
- Detailed error messages are shown
- Database connection details are logged
- Stack traces are included in error logs

## 📊 Log Analysis

### Common Log Patterns

**Successful Login:**
```
[2024-01-15 10:30:15] INFO: Attempting database connection {"host":"localhost","database":"storage_unit","charset":"utf8mb4"}
[2024-01-15 10:30:15] INFO: Database connection established successfully
[2024-01-15 10:30:15] INFO: Login attempt - Email: user@example.com, Result: true
```

**Failed Login:**
```
[2024-01-15 10:30:15] ERROR: Database connection failed {"error":"Access denied for user 'root'@'localhost'","code":1045,"host":"localhost","database":"storage_unit"}
```

**File Upload Success:**
```
[2024-01-15 10:30:15] INFO: File uploaded successfully {"filename":"image.jpg","size":12345,"path":"public/uploads/image.jpg"}
```

## 🛡️ Security Considerations

- Log files may contain sensitive information
- Ensure proper file permissions on log directories
- Consider log encryption for production environments
- Regularly rotate and archive old logs

## 📞 Getting Help

If you encounter issues not covered in this guide:

1. Check the relevant log files
2. Enable debug mode
3. Check the application configuration
4. Verify database and server setup
5. Review the main README.md for setup instructions

## 🔄 Log Cleanup

### Manual Log Cleanup
```bash
# Clear all logs (use with caution)
rm storage/logs/*.log*

# Clear only old rotated logs
rm storage/logs/*.log.[0-9]*
```

### Automated Cleanup
The system automatically manages log rotation, but you can set up cron jobs for additional cleanup:

```bash
# Add to crontab to clean logs older than 30 days
0 2 * * * find /path/to/storage/logs -name "*.log.*" -mtime +30 -delete
```

---

**Note:** Always check log files when troubleshooting issues. They contain valuable information about what's happening in your application.
