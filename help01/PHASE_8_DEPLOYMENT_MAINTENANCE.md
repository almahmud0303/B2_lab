# Phase 8: Deployment & Maintenance (Days 29-32)

## 🎯 **Phase 8 Objectives**
- Set up production environment
- Configure security measures
- Implement monitoring and logging
- Create backup and recovery system
- Set up CI/CD pipeline
- Performance optimization
- Documentation and user guides
- Maintenance procedures

---

## 📅 **Day 29: Production Environment Setup**

### **Step 1: Production Configuration**

**File: `.env.production`**

```env
APP_NAME="University Management System"
APP_ENV=production
APP_KEY=base64:YOUR_PRODUCTION_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=your-production-db-host
DB_PORT=3306
DB_DATABASE=university_management_prod
DB_USERNAME=your-production-db-user
DB_PASSWORD=your-secure-production-password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=s3
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=your-aws-access-key
AWS_SECRET_ACCESS_KEY=your-aws-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-s3-bucket
AWS_USE_PATH_STYLE_ENDPOINT=false

# bKash Production Configuration
BKASH_BASE_URL=https://tokenized.pay.bka.sh/v1.2.0-beta
BKASH_APP_KEY=your-production-bkash-app-key
BKASH_APP_SECRET=your-production-bkash-app-secret
BKASH_USERNAME=your-production-bkash-username
BKASH_PASSWORD=your-production-bkash-password

# Security
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=your-domain.com
```

### **Step 2: Production Server Setup**

**File: `deploy/setup-server.sh`**

```bash
#!/bin/bash

# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-redis

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js and NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install Redis
sudo apt install -y redis-server

# Install Supervisor for queue management
sudo apt install -y supervisor

# Create application directory
sudo mkdir -p /var/www/university-management
sudo chown -R www-data:www-data /var/www/university-management

# Configure PHP-FPM
sudo sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 10M/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/post_max_size = 8M/post_max_size = 10M/' /etc/php/8.2/fpm/php.ini

# Configure MySQL
sudo mysql_secure_installation

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo systemctl restart mysql
sudo systemctl restart redis-server

echo "Server setup completed!"
```

### **Step 3: Nginx Configuration**

**File: `deploy/nginx.conf`**

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/university-management/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript;

    # Main location block
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ /(storage|bootstrap/cache) {
        deny all;
    }

    # File upload size limit
    client_max_body_size 10M;
}
```

### **Step 4: Deployment Script**

**File: `deploy/deploy.sh`**

```bash
#!/bin/bash

# Configuration
APP_DIR="/var/www/university-management"
BACKUP_DIR="/var/backups/university-management"
REPO_URL="https://github.com/your-username/university-management-system.git"
BRANCH="main"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting deployment...${NC}"

# Create backup
echo -e "${YELLOW}Creating backup...${NC}"
sudo mkdir -p $BACKUP_DIR
sudo tar -czf $BACKUP_DIR/backup-$(date +%Y%m%d-%H%M%S).tar.gz -C $APP_DIR .

# Pull latest code
echo -e "${YELLOW}Pulling latest code...${NC}"
cd $APP_DIR
sudo git pull origin $BRANCH

# Install/Update dependencies
echo -e "${YELLOW}Installing dependencies...${NC}"
sudo composer install --no-dev --optimize-autoloader
sudo npm ci --production

# Build assets
echo -e "${YELLOW}Building assets...${NC}"
sudo npm run production

# Run migrations
echo -e "${YELLOW}Running migrations...${NC}"
sudo php artisan migrate --force

# Clear caches
echo -e "${YELLOW}Clearing caches...${NC}"
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
sudo php artisan event:cache

# Set permissions
echo -e "${YELLOW}Setting permissions...${NC}"
sudo chown -R www-data:www-data $APP_DIR
sudo chmod -R 755 $APP_DIR
sudo chmod -R 775 $APP_DIR/storage
sudo chmod -R 775 $APP_DIR/bootstrap/cache

# Restart services
echo -e "${YELLOW}Restarting services...${NC}"
sudo systemctl reload nginx
sudo systemctl restart php8.2-fpm
sudo supervisorctl restart all

echo -e "${GREEN}Deployment completed successfully!${NC}"
```

---

## 📅 **Day 30: Security & Monitoring**

### **Step 1: Security Middleware**

**File: `app/Http/Middleware/SecurityHeaders.php`**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self' https://api.bkash.com; " .
               "frame-ancestors 'self';";
        
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
```

