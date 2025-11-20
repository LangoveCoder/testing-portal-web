# 🔄 Admission Test Portal - System Flow Diagram

**Last Updated:** November 16, 2025

---

## 📊 COMPLETE SYSTEM WORKFLOW
```
┌─────────────────────────────────────────────────────────────────┐
│                    ADMISSION TEST PORTAL                         │
│                     SYSTEM ARCHITECTURE                          │
└─────────────────────────────────────────────────────────────────┘

┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ SUPER ADMIN  │    │COLLEGE ADMIN │    │   STUDENTS   │
│              │    │              │    │   (Public)   │
└──────┬───────┘    └──────┬───────┘    └──────┬───────┘
       │                   │                   │
       │                   │                   │
       ▼                   ▼                   ▼
┌─────────────────────────────────────────────────────────┐
│              LARAVEL APPLICATION LAYER                   │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐  │
│  │  Auth   │  │Business │  │  Data   │  │  View   │  │
│  │  Layer  │  │  Logic  │  │  Layer  │  │  Layer  │  │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘  │
└─────────────────────────────────────────────────────────┘
                        │
                        ▼
              ┌──────────────────┐
              │  MySQL DATABASE  │
              │ admission_portal │
              └──────────────────┘
```

---

## 🎯 SUPER ADMIN WORKFLOW

### Phase 1: System Setup
```
START
  │
  ├─► Login (admin/admin123)
  │
  ├─► COLLEGE MANAGEMENT
  │   ├─► Create College
  │   │   ├─► Basic Info (Name, Code, Contact)
  │   │   ├─► Assign Test Districts
  │   │   ├─► Set Age Policy (min/max)
  │   │   ├─► Set Gender Policy (Male/Female/Both)
  │   │   └─► Set Registration Start Date
  │   │
  │   ├─► Create College Admin Account
  │   │   ├─► Email
  │   │   └─► Password
  │   │
  │   └─► [College Ready for Tests]
  │
  ├─► TEST MANAGEMENT
  │   ├─► Create Test for College
  │   │   ├─► Test Date & Time
  │   │   ├─► Test Mode (Mode 1/2/3)
  │   │   ├─► Total Marks (100/200/300)
  │   │   ├─► Registration Deadline
  │   │   ├─► Starting Roll Number
  │   │   └─► Configure Venues
  │   │       ├─► Select Test District
  │   │       ├─► Venue Name & Address
  │   │       └─► Capacity Structure
  │   │           ├─► Number of Halls
  │   │           ├─► Zones per Hall
  │   │           ├─► Rows per Zone
  │   │           └─► Seats per Row
  │   │
  │   └─► [Test Ready for Registration]
  │
  └─► [System Ready for Students]
```

### Phase 2: Student Registration Support
```
OPTION A: Individual Registration (College Admin does this)
  │
  └─► See "College Admin Workflow" below

OPTION B: Bulk Upload
  │
  ├─► Select College & Test
  ├─► Download Template (ZIP)
  │   ├─► students.xlsx (with Excel dropdowns)
  │   ├─► pictures/ (empty folder)
  │   └─► INSTRUCTIONS.txt
  │
  ├─► Send Template to College
  │
  ├─► Receive Filled ZIP from College
  │   ├─► students.xlsx (filled)
  │   └─► pictures/ (student photos named as CNIC.jpg)
  │
  ├─► Upload ZIP to System
  │   ├─► Extract Files
  │   ├─► Validate All Data
  │   │   ├─► Check required fields
  │   │   ├─► Validate CNIC format
  │   │   ├─► Check duplicates
  │   │   ├─► Verify age policy
  │   │   ├─► Verify gender policy
  │   │   ├─► Match pictures
  │   │   └─► Validate test districts
  │   │
  │   ├─► Preview Results
  │   │   ├─► ✅ Valid Students: 85
  │   │   └─► ❌ Errors: 15
  │   │
  │   └─► DECISION
  │       ├─► Import Valid Only
  │       │   ├─► 85 students imported
  │       │   ├─► Download error report
  │       │   └─► Send to college for correction
  │       │
  │       └─► Cancel & Re-upload
  │
  └─► [Students Registered]
```

### Phase 3: Roll Number Generation
```
When Registration Deadline Reached
  │
  ├─► Navigate to "Generate Roll Numbers"
  ├─► Select Test
  ├─► Preview Assignment
  │   ├─► View student list
  │   ├─► See roll numbers (00001, 00002...)
  │   ├─► See book colors (Yellow→Green→Blue→Pink)
  │   └─► See seating (Hall-Zone-Row-Seat)
  │
  ├─► Confirm Generation
  │   ├─► Sequential roll numbers assigned
  │   ├─► Book colors cycled
  │   ├─► Seating auto-assigned
  │   └─► Database updated
  │
  └─► [Roll Numbers Generated]
      ├─► Students can check roll numbers
      └─► College can download roll slips
```

