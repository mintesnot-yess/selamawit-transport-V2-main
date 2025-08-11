<template>
  <div class="flex flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card">
    <div class="flex items-start justify-between">
      <div class="flex gap-2 flex-col">
        <h4 class="text-lg font-semibold leading-none tracking-tight">Users</h4>
        <p class="text-sm text-muted-foreground"></p>
      </div>
      <div class="flex items-center gap-2">
        <Button
          @click="togglePanel()"
          variant="ghost"
          class="text-sm text-muted-foreground flex gap-1 items-center justify-center cursor-pointer"
        >
          <CirclePlus />
          Create New
        </Button>
      </div>
    </div>

    <div class="rounded-lg overflow-hidden">
      <Table
        :columns="columns"
        :data="Users"
        :isPagination="true"
        :isSearchable="true"
        :is-filter-select="false"
      />
    </div>
    <ConfirmDelete
      v-model:open="showDeleteDialog"
      :title="deleteTitle"
      description="Are you sure you want to delete this User? This action cannot be undone."
      confirm-label="Delete User"
      @confirm="handleDelete"
    />

    <Panel
      v-model="showPanel"
      :title="isUpdate ? 'Update User Information' : 'Create A User'"
      description="Fill the User Information"
    >
      <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <div class="flex-1 space-y-2">
          <FormField name="name">
            <FormItem>
              <FormLabel>User Name</FormLabel>
              <FormControl>
                <Input type="text" v-model="form.name" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
          <FormField name="email">
            <FormItem>
              <FormLabel>Email</FormLabel>
              <FormControl>
                <Input type="email" v-model="form.email" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
          <template v-if="!isUpdate">
            <FormField name="password">
              <FormItem>
                <FormLabel>Password</FormLabel>
                <FormControl>
                  <Input type="password" v-model="form.password" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
            <FormField name="password_confirmation">
              <FormItem>
                <FormLabel>Confirm Password </FormLabel>
                <FormControl>
                  <Input type="password" v-model="form.password_confirmation" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
          </template>

          <FormField name="role">
            <FormItem>
              <FormLabel>Role</FormLabel>
              <Select v-model="form.role">
                <FormControl>
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select type" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent>
                  <SelectItem
                    v-for="item in roles"
                    :key="item.id"
                    :value="item.id"
                    >{{ item.name }}</SelectItem
                  >
                </SelectContent>
              </Select>

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

<script >
import {
  ArrowUpDown,
  CirclePlus,
  Loader2,
  LoaderCircle,
} from "lucide-vue-next";
import { Panel } from "@/components/panels";
import {
  Button,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
  Input,
  Checkbox,
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
  Badge,
} from "@/components/ui";

import { Table, DropdownAction } from "@/components/table";
import { h, ref, shallowRef } from "vue";
import useUserStore from "@/stores/users";
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";

