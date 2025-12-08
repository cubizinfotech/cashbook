<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb Navigation -->
      <div class="mb-6">
        <router-link
          :to="`/businesses/${cashbook?.business_id || businessId}`"
          class="text-sky-600 hover:text-sky-800 text-sm font-medium mb-4 inline-flex items-center"
        >
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Back to Business
        </router-link>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="bg-white rounded-lg shadow p-12 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
        <p class="text-gray-500 mt-4">Loading cashbook details...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
        {{ error }}
      </div>

      <!-- Cashbook Content -->
      <div v-else-if="cashbook" class="space-y-6">
        <!-- Cash Details Section -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Cash Details</h2>
          </div>
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <h1 class="text-2xl font-bold text-gray-900">{{ cashbook.title }}</h1>
              <span
                :class="{
                  'bg-emerald-100 text-emerald-800': cashbook.status === 'active',
                  'bg-red-100 text-red-800': cashbook.status === 'inactive',
                  'bg-yellow-100 text-yellow-800': cashbook.status === 'pending',
                  'bg-gray-100 text-gray-800': cashbook.status === 'suspended'
                }"
                class="px-3 py-1 text-sm font-medium rounded-full"
              >
                {{ cashbook.status }}
              </span>
            </div>
            <div v-if="cashbook.description" class="mt-2">
              <p class="text-gray-600">{{ cashbook.description }}</p>
            </div>
          </div>
        </div>

        <!-- Cash In Section -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Cash In</h2>
            <button
              @click="openAddForm('in')"
              class="btn-primary text-sm"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>Add Transaction</span>
            </button>
          </div>
          <div class="p-6">
            <!-- Cash In Transactions Table -->
            <div v-if="cashInTransactions.length > 0" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Party</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
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
                    <td class="px-6 py-4 text-sm text-gray-500">{{ transaction.category?.name || '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ transaction.payment_method?.name|| '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                    <div v-if="transaction.document_url">
                        <a :href="transaction.document_url" target="_blank">
                        <!-- Eye Preview Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-6 w-6 text-blue-600 hover:text-blue-800 cursor-pointer transition">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        </a>
                    </div>

                    <span v-else>-</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ transaction.description || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <button
                        @click="editTransaction(transaction, 'in')"
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
        </div>

        <!-- Cash Out Section -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Cash Out</h2>
            <button
              @click="openAddForm('out')"
              class="btn-primary text-sm"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>Add Transaction</span>
            </button>
          </div>
          <div class="p-6">
            <!-- Cash Out Transactions Table -->
            <div v-if="cashOutTransactions.length > 0" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Party</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>

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
                    <td class="px-6 py-4 text-sm text-gray-500">{{ transaction.category?.name || '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ transaction.payment_method?.name|| '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                    <div v-if="transaction.document_url">
                        <a :href="transaction.document_url" target="_blank">
                        <!-- Eye Preview Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-6 w-6 text-blue-600 hover:text-blue-800 cursor-pointer transition">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        </a>
                    </div>

                    <span v-else>-</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ transaction.description || '-' }}</td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <button
                        @click="editTransaction(transaction, 'out')"
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
        </div>
      </div>

      <!-- Transaction Form Modal -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
        @click.self="closeModal"
      >
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
          <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center z-10">
            <h3 class="text-xl font-bold text-gray-900">
              {{ editingTransaction ? 'Edit Transaction' : (currentFormType === 'in' ? 'Add Cash In Transaction' : 'Add Cash Out Transaction') }}
            </h3>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 transition-colors p-1"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="handleSubmit(currentFormType)" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Amount -->
                <div>
                <label class="label-field">Amount <span class="text-red-500">*</span></label>
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
                <!-- Date & Time -->
                <div>
                <label class="label-field">Transaction Date</label>
                <DateTimePicker
                    v-model="form.transaction_datetime"
                    :required="true"
                    :error="errors.transaction_datetime && touched.transaction_datetime ? errors.transaction_datetime : ''"
                    @blur="validateField('transaction_datetime')"
                />
                </div>
                <!-- Party Name -->
                <div>
                <label class="label-field">Party Name <span class="text-red-500">*</span></label>
                <input
                    v-model="form.party_name"
                    type="text"
                    @blur="validateField('party_name')"
                    :class="['input-field', { 'input-error': errors.party_name && touched.party_name }]"
                />
                <span v-if="errors.party_name && touched.party_name" class="error-message">{{ errors.party_name }}</span>
                </div>

                <!-- Category -->
                <div>
                <label class="label-field">Category</label>
                <SearchableSelect
                    v-model="form.category_id"
                    :options="categories"
                    placeholder="Select Category"
                    track-by="value"
                    label-key="label"
                />
                <span v-if="errors.category_id && touched.category_id" class="error-message">
                    {{ errors.category_id }}
                </span>
                </div>
                <!-- Payment Method -->
                <div class="md:col-span-2">
                 <div class="flex items-center justify-between mb-1">
                    <label class="label-field">Payment Method <span class="text-red-500">*</span></label>
                   <button
                    type="button"
                    @click="handlePaymentMethodClick"
                    class="text-sm px-2 py-1 bg-sky-600 text-white rounded hover:bg-sky-700"
                    >
                    {{ isEditingTransaction ? 'Edit Payment Method' : '+ Add Payment Method' }}
                    </button>
                </div>
                <SearchableSelect
                    v-model="form.payment_method_id"
                    :options="paymentMethods"
                    placeholder="Select Payment Method"
                    track-by="value"
                    label-key="label"
                    />
                    <span v-if="errors.payment_method_id && touched.payment_method_id" class="error-message">
                    {{ errors.payment_method_id }}
                    </span>
                </div>
             <!-- Document Upload -->
                <div class="md:col-span-2">

                    <FileUpload
                        v-model="form.document"
                        label="Document"
                        accept="image/*,application/pdf"
                        accept-text="Images or PDF up to 5MB"
                        :existing-file="editingTransaction?.document_url"
                        :error="errors.document && touched.document ? errors.document : ''"
                    />
                    </div>
                <!-- Remark (FULL WIDTH) -->
                    <div class="md:col-span-2">
                    <label class="label-field">Remark</label>
                    <textarea
                        v-model="form.remark"
                        rows="3"
                        class="input-field"
                    ></textarea>
                    </div>
                    <!-- Description (FULL WIDTH) -->
                    <div class="md:col-span-2">
                    <label class="label-field">Description</label>
                    <Ckeditor
                        :editor="editor"
                        :config="editorConfig"
                        v-model="form.description"
                    />
                    </div>
                </div>
                <!-- Error -->
                <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 mt-4 rounded">
                    {{ error }}
                </div>
            <!-- Footer Buttons -->
            <div class="flex justify-end gap-3 mt-6">
                <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100"
                >
                Cancel
                </button>

                <button
                type="submit"
                :disabled="submitting"
                class="px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700 disabled:opacity-50"
                >
                {{ submitting ? 'Saving...' : 'Save' }}
                </button>
            </div>

    </form>
        </div>
      </div>
         <!-- PAYMENT METHOD POPUP -->
            <div v-if="showPaymentMethodPopup"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
                @click.self="closePaymentMethodPopup"
                >
                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                    <!-- HEADER -->
                    <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center z-10">
                    <h3 class="text-xl font-bold text-gray-900">
                        {{ paymentMethodEditing
                            ? (currentFormType === 'in' ? 'Edit Cash In Payment Method' : 'Edit Cash Out Payment Method')
                            : (currentFormType === 'in' ? 'Add Cash In Payment Method' : 'Add Cash Out Payment Method')
                        }}
                    </h3>
                    <!-- Close Button -->
                    <button
                        @click="closePaymentMethodPopup"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-1"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    </div>
                    <!-- FORM -->
                    <form @submit.prevent="saveNewPaymentMethod(currentFormType)" class="p-6">
                        <!-- Name -->
                        <div>
                            <label class="label-field">Name <span class="text-red-500">*</span></label>
                            <input
                            v-model="paymentMethodForm.name"
                            type="text"
                            @blur="validateField('name')"
                            :class="['input-field', { 'input-error': errors.name && touched.name }]"
                        />

                        <span v-if="errors.name && touched.name" class="error-message">
                            {{ errors.name }}
                        </span>
                        </div>
                        <!-- Description -->
                        <div class="mt-6">
                            <label class="label-field">Description</label>
                            <textarea
                                v-model="paymentMethodForm.description"
                                rows="3"
                                @blur="validateField('description')"
                                class="input-field"
                            ></textarea>
                        </div>
                        <!-- Footer Buttons -->
                        <div class="flex justify-end gap-3 mt-6">
                                <button
                                    type="button"
                                    @click="closePaymentMethodPopup"
                                    class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100"
                                    >
                                    Cancel
                                </button>

                                <button
                                    type="button"
                                    @click="savePaymentMethod"
                                    class="px-4 py-2 bg-green-600 text-white rounded"
                                    >
                                    {{ paymentMethodEditing ? 'Update' : 'Save' }}
                                </button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTransactionStore } from '../stores/transaction';
import { useCashbookStore } from '../stores/cashbook';
import { useValidation } from '../composables/useValidation';
import DateTimePicker from '../components/DateTimePicker.vue';
import SearchableSelect from '../components/SearchableSelect.vue';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import axios from 'axios';
import FileUpload from '../components/FileUpload.vue';

import { nextTick } from 'vue';
const editor = ClassicEditor;
const editorConfig = {
  toolbar: ['heading', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
};

// routing
const route = useRoute();
const router = useRouter();

// stores
const transactionStore = useTransactionStore();
const cashbookStore = useCashbookStore();
const documentFile = ref(null);

const handleDocumentUpload = (e) => {
  documentFile.value = e.target.files[0];
};
// validation
const { errors, touched, validateField, validateForm, clearErrors, setErrors } = useValidation();

// state
const loading = ref(true);
const submitting = ref(false);
const error = ref(null);
const editingTransaction = ref(null);
const transactions = ref([]);
const cashbook = ref(null);
const businessId = ref(null);

const showModal = ref(false);
const currentFormType = ref('in');

// categories list
const categories = ref([]);
const paymentMethods = ref([]);
// ⭐ Load categories dynamically
const loadCategories = async () => {
  try {
    const res = await axios.get('/api/categories');

    categories.value = res.data.map(cat => ({
      label: cat.name,
      value: cat.id,
    }));
  } catch (err) {
    console.error("Error loading categories", err);
  }
};
const loadPaymentMethods = async () => {
  try {
    const res = await axios.get('/api/payment-methods');

    paymentMethods.value = res.data.map(pm => ({
      label: pm.name,
      value: pm.id,
        description: pm.description,
    }));
  } catch (err) {
    console.error("Error loading payment methods", err);
  }
};

// status dropdown options
const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'pending', label: 'Pending' },
  { value: 'suspended', label: 'Suspended' },
];