### Phase 4: Test Conductance
```
Test Day
  │
  ├─► Print Documents (FUTURE FEATURE)
  │   ├─► Roll Number Slips (per student)
  │   ├─► Attendance Sheets (per hall)
  │   └─► OMR Sheets (per student)
  │
  └─► Conduct Test at Venues
```

### Phase 5: Result Management
```
After Test Completion
  │
  ├─► Prepare Excel File
  │   └─► Format based on Test Mode
  │       ├─► Mode 1: 8 subject columns + total
  │       ├─► Mode 2: 4 subject columns + total
  │       └─► Mode 3: 1 total marks column
  │
  ├─► Upload Results
  │   ├─► Select Test
  │   ├─► Upload Excel File
  │   ├─► System Validates
  │   │   ├─► Match roll numbers
  │   │   ├─► Validate marks format
  │   │   └─► Calculate totals
  │   │
  │   └─► View Upload Report
  │       ├─► ✅ Success: 85 results
  │       └─► ❌ Errors: 15 results
  │
  ├─► Review Results
  │   ├─► View all student results
  │   ├─► Check for errors
  │   └─► Verify calculations
  │
  ├─► Publish Results
  │   ├─► Set publication date (optional)
  │   ├─► Click "Publish Results"
  │   └─► Students can now check results
  │
  └─► [Results Published]
      └─► Students check via public portal
```

### Phase 6: Monitoring & Auditing
```
Ongoing Activities
  │
  ├─► View Audit Logs
  │   ├─► Filter by user type
  │   ├─► Filter by action
  │   ├─► Filter by date range
  │   └─► View detailed changes
  │
  ├─► Manage Students
  │   ├─► View all students
  │   ├─► Edit student info
  │   │   ├─► Before roll numbers: Full edit
  │   │   └─► After roll numbers: Test district only
  │   └─► Delete students (before roll numbers only)
  │
  └─► Generate Reports (FUTURE)
```

---

## 🎓 COLLEGE ADMIN WORKFLOW

### Phase 1: Access System
```
START
  │
  ├─► Login (email/password provided by Super Admin)
  │
  └─► View Dashboard
      ├─► Total Students Registered
      ├─► Roll Numbers Generated
      └─► Available Tests
```

### Phase 2A: Individual Student Registration
```
Register Student
  │
  ├─► Click "Register Student"
  │
  ├─► Fill Registration Form
  │   ├─► Personal Info
  │   │   ├─► Name
  │   │   ├─► Father Name
  │   │   ├─► Student CNIC (13 digits)
  │   │   ├─► Father CNIC (13 digits)
  │   │   ├─► Gender (validated against policy)
  │   │   ├─► Religion
  │   │   └─► Date of Birth (age validated)
  │   │
  │   ├─► Address Info
  │   │   ├─► Province
  │   │   ├─► Division
  │   │   ├─► District
  │   │   └─► Complete Address
  │   │
  │   ├─► Test Info
  │   │   └─► Test District (from assigned list)
  │   │
  │   └─► Picture Upload
  │       ├─► JPG/PNG, Max 2MB
  │       └─► Preview before submit
  │
  ├─► System Validates
  │   ├─► CNIC uniqueness
  │   ├─► Age policy compliance
  │   ├─► Gender policy compliance
  │   ├─► Picture requirements
  │   └─► All required fields
  │
  ├─► Student Created
  │   ├─► Registration ID generated
  │   └─► Success message
  │
  └─► Repeat for next student
```

### Phase 2B: Bulk Upload Template
```
Download Template for Bulk Upload
  │
  ├─► Click "Download Bulk Upload Template"
  │
  ├─► Select Test
  │
  ├─► Download ZIP File
  │   ├─► students.xlsx
  │   │   ├─► Pre-configured with test districts
  │   │   ├─► Excel dropdowns for:
  │   │   │   ├─► Gender (Male/Female)
  │   │   │   ├─► Religion (Islam/Christianity/etc.)
  │   │   │   ├─► Province (all Pakistan provinces)
  │   │   │   ├─► Division (Balochistan divisions)
  │   │   │   ├─► District (all Balochistan districts)
  │   │   │   └─► Test District (college's districts)
  │   │   └─► Sample data in row 2
  │   │
  │   ├─► pictures/ (empty folder)
  │   └─► INSTRUCTIONS.txt
  │
  ├─► Fill Excel Using Dropdowns
  │   ├─► Fill student data
  │   ├─► Use dropdowns (prevents errors)
  │   └─► Add Picture Filename column
  │
  ├─► Prepare Photos
  │   ├─► Rename to: CNIC.jpg
  │   │   └─► e.g., 4210112345678.jpg
  │   └─► Put in pictures/ folder
  │
  ├─► Create ZIP
  │   ├─► students.xlsx (filled)
  │   └─► pictures/ (with photos)
  │
  └─► Send ZIP to Super Admin
      ├─► Email
      ├─► WhatsApp
      └─► USB Drive
```

