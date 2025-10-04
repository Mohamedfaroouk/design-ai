import { reactive, ref } from 'vue'
import api from '@/services/api'
import { useToastStore } from '@/store'

export function useForm(initialData = {}) {
    const form = reactive({ ...initialData })
    const errors = ref({})
    const loading = ref(false)
    const toast = useToastStore()

    const setErrors = (newErrors) => {
        errors.value = newErrors
    }

    const clearErrors = () => {
        errors.value = {}
    }

    const clearError = (field) => {
        delete errors.value[field]
    }

    const hasError = (field) => {
        return !!errors.value[field]
    }

    const getError = (field) => {
        return errors.value[field]?.[0] || ''
    }

    const reset = () => {
        Object.keys(form).forEach(key => {
            form[key] = initialData[key]
        })
        clearErrors()
    }

    const submit = async (method, url, options = {}) => {
        loading.value = true
        clearErrors()

        try {
            const response = await api[method](url, form)

            if (options.onSuccess) {
                options.onSuccess(response)
            }

            if (options.successMessage) {
                toast.success(options.successMessage)
            }

            if (options.resetOnSuccess) {
                reset()
            }

            return response
        } catch (err) {
            if (err.errors) {
                setErrors(err.errors)
            }

            if (options.onError) {
                options.onError(err)
            } else if (!options.silent) {
                toast.error(err.message || 'An error occurred')
            }

            throw err
        } finally {
            loading.value = false
        }
    }

    const post = (url, options = {}) => submit('post', url, options)
    const put = (url, options = {}) => submit('put', url, options)
    const patch = (url, options = {}) => submit('patch', url, options)
    const destroy = (url, options = {}) => submit('delete', url, options)

    return {
        form,
        errors,
        loading,
        setErrors,
        clearErrors,
        clearError,
        hasError,
        getError,
        reset,
        submit,
        post,
        put,
        patch,
        delete: destroy
    }
}
