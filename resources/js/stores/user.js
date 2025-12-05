import { defineStore } from 'pinia'
import axios from 'axios'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,      // Laravel user
    member: null,    // business member with role
    loading: false,
    error: null,
  }),

  getters: {
    businessRoleId: (state) => state.member?.business_role_id || null,

    roleName: (state) => state.member?.role?.name || null
  },

  actions: {
    async fetchCurrentUser() {
      try {
        const { data } = await axios.get('/api/user')

        // Store correctly
        this.user = data.user
        this.member = data.member
        console.log("API returned:", data);

      } catch (error) {
        console.error(error)
      }
    },

    setUser(userData) {
      this.user = userData.user
      this.member = userData.member
    }
  }
})
