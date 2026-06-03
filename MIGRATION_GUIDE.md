# Migration Guide: Blade to Inertia.js + Vue

## Overview of Changes

Your Laravel project has been converted from **Blade templating** to **Inertia.js with Vue 3**, complete with **Tailwind CSS** and **Arabic RTL support**.

## What Changed

### Backend (Laravel)

| Before | After |
|--------|-------|
| `view('welcome')` | `Inertia::render('Home')` |
| `resources/views/` | `resources/js/Pages/` |
| Server-side data in templates | Props passed to Vue components |
| Direct HTML output | Inertia-based SPA |

### Frontend (JavaScript/CSS)

| Before | After |
|--------|-------|
| Alpine.js for interactivity | Vue 3 with Composition API |
| Blade echo `{{ }}` | Vue template `{{ }}` + i18n |
| HTML files | `.vue` Single File Components |
| No built-in i18n | vue-i18n for translations |

### New Files Created

```
✓ resources/js/app.js                    - Vue app entry point
✓ resources/js/Layouts/MainLayout.vue    - Main layout component
✓ resources/js/Pages/Home.vue            - Home page component
✓ resources/js/Pages/Dashboard.vue       - Dashboard page component
✓ resources/js/Pages/FormExample.vue     - Form example
✓ resources/js/Components/Alert.vue      - Reusable alert component
✓ resources/js/i18n/en.json             - English translations
✓ resources/js/i18n/ar.json             - Arabic translations
✓ resources/views/app.blade.php          - Root Inertia template
✓ app/Http/Middleware/HandleInertiaRequests.php - Inertia middleware
✓ tailwind.config.js                     - Updated for Vue components
✓ vite.config.js                         - Vue and alias support
✓ INERTIA_SETUP.md                       - Detailed setup guide
```

### Modified Files

```
✓ routes/web.php                 - Routes now use Inertia::render()
✓ bootstrap/app.php              - Middleware registered
✓ resources/js/app.js            - Replaced Alpine with Vue + Inertia
✓ resources/css/app.css          - Added RTL utilities
✓ package.json                   - Added Vue, Inertia, i18n dependencies
✓ composer.json                  - Added inertiajs/inertia-laravel
```

## How to Convert Existing Blade Templates

### Example: Converting a Blade Template to Vue

**Before (Blade):**
```blade
<!-- resources/views/teachers/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Teachers</h1>
    <ul>
        @foreach($teachers as $teacher)
            <li>{{ $teacher->name }}</li>
        @endforeach
    </ul>
</div>
@endsection
```

**After (Vue):**
```vue
<!-- resources/js/Pages/Teachers/Index.vue -->
<template>
  <MainLayout>
    <div class="container mx-auto">
      <h1 class="text-3xl font-bold mb-4">{{ $t('navigation.teachers') }}</h1>
      <ul class="space-y-2">
        <li v-for="teacher in teachers" :key="teacher.id" class="p-2 bg-gray-50 rounded">
          {{ teacher.name }}
        </li>
      </ul>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'

defineProps({
  teachers: Array,
})
</script>
```

**Route Update:**
```php
// routes/web.php - Before
Route::get('/teachers', [TeacherController::class, 'index']);

// routes/web.php - After
Route::get('/teachers', function () {
    return Inertia::render('Teachers/Index', [
        'teachers' => Teacher::all(),
    ]);
});
```

## Key Concepts

### 1. **Inertia Rendering**

All pages now use `Inertia::render()`:

```php
// Controller
public function show(Teacher $teacher)
{
    return Inertia::render('Teachers/Show', [
        'teacher' => $teacher,
        'courses' => $teacher->courses,
    ]);
}
```

### 2. **Props and Data**

Data passed from Laravel becomes Vue props:

```php
// Laravel
return Inertia::render('Dashboard', [
    'stats' => ['users' => 50],
]);
```

```vue
<!-- Vue -->
<script setup>
defineProps({
  stats: Object,
})
</script>

<template>
  <p>Users: {{ stats.users }}</p>
</template>
```

### 3. **Shared Data**

Global data available to all pages via `HandleInertiaRequests` middleware:

```php
// app/Http/Middleware/HandleInertiaRequests.php
public function share(Request $request): array
{
    return [
        'auth' => ['user' => $request->user()],
        'locale' => session('locale', 'en'),
    ];
}
```

Access in any component:

```vue
<template>
  <p v-if="$page.props.auth.user">
    Welcome {{ $page.props.auth.user.name }}
  </p>
</template>
```

