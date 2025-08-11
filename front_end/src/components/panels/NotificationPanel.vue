<script setup lang="ts">
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from "@/components/ui/sheet";
import { Button } from "@/components/ui/button";

import { Bell } from "lucide-vue-next";
import { ref } from "vue";

const isSheetOpen = ref(false);
</script>

<template>
    <Sheet v-model:open="isSheetOpen">
        <SheetTrigger>
            <Button
                class="relative rounded-full h-9 w-9"
                size="icon"
                variant="ghost"
            >
                <Bell />
                <!-- <span
                    class="absolute flex items-center justify-center w-5 h-5 text-xs rounded-full text-primary-foreground bg-primary -top-1 -right-1"
                >
                    {{ 1 }}
                </span> -->
            </Button>
        </SheetTrigger>
        <SheetContent :closable="true" class="px-4 py-6 space-y-4">
            <SheetHeader>
                <SheetTitle>Notifications</SheetTitle>
            </SheetHeader>
            <div class="relative flex items-start py-4 space-x-4">
                <div class="flex-1 text-sm">
                    <div class="flex items-center gap-2">
                        <p class="capitalize">"The notification title "</p>
                        <div class="w-2 h-2 rounded-full bg-primary"></div>
                    </div>
                    <p class="text-muted-foreground">"Notication message"</p>
                    <div class="flex items-center justify-between">
                        <p class="mt-1 text-sm text-muted-foreground">
                            "2023-09-20"
                        </p>
                        <Button size="sm" class="text-xs h-7">
                            See detail
                        </Button>
                    </div>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>
