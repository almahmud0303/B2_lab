# 🌳 Git Branching Strategy for UMS - Complete Guide

## 📋 **PROFESSIONAL GIT WORKFLOW**

This guide shows you **exactly how to use Git branches** for the UMS project, **what branches to create**, and **when to create them**.

---

# 🎯 **BRANCHING STRATEGY OVERVIEW**

## **Branch Structure:**

```
main (production-ready code)
  │
  ├── develop (development branch)
  │     │
  │     ├── feature/database-schema
  │     ├── feature/authentication
  │     ├── feature/admin-panel
  │     ├── feature/student-module
  │     ├── feature/teacher-module
  │     ├── feature/staff-module
  │     ├── feature/department-head
  │     │
  │     ├── bugfix/student-login-issue
  │     ├── bugfix/attendance-calculation
  │     │
  │     └── hotfix/security-patch
  │
  └── release/v1.0.0 (release preparation)
```

---

# 📝 **BRANCH TYPES**

## **1. Main Branch**
- **Purpose:** Production-ready code
- **Rule:** Never commit directly
- **Updates:** Only from `develop` or `hotfix` branches

## **2. Develop Branch**
- **Purpose:** Integration branch for features
- **Rule:** Never commit directly (except small fixes)
- **Updates:** From feature branches via Pull Requests

## **3. Feature Branches**
- **Purpose:** New features or enhancements
- **Naming:** `feature/feature-name`
- **Created from:** `develop`
- **Merged into:** `develop`

## **4. Bugfix Branches**
- **Purpose:** Fix bugs in development
- **Naming:** `bugfix/bug-name`
- **Created from:** `develop`
- **Merged into:** `develop`

## **5. Hotfix Branches**
- **Purpose:** Emergency fixes for production
- **Naming:** `hotfix/issue-name`
- **Created from:** `main`
- **Merged into:** Both `main` and `develop`

## **6. Release Branches**
- **Purpose:** Prepare new release
- **Naming:** `release/v1.0.0`
- **Created from:** `develop`
- **Merged into:** Both `main` and `develop`

---

# 🚀 **STEP-BY-STEP BRANCHING WORKFLOW**

## **PHASE 1: Project Setup**

### **Day 1: Initialize Repository**

```bash
# 1. Create repository on GitHub
# (Done via GitHub website)

# 2. Clone repository
git clone https://github.com/yourusername/kuet-ums.git
cd kuet-ums

# 3. Check current branch
git branch
# Output: * main

# 4. Create develop branch
git checkout -b develop
# This creates and switches to develop branch

# 5. Push develop to GitHub
git push -u origin develop
# -u sets upstream tracking

# 6. Verify branches
git branch -a
# Output:
# * develop
#   main
#   remotes/origin/develop
#   remotes/origin/main
```

---

## **PHASE 2: Database Design (Day 2-3)**

### **Create Feature Branch:**

```bash
# 1. Make sure you're on develop
git checkout develop

# 2. Pull latest changes
git pull origin develop

# 3. Create feature branch
git checkout -b feature/database-schema

# 4. Verify you're on feature branch
git branch
# Output:
#   develop
#   main
# * feature/database-schema
```

### **Work on Feature:**

```bash
# 5. Create migrations
php artisan make:migration create_departments_table
php artisan make:migration create_teachers_table
# ... (all migrations)

# 6. Check status
git status
# Shows all new migration files

# 7. Add files
git add database/migrations/

# 8. Commit with meaningful message
git commit -m "feat: create database migrations for all tables

- Add departments table migration
- Add teachers table migration
- Add students table migration
- Add staff table migration
- Add courses table migration
- Add enrollments table migration
- Add all supporting tables

Related to: Database Schema Design"

# 9. Push to GitHub
git push -u origin feature/database-schema
```

### **Create Pull Request:**

