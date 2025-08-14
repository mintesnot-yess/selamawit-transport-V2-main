<template>
  <GoBack />
  <div class="flex mt-2 flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card">
    <div class="flex items-start justify-between">
      <div class="flex gap-2 flex-col">
        <h4 class="text-lg font-semibold leading-none tracking-tight">
          Accounts
        </h4>
        <p class="text-sm text-muted-foreground"></p>
      </div>
      <div class="flex items-center gap-2">
        <Button @click="showPanel = true" variant="ghost"
          class="text-sm text-muted-foreground flex gap-1 items-center justify-center cursor-pointer">
          <CirclePlus />
          Create New
        </Button>
      </div>
    </div>

    <div class="rounded-lg overflow-hidden">
      <Table :columns="columns" :data="Accounts" :isPagination="true" :isSearchable="true" :is-filter-select="false"
        filter-select-column="status" filter-select-label="Status" :filter-select-options="[
          { label: 'All', value: '__all' },
          { label: 'PENDING', value: 'PENDING' },
          { label: 'IN_PROGRESS', value: 'IN_PROGRESS' },
          { label: 'COMPLETED', value: 'COMPLETED' },
          { label: 'CANCELLED', value: 'CANCELLED' },
        ]" />
    </div>
    <ConfirmDelete v-model:open="showDeleteDialog" :title="deleteTitle"
      description="Are you sure you want to delete this bank account number ? This action cannot be undone."
      confirm-label="Delete" @confirm="handleDelete" />
    <Panel v-model="showPanel" title="Create A Account Number" description="Fill the Account Information">
      <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <div class="flex-1 space-y-2">
          <FormField name="name">
            <FormItem>
              <FormLabel>Account Number</FormLabel>
              <FormControl>
                <Input v-model="form.account_number" type="number" />
              </FormControl>
              <input v-model="this.bank_id" type="hidden" />
              <span class="text-sm pt-2 text-red-600" v-if="error">
                {{ error }}
              </span>
              <FormMessage />
            </FormItem>
          </FormField>
        </div>
        <Button type="submit">
          <span v-if="loading">
            <LoaderCircle class="fa-solid size-6 fa-circle-notch animate-spin" />
          </span>
          <span v-else> {{ isUpdate ? "Edit" : "Submit" }} </span>
        </Button>
      </form>
    </Panel>
  </div>
</template>

<script>
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
    this.fetchAccounts();
    this.ColumnDefinitions();
  },
  methods: {
    async fetchAccounts(page = 1) {
      this.loadingBanks = true;
      try {
        this.bank_id = this.$route.params.id;

        // Fetch paginated bank accounts for the specific bank
        const response = await useAccountStore.getBankAccounts(this.bank_id, {
          page: page,
          per_page: this.pagination.per_page,
        });

        if (response.data && response.meta) {
          this.Accounts = response.data;
          this.updatePagination(response.meta);
        } else if (response.accounts && response.pagination) {
          this.banks = response.accounts;
          this.updatePagination(response.pagination);
        } else if (Array.isArray(response)) {
          this.Accounts = response;
          this.updatePagination({
            current_page: 1,
            per_page: this.pagination.per_page,
            total: response.length,
            last_page: 1,
            from: 1,
            to: response.length,
          });
        } else {
          console.error("Unexpected response format:", response);
          throw new Error("Unexpected API response format");
        }
      } catch (error) {
        console.error("Error fetching bank accounts:", error);
        this.error = error.message;
        this.banks = [];
        // this.$toast.error("Failed to load bank accounts: " + error.message);
      } finally {
        this.loadingBanks = false;
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
  },
};
</script>
