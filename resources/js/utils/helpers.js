/**
 * Format a date string
 */
export const formatDate = (date, format = 'short') => {
    if (!date) return ''

    const d = new Date(date)

    if (format === 'short') {
        return d.toLocaleDateString()
    }

    if (format === 'long') {
        return d.toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })
    }

    if (format === 'datetime') {
        return d.toLocaleString()
    }

    return d.toLocaleDateString()
}

/**
 * Format currency
 */
export const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency
    }).format(amount)
}

/**
 * Format file size
 */
export const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'

    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))

    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

/**
 * Truncate text
 */
export const truncate = (text, length = 100, suffix = '...') => {
    if (!text || text.length <= length) return text
    return text.substring(0, length) + suffix
}

/**
 * Debounce function
 */
export const debounce = (func, wait = 300) => {
    let timeout
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout)
            func(...args)
        }
        clearTimeout(timeout)
        timeout = setTimeout(later, wait)
    }
}

/**
 * Capitalize first letter
 */
export const capitalize = (str) => {
    if (!str) return ''
    return str.charAt(0).toUpperCase() + str.slice(1)
}

/**
 * Get initials from name
 */
export const getInitials = (name) => {
    if (!name) return ''

    const parts = name.split(' ')
    if (parts.length === 1) {
        return parts[0].charAt(0).toUpperCase()
    }

    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase()
}

/**
 * Check if value is empty
 */
export const isEmpty = (value) => {
    if (value === null || value === undefined) return true
    if (typeof value === 'string') return value.trim() === ''
    if (Array.isArray(value)) return value.length === 0
    if (typeof value === 'object') return Object.keys(value).length === 0
    return false
}

/**
 * Generate random ID
 */
export const generateId = () => {
    return Math.random().toString(36).substring(2, 11)
}

/**
 * Download file from blob
 */
export const downloadBlob = (blob, filename) => {
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = filename
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(url)
}

/**
 * Copy text to clipboard
 */
export const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text)
        return true
    } catch (error) {
        console.error('Failed to copy:', error)
        return false
    }
}

/**
 * Get contrast color (black or white) for a background color
 */
export const getContrastColor = (hexColor) => {
    // Remove # if present
    const color = hexColor.replace('#', '')

    // Convert to RGB
    const r = parseInt(color.substr(0, 2), 16)
    const g = parseInt(color.substr(2, 2), 16)
    const b = parseInt(color.substr(4, 2), 16)

    // Calculate luminance
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255

    return luminance > 0.5 ? '#000000' : '#FFFFFF'
}

/**
 * Validate email
 */
export const isValidEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return re.test(email)
}

/**
 * Validate URL
 */
export const isValidUrl = (url) => {
    try {
        new URL(url)
        return true
    } catch {
        return false
    }
}
