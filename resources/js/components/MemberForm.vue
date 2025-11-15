<template>
  <div class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
      <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center z-10">
        <h3 class="text-xl font-bold text-gray-900">{{ member ? 'Edit Member' : 'Create Member' }}</h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600 transition-colors p-1"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="p-6" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Name -->
          <div class="md:col-span-2">
            <label class="label-field">
              Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              @blur="validateField('name')"
              class="input-field"
              :class="{ 'input-error': errors.name && touched.name }"
              placeholder="Enter member name"
            />
            <span v-if="errors.name && touched.name" class="error-message">{{ errors.name }}</span>
          </div>

          <!-- Email -->
          <div>
            <label class="label-field">
              Email Address <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.email"
              type="email"
              @blur="validateField('email')"
              class="input-field"
              :class="{ 'input-error': errors.email && touched.email }"
              placeholder="member@example.com"
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

          <!-- Password -->
          <div>
            <label class="label-field">
              Password {{ member ? '(leave blank to keep current)' : '' }}
            </label>
            <input
              v-model="form.password"
              type="password"
              @blur="validateField('password')"
              class="input-field"
              :class="{ 'input-error': errors.password && touched.password }"
              :placeholder="member ? 'Enter new password' : 'Leave blank for default (password)'"
            />
            <span v-if="errors.password && touched.password" class="error-message">{{ errors.password }}</span>
            <p v-if="!member" class="text-xs text-gray-500 mt-1">Default password will be "password" if not provided</p>
          </div>

          <!-- Business Role -->
          <div>
            <label class="label-field">
              Role <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.business_role_id"
              @blur="validateField('business_role_id')"
              class="input-field"
              :class="{ 'input-error': errors.business_role_id && touched.business_role_id }"
            >
              <option value="">Select Role</option>
              <option v-for="role in roles" :key="role.id" :value="role.id">
                {{ role.name }}
              </option>
            </select>
            <span v-if="errors.business_role_id && touched.business_role_id" class="error-message">{{ errors.business_role_id }}</span>
          </div>

          <!-- Date of Birth -->
          <div>
            <label class="label-field">Date of Birth</label>
            <input
              v-model="form.date_of_birth"
              type="date"
              class="input-field"
            />
          </div>

          <!-- Gender -->
          <div>
            <label class="label-field">Gender</label>
            <select
              v-model="form.gender"
              class="input-field"
            >
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>

          <!-- Profile Picture -->
          <div class="md:col-span-2">
            <FileUpload
              v-model="form.profile_pic"
              label="Profile Picture"
              accept="image/*"
              accept-text="PNG, JPG, GIF up to 2MB"
              :error="errors.profile_pic && touched.profile_pic ? errors.profile_pic : ''"
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

          <!-- Address -->
          <div class="md:col-span-2">
            <label class="label-field">Address</label>
            <input
              v-model="form.address"
              type="text"
              @blur="validateField('address')"
              class="input-field"
              :class="{ 'input-error': errors.address && touched.address }"
              placeholder="Enter member address"
            />
            <span v-if="errors.address && touched.address" class="error-message">{{ errors.address }}</span>
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

          <!-- Description -->
          <div class="md:col-span-2">
            <label class="label-field">Description</label>
            <textarea
              v-model="form.description"
              rows="3"
              class="input-field resize-none"
              placeholder="Enter member description"
            ></textarea>
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
            <span>{{ loading ? 'Saving...' : 'Save Member' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useMemberStore } from '../stores/member';
import { useValidation } from '../composables/useValidation';
import LocationSelect from './LocationSelect.vue';
import FileUpload from './FileUpload.vue';
import axios from 'axios';

const props = defineProps({
  member: {
    type: Object,
    default: null,
  },
  businessId: {
    type: [String, Number],
    required: true,
  },
});

const emit = defineEmits(['close', 'saved']);

const memberStore = useMemberStore();
const { errors, touched, validateField, validateForm, clearErrors, setErrors } = useValidation();
const loading = ref(false);
const error = ref(null);
const roles = ref([]);
const location = reactive({
  country_id: null,
  state_id: null,
  city_id: null,
});

const form = reactive({
  business_id: props.businessId,
  business_role_id: '',
  name: '',
  email: '',
  phone: '',
  password: '',
  date_of_birth: '',
  gender: '',
  description: '',
  address: '',
  zip_code: '',
  status: 'active',
  profile_pic: null,
});

const validationRules = {
  name: ['required', 'min:2', 'max:255'],
  email: ['required', 'email', 'max:255'],
  phone: ['max:255'],
  business_role_id: ['required'],
  password: ['min:8'],
  address: ['max:255'],
  zip_code: ['max:255'],
};

onMounted(async () => {
  clearErrors();
  await loadRoles();
  
  if (props.member) {
    Object.assign(form, {
      business_id: props.businessId,
      business_role_id: props.member.business_role_id || '',
      name: props.member.name || '',
      email: props.member.email || '',
      phone: props.member.phone || '',
      date_of_birth: props.member.date_of_birth || '',
      gender: props.member.gender || '',
      description: props.member.description || '',
      address: props.member.address || '',
      zip_code: props.member.zip_code || '',
      status: props.member.status || 'active',
      profile_pic: props.member.profile_pic || null,
    });
    
    location.country_id = props.member.country_id;
    location.state_id = props.member.state_id;
    location.city_id = props.member.city_id;
  }
});

const loadRoles = async () => {
  try {
    const response = await axios.get('/api/business-roles');
    roles.value = response.data.data || [];
  } catch (error) {
    console.error('Failed to load roles', error);
  }
};

watch(location, (newVal) => {
  form.country_id = newVal.country_id;
  form.state_id = newVal.state_id;
  form.city_id = newVal.city_id;
}, { deep: true });

const handleSubmit = async () => {
  clearErrors();
  error.value = null;

  if (!validateForm(form, validationRules)) {
    return;
  }

  loading.value = true;

  try {
    const formData = new FormData();
    
    Object.keys(form).forEach(key => {
      if (form[key] !== null && form[key] !== '' && key !== 'profile_pic') {
        formData.append(key, form[key]);
      }
    });
    
    // Handle file upload
    if (form.profile_pic instanceof File) {
      formData.append('profile_pic', form.profile_pic);
    }

    if (props.member) {
      await memberStore.updateMember(props.member.id, formData);
    } else {
      await memberStore.createMember(formData);
    }
    emit('saved');
  } catch (err) {
    if (err.response?.data?.errors) {
      setErrors(err.response.data.errors);
    } else {
      error.value = err.message || 'Failed to save member';
    }
  } finally {
    loading.value = false;
  }
};
</script>
