<template>
  <div>
    <Card>
      <CardHeader class="px-2">
        <CardTitle class="flex justify-between"
          >Expense
          <Button
            @click="togglePanel"
            variant="ghost"
            class="text-sm text-muted-foreground flex gap-1 items-center justify-center cursor-pointer"
          >
            <CirclePlus />
            Create New
          </Button>
        </CardTitle>
        <CardDescription> </CardDescription>
      </CardHeader>
      <CardContent>
        <ScrollArea class="w-[80vw] sm:w-auto overflow-x-auto">
          <Table
            :columns="ExpenseColumns"
            :data="expenseData"
            :isPagination="true"
            :isSearchable="false"
            :is-filter-select="false"
            filter-select-column="expense_type"
            filter-select-label="Type"
            :filter-select-options="[
              { label: 'All', value: '__all' },
              { label: 'Employee', value: 'employee' },
            ]"
          />
          <ScrollBar orientation="horizontal" />
        </ScrollArea>
      </CardContent>
    </Card>
    <ConfirmDelete
      v-model:open="showDeleteDialog"
      :title="deleteTitle"
      description="Are you sure you want to delete this expense? This action cannot be undone."
      confirm-label="Delete Expense"
      @confirm="handleDelete"
    />
    <Panel
      v-model="showPanel"
      title="Create A Expense"
      description="Fill the Expense Information"
    >
      <form
        @submit.prevent="handleSubmit"
        enctype="multipart/form-data"
        class="space-y-6"
      >
        <div class="space-y-6 max-w-3xl mx-auto">
          <div>
            <label for="order" class="block text-sm font-medium mb-1">
              Order
            </label>
            <div class="relative">
              <input
                id="order"
                type="text"
                step="0.01"
                min="0"
                disabled
                class="w-full rounded-sm border border-gray-300/80 px-4 py-2 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/90 focus:border-blue-500/50 transition-all duration-200 bg-white/95 shadow-sm"
                :placeholder="'ORDER #' + orderIds"
              />
              <input type="hidden" v-model="form.order_id" />
            </div>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Expense Type
          </label>
          <div class="relative">
            <select
              v-model="form.expense_types"
              class="w-full appearance-none rounded-lg border border-gray-300 px-4 py-2 pr-10 text-gray-900 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
            >
              <option value="" disabled>Select expense</option>
              <option
                v-for="expenseType in AllExpenseTypes"
                :key="expenseType.id"
                :value="expenseType.id"
              >
                {{ expenseType.name }}
              </option>
            </select>
            <div
              class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400"
            >
              <ChevronDown class="h-4 w-4" />
            </div>
          </div>
        </div>

        <div class="max-w-3xl mx-auto">
          <!-- Bank Selection Section -->
          <div class="rounded-sm space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2"
                >From Bank
              </label>
              <div class="relative">
                <select
                  v-model="form.selectedBank"
                  @change="filterBankAccounts"
                  class="w-full appearance-none rounded-lg border border-gray-300 px-4 py-2 pr-10 text-gray-900 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                >
                  <option value="">Select bank</option>
                  <option
                    v-for="bank in AllBanks"
                    :key="bank.id"
                    :value="bank.id"
                  >
                    {{ bank.name }}
                  </option>
                </select>
                <div
                  class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400"
                >
                  <ChevronDown class="h-4 w-4" />
                </div>
              </div>
            </div>

            <!-- Bank Account Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2"
                >From Account
              </label>
              <div class="relative">
                <select
                  v-model="form.selectedAccount"
                  class="w-full appearance-none rounded-lg border border-gray-300 px-4 py-2 pr-10 text-gray-900 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                  :disabled="!filteredAccounts.length"
                >
                  <option value="" disabled>Select account</option>
                  <option
                    v-for="account in filteredAccounts"
                    :key="account.id"
                    :value="account.id"
                  >
                    {{ account.account_number }}
                    -
                    {{ account.account_name }}
                    <template v-if="!form.selectedBank">
                      ({{ getBankName(account.bank_id) }})
                    </template>
                  </option>
                </select>
                <div
                  class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400"
                >
                  <ChevronDown class="h-4 w-4" />
                </div>
              </div>
              <p
                v-if="!filteredAccounts.length"
                class="mt-1 text-sm text-gray-500"
              >
                No accounts available for selected bank
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2"
                >To Bank
              </label>
              <div class="relative">
                <select
                  v-model="form.toBank"
                  class="w-full appearance-none rounded-lg border border-gray-300 px-4 py-2 pr-10 text-gray-900 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                >
                  <option value="" disabled>Select bank</option>
                  <option
                    v-for="bank in AllBanks"
                    :key="bank.id"
                    :value="bank.id"
                  >
                    {{ bank.name }}
                  </option>
                </select>
                <div
                  class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400"
                >
                  <ChevronDown class="h-4 w-4" />
                </div>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2"
                >To Account
              </label>
              <input
                v-model="form.toAccount"
                id="owner_name"
                type="text"
                class="w-full rounded-sm border border-gray-300/80 px-4 py-2 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/90 focus:border-blue-500/50 transition-all duration-200 bg-white/95 shadow-sm"
                placeholder="Enter account number"
              />
            </div>
          </div>
        </div>

        <div class="space-y-6 max-w-3xl mx-auto">
          <div>
            <label for="amount" class="block text-sm font-medium mb-1">
              Amount
            </label>
            <div class="relative">
              <input
                v-model="form.amount"
                id="amount"
                type="number"
                step="0.01"
                min="0"
                class="w-full rounded-sm border border-gray-300/80 px-4 py-2 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/90 focus:border-blue-500/50 transition-all duration-200 bg-white/95 shadow-sm"
                placeholder="Enter amount"
              />
              <div
                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400"
              >
                <span class="text-sm">ETB</span>
              </div>
            </div>
          </div>

          <div>
            <label
              for="date"
              class="block text-sm font-medium text-gray-800 mb-1"
            >
              Date
            </label>
            <input
              v-model="form.paid_date"
              id="date"
              type="date"
              class="w-full rounded-sm border border-gray-300/80 px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/90 focus:border-blue-500/50 transition-all duration-200 bg-white/95 shadow-sm"
            />
          </div>

          <div>
            <label
              for="remark"
              class="block text-sm font-medium text-gray-800 mb-1"
            >
              Remark
            </label>
            <textarea
              v-model="form.remark"
              id="remark"
              rows="3"
              class="w-full rounded-sm border border-gray-300/80 px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/90 focus:border-blue-500/50 transition-all duration-200 bg-white/95 shadow-sm"
              placeholder="Additional notes"
            ></textarea>
          </div>

          <div class="relative flex items-center">
            <input
              id="file_input"
              type="file"
              class="sr-only"
              @change="form.file = $event.target.files[0]"
            />
            <label
              for="file_input"
              class="flex items-center justify-center w-full px-4 py-2 bg-white border border-input rounded-md shadow-sm cursor-pointer hover:bg-accent hover:text-accent-foreground transition-colors text-foreground"
            >
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
          <Button
            :disabled="loading"
            type="submit"
            class="w-full py-3.5 rounded-sm"
          >
            <span v-if="!loading">Save Expense</span>
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
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";
import Panel from "@/components/panels/Panel.vue";
import { DropdownAction } from "@/components/table";
import Table from "@/components/table/Table.vue";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
  ScrollArea,
  ScrollBar,
} from "@/components/ui";
import Button from "@/components/ui/button/Button.vue";
import Checkbox from "@/components/ui/checkbox/Checkbox.vue";
import FormControl from "@/components/ui/form/FormControl.vue";
import FormItem from "@/components/ui/form/FormItem.vue";
import FormLabel from "@/components/ui/form/FormLabel.vue";
import Input from "@/components/ui/input/Input.vue";
import Select from "@/components/ui/select/Select.vue";
import SelectItem from "@/components/ui/select/SelectItem.vue";
import SelectTrigger from "@/components/ui/select/SelectTrigger.vue";
import SelectValue from "@/components/ui/select/SelectValue.vue";
import router from "@/router";
import useExpenseStore from "@/stores/expenses";
import useExpenseTypeStore from "@/stores/expenseTypes";
import {
  ChevronDown,
  CirclePlus,
  ExternalLink,
  Loader2,
  Upload,
} from "lucide-vue-next";
import { h } from "vue";

