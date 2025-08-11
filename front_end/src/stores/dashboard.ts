import apiEndPoints from "@/components/constants";
import axios from "axios";

const useDashboardStore = {
  async getAll(params: any = {}) {
    try {
      const response = await axios.get(
        `${import.meta.env.VITE_API_URL}${apiEndPoints.dashboard}`,
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
          },
          params,
        },
      );

      const { counts, chart, orders, totalIncome, totalExpense } =
        response.data;

      return {
        counts, // users, vehicles, employees, orders
        chart, // months, income, expenses,
        orders,
        totalIncome,
        totalExpense,
      };
    } catch (error) {
      throw this.handleError(error);
    }
  },

  handleError(error: any) {
    if (error.response?.data?.message) {
      return new Error(error.response.data.message);
    }
    return new Error(error.message || "An unknown error occurred");
  },
};

export default useDashboardStore;