```bash
# 10. Go to GitHub repository
# 11. Click "Compare & pull request"
# 12. Select:
#     - base: develop
#     - compare: feature/database-schema
# 13. Fill Pull Request:
#     Title: "Feature: Database Schema"
#     Description:
#       ## What
#       Created all database migrations for UMS system
#       
#       ## Tables Created
#       - users, departments, teachers, students, staff
#       - courses, enrollments, attendances
#       - exams, results, books, book_issues
#       - notices, fees, halls
#       
#       ## Testing
#       - [x] Migrations run successfully
#       - [x] No foreign key errors
#       - [x] All relationships defined
# 14. Click "Create pull request"
```

### **Merge Pull Request:**

```bash
# 15. Review code on GitHub
# 16. Click "Merge pull request"
# 17. Click "Confirm merge"
# 18. Delete branch on GitHub (optional but recommended)

# 19. Update local develop branch
git checkout develop
git pull origin develop

# 20. Delete local feature branch (optional)
git branch -d feature/database-schema
```

---

## **PHASE 3: Models (Day 3)**

### **Create New Feature Branch:**

```bash
# 1. Start from updated develop
git checkout develop
git pull origin develop

# 2. Create new feature branch
git checkout -b feature/eloquent-models

# 3. Create all models
php artisan make:model Department
php artisan make:model Teacher
# ... (all models)

# 4. Edit models, add relationships

# 5. Commit incrementally
git add app/Models/Department.php
git commit -m "feat: create Department model with relationships"

git add app/Models/Teacher.php
git commit -m "feat: create Teacher model with relationships"

# ... (commit each model separately)

# 6. Push to GitHub
git push -u origin feature/eloquent-models

# 7. Create Pull Request
# (Follow same process as above)
```

---

## **PHASE 4: Authentication (Day 4-5)**

```bash
# 1. Create feature branch
git checkout develop
git pull origin develop
git checkout -b feature/authentication

# 2. Install Breeze
composer require laravel/breeze --dev
git add composer.json composer.lock
git commit -m "feat: install Laravel Breeze for authentication"

# 3. Setup Breeze
php artisan breeze:install blade
git add .
git commit -m "feat: setup Laravel Breeze scaffolding"

# 4. Create middleware
php artisan make:middleware CheckRole
git add app/Http/Middleware/CheckRole.php
git commit -m "feat: create CheckRole middleware for role-based access"

php artisan make:middleware PreventBackButton
git add app/Http/Middleware/PreventBackButton.php
git commit -m "feat: create PreventBackButton middleware"

# 5. Update authentication logic
# (Edit files)
git add app/Http/Controllers/Auth/
git commit -m "feat: implement role-based dashboard redirection"

# 6. Create seeders
php artisan make:seeder AdminSeeder
git add database/seeders/AdminSeeder.php
git commit -m "feat: create AdminSeeder for initial admin users"

# 7. Push and create PR
git push -u origin feature/authentication
# Create Pull Request on GitHub
```

---

## **PHASE 5: Admin Panel (Day 6-7)**

```bash
# Create feature branch
git checkout develop
git pull origin develop
git checkout -b feature/admin-panel

# Work in iterations, committing each controller/view set
git add app/Http/Controllers/Admin/DashboardController.php
git add resources/views/admin/dashboard.blade.php
git commit -m "feat: implement admin dashboard with statistics"

git add app/Http/Controllers/Admin/DepartmentController.php
git add resources/views/admin/departments/
git commit -m "feat: implement department CRUD operations"

git add app/Http/Controllers/Admin/TeacherController.php
git add resources/views/admin/teachers/
git commit -m "feat: implement teacher management system"

# ... (continue for all admin features)

# Push and create PR
git push -u origin feature/admin-panel
```

---

## **PHASE 6: Student Module (Day 8-9)**

```bash
git checkout develop
git pull origin develop
git checkout -b feature/student-module

# Commit each major feature
git commit -m "feat: create student dashboard with enrollment stats"
git commit -m "feat: implement student profile management"
git commit -m "feat: add course enrollment functionality"
git commit -m "feat: implement student results display"
git commit -m "feat: add profile picture upload for students"

git push -u origin feature/student-module
```

