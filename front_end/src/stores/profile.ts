import api from "@/api";
import apiEndPoints from "@/components/constants";
import { ACCESS_TOKEN_KEY, AUTH_TOKEN_KEY } from "@/key";
import type { User } from "@/types";
import { defineStore } from "pinia";

interface ProfileState {
    user: User | null,
    permissions: string[],
    token: string | null,
}

export const useProfileStore = defineStore('profile', {
    state: (): ProfileState => ({
        user: null,
        permissions: [],
        token: localStorage.getItem(ACCESS_TOKEN_KEY),
    }),

    // actions: {
    //     async fetchProfile() {
    //         const token = localStorage.getItem(AUTH_TOKEN_KEY);
    //         if (!token) {
    //           throw new Error("Authentication required.");
    //         }
        
    //         try {
    //           const response = await api.get(apiEndPoints.user);
    //           const profile = useProfileStore();

    //           profile.$patch({
    //             user: response.data.user,
    //             permissions: response.data.permissions,
    //             token:token
    //           });
        
    //           return response.data;
    //         } catch (error) {
    //           console.error("Fetch user error:", error);
    //           // Optionally dispatch logout
    //           // useAuthStore().logout();
    //           throw error;
    //         }
    //       }
    // }
    getters: {
      hasPermission: (state) => (permission: string) => {
        return state.permissions.includes(permission);
      },
    },
    persist: true,
})
