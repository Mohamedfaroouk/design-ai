import { createRouter, createWebHistory } from 'vue-router'
import authService from '@/services/auth'

const routes = [
    // Auth Routes (No Layout)
    {
        path: '/login',
        name: 'login',
        component: () => import('@/pages/auth/Login.vue'),
        meta: {
            title: 'Login',
            guest: true // Only accessible to guests (non-authenticated users)
        }
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('@/pages/auth/ForgotPassword.vue'),
        meta: {
            title: 'Forgot Password',
            guest: true
        }
    },
    {
        path: '/otp-verification',
        name: 'otp-verification',
        component: () => import('@/pages/auth/OtpVerification.vue'),
        meta: {
            title: 'OTP Verification',
            guest: true
        }
    },
    {
        path: '/reset-password',
        name: 'reset-password',
        component: () => import('@/pages/auth/ResetPassword.vue'),
        meta: {
            title: 'Reset Password',
            guest: true
        }
    },

    // Admin Routes (With Layout)
    {
        path: '/admin',
        redirect: '/admin/dashboard',
        component: () => import('@/components/layout/AppLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: 'dashboard',
                name: 'admin.dashboard',
                component: () => import('@/pages/Dashboard.vue'),
                meta: { title: 'Dashboard' }
            },
            {
                path: 'users',
                name: 'admin.users.index',
                component: () => import('@/pages/Modules/admin/Users/UsersIndex.vue'),
                meta: { title: 'Users' }
            },
            {
                path: 'users/create',
                name: 'admin.users.create',
                component: () => import('@/pages/Modules/admin/Users/UsersForm.vue'),
                meta: { title: 'Create User' }
            },
            {
                path: 'users/:id/edit',
                name: 'admin.users.edit',
                component: () => import('@/pages/Modules/admin/Users/UsersForm.vue'),
                meta: { title: 'Edit User' }
            },
            {
                path: 'roles',
                name: 'admin.roles.index',
                component: () => import('@/pages/Modules/admin/Roles/RolesIndex.vue'),
                meta: { title: 'Roles & Permissions' }
            },
            {
                path: 'roles/create',
                name: 'admin.roles.create',
                component: () => import('@/pages/Modules/admin/Roles/RolesForm.vue'),
                meta: { title: 'Create Role' }
            },
            {
                path: 'roles/:id/edit',
                name: 'admin.roles.edit',
                component: () => import('@/pages/Modules/admin/Roles/RolesForm.vue'),
                meta: { title: 'Edit Role' }
            },
            {
                path: 'settings',
                name: 'admin.settings.index',
                component: () => import('@/pages/Modules/admin/Settings/SettingsIndex.vue'),
                meta: { title: 'Settings' }
            },
            {
                path: 'profile',
                name: 'admin.profile',
                component: () => import('@/pages/Profile/ProfileIndex.vue'),
                meta: { title: 'My Profile' }
            },
        ]
    },

    // Client Routes (With Layout)
    {
        path: '/client',
        redirect: '/client/ai-image-generator',
        component: () => import('@/components/layout/AppLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: 'ai-image-generator',
                name: 'client.ai.wizard',
                component: () => import('@/pages/Modules/client/ImageWizard.vue'),
                meta: { title: 'AI Product Image Generator' }
            },
            {
                path: 'ai-generations',
                name: 'client.ai-generations.index',
                component: () => import('@/pages/Modules/client/AiGenerationIndex.vue'),
                meta: { title: 'AI Generation History' }
            },
        ]
    },

    // Root redirect
    {
        path: '/',
        redirect: '/admin/dashboard'
    },

    // 404 Not Found
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/pages/auth/Login.vue'),
        meta: { title: '404 Not Found' }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Check if user is authenticated
const isAuthenticated = () => {
    return authService.isAuthenticated()
}

// Navigation guards
router.beforeEach((to, from, next) => {
    // Set page title
    document.title = to.meta.title ? `${to.meta.title} - Dashboard` : 'Dashboard'

    const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
    const guestOnly = to.matched.some(record => record.meta.guest)
    const authenticated = isAuthenticated()

    // Route requires authentication
    if (requiresAuth && !authenticated) {
        next({ name: 'login', query: { redirect: to.fullPath } })
        return
    }

    // Route is for guests only (redirect authenticated users to dashboard)
    if (guestOnly && authenticated) {
        next({ name: 'admin.dashboard' })
        return
    }

    next()
})

export default router