---

## **PHASE 7: Teacher Module (Day 11-12)**

```bash
git checkout develop
git pull origin develop
git checkout -b feature/teacher-module

git commit -m "feat: create teacher dashboard with course overview"
git commit -m "feat: implement attendance marking system"
git commit -m "feat: add exam creation and management"
git commit -m "feat: implement marks entry system"
git commit -m "feat: add result publishing functionality"

git push -u origin feature/teacher-module
```

---

## **PHASE 8: Staff Module (Day 13)**

```bash
git checkout develop
git pull origin develop
git checkout -b feature/staff-module

git commit -m "feat: create staff dashboard"
git commit -m "feat: implement library book management"
git commit -m "feat: add book issue and return system"
git commit -m "feat: implement student records access (limited)"

git push -u origin feature/staff-module
```

---

## **PHASE 9: Department Head (Day 14)**

```bash
git checkout develop
git pull origin develop
git checkout -b feature/department-head

git commit -m "feat: add is_department_head flag to teachers"
git commit -m "feat: create department head dashboard"
git commit -m "feat: implement course assignment to teachers"
git commit -m "feat: add teacher workload reports"

git push -u origin feature/department-head
```

---

## **PHASE 10: Bug Fixes**

### **When you find a bug:**

```bash
# 1. Create bugfix branch from develop
git checkout develop
git pull origin develop
git checkout -b bugfix/student-login-redirect

# 2. Fix the bug
# (Edit files)

# 3. Commit with clear description
git add app/Http/Controllers/Auth/AuthenticatedSessionController.php
git commit -m "fix: correct student dashboard redirect after login

Previous issue: Students redirected to admin dashboard
Root cause: Missing role check in dashboard route
Solution: Added proper role-based redirection

Fixes #15"

# 4. Push and create PR
git push -u origin bugfix/student-login-redirect

# 5. Create Pull Request
# Title: "Fix: Student Login Redirect Issue"
# Link to issue: "Fixes #15"
```

---

## **PHASE 11: Emergency Hotfix**

### **When production has critical bug:**

```bash
# 1. Create hotfix from main (not develop!)
git checkout main
git pull origin main
git checkout -b hotfix/security-vulnerability

# 2. Fix the critical issue
# (Edit files)

# 3. Commit
git commit -m "hotfix: patch SQL injection vulnerability in search

Security issue: User input not sanitized
Impact: Potential database breach
Fix: Added prepared statements

CVE: None (internal finding)"

# 4. Push
git push -u origin hotfix/security-vulnerability

# 5. Create TWO Pull Requests:
#    PR 1: hotfix/security-vulnerability → main
#    PR 2: hotfix/security-vulnerability → develop

# 6. After merging to main, tag the release
git checkout main
git pull origin main
git tag -a v1.0.1 -m "Security hotfix v1.0.1"
git push origin v1.0.1
```

---

## **PHASE 12: Release Preparation**