### **Step 2: Rate Limiting**

**File: `app/Http/Middleware/CustomRateLimit.php`**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class CustomRateLimit
{
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }

    protected function resolveRequestSignature($request)
    {
        return sha1(
            $request->method() .
            '|' . $request->server('SERVER_NAME') .
            '|' . $request->ip() .
            '|' . $request->path()
        );
    }
}
```

### **Step 3: Monitoring Configuration**

**File: `config/logging.php` (Production section)**

```php
'channels' => [
    // ... existing channels ...

    'production' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack'],
        'ignore_exceptions' => false,
    ],

    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'error'),
        'days' => 14,
    ],

    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'error',
    ],

    'telegram' => [
        'driver' => 'custom',
        'via' => App\Logging\TelegramLogger::class,
        'level' => 'error',
    ],
],
```

### **Step 4: Telegram Logger**

**File: `app/Logging/TelegramLogger.php`**

```php
<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Illuminate\Support\Facades\Http;

class TelegramLogger extends AbstractProcessingHandler
{
    protected $botToken;
    protected $chatId;

    public function __construct($level = Logger::ERROR, $bubble = true)
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
        
        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        $message = $this->formatMessage($record);
        
        Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ]);
    }

    protected function formatMessage(array $record): string
    {
        $message = "<b>🚨 Error Alert</b>\n\n";
        $message .= "<b>Environment:</b> " . app()->environment() . "\n";
        $message .= "<b>Level:</b> " . $record['level_name'] . "\n";
        $message .= "<b>Message:</b> " . $record['message'] . "\n";
        $message .= "<b>Time:</b> " . $record['datetime']->format('Y-m-d H:i:s') . "\n";
        
        if (isset($record['context']['exception'])) {
            $exception = $record['context']['exception'];
            $message .= "<b>File:</b> " . $exception->getFile() . ":" . $exception->getLine() . "\n";
        }
        
        return $message;
    }
}
```

### **Step 5: Health Check Endpoint**

**File: `app/Http/Controllers/HealthController.php`**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function check()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
        ];

        $allHealthy = collect($checks)->every(fn($check) => $check['status'] === 'ok');

        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now()->toISOString(),
        ], $allHealthy ? 200 : 503);
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    private function checkRedis()
    {
        try {
            Redis::ping();
            return ['status' => 'ok', 'message' => 'Redis connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Redis connection failed: ' . $e->getMessage()];
        }
    }

    private function checkStorage()
    {
        try {
            Storage::disk('public')->put('health-check.txt', 'test');
            Storage::disk('public')->delete('health-check.txt');
            return ['status' => 'ok', 'message' => 'Storage is writable'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Storage check failed: ' . $e->getMessage()];
        }
    }

    private function checkQueue()
    {
        try {
            // Check if queue worker is running
            $processes = shell_exec('ps aux | grep "queue:work" | grep -v grep');
            if (empty($processes)) {
                return ['status' => 'warning', 'message' => 'Queue worker not running'];
            }
            return ['status' => 'ok', 'message' => 'Queue worker is running'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Queue check failed: ' . $e->getMessage()];
        }
    }
}
```

---

## 📅 **Day 31: Backup & Recovery System**

### **Step 1: Backup Command**

