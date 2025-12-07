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
        <li>
          <div class="flex items-center">
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Businesses</span>
          </div>
        </li>
      </ol>
    </nav>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Businesses</h1>
        <p class="text-gray-600 mt-2">Manage your businesses and their details</p>
      </div>
      <button
        @click="showForm = true"
        class="btn-primary whitespace-nowrap"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <span>Add Business</span>
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="card p-4 mb-6">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <input
            v-model="searchQuery"
            @input="handleSearch"
            type="text"
            placeholder="Search businesses by name, email, or phone..."
            class="input-field"
          />
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="businessStore.loading" class="card p-12 text-center">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
      <p class="text-gray-500 mt-4">Loading businesses...</p>
    </div>

    <!-- Error State -->


    <!-- Businesses List -->
    <div v-else-if="businesses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="business in businesses"
        :key="business.id"
        class="card hover:shadow-md transition-shadow"
      >
       <!-- {{ memberStore }}

       {{ roles }} -->
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1 min-w-0">
              <router-link
                :to="`/businesses/${business.id}`"
                class="text-xl font-semibold text-gray-900 hover:text-sky-600 transition-colors block truncate"
              >
                {{ business.name }} <span class="text-sm font-semibold">({{ business.business_role }})</span>
              </router-link>
              <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ business.description || 'No description' }}</p>
            </div>

            <span
              :class="{
                'bg-emerald-100 text-emerald-800': business.status == 'active',
                'bg-red-100 text-red-800': business.status == 'inactive',
                'bg-yellow-100 text-yellow-800': business.status == 'pending',
                'bg-gray-100 text-gray-800': business.status == 'suspended'
              }"
              class="px-2.5 py-1 text-xs font-medium rounded-full whitespace-nowrap ml-2 flex-shrink-0"
            >
              {{ business.status }}
            </span>
          </div>

          <div class="space-y-2 mb-4">
            <div v-if="business.email" class="flex items-center text-sm text-gray-600">
              <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span class="truncate">{{ business.email }}</span>
            </div>
            <div v-if="business.phone" class="flex items-center text-sm text-gray-600">
              <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <span class="truncate">{{ business.phone }}</span>
            </div>
          </div>

          <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-500">
              <span>{{ business.total_members }} members</span>
              <span> • </span>
              <span>{{ business.total_cashbooks }} cashbooks</span>
            </div>
            <div class="flex space-x-3">
              <button
                @click="editBusiness(business)"
                class="text-sky-600 hover:text-sky-800 text-sm font-medium transition-colors"
              >
                Edit
              </button>
              <button
                @click="confirmDelete(business)"
                class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="card p-12 text-center">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No businesses</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by creating a new business.</p>
      <div class="mt-6">
        <button
          @click="showForm = true"
          class="btn-primary"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Business
        </button>
      </div>
    </div>

    <!-- Business Form Modal -->
    <BusinessForm
      v-if="showForm"
      :business="editingBusiness"
      @close="closeForm"
      @saved="handleSaved"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useBusinessStore } from '../stores/business';
import BusinessForm from '../components/BusinessForm.vue';
import { useMemberStore } from '../stores/member';
const memberStore = useMemberStore();
const roles = ref([]);
const businessStore = useBusinessStore();
const showForm = ref(false);
const editingBusiness = ref(null);
const searchQuery = ref('');
const statusFilter = ref('');
let searchTimeout = null;
const loadRoles = async () => {
  try {
    const response = await axios.get('/api/business-roles');
    roles.value = response.data.data || [];
    console.log("Loaded roles:", roles.value);
  } catch (error) {
    console.error('Failed to load roles', error);
  }
};

const businesses = computed(() => businessStore.businesses);
console.log(businesses);

onMounted(async () => {
  await businessStore.fetchBusinesses();
});

const handleSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(async () => {
    await businessStore.fetchBusinesses({
      search: searchQuery.value,
      status: statusFilter.value,
    });
  }, 500);
};

const editBusiness = (business) => {
  editingBusiness.value = business;
  showForm.value = true;
};

const confirmDelete = async (business) => {
  if (confirm(`Are you sure you want to delete "${business.name}"? This action cannot be undone.`)) {
    try {
      await businessStore.deleteBusiness(business.id);
      await businessStore.fetchBusinesses({
        search: searchQuery.value,
        status: statusFilter.value,
      });
    } catch (error) {
      alert('Failed to delete business. Please try again.');
    }
  }
};

const closeForm = () => {
  showForm.value = false;
  editingBusiness.value = null;
};

const handleSaved = async () => {
  closeForm();
  await businessStore.fetchBusinesses({
    search: searchQuery.value,
    status: statusFilter.value,
  });
};
</script>
