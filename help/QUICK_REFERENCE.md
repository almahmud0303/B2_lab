# ⚡ UMS Quick Reference Card

## 🎯 **ONE-PAGE GUIDE TO GET STARTED**

---

# 📚 **WHICH GUIDE DO I USE?**

| I Want To... | Use This Guide |
|-------------|----------------|
| **Start building the system** | `BUILD_UMS_STEP_BY_STEP.md` |
| **Learn Git workflow** | `GIT_WORKFLOW_MAIN_BRANCH.md` |
| **Fix an error** | `help/ERROR_HANDLING_GUIDE.md` |
| **Understand a function** | `help/FUNCTIONS_EXPLAINED.md` |
| **Prepare for interview** | `help/INTERVIEW_QA.md` |
| **See big picture** | `COMPLETE_UMS_GUIDE.md` |
| **Navigate guides** | `README_GUIDES.md` |

---

# 🚀 **GET STARTED IN 3 STEPS**

## **Step 1: Setup (5 minutes)**

```bash
cd C:\xampp\htdocs\myapp3

# Check you're on main
git branch
# Shows: * main

# Verify Git is working
git status
```

## **Step 2: Open Guide (1 minute)**

Open: `BUILD_UMS_STEP_BY_STEP.md`

Go to: **Day 1, Step 1**

## **Step 3: Start Building! (Rest of time)**

Follow step-by-step, copy code, test, commit!

---

# 🌳 **SIMPLE GIT WORKFLOW**

## **For Each Feature:**

```bash
# 1. Start from main
git checkout main
git pull origin main

# 2. Create feature branch
git checkout -b feature/database-schema

# 3. Work and commit
git add .
git commit -m "feat: create migrations"

# 4. Push to GitHub
git push -u origin feature/database-schema

# 5. Merge to main
git checkout main
git merge feature/database-schema
git push origin main

# 6. Clean up (optional)
git branch -d feature/database-schema
```

---

# 📅 **20-DAY TIMELINE**

## **Week 1 (Days 1-9):**
- **Guide:** `BUILD_UMS_STEP_BY_STEP.md`
- **Build:** Setup → Admin → Student
- **Milestone:** Can login and manage users

## **Week 2 (Days 10-15):**
- **Guide:** `DAYS_10_12_TEACHER_MODULE.md` then `DAYS_13_15_STAFF_DEPTHEAD.md`
- **Build:** Teacher → Staff → Dept Head
- **Milestone:** All 5 roles working

## **Week 3 (Days 16-20):**
- **Guide:** `DAYS_16_20_FINAL.md`
- **Build:** Features → Testing → Deploy
- **Milestone:** Production-ready!

---

# 🔧 **COMMON COMMANDS**

## **Laravel:**
```bash
php artisan serve                 # Start server
php artisan migrate              # Run migrations
php artisan migrate:fresh --seed # Fresh DB with data
php artisan db:seed              # Seed data
php artisan storage:link         # Link storage
php artisan tinker               # Database console
php artisan cache:clear          # Clear cache
php artisan route:list           # See all routes
```

## **Git:**
```bash
git status                       # Check changes
git add .                        # Stage all files
git commit -m "message"          # Commit
git push origin main             # Push to GitHub
git pull origin main             # Pull from GitHub
git branch                       # List branches
git checkout -b feature/name     # Create branch
git merge feature/name           # Merge branch
```

---

# 🐛 **QUICK FIXES**

## **Error: "route not defined"**
```bash
php artisan route:clear
php artisan route:cache
```

## **Error: "Class not found"**
```bash
composer dump-autoload
```

## **Error: "Storage link not found"**
```bash
php artisan storage:link
```

## **Error: "Cannot login"**
```bash
php artisan tinker
>>> $user = User::where('email', 'admin@kuet.ac.bd')->first()
>>> $user->update(['password' => Hash::make('password')])
>>> exit
```

## **Error: "Migration failed"**
```bash
php artisan migrate:fresh
```

---

# 📖 **FILE LOCATIONS**

## **Main Guides (Root Folder):**
- `START_HERE.md` ⭐
- `BUILD_UMS_STEP_BY_STEP.md` (Days 1-9)
- `DAYS_10_12_TEACHER_MODULE.md` (Days 10-12)
- `DAYS_13_15_STAFF_DEPTHEAD.md` (Days 13-15)
- `DAYS_16_20_FINAL.md` (Days 16-20)
- `GIT_WORKFLOW_MAIN_BRANCH.md`
- `README.md`

## **Reference Guides (help/ folder):**
- `FUNCTIONS_EXPLAINED.md`
- `ERROR_HANDLING_GUIDE.md`
- `INTERVIEW_QA.md`
- `UMS_DEVELOPMENT_GUIDE.md`
- `GIT_BRANCHING_STRATEGY.md`

---

# ✅ **TODAY'S CHECKLIST**

- [ ] Read START_HERE.md (5 mins)
- [ ] Read GIT_WORKFLOW_MAIN_BRANCH.md (5 mins)
- [ ] Verify Git working: `git status`
- [ ] Open BUILD_UMS_STEP_BY_STEP.md
- [ ] Start Day 1, Step 1
- [ ] Complete Days 1-2 if possible

---

# 🎯 **SUCCESS INDICATORS**

**You're on track when:**
- ✅ Git commands work
- ✅ Laravel server runs
- ✅ Following guides step-by-step
- ✅ Committing regularly
- ✅ Each feature works before moving on

---

# 📞 **GET HELP**

| Problem | Solution |
|---------|----------|
| Git issue | `GIT_WORKFLOW_MAIN_BRANCH.md` |
| Error message | `help/ERROR_HANDLING_GUIDE.md` |
| Don't understand code | `help/FUNCTIONS_EXPLAINED.md` |
| Need big picture | `COMPLETE_UMS_GUIDE.md` |

---

# 🎉 **YOU'RE READY!**

**Next Action:**
```bash
# Open this file:
BUILD_UMS_STEP_BY_STEP.md

# Start here:
Day 1, Step 1

# Let's build!
```

---

**Keep this file open for quick reference!** 📌

**All guides use MAIN branch - simple and effective!** 🚀

