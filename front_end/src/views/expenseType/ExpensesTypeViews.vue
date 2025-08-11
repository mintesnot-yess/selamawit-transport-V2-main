<template>
  <div class="flex flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card">
    <div class="flex items-start justify-between">
      <div class="flex gap-2 flex-col">
        <h4 class="text-lg font-semibold leading-none tracking-tight">
          ExpenseTypes
        </h4>
        <p class="text-sm text-muted-foreground"></p>
      </div>
      <div class="flex items-center gap-2">
        <Button
          @click="togglePanel"
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
        :data="expenseTypes"
        :isPagination="true"
        :isSearchable="true"
        :is-filter-select="false"
        filter-select-column="name"
        filter-select-label="Status"
        :filter-select-options="[{ label: 'All', value: '__all' }]"
      />
    </div>
    <ConfirmDelete
      v-model:open="showDeleteDialog"
      :title="deleteTitle"
      description="Are you sure you want to delete this expense type? This action cannot be undone."
      confirm-label="Delete Expense Type"
      @confirm="handleDelete"
    />
    <Panel
      v-model="showPanel"
      :title="
        isUpdate ? 'Update Expense Type Information' : 'Create A Expense Type'
      "
      :description="isUpdate ? ' ' : 'Fill the ExpenseType Information'"
    >
      <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <div class="flex-1 space-y-2">
          <FormField name="name">
            <FormItem>
              <FormLabel>Name</FormLabel>
              <FormControl>
                <Input type="text" v-model="form.name" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <FormField name="category">
            <FormItem>
              <FormLabel>Owner Type</FormLabel>
              <Select v-model="form.category">
                <FormControl>
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select type" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent>
                  <SelectItem value="OWNED">OWNED</SelectItem>
                  <SelectItem value="PRIVATE">PRIVATE</SelectItem>
                  <SelectItem value="GENERAL">GENERAL</SelectItem>
                  <SelectItem value="VEHICLE">VEHICLE</SelectItem>
                  <SelectItem value="EMPLOYEE">EMPLOYEE</SelectItem>
                </SelectContent>
              </Select>

              <FormMessage />
            </FormItem>
          </FormField>
          <div if="error" class="text-red-500 text-sm">
            {{ error }}
          </div>
        </div>
        <Button type="submit">
          <span v-if="loading">
            <LoaderCircle
              class="fa-solid size-6 fa-circle-notch animate-spin"
            />
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
} from "@/components/ui";

import { Table, DropdownAction } from "@/components/table";
import { h, ref, shallowRef } from "vue";
import useExpenseTypeStore from "@/stores/expenseTypes";
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
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
    Loader2,
    LoaderCircle,
    ConfirmDelete,
    CirclePlus,
  },
  data() {
    return {
      showPanel: false,
      expenseTypes: [],
      columns: [],
      error: "",
      loading: false,
      form: {
        name: "",
        category: "",
      },
      showPanel: false,

      // updated component
      isUpdate: false,
      editId: null,
      // deleted component
      showDeleteDialog: false,
      deleteTitle: "",
      deleteId: null,
    };
  },
  created() {
    this.fetchExpenseTypes();
    this.ColumnDefinitions();
  },
  methods: {
    async fetchExpenseTypes() {
      try {
        const response = await useExpenseTypeStore.getAll();
        this.expenseTypes = response.data || [];
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
      // If editingExpenseType is set, update the expense_type, else create new

      try {
        const response = await useExpenseTypeStore.store(this.form);
        await this.fetchExpenseTypes();

        this.showPanel = false;
        this.loading = false;
      } catch (error) {
        this.error =
          error.response?.data?.message ||
          error.message ||
          "Failed to save expense_type information";
        console.error("ExpenseType save error:", error);
      } finally {
        this.loading = false;
      }
    },
    editExpenseType(expenseType) {
      this.isUpdate = true;
      this.editId = expenseType.id;

      this.form = {
        name: expenseType.name,
        category: expenseType.category,
      };

      this.showPanel = true;
    },

    async handleSubmitUpdate() {
      try {
        this.loading = true;
        const response = await useExpenseTypeStore.update(
          this.editId,
          this.form
        );

        this.showPanel = false;
        this.fetchExpenseTypes(); // Refresh the list
        this.resetForm();
        this.isUpdate = false;
        this.editId = null;
        this.error = null;
      } catch (error) {
        this.error = error.message || "Failed to update expense";
      } finally {
        this.loading = false;
      }
    },

    confirmDelete(expenseType) {
      this.deleteTitle = `Delete ${expenseType.name}`;
      this.deleteId = expenseType.id;
      this.showDeleteDialog = true;
    },
    async handleDelete() {
      try {
        this.loading = true;
        await useExpenseTypeStore.delete(this.deleteId);

        await this.fetchExpenseTypes(); // Refresh the list
      } catch (error) {
        this.error = error.message || "Failed to delete bank";
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
          accessorKey: "id",
          header: "",
          cell: ({ row }) => {
            const name = row.original.id; // Access the nested vehicle object
            return h("div", { class: "text-sm" }, name);
          },
        },
        {
          accessorKey: "name",
          header: "Name",
          cell: ({ row }) => {
            const name = row.original.name; // Access the nested vehicle object
            return h("div", { class: "text-sm" }, name);
          },
        },
        {
          accessorKey: "category",
          header: "category",
          cell: ({ row }) => {
            const category = row.getValue("category"); // Access the nested vehicle object
            return h("div", { class: "text-sm" }, category);
          },
        },

        {
          id: "actions",
          accessorKey: "actions",
          enableHiding: false,
          header: () =>
            h("div", { class: "relative text-right font-medium " }, ""),

          cell: ({ row }) => {
            const expenseType = row.original;

            return h(
              "div",
              { class: "relative text-right font-medium " },
              [
                h(DropdownAction, {
                  item: expenseType,
                  isEdit: true,
                  isDelete: true,
                  isShow: false,
                  onEdit: () => this.editExpenseType(expenseType), // match method from parent
                  onDelete: () => this.confirmDelete(expenseType), // match method from parent
                }),
              ],
              null
            );
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






