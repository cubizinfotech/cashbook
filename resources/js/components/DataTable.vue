<template>
  <div class="card">
    <!-- Search and Filters -->
    <div class="p-4 border-b border-gray-200">
      <div class="flex flex-col md:flex-row gap-4 mb-4">
        <!-- Search -->
        <div class="flex-1">
          <input
            v-model="localSearch"
            @input="handleSearch"
            type="text"
            :placeholder="searchPlaceholder"
            class="input-field"
          />
        </div>
        
        <!-- Status Filter -->
        <div v-if="showStatusFilter" class="md:w-48">
          <SearchableSelect
            v-model="localStatusFilter"
            :options="statusOptions"
            placeholder="All Status"
            track-by="value"
            label-key="label"
            @update:model-value="handleFilter"
          />
        </div>

        <!-- Date Range Filter -->
        <div v-if="showDateFilter" class="flex gap-2 md:w-auto">
          <div class="md:w-40">
            <DatePicker
              v-model="dateFrom"
              placeholder="From"
              @update:model-value="handleFilter"
            />
          </div>
          <div class="md:w-40">
            <DatePicker
              v-model="dateTo"
              placeholder="To"
              @update:model-value="handleFilter"
            />
          </div>
        </div>

        <!-- Per Page -->
        <div class="md:w-32">
          <SearchableSelect
            v-model="localPerPage"
            :options="perPageOptions"
            placeholder="Per Page"
            track-by="value"
            label-key="label"
            @update:model-value="handlePerPageChange"
          />
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              @click="handleSort(column.key)"
              :class="[
                'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100',
                column.sortable !== false ? 'select-none' : ''
              ]"
            >
              <div class="flex items-center space-x-1">
                <span>{{ column.label }}</span>
                <span v-if="column.sortable !== false && sortBy === column.key" class="text-sky-600">
                  <svg v-if="sortOrder === 'asc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </span>
              </div>
            </th>
            <th v-if="actions" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-if="loading" class="bg-white">
            <td :colspan="columns.length + (actions ? 1 : 0)" class="px-6 py-12 text-center">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
              <p class="text-gray-500 mt-4">Loading...</p>
            </td>
          </tr>
          <tr v-else-if="data.length === 0" class="bg-white">
            <td :colspan="columns.length + (actions ? 1 : 0)" class="px-6 py-12 text-center text-gray-500">
              No data available
            </td>
          </tr>
          <tr
            v-else
            v-for="(row, index) in data"
            :key="row.id || index"
            class="hover:bg-gray-50 transition-colors"
          >
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
            >
              <slot :name="`cell-${column.key}`" :row="row" :value="getNestedValue(row, column.key)">
                {{ formatCell(getNestedValue(row, column.key), column) }}
              </slot>
            </td>
            <td v-if="actions" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <slot name="actions" :row="row">
                <button
                  v-if="actions.view"
                  @click="$emit('view', row)"
                  class="text-sky-600 hover:text-sky-800 mr-3"
                  title="View"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
                <button
                  v-if="actions.edit"
                  @click="$emit('edit', row)"
                  class="text-sky-600 hover:text-sky-800 mr-3"
                  title="Edit"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button
                  v-if="actions.delete"
                  @click="$emit('delete', row)"
                  class="text-red-600 hover:text-red-800"
                  title="Delete"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pagination && pagination.total > 0" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
      <div class="text-sm text-gray-700">
        Showing
        <span class="font-medium">{{ pagination.from }}</span>
        to
        <span class="font-medium">{{ pagination.to }}</span>
        of
        <span class="font-medium">{{ pagination.total }}</span>
        results
      </div>
      <div class="flex space-x-2">
        <button
          @click="goToPage(pagination.current_page - 1)"
          :disabled="!pagination.prev_page_url"
          class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Previous
        </button>
        <button
          @click="goToPage(pagination.current_page + 1)"
          :disabled="!pagination.next_page_url"
          class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import DatePicker from './DatePicker.vue';
import SearchableSelect from './SearchableSelect.vue';

const props = defineProps({
  columns: {
    type: Array,
    required: true,
  },
  data: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  pagination: {
    type: Object,
    default: null,
  },
  search: {
    type: String,
    default: '',
  },
  statusFilter: {
    type: String,
    default: '',
  },
  dateFrom: {
    type: String,
    default: '',
  },
  dateTo: {
    type: String,
    default: '',
  },
  perPage: {
    type: Number,
    default: 10,
  },
  sortBy: {
    type: String,
    default: '',
  },
  sortOrder: {
    type: String,
    default: 'asc',
  },
  actions: {
    type: Object,
    default: () => ({ view: true, edit: true, delete: true }),
  },
  searchPlaceholder: {
    type: String,
    default: 'Search...',
  },
  showStatusFilter: {
    type: Boolean,
    default: false,
  },
  showDateFilter: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits([
  'update:search',
  'update:statusFilter',
  'update:dateFrom',
  'update:dateTo',
  'update:perPage',
  'update:sortBy',
  'update:sortOrder',
  'page-change',
  'view',
  'edit',
  'delete',
]);

const localSearch = ref(props.search);
const localStatusFilter = ref(props.statusFilter);
const localDateFrom = ref(props.dateFrom);
const localDateTo = ref(props.dateTo);
const localPerPage = ref(props.perPage);
let searchTimeout = null;

const statusOptions = [
  { value: '', label: 'All Status' },
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'pending', label: 'Pending' },
  { value: 'suspended', label: 'Suspended' },
];

const perPageOptions = [
  { value: 5, label: '5' },
  { value: 10, label: '10' },
  { value: 20, label: '20' },
  { value: 50, label: '50' },
  { value: 100, label: '100' },
];

const handleSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    emit('update:search', localSearch.value);
  }, 500);
};

const handleFilter = () => {
  emit('update:statusFilter', localStatusFilter.value);
  emit('update:dateFrom', localDateFrom.value);
  emit('update:dateTo', localDateTo.value);
};

const handlePerPageChange = () => {
  emit('update:perPage', localPerPage.value);
};

const handleSort = (column) => {
  if (props.sortBy === column) {
    emit('update:sortOrder', props.sortOrder === 'asc' ? 'desc' : 'asc');
  } else {
    emit('update:sortBy', column);
    emit('update:sortOrder', 'asc');
  }
};

const goToPage = (page) => {
  emit('page-change', page);
};

const getNestedValue = (obj, path) => {
  return path.split('.').reduce((o, p) => o && o[p], obj);
};

const formatCell = (value, column) => {
  if (value === null || value === undefined) return '-';
  if (column.format === 'date') {
    return new Date(value).toLocaleDateString();
  }
  if (column.format === 'datetime') {
    return new Date(value).toLocaleString();
  }
  if (column.format === 'currency') {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
  }
  if (column.format === 'status') {
    const statusColors = {
      active: 'bg-emerald-100 text-emerald-800',
      inactive: 'bg-red-100 text-red-800',
      pending: 'bg-yellow-100 text-yellow-800',
      suspended: 'bg-gray-100 text-gray-800',
    };
    return `<span class="px-2.5 py-1 text-xs font-medium rounded-full ${statusColors[value] || 'bg-gray-100 text-gray-800'}">${value}</span>`;
  }
  return value;
};

watch(() => props.search, (val) => {
  localSearch.value = val;
});

watch(() => props.statusFilter, (val) => {
  localStatusFilter.value = val;
});
</script>


