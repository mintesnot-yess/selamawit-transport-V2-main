<template>
  <div>
    <div class="flex align-center">
      <div v-if="isSearchable" class="flex items-center px-1">
        <Input class="max-w-sm" :placeholder="`Search...`" :model-value="filterValue" @update:model-value="
          table.getColumn(filterColumn || '')?.setFilterValue($event)
          " />
      </div>
      <div v-if="isFilterSelect" class="flex items-center py-1 px-1">
        <label class="mr-2 text-sm font-medium">{{ filterSelectLabel }}</label>
        <Select v-model="selectedFilterValue" class="max-w-xs">
          <SelectTrigger class="w-fit">
            <SelectValue placeholder="Select..." />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-for="option in filterSelectOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </SelectItem>
          </SelectContent>
        </Select>
      </div>
    </div>
    <!-- Rest of your table template stays the same -->
    <div class="rounded-lg p-4 flex-1 overflow-auto">
      <Table class="">
        <TableHeader class="bg-accent">
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id" class="hover:bg-transparent">
            <TableHead v-for="header in headerGroup.headers" :key="header.id"
              class="px-6 py-3 text-left text-xs font-medium text-accent-foreground uppercase tracking-wider">
              <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                :props="header.getContext()" />
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody class="divide-y">
          <template v-if="table.getRowModel().rows?.length">
            <TableRow v-for="row in table.getRowModel().rows" :key="row.id"
              :data-state="row.getIsSelected() ? 'selected' : undefined" class="transition-colors"
              :class="{ 'bg-blue-50': row.getIsSelected() }">
              <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id"
                class="px-6 py-4 whitespace-nowrap text-sm text-accent-foreground">
                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
              </TableCell>
            </TableRow>
          </template>
          <template v-else>
            <TableRow>
              <TableCell :colspan="columns.length" class=" p-10 text-gray-500 italic">
                No results.
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </div>
    <div v-if="isPagination" class="flex items-center justify-between px-2">
      <div class="flex-1 text-sm text-muted-foreground">
        {{ table.getFilteredSelectedRowModel().rows.length }} of
        {{ table.getFilteredRowModel().rows.length }} row(s) selected.
      </div>
      <div class="flex items-center space-x-6 lg:space-x-8">
        <div class="flex items-center space-x-2">
          <p class="text-sm font-medium">Rows per page</p>
          <Select :model-value="`${table.getState().pagination.pageSize}`" @update:model-value="table.setPageSize">
            <SelectTrigger class="h-8 w-[70px]">
              <SelectValue :placeholder="`${table.getState().pagination.pageSize}`" />
            </SelectTrigger>
            <SelectContent side="top">
              <SelectItem v-for="pageSize in resultPerPage.options" :key="pageSize" :value="`${pageSize}`">
                {{ pageSize }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div class="flex w-[100px] items-center justify-center text-sm font-medium">
          Page {{ table.getState().pagination.pageIndex + 1 }} of
          {{ table.getPageCount() }}
        </div>
        <div class="flex items-center space-x-2">
          <Button variant="outline" class="hidden w-8 h-8 p-0 lg:flex" :disabled="!table.getCanPreviousPage()"
            @click="table.setPageIndex(0)">
            <span class="sr-only">Go to first page</span>
            <ChevronsLeft class="w-4 h-4" />
          </Button>
          <Button variant="outline" class="w-8 h-8 p-0" :disabled="!table.getCanPreviousPage()"
            @click="table.previousPage()">
            <span class="sr-only">Go to previous page</span>
            <ChevronLeftIcon class="w-4 h-4" />
          </Button>
          <Button variant="outline" class="w-8 h-8 p-0" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
            <span class="sr-only">Go to next page</span>
            <ChevronRightIcon class="w-4 h-4" />
          </Button>
          <Button variant="outline" class="hidden w-8 h-8 p-0 lg:flex" :disabled="!table.getCanNextPage()"
            @click="table.setPageIndex(table.getPageCount() - 1)">
            <span class="sr-only">Go to last page</span>
            <ChevronsRight class="w-4 h-4" />
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type {
  ColumnDef,
  ColumnFiltersState,
  SortingState,
} from "@tanstack/vue-table";
import {
  FlexRender,
  getCoreRowModel,
  useVueTable,
  getPaginationRowModel,
  getSortedRowModel,
  getFilteredRowModel,
} from "@tanstack/vue-table";

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
  Button,
  Input,
  Checkbox,
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui";
import { resultPerPage } from "@/components/constants";
import { valueUpdater } from "@/lib/utils";
import { computed, ref } from "vue";
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  ChevronsLeft,
  ChevronsRight,
} from "lucide-vue-next";

// Define props including new ones
interface TableProps {
  columns: ColumnDef<{ id: number; name: string }, unknown>[];
  data: { id: number; name: string }[];
  isSearchable?: boolean;
  isPagination?: boolean;
  filterColumn?: string;
  isFilterSelect?: boolean;
  filterSelectColumn?: string;
  filterSelectLabel?: string;
  filterSelectOptions?: Array<{ label: string; value: any }>;
}
const props = withDefaults(defineProps<TableProps>(), {
  isSearchable: false,
  filterColumn: "id",
  isFilterSelect: false,
  filterSelectColumn: "",
  filterSelectLabel: "Select...",
});

// Setup state
const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const rowSelection = ref({});

const table = useVueTable({
  get data() {
    return props.data;
  },
  get columns() {
    return props.columns;
  },
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
  onColumnFiltersChange: (updaterOrValue) =>
    valueUpdater(updaterOrValue, columnFilters),
  getFilteredRowModel: getFilteredRowModel(),
  onRowSelectionChange: (updaterOrValue) =>
    valueUpdater(updaterOrValue, rowSelection),
  state: {
    get sorting() {
      return sorting.value;
    },
    get columnFilters() {
      return columnFilters.value;
    },
    get rowSelection() {
      return rowSelection.value;
    },
  },
});

// Computed value to get current filter value dynamically
const filterValue = computed(() => {
  const col = props.filterColumn ? table.getColumn(props.filterColumn) : null;
  return col ? (col.getFilterValue() as string) : "";
});
const selectedFilterValue = computed({
  get() {
    const col = props.filterSelectColumn
      ? table.getColumn(props.filterSelectColumn)
      : null;
    const value = col ? col.getFilterValue() : "__all";
    return value === undefined ? "__all" : value;
  },
  set(value) {
    const col = props.filterSelectColumn
      ? table.getColumn(props.filterSelectColumn)
      : null;
    if (col) {
      // If "All" is selected, clear the filter
      const filterValue = value === "__all" ? undefined : value;
      col.setFilterValue(filterValue);
    }
  },
});
</script>
