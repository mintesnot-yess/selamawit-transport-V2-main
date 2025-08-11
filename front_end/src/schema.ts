// @/schema.ts
import { z } from 'zod';
 
export const roleFormSchema = z.object({
  name: z.string().min(1, 'Role name is required'),
  permissions: z.array(z.string()).default([]),
})