### 4. **Form Handling**

Inertia provides form helpers:

```vue
<template>
  <form @submit.prevent="submit">
    <input v-model="form.name" />
    <p v-if="form.errors.name">{{ form.errors.name }}</p>
    <button :disabled="form.processing">Save</button>
  </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
})

const submit = () => {
  form.post('/teachers')
}
</script>
```

### 5. **Navigation Links**

Use Inertia's `<Link>` component:

```vue
<template>
  <Link href="/teachers" class="text-blue-600 hover:text-blue-800">
    Teachers
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
```

## Localization (i18n) for Arabic

### Adding New Translation Keys

**resources/js/i18n/en.json:**
```json
{
  "validation": {
    "required": "This field is required",
    "email": "Please enter a valid email"
  }
}
```

**resources/js/i18n/ar.json:**
```json
{
  "validation": {
    "required": "هذا الحقل مطلوب",
    "email": "يرجى إدخال بريد إلكتروني صحيح"
  }
}
```

### Using in Templates

```vue
<template>
  <p v-if="form.errors.email">{{ $t('validation.email') }}</p>
  <p>{{ $t('validation.required') }}</p>
</template>
```

### Switching Language

The language switcher in MainLayout automatically handles:
1. Changing locale
2. Updating document direction (RTL/LTR)
3. Persisting to localStorage
4. Re-rendering components

## Common Patterns

### Pattern 1: Page with List

```vue
<template>
  <MainLayout>
    <div class="space-y-4">
      <div v-for="item in items" :key="item.id" class="bg-white p-4 rounded shadow">
        {{ item.name }}
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'

defineProps({
  items: Array,
})
</script>
```

### Pattern 2: Form with Validation

```vue
<template>
  <MainLayout>
    <form @submit.prevent="submit" class="max-w-md mx-auto">
      <input
        v-model="form.email"
        type="email"
        class="w-full px-4 py-2 border rounded"
        :class="{ 'border-red-500': form.errors.email }"
      />
      <p v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</p>
      <button type="submit" :disabled="form.processing">Submit</button>
    </form>
  </MainLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

const form = useForm({ email: '' })

const submit = () => {
  form.post('/subscribe')
}
</script>
```

### Pattern 3: Authenticated Routes

```php
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return Inertia::render('Profile/Show', [
            'user' => auth()->user(),
        ]);
    });
});
```

## RTL Implementation Details

### How RTL Works

1. **Locale Detection**: When user selects Arabic, locale is set to 'ar'
2. **Document Direction**: `document.documentElement.dir = 'rtl'`
3. **HTML Lang**: `document.documentElement.lang = 'ar'`
4. **CSS Classes**: Tailwind utilities like `rtl:space-x-reverse` apply

### RTL in Components

```vue
<!-- Flex that reverses in RTL -->
<div class="flex space-x-4 rtl:space-x-reverse">
  <div>First</div>
  <div>Second</div>
</div>

<!-- Margins that flip in RTL -->
<div class="ml-4 rtl:ml-0 rtl:mr-4">Content</div>
```

### Testing RTL

1. Open browser DevTools
2. In Console:
   ```javascript
   document.documentElement.dir = 'rtl'
   document.documentElement.lang = 'ar'
   ```
3. Or use the language switcher in navigation

## Troubleshooting

### Issue: Components not rendering
**Solution**: Check file paths match component names. Components in `Pages/` must match the route name.

### Issue: RTL not applying
**Solution**: Ensure `document.documentElement.dir` is set. Check that locale is 'ar'.

### Issue: Translations missing
**Solution**: Add key to both en.json and ar.json. Restart dev server.

### Issue: Assets not loading
**Solution**: Run `npm run build` and ensure `VITE_ASSET_URL` is correct in `.env`.

## Next Steps

1. **Migrate Existing Pages**: Convert remaining Blade templates to Vue components
2. **Create Reusable Components**: Extract common HTML patterns to Vue components
3. **Add Form Validation**: Use Laravel's validation with Inertia forms
4. **Set Up State Management**: Consider Pinia if needed for complex state
5. **Test RTL**: Verify all pages work in both English and Arabic

## Resources

- [Inertia.js Documentation](https://inertiajs.com)
- [Vue 3 Documentation](https://vuejs.org)
- [Tailwind CSS RTL Guide](https://tailwindcss.com/docs/rtl)
- [Vue i18n Guide](https://vue-i18n.intlify.dev/)

---

**Project successfully converted from Blade to Inertia.js + Vue 3 with Arabic RTL support!** 🎉