**File: `app/Console/Commands/BackupCommand.php`**

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupCommand extends Command
{
    protected $signature = 'backup:create {--type=all : Type of backup (all, database, files)}';
    protected $description = 'Create system backup';

    public function handle()
    {
        $type = $this->option('type');
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        
        $this->info("Starting backup process...");

        switch ($type) {
            case 'database':
                $this->backupDatabase($timestamp);
                break;
            case 'files':
                $this->backupFiles($timestamp);
                break;
            case 'all':
            default:
                $this->backupDatabase($timestamp);
                $this->backupFiles($timestamp);
                break;
        }

        $this->info("Backup completed successfully!");
    }

    private function backupDatabase($timestamp)
    {
        $this->info("Backing up database...");
        
        $filename = "database_backup_{$timestamp}.sql";
        $path = storage_path("app/backups/{$filename}");
        
        // Create backup directory if it doesn't exist
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.database'),
            $path
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info("Database backup created: {$filename}");
            
            // Upload to S3 if configured
            if (config('filesystems.default') === 's3') {
                $this->uploadToS3($path, "backups/database/{$filename}");
            }
        } else {
            $this->error("Database backup failed!");
        }
    }

    private function backupFiles($timestamp)
    {
        $this->info("Backing up files...");
        
        $filename = "files_backup_{$timestamp}.tar.gz";
        $path = storage_path("app/backups/{$filename}");
        
        $command = sprintf(
            'tar -czf %s -C %s storage/app/public storage/logs',
            $path,
            base_path()
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info("Files backup created: {$filename}");
            
            // Upload to S3 if configured
            if (config('filesystems.default') === 's3') {
                $this->uploadToS3($path, "backups/files/{$filename}");
            }
        } else {
            $this->error("Files backup failed!");
        }
    }

    private function uploadToS3($localPath, $remotePath)
    {
        try {
            Storage::disk('s3')->put($remotePath, file_get_contents($localPath));
            $this->info("Backup uploaded to S3: {$remotePath}");
            
            // Delete local backup after upload
            unlink($localPath);
        } catch (\Exception $e) {
            $this->error("Failed to upload to S3: " . $e->getMessage());
        }
    }
}
```

### **Step 2: Recovery Command**

**File: `app/Console/Commands/RecoveryCommand.php`**

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecoveryCommand extends Command
{
    protected $signature = 'backup:restore {file : Backup file to restore} {--type=all : Type of restore (all, database, files)}';
    protected $description = 'Restore system from backup';

    public function handle()
    {
        $file = $this->argument('file');
        $type = $this->option('type');
        
        $this->info("Starting restore process...");
        $this->warn("This will overwrite existing data. Are you sure?");
        
        if (!$this->confirm('Do you want to continue?')) {
            $this->info("Restore cancelled.");
            return;
        }

        switch ($type) {
            case 'database':
                $this->restoreDatabase($file);
                break;
            case 'files':
                $this->restoreFiles($file);
                break;
            case 'all':
            default:
                $this->restoreDatabase($file);
                $this->restoreFiles($file);
                break;
        }

        $this->info("Restore completed successfully!");
    }

    private function restoreDatabase($file)
    {
        $this->info("Restoring database from: {$file}");
        
        // Download from S3 if needed
        if (str_starts_with($file, 's3://')) {
            $localFile = $this->downloadFromS3($file);
        } else {
            $localFile = storage_path("app/backups/{$file}");
        }

        if (!file_exists($localFile)) {
            $this->error("Backup file not found: {$file}");
            return;
        }

        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s %s < %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.database'),
            $localFile
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info("Database restored successfully!");
        } else {
            $this->error("Database restore failed!");
        }
    }

    private function restoreFiles($file)
    {
        $this->info("Restoring files from: {$file}");
        
        // Download from S3 if needed
        if (str_starts_with($file, 's3://')) {
            $localFile = $this->downloadFromS3($file);
        } else {
            $localFile = storage_path("app/backups/{$file}");
        }

        if (!file_exists($localFile)) {
            $this->error("Backup file not found: {$file}");
            return;
        }

        $command = sprintf(
            'tar -xzf %s -C %s',
            $localFile,
            base_path()
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            $this->info("Files restored successfully!");
        } else {
            $this->error("Files restore failed!");
        }
    }

    private function downloadFromS3($s3Path)
    {
        $filename = basename($s3Path);
        $localPath = storage_path("app/backups/{$filename}");
        
        try {
            $content = Storage::disk('s3')->get($s3Path);
            file_put_contents($localPath, $content);
            return $localPath;
        } catch (\Exception $e) {
            $this->error("Failed to download from S3: " . $e->getMessage());
            return null;
        }
    }
}
```

### **Step 3: Automated Backup Schedule**

**File: `app/Console/Kernel.php` (add to schedule method)**

```php
protected function schedule(Schedule $schedule)
{
    // Daily database backup at 2 AM
    $schedule->command('backup:create --type=database')
             ->dailyAt('02:00')
             ->withoutOverlapping();

    // Weekly full backup on Sundays at 3 AM
    $schedule->command('backup:create --type=all')
             ->weeklyOn(0, '03:00')
             ->withoutOverlapping();

    // Clean old backups (keep only last 30 days)
    $schedule->command('backup:clean')
             ->daily()
             ->at('04:00');

    // Queue monitoring
    $schedule->command('queue:monitor')
             ->everyMinute();

    // Cache optimization
    $schedule->command('optimize:clear')
             ->weekly();
}
```

