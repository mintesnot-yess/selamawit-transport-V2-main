<template>
  <SidebarProvider class="select-none font-inter">
    <AppSidebar />
    <SidebarInset class="h-screen overflow-hidden space-y-1 bg-accent">
      <header
        class="flex h-16 border-b border-border bg-sidebar shrink-0 items-center justify-between px-6 gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12"
      >
        <div class="flex items-center gap-2">
          <SidebarTrigger class="-ml-1" />
        </div>
        <div class="space-x-3">
          <NotificationsPanel />
        </div>
      </header>
      <div class="p-4 md:p-6 overflow-auto">
        <router-view :key="$route.fullPath" />
      </div>
    </SidebarInset>
  </SidebarProvider>
</template>

<script setup lang="ts">
import { AppSidebar } from "@/components/navigation";
import { NotificationsPanel } from "@/components/panels";
import { SidebarInset, SidebarProvider, SidebarTrigger } from "@/components/ui";
import ScrollArea from "../ui/scroll-area/ScrollArea.vue";
</script>
