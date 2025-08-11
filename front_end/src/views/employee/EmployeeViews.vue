<template>
  <div class="flex flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card">
    <div class="flex items-start justify-between">
      <div class="flex gap-2 flex-col">
        <h4 class="text-lg font-semibold leading-none tracking-tight">
          Employees
        </h4>
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
        :data="employees"
        :isPagination="true"
        :isSearchable="true"
        :is-filter-select="true"
        filter-select-column="type"
        filter-select-label="Type"
        :filter-select-options="[
          { label: 'All', value: '__all' },
          { label: 'STUFF', value: 'STUFF' },
          { label: 'DRIVER', value: 'DRIVER' },
          { label: 'MECHANIC', value: 'MECHANIC' },
        ]"
      />
    </div>
    <ConfirmDelete
      v-model:open="showDeleteDialog"
      :title="deleteTitle"
      description="Are you sure you want to delete this Employee? This action cannot be undone."
      confirm-label="Delete Employee"
      @confirm="handleDelete"
    />
    <Panel
      v-model="showPanel"
      :title="isUpdate ? 'Update Employee Information' : 'Create A Employee'"
      description="Fill the Employee Information"
    >
      <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <div class="flex-1 space-y-4">
          <!-- First Name & Last Name -->
          <div class="pt-2">
            <div class="grid grid-cols-2 gap-4">
              <!-- First Name -->
              <div>
                <FormField name="firstName">
                  <FormItem>
                    <FormLabel>First Name</FormLabel>
                    <FormControl>
                      <Input type="text" v-model="form.first_name" step="any" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
              </div>

              <!-- Last Name -->
              <div>
                <FormField name="lastName">
                  <FormItem>
                    <FormLabel>Last Name</FormLabel>
                    <FormControl>
                      <Input type="text" v-model="form.last_name" step="any" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
              </div>
            </div>
          </div>

          <!-- Email -->
          <FormField name="email">
            <FormItem>
              <FormLabel>Email</FormLabel>
              <FormControl>
                <Input type="email" v-model="form.email" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Phone -->
          <FormField name="phone">
            <FormItem>
              <FormLabel>Phone</FormLabel>
              <FormControl>
                <Input type="text" v-model="form.phone" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <!-- Employee Type -->
          <div class="my-2">
            <label class="block text-sm font-medium my-1"
              >Type of Employee</label
            >
            <Select v-model="form.type">
              <SelectTrigger class="w-full border p-3">
                <SelectValue placeholder="Employee Type" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem value="STUFF">STUFF</SelectItem>
                  <SelectItem value="DRIVER">Driver</SelectItem>
                  <SelectItem value="MECHANIC">Mechanic</SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
          </div>

          <!-- Picture Upload -->
          <div>
            <Label
              class="block text-sm font-medium text-gray-800 mb-1"
              for="picture"
              >File</Label
            >
            <div class="relative flex items-center">
              <Input
                id="picture"
                type="file"
                @change="form.id_file = $event.target.files[0]"
              />
            </div>
          </div>
          <div v-if="error" class="text-red-500 text-sm">
            {{ error }}
          </div>
        </div>

        <!-- Submit Button -->
        <Button
          type="submit"
          class="mt-2 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-md transition-colors duration-200"
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
import { ArrowUpDown, CirclePlus, LoaderCircle } from "lucide-vue-next";
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
  SelectTrigger,
  SelectValue,
  Select,
  SelectContent,
  SelectGroup,
  SelectLabel,
  SelectItem,
} from "@/components/ui";

