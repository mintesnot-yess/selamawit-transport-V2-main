import axios from 'axios';
import apiEndPoints from '@/components/constants'

const permissionService = {
    // Get all permissions
    // permissionService.js

    async getAll() {
        try {
            const response = await axios.get(`${import.meta.env.VITE_API_URL}${apiEndPoints.permissions}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                },
                 
            });

            // Transform response if needed
            const responseData = response.data;

            // If API returns data directly without meta wrapper
            if (Array.isArray(responseData)) {
                return {
                    data: responseData,
                    meta: {
                        current_page: 1,
                         total: responseData.length,
                        last_page: 1,
                        from: 1,
                        to: responseData.length
                    }
                };
            }

            // If API returns items/pagination instead of data/meta
            if (responseData.items && responseData.pagination) {
                return {
                    data: responseData.items,
                    meta: responseData.pagination
                };
            }

            // If already in correct format
            if (responseData.data && responseData.meta) {
                return responseData;
            }

            throw new Error('Unexpected API response format');

        } catch (error) {
            throw this.handleError(error);
        }
    },

    // Create a new permission
    async store(permissionData: any) {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                throw new Error('Authentication token not found. Please log in again.');
            }
            const response = await axios.post(`${import.meta.env.VITE_API_URL}${apiEndPoints.permissions}`, permissionData, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });
            return response.data;
        } catch (error) {
            throw this.handleError(error);
        }
    },

    // Update a permission
    async update(id: any, permissionData: any) {
        try {
            const response = await axios.put(`${import.meta.env.VITE_API_URL}${apiEndPoints.updatePermission(id)}`, permissionData, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                    'Content-Type': 'application/json',

                }
            });
            return response.data;
        } catch (error) {
            throw this.handleError(error);
        }
    },

    // Delete a permission
    async delete(id: any) {
        try {
            const response = await axios.delete(`${import.meta.env.VITE_API_URL}${apiEndPoints.deletePermission(id)}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                }
            });
            return response.data;
        } catch (error) {
            throw this.handleError(error);
        }
    },

    async search(params = {}) {
        try {
            const response = await axios.get(`${import.meta.env.VITE_API_URL}${apiEndPoints.permissions}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                },
                
            });
            return response.data;
        } catch (error:any) {
            throw error.response?.data?.message || 'Search failed';
        }
    },

    // Handle API errors
    handleError(error: any) {
        if (error.response) {
            // The request was made and the server responded with a status code
            const message = error.response.data?.message || error.response.statusText;
            return new Error(message || 'Permission operation failed');
        } else if (error.request) {
            // The request was made but no response was received
            return new Error('No response from server. Please check your connection.');
        } else {
            // Something happened in setting up the request
            return new Error(error.message || 'Error configuring permission request');
        }
    }
};

export default permissionService;
