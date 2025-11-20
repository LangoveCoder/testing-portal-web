# 🧪 Admission Portal - Complete Testing Guide

**Last Updated:** November 17, 2025  
**Purpose:** Step-by-step guide to test the entire system from scratch

---

## 🎯 TESTING OVERVIEW

### Prerequisites
- XAMPP running (Apache + MySQL)
- Laravel server running: `php artisan serve`
- Fresh database (all tables empty except super_admins)
- No student photos in storage

### Test Duration
- **Quick Test:** 30 minutes (basic flow)
- **Complete Test:** 2 hours (all features)
- **Stress Test:** 4 hours (bulk operations)

### Test Accounts
- **Super Admin:** admin / admin123
- **College Admin:** (create during testing)

---

## 📋 PHASE 1: DATABASE CLEANUP (5 minutes)

### Step 1: Clean Database

**Option A: Using phpMyAdmin**

1. Open: http://localhost/phpmyadmin
2. Select database: `admission_portal`
3. Run this SQL:
```sql
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE results;
TRUNCATE TABLE students;
TRUNCATE TABLE test_venues;
TRUNCATE TABLE tests;
TRUNCATE TABLE test_districts;
TRUNCATE TABLE colleges;
TRUNCATE TABLE audit_logs;

SET FOREIGN_KEY_CHECKS = 1;
```

4. Verify super_admins still has admin:
```sql
SELECT * FROM super_admins;
-- Should show: id=1, username=admin
```

**Option B: Using Artisan**
```bash
php artisan db:seed --class=DatabaseSeeder
# Or manually truncate tables
```

### Step 2: Clean Storage

1. Navigate to: `C:\xampp\htdocs\admission-portal\storage\app\public\student-pictures\`
2. Delete all files inside
3. Navigate to: `C:\xampp\htdocs\admission-portal\storage\app\temp\`
4. Delete all files inside

### Step 3: Verify Clean State

**Check Database:**
```sql
SELECT COUNT(*) FROM colleges; -- Should be 0
SELECT COUNT(*) FROM tests; -- Should be 0
SELECT COUNT(*) FROM students; -- Should be 0
SELECT COUNT(*) FROM results; -- Should be 0
```

**Check Files:**
- `storage/app/public/student-pictures/` should be empty
- `storage/app/temp/` should be empty

✅ **Checkpoint:** Database clean, files deleted, super admin exists

---

## 📋 PHASE 2: SUPER ADMIN - SYSTEM SETUP (30 minutes)

### Test 2.1: Super Admin Login

**Steps:**
1. Open browser: http://127.0.0.1:8000
2. Click "Super Admin Login" or go to: http://127.0.0.1:8000/super-admin/login
3. Enter credentials:
   - Username: `admin`
   - Password: `admin123`
4. Click "Login"

**Expected Results:**
- ✅ Redirects to Super Admin Dashboard
- ✅ Shows "Welcome, Super Administrator"
- ✅ Statistics show all zeros:
  - Total Colleges: 0
  - Total Tests: 0
  - Total Students: 0
  - Roll Numbers Generated: 0
- ✅ All action cards visible

**If Failed:**
- Check database for super_admins table
- Verify credentials
- Check routes/web.php for super-admin routes
- Check Laravel logs: `storage/logs/laravel.log`

✅ **Checkpoint:** Super Admin logged in successfully

---

### Test 2.2: Create College

**Steps:**
1. From dashboard, click "Go to Colleges"
2. Click "Register New College" button
3. Fill the form:
```
Basic Information:
- College Name: Test College Quetta
- College Code: TCQ
- Contact Person: Muhammad Ali Khan
- Email: testcollege@example.com
- Phone: 03001234567
- Address: Main University Road, Quetta
- City: Quetta
- Province: Balochistan

