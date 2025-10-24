# 🌳 Git Workflow - Main Branch Only (Simplified)

## 📋 **SIMPLIFIED GIT WORKFLOW FOR UMS**

This guide shows you how to use Git with **ONLY the main branch** - no develop branch needed!

---

# ✅ **WHY MAIN BRANCH ONLY?**

**Benefits:**
- ✅ Simpler to understand
- ✅ No confusion about branches
- ✅ Perfect for solo projects
- ✅ Perfect for learning
- ✅ Still professional
- ✅ Works with GitHub perfectly

**You'll use:**
- `main` branch - Your primary branch
- `feature/*` branches - Temporary branches for features
- `bugfix/*` branches - Temporary branches for bug fixes

---

# 🚀 **INITIAL SETUP (Do Once)**

## **Step 1: Create Repository on GitHub**

1. Go to https://github.com
2. Click "+" → "New repository"
3. Name: `kuet-ums`
4. ✅ Add README
5. ✅ Add .gitignore (Laravel)
6. Click "Create repository"

---

## **Step 2: Clone and Setup**

```bash
# Clone repository
cd C:\xampp\htdocs
git clone https://github.com/YOUR_USERNAME/kuet-ums.git
cd kuet-ums

# Install Laravel
composer create-project laravel/laravel .

# Configure .env, create database...
# (Follow BUILD_UMS_STEP_BY_STEP.md steps)

# Commit to main
git add .
git commit -m "feat: initial Laravel installation and configuration"
git push origin main
```

**✅ Setup Complete! You're on main branch and ready to go!**

---

# 📝 **DAILY WORKFLOW**

## **Starting a New Feature (e.g., Day 2 - Database):**

```bash
# 1. Make sure you're on main and updated
git checkout main
git pull origin main

# 2. Create feature branch
git checkout -b feature/database-schema

# 3. Do your work
# - Create migrations
# - Write code
# - Test features

# 4. Commit frequently
git add .
git commit -m "feat: create departments table migration"

# Continue working...
git add .
git commit -m "feat: create teachers table migration"

# 5. Push feature branch to GitHub
git push -u origin feature/database-schema
```

---

## **Merging Feature Back to Main:**

### **Option A: Via GitHub Pull Request (Recommended)**

```bash
# 1. Push your feature branch
git push origin feature/database-schema

# 2. Go to GitHub
# 3. Click "Compare & pull request"
# 4. Set:
#    - base: main
#    - compare: feature/database-schema
# 5. Click "Create pull request"
# 6. Review code
# 7. Click "Merge pull request"
# 8. Click "Confirm merge"
# 9. Delete branch on GitHub (optional)

# 10. Back in terminal:
git checkout main
git pull origin main

# 11. Delete local feature branch (optional)
git branch -d feature/database-schema
```

### **Option B: Merge Locally (Simpler)**

```bash
# 1. Switch to main
git checkout main

# 2. Merge feature branch
git merge feature/database-schema

# 3. Push to GitHub
git push origin main

# 4. Delete feature branch (optional)
git branch -d feature/database-schema
```

---

# 📅 **BRANCH WORKFLOW FOR EACH DAY**

## **Day 1: Setup**
```bash
# Work directly on main (initial setup)
git checkout main
# ... install Laravel ...
git add .
git commit -m "feat: initial Laravel setup"
git push origin main
```

## **Day 2: Database Schema**
```bash
git checkout main
git pull origin main
git checkout -b feature/database-schema

# Create migrations...
git add database/migrations/
git commit -m "feat: create all database migrations"

git push origin feature/database-schema

# Merge back to main (via GitHub PR or locally)
git checkout main
git merge feature/database-schema
git push origin main
git branch -d feature/database-schema
```

## **Day 3: Models**
```bash
git checkout main
git pull origin main
git checkout -b feature/eloquent-models

# Create models...
git add app/Models/
git commit -m "feat: create all Eloquent models with relationships"

git push origin feature/eloquent-models

# Merge to main
git checkout main
git merge feature/eloquent-models
git push origin main
git branch -d feature/eloquent-models
```

## **Day 4-5: Authentication**
```bash
git checkout main
git pull origin main
git checkout -b feature/authentication

# Install Breeze, create middleware...
git add .
git commit -m "feat: implement authentication system"

git push origin feature/authentication

git checkout main
git merge feature/authentication
git push origin main
git branch -d feature/authentication
```

## **Day 6-7: Admin Panel**
```bash
git checkout main
git pull origin main
git checkout -b feature/admin-panel

# Create admin controllers and views...
git add .
git commit -m "feat: implement admin panel with CRUD operations"

git push origin feature/admin-panel

git checkout main
git merge feature/admin-panel
git push origin main
git branch -d feature/admin-panel
```

## **Day 8-9: Student Module**
```bash
git checkout main
git pull origin main
git checkout -b feature/student-module

# Create student controllers and views...
git add .
git commit -m "feat: implement student module with profile management"

git push origin feature/student-module

git checkout main
git merge feature/student-module
git push origin main
git branch -d feature/student-module
```

## **Day 10-12: Teacher Module**
```bash
git checkout main
git pull origin main
git checkout -b feature/teacher-module

# Create teacher features...
git add .
git commit -m "feat: implement teacher module with attendance and exams"

git push origin feature/teacher-module

git checkout main
git merge feature/teacher-module
git push origin main
git branch -d feature/teacher-module
```

