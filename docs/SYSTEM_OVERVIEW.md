# BACT Admission Portal System - Overview

## System Information

**Project Name:** BACT (Balochistan Academy for College Teachers) Admission Portal  
**Version:** 1.0  
**Technology Stack:** Laravel 12.37.0 + PHP 8.2.12 + MySQL  
**Server:** XAMPP (Development), Port 8001  
**Location:** Quetta, Balochistan, Pakistan

---

## Purpose

A comprehensive examination management system that handles the complete lifecycle of college entrance examinations, from student registration to result publication, including biometric verification across multiple platforms.

---

## System Architecture

```
┌──────────────────────────────────────────────────────────────────────────┐
│                     BACT ADMISSION PORTAL SYSTEM                          │
│                      (Laravel 12 + MySQL + PHP 8.2)                       │
└──────────────────────────────────────────────────────────────────────────┘
                                     │
                 ┌───────────────────┼───────────────────┐
                 │                   │                   │
          ┌──────▼──────┐    ┌──────▼──────┐    ┌──────▼──────┐
          │  WEB PORTAL │    │ ANDROID APP │    │ WINDOWS APP │
          │  (Laravel)  │    │  (Flutter)  │    │   (C#/WPF)  │
          │             │    │             │    │             │
          │ • Super     │    │ • Test      │    │ • Offline   │
          │   Admin     │    │   Photo     │    │   Biometric │
          │ • College   │    │   Capture   │    │   Verify    │
          │   Admin     │    │ • Real-time │    │ • Bulk      │
          │ • Biometric │    │   Upload    │    │   Download  │
          │   Operators │    └─────────────┘    └─────────────┘
          │ • Students  │
          └─────────────┘
                 │
     ┌───────────┴───────────┐
     │                       │
┌────▼────┐            ┌────▼────┐
│  MySQL  │            │ Storage │
│Database │            │ (Files) │
│         │            │         │
│ Tables: │            │ • Photos│
│ • users │            │ • Prints│
│ • tests │            │ • PDFs  │
│ • venues│            └─────────┘
│ • biom. │
└─────────┘
```

---

## Core Modules

### 1. User Management
- **Super Admin:** Full system access
- **College Admin:** College-specific operations
- **Biometric Operators:** Fingerprint registration (assigned by Super Admin)
- **Students:** Public portal access

### 2. College Management
- College registration
- Test district/venue setup
- Capacity planning (halls, zones, rows, seats)
- Google Maps integration with QR codes

### 3. Test Management
- Test creation with scheduling
- Registration deadlines
- Venue assignment
- Subject configuration

### 4. Student Management
- Manual registration (one-by-one)
- Bulk Excel upload
- Photo management
- Biometric data (fingerprint template, fingerprint image, test photo)

### 5. Roll Number Generation
- Sequential 5-digit roll numbers
- Cycling book colors (Yellow → Green → Blue → Pink)
- Automatic seating: Hall → Zone → Row → Seat (30 seats/row)
- District gap handling (+30 between venues)
- Color alignment for each venue

### 6. Document Generation
- **Roll Number Slips:** Individual PDFs with photo, QR code, seating info
- **Seating Plans:** Hall-wise seat arrangements (landscape, color-coded)
- **Attendance Sheets:** Zone+Row grouped, 10 students/page with photos

### 7. Biometric System
- **Web Portal Registration:** Operators capture fingerprints via browser
- **Web Portal Verification:** College admins verify on test day
- **Android App:** Test photo capture at venue entrance
- **Windows App:** Offline fingerprint verification with bulk download/sync

### 8. Result Management
- Excel upload
- District-wise merit lists
- Public result portal
- PDF generation

### 9. Audit & Reporting
- Complete audit trail
- Activity logs
- Custom reports
- Export functionality

---

## User Roles & Permissions