// form state
const form = reactive({
  cashbook_id: null,
  type: 'in',
  amount: '',
  party_name: '',
  transaction_datetime: new Date().toISOString().slice(0, 16),
  description: '',
  remark: '',
  status: 'active',
  category_id: null,
});

// validation rules
const validationRules = {
  cashbook_id: ['required'],
  type: ['required'],
  amount: ['required', 'numeric'],
  party_name: ['required', 'min:2', 'max:255'],
  transaction_datetime: ['required'],
  payment_method_id: ['required'], // ✅ Added this

};

// computed
const cashInTransactions = computed(() => {
  return transactions.value.filter(t => t.type === 'in');
});

const cashOutTransactions = computed(() => {
  return transactions.value.filter(t => t.type === 'out');
});

// ⭐ Load everything on mount
onMounted(async () => {
  await loadCategories();
  await loadPaymentMethods();  // <-- YOU FORGOT TO CALL THIS
  await loadCashbook();
  await loadTransactions();
});


// load cashbook
const loadCashbook = async () => {
  try {
    loading.value = true;
    const cashbookData = await cashbookStore.fetchCashbook(route.params.id);
    cashbook.value = cashbookData;
    businessId.value = cashbookData.business_id;
    form.cashbook_id = cashbookData.id;
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load cashbook';
  } finally {
    loading.value = false;
  }
};

