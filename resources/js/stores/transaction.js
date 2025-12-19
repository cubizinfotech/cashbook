import { defineStore } from 'pinia';
import axios from 'axios';

export const useTransactionStore = defineStore('transaction', {
  state: () => ({
    transactions: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchTransactions(params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/api/transactions', { params });
        this.transactions = response.data.data;
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch transactions';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async createTransaction(data) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.post('/api/transactions', data);
        return response.data.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create transaction';
        if (error.response?.data?.errors) {
          throw error.response.data.errors;
        }
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateTransaction(id, data) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.put(`/api/transactions/${id}`, data);
        return response.data.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update transaction';
        if (error.response?.data?.errors) {
          throw error.response.data.errors;
        }
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteTransaction(id) {
      this.loading = true;
      this.error = null;
      try {
        await axios.delete(`/api/transactions/${id}`);
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete transaction';
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});