### Phase 3: View Students
```
View Registered Students
  │
  ├─► Navigate to "View Students"
  │
  ├─► See Student List
  │   ├─► Name, CNIC
  │   ├─► Gender, Religion
  │   ├─► Roll Number (after generation)
  │   ├─► Book Color (after generation)
  │   └─► Seating Info (after generation)
  │
  ├─► Click "View" on Student
  │   ├─► See complete details
  │   ├─► See roll number & seating
  │   └─► See picture
  │
  └─► Download Reports (FUTURE)
      ├─► Student list (Excel/PDF)
      └─► Roll number slips
```

---

## 👨‍🎓 STUDENT WORKFLOW

### Phase 1: Check Roll Number
```
After Registration Deadline
  │
  ├─► Visit: http://127.0.0.1:8000/student/check-roll-number
  │
  ├─► Enter Details
  │   ├─► CNIC (13 digits)
  │   └─► Registration ID
  │
  ├─► Click "Check Roll Number"
  │
  ├─► System Displays
  │   ├─► Roll Number (e.g., 00001)
  │   ├─► Book Color (Yellow/Green/Blue/Pink)
  │   ├─► Test Venue Details
  │   ├─► Hall Number
  │   ├─► Zone Number
  │   ├─► Row Number
  │   └─► Seat Number
  │
  └─► Download Roll Number Slip (FUTURE)
```

### Phase 2: Take Test
```
Test Day
  │
  ├─► Arrive at Venue
  ├─► Find Hall, Zone, Row, Seat
  ├─► Receive Question Book (matching color)
  ├─► Take Test
  └─► Submit Answer Sheet
```

### Phase 3: Check Result
```
After Results Published
  │
  ├─► Visit: http://127.0.0.1:8000/student/check-result
  │
  ├─► Enter Details
  │   ├─► CNIC (13 digits)
  │   └─► Roll Number
  │
  ├─► Click "Check Result"
  │
  ├─► System Displays
  │   ├─► Student Info
  │   ├─► Subject-wise Marks (based on mode)
  │   │   ├─► Mode 1: 8 subjects
  │   │   ├─► Mode 2: 4 subjects
  │   │   └─► Mode 3: Total only
  │   ├─► Total Marks
  │   └─► Result Status
  │
  └─► Download Result Card (FUTURE)
```

---

## 🔄 DATA FLOW DIAGRAM

### Student Registration Flow
```
College Admin          System              Database
     │                   │                    │
     ├─► Fill Form ─────►│                    │
     │                   ├─► Validate ───────►│
     │                   │   - CNIC unique    │
     │                   │   - Age valid      │
     │                   │   - Gender valid   │
     │                   │                    │
     │                   ├──── Check ────────►│
     │                   │                    │
     │                   ◄──── Result ────────┤
     │                   │                    │
     │                   ├─► Upload Photo ───►│
     │                   │                    │
     │◄─── Success ──────┤◄── Insert ─────────┤
     │    Message        │    Student         │
```

### Roll Number Generation Flow
```
Super Admin           System              Database
     │                   │                    │
     ├─► Select Test ───►│                    │
     │                   ├─► Get Students ───►│
     │                   │                    │
     │                   ├─► Sort by ────────►│
     │                   │   District+CNIC    │
     │                   │                    │
     │                   ├─► Assign:          │
     │                   │   - Roll Numbers   │
     │                   │   - Book Colors    │
     │                   │   - Seating        │
     │                   │                    │
     │◄─── Preview ──────┤                    │
     │                   │                    │
     ├─► Confirm ───────►│                    │
     │                   ├─► Update All ─────►│
     │                   │   Students         │
     │                   │                    │
     │◄─── Success ──────┤◄── Updated ────────┤
```

