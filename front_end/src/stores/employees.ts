import apiEndPoints from '@/components/constants';
import axios from 'axios';

const useEmployeeStore  = {
    // Get all users
    async getAll(params:any = {}) {
        try {

            const response = await axios.get(`${import.meta.env.VITE_API_URL}${apiEndPoints.employees}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                },
                params: {
                    page: params.page || 1,
                    per_page: params.perPage || 15,
                    search: params.search || ''
                }
            });

            const responseData = response.data;

            if (Array.isArray(responseData)) {
                return {
                    data: responseData,
                    meta: {
                        current_page: 1,
                        per_page: params.perPage || 15,
                        total: responseData.length,
                        last_page: 1,
                        from: 1,
                        to: responseData.length
                    }
                };
            }

            if (responseData.items && responseData.pagination) {
                return {
                    data: responseData.items,
                    meta: responseData.pagination
                };
            }

            if (responseData.data && responseData.meta) {
                return responseData;
            }

            throw new Error('Unexpected API response format');

        } catch (error) {
            throw this.handleError(error);
        }
    },
 
    // Create a new vehicle
    async store(vehicleData:[]) {
        try {
            const formData = new FormData();
            for (const key in vehicleData) {
                formData.append(key, vehicleData[key]);
            }
            const response = await axios.post(`${import.meta.env.VITE_API_URL}${apiEndPoints.employees}`, vehicleData, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                    'Content-Type': 'multipart/form-data'
                }
            });
            return response.data;
        } catch (error) {
            throw this.handleError(error);
        }
    },

    // Get a vehicle by ID
    async getById(id:any) {
        try {
            const response = await axios.get(`${import.meta.env.VITE_API_URL}${apiEndPoints.employees}/${id}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                }
            });
            return response.data;
        } catch (error) {
            throw this.handleError(error);
        }
    },

    // Update a vehicle by ID
    async update(id:any, vehicleData:[]) {
        try {
            const formData = new FormData();
            for (const key in vehicleData) {
                formData.append(key, vehicleData[key]);
            }
            const response = await axios.post(`${import.meta.env.VITE_API_URL}${apiEndPoints.employees}/${id}?_method=PUT`, formData, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                    'Content-Type': 'multipart/form-data'
                }
            });
            return response.data;
        } catch (error) {
            throw this.handleError(error);
        }
    },

    // Delete a vehicle by ID
    async delete(id:any) {
        try {
            const response = await axios.delete(`${import.meta.env.VITE_API_URL}${apiEndPoints.employees}/${id}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                }
            });
            return response.data;
        } catch (error) {
            throw this.handleError(error);
        }
    },

    handleError(error:any) {
        if (error.response && error.response.data && error.response.data.message) {
            return new Error(error.response.data.message);
        }
        return new Error(error.message || 'An unknown error occurred');
    }

};

export default useEmployeeStore;