<template>
  <div class="flex flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card">
    <!-- Table section remains unchanged -->
    <div class="flex items-start justify-between">
      <div class="flex gap-2 flex-col">
        <h4 class="text-lg font-semibold leading-none tracking-tight">
          Expenses
        </h4>
        <p class="text-sm text-muted-foreground"></p>
      </div>
      <div class="flex items-center gap-2">
        <Button @click="togglePanel()" variant="ghost"
          class="text-sm text-muted-foreground flex gap-1 items-center justify-center cursor-pointer">
          <CirclePlus />
          Create New
        </Button>
      </div>
    </div>

    <div class="rounded-lg overflow-hidden">
      <Table :columns="columns" :data="expenses" :isPagination="true" :isSearchable="true" :is-filter-select="true"
        filter-select-column="expense_type" filter-select-label="Type" :filter-select-options="[
          { label: 'All', value: '__all' },
          { label: 'Employee', value: 'employee' },
          { label: 'Vehicle', value: 'vehicle' },
          { label: 'General', value: 'general' },
        ]" />
    </div>
    <ConfirmDelete v-model:open="showDeleteDialog" :title="deleteTitle"
      description="Are you sure you want to delete this bank? This action cannot be undone." confirm-label="Delete Bank"
      @confirm="handleDelete" />
    <Panel v-model="showPanel" title="Create A Expense" description="Fill the Expense Information">
      <form @submit.prevent="handleSubmit" enctype="multipart/form-data" class="space-y-6">
        <div class="space-y-6 max-w-3xl mx-auto">
          <!-- Main Category Selector -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Expense Category</label>
            <div class="grid grid-cols-3 gap-3">
              <label v-for="option in categoryOptions" :key="option.value" :class="{
                'border-blue-500 bg-blue-50 shadow-inner':
                  form.expense_category === option.value,
                'border-gray-200': form.expense_category !== option.value,
              }"
                class="cursor-pointer rounded-lg border p-4 transition-all duration-200 hover:border-blue-400 hover:shadow-sm">
                <input type="radio" v-model="form.expense_category" :value="option.value" class="hidden"
                  @change="resetSubCategories" />
                <div class="flex flex-col items-center gap-2">
                  <span :class="{
                    'text-blue-600': form.expense_category === option.value,
                    'text-gray-600': form.expense_category !== option.value,
                  }" class="text-2xl">
                    <component :is="option.iconComponent" />
                  </span>
                  <span :class="{
                    'text-blue-700': form.expense_category === option.value,
                    'text-gray-700': form.expense_category !== option.value,
                  }" class="text-sm font-medium">
                    {{ option.label }}
                  </span>
                </div>
              </label>
            </div>
          </div>

          <!-- Dynamic Subcategory Sections with Animation -->
          <transition enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-1" enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95" mode="out-in">
            <!-- General Expenses -->
            <div v-if="form.expense_category === 'General'" key="general"
              class="p-4 rounded-xl bg-gray-50 border border-gray-100">
              <label class="block text-sm font-medium text-gray-700 mb-2">General Expense Type</label>
              <div class="relative">
                <Select v-model="form.General_category">
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="General Expense Type" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem v-for="cat in CategoryGeneral" :key="cat.id" :value="cat.id">
                        {{ cat.name }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>

                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                  <ChevronDown class="h-4 w-4" />
                </div>
              </div>
            </div>

            <!-- Vehicle Expenses -->
            <div v-else-if="form.expense_category === 'Vehicle'" key="vehicle"
              class="p-4 rounded-xl bg-gray-50 border border-gray-100">
              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Select Vehicle</label>

                  <div class="relative">
                    <Select v-model="form.vehicle_id">
                      <SelectTrigger class="w-full">
                        <SelectValue placeholder="Choose vehicle" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectItem v-for="cat in AllVehicles" :key="cat.id" :value="cat.id">
                            {{ cat.plate_number }}
                          </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>

                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                      <ChevronDown class="h-4 w-4" />
                    </div>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Expense Type</label>
                  <div class="relative">
                    <Select v-model="form.vehicle_category">
                      <SelectTrigger class="w-full">
                        <SelectValue placeholder="Choose Vehicle Expense Type" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectItem v-for="cat in CategoryVehicle" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                          </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>

                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                      <ChevronDown class="h-4 w-4" />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Employee Expenses -->
            <div v-else-if="form.expense_category === 'Employee'" key="employee"
              class="p-4 rounded-xl bg-gray-50 border border-gray-100">
              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Employee
                  </label>
                  <div class="relative">
                    <Select v-model="form.employee_id">
                      <SelectTrigger class="w-full">
                        <SelectValue placeholder="Choose employee" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectItem v-for="cat in AllEmployees" :key="cat.id" :value="cat.id">
                            {{ cat.first_name }}
                            {{ cat.last_name }}
                          </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>

                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                      <ChevronDown class="h-4 w-4" />
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Employee Expense Type</label>
                  <div class="relative">
                    <Select v-model="form.employees_category">
                      <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select expense type" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectItem v-for="cat in CategoryEmployee" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                          </SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>

                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                      <ChevronDown class="h-4 w-4" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </transition>
        </div>

        <div class="space-y-6 max-w-3xl mx-auto">
          <!-- Bank Selection Section -->
          <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">From Bank
              </label>
              <div class="relative">
                <select v-model="form.selectedBank" @change="filterBankAccounts"
                  class="w-full appearance-none rounded-lg border border-gray-300 px-4 py-3 pr-10 text-gray-900 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                  <option value="">Select bank</option>
                  <option v-for="bank in AllBanks" :key="bank.id" :value="bank.id">
                    {{ bank.name }}
                  </option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                  <ChevronDown class="h-4 w-4" />
                </div>
              </div>
            </div>

            <!-- Bank Account Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">From Account
              </label>
              <div class="relative">
                <select v-model="form.selectedAccount"
                  class="w-full appearance-none rounded-lg border border-gray-300 px-4 py-3 pr-10 text-gray-900 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                  :disabled="!filteredAccounts.length">
                  <option value="" disabled>Select account</option>
                  <option v-for="account in filteredAccounts" :key="account.id" :value="account.id">
                    {{ account.account_number }}
                    -
                    {{ account.account_name }}
                    <template v-if="!form.selectedBank">
                      ({{ getBankName(account.bank_id) }})
                    </template>
                  </option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                  <ChevronDown class="h-4 w-4" />
                </div>
              </div>
              <p v-if="!filteredAccounts.length" class="mt-1 text-sm text-gray-500">
                No accounts available for selected bank
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">To Bank
              </label>
              <div class="relative">
                <select v-model="form.toBank"
                  class="w-full appearance-none rounded-lg border border-gray-300 px-4 py-3 pr-10 text-gray-900 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                  <option value="" disabled>Select bank</option>
                  <option v-for="bank in AllBanks" :key="bank.id" :value="bank.id">
                    {{ bank.name }}
                  </option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                  <ChevronDown class="h-4 w-4" />
                </div>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">To Account
              </label>
              <input v-model="form.toAccount" id="owner_name" type="text"
                class="w-full rounded-xl border border-gray-300/80 px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/90 focus:border-blue-500/50 transition-all duration-200 bg-white/95 shadow-sm"
                placeholder="Enter account number" />
            </div>
          </div>
        </div>

        <div class="space-y-6 max-w-3xl mx-auto">
          <div>
            <label for="amount" class="block text-sm font-medium text-gray-800 mb-1">
              Amount
            </label>
            <div class="relative">
              <input v-model="form.amount" id="amount" type="number" step="0.01" min="0"
                class="w-full rounded-xl border border-gray-300/80 px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/90 focus:border-blue-500/50 transition-all duration-200 bg-white/95 shadow-sm"
                placeholder="Enter amount" />
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                <span class="text-sm">ETB</span>
              </div>
            </div>
          </div>

          <div>
            <label for="date" class="block text-sm font-medium text-gray-800 mb-1">
              Date
            </label>
            <input v-model="form.paid_date" id="date" type="date"
              class="w-full rounded-xl border border-gray-300/80 px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/90 focus:border-blue-500/50 transition-all duration-200 bg-white/95 shadow-sm" />
          </div>

          <div>
            <label for="remark" class="block text-sm font-medium text-gray-800 mb-1">
              Remark
            </label>
            <textarea v-model="form.remark" id="remark" rows="3"
              class="w-full rounded-xl border border-gray-300/80 px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/90 focus:border-blue-500/50 transition-all duration-200 bg-white/95 shadow-sm"
              placeholder="Additional notes"></textarea>
          </div>

          <div class="relative flex items-center">
            <input id="file_input" type="file" class="sr-only" @change="form.file = $event.target.files[0]" />
            <label for="file_input"
              class="flex items-center justify-center w-full px-4 py-2 bg-white border border-input rounded-md shadow-sm cursor-pointer hover:bg-accent hover:text-accent-foreground transition-colors text-foreground">
              <Upload class="mr-2 h-4 w-4" />
              <span>
                {{
                  form.file && form.file.name
                    ? form.file.name
                    : "Choose file..."
                }}
              </span>
            </label>
          </div>
        </div>

        <div v-if="error" class="text-red-500 text-sm text-center">
          {{ error }}
        </div>

        <div class="flex gap-3 max-w-3xl mx-auto pt-4">
          <Button :disabled="loading" type="submit" class="w-full py-3.5 ">
            <span v-if="!loading">Submit</span>
            <span v-else class="flex items-center gap-2">
              <Loader2 class="h-4 w-4 animate-spin" />
              Processing...
            </span>
          </Button>
        </div>
      </form>
    </Panel>
  </div>
</template>

<script>
import {
  CirclePlus,
  ChevronDown,
  Upload,
  X,
  Loader2,
  Receipt,
  CarFront,
  ContactRound,
} from "lucide-vue-next";
import { Panel } from "@/components/panels";
import {
  Button,
  SelectTrigger,
  SelectValue,
  Select,
  SelectContent,
  SelectGroup,
  SelectLabel,
  SelectItem,
} from "@/components/ui";
import { DropdownAction, Table } from "@/components/table";
import { h, ref } from "vue";
import useExpenseStore from "@/stores/expenses";
import Checkbox from "@/components/ui/checkbox/Checkbox.vue";
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";

export default {
  components: {
    Panel,
    Table,
    CirclePlus,
    Button,
    ChevronDown,
    Upload,
    X,
    Loader2,
    Receipt,
    CarFront,
    ContactRound,
    SelectTrigger,
    SelectValue,
    Select,
    SelectContent,
    SelectGroup,
    SelectLabel,
    SelectItem,
    DropdownAction,
    ConfirmDelete,
  },
  data() {
    return {
      showPanel: false,
      expenses: [],
      columns: [],
      loading: false,
      error: null,
      filteredAccounts: [],

      form: {
        expense_category: "",
        General_category: "",
        vehicle_id: "",
        vehicle_category: "",
        employee_id: "",
        employees_category: "",
        selectedBank: "",
        selectedAccount: "",
        toAccount: "",
        toBank: "",
        amount: "",
        paid_date: "",
        remark: "",
        file: "",
      },

      categoryOptions: [
        {
          value: "General",
          label: "General",
          iconComponent: "Receipt",
        },
        {
          value: "Vehicle",
          label: "Vehicle",
          iconComponent: "CarFront",
        },
        {
          value: "Employee",
          label: "Employee",
          iconComponent: "ContactRound",
        },
      ],

      CategoryGeneral: [],
      CategoryEmployee: [],
      CategoryVehicle: [],
      AllEmployees: [],
      AllVehicles: [],
      AllBanks: [],
      AllBankAccounts: [],
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
    this.fetchExpenses();
    this.ColumnDefinitions();
  },

  methods: {
    async fetchExpenses() {
      try {
        const response = await useExpenseStore.getAll();
        this.expenses = response.data || [];
        this.CategoryEmployee = response.CategoryEmployee;
        this.CategoryGeneral = response.CategoryGeneral;
        this.CategoryVehicle = response.CategoryVehicle;
        this.AllEmployees = response.AllEmployees;
        this.AllVehicles = response.AllVehicles;
        this.AllBanks = response.AllBanks;
        this.AllBankAccounts = response.AllBanksAccount;
      } catch (error) {
        console.error("Failed to fetch expenses:", error);
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
      // If editingVehicle is set, update the expense, else create new

      try {
        const response = await useExpenseStore.store(this.form);
        await this.fetchExpenses();
        this.error = null;

        this.showPanel = false;
      } catch (error) {
        this.error =
          error.response?.data?.message ||
          error.message ||
          "Failed to save expense information";
        console.error("expense save error:", error);
      } finally {
        this.loading = false;
      }
    },
    editExpense(expense) {
      this.isUpdate = true;
      this.editId = expense.id;
      const { category, id: expenseTypeId } = expense.expense_type;

      // Validate required fields
      if (!expenseTypeId) {
        throw new Error("Expense type ID is required");
      }

      this.form = {
        expense_category: category,
        expense_type_id: expenseTypeId, // Add this line

        // Category-specific fields
        General_category: category === "General" ? expenseTypeId : null,
        vehicles_id: expense.vehicle_id,
        vehicle_category: category === "Vehicle" ? expenseTypeId : null,
        employees_id: expense.employee_id,
        employees_category: category === "Employee" ? expenseTypeId : null,

        // Bank fields
        selectedBank: expense.from_bank?.id || "",
        selectedAccount: expense.from_account?.id || "",
        toAccount: expense.to_account || "",
        toBank: expense.to_bank?.id || "",

        // Common fields
        amount: expense.amount,
        paid_date: expense.paid_date?.split("T")[0] || "",
        remark: expense.remark || "",
        file: expense.file || null,
      };

      this.showPanel = true;
    },

    async handleSubmitUpdate() {
      try {
        this.loading = true;
        const response = await useExpenseStore.update(this.editId, this.form);

        this.showPanel = false;
        this.fetchExpenses(); // Refresh the list
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
    confirmDelete(expense) {
      this.deleteTitle = `Delete ${expense.name}`;
      this.deleteId = expense.id;
      this.showDeleteDialog = true;
    },
    async handleDelete() {
      try {
        this.loading = true;
        await useExpenseStore.delete(this.deleteId);

        await this.fetchExpenses(); // Refresh the list
      } catch (error) {
        this.error = error.message || "Failed to delete expense";
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
          accessorKey: "expense_type",
          header: "Type",
          cell: ({ row }) => {
            const expense = row.original.expense_type; // Access the nested vehicle object
            return h(
              "div",
              { class: "text-sm" },
              expense ? expense.category : ""
            );
          },
        },

        {
          accessorKey: "expense_name",
          header: "Name",
          cell: ({ row }) => {
            const expense = row.original.expense_type; // Access the nested vehicle object
            return h("div", { class: "text-sm" }, expense ? expense.name : "");
          },
        },
        {
          accessorKey: "amount",
          header: () => h("div", "Amount"),
          cell: ({ row }) => {
            const amount = parseFloat(row.getValue("amount"));
            const formatted = new Intl.NumberFormat("en-US", {
              style: "currency",
              currency: "ETB",
            }).format(amount);

            return h("div", { class: "text-right " }, formatted);
          },
        },

        {
          accessorKey: "paid_date",
          header: "Date",
          cell: ({ row }) => {
            const paid_date = row.getValue("paid_date"); //
            //
            const formattedDate = new Date(paid_date).toLocaleDateString(
              "en-US",
              {
                year: "numeric",
                month: "short",
                day: "numeric",
              }
            );
            //  Access the nested vehicle object
            return h("div", { class: "text-sm" }, formattedDate);
          },
        },

        {
          accessorKey: "from",
          header: "From",
          cell: ({ row }) => {
            const expense_bank = row.original.from_bank; // Access the nested client object
            const expense_account = row.original.from_account; // Access the nested client object
            return h(
              "div",
              { class: "text-sm" },
              expense_bank
                ? expense_bank.name + " | " + expense_account.account_number
                : ""
            );
          },
        },

        {
          accessorKey: "to_account",
          header: "To",
          cell: ({ row }) => {
            const expense_bank = row.original.to_bank; // Access the nested client object
            const paid_date = row.getValue("to_account"); //

            return h(
              "div",
              { class: "text-sm" },
              expense_bank ? expense_bank.name + " | " + paid_date : ""
            );
          },
        },
        {
          accessorKey: "file",
          header: "file",
          cell: ({ row }) => {
            const file = row.getValue("file"); //

            return h("img", { class: "text-sm", src: file });
          },
        },
        {
          accessorKey: "remark",
          header: "remark",
          cell: ({ row }) => {
            const remark = row.getValue("remark"); //

            return h("p", { class: "text-sm" }, remark);
          },
        },

        {
          id: "actions",
          accessorKey: "actions",
          enableHiding: false,
          header: () =>
            h("div", { class: "relative text-right font-medium " }, ""),

          cell: ({ row }) => {
            const expense = row.original;

            return h("div", { class: "relative text-right font-medium " }, [
              h(DropdownAction, {
                item: expense,
                isEdit: true,
                isDelete: true,
                isShow: false,
                onEdit: () => this.editExpense(expense),
                onDelete: () => this.confirmDelete(expense),
              }),
            ]);
          },
        },
      ];
    },

    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.form.file = file;
      }
    },

    removeFile() {
      this.form.file = null;
      // Reset the file input
      const fileInput = document.getElementById("file_input");
      if (fileInput) {
        fileInput.value = "";
      }
    },

    resetSubCategories() {
      this.form.General_category = "";
      this.form.vehicle_id = "";
      this.form.vehicle_category = "";
      this.form.employee_id = "";
      this.form.employees_category = "";
    },

    filterBankAccounts() {
      if (!this.form.selectedBank) {
        this.filteredAccounts = [...this.AllBankAccounts];
      } else {
        this.filteredAccounts = this.AllBankAccounts.filter(
          (account) => account.bank_id === this.form.selectedBank
        );
      }
      this.form.selectedAccount = "";
    },

    getBankName(bankId) {
      const bank = this.AllBanks.find((b) => b.id === bankId);
      return bank ? bank.name : "Unknown Bank";
    },

    resetForm() {
      this.form = {
        expense_category: "",
        General_category: "",
        vehicle_id: "",
        vehicle_category: "",
        employee_id: "",
        employees_category: "",
        selectedBank: "",
        selectedAccount: "",
        toAccount: "",
        toBank: "",
        amount: "",
        paid_date: "",
        remark: "",
        file: null,
      };
      this.filteredAccounts = [...this.AllBankAccounts];
    },
  },

  async mounted() {
    // Initialize with empty arrays if no data loaded
    this.AllBanks = this.AllBanks || [];
    this.AllBankAccounts = this.AllBankAccounts || [];
    this.filteredAccounts = [...this.AllBankAccounts];
  },
};
</script>

<style scoped>
/* Your existing styles */
</style>