// load transactions
const loadTransactions = async () => {
  try {
    const response = await transactionStore.fetchTransactions({
      cashbook_id: route.params.id,
    });
    transactions.value = response.data || [];
  } catch (err) {
    console.error('Failed to load transactions', err);
  }
};

// open form modal
const openAddForm = (type) => {
  currentFormType.value = type;
  form.type = type;
  resetForm();
  const last = getLastUsedPaymentMethod(type);
  form.payment_method_id = last || null;
  showModal.value = true;
};

// close modal
const closeModal = () => {
  showModal.value = false;
  resetForm();
};

// edit transaction
const editTransaction = (transaction, type) => {
  editingTransaction.value = transaction;
  currentFormType.value = type;
    documentFile.value = null; // don't preload file input
  Object.assign(form, {
    cashbook_id: cashbook.value.id,
    type: transaction.type,
    amount: transaction.amount,
    party_name: transaction.party_name,
    transaction_datetime: new Date(transaction.transaction_datetime).toISOString().slice(0, 16),
    description: transaction.description || '',
    remark: transaction.remark || '',
    status: transaction.status || 'active',
    category_id: transaction.category_id || null,
    payment_method_id: transaction.payment_method_id || null,
    document: transaction.document || null,

  });

  showModal.value = true;
};

// delete
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

// reset form
const resetForm = () => {
  editingTransaction.value = null;
  clearErrors();
  error.value = null;

  Object.assign(form, {
    cashbook_id: cashbook.value?.id || null,
    type: currentFormType.value,
    amount: '',
    party_name: '',
    transaction_datetime: new Date().toISOString().slice(0, 16),
    description: '',
    remark: '',
    status: 'active',
    category_id: null,
  });
};

