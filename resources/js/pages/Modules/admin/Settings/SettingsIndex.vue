<template>
    <div>
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold transition-colors"
                :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                {{ $t('settings.title') }}
            </h1>
            <p class="mt-2 text-sm transition-colors"
               :class="appStore.darkMode ? 'text-gray-400' : 'text-gray-600'">
                {{ $t('settings.subtitle') }}
            </p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
            <Spinner size="lg" :text="$t('common.loading')" />
        </div>

        <!-- Settings Tabs -->
        <div v-else class="space-y-6">
            <!-- Tabs Navigation - Horizontal Scroll on Mobile -->
            <div class="border-b"
                 :class="appStore.darkMode ? 'border-gray-700' : 'border-gray-200'">
                <nav class="-mb-px flex overflow-x-auto scrollbar-hide space-x-4 sm:space-x-8">
                    <button
                        v-for="group in settingGroups"
                        :key="group.key"
                        @click="activeTab = group.key"
                        class="py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-xs sm:text-sm transition-colors whitespace-nowrap flex-shrink-0"
                        :class="activeTab === group.key
                            ? appStore.darkMode
                                ? 'border-primary-400 text-primary-400'
                                : 'border-primary-600 text-primary-600'
                            : appStore.darkMode
                                ? 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    >
                        <span class="flex items-center gap-1.5 sm:gap-2">
                            <component :is="group.icon" class="w-4 h-4 sm:w-5 sm:h-5" />
                            <span class="hidden sm:inline">{{ group.label }}</span>
                            <span class="sm:hidden">{{ group.label.split(' ')[0] }}</span>
                        </span>
                    </button>
                </nav>
            </div>

            <!-- Settings Form -->
            <form @submit.prevent="handleSave" class="space-y-6">
                <div v-for="group in settingGroups" :key="group.key" v-show="activeTab === group.key">
                    <div class="rounded-lg shadow p-4 sm:p-6 transition-colors"
                         :class="appStore.darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white'">
                        <h2 class="text-lg sm:text-xl font-semibold mb-4 sm:mb-6 transition-colors"
                            :class="appStore.darkMode ? 'text-gray-100' : 'text-gray-900'">
                            {{ group.label }}
                        </h2>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                            <template v-for="setting in getGroupSettings(group.key)" :key="setting.key">
                                <!-- Text Input -->
                                <TextInput
                                    v-if="setting.type === 'text'"
                                    v-model="form[setting.key]"
                                    :label="getSettingLabel(setting.key)"
                                    :error="getError(setting.key)"
                                />

                                <!-- Number Input -->
                                <TextInput
                                    v-else-if="setting.type === 'number'"
                                    v-model.number="form[setting.key]"
                                    type="number"
                                    :label="getSettingLabel(setting.key)"
                                    :error="getError(setting.key)"
                                />

                                <!-- Boolean (Checkbox) -->
                                <div v-else-if="setting.type === 'boolean'" class="flex items-center py-2">
                                    <input
                                        :id="setting.key"
                                        v-model="form[setting.key]"
                                        type="checkbox"
                                        class="w-4 h-4 rounded border-2 transition-all duration-200 cursor-pointer"
                                        :class="appStore.darkMode
                                            ? 'border-gray-600 bg-gray-700 checked:bg-primary-600 checked:border-primary-600'
                                            : 'border-gray-300 bg-white checked:bg-primary-600 checked:border-primary-600'"
                                    />
                                    <label
                                        :for="setting.key"
                                        class="ms-3 text-sm sm:text-base font-medium transition-colors cursor-pointer"
                                        :class="appStore.darkMode ? 'text-gray-300' : 'text-gray-700'"
                                    >
                                        {{ getSettingLabel(setting.key) }}
                                    </label>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Actions - Stack on Mobile -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
                    <Button
                        type="button"
                        variant="secondary"
                        @click="loadSettings"
                        :disabled="saving"
                        class="w-full sm:w-auto"
                    >
                        {{ $t('common.cancel') }}
                    </Button>
                    <Button
                        type="submit"
                        variant="primary"
                        :loading="saving"
                        class="w-full sm:w-auto"
                    >
                        {{ $t('common.save') }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, h } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAppStore } from '@/store'
import { useToastStore } from '@/store'
import { useAdminSettingsStore } from '@/store/admin/settings'
import TextInput from '@/components/inputs/TextInput.vue'
import Button from '@/components/ui/Button.vue'
import Spinner from '@/components/ui/Spinner.vue'

const { t } = useI18n()
const appStore = useAppStore()
const toast = useToastStore()
const settingsStore = useAdminSettingsStore()

const form = ref({})
const errors = ref({})
const loading = ref(false)
const saving = ref(false)
const activeTab = ref('general')

// Icons as functional components
const GeneralIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' })
])

const EmailIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' })
])

const AppearanceIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01' })
])

const SocialIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
])

const SystemIcon = () => h('svg', { class: 'w-5 h-5', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z' })
])

const settingGroups = computed(() => [
    { key: 'general', label: t('settings.groups.general'), icon: GeneralIcon },
    { key: 'email', label: t('settings.groups.email'), icon: EmailIcon },
    { key: 'appearance', label: t('settings.groups.appearance'), icon: AppearanceIcon },
    { key: 'social', label: t('settings.groups.social'), icon: SocialIcon },
    { key: 'system', label: t('settings.groups.system'), icon: SystemIcon },
])

const getGroupSettings = (group) => {
    return settingsStore.settings[group] || []
}

const getSettingLabel = (key) => {
    const translationKey = `settings.fields.${key.replace(/\./g, '_')}`
    return t(translationKey)
}

const getError = (key) => {
    const error = errors.value[key]
    if (!error) return ''
    return Array.isArray(error) ? error[0] : error
}

const loadSettings = async () => {
    loading.value = true
    try {
        await settingsStore.fetchAll()

        // Populate form
        for (const group in settingsStore.settings) {
            settingsStore.settings[group].forEach(setting => {
                form.value[setting.key] = setting.typed_value
            })
        }
    } catch (error) {
        toast.error(error.message || t('settings.loadError'))
    } finally {
        loading.value = false
    }
}

const handleSave = async () => {
    saving.value = true
    errors.value = {}

    try {
        await settingsStore.updateBatch(form.value)
        toast.success(t('settings.updateSuccess'))
    } catch (error) {
        if (error.errors) {
            errors.value = error.errors
        }
        if (error.status !== 422) {
            toast.error(error.message || t('settings.updateError'))
        }
    } finally {
        saving.value = false
    }
}

onMounted(() => {
    loadSettings()
})
</script>