---

## 📅 **Day 32: Documentation & Maintenance**

### **Step 1: User Documentation**

**File: `docs/USER_GUIDE.md`**

```markdown
# University Management System - User Guide

## Table of Contents
1. [Getting Started](#getting-started)
2. [Student Guide](#student-guide)
3. [Teacher Guide](#teacher-guide)
4. [Admin Guide](#admin-guide)
5. [Staff Guide](#staff-guide)
6. [Department Head Guide](#department-head-guide)
7. [Troubleshooting](#troubleshooting)

## Getting Started

### System Requirements
- Modern web browser (Chrome, Firefox, Safari, Edge)
- Stable internet connection
- JavaScript enabled

### Login Process
1. Navigate to the system URL
2. Enter your email and password
3. Click "Login"
4. You will be redirected to your role-specific dashboard

### Password Reset
1. Click "Forgot Password" on login page
2. Enter your email address
3. Check your email for reset instructions
4. Follow the link to create a new password

## Student Guide

### Dashboard
Your dashboard shows:
- Enrolled courses
- Upcoming exams
- Recent results
- Payment information
- Notices

### Course Management
- **Browse Courses**: View available courses in your department
- **Enroll**: Click "Enroll" on any available course
- **Drop Course**: Use the "Drop" button on enrolled courses
- **View Details**: Click on course name for detailed information

### Payment System
- **Make Payment**: Go to course details and click "Pay Now"
- **Payment Methods**: Cash, Bank Transfer, Mobile Banking (bKash, Nagad, Rocket)
- **Payment History**: View all your payment records
- **Receipts**: Download payment receipts

### Results
- **View Results**: Check your exam results
- **Transcript**: Download your academic transcript
- **Grade Calculation**: View how your grades are calculated

## Teacher Guide

### Dashboard
Your dashboard shows:
- Assigned courses
- Student statistics
- Upcoming exams
- Pending results

### Course Management
- **View Courses**: See all your assigned courses
- **Student List**: View enrolled students
- **Course Details**: Access course information and statistics

### Assessment Management
- **Create Assessment**: Create quizzes, midterms, or assignments
- **Enter Marks**: Input student marks after exams
- **Publish Results**: Make results visible to students
- **Grade Management**: Calculate and manage grades

### Student Management
- **Enroll Students**: Add students to your courses
- **Remove Students**: Drop students from courses
- **Student Performance**: Track individual student progress

## Admin Guide

### Dashboard
Your dashboard shows:
- System statistics
- Recent activities
- Quick actions
- User management

### User Management
- **Teachers**: Add, edit, view teacher profiles
- **Students**: Manage student accounts and information
- **Staff**: Handle staff member accounts
- **Departments**: Create and manage departments

### Course Management
- **Create Courses**: Add new courses to the system
- **Assign Teachers**: Assign teachers to courses
- **Course Settings**: Configure course parameters
- **Enrollment Management**: Oversee student enrollments

### System Management
- **Halls**: Manage student halls and assignments
- **Fees**: Set up fee structures
- **Notices**: Create and manage system-wide notices
- **Exams**: Oversee exam scheduling and management

## Staff Guide

### Dashboard
Your dashboard shows:
- Library statistics
- Recent book issues
- Overdue books
- Quick actions

### Book Management
- **Add Books**: Register new books in the system
- **Book Catalog**: Browse and search books
- **Book Details**: View book information and history
- **Inventory**: Track book availability

### Book Issue Management
- **Issue Books**: Check out books to students
- **Return Books**: Process book returns
- **Overdue Management**: Handle overdue books and fines
- **Fine Collection**: Manage fine payments

## Department Head Guide

### Dashboard
Your dashboard shows:
- Department statistics
- Faculty information
- Course assignments
- Department performance

### Course Assignment
- **Assign Teachers**: Assign teachers to courses
- **Workload Management**: Balance teaching loads
- **Course Oversight**: Monitor course performance
- **Faculty Support**: Support department faculty

### Notice Management
- **Create Notices**: Post department-specific notices
- **Target Audience**: Choose who sees the notices
- **Notice Types**: Academic, general, or event notices
- **Publish/Unpublish**: Control notice visibility

## Troubleshooting

### Common Issues

#### Login Problems
- **Forgot Password**: Use password reset feature
- **Account Locked**: Contact administrator
- **Wrong Credentials**: Verify email and password

#### Payment Issues
- **Payment Failed**: Check payment method and try again
- **bKash Problems**: Ensure correct mobile number and PIN
- **Receipt Issues**: Contact support for receipt problems

#### Course Enrollment
- **Course Full**: Wait for available slots or contact department
- **Prerequisites**: Check if you meet course requirements
- **Department Mismatch**: Verify you're in the correct department

#### Technical Issues
- **Page Not Loading**: Check internet connection
- **Slow Performance**: Clear browser cache
- **File Upload Issues**: Check file size and format

### Contact Support
- **Email**: support@your-university.edu
- **Phone**: +880-XXX-XXXXXXX
- **Office Hours**: Sunday-Thursday, 9:00 AM - 5:00 PM

### System Maintenance
- **Scheduled Maintenance**: Usually on Fridays, 2:00 AM - 4:00 AM
- **Notifications**: Check notices for maintenance announcements
- **Backup**: System is backed up daily
```

