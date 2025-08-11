<!-- src/components/FilterInput.vue -->
<script setup lang="ts">
defineProps<{
  modelValue: string;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: string): void;
}>();
</script>

<template>
  <Input
    class="max-w-sm"
    placeholder="Filter by ID..."
    :model-value="modelValue"
    @update:model-value="emit('update:modelValue', $event)"
  />
</template>
