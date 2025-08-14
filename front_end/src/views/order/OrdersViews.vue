<template>
  <div class="flex flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card">
    <div class="flex items-start justify-between">
      <div class="flex gap-2 flex-col">
        <h4 class="text-lg font-semibold leading-none tracking-tight">
          Orders
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
      <Table :columns="columns" :data="orders" :isPagination="true" :isSearchable="true" :is-filter-select="true"
        filter-select-column="current_condition" filter-select-label="Condition" :filter-select-options="[
          { label: 'All', value: '__all' },
          { label: 'Loaded', value: 'LOADED' },
          { label: 'OFFLOADED', value: 'OFFLOADED' },
        ]" />
    </div>
    <ConfirmDelete v-model:open="showDeleteDialog" :title="deleteTitle"
      description="Are you sure you want to delete this order? This action cannot be undone."
      confirm-label="Delete Order" @confirm="handleDelete" />
    <Panel v-model="showPanel" :title="isUpdate ? 'Update Order Information' : 'Create  Order'" :description="isUpdate ? '' : 'Fill in the information to create a new bank'
      ">
      <form @submit.prevent="handleSubmit" class="flex flex-col gap-4 h-full font-sans">
        <div class="flex-1 space-y-2 overflow-y-scroll py-3 px-2">
          <!-- Client -->
          <FormField name="clients">
            <FormItem class="space-y-1">
              <FormLabel class="block text-sm font-medium">Client</FormLabel>
              <Select v-model="form.client_id">
                <FormControl>
                  <SelectTrigger
                    class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <SelectValue placeholder="Select Clients" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent class="rounded-md shadow-lg border border-gray-200 bg-white">
                  <SelectItem v-for="item in clients" :key="item.id" :value="item.id"
                    class="hover:bg-gray-100 px-2 py-1.5 cursor-pointer">
                    {{ item.name }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <FormMessage class="text-xs text-red-500" />
            </FormItem>
          </FormField>

          <!-- Driver -->
          <FormField name="driver">
            <FormItem class="space-y-1">
              <FormLabel class="block text-sm font-medium">Driver</FormLabel>
              <Select v-model="form.employee_id">
                <FormControl>
                  <SelectTrigger
                    class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <SelectValue placeholder="Select Driver" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent class="rounded-md shadow-lg border border-gray-200 bg-white">
                  <SelectItem v-for="item in employees" :key="item.id" :value="item.id"
                    class="hover:bg-gray-100 px-2 py-1.5 cursor-pointer">
                    {{ item.first_name }} {{ item.last_name }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <FormMessage class="text-xs text-red-500" />
            </FormItem>
          </FormField>

          <!-- Vehicle -->
          <FormField name="vehicle">
            <FormItem class="space-y-1">
              <FormLabel class="block text-sm font-medium">Vehicle</FormLabel>
              <Select v-model="form.vehicle_id">
                <FormControl>
                  <SelectTrigger
                    class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <SelectValue placeholder="Select vehicle" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent class="rounded-md shadow-lg border border-gray-200 bg-white">
                  <SelectItem v-for="item in vehicles" :key="item.id" :value="item.id"
                    class="hover:bg-gray-100 px-2 py-1.5 cursor-pointer">
                    {{ item.plate_number }} - {{ item.vehicle_name }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <FormMessage class="text-xs text-red-500" />
            </FormItem>
          </FormField>

          <!-- Load Types -->
          <FormField name="loadType">
            <FormItem class="space-y-1">
              <FormLabel class="block text-sm font-medium">Load Type</FormLabel>
              <Select v-model="form.load_type_id">
                <FormControl>
                  <SelectTrigger
                    class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <SelectValue placeholder="Select loadType" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent class="rounded-md shadow-lg border border-gray-200 bg-white">
                  <SelectItem v-for="item in loadTypes" :key="item.id" :value="item.id"
                    class="hover:bg-gray-100 px-2 py-1.5 cursor-pointer">
                    {{ item.name }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <FormMessage class="text-xs text-red-500" />
            </FormItem>
          </FormField>

          <!-- Loading Place -->
          <FormField name="Loading">
            <FormItem class="space-y-1">
              <FormLabel class="block text-sm font-medium">Loading Place</FormLabel>
              <Select v-model="form.loading_place">
                <FormControl>
                  <SelectTrigger
                    class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <SelectValue placeholder="Select Loading Place" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent class="rounded-md shadow-lg border border-gray-200 bg-white">
                  <SelectItem v-for="item in locations" :key="item.id" :value="item.id"
                    class="hover:bg-gray-100 px-2 py-1.5 cursor-pointer">
                    {{ item.location_name }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <FormMessage class="text-xs text-red-500" />
            </FormItem>
          </FormField>

          <!-- Destination Place -->
          <FormField name="destination">
            <FormItem class="space-y-1">
              <FormLabel class="block text-sm font-medium">Destination Place</FormLabel>
              <Select v-model="form.destination">
                <FormControl>
                  <SelectTrigger
                    class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <SelectValue placeholder="Select Destination Place" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent class="rounded-md shadow-lg border border-gray-200 bg-white">
                  <SelectItem v-for="item in locations" :key="item.id" :value="item.id"
                    class="hover:bg-gray-100 px-2 py-1.5 cursor-pointer">
                    {{ item.location_name }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <FormMessage class="text-xs text-red-500" />
            </FormItem>
          </FormField>

          <!-- Quintal -->
          <FormField name="Quintal">
            <FormItem class="space-y-1">
              <FormLabel class="block text-sm font-medium">Quintal</FormLabel>
              <FormControl>
                <Input type="number" v-model="form.quintal"
                  class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500" />
              </FormControl>
              <FormMessage class="text-xs text-red-500" />
            </FormItem>
          </FormField>

          <!-- Given Tariff -->
          <FormField name="given_tariff">
            <FormItem class="space-y-1">
              <FormLabel class="block text-sm font-medium">Given Tariff</FormLabel>
              <FormControl>
                <Input type="number" v-model="form.given_tariff"
                  class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500" />
              </FormControl>
              <FormMessage class="text-xs text-red-500" />
            </FormItem>
          </FormField>

          <!-- Sub Tariff -->
          <FormField name="sub_tariff">
            <FormItem class="space-y-1">
              <FormLabel class="block text-sm font-medium">Sub Tariff</FormLabel>
              <FormControl>
                <Input type="number" v-model="form.sub_tariff"
                  class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500" />
              </FormControl>
              <FormMessage class="text-xs text-red-500" />
            </FormItem>
          </FormField>

          <!-- Date Fields -->
          <div class="pt-2">
            <div class="grid grid-cols-2 gap-4">
              <FormField name="arrival_at_loading_site">
                <FormItem class="space-y-1">
                  <FormLabel class="block text-sm font-medium">Arrival at Loading Site</FormLabel>
                  <FormControl>
                    <Input type="date" v-model="form.arrival_at_loading_site"
                      class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                  </FormControl>
                  <FormMessage class="text-xs text-red-500" />
                </FormItem>
              </FormField>

              <FormField name="loading_date">
                <FormItem class="space-y-1">
                  <FormLabel class="block text-sm font-medium">Loading Date</FormLabel>
                  <FormControl>
                    <Input type="date" v-model="form.loading_date"
                      class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                  </FormControl>
                  <FormMessage class="text-xs text-red-500" />
                </FormItem>
              </FormField>
            </div>
          </div>

          <!-- Current Condition -->
          <FormField name="current_condition">
            <FormItem class="space-y-1">
              <FormLabel class="block text-sm font-medium">Current Condition</FormLabel>
              <Select v-model="form.current_condition">
                <FormControl>
                  <SelectTrigger
                    class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <SelectValue placeholder="Select Status" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent class="rounded-md shadow-lg border border-gray-200 bg-white">
                  <SelectItem value="OFFLOADED" class="hover:bg-gray-100 px-2 py-1.5 cursor-pointer">
                    OFFLOADED
                  </SelectItem>
                  <SelectItem value="LOADED" class="hover:bg-gray-100 px-2 py-1.5 cursor-pointer">
                    LOADED
                  </SelectItem>
                </SelectContent>
              </Select>
              <FormMessage class="text-xs text-red-500" />
            </FormItem>
          </FormField>
        </div>
        <!-- Error Message -->
        <div v-if="error" class="text-red-500 text-sm text-center mt-2">
          {{ error }}
        </div>
        <!-- Submit Button -->
        <Button type="submit"
          class="mt-2 w-full  text-white font-semibold py-2 rounded-md transition-colors duration-200">
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
  Select,
  SelectValue,
  SelectTrigger,
  SelectContent,
  SelectItem,
} from "@/components/ui";

import { Table, DropdownAction } from "@/components/table";
import { h, ref, shallowRef } from "vue";
import useOrderStore from "@/stores/orders";
import router from "@/router";
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";
import { RouterLink } from "vue-router";

export default {
  components: {
    Panel,
    ConfirmDelete,
    Table,
    CirclePlus,
    DropdownAction,
    Button,
    FormControl,
    FormField,
    FormItem,
    LoaderCircle,
    FormLabel,
    FormMessage,
    Input,
    Checkbox,
    Select,
    SelectValue,
    SelectTrigger,
    SelectContent,
    SelectItem,
  },
  data() {
    return {
      form: {
        client_id: "",
        employee_id: "",
        vehicle_id: "",
        load_type_id: "",
        loading_place: "",
        destination: "",
        quintal: "",
        given_tariff: "",
        sub_tariff: "",
        arrival_at_loading_site: "",
        loading_date: "",
        current_condition: "",
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
      //--------------
      error: "",
      orders: [],
      columns: [],
      clients: [],
      employees: [],
      vehicles: [],
      locations: [],
      loadTypes: [],
    };
  },
  created() {
    this.fetchOrders();
    this.ColumnDefinitions();
  },
  methods: {
    async fetchOrders() {
      try {
        const response = await useOrderStore.getAll();
        this.orders = response.data || [];
        if (response.clients) this.clients = response.clients;
        if (response.employees) this.employees = response.employees;
        if (response.vehicles) this.vehicles = response.vehicles;
        if (response.locations) this.locations = response.locations;
        if (response.loadTypes) this.loadTypes = response.loadTypes;
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
      // If editingOrder is set, update the order, else create new
      // make Validation
      // if (
      //   !this.form.client_id ||
      //   !this.form.employee_id ||
      //   !this.form.vehicle_id ||
      //   !this.form.loading_place ||
      //   !this.form.destination ||
      //   !this.form.load_type_id ||
      //   this.form.quintal === null ||
      //   this.form.given_tariff === null ||
      //   this.form.sub_tariff === null ||
      //   !this.form.arrival_at_loading_site ||
      //   !this.form.loading_date ||
      //   !this.form.current_condition
      // ) {
      //   this.error = "Please fill all required fields";
      //   this.loading = false;
      //   return;
      // }
      if (!this.form.client_id) {
        this.error = "Please select a client";
        this.loading = false;
        this.$refs.client_id.focus();
        return;
      }
      if (!this.form.employee_id) {
        this.error = "Please select a driver";
        this.loading = false;
        this.$refs.employee_id.focus();
        return;
      }
      if (!this.form.vehicle_id) {
        this.error = "Please select a vehicle";
        this.loading = false;
        this.$refs.vehicle_id.focus();
        return;
      }
      if (!this.form.load_type_id) {
        this.error = "Please select a load type";
        this.loading = false;
        this.$refs.load_type_id.focus();
        return;

      }

      try {
        const response = await useOrderStore.store(this.form);
        await this.fetchOrders();
        this.$router.push("orders");

        this.showPanel = false;
        this.loading = false;
        this.error = "";
      } catch (error) {
        this.error =
          error.response?.data?.message ||
          error.message ||
          "Failed to save order information";
        console.error("Order save error:", error);
      } finally {
        this.loading = false;
      }
    },
    editBank(order) {
      this.isUpdate = true;
      this.editId = order.id;
      this.form = {
        client_id: order.client_id,

        employee_id: order.employee_id,
        vehicle_id: order.vehicle_id,
        load_type_id: order.load_type_id,
        loading_place: order.loading_place,
        destination: order.destination,
        quintal: order.quintal,
        given_tariff: order.given_tariff,
        sub_tariff: order.sub_tariff,
        arrival_at_loading_site: order.arrival_at_loading_site,
        loading_date: order.loading_date,
        current_condition: order.current_condition,
      };
      this.showPanel = true;
    },

    async handleSubmitUpdate() {
      try {
        this.loading = true;
        const response = await useOrderStore.update(this.editId, this.form);

        this.showPanel = false;
        this.fetchOrders(); // Refresh the list
        this.resetForm();
        this.isUpdate = false;
        this.editId = null;
      } catch (error) {
        this.error = error.message || "Failed to update order";
      } finally {
        this.loading = false;
      }
    },

    confirmDelete(order) {
      this.deleteTitle = `Delete #OR${order.id}`;
      this.deleteId = order.id;
      this.showDeleteDialog = true;
    },
    async handleDelete() {
      try {
        this.loading = true;
        await useOrderStore.delete(this.deleteId);

        await this.fetchOrders(); // Refresh the list
      } catch (error) {
        this.error = error.message || "Failed to delete order";
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
        client_id: "",
        employee_id: "",
        vehicle_id: "",
        load_type_id: "",

        loading_place: "",
        destination: "",
        quintal: "",
        given_tariff: "",
        sub_tariff: "",
        arrival_at_loading_site: "",
        loading_date: "",
        current_condition: "",
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
          header: ({ column }) => {
            return h(
              Button,
              {
                variant: "ghost",
                onClick: () =>
                  column.toggleSorting(column.getIsSorted() === "asc"),
              },
              () => ["Order Name", h(ArrowUpDown, { class: "ml-2 h-4 w-4" })]
            );
          },
          cell: ({ row }) => {
            const orderId = row.getValue("id");

            return h(
              RouterLink, // Use RouterLink component
              {
                class:
                  "text-blue-600 hover:text-blue-800 hover:underline lowercase",
                to: `/order/detail/${orderId}`,
              },
              `#order ${orderId}`
            );
          },
        },

        {
          accessorKey: "client",
          header: "Client",
          cell: ({ row }) => {
            const client = row.original.client; // Access the nested client object
            return h("div", { class: "text-sm" }, client ? client.name : "");
          },
        },
        {
          accessorKey: "vehicle",
          header: "Vehicle",
          cell: ({ row }) => {
            const vehicle = row.original.vehicle; // Access the nested vehicle object
            return h(
              "div",
              { class: "text-sm" },
              vehicle ? vehicle.vehicle_name + " | " + vehicle.plate_number : ""
            );
          },
        },
        {
          accessorKey: "employee",
          header: "Driver",
          cell: ({ row }) => {
            const employee = row.original.employee; // Access the nested vehicle object
            return h(
              "div",
              { class: "text-sm" },
              employee ? employee.first_name : ""
            );
          },
        },
        {
          accessorKey: "phone",
          header: "Phone",
          cell: ({ row }) => {
            const employee = row.original.employee; // Access the nested vehicle object
            return h(
              "div",
              { class: "text-sm" },
              employee ? employee.phone : ""
            );
          },
        },

        {
          accessorKey: "loading_location",
          header: "Loading",
          cell: ({ row }) => {
            const loadingLocation = row.original.loading_location;
            return h(
              "div",
              { class: "text-sm" },
              loadingLocation ? loadingLocation.location_name : ""
            );
          },
        },
        {
          accessorKey: "destination_location",
          header: "Destination",
          cell: ({ row }) => {
            const destinationLocation = row.original.destination_location;
            return h(
              "div",
              { class: "text-sm" },
              destinationLocation ? destinationLocation.location_name : ""
            );
          },
        },
        {
          accessorKey: "load_type",
          header: "Load Type",
          cell: ({ row }) => {
            const loadType = row.original.load_type;
            return h(
              "div",
              { class: "text-sm" },
              loadType ? loadType.name : ""
            );
          },
        },
        {
          accessorKey: "quintal",
          header: "Quintal",
          cell: ({ row }) => {
            const quintal = row.getValue("quintal");

            return h("div", { class: "text-sm" }, quintal);
          },
        },
        {
          accessorKey: "given_tariff",
          header: () => h("div", { class: "text-right" }, "Given Tariff"),
          cell: ({ row }) => {
            const amount = parseFloat(row.getValue("given_tariff"));
            const formatted = new Intl.NumberFormat("en-US", {
              style: "currency",
              currency: "ETB",
            }).format(amount);

            return h("div", { class: "text-right " }, formatted);
          },
        },
        {
          accessorKey: "sub_tariff",
          header: () => h("div", { class: "text-right" }, "Sub Tariff"),
          cell: ({ row }) => {
            const amount = parseFloat(row.getValue("sub_tariff"));
            const formatted = new Intl.NumberFormat("en-US", {
              style: "currency",
              currency: "ETB",
            }).format(amount);

            return h("div", { class: "text-right " }, formatted);
          },
        },
        {
          accessorKey: "total_revenue",
          header: () => h("div", { class: "text-right" }, "Total Revenue"),
          cell: ({ row }) => {
            const amount = parseFloat(
              row.getValue("quintal") * row.getValue("given_tariff")
            );
            const formatted = new Intl.NumberFormat("en-US", {
              style: "currency",
              currency: "ETB",
            }).format(amount);

            return h("div", { class: "text-right " }, formatted);
          },
        },
        {
          accessorKey: "revenue",
          header: () => h("div", { class: "text-right" }, "Revenue"),
          cell: ({ row }) => {
            // revenue = total_revenue - to_be_paid
            const total_revenue =
              row.getValue("quintal") * row.getValue("given_tariff");
            const to_be_paid =
              row.getValue("quintal") * row.getValue("sub_tariff");

            const amount = parseFloat(total_revenue - to_be_paid);
            const formatted = new Intl.NumberFormat("en-US", {
              style: "currency",
              currency: "ETB",
            }).format(amount);

            return h("div", { class: "text-right " }, formatted);
          },
        },
        {
          accessorKey: "to_be_paid",
          header: () => h("div", { class: "text-right" }, "To Be Paid"),
          cell: ({ row }) => {
            const quintal = row.getValue("quintal");
            const sub_tariff = row.getValue("sub_tariff");
            // to_be_paid = quintal * sub_tariff
            const amount = parseFloat(quintal * sub_tariff);
            const formatted = new Intl.NumberFormat("en-US", {
              style: "currency",
              currency: "ETB",
            }).format(amount);

            return h("div", { class: "text-right " }, formatted);
          },
        },

        {
          accessorKey: "status",
          header: "Status",
          cell: ({ row }) =>
            h("div", { class: "text-sm" }, row.getValue("status")),
        },
        {
          accessorKey: "current_condition",
          header: "Current Condition",
          cell: ({ row }) => {
            const current_condition = row.getValue("current_condition");
            return h("div", { class: "text-sm " }, current_condition);
          },
        },
        {
          accessorKey: "payment_collected",
          header: "Payment Collected",
          cell: ({ row }) => {
            const payment_collected = row.getValue("payment_collected")
              ? "Collected"
              : "Pending";
            return h("div", { class: "text-sm " }, payment_collected);
          },
        },
        {
          id: "actions",
          accessorKey: "actions",
          enableHiding: false,

          header: () =>
            h("div", { class: "relative text-right font-medium " }, ""),

          cell: ({ row }) => {
            const order = row.original;

            return h("div", { class: "relative text-right font-medium " }, [
              h(DropdownAction, {
                item: order,
                isEdit: true,
                isDelete: true,
                isShow: true,
                onEdit: () => this.editBank(order), // match method from parent
                onDelete: () => this.confirmDelete(order), // match method from parent
                onShow: () => router.push(`/order/detail/${order.id}`),
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






<style scoped>
*::-webkit-scrollbar {
  display: none;
}

/* Form input styling */
form :deep(input) {
  transition: all 0.2s ease;
}

/* Focus state */
form :deep(input:focus) {
  outline: 2px solid rgb(0, 183, 255);
  box-shadow: 0 0 0 3px rgba(0, 183, 255, 0.2);
}

/* Valid state */
form :deep(input:user-valid) {
  outline: 1px solid rgb(172, 255, 234);
  background-color: rgba(172, 255, 234, 0.05);
}

/* Invalid state */
form :deep(input:user-invalid) {
  outline: 1px solid rgba(255, 0, 0, 0.651);
  color: rgb(255, 81, 81);
  background-color: rgba(255, 0, 0, 0.05);
}
</style>