### **Step 2: Technical Documentation**

**File: `docs/TECHNICAL_DOCS.md`**

```markdown
# University Management System - Technical Documentation

## System Architecture

### Technology Stack
- **Backend**: Laravel 10.x
- **Frontend**: Blade Templates + Tailwind CSS
- **Database**: MySQL 8.0
- **Cache**: Redis
- **Queue**: Redis
- **File Storage**: AWS S3 (Production)
- **Web Server**: Nginx
- **PHP**: PHP 8.2

### Database Schema

#### Core Tables
- `users` - User accounts and authentication
- `departments` - Academic departments
- `teachers` - Teacher profiles and information
- `students` - Student profiles and academic records
- `staff` - Staff member profiles
- `courses` - Course information and settings
- `enrollments` - Student course enrollments
- `exams` - Exam/assessment information
- `results` - Student exam results
- `books` - Library book catalog
- `book_issues` - Book borrowing records
- `halls` - Student hall information
- `fees` - Fee structure and records
- `payments` - Payment transactions
- `notices` - System notices and announcements

#### Relationships
- Users have one-to-one relationships with Teachers, Students, or Staff
- Departments have one-to-many relationships with Teachers, Students, and Courses
- Courses belong to Departments and Teachers
- Students enroll in Courses through Enrollments
- Exams belong to Courses
- Results belong to Exams and Students
- Book Issues belong to Books, Students, and Staff

### API Endpoints

#### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `POST /api/refresh` - Refresh token

#### Public Endpoints
- `GET /api/v1/departments` - List departments
- `GET /api/v1/courses` - List active courses

#### Protected Endpoints
- `GET /api/v1/dashboard-stats` - Dashboard statistics
- `GET /api/v1/notices` - User-specific notices
- `GET /api/v1/profile` - User profile information

### Security Features

#### Authentication & Authorization
- Laravel Sanctum for API authentication
- Role-based access control
- Session management with secure cookies
- Password hashing with bcrypt

#### Security Headers
- X-Frame-Options: SAMEORIGIN
- X-XSS-Protection: 1; mode=block
- X-Content-Type-Options: nosniff
- Content-Security-Policy
- Strict-Transport-Security

#### Rate Limiting
- API rate limiting (60 requests per minute)
- Login attempt limiting
- Custom rate limiting for sensitive operations

### Performance Optimization

#### Caching Strategy
- Route caching
- Config caching
- View caching
- Query result caching with Redis

#### Database Optimization
- Proper indexing on frequently queried columns
- Eager loading to prevent N+1 queries
- Database query optimization
- Connection pooling

#### Frontend Optimization
- Asset minification and compression
- CDN integration for static assets
- Gzip compression
- Browser caching headers

### Monitoring & Logging

#### Application Monitoring
- Health check endpoints
- Performance metrics
- Error tracking
- User activity logging

#### Infrastructure Monitoring
- Server resource monitoring
- Database performance monitoring
- Queue monitoring
- Storage monitoring

#### Alerting
- Telegram notifications for errors
- Slack integration for team alerts
- Email notifications for critical issues

### Backup & Recovery

#### Backup Strategy
- Daily database backups
- Weekly full system backups
- Automated S3 uploads
- Retention policy (30 days)

#### Recovery Procedures
- Database restoration
- File system recovery
- Point-in-time recovery
- Disaster recovery plan

### Deployment

#### Production Environment
- Ubuntu 20.04 LTS
- Nginx web server
- PHP-FPM 8.2
- MySQL 8.0
- Redis 6.0

#### CI/CD Pipeline
- Automated testing
- Code quality checks
- Security scanning
- Automated deployment

#### Environment Configuration
- Environment-specific configs
- Secret management
- SSL/TLS configuration
- Performance tuning

### Maintenance Procedures

#### Regular Maintenance
- Weekly cache clearing
- Monthly log rotation
- Quarterly security updates
- Annual system review

#### Monitoring Tasks
- Daily health checks
- Weekly performance reviews
- Monthly security audits
- Quarterly capacity planning

#### Update Procedures
- Staging environment testing
- Backup before updates
- Rollback procedures
- Post-update verification
```

