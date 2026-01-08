# 📱 API Integration Guide - Updated Database & Endpoints

## 🔄 **Database Changes Summary**

### **New Tables Added:**
1. **`student_attendance`** - Tracks present/absent status from mobile app
2. **`offline_syncs`** - Handles offline data synchronization
3. **`biometric_operator_test`** - Pivot table for operator-test relationships

### **Updated Tables:**
1. **`students`** - Added `college_id`, `fingerprint_quality`, `fingerprint_registered_at`
2. **`biometric_operators`** - Changed from JSON arrays to proper relationships
3. **`fingerprint_verifications`** - Enhanced with status tracking

---

## 🖥️ **Windows Biometric App - Required Updates**

### **1. Updated Login Response Structure**
**Endpoint:** `POST /api/biometric-operator/login`

**OLD Response:**
```json
{
  "success": true,
  "token": "api_token",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "operator"
  }
}
```

**NEW Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "operator": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+1234567890",
      "status": "active",
      "assigned_college_id": 1,
      "assigned_college": {
        "id": 1,
        "name": "ABC College",
        "district": "Lahore",
        "province": "Punjab"
      },
      "tests": [
        {
          "id": 1,
          "test_name": "Entry Test 2026",
          "test_date": "2026-02-15",
          "test_time": "09:00:00",
          "total_marks": 100
        }
      ],
      "permissions": {
        "can_register_fingerprints": true,
        "can_verify_fingerprints": true,
        "can_view_students": true
      }
    },
    "token": "api_token_here",
    "expires_at": "2026-02-08T10:30:00.000000Z"
  }
}
```

### **2. Enhanced Fingerprint Upload**
**Endpoint:** `POST /api/biometric/fingerprint/upload-template`

**NEW Required Fields:**
```json
{
  "roll_number": "12345",
  "fingerprint_template": "base64_template_data",
  "fingerprint_quality": 85,
  "operator_id": 1,
  "device_info": "Windows 11, Scanner Model XYZ"
}
```

**NEW Response:**
```json
{
  "success": true,
  "message": "Fingerprint template saved successfully",
  "data": {
    "roll_number": "12345",
    "name": "Student Name",
    "quality_score": 85,
    "registered_at": "08 Jan 2026, 10:30 AM",
    "quality_status": "Excellent"
  }
}
```

### **3. New Fingerprint Image Upload (Base64)**
**Endpoint:** `POST /api/biometric/fingerprint/upload-image-base64`

```json
{
  "roll_number": "12345",
  "fingerprint_image_base64": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
  "fingerprint_quality": 85,
  "operator_id": 1,
  "device_info": "Windows 11, Scanner Model XYZ"
}
```

### **4. Quality Validation Endpoint**
**Endpoint:** `POST /api/biometric/fingerprint/validate-quality`

```json
{
  "fingerprint_template": "template_data",
  "quality_score": 75
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "quality_score": 75,
    "quality_level": "Good",
    "is_acceptable": true,
    "minimum_required": 60,
    "recommendation": "Fingerprint quality is acceptable for registration",
    "color_code": "blue"
  }
}
```

### **5. Enhanced Student Bulk Download**
**Endpoint:** `POST /api/biometric/students/bulk-download`

**NEW Parameters:**
```json
{
  "test_id": 1,
  "college_id": 1,
  "include_biometric_data": true,
  "page": 1,
  "per_page": 100
}
```

**NEW Response Structure:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "roll_number": "12345",
      "name": "Student Name",
      "college_id": 1,
      "college_name": "ABC College",
      "biometric_status": {
        "has_fingerprint": true,
        "fingerprint_quality": 85,
        "registered_at": "08 Jan 2026, 10:30 AM",
        "quality_status": "Excellent"
      },
      "biometric_data": {
        "fingerprint_template": "base64_template",
        "fingerprint_image_url": "https://domain.com/storage/fingerprints/12345.png"
      }
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 100,
    "total": 500
  },
  "summary": {
    "total_students": 500,
    "students_with_fingerprints": 350,
    "students_with_photos": 480
  }
}
```

---

## 📱 **Android Attendance App - New Features**

### **1. Student Info Lookup**
**Endpoint:** `POST /api/attendance/student-info`

```json
{
  "roll_number": "12345",
  "test_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "student": {
      "id": 1,
      "name": "Student Name",
      "roll_number": "12345",
      "father_name": "Father Name",
      "cnic": "12345-1234567-1",
      "gender": "Male",
      "picture": "https://domain.com/storage/pictures/12345.jpg",
      "test_photo": "https://domain.com/storage/test_photos/12345.jpg",
      "hall_number": 1,
      "seat_number": 25,
      "college_name": "ABC College",
      "test_date": "15 Feb 2026"
    },
    "biometric_status": {
      "has_fingerprint": true,
      "has_photo": true,
      "fingerprint_quality": 85,
      "registered_at": "08 Jan 2026, 10:30 AM"
    },
    "attendance": {
      "status": "present",
      "marked_at": "08 Jan 2026, 09:15 AM",
      "marked_by": "Mobile App User",
      "notes": "Student arrived on time"
    },
    "already_marked": true,
    "can_mark_attendance": false
  }
}
```

