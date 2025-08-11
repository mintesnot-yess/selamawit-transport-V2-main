// @/stores/useRoleStore.ts

import apiEndPoints from '@/components/constants'
import axios from 'axios'

 const useRoleStore = {
    formLoading: false,
    roles: [],
    permissions: [] as string[], // This will hold your permission strings

    async fetchPermissions() {
        try {
            const response = await axios.get(`${import.meta.env.VITE_API_URL}${apiEndPoints.roles}`, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
                },
            })

            const data = response.data

            if (Array.isArray(data)) {
                this.permissions = data
            } else if (data.items) {
                this.permissions = data.items
            } else {
                throw new Error('Unexpected permissions response format')
            }

            return this.permissions
        } catch (error) {
            console.error('Failed to fetch permissions:', error)
            this.permissions = []
        }
    },

    // Existing methods below
    async getAll(params = { page: null, perPage: null, search: null }) {
        try {
            const response = await axios.get(`${import.meta.env.VITE_API_URL}${apiEndPoints.roles}`, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
                },
                params: {
                    page: params.page || 1,
                    per_page: params.perPage || 15,
                    search: params.search || '',
                },
            })

            const responseData = response.data

            if (Array.isArray(responseData)) {
                return {
                    data: responseData,
                    meta: {
                        current_page: 1,
                        per_page: params.perPage || 15,
                        total: responseData.length,
                        last_page: 1,
                        from: 1,
                        to: responseData.length,
                    },
                }
            }

            if (responseData.items && responseData.pagination) {
                return {
                    data: responseData.items,
                    meta: responseData.pagination,
                }
            }

            if (responseData.data && responseData.meta) {
                return responseData
            }

            throw new Error('Unexpected API response format')
        } catch (error) {
            console.error('Error fetching roles:', error)
            return {
                data: [],
                meta: {
                    current_page: 1,
                    per_page: 15,
                    total: 0,
                    last_page: 1,
                    from: 0,
                    to: 0,
                },
            }
        }
    },

    async getAllRole() {
        try {
            const response = await axios.get(`import.meta.env.VITE_API_URL}${apiEndPoints.roles}`, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
                },
            })

            const responseData = response.data

            if (Array.isArray(responseData)) {
                return {
                    data: responseData,
                    meta: {
                        current_page: 1,
                        per_page: 5,
                        total: responseData.length,
                        last_page: 1,
                        from: 1,
                        to: responseData.length,
                    },
                }
            }

            if (responseData.items && responseData.pagination) {
                return {
                    data: responseData.items,
                    meta: responseData.pagination,
                }
            }

            if (responseData.data && responseData.meta) {
                return responseData
            }

            throw new Error('Unexpected user response format')
        } catch (error) {
            console.error('Error fetching users:', error)
            return {
                data: [],
                meta: {
                    current_page: 1,
                    per_page: 5,
                    total: 0,
                    last_page: 1,
                    from: 0,
                    to: 0,
                },
            }
        }
    },

  // Create a new role
  async store(roleData: any) {
    try {
      const token = localStorage.getItem("auth_token");
      if (!token) {
        throw new Error("Authentication token not found. Please log in again.");
      }
      const response = await axios.post(`${import.meta.env.VITE_API_URL}${apiEndPoints.roles}`, roleData, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      });
      return response.data;
    } catch (error) {
     }
  },

  // Update a role
  async update(id: any, roleData: any) {
    try {
      const response = await axios.put(
        `${import.meta.env.VITE_API_URL}${apiEndPoints.updateRole(id)}`,
        roleData,
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            "Content-Type": "application/json",
          },
        },
      );
      return response.data;
    } catch (error) {
    //   throw this.handleError(error);
    }
  },
  // Get all permissions for a role
  async getPermissions(roleId: any) {
    try {
      const token = localStorage.getItem("auth_token");
      if (!token) {
        throw new Error("Authentication token not found. Please log in again.");
      }
      const response = await axios.get(
        `${import.meta.env.VITE_API_URL}${apiEndPoints.getRolePermissions(roleId)}`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        },
      );
      return response.data;
    } catch (error) {
     }
  },

  // Assign permissions to a role
  // Assign permissions to a role
  async assignPermissions(roleId: any, permissions: any) {
    try {
      const token = localStorage.getItem("auth_token");
      if (!token) {
        throw new Error("Authentication token not found. Please log in again.");
      }
      const response = await axios.post(
        `${import.meta.env.VITE_API_URL}${apiEndPoints.getRolePermissions(roleId)} `,
        permissions,
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        },
      );
      return response.data;
    } catch (error) {
     }
  },

  // Sync permissions (replace all existing permissions)
  async syncPermissions(roleId: any, permissions: any) {
    try {
      const token = localStorage.getItem("auth_token");
      if (!token) {
        throw new Error("Authentication token not found. Please log in again.");
      }
      const response = await axios.put(
        `${import.meta.env.VITE_API_URL}${apiEndPoints.getRolePermissions(roleId)} `,
        permissions,
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        },
      );
      return response.data;
    } catch (error) {
     }
  },

  // Delete a role
  async delete(id: any) {
    try {
      const response = await axios.delete(`${import.meta.env.VITE_API_URL}${apiEndPoints.deleteRole(id)} `, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
        },
      });
      return response.data;
    } catch (error) {
     }
  },
 }

export default useRoleStore;