```bash
# 1. Create release branch from develop
git checkout develop
git pull origin develop
git checkout -b release/v1.0.0

# 2. Update version numbers
# Edit package.json, composer.json, etc.
git commit -m "chore: bump version to 1.0.0"

# 3. Update CHANGELOG.md
git add CHANGELOG.md
git commit -m "docs: update changelog for v1.0.0"

# 4. Final testing
# (Run all tests)

# 5. Push
git push -u origin release/v1.0.0

# 6. Create Pull Requests:
#    PR 1: release/v1.0.0 → main
#    PR 2: release/v1.0.0 → develop

# 7. After merging to main, tag
git checkout main
git pull origin main
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

---

# 📋 **COMPLETE BRANCH LIST FOR UMS PROJECT**

## **Required Branches:**

1. **`main`** - Production code
2. **`develop`** - Development integration

## **Feature Branches (15 total):**

1. **`feature/database-schema`** - All migrations
2. **`feature/eloquent-models`** - All models and relationships
3. **`feature/authentication`** - Breeze, middleware, login/logout
4. **`feature/seeders`** - Database seeders
5. **`feature/admin-panel`** - Admin dashboard and CRUD
6. **`feature/admin-departments`** - Department management
7. **`feature/admin-teachers`** - Teacher management
8. **`feature/admin-students`** - Student management
9. **`feature/admin-staff`** - Staff management
10. **`feature/student-module`** - Student dashboard, profile, enrollment
11. **`feature/student-results`** - Student results and academic info
12. **`feature/teacher-module`** - Teacher dashboard and courses
13. **`feature/teacher-attendance`** - Attendance marking system
14. **`feature/teacher-exams`** - Exam and marks management
15. **`feature/staff-module`** - Staff dashboard and library
16. **`feature/department-head`** - Department head features
17. **`feature/ui-improvements`** - UI/UX enhancements
18. **`feature/profile-pictures`** - File upload functionality

## **Bugfix Branches (as needed):**

- **`bugfix/login-redirect`**
- **`bugfix/attendance-calculation`**
- **`bugfix/pagination-error`**
- **`bugfix/image-upload`**

## **Hotfix Branches (as needed):**

- **`hotfix/security-patch`**
- **`hotfix/critical-bug`**

## **Release Branches:**

- **`release/v1.0.0`**
- **`release/v1.1.0`**
- **`release/v2.0.0`**

---

# 📝 **COMMIT MESSAGE CONVENTIONS**

## **Format:**
```
<type>(<scope>): <subject>

<body>

<footer>
```

## **Types:**

- **`feat:`** New feature
- **`fix:`** Bug fix
- **`docs:`** Documentation only
- **`style:`** Code style (formatting, no logic change)
- **`refactor:`** Code refactoring
- **`perf:`** Performance improvement
- **`test:`** Adding tests
- **`chore:`** Maintenance tasks

## **Examples:**

### **Feature:**
```bash
git commit -m "feat(admin): add department CRUD operations

Implemented:
- Create department
- Edit department
- Delete department
- List all departments

Includes validation and error handling."
```

### **Bug Fix:**
```bash
git commit -m "fix(student): resolve profile image upload issue

Issue: Images not displaying after upload
Cause: Storage link not created
Solution: Added storage:link command to installation

Fixes #23"
```

### **Documentation:**
```bash
git commit -m "docs: add installation instructions to README

Added:
- Prerequisites
- Installation steps
- Configuration guide
- Troubleshooting section"
```

### **Refactoring:**
```bash
git commit -m "refactor(teacher): optimize N+1 queries in dashboard

Changed:
- Added eager loading for courses
- Reduced queries from 45 to 3
- Improved page load time by 60%"
```

---

# 🔄 **DAILY WORKFLOW**

## **Every Day Before Starting Work:**

```bash
# 1. Pull latest changes
git checkout develop
git pull origin develop

# 2. Create/checkout your feature branch
git checkout -b feature/your-feature
# OR if already exists:
git checkout feature/your-feature

# 3. Merge latest develop into your branch
git merge develop

# 4. Start coding!
```

## **During Work:**

```bash
# Commit frequently (every 30-60 minutes)
git add .
git commit -m "feat: implement user profile update"

# Push to backup your work
git push origin feature/your-feature
```

## **End of Day:**

```bash
# Make sure all work is committed
git status

# Push final changes
git push origin feature/your-feature

# Create Pull Request if feature complete
```

---

# 🎯 **PULL REQUEST TEMPLATE**

```markdown
## Description
Brief description of what this PR does.

## Type of Change
- [ ] New feature
- [ ] Bug fix
- [ ] Breaking change
- [ ] Documentation update

## Changes Made
- Added student dashboard
- Implemented course enrollment
- Fixed profile image upload

