/**
 * AI Prompt Builder Composable
 *
 * Generates descriptive prompts for AI image generation based on product details,
 * design preferences, and customization options.
 */

/**
 * Category-specific design templates
 */
const categoryTemplates = {
    perfumes: {
        style: 'elegant luxury perfume bottle photography',
        lighting: 'soft warm lighting with elegant reflections',
        background: 'minimalist with subtle gradients',
        elements: 'focus on the bottle design and packaging',
    },
    clothing: {
        style: 'professional fashion product photography',
        lighting: 'natural soft lighting with fabric texture emphasis',
        background: 'clean neutral or contextual setting',
        elements: 'highlight fabric quality and design details',
    },
    electronics: {
        style: 'modern tech product photography',
        lighting: 'sharp studio lighting with precise shadows',
        background: 'pure white or tech-inspired gradient',
        elements: 'emphasize sleek design and screen displays',
    },
    food: {
        style: 'appetizing food photography',
        lighting: 'warm natural lighting to enhance colors',
        background: 'rustic or clean table setting',
        elements: 'make the product look fresh and delicious',
    },
    accessories: {
        style: 'stylish lifestyle product photography',
        lighting: 'balanced lighting with soft shadows',
        background: 'elegant neutral or lifestyle context',
        elements: 'showcase craftsmanship and details',
    },
    furniture: {
        style: 'interior design product photography',
        lighting: 'natural ambient lighting',
        background: 'modern interior or clean studio',
        elements: 'display functionality and aesthetic appeal',
    },
    beauty: {
        style: 'glamorous beauty product photography',
        lighting: 'bright clean lighting with gentle shadows',
        background: 'clean white or beauty-themed setting',
        elements: 'emphasize product texture and packaging',
    },
}

/**
 * Background style descriptions
 */
const backgroundStyles = {
    white: 'pure white background',
    scene: 'lifestyle scene with contextual elements',
    transparent: 'transparent background with subtle shadows',
    gradient: 'smooth gradient background',
    custom: 'custom background based on product category',
}

/**
 * Logo position mapping
 */
const logoPositions = {
    'top-left': 'positioned at the top-left corner',
    'top-right': 'positioned at the top-right corner',
    'bottom-left': 'positioned at the bottom-left corner',
    'bottom-right': 'positioned at the bottom-right corner',
    'center': 'positioned at the center',
    'top-center': 'positioned at the top-center',
    'bottom-center': 'positioned at the bottom-center',
}

/**
 * Build AI prompt from wizard data
 *
 * @param {Object} data - Wizard data containing all user selections
 * @returns {Object} - { prompt: string, image_urls: array }
 */
export function buildPrompt(data) {
    const {
        category,
        productName,
        description,
        productImageUrl,
        logoUrl,
        logoPosition,
        overlayText,
        background,
        designStyle,
        customNotes,
        referenceImageUrl,
    } = data

    let prompt = ''
    const imageUrls = []
    let imageIndex = 1

    // Base instruction
    prompt += 'Create a high-quality professional promotional product image'

    // Product name
    if (productName) {
        prompt += ` for a product named "${productName}"`
    }

    prompt += '. '

    // Add product image reference (first image)
    if (productImageUrl) {
        imageUrls.push(productImageUrl)
        prompt += `Refer to image ${imageIndex} as the product image for accurate product representation. `
        imageIndex++
    }

    // Add logo reference (second image)
    if (logoUrl) {
        imageUrls.push(logoUrl)
        const positionDesc = logoPositions[logoPosition] || logoPosition
        prompt += `Refer to image ${imageIndex} as the logo and place it ${positionDesc} in the final image. `
        imageIndex++
    }

    // Add reference design image (third image)
    if (referenceImageUrl) {
        imageUrls.push(referenceImageUrl)
        prompt += `Refer to image ${imageIndex} as a reference for design inspiration, composition, and style. `
        imageIndex++
    }

    // Category-specific styling
    if (category && categoryTemplates[category.toLowerCase()]) {
        const template = categoryTemplates[category.toLowerCase()]
        prompt += `Use ${template.style} with ${template.lighting}. `
        prompt += `${template.elements}. `
    }

    // Product description
    if (description) {
        prompt += `The product is described as: ${description}. `
    }

    // Background preference
    if (background) {
        const bgDescription = backgroundStyles[background.toLowerCase()] || background
        prompt += `Set the product on a ${bgDescription}. `
    }

    // Text overlay
    if (overlayText) {
        prompt += `Add overlay text that reads: "${overlayText}". `
        prompt += `Make the text prominent and easy to read. `
    }

    // Design style preference
    if (designStyle) {
        prompt += `Design style preference: ${designStyle}. `
    }

    // Custom notes
    if (customNotes) {
        prompt += `Additional requirements: ${customNotes}. `
    }

    // Final quality instructions
    prompt += 'Ensure the image is high-resolution, professionally composed, and ready for e-commerce use. '
    prompt += 'The final image should be visually appealing and effectively showcase the product.'

    return {
        prompt: prompt.trim(),
        image_urls: imageUrls,
    }
}

/**
 * Composable for building AI prompts
 */
export function useAIPromptBuilder() {
    return {
        buildPrompt,
        categoryTemplates,
        backgroundStyles,
        logoPositions,
    }
}
