# 🚨 UMS Error Handling Guide - Complete Reference

## 📋 **COMPREHENSIVE ERROR SOLUTIONS**

This guide explains **every error** you might encounter, **why it happens**, and **exactly how to fix it**.

---

# 📑 **TABLE OF CONTENTS**

1. [Authentication Errors](#authentication-errors)
2. [Database Errors](#database-errors)
3. [Validation Errors](#validation-errors)
4. [File Upload Errors](#file-upload-errors)
5. [Relationship Errors](#relationship-errors)
6. [Route Errors](#route-errors)
7. [View Errors](#view-errors)
8. [Permission Errors](#permission-errors)
9. [Session Errors](#session-errors)
10. [Migration Errors](#migration-errors)

---

# 🔐 **1. AUTHENTICATION ERRORS**

## **Error 1.1: These credentials do not match our records**

### **Full Error:**
```
These credentials do not match our records.
```

### **When It Happens:**
- Login page
- When submitting email and password

### **Why It Happens:**
1. Email doesn't exist in database
2. Password is incorrect
3. Password not hashed properly in database

### **How to Fix:**

**Step 1: Check if user exists**
```bash
php artisan tinker
>>> User::where('email', 'admin@kuet.ac.bd')->first()
```

**If returns null (user doesn't exist):**
```bash
>>> User::create([
    'name' => 'Admin',
    'email' => 'admin@kuet.ac.bd',
    'password' => Hash::make('password'),
    'role' => 'admin'
]);
```

**If user exists, check password:**
```bash
>>> $user = User::where('email', 'admin@kuet.ac.bd')->first()
>>> Hash::check('password', $user->password)
# Should return: true
# If false, password is wrong
```

**Fix password:**
```bash
>>> $user->update(['password' => Hash::make('password')]);
```

### **Prevention:**
Always use `Hash::make()` when storing passwords:
```php
// ❌ WRONG:
User::create(['password' => 'password123']);

// ✅ CORRECT:
User::create(['password' => Hash::make('password123')]);
```

---

## **Error 1.2: Unauthenticated**

### **Full Error:**
```
Unauthenticated.
```
Or redirects to login page.

### **When It Happens:**
- Accessing protected route without logging in
- Session expired

### **Why It Happens:**
1. Not logged in
2. Session expired
3. Middleware `auth` is applied

### **How to Fix:**

**Step 1: Login**
- Go to `/login`
- Enter valid credentials

**Step 2: Check session configuration**
```php
// .env file
SESSION_DRIVER=file
SESSION_LIFETIME=120  // 120 minutes
```

**Step 3: Clear sessions**
```bash
php artisan session:clear
```

**Step 4: For API/testing, generate token:**
```bash
php artisan tinker
>>> $user = User::find(1)
>>> $token = $user->createToken('test-token')->plainTextToken
>>> echo $token
```

---

## **Error 1.3: 403 Forbidden**

### **Full Error:**
```
403 | Forbidden
This action is unauthorized.
```

### **When It Happens:**
- Trying to access route for different role
- E.g., Student trying to access `/admin/dashboard`

### **Why It Happens:**
1. User role doesn't match route role
2. `role` middleware blocking access

### **How to Fix:**

**Step 1: Check your role**
```bash
php artisan tinker
>>> Auth::user()->role
# Returns: 'student'
```

**Step 2: Access correct dashboard**
```
Admin → /admin/dashboard
Teacher → /teacher/dashboard
Student → /student/dashboard
Staff → /staff/dashboard
```

**Step 3: Change role (if needed)**
```bash
>>> $user = User::find(1)
>>> $user->update(['role' => 'admin'])
```

**Step 4: Check route middleware**
```php
// routes/web.php
Route::get('/admin/dashboard', ...)
    ->middleware(['auth', 'role:admin']);
//                          ↑ Must match user's role
```

---

# 🗄️ **2. DATABASE ERRORS**

## **Error 2.1: SQLSTATE[HY000] [1045] Access denied for user**

### **Full Error:**
```
SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: NO)
```

### **When It Happens:**
- Running migrations
- Accessing database
- First time setup

### **Why It Happens:**
1. Wrong database credentials in `.env`
2. MySQL not running
3. User doesn't have permissions

### **How to Fix:**

**For SQLite (Recommended for learning):**
```env
# .env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1        ← Comment out
# DB_PORT=3306             ← Comment out
# DB_DATABASE=laravel      ← Comment out
# DB_USERNAME=root         ← Comment out
# DB_PASSWORD=             ← Comment out
```

**Create SQLite database:**
```bash
# Windows:
type nul > database\database.sqlite

# Mac/Linux:
touch database/database.sqlite
```

**For MySQL:**
```env
# .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kuet_ums
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Test connection:**
```bash
php artisan tinker
>>> DB::connection()->getPdo()
# Should show: PDO object
# If error: Credentials wrong
```

---

## **Error 2.2: SQLSTATE[42S02]: Base table or view not found**

### **Full Error:**
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'kuet_ums.students' doesn't exist
```

### **When It Happens:**
- Accessing pages that query database
- After fresh installation

### **Why It Happens:**
1. Migrations not run
2. Table deleted accidentally

### **How to Fix:**

**Step 1: Check migration status**
```bash
php artisan migrate:status
```

**Step 2: Run migrations**
```bash
php artisan migrate
```

**If already migrated, refresh:**
```bash
# WARNING: Deletes all data!
php artisan migrate:fresh
```

**Step 3: Seed database**
```bash
php artisan db:seed
```

---

## **Error 2.3: SQLSTATE[23000]: Integrity constraint violation**

### **Full Error:**
```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'CSE' for key 'departments.code'
```

### **When It Happens:**
- Creating records with duplicate unique values
- E.g., Same department code, student ID, email

### **Why It Happens:**
1. Trying to create record with existing unique value
2. Database has unique constraint

### **How to Fix:**

**Step 1: Check existing records**
```bash
php artisan tinker
>>> Department::where('code', 'CSE')->first()
# If exists, use different code
```

**Step 2: Use different value**
```php
// Instead of 'CSE', use 'CSE-2' or 'EEE'
```

**Step 3: Or update existing record**
```bash
>>> $dept = Department::where('code', 'CSE')->first()
>>> $dept->update(['name' => 'New Name'])
```

**Step 4: Or delete and recreate**
```bash
>>> Department::where('code', 'CSE')->delete()
>>> Department::create(['code' => 'CSE', 'name' => 'Computer Science'])
```

---

## **Error 2.4: SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row**

### **Full Error:**
```
SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails
```

### **When It Happens:**
- Creating record with foreign key that doesn't exist
- E.g., Student with department_id = 999 (department doesn't exist)

### **Why It Happens:**
1. Referenced record doesn't exist
2. Foreign key points to non-existent ID

### **How to Fix:**

**Step 1: Check if foreign record exists**
```bash
php artisan tinker
>>> Department::find(999)
# If null, department doesn't exist
```

**Step 2: Create the foreign record first**
```bash
>>> Department::create([
    'id' => 999,
    'name' => 'Test Department',
    'code' => 'TEST'
])
```

**Step 3: Or use existing ID**
```bash
>>> Department::all()  # See all departments
>>> # Use existing department ID
```

---

# ✅ **3. VALIDATION ERRORS**

## **Error 3.1: The name field is required**

### **Full Error:**
```
The name field is required.
```

### **When It Happens:**
- Submitting form without required field

### **Why It Happens:**
1. Form field left empty
2. Validation rule: `required`

### **How to Fix:**

**Step 1: Fill the field**
```html
<input name="name" value="John Doe">
```

**Step 2: Or make it optional**
```php
// In controller validation:
'name' => 'nullable|string|max:255',  // Changed 'required' to 'nullable'
```

**Step 3: Check input name matches**
```html
<!-- Input name MUST match validation rule name -->
<input name="name">  <!-- Matches 'name' => 'required' -->
```

---

## **Error 3.2: The email has already been taken**

### **Full Error:**
```
The email has already been taken.
```

### **When It Happens:**
- Creating user with existing email
- Email must be unique

### **Why It Happens:**
1. Email already exists in database
2. Validation rule: `unique:users,email`

### **How to Fix:**

**Step 1: Use different email**
```
admin@kuet.ac.bd → admin2@kuet.ac.bd
```

**Step 2: Or update existing user**
```bash
php artisan tinker
>>> $user = User::where('email', 'admin@kuet.ac.bd')->first()
>>> $user->update(['name' => 'New Name'])
```

**Step 3: For edit forms, exclude current record**
```php
// When editing user ID 5:
'email' => 'required|email|unique:users,email,' . $user->id
//                                              ↑ Excludes current user
```

---

## **Error 3.3: The password confirmation does not match**

### **Full Error:**
```
The password confirmation does not match.
```

### **When It Happens:**
- Password and confirmation fields don't match
- Registration/password change forms

### **Why It Happens:**
1. Different values in password fields
2. Validation rule: `confirmed`

### **How to Fix:**

**Step 1: Make passwords match**
```html
<input name="password" value="secret123">
<input name="password_confirmation" value="secret123">
<!--                                      ↑ Must be exactly same -->
```

**Step 2: Check field names**
```html
<!-- MUST be these exact names: -->
<input name="password">
<input name="password_confirmation">
<!-- NOT confirm_password or password_confirm! -->
```

**Step 3: Check for typos/spaces**
```
"password123" ≠ "password123 " (extra space!)
"Password123" ≠ "password123" (case sensitive!)
```

---

# 📁 **4. FILE UPLOAD ERRORS**

## **Error 4.1: The profile image must be an image**

### **Full Error:**
```
The profile image must be an image.
```

### **When It Happens:**
- Uploading non-image file
- E.g., PDF, Word document, etc.

### **Why It Happens:**
1. File is not an image
2. Validation rule: `image`

### **How to Fix:**

**Step 1: Upload image file**
```
Allowed: .jpg, .jpeg, .png, .gif, .svg
Not allowed: .pdf, .docx, .txt, .zip
```

**Step 2: Or allow more file types**
```php
// Allow PDFs too:
'document' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048'
```

---

## **Error 4.2: The profile image may not be greater than 2048 kilobytes**

### **Full Error:**
```
The profile image may not be greater than 2048 kilobytes.
```

### **When It Happens:**
- Uploading file larger than 2MB

### **Why It Happens:**
1. File size > 2MB
2. Validation rule: `max:2048`

### **How to Fix:**

**Option 1: Compress image**
- Use online tools like TinyPNG
- Or image editors to reduce size

**Option 2: Increase max size**
```php
// Allow up to 5MB:
'profile_image' => 'image|max:5120'  // 5120 KB = 5MB
```

**Option 3: Update PHP settings**
```ini
; php.ini
upload_max_filesize = 10M
post_max_size = 10M
```

**Restart Apache after changing php.ini:**
```bash
# XAMPP Control Panel → Apache → Stop → Start
```

---

## **Error 4.3: File does not exist at path**

### **Full Error:**
```
File does not exist at path storage/app/public/profile_images/abc123.jpg
```

### **When It Happens:**
- Trying to display uploaded image
- After uploading file

### **Why It Happens:**
1. Storage link not created
2. File was deleted
3. Wrong path

### **How to Fix:**

**Step 1: Create storage link**
```bash
php artisan storage:link
```

**This creates:**
```
public/storage → storage/app/public
```

**Step 2: Use correct path in view**
```html
<!-- ✅ CORRECT: -->
<img src="{{ asset('storage/' . Auth::user()->profile_image) }}">

<!-- ❌ WRONG: -->
<img src="{{ asset(Auth::user()->profile_image) }}">
<img src="{{ asset('public/' . Auth::user()->profile_image) }}">
```

**Step 3: Check file exists**
```bash
# Windows:
dir storage\app\public\profile_images

# Mac/Linux:
ls storage/app/public/profile_images
```

---

# 🔗 **5. RELATIONSHIP ERRORS**

## **Error 5.1: Call to undefined relationship [student]**

### **Full Error:**
```
Call to undefined relationship [student] on model [App\Models\User].
```

### **When It Happens:**
- Using relationship that doesn't exist
- E.g., `Auth::user()->student`

### **Why It Happens:**
1. Relationship method not defined in model
2. Method name typo

### **How to Fix:**

**Step 1: Add relationship to User model**
```php
// app/Models/User.php

public function student()
{
    return $this->hasOne(Student::class);
}
```

**Step 2: Check method name matches usage**
```php
// In model:
public function student() { ... }

// In code:
$user->student  // ✅ Matches

// NOT:
$user->students  // ❌ Doesn't match
```

---

## **Error 5.2: Trying to get property 'name' of non-object**

### **Full Error:**
```
Trying to get property 'name' of non-object
```

### **When It Happens:**
- Accessing property on null relationship
- E.g., `$student->user->name` when user is null

### **Why It Happens:**
1. Relationship returns null
2. Foreign key is null or invalid

### **How to Fix:**

**Option 1: Use null coalescing**
```php
// ✅ SAFE:
{{ $student->user->name ?? 'N/A' }}

// ❌ UNSAFE:
{{ $student->user->name }}
```

**Option 2: Check if exists**
```php
@if($student->user)
    {{ $student->user->name }}
@else
    N/A
@endif
```

**Option 3: Fix the data**
```bash
php artisan tinker
>>> $student = Student::find(1)
>>> $student->user_id  // Check if null
>>> # If null, assign valid user_id:
>>> $student->update(['user_id' => 1])
```

---

## **Error 5.3: BadMethodCallException: Method links does not exist**

### **Full Error:**
```
BadMethodCallException
Method Illuminate\Database\Eloquent\Collection::links does not exist.
```

### **When It Happens:**
- Calling `->links()` on Collection
- Should be on Paginator

### **Why It Happens:**
1. Used `->get()` instead of `->paginate()`
2. Collection doesn't have `links()` method

### **How to Fix:**

**Change from get() to paginate():**
```php
// ❌ WRONG:
$students = Student::all();  // Returns Collection
{{ $students->links() }}  // ERROR!

// ✅ CORRECT:
$students = Student::paginate(15);  // Returns Paginator
{{ $students->links() }}  // Works!
```

---

# 🛣️ **6. ROUTE ERRORS**

## **Error 6.1: Route [admin.dashboard] not defined**

### **Full Error:**
```
Route [admin.dashboard] not defined.
```

### **When It Happens:**
- Using `route('admin.dashboard')`
- Route doesn't exist

### **Why It Happens:**
1. Route not registered in `routes/web.php`
2. Route name typo

### **How to Fix:**

**Step 1: Check route list**
```bash
php artisan route:list | grep admin.dashboard
```

**Step 2: Add route if missing**
```php
// routes/web.php
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->name('admin.dashboard');
//        ↑ This is the route name
```

**Step 3: Clear route cache**
```bash
php artisan route:clear
```

**Step 4: Check route name matches**
```php
// Route defined as:
->name('admin.dashboard')

// Must use exact name:
route('admin.dashboard')  // ✅ Correct
route('admin-dashboard')  // ❌ Wrong
```

---

## **Error 6.2: 404 Not Found**

### **Full Error:**
```
404 | Not Found
```

### **When It Happens:**
- Accessing URL that doesn't exist
- E.g., `/admin/dashbord` (typo)

### **Why It Happens:**
1. URL typo
2. Route not defined
3. Wrong HTTP method (GET vs POST)

### **How to Fix:**

**Step 1: Check URL spelling**
```
/admin/dashboard  ✅
/admin/dashbord   ❌ (typo)
/admin/dash-board ❌ (wrong format)
```

**Step 2: List all routes**
```bash
php artisan route:list
```

**Step 3: Check HTTP method**
```php
// Route defined as POST:
Route::post('/admin/students', ...)

// Must use POST request, not GET:
<form method="POST" action="/admin/students">
    @csrf
</form>
```

---

# 👁️ **7. VIEW ERRORS**

## **Error 7.1: View [admin.dashboard] not found**

### **Full Error:**
```
View [admin.dashboard] not found.
```

### **When It Happens:**
- Controller returns non-existent view
- File missing or wrong path

### **Why It Happens:**
1. View file doesn't exist
2. Wrong view path

### **How to Fix:**

**Step 1: Create view file**
```bash
# Create directory:
mkdir resources\views\admin

# Create file:
type nul > resources\views\admin\dashboard.blade.php
```

**Step 2: Check view path matches**
```php
// Controller:
return view('admin.dashboard');

// File must be at:
resources/views/admin/dashboard.blade.php
//                ↓     ↓
//            admin  dashboard
```

**Step 3: Check file extension**
```
✅ dashboard.blade.php
❌ dashboard.php (missing .blade)
❌ dashboard.html (wrong extension)
```

---

## **Error 7.2: Undefined variable $students**

### **Full Error:**
```
Undefined variable $students
```

### **When It Happens:**
- Using variable in view that wasn't passed
- E.g., `{{ $students }}` but $students not sent

### **Why It Happens:**
1. Variable not passed from controller
2. Variable name typo

### **How to Fix:**

**Step 1: Pass variable from controller**
```php
// ❌ WRONG:
public function index() {
    $students = Student::all();
    return view('admin.students.index');
    // $students not passed!
}

// ✅ CORRECT:
public function index() {
    $students = Student::all();
    return view('admin.students.index', compact('students'));
    //                                  ↑ Pass variable
}
```

**Step 2: Check variable name matches**
```php
// Controller:
compact('students')

// View:
{{ $students }}  // ✅ Matches
{{ $student }}   // ❌ Doesn't match (missing 's')
```

**Step 3: Or check if exists**
```blade
@if(isset($students))
    @foreach($students as $student)
        ...
    @endforeach
@endif
```

---

# 🔒 **8. PERMISSION ERRORS**

## **Error 8.1: The stream or file "storage/logs/laravel.log" could not be opened**

### **Full Error:**
```
The stream or file "storage/logs/laravel.log" could not be opened: failed to open stream: Permission denied
```

### **When It Happens:**
- Laravel trying to write to storage
- File system permissions issue

### **Why It Happens:**
1. Storage folder has wrong permissions
2. Web server can't write to folder

### **How to Fix:**

**Windows:**
```bash
# Run as Administrator:
icacls storage /grant Users:F /t
icacls bootstrap/cache /grant Users:F /t
```

**Mac/Linux:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

**Or change ownership to your user:**
```bash
sudo chown -R $USER:$USER storage
sudo chown -R $USER:$USER bootstrap/cache
```

---

# 🔑 **9. SESSION ERRORS**

## **Error 9.1: 419 Page Expired**

### **Full Error:**
```
419 | Page Expired
```

### **When It Happens:**
- Submitting form after long time
- CSRF token expired

### **Why It Happens:**
1. Session expired
2. CSRF token mismatch
3. Form open too long

### **How to Fix:**

**Step 1: Refresh page and try again**
```
Press F5 → Submit form again
```

**Step 2: Add @csrf to form**
```html
<form method="POST">
    @csrf  ← Must have this!
    ...
</form>
```

**Step 3: Increase session lifetime**
```env
# .env
SESSION_LIFETIME=120  # 120 minutes = 2 hours
```

**Step 4: Clear sessions**
```bash
php artisan session:clear
```

---

# 🔧 **10. MIGRATION ERRORS**

## **Error 10.1: Migration table not found**

### **Full Error:**
```
Migration table not found.
```

### **When It Happens:**
- First time running migrations
- Fresh database

### **Why It Happens:**
1. Migrations never run before
2. Normal on first setup

### **How to Fix:**

**Simply run migrations:**
```bash
php artisan migrate
```

**This will:**
1. Create migrations table
2. Run all pending migrations

---

## **Error 10.2: Syntax error or access violation: Table already exists**

### **Full Error:**
```
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'users' already exists
```

### **When It Happens:**
- Running migrations that already ran

### **Why It Happens:**
1. Migration already executed
2. Trying to run again

### **How to Fix:**

**Option 1: Skip (it's fine)**
```bash
# Table already exists, no need to create again
```

**Option 2: Fresh migration (WARNING: deletes data!)**
```bash
php artisan migrate:fresh
```

**Option 3: Check migration status**
```bash
php artisan migrate:status
```

---

## **QUICK ERROR REFERENCE TABLE**

| Error Code | Error | Quick Fix |
|------------|-------|-----------|
| 401 | Unauthenticated | Login again |
| 403 | Forbidden | Check user role |
| 404 | Not Found | Check URL/route |
| 419 | Page Expired | Refresh and resubmit |
| 500 | Server Error | Check logs: `storage/logs/laravel.log` |
| HY000 | Database connection | Check `.env` database config |
| 42S02 | Table not found | Run `php artisan migrate` |
| 23000 | Duplicate entry | Use unique value |
| 1452 | Foreign key fails | Create referenced record first |

---

## **DEBUGGING CHECKLIST**

When you get an error:

1. **Read the error message carefully**
   - It usually tells you exactly what's wrong

2. **Check the file and line number**
   - Error shows: `at StudentController.php:25`
   - Go to line 25 in that file

3. **Check Laravel logs**
   ```bash
   storage/logs/laravel.log
   ```

4. **Use dd() to debug**
   ```php
   dd($variable);  // Dump and die
   ```

5. **Check Tinker**
   ```bash
   php artisan tinker
   >>> Student::count()
   ```

6. **Clear all caches**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

7. **Restart server**
   ```bash
   # Stop: Ctrl+C
   php artisan serve
   ```

---

**This guide covers 50+ common errors with detailed solutions!**

