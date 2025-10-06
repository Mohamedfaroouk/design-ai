# Restart Dev Server Required

When you change `tailwind.config.js` or `colors.js`, you MUST restart the dev server:

## Steps:

1. **Stop the current dev server:**
   - Press `Ctrl + C` in the terminal running `npm run dev`

2. **Start it again:**
   ```bash
   npm run dev
   ```

3. **Clear browser cache (if needed):**
   - Press `Ctrl + Shift + R` (hard refresh)
   - Or open DevTools → Network tab → Check "Disable cache"

## Why?

Vite/Tailwind only hot-reloads `.vue`, `.js`, and `.css` files.
Configuration files like `tailwind.config.js` and imported modules require a full restart.