## Testing
- [ ] Tested locally
- [ ] All existing tests pass
- [ ] Added new tests

## Screenshots (if applicable)
[Add screenshots here]

## Related Issues
Fixes #123
Related to #456

## Checklist
- [ ] Code follows project style guidelines
- [ ] Self-reviewed code
- [ ] Commented complex code
- [ ] Updated documentation
- [ ] No console.log() or dd() left
- [ ] Tested on fresh database
```

---

# ⚠️ **COMMON MISTAKES TO AVOID**

## **❌ DON'T:**

1. **Commit directly to main**
   ```bash
   # ❌ NEVER do this:
   git checkout main
   git add .
   git commit -m "updates"
   git push origin main
   ```

2. **Work on develop directly**
   ```bash
   # ❌ NEVER do this:
   git checkout develop
   # (make changes)
   git commit -m "new feature"
   ```

3. **Create feature branch from main**
   ```bash
   # ❌ WRONG:
   git checkout main
   git checkout -b feature/new-feature
   
   # ✅ CORRECT:
   git checkout develop
   git checkout -b feature/new-feature
   ```

4. **Commit without pulling first**
   ```bash
   # ❌ WRONG:
   git add .
   git commit -m "changes"
   git push
   # ERROR: Push rejected, remote has changes
   
   # ✅ CORRECT:
   git pull origin develop
   git add .
   git commit -m "changes"
   git push
   ```

5. **Use vague commit messages**
   ```bash
   # ❌ BAD:
   git commit -m "updates"
   git commit -m "fix"
   git commit -m "changes"
   
   # ✅ GOOD:
   git commit -m "feat: add student enrollment validation"
   git commit -m "fix: resolve attendance calculation error"
   git commit -m "refactor: optimize database queries in dashboard"
   ```

---

# 🚀 **QUICK REFERENCE COMMANDS**

## **Branch Management:**
```bash
# Create branch
git checkout -b feature/branch-name

# Switch branch
git checkout branch-name

# List all branches
git branch -a

# Delete local branch
git branch -d branch-name

# Delete remote branch
git push origin --delete branch-name

# Rename current branch
git branch -m new-name
```

## **Syncing:**
```bash
# Pull latest from remote
git pull origin develop

# Push to remote
git push origin feature/branch-name

# Push and set upstream
git push -u origin feature/branch-name
```

## **Merging:**
```bash
# Merge develop into feature branch
git checkout feature/branch-name
git merge develop

# Merge feature into develop (via PR preferred)
git checkout develop
git merge feature/branch-name
```

## **Stashing:**
```bash
# Save uncommitted changes
git stash

# List stashes
git stash list

# Apply stashed changes
git stash apply

# Apply and remove stash
git stash pop
```

## **Undoing:**
```bash
# Undo last commit (keep changes)
git reset --soft HEAD~1

# Undo last commit (discard changes)
git reset --hard HEAD~1

# Revert commit (create new commit that undoes)
git revert commit-hash
```

---

# 📊 **WORKFLOW VISUALIZATION**

```
DAY 1-2: Setup
main ──────────────────────────────────────────►
        \
         develop ──────────────────────────────►

DAY 2-3: Database
main ──────────────────────────────────────────►
        \
         develop ──────────────────────────────►
                  \
                   feature/database-schema ───► (merge)
                   
DAY 3: Models  
main ──────────────────────────────────────────►
        \
         develop ──────────────────────────────►
                           \
                            feature/models ───► (merge)

DAY 4-5: Auth
main ──────────────────────────────────────────►
        \
         develop ──────────────────────────────►
                                \
                                 feature/auth ─► (merge)

... (continue for all features)

DAY 20: Release
main ──┬──────────────────────────────────────►
       │                                  ▲
       │                                  │ (merge)
       └─ develop ───────────────────────┴────►
                     \
                      release/v1.0.0 ────────► (tag v1.0.0)
```

---

**This branching strategy will keep your code organized, enable collaboration, and maintain a clean project history!**


