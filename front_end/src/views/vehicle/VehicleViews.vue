<template>
    <div class="flex flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card">
        <div class="flex items-start justify-between">
            <div class="flex gap-2 flex-col">
                <h4 class="text-lg font-semibold leading-none tracking-tight">
                    Vehicles
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
                :data="vehicles"
                :isPagination="true"
                :isSearchable="true"
                :is-filter-select="true"
                filter-select-column="owner_type"
                filter-select-label="Owner"
                :filter-select-options="[
                    { label: 'All', value: '__all' },
                    { label: 'PRIVATE', value: 'PRIVATE' },
                    { label: 'OWNED', value: 'OWNED' },
                ]"
            />
        </div>
        <ConfirmDelete
            v-model:open="showDeleteDialog"
            :title="deleteTitle"
            description="Are you sure you want to delete this vehicle? This action cannot be undone."
            confirm-label="Delete Vehicle"
            @confirm="handleDelete"
        />

        <Panel
            v-model="showPanel"
            :title="
                isUpdate ? 'Update vehicle Information' : 'Create A Vehicle'
            "
            description="Fill the Vehicle Information"
        >
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <FormField name="vehicle_name">
                    <FormItem>
                        <FormLabel>Vehicle Name</FormLabel>
                        <FormControl>
                            <Input
                                type="text"
                                v-model="form.vehicle_name"
                                placeholder="Enter vehicle name"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="plate_number">
                    <FormItem>
                        <FormLabel>Plate Number</FormLabel>
                        <FormControl>
                            <Input
                                type="text"
                                v-model="form.plate_number"
                                placeholder="Enter plate number"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="owner_name">
                    <FormItem>
                        <FormLabel>Owner Name</FormLabel>
                        <FormControl>
                            <Input
                                type="text"
                                v-model="form.owner_name"
                                placeholder="Enter owner name"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="owner_phone">
                    <FormItem>
                        <FormLabel>Owner Phone</FormLabel>
                        <FormControl>
                            <Input
                                type="text"
                                v-model="form.owner_phone"
                                placeholder="Enter owner phone"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="owner_type">
                    <FormItem>
                        <FormLabel>Owner Type</FormLabel>
                        <Select v-model="form.owner_type">
                            <FormControl>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select type" />
                                </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                <SelectItem value="OWNED">OWNED</SelectItem>
                                <SelectItem value="PRIVATE">PRIVATE</SelectItem>
                            </SelectContent>
                        </Select>

                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField name="libre">
                    <FormItem>
                        <FormLabel>Libre</FormLabel>
                        <FormControl>
                            <div class="relative flex items-center">
                                <input
                                    id="libre_input"
                                    type="file"
                                    class="sr-only"
                                    @change="
                                        form.libre = $event.target.files[0]
                                    "
                                />
                                <label
                                    for="libre_input"
                                    class="flex items-center justify-center w-full px-4 py-2 bg-white border border-input rounded-md shadow-sm cursor-pointer hover:bg-accent hover:text-accent-foreground transition-colors text-foreground"
                                >
                                    <Upload class="mr-2 h-4 w-4" />
                                    <span>
                                        {{
                                            form.libre && form.libre.name
                                                ? form.libre.name
                                                : "Choose file..."
                                        }}
                                    </span>
                                </label>
                            </div>
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <div if="error" class="text-red-500 text-sm">
                    {{ error }}
                </div>

                <Button type="submit" class="w-full">
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

<script>
import {
    ArrowUpDown,
    CirclePlus,
    Loader2,
    LoaderCircle,
    Upload,
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
    SelectGroup,
    SelectItem,
} from "@/components/ui";

