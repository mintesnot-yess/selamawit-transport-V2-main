<template>
    <div class="space-y-6">
        <!-- Welcome banner -->
        <div class="rounded-xl p-6">
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between gap-4"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-3xl font-bold my-1">Hi, Minte</h4>
                        <p class="text-lg text-muted-foreground">
                            See the latest on your shipments and deliveries.
                        </p>
                    </div>
                </div>
                <!-- <Button
         @click="showPanel = true"
         class="bg-primary text-accent rounded-lg px-4 py-2 text-sm font-medium transition-colors backdrop-blur-sm self-start md:self-auto"
       >
         View Analytics
       </Button> -->
                <!-- <DataRangePicker v-model:range="dateRange" /> -->
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <StatCard
                icon="User"
                title="System Users"
                :value="users"
                color="blue"
            />
            <StatCard
                icon="Truck"
                title="Vehicles"
                :value="vehicles"
                color="green"
            />
            <StatCard
                icon="Users"
                title="Employees"
                :value="employees"
                color="purple"
            />
            <StatCard
                icon="ShoppingCart"
                title="Total Orders"
                :value="orderCount"
                color="orange"
            />
            <StatCard
                icon="ChartNoAxesColumnIcon"
                title="This Year Expense"
                :value="formatCurrency(totalExpense)"
                color="red"
            />
            <StatCard
                icon="ChartNoAxesCombined"
                title="This Year Income"
                :value="formatCurrency(totalIncome)"
                color="teal"
            />
            <!-- <StatCard
       icon="ChartNoAxesCombined"
       title="This Month Expense"
       value="$3,210"
       color="amber"
     /> -->
        </div>
        <div
            class="rounded-xl p-6 border border-surface-200 lg:col-span-2 bg-card"
        >
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6"
            >
                <div>
                    <h4
                        class="text-lg font-semibold leading-none tracking-tight my-1"
                    >
                        Income and Expense Overview
                    </h4>
                    <p class="text-sm text-muted-foreground">
                        Total performance for last 1 year
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-indigo-600 rounded-full"></span>
                        <span class="text-xs text-surface-500">Income</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-indigo-200 rounded-full"></span>
                        <span class="text-xs text-surface-500">Expose</span>
                    </div>
                </div>
            </div>
            <div class="h-64">
                <AreaChart
                    :labels="months"
                    :income-data="incomeData"
                    :expense-data="expenseData"
                />
            </div>
        </div>

        <template v-if="orders.length">
            <div
                class="flex flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card"
            >
                <div class="flex items-start justify-between">
                    <div class="flex gap-2 flex-col">
                        <h4
                            class="text-lg font-semibold leading-none tracking-tight"
                        >
                            Orders
                        </h4>
                        <p class="text-sm text-muted-foreground">
                            This month's order summary
                        </p>
                    </div>
                    <router-link to="/orders" class="flex items-center gap-2">
                        <Button
                            variant="ghost"
                            class="text-sm text-muted-foreground cursor-pointer"
                            >Show more</Button
                        >
                    </router-link>
                </div>

                <div class="rounded-lg overflow-hidden">
                    <!-- <Table :isSearchable="true" /> -->
                    <Table
                        :columns="columns"
                        :data="orders"
                        :is-searchable="false"
                        :is-pagination="false"
                        filter-column="status"
                    />
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import useDashboardStore from "@/stores/dashboard";

import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
    Card,
    CardTitle,
    CardHeader,
    CardFooter,
    Button,
    Checkbox,
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
    Input,
} from "@/components/ui";
import { StatCard, AreaChart } from "@/components/global";
import { RouterLink } from "vue-router";
import { Table, DropdownAction } from "@/components/table";

import {
    getCoreRowModel,
    getExpandedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from "@tanstack/vue-table";
import { ArrowUpDown, ChevronDown } from "lucide-vue-next";

import { defineComponent, h } from "vue";
import { valueUpdater } from "@/lib/utils";
import useExpenseStore from "@/stores/expenses";
import { DataRangePicker } from "@/components/form";

export default {
    components: {
        SidebarInset,
        SidebarProvider,
        SidebarTrigger,
        Card,
        CardTitle,
        CardHeader,
        CardFooter,
        Button,
        Checkbox,
        DropdownMenu,
        DropdownMenuCheckboxItem,
        DropdownMenuContent,
        DropdownMenuTrigger,
        Input,
        StatCard,
        AreaChart,
        Table,
        DropdownAction,
        RouterLink,
        DataRangePicker,
    },

    data() {
        return {
            sidebarOpen: false,
            users: [],
            vehicles: [],
            employees: [],
            orders: [],
            orderCount: 0,
            expenses: [],
            expenseData: [],
            incomeData: [],
            month: [],

            columns: [],
            dateRange: {
                start: null,
                end: null,
            },
        };
    },
    created() {
        this.fetchDashboard();

        this.ColumnDefinitions();
    },

    methods: {
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },
        closeSidebar() {
            this.sidebarOpen = false;
        },
        async fetchDashboard() {
            try {
                const response = await useDashboardStore.getAll();
                this.orders = response.orders;

                // Counts
                this.users = response.counts.users;
                this.vehicles = response.counts.vehicles;
                this.employees = response.counts.employees;
                this.orderCount = response.counts.orders;

                // Chart
                this.expenseData = response.chart.expenses;
                this.incomeData = response.chart.income;
                this.months = response.chart.months;

                // Totals
                this.totalIncome = response.totalIncome;
                this.totalExpense = response.totalExpense;
            } catch (error) {
                console.error("Failed to fetch dashboard data:", error);
            }
        },

        ColumnDefinitions() {
            this.columns = [
                {
                    accessorKey: "id",
                    header: ({ column }) => {
                        return h(
                            Button,
                            {
                                variant: "ghost",
                                onClick: () =>
                                    column.toggleSorting(
                                        column.getIsSorted() === "asc",
                                    ),
                            },
                            () => [
                                "Order Name",
                                h(ArrowUpDown, { class: "ml-2 h-4 w-4" }),
                            ],
                        );
                    },
                    cell: ({ row }) =>
                        h(
                            "div",
                            { class: "lowercase" },
                            "#order " + row.getValue("id"),
                        ),
                },

                {
                    accessorKey: "client",
                    header: "Client",
                    cell: ({ row }) => {
                        const client = row.original.client; // Access the nested client object
                        return h(
                            "div",
                            { class: "text-sm" },
                            client ? client.name : "",
                        );
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
                            vehicle
                                ? vehicle.vehicle_name +
                                      " | " +
                                      vehicle.plate_number
                                : "",
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
                            loadType ? loadType.name : "",
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
                            loadingLocation
                                ? loadingLocation.location_name
                                : "",
                        );
                    },
                },
                {
                    accessorKey: "destination_location",
                    header: "Destination",
                    cell: ({ row }) => {
                        const destinationLocation =
                            row.original.destination_location;
                        return h(
                            "div",
                            { class: "text-sm" },
                            destinationLocation
                                ? destinationLocation.location_name
                                : "",
                        );
                    },
                },

                {
                    accessorKey: "given_tariff",
                    header: () => h("div", { class: "text-right" }, "Amount"),
                    cell: ({ row }) => {
                        const amount = parseFloat(row.getValue("given_tariff"));
                        const formatted = new Intl.NumberFormat("en-US", {
                            style: "currency",
                            currency: "ETB",
                        }).format(amount);

                        return h(
                            "div",
                            { class: "text-right font-medium" },
                            formatted,
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
