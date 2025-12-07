
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
            <label class="label-field">Email Address <span class="text-red-500">*</span></label>
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

          <!-- Location Fields -->
          <div class="md:col-span-2">
            <LocationSelect
              :model-value="location"
              @update:model-value="updateLocation"
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

          <!-- Description (Last) -->
          <div class="">
            <label class="label-field">Description</label>
            <Ckeditor
            :editor="editor"
            :config="editorConfig"
            v-model="form.description"
            @blur="validateField('description')"
            class="ck-editor-height"
            />
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
<style>
.ck-blurred.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline {
    height: 150px;
}
.ck-focused.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline {
    height: 150px;
}
</style>
<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useBusinessStore } from '../stores/business';
import { useValidation } from '../composables/useValidation';
import LocationSelect from './LocationSelect.vue';
import FileUpload from './FileUpload.vue';
import SearchableSelect from './SearchableSelect.vue';
import { Ckeditor } from '@ckeditor/ckeditor5-vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
const editor = ClassicEditor
const editorConfig = {
  toolbar: [
    'heading',
    'bold',
    'italic',
    'link',
    'bulletedList',
    'numberedList',
    'blockQuote'
  ],
};
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

const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
  { value: 'pending', label: 'Pending' },
  { value: 'suspended', label: 'Suspended' },
];

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
      country_id: props.business.country_id || null,
      state_id: props.business.state_id || null,
      city_id: props.business.city_id || null,
      zip_code: props.business.zip_code || '',
      status: props.business.status || 'active',
      description: props.business.description || '',
      logo: props.business.logo || null,
    });

    // Set location values - ensure they're numbers or null
    location.country_id = props.business.country_id ? Number(props.business.country_id) : null;
    location.state_id = props.business.state_id ? Number(props.business.state_id) : null;
    location.city_id = props.business.city_id ? Number(props.business.city_id) : null;
  }
});

const updateLocation = (newLocation) => {
  location.country_id = newLocation.country_id;
  location.state_id = newLocation.state_id;
  location.city_id = newLocation.city_id;

  // Also update form for consistency
  form.country_id = newLocation.country_id !== null ? newLocation.country_id : '';
  form.state_id = newLocation.state_id !== null ? newLocation.state_id : '';
  form.city_id = newLocation.city_id !== null ? newLocation.city_id : '';
};

watch(location, (newVal) => {
  form.country_id = newVal.country_id !== null ? newVal.country_id : '';
  form.state_id = newVal.state_id !== null ? newVal.state_id : '';
  form.city_id = newVal.city_id !== null ? newVal.city_id : '';
}, { deep: true, immediate: true });

const handleSubmit = async () => {
  clearErrors();
  error.value = null;

  // Sync location values to form before validation (ensure latest values)
  // Read directly from location reactive object to get the most current values
  form.country_id = location.country_id !== null && location.country_id !== '' ? location.country_id : '';
  form.state_id = location.state_id !== null && location.state_id !== '' ? location.state_id : '';
  form.city_id = location.city_id !== null && location.city_id !== '' ? location.city_id : '';

  // Client-side validation
  if (!validateForm(form, validationRules)) {
    return;
  }

  loading.value = true;

  try {
    const formData = new FormData();

    // Debug: Log location values before processing
    console.log('Location values before processing:', {
      country_id: location.country_id,
      state_id: location.state_id,
      city_id: location.city_id,
      types: {
        country_id: typeof location.country_id,
        state_id: typeof location.state_id,
        city_id: typeof location.city_id,
      }
    });

    // First, ensure location values are properly set from the location reactive object
    // Read directly from location to get the most current values
    const getLocationId = (value) => {
      if (value === null || value === undefined || value === '') {
        return null;
      }
      // Convert to number and validate
      const numValue = typeof value === 'string' ? parseInt(value, 10) : Number(value);
      return !isNaN(numValue) && numValue > 0 ? numValue : null;
    };

    const countryId = getLocationId(location.country_id);
    const stateId = getLocationId(location.state_id);
    const cityId = getLocationId(location.city_id);

    console.log('Processed location IDs:', { countryId, stateId, cityId });

    // Append all form fields
    Object.keys(form).forEach(key => {
      if (key !== 'logo' && !['country_id', 'state_id', 'city_id'].includes(key)) {
        const value = form[key];
        if (value !== null && value !== '') {
          formData.append(key, value);
        }
      }
    });

    // Explicitly append location fields as strings (FormData converts to string anyway)
    if (countryId !== null) {
      formData.append('country_id', String(countryId));
      console.log('Appended country_id:', String(countryId));
    }
    if (stateId !== null) {
      formData.append('state_id', String(stateId));
      console.log('Appended state_id:', String(stateId));
    }
    if (cityId !== null) {
      formData.append('city_id', String(cityId));
      console.log('Appended city_id:', String(cityId));
    }

    // Debug: Verify FormData contents
    console.log('FormData location values:', {
      country_id: formData.get('country_id'),
      state_id: formData.get('state_id'),
      city_id: formData.get('city_id')
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
    console.log("FULL ERROR OBJECT:", err);
  console.log("ERROR RESPONSE:", err.phone);
  console.log("ERROR DATA:", err.response?.data);
  console.log("ERROR ERRORS:", err.response?.data?.errors);
 if (err.phone || err.email) {
    Object.keys(err).forEach((key) => {
      errors[key] = err[key][0]; // convert array → string
      touched[key] = true;
    });
    return;
  }

  } finally {
    loading.value = false;
  }
};
</script>
