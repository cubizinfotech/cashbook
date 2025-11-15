<template>
  <div>
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
      <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
          <router-link to="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-sky-600">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
            </svg>
            Dashboard
          </router-link>
        </li>
      </ol>
    </nav>

    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
      <p class="text-gray-600 mt-2">Welcome to your Cash Book management system</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="card p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Total Businesses</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ stats.totalBusinesses }}</p>
          </div>
          <div class="bg-sky-100 rounded-full p-3">
            <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
        </div>
      </div>

      <div class="card p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Total Members</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ stats.totalMembers }}</p>
          </div>
          <div class="bg-emerald-100 rounded-full p-3">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="card p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Total Cashbooks</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ stats.totalCashbooks }}</p>
          </div>
          <div class="bg-blue-100 rounded-full p-3">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="card p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Total Transactions</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ stats.totalTransactions }}</p>
          </div>
          <div class="bg-purple-100 rounded-full p-3">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Businesses -->
    <div class="card">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900">Recent Businesses</h2>
          <router-link
            to="/businesses"
            class="text-sky-600 hover:text-sky-800 text-sm font-medium transition-colors"
          >
            View All →
          </router-link>
        </div>
      </div>
      
      <div v-if="loading" class="p-12 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
        <p class="text-gray-500 mt-4">Loading...</p>
      </div>
      
      <div v-else-if="recentBusinesses.length > 0" class="divide-y divide-gray-200">
        <div
          v-for="business in recentBusinesses"
          :key="business.id"
          class="px-6 py-4 hover:bg-gray-50 transition-colors"
        >
          <router-link :to="`/businesses/${business.id}`" class="block">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="font-medium text-gray-900">{{ business.name }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ business.email || 'No email' }}</p>
              </div>
              <div class="flex items-center space-x-3">
                <span
                  :class="{
                    'bg-emerald-100 text-emerald-800': business.status === 'active',
                    'bg-red-100 text-red-800': business.status === 'inactive',
                    'bg-yellow-100 text-yellow-800': business.status === 'pending',
                    'bg-gray-100 text-gray-800': business.status === 'suspended'
                  }"
                  class="px-2.5 py-1 text-xs font-medium rounded-full"
                >
                  {{ business.status }}
                </span>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </div>
            </div>
          </router-link>
        </div>
      </div>
      
      <div v-else class="p-12 text-center text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <p>No businesses yet.</p>
        <router-link to="/businesses" class="text-sky-600 hover:text-sky-800 font-medium mt-2 inline-block">Create your first business</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useBusinessStore } from '../stores/business';
import axios from 'axios';

const businessStore = useBusinessStore();
const loading = ref(true);
const recentBusinesses = ref([]);
const stats = ref({
  totalBusinesses: 0,
  totalMembers: 0,
  totalCashbooks: 0,
  totalTransactions: 0,
});

onMounted(async () => {
  await loadDashboardData();
});

const loadDashboardData = async () => {
  try {
    loading.value = true;
    
    const businessesResponse = await businessStore.fetchBusinesses({ per_page: 5 });
    recentBusinesses.value = businessesResponse.data || [];
    
    const [businesses, members, cashbooks, transactions] = await Promise.all([
      axios.get('/api/businesses', { params: { per_page: 1 } }),
      axios.get('/api/members', { params: { per_page: 1 } }),
      axios.get('/api/cashbooks', { params: { per_page: 1 } }),
      axios.get('/api/transactions', { params: { per_page: 1 } }),
    ]);
    
    stats.value = {
      totalBusinesses: businesses.data.meta?.total || businesses.data.total || 0,
      totalMembers: members.data.meta?.total || members.data.total || 0,
      totalCashbooks: cashbooks.data.meta?.total || cashbooks.data.total || 0,
      totalTransactions: transactions.data.meta?.total || transactions.data.total || 0,
    };
  } catch (error) {
    console.error('Failed to load dashboard data', error);
  } finally {
    loading.value = false;
  }
};
</script>
