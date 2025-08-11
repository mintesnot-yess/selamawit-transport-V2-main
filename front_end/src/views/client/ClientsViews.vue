<template>
  <div class="flex flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card">
    <div class="flex items-start justify-between">
      <div class="flex gap-2 flex-col">
        <h4 class="text-lg font-semibold leading-none tracking-tight">
          Clients
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
        :data="Clients"
        :isPagination="true"
        :isSearchable="true"
        :is-filter-select="false"
      />
    </div>
    <ConfirmDelete
      v-model:open="showDeleteDialog"
      :title="deleteTitle"
      description="Are you sure you want to delete this Client? This action cannot be undone."
      confirm-label="Delete Client"
      @confirm="handleDelete"
    />
    <Panel
      v-model="showPanel"
      :title="isUpdate ? 'Update Load Client' : 'Create A Client'"
      description="Fill the Client Information"
    >
      <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <div class="flex-1 space-y-2">
          <FormField name="name">
            <FormItem>
              <FormLabel>Client Name</FormLabel>
              <FormControl>
                <Input v-model="form.name" type="text" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <FormField name="contact_person">
            <FormItem>
              <FormLabel>Contact Person</FormLabel>
              <FormControl>
                <Input v-model="form.contact_person" type="text" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
          <FormField name="phone">
            <FormItem>
              <FormLabel>Phone</FormLabel>
              <FormControl>
                <Input v-model="form.phone" type="text" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <FormField name="address">
            <FormItem>
              <FormLabel>Address</FormLabel>
              <FormControl>
                <Input v-model="form.address" type="text" />
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

import { Table, DropdownAction } from "@/components/table";
import { h, ref, shallowRef } from "vue";
import useClientStore from "@/stores/clients";
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";

export default {
  components: {
    Panel,
    Table,
    LoaderCircle,
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
    ConfirmDelete,
  },
  data() {
    return {
      form: {
        name: "",
        address: "",
        contact_person: "",
        phone: "",
      },
      error: "",
      Clients: [],
      columns: [],
      showPanel: false,
      loading: false,
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
    this.fetchClients();
    this.ColumnDefinitions();
  },
  methods: {
    async fetchClients() {
      try {
        const response = await useClientStore.getAll();
        this.Clients = response.data || [];
        console.table(this.Clients);
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
      // If editingClient is set, update the client, else create new

      try {
        const response = await useClientStore.store(this.form);
        await this.fetchClients();

        this.error = "";
        this.showPanel = false;
        this.loading = false;
      } catch (error) {
        this.error =
          error.response?.data?.message ||
          error.message ||
          "Failed to save client information";
        console.error("client save error:", error);
      } finally {
        this.loading = false;
      }
    },
    editClient(client) {
      this.isUpdate = true;
      this.editId = client.id;
      this.form = {
        name: client.name,
        address: client.address,
        contact_person: client.contact_person,
        phone: client.phone,
      };
      this.showPanel = true;
    },

    async handleSubmitUpdate() {
      try {
        this.loading = true;
        const response = await useClientStore.update(this.editId, this.form);

        this.showPanel = false;
        this.fetchClients(); // Refresh the list
        this.resetForm();
        this.isUpdate = false;
        this.editId = null;
      } catch (error) {
        this.error = error.message || "Failed to update clients";
      } finally {
        this.loading = false;
      }
    },

    confirmDelete(client) {
      this.deleteTitle = `Delete ${client.name}`;
      this.deleteId = client.id;
      this.showDeleteDialog = true;
    },
    async handleDelete() {
      try {
        this.loading = true;
        await useClientStore.delete(this.deleteId);

        await this.fetchClients(); // Refresh the list
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
        address: "",
        contact_person: "",
        phone: "",
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
          header: "Client name",
          cell: ({ row }) => {
            const Client_name = row.getValue("name"); // Access the nested vehicle object
            return h("div", { class: "text-sm" }, Client_name);
          },
        },
        {
          accessorKey: "contact_person",
          header: "contact person",
          cell: ({ row }) => {
            const contact_person = row.getValue("contact_person"); // Access the nested vehicle object
            return h("div", { class: "text-sm" }, contact_person);
          },
        },
        {
          accessorKey: "phone",
          header: "phone",
          cell: ({ row }) => {
            const phone = row.getValue("phone"); // Access the nested vehicle object
            return h("div", { class: "text-sm" }, phone);
          },
        },
        {
          accessorKey: "address",
          header: "address",
          cell: ({ row }) => {
            const address = row.original.address; // Access the nested vehicle object
            return h("div", { class: "text-sm" }, address);
          },
        },

        {
          id: "actions",
          accessorKey: "actions",
          enableHiding: false,

          header: () =>
            h("div", { class: "relative text-right font-medium " }, ""),

          cell: ({ row }) => {
            const client = row.original;

            return h("div", { class: "relative text-right font-medium " }, [
              h(DropdownAction, {
                item: client,
                isEdit: true,
                isDelete: true,
                isShow: false,
                onEdit: () => this.editClient(client), // match method from parent
                onDelete: () => this.confirmDelete(client), // match method from parent
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