import { Table, DropdownAction } from "@/components/table";
import { h, ref, shallowRef } from "vue";
import useEmployeeStore from "@/stores/employees";
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
    SelectTrigger,
    LoaderCircle,
    SelectValue,
    Select,
    SelectContent,
    SelectGroup,
    SelectLabel,
    SelectItem,
    CirclePlus,
    ConfirmDelete,
  },
  data() {
    return {
      showPanel: false,
      employees: [],
      columns: [],
      loading: false,
      error: null,

      form: {
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        id_file: "",
        type: "",
      },
      isUpdate: false,
      editId: null,
      // deleted component
      showDeleteDialog: false,
      deleteTitle: "",
      deleteId: null,
    };
  },
  created() {
    this.fetchEmployees();
    this.ColumnDefinitions();
  },
  methods: {
    async fetchEmployees() {
      try {
        const response = await useEmployeeStore.getAll();
        this.employees = response.data || [];
        console.table(this.employees);
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
      this.loading = true;
      this.error = null;
      // If editingEmployee is set, update the employee, else create new

      try {
        const response = await useEmployeeStore.store(this.form);
        await this.fetchEmployees();
        this.$router.push("Employees");

        this.form.name = "";
        this.showPanel = false;
        this.loading = false;
      } catch (error) {
        this.error =
          error.response?.data?.message ||
          error.message ||
          "Failed to save employee information";
        console.error("employee save error:", error);
      } finally {
        this.loading = false;
      }
    },

    editEmployee(employee) {
      this.isUpdate = true;
      this.editId = employee.id;
      this.form = {
        first_name: employee.first_name,
        last_name: employee.last_name,
        email: employee.email,
        phone: employee.phone,
        id_file: employee.id_file,
        type: employee.type,
      };
      this.showPanel = true;
    },

    async handleSubmitUpdate() {
      try {
        this.loading = true;
        const response = await useEmployeeStore.update(this.editId, this.form);

        this.showPanel = false;
        this.fetchEmployees(); // Refresh the list
        this.resetForm();
        this.isUpdate = false;
        this.editId = null;
      } catch (error) {
        this.error = error.message || "Failed to update expense";
      } finally {
        this.loading = false;
      }
    },

    confirmDelete(employee) {
      this.deleteTitle = `Delete ${
        employee.first_name + " " + employee.last_name
      }`;
      this.deleteId = employee.id;
      this.showDeleteDialog = true;
    },
    async handleDelete() {
      try {
        this.loading = true;
        await useEmployeeStore.delete(this.deleteId);

        await this.fetchEmployees(); // Refresh the list
      } catch (error) {
        this.error = error.message || "Failed to delete employee";
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
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        id_file: "",
        type: "",
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
          accessorKey: "first_name", // still needed for sorting/filtering
          header: "Name",
          cell: ({ row }) => {
            const first_name = row.getValue("first_name");
            const last_name = row.original?.last_name; // ✅ access full row data

            return h(
              "div",
              { class: "text-sm" },
              `${first_name} ${last_name || ""}`
            );
          },
        },
        {
          accessorKey: "type",
          header: "type",
          cell: ({ row }) => {
            const type = row.getValue("type"); // Access the nested employee object
            return h("div", { class: "text-sm" }, type);
          },
        },
        {
          accessorKey: "phone",
          header: "phone",
          cell: ({ row }) => {
            const phone = row.getValue("phone"); // Access the nested employee object
            return h("div", { class: "text-sm" }, phone);
          },
        },
        {
          accessorKey: "file",
          header: "file",
          cell: ({ row }) => {
            const file = row.getValue("file"); // Access the nested employee object
            return h("img", {
              class: "text-sm h-7 ",
              src: import.meta.env.VITE_IMAGE_STORAGE_URL + file,
            });
          },
        },

        {
          id: "actions",
          accessorKey: "actions",
          enableHiding: false,

          header: () =>
            h("div", { class: "relative text-right font-medium " }, ""),

          cell: ({ row }) => {
            const employee = row.original;

            return h("div", { class: "relative text-right font-medium " }, [
              h(DropdownAction, {
                item: employee,
                isEdit: true,
                isDelete: true,
                isShow: false,
                onEdit: () => this.editEmployee(employee), // match method from parent
                onDelete: () => this.confirmDelete(employee), // match method from parent
              }),
            ]);
          },
        },
      ];
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






