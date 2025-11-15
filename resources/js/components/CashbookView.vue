<template>
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">
      <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center z-10">
        <h3 class="text-xl font-bold text-gray-900">{{ cashbook?.title }}</h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="p-6">
      <!-- Tabs -->
      <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="activeTab = 'in'"
            :class="[
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === 'in'
                ? 'border-sky-500 text-sky-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            Cash In
          </button>
          <button
            @click="activeTab = 'out'"
            :class="[
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === 'out'
                ? 'border-sky-500 text-sky-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            Cash Out
          </button>
          <button
            @click="activeTab = 'form'"
            :class="[
              'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === 'form'
                ? 'border-sky-500 text-sky-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            Add Transaction
          </button>
        </nav>
      </div>

      <!-- Cash In Tab -->
      <div v-if="activeTab === 'in'" class="space-y-4">
        <h4 class="text-lg font-semibold">Cash In Transactions</h4>
        <div v-if="cashInTransactions.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Party</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="transaction in cashInTransactions" :key="transaction.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(transaction.transaction_datetime) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transaction.party_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">
                  +{{ formatCurrency(transaction.amount) }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ transaction.description }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button
                    @click="editTransaction(transaction)"
                    class="text-sky-600 hover:text-sky-800 mr-3"
                  >
                    Edit
                  </button>
                  <button
                    @click="confirmDeleteTransaction(transaction)"
                    class="text-red-600 hover:text-red-800"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center text-gray-500 py-8">
          No cash in transactions yet.
        </div>
      </div>

      <!-- Cash Out Tab -->
      <div v-if="activeTab === 'out'" class="space-y-4">
        <h4 class="text-lg font-semibold">Cash Out Transactions</h4>
        <div v-if="cashOutTransactions.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Party</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="transaction in cashOutTransactions" :key="transaction.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(transaction.transaction_datetime) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ transaction.party_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                  -{{ formatCurrency(transaction.amount) }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ transaction.description }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <button
                    @click="editTransaction(transaction)"
                    class="text-sky-600 hover:text-sky-800 mr-3"
                  >
                    Edit
                  </button>
                  <button
                    @click="confirmDeleteTransaction(transaction)"
                    class="text-red-600 hover:text-red-800"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center text-gray-500 py-8">
          No cash out transactions yet.
        </div>
      </div>

      <!-- Transaction Form Tab -->
      <div v-if="activeTab === 'form'" class="space-y-4">
        <h4 class="text-lg font-semibold">{{ editingTransaction ? 'Edit Transaction' : 'Add Transaction' }}</h4>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Type *</label>
              <select
                v-model="form.type"
                @blur="validateField('type')"
                :class="['input-field', { 'input-error': errors.type && touched.type }]"
              >
                <option value="in">Cash In</option>
                <option value="out">Cash Out</option>
              </select>
              <span v-if="errors.type && touched.type" class="error-message">{{ errors.type }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Amount *</label>
              <input
                v-model="form.amount"
                type="number"
                step="0.01"
                min="0"
                @blur="validateField('amount')"
                :class="['input-field', { 'input-error': errors.amount && touched.amount }]"
              />
              <span v-if="errors.amount && touched.amount" class="error-message">{{ errors.amount }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Party Name *</label>
              <input
                v-model="form.party_name"
                type="text"
                @blur="validateField('party_name')"
                :class="['input-field', { 'input-error': errors.party_name && touched.party_name }]"
              />
              <span v-if="errors.party_name && touched.party_name" class="error-message">{{ errors.party_name }}</span>
            </div>

            <div>
              <DateTimePicker
                v-model="form.transaction_datetime"
                label="Transaction Date"
                :required="true"
                :error="errors.transaction_datetime && touched.transaction_datetime ? errors.transaction_datetime : ''"
                @blur="validateField('transaction_datetime')"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Description</label>
              <textarea
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Remark</label>
              <textarea
                v-model="form.remark"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"
              ></textarea>
            </div>
          </div>

          <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            {{ error }}
          </div>

          <div class="flex justify-end space-x-2">
            <button
              type="button"
              @click="resetForm"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700 disabled:opacity-50"
            >
              {{ loading ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useTransactionStore } from '../stores/transaction';
import { useCashbookStore } from '../stores/cashbook';
import { useValidation } from '../composables/useValidation';
import DateTimePicker from './DateTimePicker.vue';

const props = defineProps({
  cashbook: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close', 'saved']);

const transactionStore = useTransactionStore();
const cashbookStore = useCashbookStore();
const { errors, touched, validateField, validateForm, clearErrors, setErrors } = useValidation();
const loading = ref(false);
const error = ref(null);
const activeTab = ref('in');
const editingTransaction = ref(null);
const transactions = ref([]);

const form = reactive({
  cashbook_id: props.cashbook.id,
  type: 'in',
  amount: '',
  party_name: '',
  transaction_datetime: new Date().toISOString().slice(0, 16),
  description: '',
  remark: '',
  status: 'active',
});

const validationRules = {
  cashbook_id: ['required'],
  type: ['required'],
  amount: ['required', 'numeric'],
  party_name: ['required', 'min:2', 'max:255'],
  transaction_datetime: ['required'],
};

const cashInTransactions = computed(() => {
  return transactions.value.filter(t => t.type === 'in');
});

const cashOutTransactions = computed(() => {
  return transactions.value.filter(t => t.type === 'out');
});

onMounted(async () => {
  await loadTransactions();
  await loadCashbook();
});

watch(() => props.cashbook, async () => {
  if (props.cashbook) {
    form.cashbook_id = props.cashbook.id;
    await loadTransactions();
    await loadCashbook();
  }
}, { immediate: true });

const loadTransactions = async () => {
  try {
    const response = await transactionStore.fetchTransactions({
      cashbook_id: props.cashbook.id,
    });
    transactions.value = response.data || [];
  } catch (err) {
    console.error('Failed to load transactions', err);
  }
};

const loadCashbook = async () => {
  try {
    await cashbookStore.fetchCashbook(props.cashbook.id);
  } catch (err) {
    console.error('Failed to load cashbook', err);
  }
};

const editTransaction = (transaction) => {
  editingTransaction.value = transaction;
  Object.assign(form, {
    cashbook_id: props.cashbook.id,
    type: transaction.type,
    amount: transaction.amount,
    party_name: transaction.party_name,
    transaction_datetime: new Date(transaction.transaction_datetime).toISOString().slice(0, 16),
    description: transaction.description || '',
    remark: transaction.remark || '',
    status: transaction.status || 'active',
  });
  activeTab.value = 'form';
};

const confirmDeleteTransaction = async (transaction) => {
  if (confirm(`Are you sure you want to delete this transaction?`)) {
    try {
      await transactionStore.deleteTransaction(transaction.id);
      await loadTransactions();
    } catch (err) {
      alert('Failed to delete transaction');
    }
  }
};

const resetForm = () => {
  editingTransaction.value = null;
  clearErrors();
  Object.assign(form, {
    cashbook_id: props.cashbook.id,
    type: 'in',
    amount: '',
    party_name: '',
    transaction_datetime: new Date().toISOString().slice(0, 16),
    description: '',
    remark: '',
    status: 'active',
  });
};

const handleSubmit = async () => {
  clearErrors();
  error.value = null;

  // Client-side validation
  if (!validateForm(form, validationRules)) {
    return;
  }

  loading.value = true;

  try {
    const formData = { ...form };
    Object.keys(formData).forEach(key => {
      if (formData[key] === '') {
        formData[key] = null;
      }
    });

    if (editingTransaction.value) {
      await transactionStore.updateTransaction(editingTransaction.value.id, formData);
    } else {
      await transactionStore.createTransaction(formData);
    }
    resetForm();
    await loadTransactions();
    emit('saved');
  } catch (err) {
    if (err.response?.data?.errors) {
      setErrors(err.response.data.errors);
    } else {
      error.value = err.message || 'Failed to save transaction';
    }
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString();
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
};
</script>