## **Day 13: Staff Module**
```bash
git checkout main
git pull origin main
git checkout -b feature/staff-module

# Create staff features...
git add .
git commit -m "feat: implement staff module with library management"

git push origin feature/staff-module

git checkout main
git merge feature/staff-module
git push origin main
git branch -d feature/staff-module
```

## **Day 14: Department Head**
```bash
git checkout main
git pull origin main
git checkout -b feature/department-head

# Add department head features...
git add .
git commit -m "feat: implement department head module"

git push origin feature/department-head

git checkout main
git merge feature/department-head
git push origin main
git branch -d feature/department-head
```

## **Days 15-20: Finalization**
```bash
# Each feature gets its own branch from main
git checkout main
git checkout -b feature/profile-pictures
# ... work and merge ...

git checkout main
git checkout -b bugfix/student-login
# ... work and merge ...
```

---

# 📊 **COMPLETE BRANCH LIST**

## **Main Branch:**
- `main` - Your primary branch (always deployable)

## **Feature Branches (Create as needed, delete after merge):**
1. `feature/database-schema` (Day 2)
2. `feature/eloquent-models` (Day 3)
3. `feature/authentication` (Day 4-5)
4. `feature/admin-panel` (Day 6-7)
5. `feature/student-module` (Day 8-9)
6. `feature/teacher-module` (Day 10-12)
7. `feature/staff-module` (Day 13)
8. `feature/department-head` (Day 14)
9. `feature/ui-improvements` (Day 15)
10. `feature/profile-pictures` (Day 16)
11. `feature/notices-fees` (Day 17)

## **Bugfix Branches (As needed):**
- `bugfix/login-redirect`
- `bugfix/image-upload`
- `bugfix/attendance-calculation`

---

# 🎯 **SIMPLE RULES**

## **Golden Rules:**

1. **Always start from main:**
   ```bash
   git checkout main
   git pull origin main
   ```

2. **Create feature branch for each new feature:**
   ```bash
   git checkout -b feature/feature-name
   ```

3. **Commit often:**
   ```bash
   git add .
   git commit -m "feat: what you did"
   ```

4. **Push your feature:**
   ```bash
   git push origin feature/feature-name
   ```

5. **Merge back to main:**
   ```bash
   git checkout main
   git merge feature/feature-name
   git push origin main
   ```

6. **Delete feature branch (optional):**
   ```bash
   git branch -d feature/feature-name
   ```

---

# 🔄 **QUICK REFERENCE**

## **Common Commands:**

```bash
# Check current branch
git branch

# Switch to main
git checkout main

# Create new feature branch
git checkout -b feature/my-feature

# See all branches
git branch -a

# Delete branch
git branch -d feature/my-feature

# Push to GitHub
git push origin main

# Pull from GitHub
git pull origin main

# Check status
git status

# See commit history
git log --oneline

# Undo last commit (keep changes)
git reset --soft HEAD~1
```

---

# ⚠️ **IMPORTANT NOTES**

## **When Guides Say "develop":**

**Replace with "main":**

```bash
# Guide says:
git checkout develop
git pull origin develop

# You do:
git checkout main
git pull origin main
```

**Simple substitution!**

---

## **All Feature Branches From Main:**

```
main ─────────────────────────────────────►
  │
  ├─ feature/database-schema ──► merge back
  │
  ├─ feature/models ──────────► merge back
  │
  ├─ feature/auth ────────────► merge back
  │
  └─ feature/admin ───────────► merge back
```

**Each feature branches from main, then merges back to main!**

---

# 🎓 **EXAMPLE WORKFLOW**

## **Complete Example for Day 2:**

```bash
# Start of Day 2
cd C:\xampp\htdocs\myapp3

# Make sure on main
git checkout main

# Get latest code
git pull origin main

# Create feature branch for database work
git checkout -b feature/database-schema

# Create migrations
php artisan make:migration create_departments_table
php artisan make:migration create_teachers_table
# ... create all migrations ...

# Add code to migrations...

# Commit after each migration
git add database/migrations/*departments*.php
git commit -m "feat: create departments table migration"

git add database/migrations/*teachers*.php
git commit -m "feat: create teachers table migration"

# ... commit all migrations ...

# Run migrations to test
php artisan migrate

# All working? Push to GitHub
git push -u origin feature/database-schema

# Go to GitHub, create PR, merge

# Or merge locally:
git checkout main
git merge feature/database-schema

# Push main
git push origin main

# Clean up
git branch -d feature/database-schema

# ✅ Day 2 complete!
# Ready for Day 3
```

---

# 📦 **YOUR WORKFLOW SUMMARY**

## **For Every Feature:**

1. `git checkout main` - Start from main
2. `git pull origin main` - Get latest
3. `git checkout -b feature/name` - Create feature branch
4. *Work and commit*
5. `git push origin feature/name` - Push feature
6. *Merge to main (GitHub PR or local)*
7. `git push origin main` - Push main
8. `git branch -d feature/name` - Clean up

**Repeat for each day/feature!**

---

# ✅ **BENEFITS OF THIS APPROACH**

1. **Simpler** - Only one main branch to worry about
2. **Cleaner** - Easy to understand
3. **Safe** - Feature branches protect main
4. **Professional** - Still using Git properly
5. **Portfolio-Ready** - Shows good Git practices

---

# 🎯 **START BUILDING NOW!**

```bash
# You're already on main
git branch
# Shows: * main

# Just start creating feature branches as needed
git checkout -b feature/database-schema

# Follow BUILD_UMS_STEP_BY_STEP.md
# Whenever it says "develop", use "main"
```

**That's it! Simple and effective!** 🚀

---

**All guides updated to work with main branch only!**

