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
      <div class="flex items-center gap-1 ml-auto">
        <DropdownAction
          :item="order"
          :isEdit="false"
          :isDelete="true"
          :isShow="false"
          @delete="confirmDelete"
        />
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

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Plate Number:</dt>
            <dd>{{ order.vehicle?.plate_number || "N/A" }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Driver Name:</dt>
            <dd>
              {{
                order.employee?.first_name + " " + order.employee?.last_name ||
                "N/A"
              }}
            </dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Driver Phone:</dt>
            <dd>{{ order.employee?.phone || "N/A" }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Owner:</dt>
            <dd>{{ order.vehicle?.owner_type || "N/A" }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Loading Place:</dt>
            <dd>{{ order.loading_location?.location_name || "N/A" }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Destination:</dt>
            <dd>{{ order.destination_location?.location_name || "N/A" }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Load Type:</dt>
            <dd>{{ order.load_type?.name || "N/A" }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Quintal:</dt>
            <dd>{{ order.quintal || "N/A" }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Given Tariff:</dt>
            <dd>{{ formatCurrency(order.given_tariff) }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Sub-Tariff:</dt>
            <dd>{{ formatCurrency(order.sub_tariff) }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Total Revenue:</dt>
            <dd>
              {{
                formatCurrency((order.quintal || 0) * (order.given_tariff || 0))
              }}
            </dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Revenue:</dt>
            <dd>
              {{
                formatCurrency(
                  (order.quintal || 0) * (order.given_tariff || 0) -
                    (order.quintal || 0) * (order.sub_tariff || 0)
                )
              }}
            </dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">To Be Paid:</dt>
            <dd>
              {{
                formatCurrency((order.quintal || 0) * (order.sub_tariff || 0))
              }}
            </dd>
          </div>
          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Arrival Date:</dt>
            <dd>{{ formatDate(order.arrival_at_loading_site) }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Loading Date:</dt>
            <dd>{{ formatDate(order.loading_date) }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-muted-foreground">Current Condition:</dt>
            <dd>{{ order.current_condition || "N/A" }}</dd>
          </div>

          <div class="flex items-center justify-between">
            <dt class="text-gray-600">Payment Status:</dt>
            <dd>
              <div
                v-if="order.payment_collected"
                class="flex items-center gap-2"
              >
                <span variant="success" class="text-sm"> Collected </span>
              </div>
              <div v-else class="flex items-center gap-2">
                <span variant="warning" class="text-sm"> Pending </span>
                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="outline" size="sm" class="h-8">
                      Request
                      <ChevronDown class="ml-1 h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem as-child>
                      <a
                        v-if="order.client?.phone"
                        :href="`sms:${formatPhoneNumber(order.client.phone)}`"
                        class="flex w-full items-center"
                      >
                        <Phone class="mr-2 h-4 w-4" />
                        Request via SMS
                      </a>
                      <span
                        v-else
                        class="flex w-full items-center text-muted-foreground"
                      >
                        <Phone class="mr-2 h-4 w-4" />
                        No phone available
                      </span>
                    </DropdownMenuItem>
                    <DropdownMenuItem as-child>
                      <a
                        v-if="order.client?.phone"
                        :href="`tel:${formatPhoneNumber(order.client.phone)}`"
                        class="flex w-full items-center"
                      >
                        <PhoneCall class="mr-2 h-4 w-4" />
                        Request via Call
                      </a>
                      <span
                        v-else
                        class="flex w-full items-center text-muted-foreground"
                      >
                        <PhoneCall class="mr-2 h-4 w-4" />
                        No phone available
                      </span>
                    </DropdownMenuItem>
                    <DropdownMenuItem v-if="order.client?.email" as-child>
                      <a
                        :href="`mailto:${order.client.email}?subject=Payment Request for Order #${order.id}`"
                        class="flex w-full items-center"
                      >
                        <Mail class="mr-2 h-4 w-4" />
                        Request via Email
                      </a>
                    </DropdownMenuItem>
                    <DropdownMenuItem v-else class="text-muted-foreground">
                      <Mail class="mr-2 h-4 w-4" />
                      No email available
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
            </dd>
          </div>
        </dl>

        <!-- <div class="font-semibold">Foreman Information</div>
        <div class="flex items-start gap-4">
          <div>
            <RouterLink
              v-if="order.foremanId"
              class="flex items-center hover:underline group"
              :to="`/users/${order.foremanId}`"
            >
              <p class="font-medium capitalize">
                {{ order.foremanName || "N/A" }}
              </p>
              <ExternalLink
                class="h-4 ml-0.5 opacity-0 group-hover:opacity-100"
              />
            </RouterLink>
            <p v-else class="text-muted-foreground">No foreman assigned</p>
            <p class="text-sm text-muted-foreground">
              {{ order.foremanPhone || "" }}
            </p>
          </div>
        </div> -->
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
    confirmDelete() {
      this.$emit("delete", this.order);
    },

    formatCurrency(value) {
      if (!value) return "N/A";
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "ETB",
      }).format(value);
    },

    formatDate(dateString) {
      if (!dateString) return "N/A";
      const options = { year: "numeric", month: "short", day: "numeric" };
      return new Date(dateString).toLocaleDateString("en-US", options);
    },

    getPaymentStatusVariant(status) {
      switch (status?.toLowerCase()) {
        case "COMPLETED":
          return "success";
        case "PENDING":
          return "warning";
        case "IN_PROGRESS":
          return "destructive";
        default:
          return "outline";
      }
    },
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
      orders: [],
    };
  },
};
</script>

<style scoped>
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  padiving: 20px;
  border-radius: 8px;
  width: 400px;
}
</style>