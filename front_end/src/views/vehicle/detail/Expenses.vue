<template>
    <div>
        <Card>
            <CardHeader class="px-2">
                <CardTitle class="flex justify-between">Expense

                </CardTitle>
                <CardDescription> </CardDescription>
            </CardHeader>
            <CardContent>
                <ScrollArea class="w-[80vw] sm:w-auto overflow-x-auto">
                    <Table :columns="ExpenseColumns" :data="expenseData" :isPagination="true" :isSearchable="false"
                        :is-filter-select="false" filter-select-column="expense_type" filter-select-label="Type"
                        :filter-select-options="[
                            { label: 'All', value: '__all' },
                            { label: 'Employee', value: 'employee' },
                        ]" />
                    <ScrollBar orientation="horizontal" />
                </ScrollArea>
            </CardContent>
        </Card>


    </div>
</template>
<script>
import ConfirmDelete from "@/components/form/ConfirmDelete.vue";
import Panel from "@/components/panels/Panel.vue";
import { DropdownAction } from "@/components/table";
import Table from "@/components/table/Table.vue";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    ScrollArea,
    ScrollBar,
} from "@/components/ui";
import Button from "@/components/ui/button/Button.vue";
import Checkbox from "@/components/ui/checkbox/Checkbox.vue";
import FormControl from "@/components/ui/form/FormControl.vue";
import FormItem from "@/components/ui/form/FormItem.vue";
import FormLabel from "@/components/ui/form/FormLabel.vue";
import Input from "@/components/ui/input/Input.vue";
import Select from "@/components/ui/select/Select.vue";
import SelectItem from "@/components/ui/select/SelectItem.vue";
import SelectTrigger from "@/components/ui/select/SelectTrigger.vue";
import SelectValue from "@/components/ui/select/SelectValue.vue";
import router from "@/router";
import useExpenseStore from "@/stores/expenses";
import useExpenseTypeStore from "@/stores/expenseTypes";
import {
    ChevronDown,
    CirclePlus,
    ExternalLink,
    Loader2,
    Upload,
} from "lucide-vue-next";
import { h } from "vue";

export default {
    components: {
        Card,
        CardContent,
        CardDescription,
        CardHeader,
        CardTitle,
        Table,
        TableBody,
        TableCell,
        TableHead,
        TableHeader,
        TableRow,
        ScrollArea,
        ScrollBar,
        Panel,
        Button,
        DropdownAction,
        CirclePlus,
        ConfirmDelete,
        Input,
        FormControl,
        FormItem,
        FormLabel,
        Select,
        SelectItem,
        SelectTrigger,
        SelectValue,
        ChevronDown,
        Checkbox,
        Loader2,
        Upload,
    },

    props: {
        expenseData: {
            type: Object,
            required: false,
        },
    },
    emits: ["data-changed"],
    data() {
        return {

            loading: false,
            error: null,

            ExpenseColumns: [],


        };
    },
    created() {
        this.ColumnDefinitions();
    },
    methods: {


        ColumnDefinitions() {
            this.ExpenseColumns = [


                {
                    accessorKey: "type",
                    header: "Type",
                    cell: ({ row }) => {
                        const expenseType = row.original.expense_type; // Access the nested vehicle object
                        return h(
                            "div",
                            { class: "text-sm" },
                            expenseType ? expenseType.category + " | " + expenseType.name : ""
                        );
                    },
                },

                {
                    accessorKey: "amount",
                    header: () => h("div", "Amount"),
                    cell: ({ row }) => {
                        const amount = parseFloat(row.getValue("amount"));
                        const formatted = new Intl.NumberFormat("en-US", {
                            style: "currency",
                            currency: "ETB",
                        }).format(amount);

                        return h("div", { class: " " }, formatted);
                    },
                },
                {
                    accessorKey: "from",
                    header: "From",
                    cell: ({ row }) => {
                        const expense_bank = row.original.from_bank; // Access the nested client object
                        const expense_account = row.original.from_account; // Access the nested client object
                        return h(
                            "div",
                            { class: "text-sm" },
                            expense_bank
                                ? expense_bank.name + " | " + expense_account.account_number
                                : ""
                        );
                    },
                },

                {
                    accessorKey: "to_account",
                    header: "To",
                    cell: ({ row }) => {
                        const expense_bank = row.original.to_bank; // Access the nested client object
                        const paid_date = row.getValue("to_account"); //

                        return h(
                            "div",
                            { class: "text-sm" },
                            expense_bank ? expense_bank.name + " | " + paid_date : ""
                        );
                    },
                },
                {
                    accessorKey: "paid_date",
                    header: "Date",
                    cell: ({ row }) => {
                        const paid_date = row.getValue("paid_date"); //
                        //
                        const formattedDate = new Date(paid_date).toLocaleDateString(
                            "en-US",
                            {
                                year: "numeric",
                                month: "short",
                                day: "numeric",
                            }
                        );
                        //  Access the nested vehicle object
                        return h("div", { class: "text-sm" }, formattedDate);
                    },
                },


            ];
        },

        formatDate(dateString) {
            if (!dateString) return "N/A";
            const options = { year: "numeric", month: "short", day: "numeric" };
            return new Date(dateString).toLocaleDateString("en-US", options);
        },
        formatCurrency(value) {
            if (!value) return "N/A";
            return new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "ETB",
            }).format(value);
        },
    },
};
</script>