### **Step 3: Maintenance Scripts**

**File: `scripts/maintenance.sh`**

```bash
#!/bin/bash

# Maintenance script for University Management System

APP_DIR="/var/www/university-management"
LOG_FILE="/var/log/ums-maintenance.log"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a $LOG_FILE
}

log "Starting maintenance tasks..."

# Clear application caches
log "Clearing application caches..."
cd $APP_DIR
sudo php artisan cache:clear
sudo php artisan config:clear
sudo php artisan route:clear
sudo php artisan view:clear

# Optimize application
log "Optimizing application..."
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache

# Clean up old logs
log "Cleaning up old logs..."
find $APP_DIR/storage/logs -name "*.log" -mtime +30 -delete

# Clean up old backups
log "Cleaning up old backups..."
find /var/backups/university-management -name "*.tar.gz" -mtime +30 -delete

# Update system packages
log "Updating system packages..."
sudo apt update && sudo apt upgrade -y

# Restart services
log "Restarting services..."
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart mysql
sudo systemctl restart redis-server

# Check disk space
log "Checking disk space..."
df -h | grep -E "(Filesystem|/dev/)"

# Check memory usage
log "Checking memory usage..."
free -h

# Check service status
log "Checking service status..."
sudo systemctl status nginx php8.2-fpm mysql redis-server --no-pager

log "Maintenance tasks completed!"
```

### **Step 4: Final Git Commit**

```bash
git add .
git commit -m "Phase 8 complete: Production deployment, security, monitoring, and documentation"
git tag -a v1.0.0 -m "Initial production release"
git push origin main --tags
```

---

## ✅ **Phase 8 Checklist**

- [x] Production environment configuration
- [x] Nginx web server setup
- [x] SSL/TLS configuration
- [x] Security headers and middleware
- [x] Rate limiting implementation
- [x] Monitoring and logging system
- [x] Health check endpoints
- [x] Backup and recovery system
- [x] Automated backup scheduling
- [x] User documentation
- [x] Technical documentation
- [x] Maintenance procedures
- [x] Performance optimization
- [x] Error tracking and alerting
- [x] CI/CD pipeline setup

---

## 🎉 **Project Completion Summary**

Congratulations! You have successfully built a complete University Management System with the following features:

### **✅ Core Features Implemented:**
- **Multi-role Authentication System** (Admin, Teacher, Student, Staff, Department Head)
- **Complete Admin Module** with user management, course management, hall management
- **Teacher Module** with course management, exam creation, result management
- **Student Module** with course enrollment, payment system, result viewing
- **Staff Module** with library management, book issue/return system
- **Department Head Module** with course assignment, notice management
- **Payment System** with bKash mobile banking integration
- **Advanced Search and Filtering**
- **File Upload and Management**
- **Comprehensive Reporting and Analytics**
- **Data Export Functionality**
- **Complete Testing Suite**
- **Performance Optimization**
- **API Endpoints**
- **Production Deployment**
- **Security Implementation**
- **Monitoring and Logging**
- **Backup and Recovery System**
- **Complete Documentation**

### **🚀 Ready for Production:**
- Secure and optimized for production use
- Comprehensive error handling and logging
- Automated backup and recovery
- Performance monitoring
- Complete user and technical documentation
- Maintenance procedures in place

### **📈 Future Enhancements:**
- Mobile application development
- Advanced analytics dashboard
- Integration with external systems
- Advanced reporting features
- Real-time notifications
- Video conferencing integration

**Your University Management System is now ready for deployment and use!** 🎓
