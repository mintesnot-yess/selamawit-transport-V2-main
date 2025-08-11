<template>
  <Button size="icon" variant="outline" @click="goBack">
    <ChevronLeft />
  </Button>
</template>

<script setup lang="ts">
import { useRouter } from "vue-router";
import { Button } from "../ui";
import { ChevronLeft } from "lucide-vue-next";

const router = useRouter();

function goBack() {
  router.back();
}
</script>