import apiEndPoints from '@/components/constants';
import axios from 'axios';

const useOrderStore = {
    // Get all users
    async getAll(params: any = {}) {
        try {

            const response = await axios.get(`${import.meta.env.VITE_API_URL}${apiEndPoints.orders}`, {
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
    // async getById(id)

    // Create a new order
    async store(orderData: []) {
        try {
            const formData = new FormData();
            for (const key in orderData) {
                formData.append(key, orderData[key]);
            }
            const response = await axios.post(`${import.meta.env.VITE_API_URL}${apiEndPoints.orders}`, orderData, {
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
    // Get a order by ID
    async getById(id: any) {
        try {
            const response = await axios.get(`${import.meta.env.VITE_API_URL}${apiEndPoints.showOrder(id)}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                }
            });
            return response.data;
        } catch (error) {
            throw this.handleError(error);
        }
    },

    // Update a order by ID
    async update(id: any, orderData: []) {
        try {
            const formData = new FormData();
            for (const key in orderData) {
                formData.append(key, orderData[key]);
            }
            const response = await axios.post(`${import.meta.env.VITE_API_URL}${apiEndPoints.updateOrder(id)}?_method=PUT`, formData, {
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

    // Delete a order by ID
    async delete(id: any) {
        try {
            const response = await axios.delete(`${import.meta.env.VITE_API_URL}${apiEndPoints.deleteOrder(id)}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                }
            });
            return response.data;
        } catch (error) {
            throw this.handleError(error);
        }
    },

    handleError(error: any) {
        if (error.response && error.response.data && error.response.data.message) {
            return new Error(error.response.data.message);
        }
        return new Error(error.message || 'An unknown error occurred');
    }

};

export default useOrderStore;