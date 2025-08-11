<script setup lang="ts">
import { MoreVertical, Eye, Pencil, Trash2 } from "lucide-vue-next";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  isEdit: {
    type: Boolean,
    default: true,
  },
  isDelete: {
    type: Boolean,
    default: true,
  },
  isShow: {
    type: Boolean,
    default: false,
  },
  editLabel: {
    type: String,
    default: "Edit",
  },
  deleteLabel: {
    type: String,
    default: "Delete",
  },
  showLabel: {
    type: String,
    default: "View",
  },
});

const emit = defineEmits(["edit", "delete", "show"]);

const handleShow = () => emit("show", props.item);
const handleEdit = () => emit("edit", props.item);
const handleDelete = () => emit("delete", props.item);
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="w-8 h-8 p-0">
        <span class="sr-only">Open menu</span>
        <MoreVertical class="w-4 h-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem v-if="isShow" @click="handleShow">
        <Eye class="mr-2 h-4 w-4" />
        {{ showLabel }}
      </DropdownMenuItem>

      <DropdownMenuSeparator v-if="isShow && (isEdit || isDelete)" />

      <DropdownMenuItem v-if="isEdit" @click="handleEdit">
        <Pencil class="mr-2 h-4 w-4" />
        {{ editLabel }}
      </DropdownMenuItem>

      <DropdownMenuItem
        v-if="isDelete"
        @click="handleDelete"
        class="text-red-600 focus:text-red-600"
      >
        <Trash2 class="mr-2 h-4 w-4" />
        {{ deleteLabel }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>