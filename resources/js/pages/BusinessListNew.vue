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

    <!-- DataTable -->
    <DataTable
      :columns="columns"
      :data="businesses"
      :loading="businessStore.loading"
      :pagination="pagination"
      :search="search"
      :status-filter="statusFilter"
      :per-page="perPage"
      :sort-by="sortBy"
      :sort-order="sortOrder"
      show-status-filter
      search-placeholder="Search businesses by name, email, or phone..."
      :actions="{ view: true, edit: true, delete: true }"
      @update:search="search = $event"
      @update:status-filter="statusFilter = $event"
      @update:per-page="perPage = $event"
      @update:sort-by="sortBy = $event"
      @update:sort-order="sortOrder = $event"
      @page-change="handlePageChange"
      @view="viewBusiness"
      @edit="editBusiness"
      @delete="confirmDelete"
    >
      <template #cell-status="{ value }">
        <span
          :class="{
            'bg-emerald-100 text-emerald-800': value === 'active',
            'bg-red-100 text-red-800': value === 'inactive',
            'bg-yellow-100 text-yellow-800': value === 'pending',
            'bg-gray-100 text-gray-800': value === 'suspended'
          }"
          class="px-2.5 py-1 text-xs font-medium rounded-full"
        >
          {{ value }}
        </span>
      </template>
    </DataTable>

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
import { ref, computed, onMounted, watch } from 'vue';
import { useBusinessStore } from '../stores/business';
import BusinessForm from '../components/BusinessForm.vue';
import DataTable from '../components/DataTable.vue';

const businessStore = useBusinessStore();
const showForm = ref(false);
const editingBusiness = ref(null);
const search = ref('');
const statusFilter = ref('');
const perPage = ref(10);
const sortBy = ref('');
const sortOrder = ref('asc');
const pagination = ref(null);

const businesses = computed(() => businessStore.businesses || []);

const columns = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'phone', label: 'Phone', sortable: false },
  { key: 'status', label: 'Status', sortable: true, format: 'status' },
  { key: 'created_at', label: 'Created', sortable: true, format: 'date' },
];

onMounted(async () => {
  await loadBusinesses();
});

watch([search, statusFilter, perPage, sortBy, sortOrder], () => {
  loadBusinesses();
}, { deep: true });

const loadBusinesses = async () => {
  try {
    const params = {
      search: search.value,
      status: statusFilter.value,
      per_page: perPage.value,
      sort_by: sortBy.value,
      sort_order: sortOrder.value,
    };
    
    const response = await businessStore.fetchBusinesses(params);
    pagination.value = {
      current_page: response.current_page || 1,
      from: response.from || 0,
      to: response.to || 0,
      total: response.total || 0,
      last_page: response.last_page || 1,
      prev_page_url: response.prev_page_url,
      next_page_url: response.next_page_url,
    };
  } catch (error) {
    console.error('Failed to load businesses', error);
  }
};

const handlePageChange = (page) => {
  loadBusinesses();
};

const viewBusiness = (business) => {
  // Navigate to business view page
  window.location.href = `/businesses/${business.id}`;
};

const editBusiness = (business) => {
  editingBusiness.value = business;
  showForm.value = true;
};

const confirmDelete = async (business) => {
  if (confirm(`Are you sure you want to delete "${business.name}"? This action cannot be undone.`)) {
    try {
      await businessStore.deleteBusiness(business.id);
      await loadBusinesses();
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
  await loadBusinesses();
};
</script>

