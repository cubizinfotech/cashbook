import { defineStore } from 'pinia';
import axios from 'axios';

export const useMemberStore = defineStore('member', {
  state: () => ({
    members: [],
    createMeta: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchMembers(params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/api/members', { params });
        this.members = response.data.data;
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch members';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchCreateData(params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/api/members/create', { params });
        this.createMeta = response.data?.data || response.data || null;
        return this.createMeta;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to load member defaults';
        throw error;
      } finally {
        this.loading = false;
      }
    },
    async fetchMember(id) {
    this.loading = true;
    this.error = null;

    try {
        const response = await axios.get(`/api/members/${id}`);
        console.log("📌 Single member loaded:", response.data.data);
        return response.data.data;    // return one member
    } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch member';
        throw error;
    } finally {
        this.loading = false;
    }
    },

    async createMember(data) {
      this.loading = true;
      this.error = null;
      try {
        const config = {
          headers: {
            'Content-Type': data instanceof FormData ? 'multipart/form-data' : 'application/json',
          },
        };
        const response = await axios.post('/api/members', data, config);
        return response.data.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create member';
        if (error.response?.data?.errors) {
          throw error.response.data.errors;
        }
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateMember(id, data) {
      this.loading = true;
      this.error = null;
      try {
        const config = {
          headers: {
            'Content-Type': data instanceof FormData ? 'multipart/form-data' : 'application/json',
          },
        };
        const response = await axios.post(`/api/members/${id}`, data, {
          ...config,
          params: { _method: 'PUT' },
        });
        return response.data.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update member';
        if (error.response?.data?.errors) {
          throw error.response.data.errors;
        }
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteMember(id) {
      this.loading = true;
      this.error = null;
      try {
        await axios.delete(`/api/members/${id}`);
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete member';
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});

