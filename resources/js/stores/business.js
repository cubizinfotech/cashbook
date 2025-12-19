import { defineStore } from 'pinia';
import axios from 'axios';

export const useBusinessStore = defineStore('business', {
  state: () => ({
    businesses: [],
    currentBusiness: null,
    pagination: null,
    createMeta: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchBusinesses(params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/api/businesses', { params });
        this.businesses = response.data.data;
        this.pagination = response.data.meta || null;
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch businesses';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchBusiness(id) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(`/api/businesses/${id}`);
        this.currentBusiness = response.data.data;
        console.log(response.data.data);
        return response.data.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch business';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchCreateData() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/api/businesses/create');
        this.createMeta = response.data?.data || response.data || null;
        return this.createMeta;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load business defaults';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async createBusiness(data) {
      this.loading = true;
      this.error = null;
      try {
        const config = {
          headers: {
            'Content-Type': data instanceof FormData ? 'multipart/form-data' : 'application/json',
          },
        };
        const response = await axios.post('/api/businesses', data, config);
        return response.data.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create business';
        if (error.response?.data?.errors) {
          throw error.response.data.errors;
        }
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateBusiness(id, data) {
      this.loading = true;
      this.error = null;
      try {
        const config = {
          headers: {
            'Content-Type': data instanceof FormData ? 'multipart/form-data' : 'application/json',
          },
        };
        const response = await axios.post(`/api/businesses/${id}`, data, {
          ...config,
          params: { _method: 'PUT' },
        });
        return response.data.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update business';
        if (error.response?.data?.errors) {
          throw error.response.data.errors;
        }
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteBusiness(id) {
      this.loading = true;
      this.error = null;
      try {
        await axios.delete(`/api/businesses/${id}`);
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete business';
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});

