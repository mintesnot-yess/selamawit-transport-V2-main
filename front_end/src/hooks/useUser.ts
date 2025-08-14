// stores/userStore.ts
import auth from '@/stores/auth'
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { string } from 'zod'

export const useUserStore = defineStore('user', () => {
    const user = ref(null)
    const permissions = ref<string[]>([])
    const roles = ref<string[]>([])
    const loading = ref(false)
    const error = ref(null)

    const fetchUser = async () => {
        loading.value = true
        error.value = null

        try {
            const userData = await auth.fetchUser()
            user.value = userData
            permissions.value = userData.permissions || []
            roles.value = userData.roles || []
        } catch (err) {
            error.value = err,
                console.error('Failed to fetch user:', err)
        } finally {
            loading.value = false
        }
    }

    return {
        user,
        permissions,
        roles,
        loading,
        error,
        fetchUser,
    }
})