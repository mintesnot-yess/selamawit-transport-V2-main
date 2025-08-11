<script setup lang="ts">
import type { SidebarProps } from "@/components/ui/sidebar";

// Icons from lucide-vue-next
import {
    ClipboardList,
    Handshake,
    Landmark,
    Layers2,
    LayoutDashboard,
    MapPin,
    ShoppingCart,
    Truck,
    TruckElectricIcon,
    UserLock,
    Users,
    Wallet,
    WalletMinimal,
} from "lucide-vue-next";

// Navigation components
import { NavProjects, NavUser, TeamSwitcher } from "./";

// UI components
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarRail,
} from "@/components/ui";

// Props definition with default values
const props = withDefaults(defineProps<SidebarProps>(), {
    collapsible: "icon",
});

// Sidebar static data (mock)
const data = {
    user: {
        name: "Minte",
        email: "minte@gmail.com",
        avatar: "/avatars/shadcn.jpg",
    },
    teams: [
        {
            name: "Selamawit",
            logo: TruckElectricIcon,
            plan: "Transport",
        },
    ],
    navMain: [
        {
            title: "Report",
            url: "#",
            icon: ClipboardList,
            items: [
                { title: "Own Summary", url: "#", permission: "read-report" },
                {
                    title: "Uncollected Payments",
                    url: "#",
                    permission: "read-report",
                },
                { title: "Quantum", url: "#", permission: "read-report" },
            ],
        },
    ],
    projects: [
        {
            name: "Dashboard",
            url: "/",
            icon: LayoutDashboard,
            permission: "",
        },
    ],
    orders: [
        {
            name: "Orders",
            url: "/orders",
            icon: ShoppingCart,
            permission: "view-order",
        },
        {
            name: "Expenses",
            url: "/expenses",
            icon: Wallet,
            permission: "view-expense",
        },
    ],
    general: [
        {
            name: "Expense Type",
            url: "/expense-type",
            icon: WalletMinimal,
            permission: "view-expense-type",
        },
        {
            name: "Vehicle",
            url: "/vehicles",
            icon: Truck,
            permission: "view-vehicle",
        },
        {
            name: "Employees",
            url: "/employees",
            icon: Users,
            permission: "view-employee",
        },
        {
            name: "Locations",
            url: "/locations",
            icon: MapPin,
            permission: "view-location",
        },
        {
            name: "Load Type",
            url: "/load-type",
            icon: Layers2,
            permission: "view-load-type",
        },
        {
            name: "Bank",
            url: "/banks",
            icon: Landmark,
            permission: "view-bank",
        },
    ],
    systemUser: [
        {
            name: "Clients",
            url: "/clients",
            icon: Handshake,
            permission: "view-client",
        },
        {
            name: "System Users",
            url: "/users",
            icon: UserLock,
            permission: "view-system-user",
        },
        {
            name: "Roles",
            url: "/roles",
            icon: UserLock,
            permission: "view-role",
        },
    ],
};
</script>

<template>
    <Sidebar v-bind="props">
        <!-- Header -->
        <SidebarHeader>
            <TeamSwitcher :teams="data.teams" />
        </SidebarHeader>

        <!-- Main Content -->
        <SidebarContent>
            <NavProjects
                :report="data.projects"
                :items="data.navMain"
                :order="data.orders"
                :general="data.general"
                :systemUser="data.systemUser"
            />
        </SidebarContent>

        <!-- Footer -->
        <SidebarFooter>
            <NavUser :user="data.user" />
        </SidebarFooter>

        <!-- Rail for hover expand/collapse -->
        <SidebarRail />
    </Sidebar>
</template>

<style scoped>
*::-webkit-scrollbar {
    display: none;
}
</style>
