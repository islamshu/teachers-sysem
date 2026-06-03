# Inertia.js + Vue 3 Setup Guide

## Overview
This project has been successfully converted from Blade templating to Inertia.js with Vue 3, Tailwind CSS, and full Arabic RTL support.

## Directory Structure

```
resources/
├── js/
│   ├── app.js                 # Vue app entry point
│   ├── i18n/                  # Localization files
│   │   ├── en.json           # English translations
│   │   └── ar.json           # Arabic translations
│   ├── Layouts/              # Layout components
│   │   └── MainLayout.vue    # Main layout with navigation
│   ├── Pages/                # Page components
│   │   ├── Dashboard.vue     # Dashboard page
│   │   └── Home.vue          # Home page
│   └── Components/           # Reusable components
├── css/
│   └── app.css               # Tailwind + RTL CSS
└── views/
    └── app.blade.php         # Root Blade template for Inertia
```

## Key Features

### 1. **Inertia.js Integration**
- Server-side routing with Vue components
- Automatic component resolving
- Type-safe data passing from Laravel to Vue

### 2. **Vue 3 with Composition API**
- Modern Vue 3 setup script syntax
- Reactive components

### 3. **Tailwind CSS**
- Updated content paths for Vue components
- RTL utilities built-in
- Responsive design classes

### 4. **Arabic RTL Support**
- Automatic RTL/LTR detection based on locale
- Document direction changes automatically
- RTL-aware spacing utilities

### 5. **i18n Localization**
- Client-side translations with vue-i18n
- Language switcher in navigation
- Persists locale to localStorage

## Creating New Pages

### Step 1: Create a Vue Component

Create a new file in `resources/js/Pages/MyPage.vue`:

```vue
<template>
  <MainLayout>
    <div class="bg-white rounded-lg shadow p-6">
      <h1 class="text-3xl font-bold">{{ $t('app.title') }}</h1>
      <p>Your content here</p>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
</script>
```

### Step 2: Add a Route

Update `routes/web.php`:

```php
Route::get('/my-page', function () {
    return Inertia::render('MyPage');
});
```

### Step 3: Add Translations

Update translation files:

**en.json:**
```json
{
  "app": {
    "title": "My Page Title"
  }
}
```

**ar.json:**
```json
{
  "app": {
    "title": "عنوان صفحتي"
  }
}
```

## Creating Reusable Components

Create in `resources/js/Components/MyComponent.vue`:

```vue
<template>
  <div class="my-component">
    <h2>{{ title }}</h2>
    <slot />
  </div>
</template>

<script setup>
defineProps({
  title: String,
})
</script>
```

Use in pages:

```vue
<template>
  <MainLayout>
    <MyComponent title="Hello">
      Component content here
    </MyComponent>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import MyComponent from '@/Components/MyComponent.vue'
</script>
```

## Passing Data from Laravel to Vue

### In Routes/Controllers:

```php
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'user' => auth()->user(),
        'stats' => [
            'courses' => 5,
            'students' => 100,
        ],
    ]);
});
```

### In Vue Component:

```vue
<template>
  <div>
    <h1>{{ user.name }}</h1>
    <p>Courses: {{ stats.courses }}</p>
  </div>
</template>

<script setup>
defineProps({
  user: Object,
  stats: Object,
})
</script>
```

## RTL Support

The layout automatically switches between RTL and LTR based on the selected locale. Use Tailwind's RTL utilities:

```vue
<div class="flex space-x-4 rtl:space-x-reverse">
  <!-- Content -->
</div>
```

## Language Switching

The language switcher is built into the navigation. It:
1. Changes the locale globally
2. Updates document direction
3. Persists selection to localStorage
4. Re-renders all components with new translations

## Development Commands

```bash
# Start development server with Vite
npm run dev

# Build for production
npm run build

# Run Laravel server
php artisan serve

# Start complete dev environment (Laravel + Vite + Queue + Logs)
npm run dev:full  # via composer.json
```

## Middleware

### HandleInertiaRequests

Located at `app/Http/Middleware/HandleInertiaRequests.php`

Shares common data across all Inertia responses:
- User authentication data
- Current locale
- Flash messages

Modify this to share additional global data.

## Translation Files

- English: `resources/js/i18n/en.json`
- Arabic: `resources/js/i18n/ar.json`

Add new keys as needed. They'll be available in templates via `{{ $t('path.to.key') }}`

## Helpful Links

- [Inertia.js Docs](https://inertiajs.com)
- [Vue 3 Docs](https://vuejs.org)
- [Tailwind CSS Docs](https://tailwindcss.com)
- [Vue i18n Docs](https://vue-i18n.intlify.dev)

## Troubleshooting

### Components not loading?
- Ensure the file path matches the component name
- Check browser console for errors
- Run `npm run build` again

### RTL not working?
- Verify locale is set to 'ar'
- Check `document.documentElement.dir` in browser console
- Ensure parent elements have `dir` attribute

### Translations not showing?
- Verify keys exist in both en.json and ar.json
- Check localStorage for correct locale setting
- Restart dev server after changing i18n files