// submit
const handleSubmit = async (type) => {
  clearErrors();
  error.value = null;
  form.type = type;

  if (!validateForm(form, validationRules)) {
    return;
  }

  submitting.value = true;

  try {
    const formData = new FormData();

    // append all normal fields
    for (const key in form) {
      formData.append(key, form[key] ?? '');
    }

    // append document file
    if (documentFile.value) {
      formData.append('document', documentFile.value);
    }

    if (editingTransaction.value) {
      formData.append('_method', 'PUT');
      await axios.post(
        `/api/transactions/${editingTransaction.value.id}`,
        formData,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      );
    } else {
      await axios.post('/api/transactions', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }

    resetForm();
    closeModal();
    await loadTransactions();
  } catch (err) {
    if (err.response?.data?.errors) {
      setErrors(err.response.data.errors);
    } else {
      error.value = err.message || 'Failed to save transaction';
    }
  } finally {
    submitting.value = false;
  }
};


// helpers
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString();
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
};

const showPaymentMethodPopup = ref(false);
const paymentMethodEditing = ref(false); // false = add, true = edit
const isEditingTransaction = computed(() => editingTransaction.value !== null);
const paymentMethodForm = reactive({
  id: null,
  name: '',
  description: '',
  cashbook_id: ''
});

const paymentMethodRules = {
  name: ['required', 'min:2', 'max:255'],
  description: ['max:500']
};
const handlePaymentMethodClick = () => {
  // If user is editing a transaction → edit payment method
  if (isEditingTransaction.value) {
    editPaymentMethod();
    return;
  }

  // User is adding a new transaction
  // Even if auto-filled payment method exists → treat as ADD
  if (!paymentMethodEditing.value) {
    openAddPaymentMethod();
  } else {
    editPaymentMethod();
  }
};


const openAddPaymentMethod = () => {
  paymentMethodEditing.value = false;
  paymentMethodForm.id = null;
  paymentMethodForm.name = '';
  paymentMethodForm.description = '';
  paymentMethodForm.cashbook_id = '';
  showPaymentMethodPopup.value = true;
};


const editPaymentMethod = () => {
  const selected = paymentMethods.value.find(
    pm => pm.value === form.payment_method_id
  );
    // currentFormType.value = type;
  if (!selected) return;

  paymentMethodEditing.value = true; // EDIT mode

  paymentMethodForm.id = selected.value;
  paymentMethodForm.name = selected.label;
    paymentMethodForm.description = selected.description || '';
  paymentMethodForm.cashbook_id = selected.cashbook_id || '';

  showPaymentMethodPopup.value = true;
};


const closePaymentMethodPopup = () => {
  showPaymentMethodPopup.value = false;
  paymentMethodEditing.value = false;
  paymentMethodForm.id = null;
  paymentMethodForm.name = '';
  paymentMethodForm.description = '';
  paymentMethodForm.cashbook_id = '';
};


const saveNewPaymentMethod = async () => {
  clearErrors();

  const isValid = validateForm(paymentMethodForm, paymentMethodRules);
  if (!isValid) return;

  try {
    const res = await axios.post('/api/payment-methods', {
      name: paymentMethodForm.name,
      description: paymentMethodForm.description,
      cash_id: businessId.value,
    });

    const created = res.data.data;

    paymentMethods.value.unshift({
      label: created.name,
      value: created.id,
      description: created.description,
      cashbook_id: created.cashbook_id
    });

    form.payment_method_id = created.id;

    closePaymentMethodPopup();
  } catch (err) {
    console.error("Save payment method error:", err);
  }
};


const updatePaymentMethod = async () => {
  clearErrors();

  const isValid = validateForm(paymentMethodForm, paymentMethodRules);
  if (!isValid) return;

  try {
    const res = await axios.put(`/api/payment-methods/${paymentMethodForm.id}`, {
      name: paymentMethodForm.name,
      description: paymentMethodForm.description,
      cashbook_id: paymentMethodForm.cashbook_id,
    });

    const updated = res.data.data;

    // Update list
    paymentMethods.value = paymentMethods.value.map(pm =>
      pm.value === updated.id
        ? {
            label: updated.name,
            value: updated.id,
            description: updated.description,
            cashbook_id: updated.cashbook_id
          }
        : pm
    );

    // ⭐ Force dropdown refresh
    form.payment_method_id = null;
    await nextTick();
    form.payment_method_id = updated.id;

    closePaymentMethodPopup();
  } catch (err) {
    console.error("Update error:", err);
  }
};


const savePaymentMethod = () => {
  return paymentMethodEditing.value
    ? updatePaymentMethod()
    : saveNewPaymentMethod();
};
const getLastUsedPaymentMethod = (type) => {
  const list = transactions.value
    .filter(t => t.type === type && t.payment_method_id)
    .sort((a, b) => new Date(b.transaction_datetime) - new Date(a.transaction_datetime));

  return list.length ? list[0].payment_method_id : null;
};

</script>
