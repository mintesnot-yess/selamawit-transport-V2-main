<script setup lang="ts">
import { ChevronRight, type LucideIcon } from "lucide-vue-next";
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from "@/components/ui/collapsible";
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from "@/components/ui/sidebar";

defineProps<{
    singleItems: {
        name: string;
        url: string;
        icon: LucideIcon;
    }[];
    reportItems: {
        name: string;
        url: string;
        icon?: LucideIcon;
        isActive?: boolean;
        permission?: string;

        items?: {
            name: string;
            url: string;
        }[];
    }[];
    orderItems: {
        name: string;
        url: string;
        icon: LucideIcon;
        permission?: string;
    }[];
    generalItems: {
        name: string;
        url: string;
        icon: LucideIcon;
        permission?: string;
    }[];
    systemUser: {
        name: string;
        url: string;
        icon: LucideIcon;
        permission?: string;
    }[];
}>();
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel>Core</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in singleItems" :key="item.name">
                <SidebarMenuButton :tooltip="item.name" as-child>
                    <a :href="item.url">
                        <component :is="item.icon" />
                        <span>{{ item.name }}</span>
                    </a>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>

    <SidebarGroup>
        <SidebarGroupLabel>Orders</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in orderItems" :key="item.name">
                <SidebarMenuButton :tooltip="item.name" as-child>
                    <a :href="item.url">
                        <component :is="item.icon" />
                        <span>{{ item.name }}</span>
                    </a>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
    <SidebarGroup>
        <SidebarGroupLabel>Reports</SidebarGroupLabel>

        <SidebarMenu>
            <Collapsible
                v-for="item in reportItems"
                :key="item.name"
                as-child
                class="group/collapsible"
            >
                <SidebarMenuItem>
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton :tooltip="item.name">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ item.name }}</span>
                            <ChevronRight
                                class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                            />
                        </SidebarMenuButton>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <SidebarMenuSub>
                            <SidebarMenuSubItem
                                v-for="subItem in item.items"
                                :key="subItem.name"
                            >
                                <SidebarMenuSubButton as-child>
                                    <a :href="subItem.url">
                                        <span>{{ subItem.name }}</span>
                                    </a>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                        </SidebarMenuSub>
                    </CollapsibleContent>
                </SidebarMenuItem>
            </Collapsible>
        </SidebarMenu>
    </SidebarGroup>
    <!-- settings -->
    <SidebarGroup>
        <SidebarGroupLabel>Settings</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in generalItems" :key="item.name">
                <SidebarMenuButton :tooltip="item.name" as-child>
                    <a :href="item.url">
                        <component :is="item.icon" />
                        <span>{{ item.name }}</span>
                    </a>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
    <!-- system user -->
    <SidebarGroup>
        <SidebarGroupLabel>System User</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in systemUser" :key="item.name">
                <SidebarMenuButton :tooltip="item.name" as-child>
                    <a :href="item.url">
                        <component :is="item.icon" />
                        <span>{{ item.name }}</span>
                    </a>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
