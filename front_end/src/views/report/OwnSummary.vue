<template>
    <div class="flex flex-col gap-6 p-6 rounded-xl border shadow-sm bg-card">
        <div class="flex items-start justify-between">
            <div class="flex gap-2 flex-col">
                <h4 class="text-lg font-semibold leading-none tracking-tight">
                    Own Summary
                </h4>
                <p class="text-sm text-muted-foreground"></p>
            </div>

        </div>

        <div class="rounded-lg overflow-hidden">
            <Table :columns="columns" :data="report" :isPagination="true" :isSearchable="true" :is-filter-select="false"
                filter-select-column="owner_type" filter-select-label="Owner" />
        </div>

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
import useLocationStore from "@/stores/locations";
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";
import useLogStore from "@/stores/logs";
import useReportStore from "@/stores/report";

export default {
    components: {
        Panel,
        Table,
        DropdownAction,
        Button,
        LoaderCircle,
        FormControl,
        FormField,
        FormItem,
        FormLabel,
        FormMessage,
        Input,
        Checkbox,
        ConfirmDelete,
        CirclePlus,
    },
    data() {
        return {
            error: false,
            loading: false,
            report: [],
            columns: [],


            loading: false,


        };
    },
    created() {
        this.fetchOwnSummary();
        this.ColumnDefinitions();
    },
    methods: {
        async fetchOwnSummary() {
            try {
                const response = await useReportStore.getOwnSummary();
                this.report = response.data.orders || [];
                console.table(response.data);
            } catch (error) {
                console.error("Failed to fetch users:", error);
            }
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
                    accessorKey: "action",
                    header: "Activity",
                    cell: ({ row }) => {
                        const action = row.getValue("action");
                        return h("div", { class: "text-sm" }, action);
                    },
                },
                {
                    accessorKey: "user",
                    header: "User",
                    cell: ({ row }) => {
                        const user = row.getValue("user");
                        if (!user) return h("div", { class: "text-sm text-gray-400" }, "Unknown");
                        return h(
                            "a",
                            {
                                class: "text-sm text-blue-600 hover:underline",
                                href: `/users/${user.id}`,
                                title: `View profile of ${user.name}`,
                            },
                            user.name || "Unknown"
                        );
                    },
                },
                {
                    accessorKey: "timestamp",
                    header: "Time",
                    cell: ({ row }) => {
                        const timestamp = row.getValue("timestamp");
                        if (!timestamp) return h("div", { class: "text-sm text-gray-400" }, "N/A");
                        const date = new Date(timestamp);
                        const formatted = date.toLocaleString("en-US", {
                            year: "numeric",
                            month: "short",
                            day: "2-digit",
                            hour: "2-digit",
                            minute: "2-digit",
                        });
                        return h("div", { class: "text-sm font-mono" }, formatted);
                    },
                },
                {
                    accessorKey: "ip_address",
                    header: "IP Address",
                    cell: ({ row }) => {
                        const ip_add = row.getValue("ip_address");
                        return h(
                            "div",
                            {
                                class: "text-sm",
                                style: "font-family: monospace; color: #64748b;",
                                title: ip_add,
                            },
                            ip_add || "—"
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
