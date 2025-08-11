<template>
  <div class="flex flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card">
    <div class="flex items-start justify-between">
      <div class="flex gap-2 flex-col">
        <h4 class="text-lg font-semibold leading-none tracking-tight">
          Load Types
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
        :data="loadTypes"
        :isPagination="true"
        :isSearchable="true"
        :is-filter-select="false"
      />
    </div>
    <ConfirmDelete
      v-model:open="showDeleteDialog"
      :title="deleteTitle"
      description="Are you sure you want to delete this Load Type? This action cannot be undone."
      confirm-label="Delete LoadType"
      @confirm="handleDelete"
    />

    <Panel
      v-model="showPanel"
      :title="isUpdate ? 'Update Load Type Information' : 'Create A Load Type'"
      description="Fill the LoadType Information"
    >
      <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <div class="flex-1 space-y-2">
          <FormField name="name">
            <FormItem>
              <FormLabel>Site name</FormLabel>
              <FormControl>
                <Input type="text" v-model="form.name" />
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
import useLoadTypeStore from "@/stores/loadTypes";
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";

export default {
  components: {
    Panel,
    Table,
    DropdownAction,
    Button,
    FormControl,
    CirclePlus,
    FormField,
    LoaderCircle,
    FormItem,
    FormLabel,
    FormMessage,
    Input,
    Checkbox,
    ConfirmDelete,
  },
  data() {
    return {
      showPanel: false,
      loading: false,
      error: false,
      loadTypes: [],
      columns: [],
      form: {
        name: "",
      },
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
    this.fetchLoadTypes();
    this.ColumnDefinitions();
  },
  methods: {
    async fetchLoadTypes() {
      try {
        const response = await useLoadTypeStore.getAll();
        this.loadTypes = response.data || [];
        console.table(this.loadTypes);
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
      // If editingLoadTypes is set, update the load_type, else create new

      try {
        const response = await useLoadTypeStore.store(this.form);
        await this.fetchLoadTypes();

        this.form.name = "";
        this.isSideFormVisible = false;
        this.showPanel = false;
        this.loading = false;
      } catch (error) {
        this.error =
          error.response?.data?.message ||
          error.message ||
          "Failed to save load_type information";
        console.error("Load Type save error:", error);
      } finally {
        this.loading = false;
      }
    },

    editLoadType(loadType) {
      this.isUpdate = true;
      this.editId = loadType.id;
      this.form = {
        name: loadType.name,
      };
      this.showPanel = true;
    },

    async handleSubmitUpdate() {
      try {
        this.loading = true;
        const response = await useLoadTypeStore.update(this.editId, this.form);

        this.showPanel = false;
        this.fetchLoadTypes(); // Refresh the list
        this.resetForm();
        this.isUpdate = false;
        this.editId = null;
      } catch (error) {
        this.error = error.message || "Failed to update load Type";
      } finally {
        this.loading = false;
      }
    },

    confirmDelete(loadType) {
      this.deleteTitle = `Delete ${loadType.name}`;
      this.deleteId = loadType.id;
      this.showDeleteDialog = true;
    },
    async handleDelete() {
      try {
        this.loading = true;
        await useLoadTypeStore.delete(this.deleteId);

        await this.fetchLoadTypes(); // Refresh the list
      } catch (error) {
        this.error = error.message || "Failed to delete Load Type";
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
          header: "Name",
          cell: ({ row }) => {
            const loadType_name = row.getValue("name"); // Access the nested vehicle object
            return h("div", { class: "text-sm" }, loadType_name);
          },
        },

        {
          id: "actions",
          accessorKey: "actions",
          enableHiding: false,

          header: () =>
            h("div", { class: "relative text-right font-medium " }, ""),

          cell: ({ row }) => {
            const loadType = row.original;

            return h("div", { class: "relative text-right font-medium " }, [
              h(DropdownAction, {
                item: loadType,
                isEdit: true,
                isDelete: true,
                isShow: false,
                onEdit: () => this.editLoadType(loadType), // match method from parent
                onDelete: () => this.confirmDelete(loadType), // match method from parent
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






