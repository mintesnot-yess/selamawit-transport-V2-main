import AppLayout from "@/components/layouts/AppLayout.vue";
import { AUTH_TOKEN_KEY } from "@/key";
import auth from "@/stores/auth";
import { useProfileStore } from "@/stores/profile";
import LoginView from "@/views/auth/LoginView.vue";
import { BankView } from "@/views/bank";
import AccountsViews from "@/views/bank/account/AccountsViews.vue";
import { ClientsViews } from "@/views/client";
import DashboardView from "@/views/dashboard/DashboardView.vue";
import { EmployeeViews } from "@/views/employee";
import { ExpensesViews } from "@/views/expense";
import { ExpensesTypeViews } from "@/views/expenseType";
import LoadTypeViews from "@/views/loadType/LoadTypeViews.vue";
import LocationViews from "@/views/location/LocationViews.vue";
import LogsViews from "@/views/logs/LogsViews.vue";
import { OrderView } from "@/views/order";
import { OrderDetail } from "@/views/order/detail";
import OwnSummary from "@/views/report/OwnSummary.vue";
import { RolesViews } from "@/views/rolePermission";
import { UsersViews } from "@/views/user";
import VehicleDetailView from "@/views/vehicle/detail/vehicleDetailView.vue";
import VehicleViews from "@/views/vehicle/VehicleViews.vue";
import { createRouter, createWebHistory, type RouteRecordRaw } from "vue-router";

const routes: RouteRecordRaw[] = [
  {
    path: "/",
    redirect: "/dashboard",
    component: AppLayout,
    children: [
      {
        path: "/dashboard",
        name: "Dashboard",
        component: DashboardView,
        meta: {
          title: "Dashboard",
          requiresAuth: true

        },
      },
      {
        path: "/orders",
        name: "Orders",
        component: OrderView,
        meta: {
          title: "Orders",
          requiresAuth: true
        },
      },

      {
        path: "/order/detail/:id",
        name: "OrderDetail",
        component: OrderDetail,
        meta: {
          title: "Order Detail",
          requiresAuth: true
        },
      },
      {
        path: "/banks",
        name: "Banks",
        component: BankView,
        meta: {
          title: "Banks",
          requiresAuth: true
        },
      },
      {
        path: "/banks/accounts/:id",
        name: "BankAccounts",
        component: AccountsViews,
        meta: {
          title: "Bank Accounts",
          requiresAuth: true
        },
      },
      {
        path: "/expenses",
        name: "Expenses",
        component: ExpensesViews,
        meta: {
          title: "Expenses",
          requiresAuth: true
        },
      },
      {
        path: "/expense-type",
        name: "ExpensesType",
        component: ExpensesTypeViews,
        meta: {
          title: "Expenses Type",
          requiresAuth: true
        },
      },
      {
        path: "/vehicles",
        name: "Vehicles",
        component: VehicleViews,
        meta: {
          title: "Vehicles",
          requiresAuth: true
        },
      },

      {
        path: "/vehicle/:id",
        name: "VehiclesDetail",
        component: VehicleDetailView,
        meta: {
          title: "Vehicles Detail",
          requiresAuth: true
        },
      },
      {
        path: "/employees",
        name: "Employees",
        component: EmployeeViews,
        meta: {
          title: "Employees",
          requiresAuth: true
        },
      },
      {
        path: "/locations",
        name: "Locations",
        component: LocationViews,
        meta: {
          title: "Locations",
          requiresAuth: true
        },
      },
      {
        path: "/load-type",
        name: "loadType",
        component: LoadTypeViews,
        meta: {
          title: "Load Type",
          requiresAuth: true
        },
      },
      {
        path: "/clients",
        name: "Clients",
        component: ClientsViews,
        meta: {
          title: "Clients",
          requiresAuth: true
        },
      }, {
        path: "/users",
        name: "Users",
        component: UsersViews,
        meta: {
          title: "Users",
          requiresAuth: true
        },
      }, {
        path: "/roles",
        name: "Roles",
        component: RolesViews,
        meta: {
          title: "Roles",
          requiresAuth: true
        },
      },
      {
        path: "/Logs",
        name: "Logs",
        component: LogsViews,
        meta: {
          title: "Logs",
          requiresAuth: true
        },
      },

      {
        path: "/report/own-summary",
        name: "OwnSummary",
        component: OwnSummary,
        meta: {
          title: "Own Summary",
          requiresAuth: true
        },
      },
    ]

  },
  {
    path: "/login",
    component: LoginView,
    children: [
      {
        path: "",
        name: "Login",
        component: LoginView,
        meta: { requiresAuth: false, isGuest: true } // Prevent if authenticated
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

router.beforeEach(async (to, _from, next) => {
  const profileStore = useProfileStore();
  const token = localStorage.getItem(AUTH_TOKEN_KEY);

  if (to.meta.isGuest && token) {
    // If guest-only page and user is already logged in
    return next({ name: "Dashboard" });
  }

  if (to.meta.title) {
    document.title = `${to.meta.title} | Selamawit Transport`;
  } else {
    document.title = `Selamawit Transport`;
  }

  if (to.meta.requiresAuth) {
    if (!token) {
      // Redirect to login if not authenticated
      return next({ name: "Login" });
    }
    try {
      // Fetch user data if not already loaded
      if (!profileStore.user || !profileStore.permissions) {
        await auth.fetchUser();
      }

      const userPermissions = profileStore.permissions || [];

      // Check required permissions
      const requiredPermissions = to.meta.requiredPermissions;

      if (requiredPermissions) {
        // Check if the user has at least one of the required permissions
        const hasPermission = requiredPermissions.some((perm: string) => userPermissions.includes(perm));

        if (!hasPermission) {
          return next({ name: "Dashboard" });
        }
      }

      // Proceed to the route
      return next();

    } catch (error) {
      console.error("Failed to fetch user data:", error);
      return next({ name: "Login" });
    }
  }

  next();
});

export default router;
