import { dashboardColors } from './resources/js/config/colors.js'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: dashboardColors,
    },
  },
  plugins: [
    require('tailwindcss-rtl'),
  ],
}
