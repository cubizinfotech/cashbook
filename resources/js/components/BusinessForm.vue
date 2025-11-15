<template>
  <div class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
      <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center z-10">
        <h3 class="text-xl font-bold text-gray-900">{{ business ? 'Edit Business' : 'Create Business' }}</h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600 transition-colors p-1"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Name -->
          <div class="md:col-span-2">
            <label class="label-field">
              Business Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              @blur="validateField('name')"
              class="input-field"
              :class="{ 'input-error': errors.name && touched.name }"
              placeholder="Enter business name"
            />
            <span v-if="errors.name && touched.name" class="error-message">{{ errors.name }}</span>
          </div>

          <!-- Email -->
          <div>
            <label class="label-field">Email Address</label>
            <input
              v-model="form.email"
              type="email"
              @blur="validateField('email')"
              class="input-field"
              :class="{ 'input-error': errors.email && touched.email }"
              placeholder="business@example.com"
            />
            <span v-if="errors.email && touched.email" class="error-message">{{ errors.email }}</span>
          </div>

          <!-- Phone -->
          <div>
            <label class="label-field">Phone Number</label>
            <input
              v-model="form.phone"
              type="text"
              @blur="validateField('phone')"
              class="input-field"
              :class="{ 'input-error': errors.phone && touched.phone }"
              placeholder="+1 234 567 8900"
            />
            <span v-if="errors.phone && touched.phone" class="error-message">{{ errors.phone }}</span>
          </div>

          <!-- Website -->
          <div>
            <label class="label-field">Website</label>
            <input
              v-model="form.website"
              type="url"
              @blur="validateField('website')"
              class="input-field"
              :class="{ 'input-error': errors.website && touched.website }"
              placeholder="https://www.example.com"
            />
            <span v-if="errors.website && touched.website" class="error-message">{{ errors.website }}</span>
          </div>

          <!-- GST Number -->
          <div>
            <label class="label-field">GST Number</label>
            <input
              v-model="form.gst_number"
              type="text"
              @blur="validateField('gst_number')"
              class="input-field"
              :class="{ 'input-error': errors.gst_number && touched.gst_number }"
              placeholder="GST123456789"
            />
            <span v-if="errors.gst_number && touched.gst_number" class="error-message">{{ errors.gst_number }}</span>
          </div>

          <!-- Address -->
          <div class="md:col-span-2">
            <label class="label-field">Address</label>
            <input
              v-model="form.address"
              type="text"
              @blur="validateField('address')"
              class="input-field"
              :class="{ 'input-error': errors.address && touched.address }"
              placeholder="Enter business address"
            />
            <span v-if="errors.address && touched.address" class="error-message">{{ errors.address }}</span>
          </div>

          <!-- Logo Upload -->
          <div class="md:col-span-2">
            <FileUpload
              v-model="form.logo"
              label="Business Logo"
              accept="image/*"
              accept-text="PNG, JPG, GIF up to 2MB"
              :error="errors.logo && touched.logo ? errors.logo : ''"
            />
          </div>

          <!-- Location Fields -->
          <div class="md:col-span-2">
            <LocationSelect
              v-model="location"
              :errors="errors"
              :touched="touched"
            />
          </div>

          <!-- Zip Code -->
          <div>
            <label class="label-field">Zip Code</label>
            <input
              v-model="form.zip_code"
              type="text"
              @blur="validateField('zip_code')"
              class="input-field"
              :class="{ 'input-error': errors.zip_code && touched.zip_code }"
              placeholder="12345"
            />
            <span v-if="errors.zip_code && touched.zip_code" class="error-message">{{ errors.zip_code }}</span>
          </div>

          <!-- Status -->
          <div>
            <label class="label-field">Status</label>
            <select
              v-model="form.status"
              class="input-field"
            >
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="pending">Pending</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>

          <!-- Description (Last) -->
          <div class="md:col-span-2">
            <label class="label-field">Description</label>
            <textarea
              v-model="form.description"
              rows="4"
              @blur="validateField('description')"
              class="input-field resize-none"
              :class="{ 'input-error': errors.description && touched.description }"
              placeholder="Enter business description"
            ></textarea>
            <span v-if="errors.description && touched.description" class="error-message">{{ errors.description }}</span>
          </div>
        </div>

        <div v-if="error" class="mt-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
          {{ error }}
        </div>

        <div class="flex justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
            class="btn-secondary"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
            <span>{{ loading ? 'Saving...' : 'Save Business' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useBusinessStore } from '../stores/business';
import { useValidation } from '../composables/useValidation';
import LocationSelect from './LocationSelect.vue';
import FileUpload from './FileUpload.vue';

const props = defineProps({
  business: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'saved']);

const businessStore = useBusinessStore();
const { errors, touched, validateField, validateForm, clearErrors, setErrors } = useValidation();
const loading = ref(false);
const error = ref(null);
const location = reactive({
  country_id: null,
  state_id: null,
  city_id: null,
});

const form = reactive({
  name: '',
  email: '',
  phone: '',
  website: '',
  gst_number: '',
  address: '',
  country_id: '',
  state_id: '',
  city_id: '',
  zip_code: '',
  status: 'active',
  description: '',
  logo: null,
});

const validationRules = {
  name: ['required', 'min:2', 'max:255'],
  email: ['email', 'max:255'],
  phone: ['max:255'],
  website: ['url', 'max:255'],
  gst_number: ['max:255'],
  address: ['max:255'],
  zip_code: ['max:255'],
  description: [],
};

onMounted(() => {
  clearErrors();
  if (props.business) {
    Object.assign(form, {
      name: props.business.name || '',
      email: props.business.email || '',
      phone: props.business.phone || '',
      website: props.business.website || '',
      gst_number: props.business.gst_number || '',
      address: props.business.address || '',
      country_id: props.business.country_id || '',
      state_id: props.business.state_id || '',
      city_id: props.business.city_id || '',
      zip_code: props.business.zip_code || '',
      status: props.business.status || 'active',
      description: props.business.description || '',
      logo: props.business.logo || null,
    });
    
    location.country_id = props.business.country_id;
    location.state_id = props.business.state_id;
    location.city_id = props.business.city_id;
  }
});

watch(location, (newVal) => {
  form.country_id = newVal.country_id;
  form.state_id = newVal.state_id;
  form.city_id = newVal.city_id;
}, { deep: true });

const handleSubmit = async () => {
  clearErrors();
  error.value = null;

  // Client-side validation
  if (!validateForm(form, validationRules)) {
    return;
  }

  loading.value = true;

  try {
    const formData = new FormData();
    
    Object.keys(form).forEach(key => {
      if (form[key] !== null && form[key] !== '' && key !== 'logo') {
        formData.append(key, form[key] || null);
      }
    });
    
    // Handle file upload
    if (form.logo instanceof File) {
      formData.append('logo', form.logo);
    }

    if (props.business) {
      await businessStore.updateBusiness(props.business.id, formData);
    } else {
      await businessStore.createBusiness(formData);
    }
    emit('saved');
  } catch (err) {
    if (err.response?.data?.errors) {
      setErrors(err.response.data.errors);
    } else {
      error.value = err.message || 'Failed to save business';
    }
  } finally {
    loading.value = false;
  }
};
</script>
