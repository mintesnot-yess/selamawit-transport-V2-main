<template>
  <div class="flex flex-col space-y-3">
    <GoBack />
    <div class="flex items-start justify-between px-1">
      <div>
        <div class="flex items-center space-x-5 font-normal text-2xl">
          <h4>Order Details</h4>
        </div>
        <p class="text-sm text-muted-foreground">
          Manage access order information.
        </p>
      </div>
    </div>

    <main
      class="grid items-start flex-1 gap-4 md:gap-8 lg:grid-cols-3 xl:grid-cols-3"
    >
      <OrderCard :order="orders" @delete="handleDeleteOrder" />
      <div
        class="grid items-start gap-1 auto-rows-max md:gap-4 lg:col-span-2 overflow-x-auto"
      >
        <Tabs default-value="expense">
          <div class="flex items-center">
            <TabsList>
              <TabsTrigger value="expense">Expense </TabsTrigger>
              <TabsTrigger value="payment_collected">
                Payment Collected
              </TabsTrigger>
            </TabsList>
          </div>

          <TabsContent value="expense">
            <Expense :expenseData="expenses" @data-changed="fetchOrder" />
          </TabsContent>
          <TabsContent value="payment_collected">
            <PaymentCollected
              :expenseData="incomes"
              @data-changed="fetchOrder"
            />
          </TabsContent>
        </Tabs>
      </div>
    </main>
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
} from "@/components/ui";

import { GoBack } from "@/components/navigation";

import { Table, DropdownAction } from "@/components/table";
import { h, ref, shallowRef } from "vue";
import useAccountStore from "@/stores/accounts";
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";
import OrderCard from "./orderCard.vue";
import Tabs from "@/components/ui/tabs/Tabs.vue";
import TabsList from "@/components/ui/tabs/TabsList.vue";
import TabsTrigger from "@/components/ui/tabs/TabsTrigger.vue";
import TabsContent from "@/components/ui/tabs/TabsContent.vue";
import Card from "@/components/ui/card/Card.vue";
import CardHeader from "@/components/ui/card/CardHeader.vue";
import CardTitle from "@/components/ui/card/CardTitle.vue";
import CardDescription from "@/components/ui/card/CardDescription.vue";
import CardContent from "@/components/ui/card/CardContent.vue";
import Label from "@/components/ui/label/Label.vue";
import CardFooter from "@/components/ui/card/CardFooter.vue";
import useOrderStore from "@/stores/orders";
import Expense from "./Expenses.vue";
import PaymentCollected from "./PaymentCollected.vue";

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
    LoaderCircle,
    ArrowUpDown,
    CirclePlus,
    ConfirmDelete,
    GoBack,
    OrderCard,
    TabsList,
    Tabs,
    TabsTrigger,
    TabsContent,
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
    Label,
    CardFooter,
    Expense,
    PaymentCollected,
  },
  data() {
    return {
      form: {
        account_number: "",
        bank_id: "",
        active: true,
      },
      bank_id: "",
      showPanel: false,
      loading: false,
      loadingBanks: false,
      error: null,
      Accounts: [],
      columns: [],
      orderData: [],
      orders: [],
      expenses: [],
      incomes: [],
      expense_types: [],
      banks: [],
      bank_accounts: [],

      // updated component
      isUpdate: false,
      editId: null,
      // deleted component
      showDeleteDialog: false,
      deleteTitle: "",
      deleteId: null,

      pagination: {
        current_page: 1,
        per_page: 15,
        total: 0,
        last_page: 1,
        from: 0,
        to: 0,
      },
    };
  },
  created() {
    this.fetchOrder();
    this.ColumnDefinitions();
  },
  methods: {
    async fetchOrder() {
      this.loading = true;
      try {
        const id = this.$route.params.id;
        const response = await useOrderStore.getById(id);
        this.expenses = response.expenses;

        this.orders = response.order;

        this.incomes = response.income;
        this.expense_types = response.expense_type;
        this.banks = response.bank;
        this.bank_accounts = response.bank_account;
        this.form.order_id = this.orders.id;
      } catch (err) {
        this.error = "Unable to load order.";
      } finally {
        this.loading = false;
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
      this.form.bank_id = this.bank_id;

      try {
        const response = await useAccountStore.store(this.form);
        await this.fetchAccounts();
        this.showPanel = false;
        this.form.account_number = "";
        this.error = "";
        this.isSideFormVisible = false;
      } catch (error) {
        this.error =
          error.response?.data?.message ||
          error.message ||
          "Failed to save account information";
        console.error("Account save error:", error);
      } finally {
        this.loading = false;
      }
    },
    editBankAccount(bankAccount) {
      this.isUpdate = true;
      this.editId = bankAccount.id;
      this.form = {
        account_number: bankAccount.account_number,
        bank_id: this.bank_id,
      };
      this.showPanel = true;
    },

    async handleSubmitUpdate() {
      try {
        this.loading = true;
        const response = await useAccountStore.update(this.editId, this.form);

        this.showPanel = false;
        this.fetchAccounts(); // Refresh the list
        this.resetForm();
        this.isUpdate = false;
        this.editId = null;
      } catch (error) {
        this.error = error.message || "Failed to update expense";
      } finally {
        this.loading = false;
      }
    },
    confirmDelete(bankAccount) {
      this.deleteTitle = `Delete ${bankAccount.account_number}`;
      this.deleteId = bankAccount.id;
      this.showDeleteDialog = true;
    },
    async handleDelete() {
      try {
        this.loading = true;
        await useAccountStore.delete(this.deleteId);

        await this.fetchAccounts(); // Refresh the list
      } catch (error) {
        this.error = error.message || "Failed to delete bank";
      } finally {
        this.loading = false;
        this.showDeleteDialog = false;
        this.deleteId = null;
      }
    },
    updatePagination(meta) {
      this.pagination = {
        current_page: meta.current_page || 1,
        per_page: meta.per_page || this.pagination.per_page,
        total: meta.total || 0,
        last_page: meta.last_page || 1,
        from:
          meta.from ||
          ((meta.current_page || 1) - 1) *
            (meta.per_page || this.pagination.per_page) +
            1,
        to:
          meta.to ||
          Math.min(
            (meta.current_page || 1) *
              (meta.per_page || this.pagination.per_page),
            meta.total || 0
          ),
      };
    },

    resetForm() {
      this.form = {
        account_number: "",
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
          accessorKey: "account_number",
          header: "Account Number",
          cell: ({ row }) => {
            const account_number = row.getValue("account_number"); // Access the nested vehicle object
            return h("div", { class: "text-sm" }, account_number);
          },
        },

        {
          id: "actions",
          accessorKey: "actions",
          enableHiding: false,
          header: () =>
            h("div", { class: "relative text-right font-medium " }, ""),

          cell: ({ row }) => {
            const bankAccount = row.original;

            return h(
              "div",
              { class: "relative text-right font-medium " },
              [
                h(DropdownAction, {
                  item: bankAccount,
                  isEdit: true,
                  isDelete: true,
                  isShow: false,
                  onEdit: () => this.editBankAccount(bankAccount), // match method from parent
                  onDelete: () => this.confirmDelete(bankAccount), // match method from parent
                }),
              ],
              null
            );
          },
        },
      ];
    },

    handleDeleteOrder(order) {
      // Show confirmation and delete
      if (confirm(`Delete order ${order.orderNumber}?`)) {
        // API call to delete
      }
    },
  },
};
</script>




<style scoped>
.max-width {
  max-width: 800px;
}
</style>

