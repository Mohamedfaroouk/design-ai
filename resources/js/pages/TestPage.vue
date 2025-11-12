<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
  message: String
})
</script>

<template>
  <Head title="Test Page" />
  
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
        {{ message }}
      </h1>
      <p class="text-gray-600 dark:text-gray-400">
        Inertia.js is successfully integrated with your Laravel + Vue 3 application.
      </p>
    </div>
  </div>
</template>
