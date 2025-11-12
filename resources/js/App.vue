<script setup>
import { computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from './components/layout/AppLayout.vue'
import AuthLayout from './components/layout/AuthLayout.vue'

const page = usePage()

// Determine which layout to use based on the component name
const layout = computed(() => {
    const component = page?.component || ''
    const url = page?.url || window.location.pathname

    // Check by component name first (most reliable)
    const authComponents = ['auth/Login', 'auth/ForgotPassword', 'auth/OtpVerification', 'auth/ResetPassword']

    // If component explicitly matches auth pages, use AuthLayout
    if (component && authComponents.some(authComp => component === authComp)) {
        return AuthLayout
    }

    // If URL explicitly matches auth routes, use AuthLayout
    const authRoutes = ['/login', '/forgot-password', '/verify-otp', '/reset-password', '/register']
    if (authRoutes.includes(url) || authRoutes.some(route => url.startsWith(route + '?'))) {
        return AuthLayout
    }

    // Default to AppLayout for all other pages (dashboard, admin, client, etc.)
    return AppLayout
})

// Get layout key to force re-render when layout changes
const layoutKey = computed(() => {
    const component = page?.component || ''
    const url = page?.url || window.location.pathname

    // Check by component name first
    const authComponents = ['auth/Login', 'auth/ForgotPassword', 'auth/OtpVerification', 'auth/ResetPassword']
    if (component && authComponents.some(authComp => component === authComp)) {
        return 'auth-layout'
    }

    // Fallback to URL check
    const authRoutes = ['/login', '/forgot-password', '/verify-otp', '/reset-password', '/register']
    if (authRoutes.includes(url) || authRoutes.some(route => url.startsWith(route + '?'))) {
        return 'auth-layout'
    }

    return 'app-layout'
})

// Get auth page title and subtitle based on component
const authTitle = computed(() => {
    const component = page?.component || ''
    if (component.includes('auth/Login')) return 'Login'
    if (component.includes('auth/ForgotPassword')) return 'Forgot Password'
    if (component.includes('auth/OtpVerification')) return 'Verify OTP'
    if (component.includes('auth/ResetPassword')) return 'Reset Password'
    return ''
})

const authSubtitle = computed(() => {
    const component = page?.component || ''
    if (component.includes('auth/Login')) return 'Sign in to your account'
    if (component.includes('auth/ForgotPassword')) return 'Enter your email to reset password'
    if (component.includes('auth/OtpVerification')) return 'Enter the code sent to your email'
    if (component.includes('auth/ResetPassword')) return 'Enter your new password'
    return ''
})
</script>

<template>
    <component :is="layout" :key="layoutKey" :title="authTitle" :subtitle="authSubtitle">
        <slot />
    </component>
</template>