export default {
  components: {
    Panel,
    Table,
    DropdownAction,
    Button,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
    Input,
    Checkbox,
    ArrowUpDown,
    CirclePlus,
    Select,
    SelectTrigger,
    SelectValue,
    Loader2,
    SelectContent,
    SelectItem,
    Badge,
    LoaderCircle,
    ConfirmDelete,
  },
  data() {
    return {
      showPanel: false,
      loading: false,
      error: "",
      Users: [],
      roles: [],
      columns: [],
      form: {
        id: null,
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        role: "",
      },

      // updated component
      isUpdate: false,
      editId: null,
      // deleted component
      showDeleteDialog: false,
      deleteTitle: "",
      deleteId: null,

      canEdit: true,
      canCreate: true,
      canDelete: true,
      canView: true,
    };
  },
  created() {
    this.fetchUsers();
    this.ColumnDefinitions();
    this.giveRoles();
  },
  methods: {
    async fetchUsers() {
      try {
        const response = await useUserStore.getAll();
        this.Users = response.data || [];
        this.roles = response.roles || [];
        console.table(this.roles);
      } catch (error) {
        console.error("Failed to fetch users:", error);
      }
    },

    handleSubmit() {
      if (this.isUpdate) {
        this.handleSubmitUpdate();
      } else {
        this.handleSubmitAdd();
      }
    },

    async handleSubmitAdd() {
      this.error = null;
      this.loading = true;

      try {
        const response = await useUserStore.store(this.form);
        await this.fetchUsers();
        this.showPanel = false;
        this.error = "";
      } catch (error) {
        this.error =
          error.response?.data?.message ||
          error.message ||
          "Failed to create user";
      } finally {
        this.loading = false;
      }
    },
    editUser(user) {
      this.isUpdate = true;
      this.editId = user.id;
      this.form = {
        name: user.name,
        email: user.email,
        role: user.role[0].id,
      };
      this.showPanel = true;
    },

    async handleSubmitUpdate() {
      try {
        this.loading = true;
        const response = await useUserStore.update(this.editId, this.form);

        this.showPanel = false;
        this.fetchUsers(); // Refresh the list
        this.resetForm();
        this.isUpdate = false;
        this.editId = null;
      } catch (error) {
        this.error = error.message || "Failed to update User";
      } finally {
        this.loading = false;
      }
    },

    confirmDelete(user) {
      this.deleteTitle = `Delete ${user.name}`;
      this.deleteId = user.id;
      this.showDeleteDialog = true;
    },
    async handleDelete() {
      try {
        this.loading = true;
        await useUserStore.delete(this.deleteId);

        await this.fetchUsers(); // Refresh the list
      } catch (error) {
        this.error = error.message || "Failed to delete User";
      } finally {
        this.loading = false;
        this.showDeleteDialog = false;
        this.deleteId = null;
      }
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
        name: "",
      };
      this.error = "";
    },
    ColumnDefinitions() {
      this.columns = [
        {
          id: "select",
          header: ({ table }) =>
            h(Checkbox, {
              modelValue:
                table.getIsAllPageRowsSelected() ||
                (table.getIsSomePageRowsSelected() && "indeterminate"),
              "onUpdate:modelValue": (value) =>
                table.toggleAllPageRowsSelected(!!value),
              ariaLabel: "Select all",
            }),
          cell: ({ row }) =>
            h(Checkbox, {
              modelValue: row.getIsSelected(),
              "onUpdate:modelValue": (value) => row.toggleSelected(!!value),
              ariaLabel: "Select row",
            }),
          enableSorting: false,
          enableHiding: false,
        },

        {
          id: "index", // Don't use accessorKey for computed fields
          header: "#",
          cell: ({ row }) => {
            const index = row.index + 1; // +1 to start from 1 instead of 0
            return h("div", { class: "text-sm" }, index.toString());
          },
        },
        {
          accessorKey: "name",
          header: "User name",
          cell: ({ row }) => {
            const User_name = row.getValue("name"); // Access the nested vehicle object
            return h("div", { class: "text-sm" }, User_name);
          },
        },
        {
          accessorKey: "email",
          header: "Email",
          cell: ({ row }) => {
            // 1. Safe localStorage access with error handling
            const getCurrentUserEmail = () => {
              try {
                const userData = localStorage.getItem("user");
                if (!userData) return null;
                const parsed = JSON.parse(userData);
                return parsed?.user?.email ?? null;
              } catch (error) {
                console.error("Error parsing user data:", error);
                return null;
              }
            };

            // 2. Memoize the current user email check
            const currentUserEmail = getCurrentUserEmail();
            const rowEmail = row.getValue("email");
            const isCurrentUser = currentUserEmail === rowEmail;

            // 3. Enhanced visual feedback
            return h(
              "div",
              {
                class: [
                  "text-sm",
                  isCurrentUser
                    ? "font-semibold text-blue-600"
                    : "text-gray-700",
                  "flex items-center gap-2",
                ],
                title: isCurrentUser ? "Your account" : undefined,
                "aria-current": isCurrentUser ? "true" : undefined,
              },
              [
                rowEmail,
                isCurrentUser &&
                  h(
                    "span",
                    {
                      class:
                        "px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800",
                    },
                    "You"
                  ),
              ]
            );
          },
        },

        {
          accessorKey: "role",
          header: "role",
          cell: ({ row }) => {
            const role = row.original.role[0]; // Access the nested client object

            return h(
              Badge,
              { variant: "secondary", class: "uppercase" },
              role ? role.name : ""
            );
          },
        },

        {
          id: "actions",
          accessorKey: "actions",
          enableHiding: false,
          header: () =>
            h("div", { class: "relative text-right font-medium" }, ""),

          cell: ({ row }) => {
            const user = row.original;

            // Get current user email
            const currentUserEmail = localStorage.getItem("user")
              ? JSON.parse(localStorage.getItem("user")).user.email
              : null;

            const currentRowEmail = row.getValue("email");

            // Determine permissions
            const isCurrentUser = currentUserEmail === currentRowEmail;
            this.canEdit = !isCurrentUser; // allow editing only if NOT current user
            this.canDelete = !isCurrentUser; // same for delete

            return h("div", { class: "relative text-right font-medium" }, [
              h(DropdownAction, {
                item: user,
                isEdit: this.canEdit,
                isDelete: this.canDelete,
                isShow: false,
                onEdit: () => this.editUser(user),
                onDelete: () => this.confirmDelete(user),
              }),
            ]);
          },
        },
      ];
    },

    giveRoles() {
      this.canEdit = true;
      this.canCreate = true;
      this.canDelete = true;
      this.canView = true;
    },
    formatCurrency(value) {
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "ETB",
      }).format(value);
    },
    calculateTotal(data) {
      // Flatten the array and reduce to a single sum
      const total = data.flat().reduce((sum, value) => {
        return sum + parseFloat(value || 0);
      }, 0);

      return total;
    },
  },
};
</script>






