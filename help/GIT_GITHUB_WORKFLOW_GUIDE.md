# 🚀 Complete Git & GitHub Workflow for University Management System

## 📋 **TABLE OF CONTENTS**
1. [Prerequisites](#prerequisites)
2. [Initial Setup](#initial-setup)
3. [Git Configuration](#git-configuration)
4. [Creating GitHub Repository](#creating-github-repository)
5. [Project Initialization](#project-initialization)
6. [Development Workflow](#development-workflow)
7. [Branching Strategy](#branching-strategy)
8. [Collaboration Workflow](#collaboration-workflow)
9. [Deployment Workflow](#deployment-workflow)
10. [Best Practices](#best-practices)

---

## 📦 **PREREQUISITES**

### **Required Software:**
```bash
# 1. Install Git
Download from: https://git-scm.com/downloads
Version: 2.40 or higher

# 2. Install PHP
Version: 8.2 or higher

# 3. Install Composer
Download from: https://getcomposer.org/download/

# 4. Install Node.js (optional for frontend assets)
Download from: https://nodejs.org/
Version: 18.x or higher

# 5. Create GitHub Account
Sign up at: https://github.com
```

### **Verify Installation:**
```bash
git --version
php --version
composer --version
node --version  # optional
npm --version   # optional
```

---

## 🔧 **INITIAL SETUP**

### **Step 1: Configure Git**
```bash
# Set your name
git config --global user.name "Your Name"

# Set your email
git config --global user.email "your.email@example.com"

# Set default branch name
git config --global init.defaultBranch main

# Set line ending handling (Windows)
git config --global core.autocrlf true

# Set line ending handling (Mac/Linux)
git config --global core.autocrlf input

# Verify configuration
git config --list
```

### **Step 2: Generate SSH Key (Recommended)**
```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "your.email@example.com"

# Press Enter to accept default location
# Enter passphrase (optional but recommended)

# Copy public key
# Windows:
type %USERPROFILE%\.ssh\id_ed25519.pub

# Mac/Linux:
cat ~/.ssh/id_ed25519.pub

# Add to GitHub:
# 1. Go to GitHub.com
# 2. Settings → SSH and GPG keys
# 3. New SSH key
# 4. Paste your public key
# 5. Save
```

---

## 🌐 **CREATING GITHUB REPOSITORY**

### **Step 1: Create New Repository on GitHub**
```
1. Go to https://github.com
2. Click "+" icon → "New repository"
3. Repository name: kuet-ums
4. Description: "Khulna University of Engineering & Technology - University Management System"
5. Visibility: 
   - Public (if open source)
   - Private (if confidential)
6. ✅ Add README file
7. ✅ Add .gitignore → Choose "Laravel"
8. ✅ Choose license (MIT recommended)
9. Click "Create repository"
```

### **Step 2: Clone Repository to Local**
```bash
# Using HTTPS
git clone https://github.com/yourusername/kuet-ums.git

# Using SSH (recommended)
git clone git@github.com:yourusername/kuet-ums.git

# Navigate to project
cd kuet-ums
```

---

## 🏗️ **PROJECT INITIALIZATION**

### **Step 1: Install Laravel**
```bash
# Option A: Create new Laravel project
composer create-project laravel/laravel .

# Option B: If Laravel already installed
composer install
```

### **Step 2: Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Create database file
touch database/database.sqlite  # Mac/Linux
type nul > database/database.sqlite  # Windows
```

### **Step 3: Install Dependencies**
```bash
# Install PHP dependencies
composer install

# Install Node dependencies (if using Vite)
npm install

# Or use CDN (as we did)
# No npm install needed
```

### **Step 4: Run Migrations**
```bash
php artisan migrate
```

### **Step 5: Initial Commit**
```bash
git add .
git commit -m "Initial Laravel setup with environment configuration"
git push origin main
```

---

## 💻 **DEVELOPMENT WORKFLOW**

### **Daily Development Cycle:**

#### **1. Start Your Day**
```bash
# Pull latest changes
git pull origin main

# Create feature branch
git checkout -b feature/user-authentication

# Start development server
php artisan serve
```

#### **2. Make Changes**
```bash
# Work on your feature
# Create controllers, models, views, etc.

# Check status frequently
git status

# View changes
git diff
```

#### **3. Commit Changes**
```bash
# Add specific files
git add app/Http/Controllers/AuthController.php
git add resources/views/auth/login.blade.php

# Or add all changes
git add .

# Commit with descriptive message
git commit -m "Add user authentication with login and registration"

# Push to GitHub
git push origin feature/user-authentication
```

#### **4. Create Pull Request**
```
1. Go to GitHub repository
2. Click "Pull requests"
3. Click "New pull request"
4. Select: base: main ← compare: feature/user-authentication
5. Add title and description
6. Click "Create pull request"
7. Review changes
8. Merge when ready
```

#### **5. Merge and Clean Up**
```bash
# Switch back to main
git checkout main

# Pull merged changes
git pull origin main

# Delete local feature branch
git branch -d feature/user-authentication

# Delete remote feature branch
git push origin --delete feature/user-authentication
```

---

## 🌳 **BRANCHING STRATEGY**

### **Branch Types:**

#### **1. Main Branch**
```bash
# Protected branch
# Always deployable
# Only merge through pull requests
```

#### **2. Development Branch (Optional)**
```bash
# Create development branch
git checkout -b development
git push origin development

# All features merge here first
# Test before merging to main
```

#### **3. Feature Branches**
```bash
# Naming convention: feature/description
git checkout -b feature/student-dashboard
git checkout -b feature/teacher-attendance
git checkout -b feature/library-management
```

#### **4. Bugfix Branches**
```bash
# Naming convention: bugfix/description
git checkout -b bugfix/login-error
git checkout -b bugfix/dashboard-crash
```

#### **5. Hotfix Branches**
```bash
# For urgent production fixes
git checkout -b hotfix/security-patch
```

### **Branch Workflow:**
```
main (production)
  ↑
  └── development (testing)
        ↑
        ├── feature/student-module
        ├── feature/teacher-module
        ├── feature/staff-module
        └── bugfix/attendance-error
```

---

## 👥 **COLLABORATION WORKFLOW**

### **For Team Development:**

#### **1. Fork Repository (Team Member)**
```
1. Go to main repository on GitHub
2. Click "Fork" button
3. Clone YOUR fork:
   git clone https://github.com/YOUR-USERNAME/kuet-ums.git
```

#### **2. Add Upstream Remote**
```bash
# Add original repository as upstream
git remote add upstream https://github.com/ORIGINAL-OWNER/kuet-ums.git

# Verify remotes
git remote -v
# Should show:
# origin    (your fork)
# upstream  (original repo)
```

#### **3. Keep Fork Updated**
```bash
# Fetch upstream changes
git fetch upstream

# Merge into your main
git checkout main
git merge upstream/main

# Push to your fork
git push origin main
```

#### **4. Create Feature Branch**
```bash
# Create branch from updated main
git checkout -b feature/my-feature

# Make changes and commit
git add .
git commit -m "Add my feature"

# Push to YOUR fork
git push origin feature/my-feature
```

#### **5. Create Pull Request**
```
1. Go to YOUR fork on GitHub
2. Click "Contribute" → "Open pull request"
3. Select: base repository: ORIGINAL → base: main
4. Select: head repository: YOUR-FORK → compare: feature/my-feature
5. Add description
6. Click "Create pull request"
7. Wait for review and merge
```

---

## 🚀 **DEPLOYMENT WORKFLOW**

### **Deployment to Production:**

#### **1. Prepare for Deployment**
```bash
# Ensure on main branch
git checkout main

# Pull latest changes
git pull origin main

# Run tests
php artisan test

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### **2. Tag Release**
```bash
# Create version tag
git tag -a v1.0.0 -m "Release version 1.0.0 - Initial production release"

# Push tag to GitHub
git push origin v1.0.0

# View all tags
git tag -l
```

#### **3. Deploy to Server**
```bash
# SSH to server
ssh user@your-server.com

# Navigate to project
cd /var/www/kuet-ums

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

---

## 📝 **COMMIT MESSAGE CONVENTIONS**

### **Format:**
```
<type>(<scope>): <subject>

<body>

<footer>
```

### **Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting)
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

### **Examples:**
```bash
# Feature
git commit -m "feat(auth): add user login functionality"

# Bug fix
git commit -m "fix(dashboard): resolve teacher dashboard crash"

# Multiple changes
git commit -m "feat(library): add book issue management

- Add book issue controller
- Create book issue views
- Implement approval workflow
- Add overdue tracking"

# Breaking change
git commit -m "feat(api): change authentication to JWT

BREAKING CHANGE: Session-based auth removed"
```

---

## 🔄 **COMMON GIT COMMANDS**

### **Basic Commands:**
```bash
# Check status
git status

# View changes
git diff

# View commit history
git log
git log --oneline
git log --graph --oneline --all

# Add files
git add filename.php
git add .

# Commit
git commit -m "Your message"

# Push
git push origin branch-name

# Pull
git pull origin branch-name
```

### **Branch Management:**
```bash
# List branches
git branch
git branch -a  # include remote

# Create branch
git branch feature/new-feature

# Switch branch
git checkout feature/new-feature

# Create and switch (shortcut)
git checkout -b feature/new-feature

# Delete branch
git branch -d feature/new-feature  # local
git push origin --delete feature/new-feature  # remote

# Rename branch
git branch -m old-name new-name
```

### **Undo Changes:**
```bash
# Discard changes in file
git checkout -- filename.php

# Unstage file
git reset HEAD filename.php

# Undo last commit (keep changes)
git reset --soft HEAD~1

# Undo last commit (discard changes)
git reset --hard HEAD~1

# Revert commit (create new commit)
git revert commit-hash
```

### **Stash Changes:**
```bash
# Save changes temporarily
git stash

# List stashes
git stash list

# Apply stash
git stash apply

# Apply and remove stash
git stash pop

# Clear all stashes
git stash clear
```

---

## 🎯 **STEP-BY-STEP: BUILDING UMS FROM SCRATCH**

### **Phase 1: Project Setup (Day 1)**

#### **Step 1: Create Repository**
```bash
# On GitHub: Create new repository "kuet-ums"
# Clone to local
git clone git@github.com:yourusername/kuet-ums.git
cd kuet-ums
```

#### **Step 2: Install Laravel**
```bash
composer create-project laravel/laravel .
git add .
git commit -m "chore: initial Laravel installation"
git push origin main
```

#### **Step 3: Configure Environment**
```bash
# Edit .env file
# Set database, app name, etc.
git add .env.example
git commit -m "chore: update environment configuration"
git push origin main
```

---

### **Phase 2: Database Design (Day 2-3)**

#### **Step 1: Create Migrations**
```bash
# Create all migrations
php artisan make:migration create_departments_table
php artisan make:migration create_teachers_table
php artisan make:migration create_students_table
php artisan make:migration create_staff_table
php artisan make:migration create_courses_table
php artisan make:migration create_enrollments_table
php artisan make:migration create_exams_table
php artisan make:migration create_results_table
php artisan make:migration create_attendances_table
php artisan make:migration create_books_table
php artisan make:migration create_book_issues_table
php artisan make:migration create_notices_table
php artisan make:migration create_fees_table
php artisan make:migration create_halls_table

# Commit
git add database/migrations/
git commit -m "feat(database): add all database migrations"
git push origin main
```

#### **Step 2: Create Models**
```bash
# Create all models
php artisan make:model Department
php artisan make:model Teacher
php artisan make:model Student
php artisan make:model Staff
php artisan make:model Course
php artisan make:model Enrollment
php artisan make:model Exam
php artisan make:model Result
php artisan make:model Attendance
php artisan make:model Book
php artisan make:model BookIssue
php artisan make:model Notice
php artisan make:model Fee
php artisan make:model Hall

# Commit
git add app/Models/
git commit -m "feat(models): create all eloquent models with relationships"
git push origin main
```

---

### **Phase 3: Authentication (Day 4)**

#### **Step 1: Install Laravel Breeze**
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build  # or use CDN

git add .
git commit -m "feat(auth): install Laravel Breeze authentication"
git push origin main
```

#### **Step 2: Create Middleware**
```bash
php artisan make:middleware CheckRole
php artisan make:middleware PreventBackButton
php artisan make:middleware AutoEnrollCompulsoryCourses

# Edit middleware files
# Register in bootstrap/app.php

git add app/Http/Middleware/
git add bootstrap/app.php
git commit -m "feat(auth): add role-based access control middleware"
git push origin main
```

---

### **Phase 4: Admin Panel (Day 5-7)**

#### **Step 1: Create Admin Controllers**
```bash
git checkout -b feature/admin-panel

php artisan make:controller Admin/DashboardController
php artisan make:controller Admin/DepartmentController
php artisan make:controller Admin/TeacherController
php artisan make:controller Admin/StudentController
php artisan make:controller Admin/StaffController
php artisan make:controller Admin/CourseController
# ... create all admin controllers

git add app/Http/Controllers/Admin/
git commit -m "feat(admin): create admin panel controllers"
```

#### **Step 2: Create Admin Views**
```bash
# Create views in resources/views/admin/
# Create layout in resources/views/layouts/app.blade.php

git add resources/views/admin/
git add resources/views/layouts/
git commit -m "feat(admin): create admin panel views and layouts"
```

#### **Step 3: Add Admin Routes**
```bash
# Edit routes/web.php
# Add admin routes with middleware

git add routes/web.php
git commit -m "feat(admin): add admin panel routes with authentication"
```

#### **Step 4: Merge Feature**
```bash
git push origin feature/admin-panel

# Create pull request on GitHub
# Review and merge

git checkout main
git pull origin main
git branch -d feature/admin-panel
```

---

### **Phase 5: Student Module (Day 8-10)**

```bash
git checkout -b feature/student-module

# Create controllers
php artisan make:controller Student/DashboardController
php artisan make:controller Student/ProfileController
php artisan make:controller Student/CourseEnrollmentController
php artisan make:controller Student/ResultController
php artisan make:controller Student/LibraryController
# ... etc

git add app/Http/Controllers/Student/
git commit -m "feat(student): create student controllers"

# Create views
# resources/views/student/
git add resources/views/student/
git commit -m "feat(student): create student panel views"

# Create layout
git add resources/views/components/student-layout.blade.php
git commit -m "feat(student): add student layout component"

# Add routes
git add routes/web.php
git commit -m "feat(student): add student routes"

# Push and merge
git push origin feature/student-module
# Create PR, review, merge
```

---

### **Phase 6: Teacher Module (Day 11-13)**

```bash
git checkout -b feature/teacher-module

# Create controllers
php artisan make:controller Teacher/DashboardController
php artisan make:controller Teacher/ProfileController
php artisan make:controller Teacher/CourseController
php artisan make:controller Teacher/AttendanceController
php artisan make:controller Teacher/ExamController
php artisan make:controller Teacher/ResultController

git add app/Http/Controllers/Teacher/
git commit -m "feat(teacher): create teacher controllers"

# Create attendance system
php artisan make:migration create_attendances_table
php artisan make:model Attendance

git add database/migrations/
git add app/Models/Attendance.php
git commit -m "feat(teacher): add attendance tracking system"

# Create views
git add resources/views/teacher/
git add resources/views/components/teacher-layout.blade.php
git commit -m "feat(teacher): create teacher panel views"

# Add routes
git add routes/web.php
git commit -m "feat(teacher): add teacher routes"

# Push and merge
git push origin feature/teacher-module
```

---

### **Phase 7: Staff Module (Day 14-15)**

```bash
git checkout -b feature/staff-module

# Create staff table
php artisan make:migration create_staff_table
php artisan make:model Staff

git add database/migrations/
git add app/Models/Staff.php
git commit -m "feat(staff): create staff table and model"

# Create controllers
php artisan make:controller Staff/DashboardController
php artisan make:controller Staff/ProfileController
php artisan make:controller Staff/LibraryController
php artisan make:controller Staff/BookIssueController

git add app/Http/Controllers/Staff/
git commit -m "feat(staff): create staff controllers"

# Create views
git add resources/views/staff/
git add resources/views/components/staff-layout.blade.php
git commit -m "feat(staff): create staff panel views"

# Add routes
git add routes/web.php
git commit -m "feat(staff): add staff routes"

# Push and merge
git push origin feature/staff-module
```

---

### **Phase 8: Department Head (Day 16)**

```bash
git checkout -b feature/department-head

# Add department head flag
php artisan make:migration add_is_department_head_to_teachers_table

git add database/migrations/
git commit -m "feat(dept-head): add department head flag to teachers"

# Create controllers
php artisan make:controller DepartmentHead/DashboardController
php artisan make:controller DepartmentHead/CourseAssignmentController

git add app/Http/Controllers/DepartmentHead/
git commit -m "feat(dept-head): create department head controllers"

# Create views
git add resources/views/department-head/
git commit -m "feat(dept-head): create department head views"

# Update teacher layout
git add resources/views/components/teacher-layout.blade.php
git commit -m "feat(dept-head): add conditional menu for department heads"

# Push and merge
git push origin feature/department-head
```

---

### **Phase 9: Testing & Bug Fixes (Day 17-18)**

```bash
git checkout -b bugfix/various-fixes

# Fix bugs as discovered
# Test all features

git add .
git commit -m "fix: resolve dashboard isEmpty() error"

git add .
git commit -m "fix: add staff records for existing users"

git add .
git commit -m "fix: update login credentials in documentation"

# Push and merge
git push origin bugfix/various-fixes
```

---

### **Phase 10: Final Polish (Day 19-20)**

```bash
git checkout -b feature/final-improvements

# Add finishing touches
# Optimize code
# Add documentation

git add .
git commit -m "docs: add comprehensive system documentation"

git add .
git commit -m "refactor: optimize database queries"

git add .
git commit -m "style: improve UI consistency"

# Push and merge
git push origin feature/final-improvements
```

---

## 📊 **COMMIT HISTORY EXAMPLE**

```bash
# View your commit history
git log --oneline --graph --all

# Example output:
* a1b2c3d (HEAD -> main) docs: add Git workflow guide
* d4e5f6g feat(dept-head): add course assignment
* g7h8i9j feat(staff): complete staff module
* j1k2l3m feat(teacher): add attendance system
* m4n5o6p feat(student): complete student module
* p7q8r9s feat(admin): create admin panel
* s1t2u3v feat(auth): add authentication
* v4w5x6y feat(database): create all migrations
* y7z8a9b chore: initial Laravel setup
```

---

## 🔒 **GITIGNORE CONFIGURATION**

### **Essential .gitignore entries:**
```gitignore
# Laravel
/vendor
/node_modules
/public/hot
/public/storage
/storage/*.key
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log

# IDE
.idea/
.vscode/
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db

# Database
*.sqlite
*.sqlite-journal

# Temporary files
*.tmp
*.log
```

---

## 🎯 **BEST PRACTICES**

### **1. Commit Frequently**
```bash
# Small, focused commits
git commit -m "feat(auth): add login form"
git commit -m "feat(auth): add login validation"
git commit -m "feat(auth): add login controller"

# NOT this:
git commit -m "feat(auth): complete entire authentication system"
```

### **2. Write Descriptive Messages**
```bash
# Good
git commit -m "fix(dashboard): resolve isEmpty() error on courseStats array"

# Bad
git commit -m "fix bug"
```

### **3. Pull Before Push**
```bash
# Always pull first to avoid conflicts
git pull origin main
git push origin main
```

### **4. Use Branches**
```bash
# Never work directly on main
git checkout -b feature/my-feature
# Make changes
git push origin feature/my-feature
```

### **5. Review Before Commit**
```bash
# Check what you're committing
git status
git diff

# Then commit
git add .
git commit -m "Your message"
```

---

## 🚨 **HANDLING CONFLICTS**

### **When Merge Conflicts Occur:**

```bash
# Pull latest changes
git pull origin main

# If conflicts:
# 1. Git will mark conflicted files
# 2. Open files and look for:
<<<<<<< HEAD
Your changes
=======
Their changes
>>>>>>> branch-name

# 3. Edit file to resolve
# 4. Remove conflict markers
# 5. Save file

# 6. Mark as resolved
git add conflicted-file.php

# 7. Complete merge
git commit -m "merge: resolve conflicts from main"

# 8. Push
git push origin your-branch
```

---

## 📦 **RELEASE WORKFLOW**

### **Creating Releases:**

#### **1. Prepare Release**
```bash
# Ensure all features merged to main
git checkout main
git pull origin main

# Run full test suite
php artisan test

# Update version in composer.json
# Update CHANGELOG.md
```

#### **2. Create Tag**
```bash
# Semantic versioning: MAJOR.MINOR.PATCH
git tag -a v1.0.0 -m "Release v1.0.0

Features:
- Complete admin panel
- Student management
- Teacher management
- Staff management
- Course enrollment
- Attendance tracking
- Exam management
- Library system
- Department head features

Fixes:
- Dashboard isEmpty() error
- Staff login issues
- Authentication bugs
"

git push origin v1.0.0
```

#### **3. Create GitHub Release**
```
1. Go to GitHub repository
2. Click "Releases"
3. Click "Create a new release"
4. Select tag: v1.0.0
5. Release title: "KUET UMS v1.0.0 - Initial Release"
6. Description: Copy from tag message
7. Attach files (if any)
8. Click "Publish release"
```

---

## 🔄 **CONTINUOUS INTEGRATION**

### **GitHub Actions Workflow:**

Create `.github/workflows/laravel.yml`:

```yaml
name: Laravel CI

on:
  push:
    branches: [ main, development ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        
    - name: Install Dependencies
      run: composer install
      
    - name: Copy Environment
      run: cp .env.example .env
      
    - name: Generate Key
      run: php artisan key:generate
      
    - name: Run Migrations
      run: php artisan migrate
      
    - name: Run Tests
      run: php artisan test
```

Commit:
```bash
git add .github/workflows/laravel.yml
git commit -m "ci: add GitHub Actions workflow"
git push origin main
```

---

## 📚 **DOCUMENTATION WORKFLOW**

### **Keep Documentation Updated:**

```bash
# Update README.md with each major feature
git add README.md
git commit -m "docs: update README with installation instructions"

# Create API documentation
git add docs/API.md
git commit -m "docs: add API documentation"

# Update changelog
git add CHANGELOG.md
git commit -m "docs: update changelog for v1.1.0"
```

---

## 🎓 **LEARNING RESOURCES**

### **Git Basics:**
- Official Git Book: https://git-scm.com/book/en/v2
- GitHub Guides: https://guides.github.com/
- Interactive Tutorial: https://learngitbranching.js.org/

### **Laravel Development:**
- Laravel Documentation: https://laravel.com/docs
- Laracasts: https://laracasts.com
- Laravel News: https://laravel-news.com

---

## ✅ **CHECKLIST FOR NEW PROJECT**

### **Initial Setup:**
- [ ] Install Git
- [ ] Configure Git user
- [ ] Generate SSH key
- [ ] Add SSH key to GitHub
- [ ] Create GitHub repository
- [ ] Clone repository locally

### **Laravel Setup:**
- [ ] Install Laravel
- [ ] Configure .env
- [ ] Generate app key
- [ ] Create database
- [ ] Run migrations

### **Development:**
- [ ] Create feature branches
- [ ] Commit regularly
- [ ] Write descriptive messages
- [ ] Push to GitHub
- [ ] Create pull requests
- [ ] Review code
- [ ] Merge to main

### **Testing:**
- [ ] Test all features
- [ ] Fix bugs
- [ ] Run automated tests
- [ ] Manual testing

### **Deployment:**
- [ ] Tag release
- [ ] Deploy to server
- [ ] Monitor for issues
- [ ] Create GitHub release

---

## 🎯 **QUICK REFERENCE**

### **Most Used Commands:**
```bash
# Daily workflow
git status
git pull origin main
git checkout -b feature/new-feature
git add .
git commit -m "feat: add new feature"
git push origin feature/new-feature

# Branch management
git branch
git checkout branch-name
git merge branch-name

# Undo
git stash
git reset --hard HEAD

# View history
git log --oneline
git log --graph --all
```

---

## 🚀 **FINAL WORKFLOW SUMMARY**

```
1. Create GitHub repo
2. Clone to local
3. Install Laravel
4. Create feature branch
5. Develop feature
6. Commit changes
7. Push to GitHub
8. Create pull request
9. Review code
10. Merge to main
11. Delete feature branch
12. Repeat for next feature
13. Tag releases
14. Deploy to production
```

---

**This guide covers everything you need to build and manage a UMS project with Git and GitHub!**

**Date:** October 9, 2025  
**Version:** 1.0  
**Status:** Complete