| Role | Access Level | Key Functions |
|------|--------------|---------------|
| **Super Admin** | Full System | Manage all colleges, tests, students, roll numbers, results, assign biometric operators |
| **College Admin** | College-Specific | Register students, view tests/results, verify fingerprints on test day |
| **Biometric Operator** | Assigned Tests/Colleges | Capture fingerprints via web portal, registration only |
| **Student** | Public Portal | Check roll number, download roll slip, view results |

---

## Technology Stack

### Backend
- **Framework:** Laravel 12.37.0
- **Language:** PHP 8.2.12
- **Database:** MySQL (XAMPP)
- **Authentication:** Laravel Guards (multi-auth)
- **PDF Generation:** DomPDF
- **Excel Processing:** PhpSpreadsheet

### Frontend
- **CSS Framework:** Tailwind CSS
- **JavaScript:** Vanilla JS, AJAX
- **Biometric SDK:** Device-specific JavaScript libraries

### Mobile
- **Android App:** Flutter
- **API Communication:** RESTful JSON APIs

### Desktop
- **Windows App:** C# / WPF
- **Local Database:** SQLite (encrypted)
- **Fingerprint SDK:** Device manufacturer SDK

---

## Data Flow

```
Registration → Roll Number Generation → Document Generation → Test Day Operations → Result Processing → Publication

1. Students registered (manual or bulk upload)
2. Roll numbers generated with seating assignments
3. Documents generated (slips, plans, attendance sheets)
4. Test day: Photo capture (Android) + Fingerprint verify (Web/Windows)
5. Results uploaded and processed
6. Merit lists generated
7. Results published to student portal
```

---

## Security Features

- Password hashing (bcrypt)
- Role-based access control (RBAC)
- CSRF protection
- SQL injection prevention
- File upload validation (type, size)
- Audit logging for all critical actions
- Encrypted biometric data storage
- Secure API endpoints with validation
- Session management
- XSS protection

---

## System Capacity

- **Students:** Tested with 20,000+ students
- **Colleges:** Unlimited
- **Tests:** Unlimited
- **Venues per Test:** Unlimited
- **Concurrent Users:** Server-dependent
- **File Storage:** Disk-dependent
- **Offline Support:** Full via Windows app

---

## Development Environment

- **OS:** Windows (XAMPP)
- **Web Server:** Apache (via XAMPP)
- **Port:** 8001
- **Base URL:** http://localhost:8001
- **Database:** MySQL via XAMPP
- **Storage:** public/storage (symlinked)

---

## Production Deployment Checklist

- [ ] Configure production database
- [ ] Set APP_ENV=production in .env
- [ ] Enable caching (config, routes, views)
- [ ] Configure queue workers
- [ ] Set up SSL certificate
- [ ] Configure backup system
- [ ] Set proper file permissions
- [ ] Configure CORS for API
- [ ] Set up monitoring/logging
- [ ] Load test system
- [ ] Configure fingerprint device drivers
- [ ] Test biometric functionality end-to-end

---

## Support & Maintenance

- **Developer:** Nadeem (Full-stack)
- **Development Approach:** Step-by-step, one file at a time
- **Testing:** Comprehensive with bulk data
- **Documentation:** Code comments + external docs

---

## Project Status

**Completion:** 99%

**Completed:**
- ✅ User management (Super Admin, College Admin)
- ✅ College & test management
- ✅ Student registration (manual + bulk)
- ✅ Roll number generation with seating
- ✅ Roll slips, seating plans, attendance sheets
- ✅ Result management & merit lists
- ✅ Biometric API endpoints (Android + Windows)
- ✅ Student portal

**In Progress:**
- 🚧 Biometric operator module (web portal)
- 🚧 Fingerprint verification tab (college portal)
- 🚧 Android app development (Flutter)

**Pending:**
- ⏳ OMR sheets generation
- ⏳ Answer keys system
- ⏳ QR code on roll slips (server extension issue)

---

## Contact

**Project Location:** Quetta, Balochistan, Pakistan  
**Primary Use Case:** College entrance examination management  
**Academic Session:** November - April (admission window)
