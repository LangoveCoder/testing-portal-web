# BACT Portal - API Documentation

## Base URLs

**Development:** `http://localhost:8001/api/biometric`  
**Production:** `https://yourdomain.com/api/biometric`

---

## Authentication

Currently, APIs are open (no authentication required for biometric endpoints).  
**For production, implement:**
- API tokens
- Rate limiting
- IP whitelisting

---

## Android App Endpoints

### 1. Get Active Colleges

**Endpoint:** `GET /colleges`

**Description:** Retrieve list of all active colleges with their active tests

**Request:**
```http
GET http://localhost:8001/api/biometric/colleges
Accept: application/json
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Test College, Quetta",
      "district": "Quetta",
      "province": "Balochistan",
      "tests": [
        {
          "id": 4,
          "college_id": 1,
          "test_name": "Test College, Quetta",
          "test_date": "2025-12-13"
        }
      ]
    }
  ]
}
```

---

### 2. Get Student Info by Roll Number

**Endpoint:** `POST /student/info`

**Description:** Search and retrieve student information by roll number

**Request:**
```http
POST http://localhost:8001/api/biometric/student/info
Content-Type: application/json

{
  "roll_number": "00001"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "roll_number": "00001",
    "name": "Amir Zaib",
    "father_name": "Abdul Majeed",
    "cnic": "5650307622993",
    "gender": "Male",
    "picture": "http://localhost:8001/storage/students/photo.jpg",
    "test_photo": null,
    "test_photo_captured": false,
    "test_name": "Test College, Quetta",
    "test_date": "13 Dec 2025",
    "hall_number": 1,
    "zone_number": 1,
    "row_number": 1,
    "seat_number": 1,
    "venue": "Quetta, Balochistan"
  }
}
```

**Response (404 Not Found):**
```json
{
  "success": false,
  "message": "Student not found with this roll number"
}
```

---

### 3. Upload Test Photo (Multipart)

**Endpoint:** `POST /student/upload-photo`

**Description:** Upload test photo as multipart/form-data

**Request:**
```http
POST http://localhost:8001/api/biometric/student/upload-photo
Content-Type: multipart/form-data

roll_number: 00001
test_photo: (binary image file - JPEG/JPG/PNG, max 5MB)
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Test photo uploaded successfully",
  "data": {
    "roll_number": "00001",
    "name": "Amir Zaib",
    "test_photo_url": "http://localhost:8001/storage/test_photos/00001_123456.jpg",
    "uploaded_at": "13 Dec 2025, 02:30 PM"
  }
}
```

---

### 4. Upload Test Photo (Base64)

**Endpoint:** `POST /student/upload-photo-base64`

**Description:** Upload test photo as base64 encoded string

**Request:**
```http
POST http://localhost:8001/api/biometric/student/upload-photo-base64
Content-Type: application/json

{
  "roll_number": "00001",
  "test_photo_base64": "data:image/jpeg;base64,/9j/4AAQSkZJRg..."
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Test photo uploaded successfully",
  "data": {
    "roll_number": "00001",
    "name": "Amir Zaib",
    "test_photo_url": "http://localhost:8001/storage/test_photos/00001_123456.jpg",
    "uploaded_at": "13 Dec 2025, 02:30 PM"
  }
}
```

---

## Biometric Module Endpoints (Web/Windows App)

### 5. Upload Fingerprint Template

**Endpoint:** `POST /fingerprint/upload-template`

**Description:** Upload fingerprint template string

**Request:**
```http
POST http://localhost:8001/api/biometric/fingerprint/upload-template
Content-Type: application/json

{
  "roll_number": "00001",
  "fingerprint_template": "base64_encoded_template_string_here..."
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Fingerprint template saved successfully"
}
```

---

### 6. Upload Fingerprint Image

**Endpoint:** `POST /fingerprint/upload-image`

**Description:** Upload fingerprint image file

**Request:**
```http
POST http://localhost:8001/api/biometric/fingerprint/upload-image
Content-Type: multipart/form-data

roll_number: 00001
fingerprint_image: (binary image file - JPEG/JPG/PNG/BMP, max 2MB)
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Fingerprint image uploaded successfully",
  "data": {
    "fingerprint_image_url": "http://localhost:8001/storage/fingerprints/00001_thumb.png"
  }
}
```

---

### 7. Verify Fingerprint

**Endpoint:** `POST /fingerprint/verify`

**Description:** Verify fingerprint against stored template

**Request:**
```http
POST http://localhost:8001/api/biometric/fingerprint/verify
Content-Type: application/json

{
  "roll_number": "00001",
  "fingerprint_template": "live_captured_template_string..."
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "roll_number": "00001",
    "name": "Amir Zaib",
    "stored_template": "stored_template_string...",
    "match": false
  }
}
```

**Note:** Actual matching should be done by biometric SDK on client side. This endpoint returns stored template for comparison.

---

### 8. Bulk Download Students

**Endpoint:** `POST /students/bulk-download`

**Description:** Download all students for offline use (Windows app)

