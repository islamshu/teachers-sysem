# 🚀 Testing & Deployment Guide

## Pre-Deployment Checklist

### ✅ Database & Setup
- [ ] Run migrations: `php artisan migrate` (if not already done)
- [ ] Verify `users` table has `is_admin` column
- [ ] Verify `teacher_profiles` table exists with status column
- [ ] Create storage symlink: `php artisan storage:link`

### ✅ Build & Assets
- [ ] Run `npm run build` successfully
- [ ] Assets compiled without errors (check `public/build/`)
- [ ] Manifest file exists: `public/build/manifest.json`

### ✅ Configuration
- [ ] `FILESYSTEM_DISK=public` in .env
- [ ] `APP_ENV=production` or `local` as needed
- [ ] Redis/Cache configured (if using)

---

## 🧪 Manual Testing Flow

### 1. Register New User (Teacher)
```
Steps:
1. Visit /register
2. Fill in name, email, password
3. Submit form
4. Verify email (or skip if not required)
5. Login
```

**Expected Result:**
- User is created
- Redirected to /dashboard
- Can see /teacher-profile/create link

---

### 2. Create Teacher Profile
```
Steps:
1. Navigate to /teacher-profile/create
2. Upload photo (optional)
3. Fill in personal info:
   - Name, Phone, Birth Date, Gender
   - Residence Place
4. Select Subject (dropdown)
5. Check Grade(s) (checkboxes)
6. Fill Academic Info:
   - Experience Years (number)
   - Qualification (text)
7. Fill Bio (textarea)
8. Submit form
```

**Expected Result:**
- Profile created successfully
- Redirect to /dashboard with success message
- Message: "تم حفظ بيانات المدرس بنجاح"
- Photo stored in storage/teachers/

---

### 3. Access Public Teachers Listing
```
Steps:
1. Visit /teachers (no auth required)
2. Should see EMPTY (no approved teachers yet)
3. Test filters:
   - Select a subject
   - Select a grade
   - Enter search text
   - Click search button
4. Should still be empty
```

**Expected Result:**
- Page loads without auth
- No teachers visible (all pending)
- Filters work (form submits)

---

### 4. Login as Admin
```
Steps:
1. Create admin user in database:
   INSERT INTO users (name, email, password, is_admin) 
   VALUES ('Admin', 'admin@example.com', hashed_password, 1);
   
   OR use Tinker:
   php artisan tinker
   > User::create(['name'=>'Admin','email'=>'admin@test.com','password'=>bcrypt('password'),'is_admin'=>true])
   
2. Login with admin credentials
3. Navigate to /admin/teachers
```

**Expected Result:**
- Access granted to /admin/teachers
- Should see list of PENDING teachers
- Table shows: Name, Subject, Grades, Experience, Phone
- Action buttons visible: approve (✓), reject (✕), view (👁)

---

### 5. Admin Approves Teacher
```
Steps:
1. On /admin/teachers page
2. Click "view" button (👁) for a teacher
3. Review all details
4. Click "قبول المدرس" (Approve) button
5. Confirm in dialog
6. Page should redirect back to /admin/teachers
7. Success message: "تم قبول المدرس بنجاح"
```

**Expected Result:**
- Teacher status changed to 'approved'
- Teacher removed from pending list
- Flash message displayed
- Can now see teacher in public listing

---

### 6. Public Now Sees Approved Teacher
```
Steps:
1. Visit /teachers (public page)
2. Should see the approved teacher card
3. Test search/filters work
4. Click "التواصل مع المدرس" (Contact)
```

**Expected Result:**
- Teacher appears in grid
- Card shows: photo, name, subject, grades, experience, location, phone
- Contact button opens phone dialer

---

### 7. Admin Rejects Teacher
```
Steps:
1. Go back to /admin/teachers
2. Create another teacher profile (as different user)
3. On admin page, click reject button (✕)
4. Confirm in dialog
5. Teacher removed from pending
```

**Expected Result:**
- Teacher status changed to 'rejected'
- Teacher not visible in pending list
- Teacher not visible in public listing

---

### 8. Teacher Edits Profile
```
Steps:
1. Login as teacher who created profile
2. Navigate to /teacher-profile/edit
3. Change some fields
4. Update photo
5. Submit form
```

**Expected Result:**
- Profile updated successfully
- Message: "تم تحديث بيانات المدرس بنجاح"
- Changes visible in form

---

### 9. Arabic/RTL Testing
```
Steps:
1. Visit any page
2. Look for language switcher (in top nav)
3. Click to switch to Arabic
4. Page should change:
   - Direction: rtl
   - Text: Arabic
   - Layout: mirrored
5. All UI elements should align to right
```

**Expected Result:**
- Smooth language switch
- RTL layout works
- No layout breaks
- Text is readable

---

## 🐛 Common Issues & Solutions

### Issue: 404 on /admin/teachers
**Solution:**
- Check user has `is_admin = true` in database
- Verify AdminMiddleware is registered in bootstrap/app.php
- Check routes/web.php has admin routes

