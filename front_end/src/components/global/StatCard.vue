<template>
  <Card>
    <CardContent class="grid gap-2">
      <div class="flex items-center space-x-2 rounded-md p-2">
        <div
          :class="[
            'w-13 h-13 rounded-lg flex items-center justify-center',
            iconBgColor,
          ]"
        >
          <component :is="getIconComponent" :size="25" :color="iconColor" />
        </div>
        <div class="flex-1 flex flex-col gap-1">
          <p class="font-medium text-lg leading-none">
            {{ title }}
          </p>
          <p class="text-sm text-muted-foreground">
            {{ value }}
          </p>
        </div>
        <Switch />
      </div>
      <div></div>
    </CardContent>
  </Card>
</template>

<script setup>
import { computed } from "vue";
import {
  User,
  Truck,
  Users,
  ShoppingCart,
  BarChart2 as ChartNoAxesColumnIcon,
  LineChart as ChartNoAxesCombined,
} from "lucide-vue-next";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui";

const props = defineProps({
  icon: {
    type: String,
    required: true,
    validator: (value) =>
      [
        "User",
        "Truck",
        "Users",
        "ShoppingCart",
        "ChartNoAxesColumnIcon",
        "ChartNoAxesCombined",
      ].includes(value),
  },
  title: {
    type: String,
    required: true,
  },
  value: {
    type: [String, Number],
    required: true,
  },
  color: {
    type: String,
    default: "blue",
  },
});

const iconComponents = {
  User,
  Truck,
  Users,
  ShoppingCart,
  ChartNoAxesColumnIcon,
  ChartNoAxesCombined,
};

const getIconComponent = computed(() => iconComponents[props.icon]);

const colorMap = {
  blue: { bg: "bg-blue-50", icon: "#3b82f6" },
  green: { bg: "bg-green-50", icon: "#10b981" },
  purple: { bg: "bg-purple-50", icon: "#8b5cf6" },
  orange: { bg: "bg-orange-50", icon: "#f97316" },
  red: { bg: "bg-red-50", icon: "#ef4444" },
  teal: { bg: "bg-teal-50", icon: "#14b8a6" },
  amber: { bg: "bg-amber-50", icon: "#f59e0b" },
};

const iconBgColor = computed(
  () => colorMap[props.color]?.bg || colorMap.blue.bg
);
const iconColor = computed(
  () => colorMap[props.color]?.icon || colorMap.blue.icon
);
</script>