**Request:**
```http
POST http://localhost:8001/api/biometric/students/bulk-download
Content-Type: application/json

{
  "test_id": 4
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "count": 250,
  "data": [
    {
      "id": 1,
      "roll_number": "00001",
      "name": "Amir Zaib",
      "father_name": "Abdul Majeed",
      "cnic": "5650307622993",
      "gender": "Male",
      "picture": "students/photo.jpg",
      "fingerprint_template": "base64_template...",
      "fingerprint_image": "fingerprints/00001.png",
      "test_photo": "test_photos/00001.jpg",
      "hall_number": 1,
      "zone_number": 1,
      "row_number": 1,
      "seat_number": 1
    }
  ]
}
```

---

## Error Responses

All error responses follow this format:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

### HTTP Status Codes

- `200` - Success
- `404` - Resource not found
- `422` - Validation error
- `500` - Server error

---

## Android Code Examples

### Using Retrofit

```java
// API Interface
public interface BiometricAPI {
    @GET("colleges")
    Call<CollegeResponse> getColleges();
    
    @POST("student/info")
    Call<StudentResponse> getStudentInfo(@Body StudentInfoRequest request);
    
    @Multipart
    @POST("student/upload-photo")
    Call<UploadResponse> uploadPhoto(
        @Part("roll_number") RequestBody rollNumber,
        @Part MultipartBody.Part photo
    );
    
    @POST("student/upload-photo-base64")
    Call<UploadResponse> uploadPhotoBase64(@Body UploadBase64Request request);
}

// Usage Example
Retrofit retrofit = new Retrofit.Builder()
    .baseUrl("http://localhost:8001/api/biometric/")
    .addConverterFactory(GsonConverterFactory.create())
    .build();

BiometricAPI api = retrofit.create(BiometricAPI.class);

// Get student info
StudentInfoRequest request = new StudentInfoRequest("00001");
api.getStudentInfo(request).enqueue(new Callback<StudentResponse>() {
    @Override
    public void onResponse(Call<StudentResponse> call, Response<StudentResponse> response) {
        if (response.isSuccessful()) {
            StudentData student = response.body().getData();
            // Display student info
        }
    }
    
    @Override
    public void onFailure(Call<StudentResponse> call, Throwable t) {
        // Handle error
    }
});

// Upload photo (multipart)
File photoFile = new File(photoPath);
RequestBody rollNumber = RequestBody.create(MediaType.parse("text/plain"), "00001");
RequestBody requestFile = RequestBody.create(MediaType.parse("image/jpeg"), photoFile);
MultipartBody.Part body = MultipartBody.Part.createFormData("test_photo", photoFile.getName(), requestFile);

api.uploadPhoto(rollNumber, body).enqueue(callback);

// Upload photo (base64)
Bitmap bitmap = BitmapFactory.decodeFile(photoPath);
ByteArrayOutputStream baos = new ByteArrayOutputStream();
bitmap.compress(Bitmap.CompressFormat.JPEG, 80, baos);
String base64 = "data:image/jpeg;base64," + Base64.encodeToString(baos.toByteArray(), Base64.NO_WRAP);

UploadBase64Request req = new UploadBase64Request("00001", base64);
api.uploadPhotoBase64(req).enqueue(callback);
```

---

## Testing with cURL

```bash
# Get colleges
curl http://localhost:8001/api/biometric/colleges

# Get student info
curl -X POST http://localhost:8001/api/biometric/student/info \
  -H "Content-Type: application/json" \
  -d '{"roll_number":"00001"}'

# Upload photo (multipart)
curl -X POST http://localhost:8001/api/biometric/student/upload-photo \
  -F "roll_number=00001" \
  -F "test_photo=@/path/to/photo.jpg"

# Upload fingerprint template
curl -X POST http://localhost:8001/api/biometric/fingerprint/upload-template \
  -H "Content-Type: application/json" \
  -d '{"roll_number":"00001","fingerprint_template":"template_data_here"}'

# Bulk download
curl -X POST http://localhost:8001/api/biometric/students/bulk-download \
  -H "Content-Type: application/json" \
  -d '{"test_id":4}'
```

---

## Rate Limiting (Production Recommendation)

Implement rate limiting to prevent abuse:

- **Per IP:** 100 requests per minute
- **Per Endpoint:** Specific limits based on resource intensity
- **Bulk Download:** 10 requests per hour

---

## CORS Configuration

For production, configure CORS in `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_origins' => ['https://yourdomain.com'],
'allowed_methods' => ['GET', 'POST'],
'allowed_headers' => ['Content-Type', 'Authorization'],
```

---

## Security Recommendations

1. **API Authentication:** Implement bearer tokens
2. **HTTPS Only:** Enforce SSL in production
3. **Input Validation:** All endpoints validate input
4. **File Size Limits:** Enforce max upload sizes
5. **Rate Limiting:** Prevent abuse
6. **Logging:** Log all API requests
7. **Error Handling:** Don't expose sensitive info in errors

---

**End of API Documentation**
