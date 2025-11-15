import { defineStore } from 'pinia';
import axios from 'axios';

export const useCashbookStore = defineStore('cashbook', {
  state: () => ({
    cashbooks: [],
    currentCashbook: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchCashbooks(params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/api/cashbooks', { params });
        this.cashbooks = response.data.data;
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch cashbooks';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchCashbook(id) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(`/api/cashbooks/${id}`);
        this.currentCashbook = response.data.data;
        return response.data.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch cashbook';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async createCashbook(data) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.post('/api/cashbooks', data);
        return response.data.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create cashbook';
        if (error.response?.data?.errors) {
          throw error.response.data.errors;
        }
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateCashbook(id, data) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.put(`/api/cashbooks/${id}`, data);
        return response.data.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update cashbook';
        if (error.response?.data?.errors) {
          throw error.response.data.errors;
        }
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteCashbook(id) {
      this.loading = true;
      this.error = null;
      try {
        await axios.delete(`/api/cashbooks/${id}`);
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete cashbook';
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});

