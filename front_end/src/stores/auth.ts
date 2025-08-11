import api from "@/api.ts";
import apiEndPoints from "@/components/constants";
import { useProfileStore } from "./profile";
import { AUTH_TOKEN_KEY } from "@/key";
 
export default {
  
  async login(credentials: any) {
    try {
      const response = await api.post(`${import.meta.env.VITE_API_URL}${apiEndPoints.login}`, credentials);

      return response.data;
    } catch (error:any) {
      throw new Error(error.response?.data?.message || "Login failed");
    }
  },

  setAuthToken(token: any) {
    localStorage.setItem(AUTH_TOKEN_KEY, token);
    api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  },

  async fetchUser() {

    const token = localStorage.getItem(AUTH_TOKEN_KEY);
    if (!token) {
      throw new Error("Authentication required.");
    }

    try {

       const response = await api.get(apiEndPoints.user);
      
      // localStorage.setItem('user', JSON.stringify(response.data))
      const profile = useProfileStore();
        
      profile.$patch({
        user: response.data.user,
        permissions: response.data.permissions,
        token:token
      });

      return response.data;

    } catch (error) {
      console.error("Fetch user error:", error);
      // Optionally dispatch logout
       this.logout();
      throw error;
    }
  },
  async logout() {
    try {
      await api.post(apiEndPoints.logout);
    } catch (error) {
      console.error("Logout error:", error);
    } finally {
      // Clear local storage
      localStorage.removeItem(AUTH_TOKEN_KEY);
      localStorage.removeItem("user");
      // Remove axios authorization header
      delete api.defaults.headers.common["Authorization"];
      // Redirect to login page
      window.location.href = "/";

    }
  },
}
