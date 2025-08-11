<template>
  <Card class="overflow-hidiven">
    <CardHeader class="flex flex-row items-start">
      <div class="flex items-start gap-2">
        <div class="flex flex-col justify-around">
          <div>
            <CardTitle class="flex items-center gap-2 group">
              #Order{{ order.id || "N/A" }}
            </CardTitle>
          </div>
        </div>
      </div>
      
    </CardHeader>

    <CardContent class="space-y-4 text-sm">
      <div class="grid gap-5">
        <div class="font-semibold">Order Information</div>
        <dl class="grid gap-3">
          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Order:</dt>
            <dd>{{ orders.name || `Order#${order.id}` }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Client Name:</dt>
            <dd>{{ order.client?.name || "N/A" }}</dd>
          </div>

         

          
        </dl>

       
      </div>
    </CardContent>
    <CardFooter
      class="flex flex-row items-center justify-between px-6 py-3 border-t bg-muted/50"
    >
      <div class="text-sm text-muted-foreground">
        Last updated: {{ formatDate(order.updated_at) }}
      </div>
    </CardFooter>
  </Card>
</template>

<script >
import {
  Button,
  Card,
  CardContent,
  CardFooter,
  CardHeader,
  CardTitle,
  Checkbox,
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
  Label,
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
  Separator,
} from "@/components/ui";
import {
  ArrowUpRight,
  Baby,
  BabyIcon,
  Badge,
  Check,
  ChevronDown,
  ExternalLink,
  IdCard,
  IdCardIcon,
  Mail,
  MailIcon,
  MoreVertical,
  Phone,
  PhoneCall,
  PlusCircle,
  UserCog,
} from "lucide-vue-next";
import { usePermissions } from "@/hooks/usePermissions";
import { computed, onMounted, ref } from "vue";
import { RouterLink } from "vue-router";
import { DropdownAction } from "@/components/table";
import { item } from "@unovis/ts/components/bullet-legend/style";
import Expenses from "./Expenses.vue";

export default {
  components: {
    ArrowUpRight,
    Baby,
    BabyIcon,
    Check,
    ChevronDown,
    ExternalLink,
    IdCard,
    IdCardIcon,
    Mail,
    MailIcon,
    MoreVertical,
    Phone,
    PhoneCall,
    PlusCircle,
    UserCog,
    ArrowUpRight,
    Baby,
    BabyIcon,
    Check,
    ChevronDown,
    ExternalLink,
    IdCard,
    IdCardIcon,
    Mail,
    MailIcon,
    MoreVertical,
    Phone,
    PhoneCall,
    PlusCircle,
    UserCog,
    Button,
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
    Checkbox,
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
    Label,
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
    Separator,
    DropdownAction,
    Badge,
  },

  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  methods: {
    
    formatPhoneNumber(phone) {
      if (!phone) return "";
      // Remove all non-digit characters
      const cleaned = phone.replace(/\D/g, "");
      // Add international prefix if it's not there (assuming Ethiopian numbers)
      return cleaned.startsWith("0") ? `+251${cleaned.substring(1)}` : cleaned;
    },

    requestPayment(method) {
      // This is now handled by the direct links, but kept for any other logic
      console.log(
        `Requesting payment via ${method} for order ${this.order.id}`
      );
      this.$emit("request-payment", {
        orderId: this.order.id,
        method: method,
      });
    },
  },
  data() {
    return {
      vehicle: [],
     };
  },
};
</script>
