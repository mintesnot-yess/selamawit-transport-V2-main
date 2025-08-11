<script setup lang="ts">
import { type LucideIcon } from "lucide-vue-next";
import { ChevronRight } from "lucide-vue-next";

// UI Components
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
import { usePermissions } from "@/hooks/usePermissions";

// Define props
defineProps<{
    report: {
        name: string;
        url: string;
        icon: LucideIcon;
        permission: string;
    }[];
    items: {
        title: string;
        url: string;
        icon?: LucideIcon;
        isActive?: boolean;
        items?: {
            title: string;
            url: string;
        }[];
    }[];
    order: {
        name: string;
        url: string;
        icon: LucideIcon;
        permission: string;
    }[];
    general: {
        name: string;
        url: string;
        icon: LucideIcon;
        permission: string;
    }[];
    systemUser: {
        name: string;
        url: string;
        icon: LucideIcon;
        permission: string;
    }[];
}>();

const { hasPermission, hasAnyPermission } = usePermissions(); // ✅ Safe: called inside setup()
</script>

<template>
    <!-- Dashboard -->
    <SidebarGroup>
        <SidebarGroupLabel>core</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in report" :key="item.name">
                <SidebarMenuButton :tooltip="item.name" as-child>
                    <router-link :to="item.url">
                        <component :is="item.icon" />
                        <span>{{ item.name }}</span>
                    </router-link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>

    <!-- Orders -->
    <SidebarGroup>
        <SidebarGroupLabel>Orders</SidebarGroupLabel>
        <SidebarMenu v-if="hasAnyPermission(['view-order'])">
            <SidebarMenuItem v-for="item in order" :key="item.name">
                <template v-if="hasPermission(item.permission)">
                    <SidebarMenuButton :tooltip="item.name" as-child>
                        <router-link :to="item.url">
                            <component :is="item.icon" />
                            <span>{{ item.name }}</span>
                        </router-link>
                    </SidebarMenuButton>
                </template>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
    <SidebarGroup>
        <SidebarGroupLabel>Report</SidebarGroupLabel>
        <!-- Report (Collapsible) -->
        <SidebarMenu v-if="hasPermission('view-report')">
            <Collapsible
                v-for="item in items"
                :key="item.title"
                as-child
                :default-open="item.isActive"
                class="group/collapsible"
            >
                <SidebarMenuItem>
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton :tooltip="item.title">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                            <ChevronRight
                                class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                            />
                        </SidebarMenuButton>
                    </CollapsibleTrigger>

                    <CollapsibleContent>
                        <SidebarMenuSub>
                            <SidebarMenuSubItem
                                v-for="subItem in item.items"
                                :key="subItem.title"
                            >
                                <SidebarMenuSubButton as-child>
                                    <a :href="subItem.url">
                                        <span>{{ subItem.title }}</span>
                                    </a>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                        </SidebarMenuSub>
                    </CollapsibleContent>
                </SidebarMenuItem>
            </Collapsible>
        </SidebarMenu>
    </SidebarGroup>
    <SidebarGroup>
        <!-- General Settings -->
        <SidebarGroupLabel>Settings</SidebarGroupLabel>

        <!-- <SidebarMenu
            v-if="
                hasAnyPermission([
                    'view-expense-type',
                    'view-employee',
                    'load-type',
                ])
            "
        > -->
        <SidebarMenu>
            <SidebarMenuItem v-for="item in general" :key="item.name">
                <template v-if="hasPermission(item.permission)">
                    <SidebarMenuButton :tooltip="item.name" as-child>
                        <router-link :to="item.url">
                            <component :is="item.icon" />
                            <span>{{ item.name }}</span>
                        </router-link>
                    </SidebarMenuButton>
                </template>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
    <SidebarGroup>
        <!-- User Management -->
        <SidebarGroupLabel>User Management</SidebarGroupLabel>

        <SidebarMenu
            v-if="
                hasAnyPermission([
                    'view-client',
                    'view-system-user',
                    'view-role',
                ])
            "
        >
            <SidebarMenuItem v-for="item in systemUser" :key="item.name">
                <template v-if="hasPermission(item.permission)">
                    <SidebarMenuButton :tooltip="item.name" as-child>
                        <router-link :to="item.url">
                            <component :is="item.icon" />
                            <span>{{ item.name }}</span>
                        </router-link>
                    </SidebarMenuButton>
                </template>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
