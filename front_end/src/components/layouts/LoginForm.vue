<script  setup lang="ts">
import { cn } from "@/lib/utils";
import { Button, Input, Label } from "@/components/ui";
import auth from "@/stores/auth";
</script>

<template>
  <form @submit.prevent="handleSubmit" :class="cn('flex flex-col gap-6')">
    <div class="flex flex-col gap-2">
      <h1 class="text-3xl font-bold">Login to your account</h1>
      <p class="text-balance text-sm text-muted-foreground">
        Enter your email below to login to your account
      </p>
    </div>
    <div class="grid gap-6">
      <div class="grid gap-2">
        <Label for="email">Email</Label>
        <Input
          id="email"
          type="email"
          v-model="form.email"
          placeholder="m@example.com"
          required
        />
      </div>
      <div class="grid gap-2">
        <div class="flex items-center">
          <Label for="password">Password</Label>
          <a
            href="#"
            class="ml-auto text-sm underline-offset-4 hover:underline"
          >
            Forgot your password?
          </a>
        </div>
        <Input
          id="password"
          type="password"
          v-model="form.password"
          placeholder="Password"
          required
        />
      </div>
      <template v-if="error">
        <span class="text-red-600 text-sm">
          {{ error }}
        </span>
      </template>

      <Button
        class="flex items-center justify-center h-10 w-full"
        v-if="loading"
      >
        <svg
          class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        Log In...
      </Button>

      <Button class="h-10 flex items-center content-center" v-else>
        <span>Log In</span>
      </Button>

      <div
        class="relative text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t after:border-border"
      ></div>
    </div>
  </form>
</template>
<script lang="ts">
export default {
  data() {
    return {
      form: {
        email: "minte@gmail.com",
        password: "@Mntman40",
      },
      loading: false,
      error: null,
    };
  },
  methods: {
    async handleSubmit() {
      this.loading = true;
      this.error = null;

      try {
        const { access_token } = await auth.login(this.form);
        auth.setAuthToken(access_token);
        await auth.fetchUser();
        window.location.href = "/";
      } catch (error: any) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>