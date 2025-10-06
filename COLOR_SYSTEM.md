# Dashboard Color System

## Overview

All dashboard colors are managed from a single configuration file: `resources/js/config/colors.js`

## How to Change Colors

1. Open `resources/js/config/colors.js`
2. Modify the color values in the `dashboardColors` object
3. Save the file
4. Run `npm run build` or `npm run dev` to rebuild CSS

## Available Color Classes

### Primary Colors
```vue
<div class="bg-primary">Primary background</div>
<div class="bg-primary-light">Lighter primary</div>
<div class="bg-primary-dark">Darker primary</div>
<div class="text-primary">Primary text</div>
<div class="border-primary">Primary border</div>

<!-- With shades -->
<div class="bg-primary-100">Very light primary</div>
<div class="bg-primary-500">Default primary</div>
<div class="bg-primary-900">Very dark primary</div>
```

### Secondary Colors
```vue
<div class="bg-secondary">Secondary background</div>
<div class="text-secondary-dark">Dark secondary text</div>
<div class="border-secondary-light">Light secondary border</div>
```

### Status Colors
```vue
<!-- Success -->
<div class="bg-success text-white">Success message</div>
<div class="text-success-dark">Success text</div>

<!-- Warning -->
<div class="bg-warning text-white">Warning message</div>

<!-- Danger/Error -->
<div class="bg-danger text-white">Error message</div>
```

### Accent Colors
```vue
<div class="bg-accent">Accent background</div>
<div class="text-accent-dark">Accent text</div>
```

### Special Areas
```vue
<!-- Sidebar -->
<div class="bg-sidebar-bg text-sidebar-text">Sidebar content</div>
<div class="hover:bg-sidebar-hover">Sidebar menu item</div>

<!-- Topbar -->
<div class="bg-topbar-bg border-topbar-border">Topbar content</div>
```

### Dark Mode
All colors work with dark mode using the `dark:` prefix:
```vue
<div class="bg-white dark:bg-gray-800">
<div class="text-gray-900 dark:text-gray-100">
<div class="border-primary dark:border-primary-light">
```

## Quick Theme Change

In `colors.js`, uncomment one of the preset themes at the bottom:

```javascript
// Blue Theme
export const dashboardColors = {
    primary: { DEFAULT: '#3b82f6', light: '#60a5fa', dark: '#2563eb' },
    secondary: { DEFAULT: '#06b6d4', light: '#22d3ee', dark: '#0891b2' },
    // ... copy full color definitions from the main export
}
```

Or create your own by changing the hex values in the main `dashboardColors` object.

## Examples in Components

### Buttons
```vue
<!-- Primary button -->
<button class="bg-primary hover:bg-primary-dark text-white">
    Click me
</button>

<!-- Secondary button -->
<button class="bg-secondary hover:bg-secondary-dark text-white">
    Click me
</button>

<!-- Success button -->
<button class="bg-success hover:bg-success-dark text-white">
    Save
</button>
```

### Cards
```vue
<div class="bg-white dark:bg-gray-800 border border-primary-200">
    <h3 class="text-primary-600">Card Title</h3>
    <p class="text-gray-600 dark:text-gray-300">Content</p>
</div>
```

### Badges
```vue
<!-- Success badge -->
<span class="bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400">
    Active
</span>

<!-- Warning badge -->
<span class="bg-warning-100 text-warning-800">
    Pending
</span>

<!-- Danger badge -->
<span class="bg-danger-100 text-danger-800">
    Inactive
</span>
```

### Gradients
```vue
<!-- Primary gradient -->
<div class="bg-gradient-to-r from-primary to-secondary">
    Gradient background
</div>

<!-- Accent gradient -->
<div class="bg-gradient-to-br from-accent to-warning">
    Warm gradient
</div>
```

## Tips

1. **Consistency**: Use `bg-primary` instead of hardcoded colors like `bg-indigo-600`
2. **Dark Mode**: Always provide dark mode variants for better UX
3. **Semantic Colors**: Use status colors (success, warning, danger) for appropriate contexts
4. **Shades**: Use numbered shades (100-900) for subtle variations
5. **Hover States**: Use darker shades on hover: `hover:bg-primary-dark`

## Rebuild CSS

After changing colors in `colors.js`, rebuild your CSS:

```bash
# Development
npm run dev

# Production
npm run build
```
