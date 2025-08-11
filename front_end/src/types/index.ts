import type { JSX } from "vue/jsx-runtime"

export   interface Order {
  id: string
  amount: number
  order_name:string
  status: 'pending' | 'processing' | 'success' | 'failed'
  email: string,
  password:string,
}
export   interface Bank {
  id: number
  name: string
}export   interface BankAccount {
  id: number
  account_number: number
  bank_id:Partial<Bank['id']>
}

export  interface Expense {
  id: string
  amount: number
  status: 'pending' | 'processing' | 'success' | 'failed'
  from: string,
  to: string,
 }
 
export type User = {
  id?: number | null;
  email: string;
  name: string;
  phone: string;
  role_obj: Partial<Role>;
  user_permissions: number[];
  created_date?: string;
  updated_date?: string;
  updated_by_user?: Partial<User>;
   
};
export interface Payment {
  id: string;
  amount: number;
  status: "pending" | "processing" | "success" | "failed";
  email: string;
 }

export type Token = {
  access: string | null;
 };

 export interface Column {
  name: string;
  label: string;
  sortable?: boolean;
  render: (item: any) => string | JSX.Element;
  width?: string;
}

// types/index.ts
export interface Permission {
  id: string
  name: string
}

export interface PermissionGroup {
  name: string
  permissions: Permission[]
}

export interface Role {
  id?: string
  name: string
  permissions: string[]
  is_protected?: boolean
}