### Issue: Photo not uploading
**Solution:**
- Verify `public/storage` symlink exists
- Check `FILESYSTEM_DISK=public` in .env
- File permissions on storage/ directory

### Issue: Arabic text not showing
**Solution:**
- Check browser encoding is UTF-8
- Verify i18n files (en.json, ar.json) have correct keys
- Check database collation is utf8mb4

### Issue: Components not rendering
**Solution:**
- Run `npm run build` again
- Clear browser cache (Ctrl+Shift+Delete)
- Check manifest.json in public/build/

### Issue: Form not submitting
**Solution:**
- Open browser console, check for JS errors
- Verify CSRF token is present
- Check form action/method are correct

---

## 📊 Test Cases Checklist

### Teacher Registration Flow
- [ ] Create account
- [ ] Fill profile form
- [ ] Upload photo
- [ ] See pending status
- [ ] Edit profile
- [ ] Update photo

### Admin Approval Flow
- [ ] Login as admin
- [ ] View pending teachers
- [ ] Approve teacher
- [ ] Teacher appears in public listing
- [ ] Reject teacher
- [ ] Teacher disappears from public

### Public Listing
- [ ] See approved teachers only
- [ ] Filter by subject
- [ ] Filter by grade
- [ ] Search by name
- [ ] View teacher details
- [ ] Contact teacher

### Language & RTL
- [ ] Switch to Arabic
- [ ] Page layout mirrors
- [ ] Text is readable
- [ ] Forms work in RTL
- [ ] Navigation works in RTL
- [ ] Switch back to English

### Form Validation
- [ ] Required fields show errors
- [ ] Invalid email rejected
- [ ] Phone number format
- [ ] File upload validation
- [ ] Error messages display correctly

---

## 🔧 Development Server

### Terminal 1: Start Vue Dev Server
```bash
npm run dev
```
Output should show:
```
  VITE v8.0.16 ready in 123 ms

  ➜  Local:   http://localhost:5173/
  ➜  press h to show help
```

### Terminal 2: Start Laravel Server
```bash
php artisan serve
```
Output should show:
```
   INFO  Server running on [http://127.0.0.1:8000].
```

### Terminal 3: Run Tests (Optional)
```bash
php artisan test
```

---

## 🚀 Production Build

### Step 1: Build Assets
```bash
npm run build
```

### Step 2: Run Migrations
```bash
php artisan migrate --force
```

### Step 3: Create Storage Link
```bash
php artisan storage:link
```

### Step 4: Optimize
```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
```

### Step 5: Verify Build
```bash
# Check public/build/ exists and has assets
ls -la public/build/

# Should output:
# - manifest.json
# - assets/app-*.css
# - assets/app-*.js
# - assets/*.js for each component
```

---

## 📱 Responsive Testing

Test all pages on:
- [ ] Desktop (1920px)
- [ ] Tablet (768px)
- [ ] Mobile (375px)

Browsers:
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

---

## 🔒 Security Checklist

- [ ] CSRF token in forms
- [ ] Authentication required for teacher profile
- [ ] Admin middleware on admin routes
- [ ] Photo upload validation (size, type)
- [ ] SQL injection prevention (Laravel ORM)
- [ ] XSS prevention (Vue templates)
- [ ] No secrets in code
- [ ] Proper error handling (no stack traces in production)

---

## 📝 Test Data

### Create Admin User
```bash
php artisan tinker
User::create([
    'name' => 'Admin User',
    'email' => 'admin@test.com',
    'password' => bcrypt('password'),
    'is_admin' => true,
    'email_verified_at' => now(),
])
```

### Create Teacher User
```bash
php artisan tinker
User::create([
    'name' => 'Teacher User',
    'email' => 'teacher@test.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now(),
])
```

### Create Subject & Grades
```bash
php artisan tinker
Subject::create(['name' => 'Mathematics'])
Subject::create(['name' => 'English'])
Grade::create(['name' => 'Grade 1'])
Grade::create(['name' => 'Grade 2'])
```

---

## ✅ Final Verification

Before going live:

1. **Functionality**
   - All pages load
   - Forms work
   - Approvals/rejections work
   - Filtering works
   - Search works

2. **Performance**
   - Pages load quickly
   - Images optimized
   - Assets minified
   - No console errors

3. **Security**
   - Auth working
   - Admin-only routes protected
   - Forms have CSRF tokens
   - File uploads secure

4. **UX**
   - No layout breaks
   - Arabic/English working
   - Mobile responsive
   - Accessible (keyboard nav)

5. **Data**
   - Photos stored correctly
   - Database queries optimized
   - No N+1 queries

---

## 📞 Support

If you encounter issues:

1. Check browser console for JS errors
2. Check Laravel logs: `tail -f storage/logs/laravel.log`
3. Run migrations if needed: `php artisan migrate`
4. Clear caches: `php artisan cache:clear`
5. Rebuild assets: `npm run build`

---

**Status:** ✅ Ready for Testing and Deployment
