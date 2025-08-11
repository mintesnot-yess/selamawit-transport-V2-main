import { useProfileStore } from '@/stores/profile';
import { computed } from 'vue';

export function usePermissions() {
    const userStore = useProfileStore();

    function hasPermission(permission: string): boolean {
        return userStore.permissions.includes(permission);
    }
 
    function hasAnyPermission(permissions: string[]): boolean {
        return permissions.some(permission => hasPermission(permission));
    }

    const permissions = computed(() => userStore.permissions);

    return {
        hasPermission,
        hasAnyPermission,
        permissions,
    };

}