### Result Upload Flow
```
Super Admin           System              Database
     │                   │                    │
     ├─► Upload Excel ──►│                    │
     │                   ├─► Parse File       │
     │                   │                    │
     │                   ├─► For Each Row:    │
     │                   │   - Find Student ─►│
     │                   │   - Validate Marks │
     │                   │   - Calculate Total│
     │                   │                    │
     │                   ├─► Collect:         │
     │                   │   - Valid Records  │
     │                   │   - Errors         │
     │                   │                    │
     │◄─── Report ───────┤                    │
     │    (Success/Fail) │                    │
     │                   │                    │
     ├─► Review & ──────►│                    │
     │   Confirm         ├─► Insert ─────────►│
     │                   │   Results          │
     │                   │                    │
     ├─► Publish ───────►│                    │
     │                   ├─► Update ─────────►│
     │                   │   is_published=1   │
     │                   │                    │
     │◄─── Done ─────────┤◄── Updated ────────┤
```

---

## 🎨 USER INTERFACE FLOW

### Super Admin Dashboard Navigation
```
Dashboard
├── Manage Colleges
│   ├── View All Colleges
│   ├── Create New College
│   ├── Edit College
│   └── View College Details
│
├── Manage Tests
│   ├── View All Tests
│   ├── Create New Test
│   └── View Test Details
│
├── Manage Students
│   ├── View All Students (with filters)
│   ├── View Student Details
│   ├── Edit Student
│   └── Delete Student
│
├── Generate Roll Numbers
│   ├── Select Test
│   ├── Preview Assignments
│   └── Confirm Generation
│
├── Manage Results
│   ├── View All Tests
│   ├── Upload Results (select test)
│   ├── View Results (with students)
│   ├── Publish/Unpublish
│   └── Delete Results
│
├── Bulk Upload (IN PROGRESS)
│   ├── Download Template
│   ├── Upload ZIP
│   ├── Preview & Validate
│   └── Import Students
│
└── View Audit Logs
    ├── Filter by criteria
    └── View detailed changes
```

### College Admin Dashboard Navigation
```
Dashboard
├── Register Student
│   ├── Fill Form
│   ├── Upload Picture
│   └── Submit
│
├── View Students
│   ├── Student List
│   └── Student Details
│
└── Download Template (for bulk upload)
    └── Send to Super Admin
```

---

## 📊 FEATURE STATUS MATRIX

| Feature Category | Sub-Feature | Status | Priority |
|-----------------|-------------|--------|----------|
| **Authentication** | Super Admin Login | ✅ Complete | Critical |
| | College Admin Login | ✅ Complete | Critical |
| | Session Timeout | ✅ Complete | High |
| **College Management** | CRUD Operations | ✅ Complete | Critical |
| | Test Districts | ✅ Complete | Critical |
| | Policies (Age/Gender) | ✅ Complete | High |
| **Test Management** | Create Tests | ✅ Complete | Critical |
| | Venue Configuration | ✅ Complete | Critical |
| | 3 Test Modes | ✅ Complete | Critical |
| **Student Registration** | Individual (College) | ✅ Complete | Critical |
| | Bulk Upload | 🔄 80% Done | High |
| | Picture Upload | ✅ Complete | High |
| **Student Management** | View All (Super Admin) | ✅ Complete | High |
| | Edit Students | ✅ Complete | Medium |
| | Delete Students | ✅ Complete | Medium |
| **Roll Number Generation** | Sequential Assignment | ✅ Complete | Critical |
| | Book Color Cycling | ✅ Complete | Critical |
| | Seating Assignment | ✅ Complete | Critical |
| | Preview & Confirm | ✅ Complete | High |
| **Result Management** | Excel Upload | ✅ Complete | Critical |
| | Mode 1 Support | ✅ Complete | Critical |
| | Mode 2 Support | ✅ Complete | Critical |
| | Mode 3 Support | ⚠️ Untested | High |
| | Publish/Unpublish | ✅ Complete | Critical |
| **Audit Logs** | Activity Tracking | ✅ Complete | High |
| | Filtering | ✅ Complete | Medium |
| | Detail View | ⚠️ Minor Issues | Low |
| **PDF Generation** | Roll Slips | ❌ Not Started | Critical |
| | Attendance Sheets | ❌ Not Started | Critical |
| | OMR Sheets | ❌ Not Started | High |
| **Student Portal** | Check Roll Number | ❌ Not Started | High |
| | Check Results | ❌ Not Started | Critical |
| | Download Cards | ❌ Not Started | Medium |

**Legend:**
- ✅ Complete and tested
- 🔄 In progress
- ⚠️ Completed with minor issues
- ❌ Not started

---

**End of System Flow Diagram**