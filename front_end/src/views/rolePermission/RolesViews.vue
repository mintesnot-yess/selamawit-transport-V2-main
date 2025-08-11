<template>
  <div class="flex flex-col gap-6 p-6 rounded-xl">
    <div class="p-4 md:p-6 space-y-6">
      <!-- Combined Role & Permission Management -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Roles Section -->
        <div
          class="lg:col-span-1 h-fit md:sticky top-0 bg-white rounded-xl shadow-sm border border-surface-200"
        >
          <div class="px-6 py-4 border-b border-surface-200">
            <div
              class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
              <div>
                <h2 class="text-lg font-semibold text-surface-900">Roles</h2>
                <p class="text-xs text-surface-500 mt-1">
                  {{ roles.length }} role{{ roles.length !== 1 ? "s" : "" }} in
                  system
                </p>
              </div>
              <Button @click="togglePanel">
                <i class="fas fa-plus"></i>
                <span>Add Role</span>
              </Button>
            </div>
          </div>

          <div class="px-4 py-2 border-b border-surface-200">
            <div class="relative">
              <input
                v-model="roleSearch"
                @input="filterRoles"
                placeholder="Search roles..."
                class="pl-8 pr-4 py-2 w-full border border-surface-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
              <i
                class="fas fa-search absolute left-3 top-3 text-surface-400 text-sm"
              ></i>
            </div>
          </div>

          <div class="overflow-y-auto" style="max-height: 600px">
            <table class="min-w-full divide-y divide-surface-200">
              <thead class="bg-surface-50 sticky top-0">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider"
                  >
                    Role Name
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider"
                  >
                    Perms
                  </th>
                  <th scope="col" class="relative px-6 py-3"></th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-surface-200">
                <template v-if="loadingRoles">
                  <tr v-for="i in 5" :key="`skeleton-${i}`">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div
                        class="h-4 bg-surface-200 rounded w-3/4 animate-pulse"
                      ></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div
                        class="h-4 bg-surface-200 rounded w-8 animate-pulse"
                      ></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div
                        class="h-4 bg-surface-200 rounded w-8 animate-pulse"
                      ></div>
                    </td>
                  </tr>
                </template>
                <template v-else>
                  <tr
                    v-for="role in filteredRoles"
                    :key="role.id"
                    class="hover:bg-surface-50"
                    :class="{
                      'bg-blue-50': selectedRole && selectedRole.id === role.id,
                    }"
                  >
                    <td
                      @click="selectRole(role)"
                      class="px-6 py-4 whitespace-nowrap cursor-pointer"
                    >
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-user-cog"
                          >
                            <path d="M10 15H6a4 4 0 0 0-4 4v2" />
                            <circle cx="18" cy="15" r="3" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M19 15v3.5" />
                            <path d="M16 18h6" />
                          </svg>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-surface-900">
                            {{ role.name }}
                          </div>
                          <div class="text-xs text-surface-500">
                            {{ role.description || "No description" }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-surface-700"
                    >
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                      >
                        {{ role.permissions_count }}
                      </span>
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                    >
                      <DropdownAction
                        :item="role"
                        :isEdit="true"
                        :isDelete="true"
                        :isShow="false"
                        @delete="confirmDelete"
                        @edit="editLocation(role)"
                      />
                    </td>
                  </tr>
                  <tr v-if="filteredRoles.length === 0 && !loadingRoles">
                    <td
                      colspan="3"
                      class="px-4 py-8 text-center text-surface-400 text-base font-medium"
                    >
                      <i
                        class="fas fa-building-columns text-2xl mb-2 block"
                      ></i>
                      No roles found
                    </td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Permissions Section -->
        <div
          class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-surface-200"
        >
          <div class="px-6 py-4 border-b border-surface-200">
            <div
              class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
              <div>
                <h2 class="text-lg font-semibold text-surface-900">
                  Permissions
                  <span v-if="selectedRole" class="text-blue-600"
                    >for {{ selectedRole.name }}</span
                  >
                  <span v-else class="text-gray-500">(Select a role)</span>
                </h2>
                <p class="text-sm text-surface-500 mt-1" v-if="selectedRole">
                  <span class="font-medium">{{
                    selectedPermissions.length
                  }}</span>
                  permission{{ selectedPermissions.length !== 1 ? "s" : "" }}
                  selected
                </p>
              </div>
              <div class="flex gap-2">
                <Button
                  @click="saveRolePermissions"
                  :disabled="!selectedRole || isSaving"
                >
                  <i class="fas fa-save" :class="{ 'fa-spin': isSaving }"></i>
                  <span>{{ isSaving ? "Saving..." : "Save Permissions" }}</span>
                </Button>
              </div>
            </div>
          </div>

          <div class="p-6">
            <div
              v-if="!selectedRole"
              class="text-center py-12 text-surface-400"
            >
              <i class="fas fa-user-shield text-4xl mb-4"></i>
              <p class="text-lg">Please select a role to manage permissions</p>
            </div>

            <div v-else>
              <div class="mb-4 flex justify-between items-center">
                <div class="flex items-center">
                  <input
                    type="checkbox"
                    :checked="allPermissionsSelected"
                    @change="toggleSelectAllPermissions"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mr-2"
                  />
                  <span class="text-sm text-surface-700">
                    {{ allPermissionsSelected ? "Deselect All" : "Select All" }}
                  </span>
                </div>
                <div class="relative">
                  <input
                    v-model="permissionSearch"
                    @input="filterPermissions"
                    placeholder="Filter permissions..."
                    class="pl-8 pr-4 py-2 border border-surface-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                  <i
                    class="fas fa-search absolute left-3 top-3 text-surface-400 text-sm"
                  ></i>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <template v-if="loadingPermissions">
                  <div
                    v-for="i in 6"
                    :key="`perm-skeleton-${i}`"
                    class="p-3 border rounded-lg bg-surface-50 animate-pulse"
                  >
                    <div class="h-4 w-3/4 bg-surface-200 rounded mb-2"></div>
                    <div class="h-3 w-1/2 bg-surface-200 rounded"></div>
                  </div>
                </template>

                <template v-else>
                  <label
                    v-for="permission in filteredPermissions"
                    :key="permission.id"
                    class="flex items-start p-3 border rounded-lg hover:bg-surface-50 cursor-pointer transition-colors"
                    :class="{
                      'border-blue-300 bg-blue-50':
                        selectedPermissions.includes(String(permission.id)),
                    }"
                  >
                    <div class="flex items-center h-5">
                      <input
                        type="checkbox"
                        v-model="selectedPermissions"
                        :value="String(permission.id)"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                      />
                    </div>
                    <div class="ml-3 text-sm">
                      <span class="font-medium text-surface-700">{{
                        permission.name
                      }}</span>
                      <p class="text-surface-500 mt-1 text-xs">
                        {{ permission.description || "No description" }}
                      </p>
                      <div v-if="permission.guard_name" class="mt-1">
                        <span
                          class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                        >
                          {{ permission.guard_name }}
                        </span>
                      </div>
                    </div>
                  </label>
                </template>

                <div
                  v-if="filteredPermissions.length === 0 && !loadingPermissions"
                  class="col-span-full text-center py-8 text-surface-400"
                >
                  <i class="fas fa-ban text-2xl mb-2"></i>
                  <p>No permissions found</p>
                  <button
                    v-if="permissionSearch"
                    @click="clearPermissionSearch"
                    class="mt-2 text-sm text-blue-500 hover:text-blue-700"
                  >
                    Clear search
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Role Form Modal -->
      <div
        v-if="isSideFormVisible"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div
          class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
        >
          <!-- Background overlay -->
          <div
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            aria-hidden="true"
            @click="toggleAddRoleForm"
          ></div>

          <!-- Modal content -->
          <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
          >
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div
                  class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full"
                >
                  <div class="flex justify-between items-center mb-3">
                    <h3
                      class="text-lg leading-6 font-medium text-gray-900"
                      id="modal-title"
                    >
                      {{ isUpdating ? "Update" : "Add" }} Role
                    </h3>
                    <button
                      @click="toggleAddRoleForm"
                      class="text-gray-400 hover:text-gray-500 focus:outline-none"
                    >
                      <span class="sr-only">Close</span>
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div
        v-if="showUsageModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="usage-modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div
          class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
        >
          <div
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            aria-hidden="true"
            @click="showUsageModal = false"
          ></div>

          <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
          >
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div
                  class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full"
                >
                  <div class="flex justify-between items-center mb-3">
                    <h3
                      class="text-lg leading-6 font-medium text-gray-900"
                      id="usage-modal-title"
                    >
                      Permission Usage: {{ selectedRole.name }}
                    </h3>
                    <button
                      @click="showUsageModal = false"
                      class="text-gray-400 hover:text-gray-500 focus:outline-none"
                    >
                      <span class="sr-only">Close</span>
                      <i class="fas fa-times"></i>
                    </button>
                  </div>

                  <div class="mt-4">
                    <div v-if="loadingUsage" class="text-center py-8">
                      <i
                        class="fas fa-spinner fa-spin text-2xl text-blue-500"
                      ></i>
                      <p class="mt-2 text-sm text-gray-500">
                        Loading usage data...
                      </p>
                    </div>

                    <div v-else>
                      <div
                        v-if="permissionUsage.length === 0"
                        class="text-center py-8 text-gray-400"
                      >
                        <i class="fas fa-info-circle text-2xl mb-2"></i>
                        <p>No usage data found for this role's permissions</p>
                      </div>

                      <div v-else class="space-y-4">
                        <div
                          v-for="usage in permissionUsage"
                          :key="usage.permission_id"
                          class="border rounded-md p-4"
                        >
                          <div class="flex items-center justify-between">
                            <h4 class="font-medium text-gray-900">
                              {{ getPermissionName(usage.permission_id) }}
                            </h4>
                            <span
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                            >
                              {{ usage.users_count }} user{{
                                usage.users_count !== 1 ? "s" : ""
                              }}
                            </span>
                          </div>

                          <div
                            v-if="usage.users && usage.users.length > 0"
                            class="mt-3"
                          >
                            <div class="text-sm text-gray-500 mb-2">
                              Users with this permission:
                            </div>
                            <div class="flex flex-wrap gap-2">
                              <span
                                v-for="user in usage.users"
                                :key="user.id"
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                              >
                                {{ user.name }}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div
              class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
            >
              <button
                type="button"
                @click="showUsageModal = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ConfirmDelete
      v-model:open="showDeleteDialog"
      :title="deleteTitle"
      description="Are you sure you want to delete this Role? This action cannot be undone."
      confirm-label="Delete Role"
      @confirm="handleDelete"
    />
    <Panel
      v-model="showPanel"
      :title="isUpdate ? 'Update role Information' : 'Create A Role'"
      description="Fill the Location Information"
    >
      <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <div class="flex-1 space-y-2">
          <FormField name="name">
            <FormItem>
              <FormLabel>Role Name</FormLabel>
              <FormControl>
                <Input v-model="form.name" type="text" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
        </div>
        <div v-if="error" class="text-red-500 text-sm text-center mt-2">
          {{ error }}
        </div>
        <Button
          type="submit"
          class="mt-2 w-full text-white font-semibold py-2 rounded-md transition-colors duration-200"
        >
          <span v-if="loading" class="flex items-center justify-center">
            <LoaderCircle class="animate-spin mr-2 size-5" />
            Loading...
          </span>
          <span v-else> {{ isUpdate ? "Edit" : "Submit" }} </span>
        </Button>
      </form>
    </Panel>
  </div>
