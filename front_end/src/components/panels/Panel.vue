<script setup lang="ts">
import { type HtmlHTMLAttributes, ref } from "vue"; // ✅ import ref from Vue
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Sheet,
  SheetClose,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@/components/ui/sheet";
import { cn } from "@/lib/utils";

// ✅ make showPanel reactive
const showPanel = ref(false);

const props = withDefaults(
  defineProps<{
    modelValue?: boolean;
    title: string;
    description: string;
    closable?: boolean;
    position?: "left" | "right" | "top" | "bottom";
    class?: HtmlHTMLAttributes["class"];
  }>(),
  {
    modelValue: true,
    closable: true,
    position: "right",
  }
);

const emit = defineEmits(["update:modelValue"]);

const handleClose = () => {
  emit("update:modelValue", false);
};
</script>

<template>
  <Sheet :open="modelValue" @update:open="handleClose">
    <SheetContent
      :side="position"
      :closable="closable"
      :class="cn('flex flex-col py-4 ', props.class)"
    >
      <SheetHeader>
        <SheetTitle>{{ title }}</SheetTitle>
        <SheetDescription
          v-if="description"
          class="max-w-2xl -mt-1 text-sm text-muted-foreground"
        >
          {{ description }}
        </SheetDescription>
      </SheetHeader>
      <div
        class="flex-grow p-4 overflow-y-auto scrollbar-thin scrollbar-track-transparent scrollbar-thumb-muted"
      >
        <slot />
      </div>
    </SheetContent>
  </Sheet>
</template>