import { Table, DropdownAction } from "@/components/table";
import { h, ref, shallowRef } from "vue";
import useVehicleStore from "@/stores/vehicles";
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";
import router from "@/router";
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
        Upload,
        Select,
        SelectTrigger,
        SelectValue,
        SelectContent,
        SelectGroup,
        SelectItem,
        Loader2,
        ArrowUpDown,
        CirclePlus,
        LoaderCircle,
        ConfirmDelete,
    },
    data() {
        return {
            showPanel: false,
            error: "",
            loading: false,
            vehicles: [],
            columns: [],
            form: {
                vehicle_name: "",
                plate_number: "",
                owner_name: "",
                owner_phone: "",
                owner_type: "",
                libre: "",
            },
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
        this.fetchVehicles();
        this.ColumnDefinitions();
    },
    methods: {
        async fetchVehicles() {
            try {
                const response = await useVehicleStore.getAll();
                this.vehicles = response.data || [];
                console.table(this.vehicles);
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
            // If editingVehicle is set, update the vehicle, else create new

            try {
                const response = await useVehicleStore.store(this.form);
                await this.fetchVehicles();

                this.showPanel = false;
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    error.message ||
                    "Failed to save vehicle information";
                console.error("vehicle save error:", error);
            } finally {
                this.loading = false;
            }
        },
        editVehicle(vehicle) {
            this.isUpdate = true;
            this.editId = vehicle.id;
            this.form = {
                vehicle_name: vehicle.vehicle_name,
                plate_number: vehicle.plate_number,
                owner_name: vehicle.owner_name,
                owner_type: vehicle.owner_type,
                owner_phone: vehicle.owner_phone,
                libre: vehicle.libre,
            };
            this.showPanel = true;
        },

        async handleSubmitUpdate() {
            try {
                this.loading = true;
                const response = await useVehicleStore.update(
                    this.editId,
                    this.form,
                );

                this.showPanel = false;
                this.fetchVehicles(); // Refresh the list
                this.resetForm();
                this.isUpdate = false;
                this.editId = null;
            } catch (error) {
                this.error = error.message || "Failed to update expense";
            } finally {
                this.loading = false;
            }
        },

        confirmDelete(vehicle) {
            this.deleteTitle = `Delete ${vehicle.name}`;
            this.deleteId = vehicle.id;
            this.showDeleteDialog = true;
        },
        async handleDelete() {
            try {
                this.loading = true;
                await useVehicleStore.delete(this.deleteId);

                await this.fetchVehicles(); // Refresh the list
            } catch (error) {
                this.error = error.message || "Failed to delete vehicle";
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
                                (table.getIsSomePageRowsSelected() &&
                                    "indeterminate"),
                            "onUpdate:modelValue": (value) =>
                                table.toggleAllPageRowsSelected(!!value),
                            ariaLabel: "Select all",
                        }),
                    cell: ({ row }) =>
                        h(Checkbox, {
                            modelValue: row.getIsSelected(),
                            "onUpdate:modelValue": (value) =>
                                row.toggleSelected(!!value),
                            ariaLabel: "Select row",
                        }),
                    enableSorting: false,
                    enableHiding: false,
                },

                {
                    accessorKey: "vehicle",
                    header: "Vehicle",
                    cell: ({ row }) => {
                        const plate_number = row.original.plate_number; // Access the nested vehicle object
                        const vehicle_name = row.original.vehicle_name; // Access the nested vehicle object
                        const vehicle_id = row.original.id; // Access the nested vehicle object

                        return h(
                            RouterLink,
                            { class:  "text-blue-600 hover:text-blue-800 hover:underline lowercase",
                                to: `/vehicle/${vehicle_id}`,
                             },
                            vehicle_name + " | " + plate_number,
                        );
                    },
                },
                {
                    accessorKey: "owner_type",
                    header: "owner type",
                    cell: ({ row }) => {
                        const owner_type = row.getValue("owner_type"); // Access the nested vehicle object
                        return h("div", { class: "text-sm" }, owner_type);
                    },
                },
                {
                    accessorKey: "owner_name",
                    header: "owner name",
                    cell: ({ row }) => {
                        const owner_name = row.getValue("owner_name"); // Access the nested vehicle object
                        return h("div", { class: "text-sm" }, owner_name);
                    },
                },
                {
                    accessorKey: "owner_phone",
                    header: "owner phone",
                    cell: ({ row }) => {
                        const owner_phone = row.getValue("owner_phone"); // Access the nested vehicle object
                        return h("div", { class: "text-sm" }, owner_phone);
                    },
                },
                {
                    accessorKey: "libre",
                    header: "libre",
                    cell: ({ row }) => {
                        const libre = row.getValue("libre"); // Access the nested vehicle object
                        return h("img", {
                            class: "text-sm h-7 ",
                            src: import.meta.env.VITE_IMAGE_STORAGE_URL + libre,
                        });
                    },
                },

                {
                    id: "actions",
                    accessorKey: "actions",
                    enableHiding: false,
                    header: () =>
                        h(
                            "div",
                            { class: "relative text-right font-medium " },
                            "",
                        ),

                    cell: ({ row }) => {
                        const vehicle = row.original;

                        return h(
                            "div",
                            { class: "relative text-right font-medium " },
                            [
                                h(DropdownAction, {
                                    item: vehicle,
                                    isEdit: true,
                                    isDelete: true,
                                    isShow: true,
                                    onEdit: () => this.editVehicle(vehicle), // match method from parent
                                    onDelete: () => this.confirmDelete(vehicle), // match method from parent
                                    onShow: () =>
                                        router.push(`/vehicle/${vehicle.id}`),
                                }),
                            ],
                            null,
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