Policies:
- Gender Policy: Both
- Minimum Age: 16
- Maximum Age: 25
- Registration Start Date: [Select today's date]

Status:
- Is Active: ✓ (checked)
```

4. Add Test Districts:
   - Click "Add Test District"
   - **District 1:**
     - Province: Balochistan
     - Division: Quetta Division
     - District: Quetta
   - Click "Add Another District"
   - **District 2:**
     - Province: Balochistan
     - Division: Quetta Division
     - District: Pishin

5. Click "Register College"

**Expected Results:**
- ✅ Success message: "College registered successfully!"
- ✅ Redirects to colleges list
- ✅ College "Test College Quetta (TCQ)" appears in list
- ✅ Shows 2 test districts
- ✅ Status shows "Active"

**Verify in Database:**
```sql
SELECT * FROM colleges WHERE code = 'TCQ';
SELECT * FROM test_districts WHERE college_id = [college_id];
```

✅ **Checkpoint:** College created with 2 test districts

---

### Test 2.3: Create College Admin Account

**Steps:**
1. From colleges list, click "View" on "Test College Quetta"
2. In college details page, find "College Admin Account" section
3. Click "Create College Admin Account"
4. Fill modal:
   - Email: `admin@testcollege.com`
   - Password: `college123`
   - Confirm Password: `college123`
5. Click "Create Account"

**Expected Results:**
- ✅ Success message: "College admin account created successfully!"
- ✅ Email field now shows: admin@testcollege.com
- ✅ "Create College Admin Account" button disabled or changes to "Update"

**Verify in Database:**
```sql
SELECT * FROM colleges WHERE email = 'admin@testcollege.com';
-- password column should be hashed (bcrypt)
```

✅ **Checkpoint:** College admin account created

---

### Test 2.4: Create Test with Venues

**Steps:**
1. Go back to Dashboard
2. Click "Go to Tests"
3. Click "Create New Test"
4. Fill Test Information:
```
Basic Information:
- Select College: Test College Quetta (TCQ)
- Test Date: [Select a date 1 week from today]
- Test Time: 10:00 AM
- Test Mode: Mode 1 (MCQ + Subjective)
- Total Marks: 300
- Registration Deadline: [Select 2 days from today]
- Starting Roll Number: 00001
```

5. Add Venue 1:
   - Click "Add Venue" if not already visible
   - Test District: Quetta
   - Venue Name: Government College Quetta
   - Venue Address: Jinnah Road, Quetta City
   - Number of Halls: 2
   - Zones per Hall: 2
   - Rows per Zone: 5
   - Seats per Row: 10
   - **Total Capacity:** (auto-calculated) 200

6. Click "Add Another Venue"

7. Add Venue 2:
   - Test District: Pishin
   - Venue Name: Government High School Pishin
   - Venue Address: Main Bazaar Road, Pishin
   - Number of Halls: 1
   - Zones per Hall: 2
   - Rows per Zone: 5
   - Seats per Row: 10
   - **Total Capacity:** (auto-calculated) 100

8. Review total capacity: Should show 300 (200 + 100)

9. Click "Create Test"

**Expected Results:**
- ✅ Success message: "Test created successfully!"
- ✅ Redirects to test details page
- ✅ Test information displayed correctly
- ✅ 2 venues listed with correct details
- ✅ Capacity calculations correct

**Verify in Database:**
```sql
SELECT * FROM tests WHERE college_id = [college_id];
SELECT * FROM test_venues WHERE test_id = [test_id];
```

✅ **Checkpoint:** Test created with 2 venues, total capacity 300

---

## 📋 PHASE 3: COLLEGE ADMIN - STUDENT REGISTRATION (30 minutes)

### Test 3.1: College Admin Login

**Steps:**
1. **Logout from Super Admin:**
   - Click "Logout" button in top right
2. Go to: http://127.0.0.1:8000/college/login
3. Enter credentials:
   - Email: `admin@testcollege.com`
   - Password: `college123`
4. Click "Login"

**Expected Results:**
- ✅ Redirects to College Admin Dashboard
- ✅ Shows college name in navigation
- ✅ Statistics show all zeros
- ✅ Action cards visible

✅ **Checkpoint:** College Admin logged in

---

### Test 3.2: Register Student 1

**Steps:**
1. Click "Register New Student"
2. Fill form:
```
Personal Information:
- Student Name: Ali Ahmed
- Father Name: Ahmed Khan
- Student CNIC: 4210112345671
- Father CNIC: 4210198765431

Demographics:
- Gender: Male (dropdown)
- Religion: Islam (dropdown)
- Date of Birth: 15/01/2005

Address Information:
- Province: Balochistan (dropdown)
- Division: Quetta Division (dropdown)
- District: Quetta (dropdown)
- Complete Address: House 123, Street 5, Satellite Town, Quetta

Test Information:
- Select Test: [Select the test you created]
- Test District: Quetta (dropdown - should load via AJAX)

Photo Upload:
- Upload: [Select any small JPG/PNG image]
```

3. Preview image should appear
4. Click "Register Student"

**Expected Results:**
- ✅ Success message: "Student registered successfully!"
- ✅ Redirects to students list
- ✅ Student "Ali Ahmed" appears
- ✅ Registration ID generated (e.g., REG1731234567890)
- ✅ Roll Number shows "Not Generated"

**Verify:**
- Photo uploaded to: `storage/app/public/student-pictures/`
- Database record created in students table

✅ **Checkpoint:** First student registered

---

### Test 3.3: Register Student 2

**Repeat Test 3.2 with:**
```
- Student Name: Fatima Noor
- Father Name: Noor Muhammad
- Student CNIC: 4210112345672
- Father CNIC: 4210198765432
- Gender: Female
- Religion: Islam
- Date of Birth: 20/03/2005
- Province: Balochistan
- Division: Quetta Division
- District: Quetta
- Complete Address: House 456, Street 10, Brewery Road, Quetta
- Test District: Quetta
- Photo: [Upload image]
```

✅ **Checkpoint:** Second student registered

---

### Test 3.4: Register Student 3

**Repeat Test 3.2 with:**
```
- Student Name: Hassan Ali
- Father Name: Ali Raza
- Student CNIC: 4210112345673
- Father CNIC: 4210198765433
- Gender: Male
- Religion: Islam
- Date of Birth: 10/07/2005
- Province: Balochistan
- Division: Quetta Division
- District: Pishin
- Complete Address: House 789, Main Road, Pishin City
- Test District: Pishin
- Photo: [Upload image]
```

✅ **Checkpoint:** Third student registered (different test district)

---

### Test 3.5: View Students

**Steps:**
1. Click "View All Students" from dashboard
2. Verify list shows 3 students
3. Click "View" on "Ali Ahmed"
4. Verify all details correct
5. Check photo displays
6. Note Registration ID

**Expected Results:**
- ✅ All 3 students listed
- ✅ Student details page shows complete information
- ✅ Photo displays correctly
- ✅ Roll Number shows "Not Generated Yet"

✅ **Checkpoint:** Can view students list and details

---

### Test 3.6: Download Bulk Upload Template

**Steps:**
1. Go back to College Dashboard
2. Scroll to "Download Bulk Template" card
3. Select the test from dropdown
4. Click "Download Template"

**Expected Results:**
- ✅ ZIP file downloads
- ✅ ZIP contains:
  - students.xlsx
  - pictures/ folder (empty)
  - INSTRUCTIONS.txt

5. Extract ZIP and open students.xlsx
6. Check dropdowns work:
   - Click cell E2 (Gender) - should show dropdown arrow
   - Click cell F2 (Religion) - should show dropdown arrow
   - Click cell H2 (Province) - should show dropdown arrow
   - Click cell I2 (Division) - should show dropdown arrow
   - Click cell J2 (District) - should show dropdown arrow
   - Click cell L2 (Test District) - should show Quetta, Pishin

**Expected Results:**
- ✅ All dropdowns work
- ✅ Test districts match college's districts
- ✅ Sample data in row 2
- ✅ Instructions sheet present

✅ **Checkpoint:** Template downloaded with working dropdowns

---

## 📋 PHASE 4: BULK UPLOAD (30 minutes)

### Test 4.1: Prepare Bulk Upload Data

**Steps:**
1. Use the downloaded template or create new Excel file
2. Fill with 2 new students:

**Student 4:**
```
Row 3:
Name: Sara Khan
Father Name: Khan Bahadur
Student CNIC: 4210112345674
Father CNIC: 4210198765434
Gender: Female (use dropdown)
Religion: Islam (use dropdown)
Date of Birth: 25/05/2005
Province: Balochistan (use dropdown)
Division: Quetta Division (use dropdown)
District: Quetta (use dropdown)
Complete Address: House 111, Street 3, Cantt Area, Quetta
Test District: Quetta (use dropdown)
Picture Filename: 4210112345674.jpg
```

**Student 5:**
```
Row 4:
Name: Usman Ahmed
Father Name: Ahmed Hassan
Student CNIC: 4210112345675
Father CNIC: 4210198765435
Gender: Male (use dropdown)
Religion: Islam (use dropdown)
Date of Birth: 30/08/2005
Province: Balochistan (use dropdown)
Division: Quetta Division (use dropdown)
District: Pishin (use dropdown)
Complete Address: House 222, Main Road, Pishin
Test District: Pishin (use dropdown)
Picture Filename: 4210112345675.jpg
```

3. Save as: `students.xlsx`

4. Prepare photos:
   - Get 2 small image files
   - Rename to: `4210112345674.jpg` and `4210112345675.jpg`
   - Put in `pictures/` folder

5. Create ZIP:
   - Select `students.xlsx` and `pictures/` folder
   - Right-click → Send to → Compressed (zipped) folder
   - Name: `bulk_upload.zip`

✅ **Checkpoint:** Bulk upload ZIP file ready

---

### Test 4.2: Upload and Validate

**Steps:**
1. **Logout from College Admin**
2. **Login as Super Admin** (admin/admin123)
3. Go to "Bulk Upload"
4. Select College: Test College Quetta
5. Select Test: [The test you created]
6. Upload `bulk_upload.zip`
7. Click "Upload & Validate"

**Expected Results:**
- ✅ Processing message appears
- ✅ Redirects to preview page
- ✅ Statistics cards show:
  - Valid Students: 2
  - Invalid Students: 0
  - College: Test College Quetta
- ✅ Valid students table shows 2 students:
  - Sara Khan, 4210112345674
  - Usman Ahmed, 4210112345675
- ✅ All details correct
- ✅ "Import Valid Students" button enabled

**If Errors Appear:**
- Check error table for specific issues
- Common errors:
  - Picture not found (check filename matches CNIC exactly)
  - CNIC format (must be 13 digits)
  - Age validation (check DOB is within college's age policy)
  - Duplicate CNIC (check against existing students)

✅ **Checkpoint:** Validation passed, preview shows 2 valid students

---

### Test 4.3: Import Students

**Steps:**
1. Review preview page one more time
2. Click "Import 2 Valid Students"
3. Confirm in dialog

**Expected Results:**
- ✅ Success message: "Successfully imported 2 students for Test College Quetta!"
- ✅ Redirects back to bulk upload page
- ✅ Session data cleared

**Verify Import:**
1. Go to "Manage Students"
2. Filter by college: Test College Quetta
3. Should now see 5 students total:
   - Ali Ahmed (manual)
   - Fatima Noor (manual)
   - Hassan Ali (manual)
   - Sara Khan (bulk)
   - Usman Ahmed (bulk)

4. View "Sara Khan":
   - ✅ Photo uploaded correctly
   - ✅ All details correct
   - ✅ Registration ID generated

5. View "Usman Ahmed":
   - ✅ Photo uploaded correctly
   - ✅ All details correct
   - ✅ Registration ID generated

**Verify in Database:**
```sql
SELECT COUNT(*) FROM students; -- Should be 5
SELECT * FROM students ORDER BY created_at DESC LIMIT 2; -- Should show Sara and Usman
```

**Verify Photos:**
- Check: `storage/app/public/student-pictures/`
- Should have 5 photos total

✅ **Checkpoint:** Bulk upload successful, 5 students total

---

## 📋 PHASE 5: ROLL NUMBER GENERATION (15 minutes)

### Test 5.1: Preview Roll Number Assignment

**Steps:**
1. From Super Admin Dashboard, click "Generate Roll Numbers"
2. Select the test
3. Click "Preview Assignment"

**Expected Results:**
- ✅ Preview page shows all 5 students
- ✅ Students sorted by:
  1. Test District (Pishin, Quetta)
  2. CNIC (ascending within district)
  
**Expected Order:**
```
Test District: Pishin (alphabetically first)
1. Hassan Ali (4210112345673) - Roll 00001, Yellow, Venue: Pishin, H1-Z1-R1-S1
2. Usman Ahmed (4210112345675) - Roll 00002, Green, Venue: Pishin, H1-Z1-R1-S2

Test District: Quetta
3. Ali Ahmed (4210112345671) - Roll 00003, Blue, Venue: Quetta, H1-Z1-R1-S1
4. Fatima Noor (4210112345672) - Roll 00004, Pink, Venue: Quetta, H1-Z1-R1-S2
5. Sara Khan (4210112345674) - Roll 00005, Yellow, Venue: Quetta, H1-Z1-R1-S3
```

**Verify:**
- ✅ Roll numbers sequential: 00001, 00002, 00003, 00004, 00005
- ✅ Book colors cycle: Yellow, Green, Blue, Pink, Yellow
- ✅ Seating sequential within venue
- ✅ Venue assignment matches test district

✅ **Checkpoint:** Preview shows correct roll number assignments

---

### Test 5.2: Generate Roll Numbers

**Steps:**
1. Review preview one more time
2. Click "Confirm and Generate Roll Numbers"
3. Confirm in dialog

**Expected Results:**
- ✅ Success message: "Roll numbers generated successfully!"
- ✅ Shows generation date/time
- ✅ "Generated" badge visible

**Verify Generation:**
1. Go to "Manage Students"
2. All 5 students should now have:
   - ✅ Roll Number (5-digit)
   - ✅ Book Color
   - ✅ Hall, Zone, Row, Seat numbers

3. View any student:
   - ✅ Roll Number displays
   - ✅ Book Color shows
   - ✅ Seating info complete
   - ✅ Venue name shows

**Verify in Database:**
```sql
SELECT 
    name, 
    roll_number, 
    book_color, 
    hall_number, 
    zone_number, 
    row_number, 
    seat_number 
FROM students 
ORDER BY roll_number;
```

**Try to Edit Student:**
1. Click "Edit" on any student
2. Try to change test district
3. ✅ Only test district should be editable
4. ✅ Other fields disabled with message

**Try to Delete Student:**
1. Click "Delete" on any student
2. ✅ Should show error: "Cannot delete student with roll number"

**Try to Regenerate:**
1. Go back to "Generate Roll Numbers"
2. Select same test
3. ✅ Should show "Regenerate" option
4. ✅ Warning message about existing roll numbers

✅ **Checkpoint:** Roll numbers generated successfully, edit/delete restrictions work

---

## 📋 PHASE 6: RESULT MANAGEMENT (20 minutes)

### Test 6.1: Prepare Results Excel

**Create Excel file with Mode 1 format:**

Headers (Row 1):
```
Roll Number | Book Color | English(Obj) | Urdu(Obj) | Math(Obj) | Science(Obj) | English(Subj) | Urdu(Subj) | Math(Subj) | Science(Subj) | Total
```

Data (Rows 2-6):
```
00001 | Yellow | 30 | 35 | 28 | 32 | 40 | 38 | 35 | 37 | 275
00002 | Green  | 35 | 38 | 30 | 35 | 42 | 40 | 38 | 40 | 298
00003 | Blue   | 28 | 30 | 25 | 28 | 35 | 33 | 30 | 32 | 241
00004 | Pink   | 32 | 35 | 30 | 33 | 38 | 37 | 36 | 38 | 279
00005 | Yellow | 38 | 40 | 35 | 38 | 45 | 42 | 40 | 42 | 320
```

Save as: `test_results.xlsx`

✅ **Checkpoint:** Results file prepared

---

### Test 6.2: Upload Results

**Steps:**
1. From Super Admin Dashboard, click "Go to Results"
2. Find your test, click "Upload Results"
3. Upload `test_results.xlsx`
4. Click "Upload"

**Expected Results:**
- ✅ Processing message
- ✅ Upload report shows:
  - Successfully uploaded: 5 results
  - Errors: 0
  - Warnings: 0
- ✅ Success message

**Verify Upload:**
1. Click "View Uploaded Results"
2. Should see table with all 5 students:
   - ✅ Roll numbers match
   - ✅ Book colors match
   - ✅ All subject marks display
   - ✅ Total marks correct
3. ✅ Status shows "Unpublished"
4. ✅ "Publish Results" button visible

**Verify in Database:**
```sql
SELECT * FROM results WHERE test_id = [test_id];
-- Should show 5 records
SELECT * FROM results WHERE is_published = 0;
-- Should show 5 (all unpublished)
```

✅ **Checkpoint:** Results uploaded successfully

---

### Test 6.3: Publish Results

**Steps:**
1. On results view page, click "Publish Results"
2. Confirm in dialog: "Are you sure?"
3. Click "Yes, Publish"

**Expected Results:**
- ✅ Success message: "Results published successfully!"
- ✅ Status changes to "Published"
- ✅ Publication date/time displayed
- ✅ "Publish" button changes to "Unpublish"

**Verify in Database:**
```sql
SELECT is_published, published_at FROM results WHERE test_id = [test_id];
-- is_published should be 1 for all
-- published_at should have current timestamp
```

✅ **Checkpoint:** Results published

---

## 📋 PHASE 7: COLLEGE ADMIN - VIEW RESULTS & REPORTS (15 minutes)

### Test 7.1: View Published Results

**Steps:**
1. **Logout from Super Admin**
2. **Login as College Admin** (admin@testcollege.com / college123)
3. Click "View Results" from dashboard

**Expected Results:**
- ✅ Test card appears with "Published" status
- ✅ Shows result count: 5
- ✅ Shows test date and mode

4. Click "View Results"

**Expected Results:**
- ✅ Statistics cards show:
  - Total Students: 5
  - Average Marks: 282.6 (calculated)
  - Highest Marks: 320
  - Lowest Marks: 241
- ✅ Results table shows all 5 students
- ✅ Sorted by marks (highest first)
- ✅ Book colors display with correct colors
- ✅ All subject marks visible
- ✅ Total marks correct

**Expected Order (by marks):**
```
1. Usman Ahmed - 320
2. Fatima Noor - 298
3. Sara Khan - 279
4. Ali Ahmed - 275
5. Hassan Ali - 241
```

✅ **Checkpoint:** Results visible to College Admin

---

### Test 7.2: Generate Student List Report

**Steps:**
1. Go to "Generate Reports"
2. In "Student List Report" section:
   - Leave "Filter by Test" as "All Tests"
   - Click "Download Student List (Excel)"

**Expected Results:**
- ✅ Excel file downloads
- ✅ Filename: TCQ_students_YYYY-MM-DD.xlsx
- ✅ Open file and verify:
  - All 5 students listed
  - All columns present (15 columns)
  - Registration IDs correct
  - Roll numbers present
  - Seating info present
  - Book colors correct

✅ **Checkpoint:** Student list report generated

---

### Test 7.3: Generate Result Report

**Steps:**
1. Still in "Generate Reports"
2. In "Test Results Report" section:
   - Select the test from dropdown
   - Click "Download Results Report (Excel)"

**Expected Results:**
- ✅ Excel file downloads
- ✅ Filename: TCQ_results_YYYY-MM-DD.xlsx
- ✅ Open file and verify:
  - All 5 students listed
  - Roll numbers correct
  - Names and CNICs present
  - Book colors correct
  - All 8 subject columns present (Mode 1)
  - Total marks correct
  - Sorted by roll number

✅ **Checkpoint:** Result report generated

---

## 📋 PHASE 8: AUDIT LOGS (10 minutes)

### Test 8.1: View Audit Logs

**Steps:**
1. **Logout from College Admin**
2. **Login as Super Admin** (admin/admin123)
3. Click "View Logs" from dashboard

**Expected Results:**
- ✅ Audit logs page loads
- ✅ Statistics cards show counts
- ✅ Filters available

**Verify Log Entries:**

Should see logs for (scroll through list):
1. ✅ College created (created action)
2. ✅ Test created (created action)
3. ✅ Students registered - 3 manual (created action)
4. ✅ Students uploaded - 2 bulk (uploaded action)
5. ✅ Roll numbers generated (generated action)
6. ✅ Results uploaded (uploaded action)
7. ✅ Results published (published action)

**Test Filters:**
1. Filter by Action: "created"
   - ✅ Shows only created actions
2. Filter by Model: "Student"
   - ✅ Shows only student-related logs
3. Filter by Date: Today
   - ✅ Shows today's logs
4. Search: "bulk"
   - ✅ Shows bulk upload log

**View Log Details:**
1. Click "View Details" on "Roll numbers generated" log
2. ✅ Shows complete information
3. ✅ Shows new values (JSON)
4. ✅ Shows IP address
5. ✅ Shows timestamp

✅ **Checkpoint:** Complete audit trail present

---

## 📋 PHASE 9: FINAL VERIFICATION (10 minutes)

### Test 9.1: Database Verification

**Run these SQL queries:**
```sql
-- Check colleges
SELECT * FROM colleges;
-- Should have 1 record: Test College Quetta

-- Check test districts
SELECT * FROM test_districts;
-- Should have 2 records: Quetta, Pishin

-- Check tests
SELECT * FROM tests;
-- Should have 1 record

-- Check test venues
SELECT * FROM test_venues;
-- Should have 2 records: Quetta venue, Pishin venue

-- Check students
SELECT COUNT(*) FROM students;
-- Should be 5

-- Check students with roll numbers
SELECT COUNT(*) FROM students WHERE roll_number IS NOT NULL;
-- Should be 5

-- Check roll number sequence
SELECT roll_number FROM students ORDER BY roll_number;
-- Should be: 00001, 00002, 00003, 00004, 00005

-- Check book colors
SELECT roll_number, book_color FROM students ORDER BY roll_number;
-- Should cycle: Yellow, Green, Blue, Pink, Yellow

-- Check results
SELECT COUNT(*) FROM results;
-- Should be 5

-- Check published results
SELECT COUNT(*) FROM results WHERE is_published = 1;
-- Should be 5

-- Check audit logs
SELECT COUNT(*) FROM audit_logs;
-- Should be 10+ (depends on actions taken)
```

✅ **Checkpoint:** Database state correct

---

### Test 9.2: File System Verification

**Check these directories:**
```
storage/app/public/student-pictures/
- Should contain 5 image files
- Filenames should be random strings with extensions

storage/app/temp/
- Should be empty or have only recent temporary files

storage/logs/
- Check laravel-YYYY-MM-DD.log
- Should have no error messages
```

✅ **Checkpoint:** Files stored correctly

---

### Test 9.3: Session Timeout Test

**Steps:**
1. Login as College Admin
2. Wait 15 minutes (do not interact with page)
3. Try to navigate to any page

**Expected Results:**
- ✅ Redirects to login page
- ✅ Session expired message

✅ **Checkpoint:** Session timeout working

---

## ✅ TESTING SUMMARY

### Success Criteria

**Core Functionality:**
- [x] Super Admin can login
- [x] College can be created
- [x] College admin account can be created
- [x] Test can be created with venues
- [x] College admin can login
- [x] Students can be registered manually (3 students)
- [x] Bulk upload template can be downloaded
- [x] Bulk upload validates and imports (2 students)
- [x] Roll numbers generate correctly
- [x] Roll number algorithm works (sorting, colors, seating)
- [x] Results can be uploaded
- [x] Results can be published
- [x] College admin can view results
- [x] Reports can be generated
- [x] Audit logs capture all actions
- [x] Session timeout works

**Data Integrity:**
- [x] 1 college in database
- [x] 2 test districts assigned
- [x] 1 test with 2 venues
- [x] 5 students registered
- [x] 5 roll numbers generated (sequential)
- [x] 5 results uploaded and published
- [x] 10+ audit log entries
- [x] 5 photos uploaded

**Business Logic:**
- [x] Roll numbers sequential (00001-00005)
- [x] Book colors cycle correctly
- [x] Students sorted by district then CNIC
- [x] Seating assigned correctly
- [x] Age validation works
- [x] Gender policy enforced
- [x] CNIC uniqueness enforced
- [x] Cannot delete students after roll numbers
- [x] Cannot edit students after roll numbers (except test district)

---

## 🐛 TROUBLESHOOTING

### Common Issues

**Issue 1: Login Fails**
- Check credentials
- Check database: `SELECT * FROM super_admins;`
- Clear browser cache
- Check routes: `php artisan route:list`

**Issue 2: Bulk Upload "No Excel File Found"**
- Ensure students.xlsx is in ZIP root (not in subfolder)
- Check ZIP structure: students.xlsx and pictures/ folder side by side

**Issue 3: Validation Errors in Bulk Upload**
- Check CNIC format (exactly 13 digits)
- Check picture filenames match CNIC exactly
- Check date format (DD/MM/YYYY)
- Check age falls within college policy
- Check gender matches college policy

**Issue 4: Roll Number Generation Wrong Order**
- Expected: Sorted by test district (alphabetical), then CNIC
- Check students' test districts
- Check CNIC values

**Issue 5: Result Upload Fails**
- Check Excel columns match test mode
- Check roll numbers exist in database
- Check book colors match
- Check marks are numeric

**Issue 6: Photos Not Displaying**
- Check storage link: `php artisan storage:link`
- Check file exists: `storage/app/public/student-pictures/`
- Check file permissions

---

## 📊 TEST REPORT TEMPLATE

Use this to document your testing:
```
ADMISSION PORTAL - TEST REPORT

Test Date: _______________
Tester Name: _______________
Environment: Development/Production

PHASE 1: Database Cleanup
□ Tables truncated
□ Files deleted
□ Super admin verified

PHASE 2: Super Admin Setup
□ Login successful
□ College created
□ College admin created
□ Test created
Issues: _______________

PHASE 3: College Admin Registration
□ Login successful
□ Student 1 registered
□ Student 2 registered
□ Student 3 registered
□ View students working
□ Template downloaded
Issues: _______________

PHASE 4: Bulk Upload
□ Excel prepared
□ Upload successful
□ Validation passed
□ Import successful
Issues: _______________

PHASE 5: Roll Numbers
□ Preview correct
□ Generation successful
□ Sorting correct
□ Colors correct
□ Seating correct
□ Edit restrictions work
□ Delete restrictions work
Issues: _______________

PHASE 6: Results
□ Excel prepared
□ Upload successful
□ Publish successful
Issues: _______________

PHASE 7: College Views
□ Results visible
□ Reports downloadable
Issues: _______________

PHASE 8: Audit Logs
□ All actions logged
□ Filters work
□ Details viewable
Issues: _______________

PHASE 9: Final Verification
□ Database correct
□ Files correct
□ Session timeout works
Issues: _______________

OVERALL STATUS: PASS / FAIL
NOTES:
_______________________________________________
_______________________________________________
_______________________________________________
```

---

## ✅ COMPLETION

**When All Tests Pass:**
1. ✅ System is fully functional
2. ✅ Ready for production testing
3. ✅ Can proceed to student portal development

**Next Steps:**
1. Build student public portal
2. Add PDF generation
3. Production deployment

---

**Document Version:** 1.0  
**Last Updated:** November 17, 2025  
**Status:** Complete Testing Guide

---

**End of Complete Testing Guide**