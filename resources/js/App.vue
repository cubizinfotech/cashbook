<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed top-0 left-0 z-40 w-64 h-screen transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        'md:translate-x-0'
      ]"
      class="bg-gray-900 text-white"
    >
      <div class="flex items-center justify-between h-16 px-6 border-b border-gray-800">
        <h1 class="text-xl font-bold text-sky-400">Cash Book</h1>
        <button
          @click="sidebarOpen = false"
          class="md:hidden text-gray-400 hover:text-white transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <nav class="mt-6 px-3">
        <router-link
          to="/dashboard"
          :class="[
            'sidebar-link',
            isActive('/dashboard') ? 'sidebar-link-active' : 'sidebar-link-inactive'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          Dashboard
        </router-link>

        <router-link
          to="/businesses"
          :class="[
            'sidebar-link',
            isActive('/businesses') ? 'sidebar-link-active' : 'sidebar-link-inactive'
          ]"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          Businesses
        </router-link>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="md:ml-64">
      <!-- Top Navbar -->
      <nav class="bg-white shadow-sm border-b border-gray-200 h-16 flex items-center justify-between px-4 md:px-6 sticky top-0 z-30">
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="md:hidden text-gray-600 hover:text-gray-900 transition-colors p-2"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <div class="flex-1"></div>

        <div class="flex items-center space-x-4">
      <div class="text-sm text-gray-700 font-medium hidden sm:block">
          {{ user?.user?.name || 'User' }}
          <span class="text-gray-500 ml-1">
            <!-- ({{ user?.member?.role?.name || 'admin' }}) -->
          </span>
        </div>


          <form method="POST" action="/logout" class="inline">
            <input type="hidden" name="_token" :value="csrfToken" />
            <button
              type="submit"
              class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors"
            >
              Logout
            </button>
          </form>
        </div>
      </nav>

      <!-- Page Content -->
      <main class="p-4 md:p-6">
        <router-view />
      </main>
    </div>

    <!-- Sidebar Overlay (Mobile) -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden transition-opacity"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useUserStore } from './stores/user';

const route = useRoute();
const sidebarOpen = ref(false);
const userStore = useUserStore();
const user = computed(() => userStore);

const csrfToken = computed(() => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
});

const isActive = (path) => {
  return route.path === path || route.path.startsWith(path + '/');
};

onMounted(async () => {
  if (!userStore.user) {
    await userStore.fetchCurrentUser();
  }
});
</script>
