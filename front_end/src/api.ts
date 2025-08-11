import axios from "axios";
import { AUTH_TOKEN_KEY } from "./key";

// Determine base URL based on environment
let baseURL;

if (import.meta.env.MODE === "development") {
  baseURL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";
} else {
  baseURL = import.meta.env.VITE_API_URL || "/api";
}

const api = axios.create({
  baseURL,
  timeout: 10000,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

// Request interceptor
api.interceptors.request.use((config) => {
  const token = localStorage.getItem(AUTH_TOKEN_KEY);
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// // Response interceptor
// api.interceptors.response.use(
//   (response) => response,
//   (error) => {
//     if (error.response?.status === 401) {
//       localStorage.removeItem(AUTH_TOKEN_KEY);
//       window.location.href = "/login";
//     }
//     return Promise.reject(error);
//   },
// );

export default api;