</template>

<script>
import { debounce } from "lodash";
import useRoleStore from "@/stores/roles";
import permissionService from "@/stores/permissions";
import Panel from "@/components/panels/Panel.vue";
import { FormField } from "@/components/ui";
import FormItem from "@/components/ui/form/FormItem.vue";
import FormLabel from "@/components/ui/form/FormLabel.vue";
import FormControl from "@/components/ui/form/FormControl.vue";
import Input from "@/components/ui/input/Input.vue";
import { LoaderCircle } from "lucide-vue-next";
import Button from "@/components/ui/button/Button.vue";
import { DropdownAction } from "@/components/table";
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";

export default {
  components: {
    Panel,
    FormField,
    FormItem,
    FormLabel,
    FormControl,
    Input,
    LoaderCircle,
    Button,
    DropdownAction,
    ConfirmDelete,
  },
  data() {
    return {
      roles: [],
      filteredRoles: [],
      selectedRole: null,
      loadingRoles: false,
      isSideFormVisible: false,
      isUpdating: false,
      roleSearch: "",

      // Permissions data
      permissions: [],
      filteredPermissions: [],
      selectedPermissions: [],
      loadingPermissions: false,
      isSaving: false,
      permissionSearch: "",

      // Forms
      form: {
        id: null,
        name: "",
      },

      // Permission usage
      showUsageModal: false,
      permissionUsage: [],
      loadingUsage: false,
      showPanel: false,
      showDeleteDialog: false,
      deleteTitle: "",
      deleteId: null,
      // Status messages
      error: null,
      success: null,
      loading: false,
    };
  },

  computed: {
    allPermissionsSelected() {
      if (this.filteredPermissions.length === 0) return false;
      return this.filteredPermissions.every((perm) =>
        this.selectedPermissions.includes(String(perm.id))
      );
    },
  },

  watch: {
    selectedRole(newRole) {
      if (newRole) {
        this.loadRolePermissions();
      }
    },
  },

  async created() {
    this.fetchRoles();
    await this.fetchAllPermissions();
  },

  methods: {
    async fetchRoles() {
      this.loadingRoles = true;
      try {
        const response = await useRoleStore.getAll();
        this.roles = response.data;
        this.filteredRoles = [...this.roles];

        // If we have a selected role, refresh its data
        if (this.selectedRole) {
          const updatedRole = this.roles.find(
            (r) => r.id === this.selectedRole.id
          );
          if (updatedRole) {
            this.selectedRole = updatedRole;
          }
        }
      } catch (error) {
        console.error("Error fetching roles:", error);
        this.error = "Failed to load roles. Please try again.";
        this.$toast.error("Failed to load roles");
      } finally {
        this.loadingRoles = false;
      }
    },
    handleSubmit() {
      if (this.isUpdate) {
        this.handleSubmitUpdate();
      } else {
        this.handleSubmitAdd();
      }
    },
    filterRoles() {
      if (!this.roleSearch.trim()) {
        this.filteredRoles = [...this.roles];
        return;
      }

      const searchTerm = this.roleSearch.toLowerCase();
      this.filteredRoles = this.roles.filter(
        (role) =>
          role.name.toLowerCase().includes(searchTerm) ||
          (role.description &&
            role.description.toLowerCase().includes(searchTerm))
      );
    },

    selectRole(role) {
      this.selectedRole = role;
    },

    async loadRolePermissions() {
      if (!this.selectedRole) return;

      this.loadingPermissions = true;
      this.selectedPermissions = [];

      try {
        const response = await useRoleStore.getPermissions(
          this.selectedRole.id
        );

        // Handle response format variations
        let permissionsData = [];
        if (Array.isArray(response.permissions)) {
          permissionsData = response.permissions;
        } else if (
          response.permission &&
          Array.isArray(response.permission.id)
        ) {
          permissionsData = response.permission.id.map((id) => ({ id }));
        } else if (Array.isArray(response)) {
          permissionsData = response;
        }

        this.selectedPermissions = permissionsData
          .filter((p) => p !== null && p !== undefined)
          .map((p) => String(p.id || p));
      } catch (error) {
        console.error("Error loading permissions:", error);
        this.$toast.error("Failed to load permissions");
      } finally {
        this.loadingPermissions = false;
      }
    },

    async fetchAllPermissions() {
      this.loadingPermissions = true;
      try {
        const response = await permissionService.getAll();
        this.permissions = response.data;
        this.filteredPermissions = [...this.permissions];
      } catch (error) {
        console.error("Error fetching permissions:", error);
        this.$toast.error("Failed to load permissions");
      } finally {
        this.loadingPermissions = false;
      }
    },

    filterPermissions: debounce(function () {
      if (!this.permissionSearch.trim()) {
        this.filteredPermissions = [...this.permissions];
        return;
      }

      const searchTerm = this.permissionSearch.toLowerCase();
      this.filteredPermissions = this.permissions.filter(
        (perm) =>
          perm.name.toLowerCase().includes(searchTerm) ||
          (perm.description &&
            perm.description.toLowerCase().includes(searchTerm))
      );
    }, 300),

    clearPermissionSearch() {
      this.permissionSearch = "";
      this.filteredPermissions = [...this.permissions];
    },

    toggleSelectAllPermissions() {
      const allFilteredPermIds = this.filteredPermissions.map((p) =>
        String(p.id)
      );

      if (this.allPermissionsSelected) {
        // Remove all filtered permissions from selection
        this.selectedPermissions = this.selectedPermissions.filter(
          (id) => !allFilteredPermIds.includes(id)
        );
      } else {
        // Add all filtered permissions to selection (without duplicates)
        const newSelection = [
          ...new Set([...this.selectedPermissions, ...allFilteredPermIds]),
        ];
        this.selectedPermissions = newSelection;
      }
    },

    async saveRolePermissions() {
      if (!this.selectedRole) return;

      this.isSaving = true;
      try {
        await useRoleStore.assignPermissions(this.selectedRole.id, {
          permissions: this.selectedPermissions,
        });

        // Update the permissions count for the selected role
        const updatedRole = await useRoleStore.get(this.selectedRole.id);
        this.selectedRole.permissions_count = updatedRole.permissions_count;

        this.$toast.success("Permissions saved successfully");
      } catch (error) {
        console.error("Error saving permissions:", error);
        this.$toast.error("Failed to save permissions");
      } finally {
        this.isSaving = false;
      }
    },

    openAddRoleForm() {
      this.isUpdating = false;
      this.resetForm();
      this.isSideFormVisible = true;
    },

    toggleAddRoleForm() {
      this.isSideFormVisible = !this.isSideFormVisible;
      if (!this.isSideFormVisible) {
        this.resetForm();
      }
    },

    editRole(role) {
      this.form = {
        id: role.id,
        name: role.name,
        description: role.description || "",
      };
      this.isUpdating = true;
      this.isSideFormVisible = true;
    },

    async handleSubmitAdd() {
      this.loading = true;
      this.error = null;
      this.success = null;

      try {
        const response = await useRoleStore.store(this.form);
        this.success = "Role created successfully";
        await this.fetchRoles();
        this.showPanel = false;
        this.isUpdate = false;
        this.editId = null;
      } catch (error) {
        this.error =
          error.response?.data?.message ||
          error.message ||
          "Failed to create role";
        console.error("Role creation error:", error);
      } finally {
        this.loading = false;
      }
    },
    editLocation(role) {
      this.isUpdate = true;
      this.editId = role.id;
      this.form = {
        name: role.name,
        description: role.description,
      };
      this.showPanel = true;
    },

    async handleSubmitUpdate() {
      this.loading = true;
      this.error = null;
      this.success = null;

      try {
        await useRoleStore.update(this.editId, this.form);
        this.success = "Role updated successfully";
        await this.fetchRoles();

        // If we're updating the currently selected role, update it
        if (this.selectedRole && this.selectedRole.id === this.form.id) {
          this.selectedRole.name = this.form.name;
          this.selectedRole.description = this.form.description;
        }
      } catch (error) {
        this.error =
          error.response?.data?.message ||
          error.message ||
          "Failed to update role";
        console.error("Role update error:", error);
      } finally {
        this.loading = false;
      }
    },

    confirmDelete(location) {
      this.deleteTitle = `Delete ${location.name}`;
      this.deleteId = location.id;
      this.showDeleteDialog = true;
    },
    async handleDelete() {
      try {
        this.loading = true;
        await useRoleStore.delete(this.deleteId);

        await this.fetchRoles(); // Refresh the list
      } catch (error) {
        this.error = error.message || "Failed to delete bank";
      } finally {
        this.loading = false;
        this.showDeleteDialog = false;
        this.deleteId = null;
      }
    },

    async showPermissionUsage() {
      if (!this.selectedRole) return;

      this.showUsageModal = true;
      this.loadingUsage = true;
      this.permissionUsage = [];

      try {
        // Fetch users with this role's permissions
        const response = await useRoleStore.getPermissionUsage(
          this.selectedRole.id
        );
        this.permissionUsage = response.data;
      } catch (error) {
        console.error("Error fetching permission usage:", error);
        this.$toast.error("Failed to load permission usage data");
      } finally {
        this.loadingUsage = false;
      }
    },

    getPermissionName(permissionId) {
      const permission = this.permissions.find((p) => p.id === permissionId);
      return permission ? permission.name : `Permission ID: ${permissionId}`;
    },
    togglePanel() {
      this.showPanel = !this.showPanel;
      if (this.showPanel) {
        this.resetForm();
        this.isUpdate = false;
      }
    },

    resetForm() {
      this.form = {
        id: null,
        name: "",
        description: "",
      };
      this.error = null;
      this.success = null;
    },
  },
};
</script>

<style scoped>
/* Add any custom styles here */
.bg-surface-50 {
  background-color: #f9fafb;
}
.bg-surface-100 {
  background-color: #f3f4f6;
}
.border-surface-200 {
  border-color: #e5e7eb;
}
.text-surface-500 {
  color: #6b7280;
}
.text-surface-700 {
  color: #374151;
}
.text-surface-900 {
  color: #111827;
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
}
.duration-200 {
  transition-duration: 200ms;
}

/* Focus styles */
.focus\:ring-2:focus {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0
    var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0
    calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow),
    var(--tw-shadow, 0 0 #0000);
}
.focus\:ring-blue-500:focus {
  --tw-ring-color: #3b82f6;
}
.focus\:ring-offset-2:focus {
  --tw-ring-offset-width: 2px;
}
</style>