# Quick Start Checklist

## ✅ Completed Setup Steps

- [x] Installed Laravel Inertia package (composer)
- [x] Installed Vue 3, Inertia client, and vue-i18n (npm)
- [x] Created Vue app entry point with i18n support
- [x] Set up HandleInertiaRequests middleware
- [x] Created main layout component with RTL support
- [x] Created sample pages (Home, Dashboard)
- [x] Created example components (Alert, FormExample)
- [x] Set up translation files (English & Arabic)
- [x] Updated Tailwind config for Vue components
- [x] Added Vite config for Vue + path aliases
- [x] Updated routes to use Inertia::render()
- [x] Updated root Blade template (app.blade.php)
- [x] Built project successfully ✓

## 🚀 Getting Started

### 1. Start Development Server

```bash
cd c:\laragon\www\teachers
npm run dev
```

Then in another terminal:

```bash
php artisan serve
```

Access the application at `http://localhost:8000`

### 2. Create Your First Page

```bash
# Create file: resources/js/Pages/MyPage.vue
```

```vue
<template>
  <MainLayout>
    <h1>{{ $t('app.title') }}</h1>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
</script>
```

### 3. Add Route

Update `routes/web.php`:

```php
Route::get('/my-page', function () {
    return Inertia::render('MyPage');
});
```

### 4. Test Arabic/RTL

- Use the language selector in the navigation
- Or visit any page and click the language dropdown
- Watch the layout flip to RTL automatically

## 📁 Project Structure Reference

```
teachers/
├── app/
│   └── Http/
│       └── Middleware/
│           └── HandleInertiaRequests.php ✓ NEW
├── resources/
│   ├── css/
│   │   └── app.css ✓ UPDATED
│   ├── js/
│   │   ├── app.js ✓ NEW
│   │   ├── Layouts/
│   │   │   └── MainLayout.vue ✓ NEW
│   │   ├── Pages/
│   │   │   ├── Home.vue ✓ NEW
│   │   │   ├── Dashboard.vue ✓ NEW
│   │   │   └── FormExample.vue ✓ NEW
│   │   ├── Components/
│   │   │   └── Alert.vue ✓ NEW
│   │   └── i18n/
│   │       ├── en.json ✓ NEW
│   │       └── ar.json ✓ NEW
│   └── views/
│       └── app.blade.php ✓ NEW
├── routes/
│   └── web.php ✓ UPDATED
├── bootstrap/
│   └── app.php ✓ UPDATED
├── tailwind.config.js ✓ UPDATED
├── vite.config.js ✓ UPDATED
├── package.json ✓ UPDATED
├── composer.json ✓ UPDATED
├── INERTIA_SETUP.md ✓ NEW (Comprehensive guide)
└── MIGRATION_GUIDE.md ✓ NEW (How to migrate existing templates)
```

## 🛠 Common Tasks

### Create a New Page Component

1. Create file: `resources/js/Pages/MyFeature/Index.vue`
2. Add route in `routes/web.php`
3. Use `MainLayout` as wrapper
4. Add translations to `i18n/en.json` and `i18n/ar.json`

### Create a Reusable Component

1. Create file: `resources/js/Components/MyComponent.vue`
2. Import and use in pages
3. Accepts props and slots

### Add Form Validation

Use Inertia's `useForm()`:

```vue
<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
})

const submit = () => {
  form.post('/submit-endpoint')
}
</script>
```

### Add Translation Key

1. Edit `resources/js/i18n/en.json` - add English text
2. Edit `resources/js/i18n/ar.json` - add Arabic text
3. Use in template: `{{ $t('path.to.key') }}`

### Switch Language Programmatically

```vue
<script setup>
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()

const switchToArabic = () => {
  locale.value = 'ar'
  localStorage.setItem('locale', 'ar')
  document.documentElement.lang = 'ar'
  document.documentElement.dir = 'rtl'
}
</script>
```

## 🎨 Tailwind CSS Classes You'll Use Often

### Spacing (RTL-aware)

```html
<!-- These automatically flip in RTL -->
<div class="ml-4 rtl:ml-0 rtl:mr-4">Content</div>
<div class="flex space-x-4 rtl:space-x-reverse">...</div>
```

### Layout

```html
<div class="max-w-7xl mx-auto px-4">Container</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">2 columns</div>
<div class="flex justify-between items-center">Row</div>
```

### Colors

```html
<div class="bg-blue-600 text-white">Primary</div>
<div class="bg-gray-100 text-gray-900">Secondary</div>
<div class="bg-red-50 border border-red-200">Alert</div>
```

## 🐛 Debugging Tips

### View shared data in component

```vue
<script setup>
import { usePage } from '@inertiajs/vue3'

const page = usePage()
console.log(page.props) // See all shared data
</script>
```

### Check locale

```javascript
// In browser console
localStorage.getItem('locale')
document.documentElement.dir
document.documentElement.lang
```

### Check Inertia response

```javascript
// In browser Network tab
// Look for XHR requests to your routes
// They return component name and props as JSON
```

## 📚 Learning Resources

- **Inertia.js**: https://inertiajs.com/installation/laravel
- **Vue 3**: https://vuejs.org/guide/
- **Tailwind**: https://tailwindcss.com/docs
- **vue-i18n**: https://vue-i18n.intlify.dev/
- **Heroicons**: https://heroicons.com/

## ⚡ Performance Tips

1. **Lazy load components** - Use dynamic imports for heavy components
2. **Use v-for :key** - Always include key prop in lists
3. **Limit watchers** - Watch only what you need
4. **Optimize images** - Compress before uploading
5. **Cache translations** - localStorage persists locale choice

## 🚨 Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Components not loading | Check file path matches component name exactly |
| RTL not working | Verify locale is 'ar' and restart dev server |
| Translations blank | Add key to both en.json and ar.json files |
| Styles not updating | Run `npm run build` or restart dev server |
| Form not submitting | Check route exists and form.post() URL is correct |
| 404 on page load | Verify component path in Inertia::render() call |

## 📋 Before Going to Production

- [ ] Test all pages in both English and Arabic
- [ ] Verify RTL layouts render correctly
- [ ] Run `npm run build` to generate production assets
- [ ] Check console for any JavaScript errors
- [ ] Test form submissions and validations
- [ ] Update .env with production settings
- [ ] Run migrations if database changes needed
- [ ] Set up CI/CD if available

## 🎯 Next Major Steps

1. **Convert All Templates** - Migrate remaining Blade templates to Vue
2. **Add Authentication Pages** - Create login/register components
3. **Build API Responses** - Return proper JSON from controllers
4. **Add State Management** - Install Pinia if needed for complex state
5. **Set Up Testing** - Add Vue component tests
6. **Deploy** - Push to production server

---

**Need Help?** Refer to:
- `INERTIA_SETUP.md` - Detailed component creation guide
- `MIGRATION_GUIDE.md` - How to convert Blade templates to Vue

You're ready to start building with Inertia.js + Vue 3! 🚀
