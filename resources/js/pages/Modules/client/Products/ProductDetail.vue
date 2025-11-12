<template>
  <div>
    <div
      :class="appStore.darkMode ? 'bg-gray-900/50 text-gray-100' : 'bg-gray-50/50 text-gray-900'"
      class="min-h-screen p-6 transition-colors rounded-lg"
    >
      <Head :title="product?.name || $t('products.title')" />
      <div class="mx-auto">
        <!-- Product Details -->
        <div v-if="product">
          <!-- Header -->
          <div class="mb-6 flex justify-between items-start">
            <div class="flex items-start gap-4">
              <Link href="/client/products">
                <Button variant="secondary">
                  {{ $t('common.back') }}
                </Button>
              </Link>
              <div>
                <div class="flex items-center gap-3 mb-2">
                  <h1
                    class="text-3xl font-bold"
                    :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'"
                  >
                    {{ product.name }}
                  </h1>
                  <span class="px-2 py-1 rounded-full text-xs font-medium" :class="platformColors[product.platform] || platformColors.others">
                    {{ $t(`products.platforms.${product.platform}`) }}
                  </span>
                </div>
                <p :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                  {{ product.description || $t('products.noDescription') }}
                </p>
              </div>
            </div>

            <Link :href="`/client/ai-generations/wizard?productId=${product.id}`">
              <Button variant="primary">
                {{ $t('products.createNewImage') }}
              </Button>
            </Link>
          </div>

          <!-- Product Info Card -->
          <div
            class="mb-6 p-6 rounded-lg"
            :class="appStore.darkMode ? 'bg-gray-800/50' : 'bg-white/50'"
          >
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div v-if="product.image_url" class="col-span-1">
                <img
                  :src="product.image_url"
                  :alt="product.name"
                  class="w-full h-48 object-cover rounded-lg"
                />
              </div>
              <div :class="product.image_url ? 'col-span-3' : 'col-span-4'">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-if="product.sku">
                    <dt
                      class="text-sm font-medium"
                      :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'"
                    >
                      {{ $t('products.fields.sku') }}
                    </dt>
                    <dd
                      class="mt-1 text-sm"
                      :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'"
                    >
                      {{ product.sku }}
                    </dd>
                  </div>
                  <div v-if="product.price">
                    <dt
                      class="text-sm font-medium"
                      :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'"
                    >
                      {{ $t('products.fields.price') }}
                    </dt>
                    <dd
                      class="mt-1 text-sm"
                      :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'"
                    >
                      {{ product.price }}
                    </dd>
                  </div>
                  <div>
                    <dt
                      class="text-sm font-medium"
                      :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'"
                    >
                      {{ $t('products.fields.createdAt') }}
                    </dt>
                    <dd
                      class="mt-1 text-sm"
                      :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'"
                    >
                      {{ formatDate(product.created_at) }}
                    </dd>
                  </div>
                  <div>
                    <dt
                      class="text-sm font-medium"
                      :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'"
                    >
                      {{ $t('products.fields.aiImagesCount') }}
                    </dt>
                    <dd
                      class="mt-1 text-sm"
                      :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'"
                    >
                      {{ product.images?.length || 0 }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>
          </div>

          <!-- AI Generated Images -->
          <div>
            <h2
              class="text-2xl font-bold mb-4"
              :class="appStore.darkMode ? 'text-gray-100/50' : 'text-gray-900/50'"
            >
              {{ $t('products.aiGeneratedImages') }}
            </h2>

            <!-- No Images State -->
            <div
              v-if="!product.images || product.images.length === 0"
              class="text-center py-20 rounded-lg"
              :class="appStore.darkMode ? 'bg-gray-800/50' : 'bg-white/50'"
            >
              <div class="text-6xl mb-4">🎨</div>
              <h3
                class="text-xl font-semibold mb-2"
                :class="appStore.darkMode ? 'text-gray-100 ' : 'text-gray-900'"
              >
                {{ $t('products.noImagesYet') }}
              </h3>
              <p
                class="mb-6"
                :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'"
              >
                {{ $t('products.noImagesDescription') }}
              </p>
              <Link :href="`/client/ai-generations/wizard?productId=${product.id}`">
                <Button variant="primary">
                  {{ $t('products.createFirstImage') }}
                </Button>
              </Link>
            </div>

            <!-- Images Grid -->
            <div
              v-else
              class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
            >
              <div
                v-for="image in product.images"
                :key="image.id"
                class="rounded-lg overflow-hidden transition-colors"
                :class="appStore.darkMode ? 'bg-gray-800' : 'bg-white'"
              >
                <img
                  :src="image.image_url"
                  :alt="image.prompt || product.name"
                  class="w-full h-64 object-cover"
                />
                <div class="p-4">
                  <div v-if="image.prompt" class="mb-2">
                    <p
                      class="text-sm font-medium"
                      :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'"
                    >
                      {{ image.prompt }}
                    </p>
                  </div>
                  <div class="flex items-center justify-between">
                    <span
                      v-if="image.style"
                      class="text-xs px-2 py-1 rounded-full"
                      :class="appStore.darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-700'"
                    >
                      {{ image.style }}
                    </span>
                    <span
                      class="text-xs"
                      :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-500'"
                    >
                      {{ formatDate(image.created_at) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '@/store'
import Button from '@/components/ui/Button.vue'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const { t } = useI18n()
const appStore = useAppStore()

// Platform badge colors
const platformColors = {
  salla: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
  zid: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
  others: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>
