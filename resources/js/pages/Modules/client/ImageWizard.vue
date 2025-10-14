<template>
    <div
        :class="appStore.darkMode ? 'bg-gray-900 text-gray-100' : 'bg-gray-50 text-gray-900'"
        class="min-h-screen py-8 transition-colors"
    >
        <div class="max-w-6xl mx-auto px-4">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-2">
                    {{ $t('ai.wizard.title') }}
                </h1>
                <p
                    :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'"
                    class="transition-colors"
                >
                    {{ $t('ai.wizard.description') }}
                </p>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div
                        v-for="(step, index) in steps"
                        :key="index"
                        class="flex items-center flex-1"
                    >
                        <div class="flex flex-col items-center flex-1">
                            <!-- Step Circle -->
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors"
                                :class="{
                                    'bg-primary-600 text-white': currentStep === index,
                                    'bg-green-600 text-white': currentStep > index,
                                    'bg-gray-300 text-gray-600 dark:bg-gray-700 dark:text-gray-400':
                                        currentStep < index,
                                }"
                            >
                                <span v-if="currentStep > index">✓</span>
                                <span v-else>{{ index + 1 }}</span>
                            </div>
                            <!-- Step Label -->
                            <span
                                class="mt-2 text-xs text-center transition-colors"
                                :class="{
                                    'text-primary-600 font-semibold': currentStep === index,
                                    'text-gray-600 dark:text-gray-400': currentStep !== index,
                                }"
                            >
                                {{ $t(`ai.wizard.steps.${step}`) }}
                            </span>
                        </div>
                        <!-- Connector Line -->
                        <div
                            v-if="index < steps.length - 1"
                            class="h-1 flex-1 mx-2 transition-colors"
                            :class="{
                                'bg-green-600': currentStep > index,
                                'bg-gray-300 dark:bg-gray-700': currentStep <= index,
                            }"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Wizard Content -->
            <div
                :class="
                    appStore.darkMode
                        ? 'bg-gray-800 border-gray-700'
                        : 'bg-white border-gray-200'
                "
                class="rounded-lg border p-8 transition-colors"
            >
                <!-- Step 1: Store Type Selection -->
                <div v-if="currentStep === 0">
                    <h2 class="text-2xl font-bold mb-4">
                        {{ $t('ai.wizard.storeType.title') }}
                    </h2>
                    <p
                        :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'"
                        class="mb-6 transition-colors"
                    >
                        {{ $t('ai.wizard.storeType.description') }}
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div
                            v-for="platform in platforms"
                            :key="platform.value"
                            @click="selectPlatform(platform.value)"
                            class="p-6 rounded-lg border-2 cursor-pointer transition-all"
                            :class="{
                                'border-primary-600 bg-primary-50 dark:bg-primary-900/20':
                                    formData.platform === platform.value,
                                'border-gray-300 dark:border-gray-600 hover:border-primary-400':
                                    formData.platform !== platform.value,
                            }"
                        >
                            <h3 class="text-lg font-semibold mb-2">{{ platform.label }}</h3>
                            <p
                                :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'"
                                class="text-sm transition-colors"
                            >
                                {{ platform.description }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Product Selection or Manual Entry -->
                <div v-if="currentStep === 1">
                    <h2 class="text-2xl font-bold mb-4">
                        {{ $t('ai.wizard.product.title') }}
                    </h2>

                    <!-- Manual Entry Form with Repeater (for "Other" platform) -->
                    <div v-if="formData.platform === 'other'" class="space-y-6">
                        <div
                            v-for="(product, index) in formData.products"
                            :key="index"
                            :class="
                                appStore.darkMode
                                    ? 'bg-gray-700 border-gray-600'
                                    : 'bg-gray-50 border-gray-200'
                            "
                            class="p-6 rounded-lg border transition-colors relative"
                        >
                            <!-- Delete Button -->
                            <button
                                v-if="formData.products.length > 1"
                                @click="removeProduct(index)"
                                class="absolute top-4 end-4 text-red-600 hover:text-red-700 transition-colors"
                                :title="$t('ai.wizard.product.remove')"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <h3 class="font-semibold mb-4">
                                {{ $t('ai.wizard.product.item_number', { number: index + 1 }) }}
                            </h3>

                            <div class="space-y-4">
                                <TextInput
                                    v-model="product.productName"
                                    :label="$t('ai.wizard.product.name')"
                                    :placeholder="$t('ai.wizard.product.namePlaceholder')"
                                    required
                                />

                                <Textarea
                                    v-model="product.description"
                                    :label="$t('ai.wizard.product.description')"
                                    :placeholder="$t('ai.wizard.product.descriptionPlaceholder')"
                                    :rows="3"
                                />

                                <ImagePicker
                                    v-model="product.productImageUrl"
                                    :label="$t('ai.wizard.product.image')"
                                    :hint="$t('ai.wizard.product.imageHint')"
                                    :upload-url="'/client/uploads'"
                                    :auto-upload="true"
                                    @upload:success="(response) => handleProductImageUpload(response, index)"
                                />
                            </div>
                        </div>

                        <!-- Add Product Button -->
                        <button
                            @click="addProduct"
                            :class="
                                appStore.darkMode
                                    ? 'border-gray-600 text-gray-300 hover:bg-gray-700'
                                    : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                            "
                            class="w-full p-4 rounded-lg border-2 border-dashed transition-all flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ $t('ai.wizard.product.add_another') }}
                        </button>
                    </div>

                    <!-- Store Integration (Salla/Zid) - Multi-select placeholder -->
                    <div v-else>
                        <div
                            :class="
                                appStore.darkMode
                                    ? 'bg-blue-900/20 border-blue-800 text-blue-300'
                                    : 'bg-blue-50 border-blue-200 text-blue-800'
                            "
                            class="p-4 rounded-lg border transition-colors"
                        >
                            <p class="font-semibold mb-2">
                                {{ $t('ai.wizard.product.comingSoon') }}
                            </p>
                            <p
                                :class="appStore.darkMode ? 'text-blue-400' : 'text-blue-600'"
                                class="text-sm transition-colors"
                            >
                                {{
                                    $t('ai.wizard.product.comingSoonDesc', {
                                        platform: formData.platform,
                                    })
                                }}
                            </p>
                            <p
                                :class="appStore.darkMode ? 'text-blue-300' : 'text-blue-700'"
                                class="text-sm mt-2 transition-colors"
                            >
                                {{ $t('ai.wizard.product.multiSelectPlaceholder') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Category Selection -->
                <div v-if="currentStep === 2">
                    <h2 class="text-2xl font-bold mb-4">
                        {{ $t('ai.wizard.category.title') }}
                    </h2>
                    <p
                        :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'"
                        class="mb-6 transition-colors"
                    >
                        {{ $t('ai.wizard.category.description') }}
                    </p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div
                            v-for="category in categories"
                            :key="category.value"
                            @click="formData.category = category.value"
                            class="p-4 rounded-lg border-2 cursor-pointer text-center transition-all"
                            :class="{
                                'border-primary-600 bg-primary-50 dark:bg-primary-900/20':
                                    formData.category === category.value,
                                'border-gray-300 dark:border-gray-600 hover:border-primary-400':
                                    formData.category !== category.value,
                            }"
                        >
                            <div class="text-3xl mb-2">{{ category.icon }}</div>
                            <h3 class="font-semibold">{{ category.label }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Design Style -->
                <div v-if="currentStep === 3">
                    <h2 class="text-2xl font-bold mb-4">
                        {{ $t('ai.wizard.design.title') }}
                    </h2>

                    <div class="space-y-6">
                        <!-- Background Selection -->
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                {{ $t('ai.wizard.design.background') }}
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div
                                    v-for="bg in backgrounds"
                                    :key="bg.value"
                                    @click="formData.background = bg.value"
                                    class="p-3 rounded-lg border-2 cursor-pointer text-center transition-all"
                                    :class="{
                                        'border-primary-600 bg-primary-50 dark:bg-primary-900/20':
                                            formData.background === bg.value,
                                        'border-gray-300 dark:border-gray-600 hover:border-primary-400':
                                            formData.background !== bg.value,
                                    }"
                                >
                                    <span class="text-sm font-medium">{{ bg.label }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Design Style Input -->
                        <Textarea
                            v-model="formData.designStyle"
                            :label="$t('ai.wizard.design.style')"
                            :placeholder="$t('ai.wizard.design.stylePlaceholder')"
                            :hint="$t('ai.wizard.design.styleHint')"
                            :rows="3"
                        />

                        <!-- Reference Image Upload -->
                        <ImagePicker
                            v-model="formData.referenceImageUrl"
                            :label="$t('ai.wizard.design.reference')"
                            :hint="$t('ai.wizard.design.referenceHint')"
                            :upload-url="'/client/uploads'"
                            :auto-upload="true"
                            @upload:success="handleReferenceImageUpload"
                        />
                    </div>
                </div>

                <!-- Step 5: Customization -->
                <div v-if="currentStep === 4">
                    <h2 class="text-2xl font-bold mb-4">
                        {{ $t('ai.wizard.customize.title') }}
                    </h2>

                    <div class="space-y-6">
                        <!-- Logo Upload with User Avatar Default -->
                        <div>
                            <ImagePicker
                                v-model="formData.logoUrl"
                                :label="$t('ai.wizard.customize.logo')"
                                :hint="userHasAvatar ? $t('ai.wizard.customize.logoHintWithAvatar') : $t('ai.wizard.customize.logoHint')"
                                :upload-url="'/client/uploads'"
                                :auto-upload="true"
                                @upload:success="handleLogoUpload"
                            />
                            <button
                                v-if="userHasAvatar && !formData.logoUrl"
                                @click="useUserAvatar"
                                :class="
                                    appStore.darkMode
                                        ? 'text-primary-400 hover:text-primary-300'
                                        : 'text-primary-600 hover:text-primary-700'
                                "
                                class="text-sm mt-2 transition-colors"
                            >
                                {{ $t('ai.wizard.customize.useMyAvatar') }}
                            </button>
                        </div>

                        <!-- Logo Position -->
                        <div v-if="formData.logoUrl">
                            <label class="block text-sm font-medium mb-2">
                                {{ $t('ai.wizard.customize.logoPosition') }}
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <div
                                    v-for="pos in logoPositions"
                                    :key="pos.value"
                                    @click="formData.logoPosition = pos.value"
                                    class="p-3 rounded-lg border-2 cursor-pointer text-center transition-all"
                                    :class="{
                                        'border-primary-600 bg-primary-50 dark:bg-primary-900/20':
                                            formData.logoPosition === pos.value,
                                        'border-gray-300 dark:border-gray-600 hover:border-primary-400':
                                            formData.logoPosition !== pos.value,
                                    }"
                                >
                                    <span class="text-sm">{{ pos.label }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Text Overlay -->
                        <TextInput
                            v-model="formData.overlayText"
                            :label="$t('ai.wizard.customize.overlayText')"
                            :placeholder="$t('ai.wizard.customize.overlayTextPlaceholder')"
                            :hint="$t('ai.wizard.customize.overlayTextHint')"
                        />

                        <!-- Custom Notes -->
                        <Textarea
                            v-model="formData.customNotes"
                            :label="$t('ai.wizard.customize.notes')"
                            :placeholder="$t('ai.wizard.customize.notesPlaceholder')"
                            :hint="$t('ai.wizard.customize.notesHint')"
                            :rows="3"
                        />
                    </div>
                </div>

                <!-- Step 6: Review & Generate -->
                <div v-if="currentStep === 5">
                    <h2 class="text-2xl font-bold mb-4">
                        {{ $t('ai.wizard.review.title') }}
                    </h2>
                    <p
                        :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'"
                        class="mb-6 transition-colors"
                    >
                        {{ $t('ai.wizard.review.description') }}
                    </p>

                    <!-- Summary Cards -->
                    <div class="space-y-4">
                        <!-- Products Summary -->
                        <div
                            :class="
                                appStore.darkMode
                                    ? 'bg-gray-700 border-gray-600'
                                    : 'bg-gray-50 border-gray-200'
                            "
                            class="p-4 rounded-lg border transition-colors"
                        >
                            <h3 class="font-semibold mb-2">
                                {{ $t('ai.wizard.review.products', { count: formData.products.length }) }}
                            </h3>
                            <div
                                :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'"
                                class="text-sm space-y-2 transition-colors"
                            >
                                <div
                                    v-for="(product, index) in formData.products"
                                    :key="index"
                                    class="pb-2 border-b border-gray-600 dark:border-gray-500 last:border-0"
                                >
                                    <p class="font-medium">{{ index + 1 }}. {{ product.productName }}</p>
                                    <p v-if="product.description" class="text-xs opacity-75">
                                        {{ product.description.substring(0, 100) }}{{ product.description.length > 100 ? '...' : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Design Preferences -->
                        <div
                            :class="
                                appStore.darkMode
                                    ? 'bg-gray-700 border-gray-600'
                                    : 'bg-gray-50 border-gray-200'
                            "
                            class="p-4 rounded-lg border transition-colors"
                        >
                            <h3 class="font-semibold mb-2">
                                {{ $t('ai.wizard.review.design') }}
                            </h3>
                            <div
                                :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'"
                                class="text-sm space-y-1 transition-colors"
                            >
                                <p v-if="formData.background">
                                    <strong>{{ $t('ai.wizard.design.background') }}:</strong>
                                    {{ getBackgroundLabel(formData.background) }}
                                </p>
                                <p v-if="formData.category">
                                    <strong>{{ $t('ai.wizard.category.title') }}:</strong>
                                    {{ getCategoryLabel(formData.category) }}
                                </p>
                                <p v-if="formData.designStyle">
                                    <strong>{{ $t('ai.wizard.design.style') }}:</strong>
                                    {{ formData.designStyle }}
                                </p>
                            </div>
                        </div>

                        <!-- Customization -->
                        <div
                            v-if="
                                formData.logoUrl ||
                                formData.overlayText ||
                                formData.customNotes
                            "
                            :class="
                                appStore.darkMode
                                    ? 'bg-gray-700 border-gray-600'
                                    : 'bg-gray-50 border-gray-200'
                            "
                            class="p-4 rounded-lg border transition-colors"
                        >
                            <h3 class="font-semibold mb-2">
                                {{ $t('ai.wizard.review.customization') }}
                            </h3>
                            <div
                                :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'"
                                class="text-sm space-y-1 transition-colors"
                            >
                                <p v-if="formData.logoUrl">
                                    <strong>{{ $t('ai.wizard.customize.logo') }}:</strong>
                                    {{ $t('ai.wizard.review.logoUploaded') }} ({{
                                        getLogoPositionLabel(formData.logoPosition)
                                    }})
                                </p>
                                <p v-if="formData.overlayText">
                                    <strong>{{ $t('ai.wizard.customize.overlayText') }}:</strong>
                                    {{ formData.overlayText }}
                                </p>
                                <p v-if="formData.customNotes">
                                    <strong>{{ $t('ai.wizard.customize.notes') }}:</strong>
                                    {{ formData.customNotes }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-8">
                    <Button
                        v-if="currentStep > 0"
                        @click="previousStep"
                        variant="secondary"
                        :disabled="generating"
                    >
                        {{ $t('ai.wizard.previous') }}
                    </Button>
                    <div v-else></div>

                    <Button
                        v-if="currentStep < steps.length - 1"
                        @click="nextStep"
                        :disabled="!canProceed"
                    >
                        {{ $t('ai.wizard.next') }}
                    </Button>
                    <Button
                        v-else
                        @click="generateImages"
                        :loading="generating"
                        :disabled="!canGenerate"
                    >
                        {{ $t('ai.wizard.generate_batch', { count: formData.products.length }) }}
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAppStore, useToastStore } from '@/store/index'
import { buildPrompt } from '@/composables/useAIPromptBuilder'
import aiImageService from '@/services/client/aiImage'
import authService from '@/services/auth'
import TextInput from '@/components/inputs/TextInput.vue'
import Textarea from '@/components/inputs/Textarea.vue'
import ImagePicker from '@/components/inputs/ImagePicker.vue'
import Button from '@/components/ui/Button.vue'

const { t } = useI18n()
const router = useRouter()
const appStore = useAppStore()
const toast = useToastStore()

const steps = ['storeType', 'product', 'category', 'design', 'customize', 'review']
const currentStep = ref(0)
const generating = ref(false)

// Get current user
const currentUser = computed(() => authService.getUser())
const userHasAvatar = computed(() => !!currentUser.value?.avatar)

const formData = ref({
    platform: '',
    products: [
        {
            productName: '',
            description: '',
            productImageUrl: '',
        }
    ],
    category: '',
    background: 'white',
    designStyle: '',
    referenceImageUrl: '',
    logoUrl: '',
    logoPosition: 'top-left',
    overlayText: '',
    customNotes: '',
})

const platforms = [
    {
        value: 'salla',
        label: 'Salla',
        description: t('ai.wizard.storeType.sallaDesc'),
    },
    {
        value: 'zid',
        label: 'Zid',
        description: t('ai.wizard.storeType.zidDesc'),
    },
    {
        value: 'other',
        label: t('ai.wizard.storeType.other'),
        description: t('ai.wizard.storeType.otherDesc'),
    },
]

const categories = [
    { value: 'perfumes', label: t('ai.wizard.category.perfumes'), icon: '🧴' },
    { value: 'clothing', label: t('ai.wizard.category.clothing'), icon: '👕' },
    { value: 'electronics', label: t('ai.wizard.category.electronics'), icon: '📱' },
    { value: 'food', label: t('ai.wizard.category.food'), icon: '🍕' },
    { value: 'accessories', label: t('ai.wizard.category.accessories'), icon: '👜' },
    { value: 'furniture', label: t('ai.wizard.category.furniture'), icon: '🪑' },
    { value: 'beauty', label: t('ai.wizard.category.beauty'), icon: '💄' },
]

const backgrounds = [
    { value: 'white', label: t('ai.wizard.design.bgWhite') },
    { value: 'scene', label: t('ai.wizard.design.bgScene') },
    { value: 'transparent', label: t('ai.wizard.design.bgTransparent') },
    { value: 'gradient', label: t('ai.wizard.design.bgGradient') },
]

const logoPositions = [
    { value: 'top-left', label: t('ai.wizard.customize.posTopLeft') },
    { value: 'top-center', label: t('ai.wizard.customize.posTopCenter') },
    { value: 'top-right', label: t('ai.wizard.customize.posTopRight') },
    { value: 'center', label: t('ai.wizard.customize.posCenter') },
    { value: 'bottom-left', label: t('ai.wizard.customize.posBottomLeft') },
    { value: 'bottom-center', label: t('ai.wizard.customize.posBottomCenter') },
    { value: 'bottom-right', label: t('ai.wizard.customize.posBottomRight') },
]

const canProceed = computed(() => {
    switch (currentStep.value) {
        case 0:
            return formData.value.platform !== ''
        case 1:
            if (formData.value.platform === 'other') {
                return formData.value.products.some(p => p.productName !== '')
            }
            return true // For Salla/Zid (coming soon)
        case 2:
            return formData.value.category !== ''
        default:
            return true
    }
})

const canGenerate = computed(() => {
    return (
        formData.value.products.length > 0 &&
        formData.value.products.every(p => p.productName) &&
        formData.value.category &&
        formData.value.background
    )
})

const selectPlatform = (platform) => {
    formData.value.platform = platform
}

const addProduct = () => {
    formData.value.products.push({
        productName: '',
        description: '',
        productImageUrl: '',
    })
}

const removeProduct = (index) => {
    formData.value.products.splice(index, 1)
}

const useUserAvatar = () => {
    if (currentUser.value?.avatar) {
        formData.value.logoUrl = currentUser.value.avatar
        toast.success(t('ai.wizard.customize.avatarUsed'))
    }
}

const nextStep = () => {
    if (currentStep.value < steps.length - 1 && canProceed.value) {
        currentStep.value++
    }
}

const previousStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--
    }
}

const handleProductImageUpload = (response, index) => {
    formData.value.products[index].productImageUrl = response.url
    toast.success(t('ai.wizard.uploadSuccess'))
}

const handleReferenceImageUpload = (response) => {
    formData.value.referenceImageUrl = response.url
    toast.success(t('ai.wizard.uploadSuccess'))
}

const handleLogoUpload = (response) => {
    formData.value.logoUrl = response.url
    toast.success(t('ai.wizard.uploadSuccess'))
}

const generateImages = async () => {
    if (!canGenerate.value) return

    generating.value = true

    try {
        // Build products payload
        const products = formData.value.products.map((product) => {
            const promptData = buildPrompt({
                ...formData.value,
                productName: product.productName,
                description: product.description,
                productImageUrl: product.productImageUrl,
            })

            return {
                prompt: promptData.prompt,
                product_name: product.productName,
                image_urls: promptData.image_urls,
            }
        })

        const response = await aiImageService.generate({
            products: products,
            image_size: '1:1',
            output_format: 'png',
        })

        // Show success message
        toast.success(
            t('ai.wizard.batch_success', { count: formData.value.products.length })
        )

        // Navigate to AI generations index
        setTimeout(() => {
            router.push({ name: 'client.ai-generations.index' })
        }, 1500)
    } catch (error) {
        toast.error(error.message || t('ai.generation.failed'))
    } finally {
        generating.value = false
    }
}

const getCategoryLabel = (value) => {
    return categories.find((c) => c.value === value)?.label || value
}

const getBackgroundLabel = (value) => {
    return backgrounds.find((b) => b.value === value)?.label || value
}

const getLogoPositionLabel = (value) => {
    return logoPositions.find((p) => p.value === value)?.label || value
}
</script>
