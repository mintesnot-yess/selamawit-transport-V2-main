<template>
  <div class="flex flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card">
    <div class="flex items-start justify-between">
      <div class="flex gap-2 flex-col">
        <h4 class="text-lg font-semibold leading-none tracking-tight">Banks</h4>
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
      <Table :columns="columns" :data="Banks" :isPagination="true" :isSearchable="true" :is-filter-select="false"
        filter-select-column="status" filter-select-label="Status" :filter-select-options="[
          { label: 'All', value: '__all' },
          { label: 'PENDING', value: 'PENDING' },
          { label: 'IN_PROGRESS', value: 'IN_PROGRESS' },
          { label: 'COMPLETED', value: 'COMPLETED' },
          { label: 'CANCELLED', value: 'CANCELLED' },
        ]" />
    </div>
    <ConfirmDelete v-model:open="showDeleteDialog" :title="deleteTitle"
      description="Are you sure you want to delete this bank? This action cannot be undone." confirm-label="Delete Bank"
      @confirm="handleDelete" />
    <Panel v-model="showPanel" :title="isUpdate ? 'Update Bank Information' : 'Create  Bank'" :description="isUpdate ? '' : 'Fill in the information to create a new bank'
      ">
      <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <div class="flex-1 space-y-2">
          <FormField name="name">
            <FormItem>
              <FormLabel>Bank name</FormLabel>
              <FormControl>
                <Input v-model="form.name" type="text" />
              </FormControl>
              <span class="text-sm pt-2 text-red-600" v-if="error">{{
                error
                }}</span>
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

import { Table, DropdownAction } from "@/components/table";
import { h, ref, shallowRef } from "vue";
import useBankStore from "@/stores/banks";
import router from "@/router";
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";
import { RouterLink } from "vue-router";

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
  },
  data() {
    return {
      form: {
        name: "",
      },
      showPanel: false,
      loading: false,
      error: null,
      Banks: [],
      columns: [],
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
    this.fetchBanks();
    this.ColumnDefinitions();
  },
  methods: {
    async fetchBanks() {
      try {
        const response = await useBankStore.getAll();
        this.Banks = response.data || [];
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
      // If editingBank is set, update the bank, else create new

      try {
        const response = await useBankStore.store(this.form);
        await this.fetchBanks();
        this.showPanel = false;
        this.resetForm();
      } catch (error) {
        this.error =
          error.response?.data?.message ||
          error.message ||
          "Failed to save bank information";
      } finally {
        this.loading = false;
      }
    },
    editBank(bank) {
      this.isUpdate = true;
      this.editId = bank.id;
      this.form = {
        name: bank.name,
      };
      this.showPanel = true;
    },

    async handleSubmitUpdate() {
      try {
        this.loading = true;
        const response = await useBankStore.update(this.editId, this.form);

        this.showPanel = false;
        this.fetchBanks(); // Refresh the list
        this.resetForm();
        this.isUpdate = false;
        this.editId = null;
      } catch (error) {
        this.error = error.message || "Failed to update expense";
      } finally {
        this.loading = false;
      }
    },

    confirmDelete(bank) {
      this.deleteTitle = `Delete ${bank.name}`;
      this.deleteId = bank.id;
      this.showDeleteDialog = true;
    },
    async handleDelete() {
      try {
        this.loading = true;
        await useBankStore.delete(this.deleteId);

        await this.fetchBanks(); // Refresh the list
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
          accessorKey: "name",
          header: "Bank name",
          cell: ({ row }) => {

            const bank = row.original;
            return h(
              RouterLink, // Use RouterLink component
              {
                class:
                  "hover:text-blue-800 hover:underline",
                to: `/banks/accounts/${bank.id}`,
              },
              ` ${bank.name} `);


          },
        },

        {
          id: "actions",
          accessorKey: "actions",
          enableHiding: false,
          header: () =>
            h("div", { class: "relative text-right font-medium " }, ""),

          cell: ({ row }) => {
            const bank = row.original;

            return h(
              "div",
              { class: "relative text-right font-medium " },
              [
                h(DropdownAction, {
                  item: bank,
                  isEdit: true,
                  isDelete: true,
                  isShow: true,
                  onEdit: () => this.editBank(bank), // match method from parent
                  onDelete: () => this.confirmDelete(bank), // match method from parent
                  onShow: () => router.push(`/banks/accounts/${bank.id}`),
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
