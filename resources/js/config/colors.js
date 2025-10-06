/**
 * Dashboard Color Configuration
 *
 * Change all dashboard colors from this single file.
 * These colors will be available as Tailwind classes throughout the app.
 *
 * Usage examples:
 * - bg-primary → Primary background
 * - text-primary → Primary text color
 * - border-primary → Primary border color
 * - bg-primary-light → Lighter shade of primary
 * - hover:bg-primary-dark → Darker shade on hover
 */

export const dashboardColors = {
    // Primary Brand Colors - Main theme colors (Orange)
    primary: {
        DEFAULT: '#FF7B00',  // Main orange
        light: '#FFA040',     // Lighter orange
        dark: '#E96D00',      // Darker orange
        50: '#FFF4E6',        // Very light orange tint
        100: '#FFE8CC',       // Light orange tint
        200: '#FFD199',       // Lighter orange
        300: '#FFBA66',       // Light-medium orange
        400: '#FFA040',       // Medium-light orange
        500: '#FF7B00',       // Base orange (DEFAULT)
        600: '#E96D00',       // Medium-dark orange
        700: '#CC5F00',       // Dark orange
        800: '#995200',       // Darker orange
        900: '#663700',       // Very dark orange
    },

    // Secondary Accent Colors (Yellow/Gold)
    secondary: {
        DEFAULT: '#F0C400',  // Gold/Yellow
        light: '#FFD633',     // Lighter gold
        dark: '#D9B000',      // Darker gold
        50: '#FFFCEB',        // Very light yellow tint
        100: '#FFF9D6',       // Light yellow tint
        200: '#FFF3AD',       // Lighter yellow
        300: '#FFED85',       // Light-medium yellow
        400: '#FFD633',       // Medium-light yellow
        500: '#F0C400',       // Base gold (DEFAULT)
        600: '#D9B000',       // Medium-dark gold
        700: '#B39600',       // Dark gold
        800: '#8C7500',       // Darker gold
        900: '#665400',       // Very dark gold
    },

    // Success Colors
    success: {
        DEFAULT: '#10b981',  // green-500
        light: '#34d399',     // green-400
        dark: '#059669',      // green-600
        50: '#ecfdf5',
        100: '#d1fae5',
        200: '#a7f3d0',
        300: '#6ee7b7',
        400: '#34d399',
        500: '#10b981',
        600: '#059669',
        700: '#047857',
        800: '#065f46',
        900: '#064e3b',
    },

    // Warning Colors
    warning: {
        DEFAULT: '#f59e0b',  // amber-500
        light: '#fbbf24',     // amber-400
        dark: '#d97706',      // amber-600
        50: '#fffbeb',
        100: '#fef3c7',
        200: '#fde68a',
        300: '#fcd34d',
        400: '#fbbf24',
        500: '#f59e0b',
        600: '#d97706',
        700: '#b45309',
        800: '#92400e',
        900: '#78350f',
    },

    // Error/Danger Colors
    danger: {
        DEFAULT: '#ef4444',  // red-500
        light: '#f87171',     // red-400
        dark: '#dc2626',      // red-600
        50: '#fef2f2',
        100: '#fee2e2',
        200: '#fecaca',
        300: '#fca5a5',
        400: '#f87171',
        500: '#ef4444',
        600: '#dc2626',
        700: '#b91c1c',
        800: '#991b1b',
        900: '#7f1d1d',
    },

    // Accent Gradient Colors (for special elements)
    accent: {
        DEFAULT: '#f97316',  // orange-500
        light: '#fb923c',     // orange-400
        dark: '#ea580c',      // orange-600
        50: '#fff7ed',
        100: '#ffedd5',
        200: '#fed7aa',
        300: '#fdba74',
        400: '#fb923c',
        500: '#f97316',
        600: '#ea580c',
        700: '#c2410c',
        800: '#9a3412',
        900: '#7c2d12',
    },

    // Sidebar Colors
    sidebar: {
        bg: '#4f46e5',           // Main sidebar background (indigo-600)
        'bg-dark': '#1f2937',    // Dark mode sidebar background (gray-800)
        text: '#ffffff',         // Sidebar text color
        'text-light': '#e0e7ff', // Lighter sidebar text (indigo-100)
        hover: 'rgba(255, 255, 255, 0.1)', // Hover state
        active: 'rgba(255, 255, 255, 0.2)', // Active menu item
    },

    // Topbar Colors
    topbar: {
        bg: '#ffffff',           // Main topbar background
        'bg-dark': '#1f2937',    // Dark mode topbar background (gray-800)
        border: '#e0e7ff',       // Topbar border (indigo-100)
        'border-dark': '#374151', // Dark mode border (gray-700)
    },
}

/**
 * Quick Theme Presets
 * Uncomment one of these to quickly change the entire dashboard theme
 */

// Blue Theme
// export const dashboardColors = {
//     primary: { DEFAULT: '#3b82f6', light: '#60a5fa', dark: '#2563eb' },
//     secondary: { DEFAULT: '#06b6d4', light: '#22d3ee', dark: '#0891b2' },
//     // ... rest of colors
// }

// Purple Theme
// export const dashboardColors = {
//     primary: { DEFAULT: '#a855f7', light: '#c084fc', dark: '#9333ea' },
//     secondary: { DEFAULT: '#ec4899', light: '#f472b6', dark: '#db2777' },
//     // ... rest of colors
// }

// Green Theme
// export const dashboardColors = {
//     primary: { DEFAULT: '#10b981', light: '#34d399', dark: '#059669' },
//     secondary: { DEFAULT: '#14b8a6', light: '#2dd4bf', dark: '#0d9488' },
//     // ... rest of colors
// }
