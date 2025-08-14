const apiEndPoints = {
  login: "/login",
  logout: "/logout",
  me: "/user",

  dashboard: "/dashboard",

  // Users
  users: "/users",
  user: "/user",
  register: "/register",
  showUser: (id: any) => `/users/${id}`,
  updateUser: (id: any) => `/users/${id}`,
  deleteUser: (id: any) => `/users/${id}`,
  bulkDeleteUsers: "/users/bulk",

  // Banks
  banks: "/banks",
  createBank: "/banks",
  searchBanks: "/banks/search",
  updateBank: (id: any) => `/banks/${id}`,
  deleteBank: (id: any) => `/banks/${id}`,

  // Bank Accounts
  bankAccounts: (bankId: any) => `/bank-accounts/${bankId}`,
  createBankAccount: "/bank-accounts",
  searchBankAccount: (id: any) => `/bank-accounts/search/${id}`,
  updateBankAccount: (id: any) => `/bank-accounts/${id}`,
  deleteBankAccount: (id: any) => `/bank-accounts/${id}`,

  // Vehicles
  vehicles: "/vehicles",
  createVehicle: "/vehicles",
  vehicleDetail: (id: any) => `/vehicles/${id}`,
  searchVehicle: "/vehicles/search",
  updateVehicle: (id: any) => `/vehicles/${id}`,
  deleteVehicle: (id: any) => `/vehicles/${id}`,

  // Clients
  clients: "/clients",
  createClient: "/clients",
  searchClient: "/clients/search",
  updateClient: (id: any) => `/clients/${id}`,
  deleteClient: (id: any) => `/clients/${id}`,

  // Employees
  employees: "/employees",
  createEmployee: "/employees",
  updateEmployee: (id: any) => `/employees/${id}`,
  deleteEmployee: (id: any) => `/employees/${id}`,

  // Expense Types
  expenseTypes: "/expense-types",
  createExpenseType: "/expense-types",
  searchExpenseType: "/expense-types/search",
  updateExpenseType: (id: any) => `/expense-types/${id}`,
  deleteExpenseType: (id: any) => `/expense-types/${id}`,

  // Expenses
  expenses: "/expenses",
  createExpense: "/expenses",
  searchExpense: "/expenses/search",
  updateExpense: (id: any) => `/expenses/${id}`,
  deleteExpense: (id: any) => `/expenses/${id}`,

  // Load Types
  loadTypes: "/load-types",
  createLoadType: "/load-types",
  searchLoadType: "/load-types/search",
  showLoadType: (id: any) => `/load-types/${id}`,
  updateLoadType: (id: any) => `/load-types/${id}`,
  deleteLoadType: (id: any) => `/load-types/${id}`,

  // Locations
  locations: "/locations",
  createLocation: "/locations",
  searchLocation: "/locations/search",
  showLocation: (id: any) => `/locations/${id}`,
  updateLocation: (id: any) => `/locations/${id}`,
  deleteLocation: (id: any) => `/locations/${id}`,

  // Orders
  orders: "/orders",
  createOrder: "/orders",
  searchOrder: "/orders/search",
  showOrder: (id: any) => `/orders/${id}`,
  updateOrder: (id: any) => `/orders/${id}`,
  deleteOrder: (id: any) => `/orders/${id}`,

  // Incomes
  incomes: "/incomes",
  createIncome: "/incomes",
  searchIncome: "/incomes/search",
  showIncome: (id: any) => `/incomes/${id}`,
  updateIncome: (id: any) => `/incomes/${id}`,
  deleteIncome: (id: any) => `/incomes/${id}`,

  // Logs
  logs: "/logs",
  showLog: (logId: any) => `/logs/${logId}`,

  // Roles
  roles: "/roles",
  createRole: "/roles",
  searchRole: "/roles/search",
  showRole: (id: any) => `/roles/${id}`,
  updateRole: (id: any) => `/roles/${id}`,
  deleteRole: (id: any) => `/roles/${id}`,
  updateRolePermissions: (id: any) => `/roles/${id}/permissions`,
  getRolePermissions: (id: any) => `/roles/${id}/permissions`,

  // Permissions
  permissions: "/permissions",
  createPermission: "/permissions",
  showPermission: (role: any) => `/permissions/${role}`,
  searchPermission: "/permissions/search",
  updatePermission: (role: any) => `/permissions/${role}`,
  deletePermission: (role: any) => `/permissions/${role}`,

  // Reports
  reports: "/reports",
  getOwnReport: "/reports/own-summary",
  getIncomeReport: "/reports/income",
  getExpenseReport: "/reports/expense",
  getOrderReport: "/reports/order",
  getVehicleReport: "/reports/vehicle",
  getEmployeeReport: "/reports/employee",
};

export default apiEndPoints;

const resultPerPage = {
  default: 10,
  options: [10, 25, 50],
};
export { apiEndPoints, resultPerPage };