export default {
  components: {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    ScrollArea,
    ScrollBar,
    Panel,
    Button,
    DropdownAction,
    CirclePlus,
    ConfirmDelete,
    Input,
    FormControl,
    FormItem,
    FormLabel,
    Select,
    SelectItem,
    SelectTrigger,
    SelectValue,
    ChevronDown,
    Checkbox,
    Loader2,
    Upload,
  },

  props: {
    expenseData: {
      type: Object,
      required: false,
    },
  },
  emits: ["data-changed"],
  data() {
    return {
      showPanel: false,
      loading: false,
      error: null,
      filteredAccounts: [],
      AllExpenseTypes: [],
      ExpenseColumns: [],

      form: {
        expense_types: "",
        order_id: "",
        selectedBank: "",
        selectedAccount: "",
        toAccount: "",
        toBank: "",
        amount: "",
        paid_date: "",
        remark: "",
        file: "",
      },

      // updated component
      isUpdate: false,
      editId: null,
      orderIds: null,
      // deleted component
      showDeleteDialog: false,
      deleteTitle: "",
      deleteId: null,
    };
  },
  created() {
    this.fetchExpenseType();
    this.ColumnDefinitions();
  },
  methods: {
    async fetchExpenseType() {
      const expenseType = await useExpenseTypeStore.getAll();
      const response = await useExpenseStore.getAll();
      this.expenses = response.data || [];

      this.AllBanks = response.AllBanks;
      this.AllBankAccounts = response.AllBanksAccount;
      this.AllExpenseTypes = expenseType.data;
      const orderId = this.$route.params.id;
      this.orderIds = orderId;
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
      const orderId = this.$route.params.id;

      this.form.order_id = orderId;
      try {
        const response = await useExpenseStore.store(this.form);
        this.$emit("data-changed");

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
      const { category } = expense.expense_type;
      this.form = {
        expense_types: expense.expense_type.id,
        order_id: expense.order_id,
        selectedBank: expense.from_bank.id,
        selectedAccount: expense.from_account.id,
        toAccount: expense.to_account,
        toBank: expense.to_bank.id,
        amount: expense.amount,
        paid_date: expense.paid_date,
        remark: expense.remark,
        file: expense.file,
      };
      this.showPanel = true;
    },

    async handleSubmitUpdate() {
      try {
        this.loading = true;
        const response = await useExpenseStore.update(this.editId, this.form);

        this.showPanel = false;
        this.$emit("data-changed");
        this.resetForm();
        this.isUpdate = false;
        this.editId = null;
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
        const orderId = this.$route.params.id;

        this.loading = true;
        await useExpenseStore.delete(this.deleteId);
        this.$emit("data-changed");
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
      this.ExpenseColumns = [
        {
          id: "id",
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
          accessorKey: "type",
          header: "Type",
          cell: ({ row }) => {
            const expenseType = row.original.expense_type; // Access the nested vehicle object
            return h(
              "div",
              { class: "text-sm" },
              expenseType ? expenseType.category + " | " + expenseType.name : ""
            );
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

            return h("div", { class: " " }, formatted);
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
    formatDate(dateString) {
      if (!dateString) return "N/A";
      const options = { year: "numeric", month: "short", day: "numeric" };
      return new Date(dateString).toLocaleDateString("en-US", options);
    },
    formatCurrency(value) {
      if (!value) return "N/A";
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "ETB",
      }).format(value);
    },
  },
};
</script>