### **2. Mark Attendance**
**Endpoint:** `POST /api/attendance/mark`

```json
{
  "roll_number": "12345",
  "test_id": 1,
  "attendance_status": "present",
  "marked_by": "Mobile App User",
  "device_info": "Android 12, Samsung Galaxy S21",
  "notes": "Student arrived on time",
  "location": {
    "latitude": 31.5204,
    "longitude": 74.3587
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Attendance marked successfully",
  "data": {
    "attendance": {
      "roll_number": "12345",
      "student_name": "Student Name",
      "father_name": "Father Name",
      "college_name": "ABC College",
      "attendance_status": "present",
      "marked_at": "08 Jan 2026, 09:15 AM",
      "marked_by": "Mobile App User",
      "notes": "Student arrived on time | Location: 31.520400, 74.358700"
    },
    "student_info": {
      "hall_number": 1,
      "seat_number": 25,
      "has_photo": true,
      "has_fingerprint": true
    }
  }
}
```

### **3. Bulk Attendance (Offline Sync)**
**Endpoint:** `POST /api/attendance/bulk-mark`

```json
{
  "attendance_records": [
    {
      "roll_number": "12345",
      "test_id": 1,
      "attendance_status": "present",
      "marked_by": "Mobile App User",
      "device_info": "Android 12, Samsung Galaxy",
      "notes": "Synced from offline",
      "offline_marked_at": "2026-01-08T09:15:00Z"
    }
  ]
}
```

### **4. Update Attendance**
**Endpoint:** `PUT /api/attendance/update`

```json
{
  "roll_number": "12345",
  "test_id": 1,
  "attendance_status": "absent",
  "updated_by": "Mobile App User",
  "reason": "Student left early due to emergency"
}
```

### **5. Attendance Statistics**
**Endpoint:** `GET /api/attendance/stats?test_id=1`

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 500,
    "present": 450,
    "absent": 50,
    "present_percentage": 90.0
  }
}
```

---

## 🔄 **Offline Sync System**

### **1. Queue for Sync**
**Endpoint:** `POST /api/sync/queue`

```json
{
  "device_id": "android_device_123",
  "sync_type": "upload",
  "data_type": "attendance",
  "record_id": "12345",
  "sync_data": {
    "roll_number": "12345",
    "test_id": 1,
    "attendance_status": "present",
    "marked_by": "Mobile App User"
  },
  "created_offline_at": "2026-01-08T09:15:00Z"
}
```

### **2. Process Pending Sync**
**Endpoint:** `POST /api/sync/process`

```json
{
  "device_id": "android_device_123",
  "batch_size": 20
}
```

### **3. Sync Status**
**Endpoint:** `GET /api/sync/status?device_id=android_device_123`

**Response:**
```json
{
  "success": true,
  "data": {
    "device_id": "android_device_123",
    "sync_stats": {
      "total": 50,
      "pending": 5,
      "completed": 43,
      "failed": 2,
      "last_sync": "2026-01-08T10:30:00Z"
    },
    "needs_sync": true,
    "last_sync_time": "08 Jan 2026, 10:30 AM"
  }
}
```

---

## 🎯 **Implementation Priorities**

### **Windows Biometric App:**
1. **Update login response parsing** - Handle new operator structure
2. **Add quality validation** - Pre-validate fingerprints before upload
3. **Implement base64 image upload** - For consistent image processing
4. **Add device info tracking** - Include device details in requests
5. **Handle enhanced error responses** - Better error handling and user feedback

### **Android Attendance App:**
1. **Implement student lookup** - Verify student before marking attendance
2. **Add attendance marking** - Present/Absent with location tracking
3. **Implement offline sync** - Queue attendance when offline
4. **Add bulk sync** - Sync multiple records when online
5. **Show biometric status** - Display if student has fingerprint/photo

### **Quality Standards:**
- **Minimum fingerprint quality:** 60%
- **Recommended quality:** 80%+
- **Image format:** PNG with enhanced contrast
- **Error handling:** Comprehensive validation and user feedback
- **Offline support:** Queue and sync when connection restored

---

## 🔧 **Testing Endpoints**

### **Base URL:** `https://your-domain.com/api`

### **Authentication:**
- **Biometric Operator:** Use token from login response
- **Mobile App:** No authentication required for attendance endpoints

### **Error Handling:**
All endpoints return consistent error format:
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

---

## 📊 **Web Dashboard Features**

### **Super Admin Dashboard:**
- **Student Attendance** - Real-time attendance monitoring
- **Biometric Status** - Enhanced with attendance integration
- **Priority Highlighting** - Present students needing biometrics
- **Export Functions** - CSV export of attendance data

### **Biometric Status Page:**
- **Attendance Column** - Shows present/absent status
- **Priority Filtering** - Focus on present students
- **Quality Indicators** - Color-coded fingerprint quality
- **Enhanced Statistics** - Attendance-aware metrics

This guide provides all the necessary information to update both your Windows biometric app and Android attendance app to work with the enhanced database structure and new API endpoints.