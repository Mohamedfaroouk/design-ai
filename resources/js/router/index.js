import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/',
        component: () => import('@/components/layout/AppLayout.vue'),
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('@/pages/Dashboard.vue'),
                meta: { title: 'Dashboard' }
            },
            {
                path: 'users',
                name: 'users.index',
                component: () => import('@/pages/Modules/Users/UsersIndex.vue'),
                meta: { title: 'Users' }
            },
            {
                path: 'users/create',
                name: 'users.create',
                component: () => import('@/pages/Modules/Users/UsersForm.vue'),
                meta: { title: 'Create User' }
            },
            {
                path: 'users/:id/edit',
                name: 'users.edit',
                component: () => import('@/pages/Modules/Users/UsersForm.vue'),
                meta: { title: 'Edit User' }
            },
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Navigation guard for setting page title
router.beforeEach((to, from, next) => {
    document.title = to.meta.title ? `${to.meta.title} - Dashboard` : 'Dashboard'
    next()
})

export default router
