You are implementing the **Product Image Generator** feature for our Laravel + Vue 3 SaaS Dashboard.
This feature will be a **multi-step wizard** that allows users to generate AI-powered product images using the **Kie.ai API**.

---

### 🎯 Goal

Create a modular, reusable implementation that:

* Lets users input or import product data based on their store type.
* Collects design preferences (category, design style, logo, background, etc.).
* Generates a descriptive AI prompt from user selections.
* Sends the request to Kie.ai’s API.
* Handles image uploads via an internal endpoint and returns their URLs to be used in the AI prompt.

---

### 🧭 Required Steps

#### 1. Create a Vue wizard component

**Path:** `resources/js/pages/client/modules/products/ImageWizard.vue`

This wizard will have **6 steps:**

1. **Store Type Selection**

   * Options: `Salla`, `Zid`, `Other` according to PlatformType enum
   * If `Other` → move to manual product entry form.

2. **Product Selection or Manual Entry**

   * If Salla/Zid: Fetch product list via their API integration and we will implement it later .
   * If Other: Manual form with:

     * Product Name
     * Description
     * Upload Product Image
     * Price (optional)

3. **Category Selection**

   * Dropdown with categories (Perfumes, Clothing, Electronics, Food, Accessories, Furniture, Beauty)
   * Based on selection, show category-specific design templates or image upload fields.

4. **Design Style**

   * Display recommended designs for each category.
   * Allow user to:

     * Choose from templates
     * Upload custom reference image
     * Select background (white, scene, transparent, etc.)

5. **Customization**

   * Upload store logo
   * Specify logo position (“top-left”, “bottom-right”, etc.)
   * Enter text overlay (“Discount 30%”, “New Collection”, etc.)
   * Optional design notes (e.g., “Use soft lighting”, “Minimalist layout”)

6. **Review & Generate**

   * Show summary of all selected options.
   * On “Generate Image”, build a final **AI prompt** and send it to the Kie.ai API.

---

### 🧠 AI Prompt Builder

Create a composable `useAIPromptBuilder()` in `resources/js/composables/`.

This will take all wizard data and construct a descriptive English prompt.
It should adapt automatically to all inputs (dynamic generation).

**Example output:**

```js
const prompt = buildPrompt({
  category: 'Perfumes',
  productName: 'Royal Musk',
  description: 'Oriental perfume with amber and musk notes',
  logoPosition: 'top-left',
  overlayText: 'Special Offer - 30% OFF',
  background: 'white',
  style: 'Luxury perfume design for Saudi market',
  customNotes: 'Use elegant lighting and Arabic-style packaging'
})
```

**Generated prompt (string):**

```
Create a high-quality promotional image for a perfume product named "Royal Musk". 
Show the bottle elegantly on a white background with the store logo positioned at the top-left corner. 
Include overlay text "Special Offer - 30% OFF". 
The perfume has oriental notes of amber and musk. 
Design should look luxurious and suitable for Saudi perfume brands. 
Use elegant lighting and Arabic-style packaging.
```

---

### 🌐 API Integration

Use the following endpoint to create the AI image task:

```bash
curl -X POST "https://api.kie.ai/api/v1/jobs/createTask" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -d '{
    "model": "bytedance/seedream-v4-text-to-image",
    "callBackUrl": "https://your-domain.com/api/callback",
    "input": {
      "prompt": "{generatedPrompt}",
      "image_size": "square_hd",
      "image_resolution": "1K",
      "max_images": 1
    }
  }'
```

In code:

```js
await api.post('/ai/generate', {
  prompt: generatedPrompt,
  image_size: 'square_hd',
  image_resolution: '1K',
  max_images: 1
})
```

Backend endpoint `/api/ai/generate` will handle the request to Kie.ai and return the job status or image URL after completion.

---

### 📤 Image Upload Endpoint

Create an endpoint `/api/uploads` that:

1. Accepts user image uploads (product, logo, or design reference).
2. Stores them temporarily in `storage/app/public/uploads`.
3. Returns the **public URL** for that image.
4. URL is injected into the AI prompt dynamically when available.

**Response:**

```json
{
  "message": "Upload successful",
  "url": "https://your-domain.com/storage/uploads/image123.png"
}
```

Integrate with existing `useImageUpload()` composable for consistency.

---

### ✅ Expected Behavior

* The wizard should guide users visually and save progress at each step.
* The final prompt should **automatically adapt** based on:

  * Category
  * User-uploaded images
  * Text entries
  * Style or background
* The generated image should appear in a preview modal with an option to download or regenerate.

---

### 📦 Deliverables

1. `ImageWizard.vue` (full 6-step wizard)
2. `useAIPromptBuilder.js` composable
3. `/api/ai/generate` backend endpoint
4. `/api/uploads` backend endpoint
5. Integration with `useImageUpload()` and Toast notifications

---

Make sure all texts use i18n `$t()` and support both **dark mode** and **RTL**.
Use only existing components (`TextInput`, `Select`, `ImagePicker`, `Button`, etc.) from the project.
