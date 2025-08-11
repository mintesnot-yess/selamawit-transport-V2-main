<template>
    <div class="flex flex-col space-y-3">
        <GoBack />
        <div class="flex items-start justify-between px-1">
            <div>
                <div class="flex items-center space-x-5 font-normal text-2xl">
                    <h4>Vehicle Details</h4>
                </div>
                <p class="text-sm text-muted-foreground">
                    Manage access vehicle information.
                </p>
            </div>
        </div>


        <main class="grid items-start flex-1 gap-4 md:gap-8 lg:grid-cols-3 xl:grid-cols-3">

            <Card class="overflow-hidiven">
                <CardHeader class="flex flex-row items-start">
                    <div class="flex items-start gap-2">
                        <div class="flex flex-col justify-around">
                            <div>
                                <CardTitle class="flex items-center gap-2 group">
                                    {{ vehicle.vehicle_name }}
                                </CardTitle>
                            </div>
                        </div>
                    </div>

                </CardHeader>

                <CardContent class="space-y-4 text-sm">
                    <div class="grid gap-5">
                        <div class="font-semibold">Vehicle Information</div>
                        <dl class="grid gap-3">
                            <div class="flex items-center justify-between">
                                <dt class="text-muted-foreground">Vehicle Name:</dt>
                                <dd>{{ vehicle.vehicle_name || `N/A` }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-muted-foreground">Plate Number:</dt>
                                <dd>{{ vehicle.plate_number || "N/A" }}</dd>
                            </div>

                            <div class="flex items-center justify-between">
                                <dt class="text-muted-foreground">Owner Name:</dt>
                                <dd>{{ vehicle.owner_name || "N/A" }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-muted-foreground">Owner Phone:</dt>
                                <dd>{{ vehicle.owner_phone || "N/A" }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-muted-foreground">Owner Type:</dt>
                                <dd>{{ vehicle.owner_type || "N/A" }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-muted-foreground">libre:</dt>
                                <dd><img src="" alt="" width="30" height="30"></dd>
                            </div>



                        </dl>


                    </div>
                </CardContent>
                <CardFooter class="flex flex-row items-center justify-between px-6 py-3 border-t bg-muted/50">
                    <div class="text-sm text-muted-foreground">
                        Last updated: {{ formatDate(vehicle.updated_at) }}
                    </div>
                </CardFooter>
            </Card>
            <div class="grid items-start gap-1 auto-rows-max md:gap-4 lg:col-span-2 overflow-x-auto">
                <Tabs default-value="expense">
                    <div class="flex items-center">
                        
                    </div>


                    

                    <TabsContent value="expense">
                        <Expenses :expenseData="expenses" @data-changed="fetchVehicleDetail" />
                    </TabsContent>
                </Tabs>
            </div>
        </main>
    </div>
</template>

<script>
import { ArrowUpDown, CirclePlus, LoaderCircle } from "lucide-vue-next";
import useVehicleStore from "@/stores/vehicles";

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
import { h, ref, shallowRef, version } from "vue";
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";
import Tabs from "@/components/ui/tabs/Tabs.vue";
import TabsList from "@/components/ui/tabs/TabsList.vue";
import TabsTrigger from "@/components/ui/tabs/TabsTrigger.vue";
import TabsContent from "@/components/ui/tabs/TabsContent.vue";
import Card from "@/components/ui/card/Card.vue";
import CardHeader from "@/components/ui/card/CardHeader.vue";
import CardTitle from "@/components/ui/card/CardTitle.vue";
import CardDescription from "@/components/ui/card/CardDescription.vue";
import CardContent from "@/components/ui/card/CardContent.vue";
import DetailCard from './detailCard.vue'

 import Expenses from "./Expenses.vue";
import CardFooter from "@/components/ui/card/CardFooter.vue";

export default {
    components: {
        Tabs,
        TabsList,
        TabsTrigger,
        TabsContent,
        Card,
        CardHeader,
        CardTitle,
        CardDescription,
        CardFooter,
        CardContent,
        DetailCard,
        Expenses,
        GoBack
    },
    data() {
        return {
            loading: false,
            loadingBanks: false,
            error: null,
            vehicle: [],
            expenses: [],

            columns: [],

            // deleted component

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
        this.fetchVehicleDetail();
        this.ColumnDefinitions();
    },
    methods: {
        async fetchVehicleDetail() {
            this.loading = true;
            try {
                const id = this.$route.params.id;
                const response = await useVehicleStore.getById(id);
                this.vehicle = response.vehicle;
                this.expenses = response.expenses;


                 
            } catch (err) {
                this.error = "Unable to load vehicle.";
            } finally {
                this.loading = false;
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
                        meta.total || 0,
                    ),
            };
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
            ];
        },


        

        handleDeleteOrder(vehicle) {
            // Show confirmation and delete
            if (confirm(`Delete vehicle ${vehicle.orderNumber}?`)) {
                // API call to delete
            }
        },
        formatDate(dateString) {
            if (!dateString) return "N/A";
            const options = { year: "numeric", month: "short", day: "numeric" };
            return new Date(dateString).toLocaleDateString("en-US", options);
        },

    },
};
</script>

<style scoped>
.max-width {
    max-width: 800px;
}
</style>
