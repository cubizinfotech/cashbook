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
            <SearchableSelect
            v-model="form.business_role_id"
            :options="filteredRoles"
            label="Role"
            :required="true"
            placeholder="Select Role"
            @blur="validateField('business_role_id')"
            />
            <span v-if="errors.business_role_id && touched.business_role_id" class="error-message">
                {{ errors.business_role_id }}
            </span>
          </div>

          <!-- Date of Birth -->
          <div>
            <DatePicker
              v-model="form.date_of_birth"
              label="Date of Birth"
              placeholder="Select date of birth"
              @blur="validateField('date_of_birth')"
            />
            <span v-if="errors.date_of_birth && touched.date_of_birth" class="error-message">{{ errors.date_of_birth }}</span>
          </div>

          <!-- Gender -->
          <div>
            <SearchableSelect
              v-model="form.gender"
              :options="genderOptions"
              label="Gender"
              placeholder="Select Gender"
              track-by="value"
              label-key="label"
            />
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
               @update:model-value="updateLocation"
              :errors="errors"
              :touched="touched"
              :prefetched-countries="prefetchedCountries"
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

          <!-- Description -->
          <div class="">
            <label class="label-field">Description</label>

            <Ckeditor
                :editor="editor"
                v-model="form.description"
                :config="editorConfig"

                @blur="validateField('description')"
            />
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
import { ref, reactive, onMounted, watch, computed } from 'vue';
import { useMemberStore } from '../stores/member';
import { useValidation } from '../composables/useValidation';
import LocationSelect from './LocationSelect.vue';
import FileUpload from './FileUpload.vue';
import DatePicker from './DatePicker.vue';
import SearchableSelect from './SearchableSelect.vue';
import axios from 'axios';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import { useBusinessStore } from '../stores/business';
import { useUserStore } from '../stores/user';
const businessStore = useBusinessStore();
const editor = ClassicEditor;
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
  member: {
    type: Object,
    default: null,
  },
  businessId: {
    type: [String, Number],
    required: true,
  },
  createData: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close', 'saved']);
const memberStore = useMemberStore();
const userStore = useUserStore();
const { errors, touched, validateField, validateForm, clearErrors, setErrors } = useValidation();
const loading = ref(false);
const error = ref(null);
const roles = ref([]);
const prefetchedCountries = ref([]);
const location = reactive({
  country_id: null,
  state_id: null,
  city_id: null,
});

const businessMember = computed(() => {
  const currentBusiness = businessStore.currentBusiness;
  if (!currentBusiness || !userStore.user) return null;
  return currentBusiness.members?.find(
    (m) => m.business_id == props.businessId && m.user_id === userStore.user.id
  ) || null;
});

const genderOptions = [
  { value: 'male', label: 'Male' },
  { value: 'female', label: 'Female' },
  { value: 'other', label: 'Other' },
];

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
  phone: ['numeric', 'min:10', 'max:10'],
  business_role_id: ['required'],
  password: ['min:8'],
  address: ['max:255'],
  zip_code: ['max:255'],
  date_of_birth: ['date', 'not_future'], // <-- add this

};
const currentRoleName = computed(() => {
  const memberRole = userStore.member?.businessRole?.name || userStore.member?.role?.name;
  const businessRole = businessMember.value?.business_role?.name || businessMember.value?.businessRole?.name;
  return (memberRole || businessRole || '').toLowerCase();
});

const filteredRoles = computed(() => {
  const all = Array.isArray(roles.value) ? roles.value : [];
  const roleName = currentRoleName.value;
  let result = all;

  if (roleName === 'owner') {
    result = all.filter(r => ['partner', 'staff'].includes((r.name || '').toLowerCase()));
  } else if (roleName === 'partner') {
    result = all.filter(r => (r.name || '').toLowerCase() === 'staff');
  } else if (!roleName || roleName === 'creator') {
    result = all;
  }

  const editingRoleId = props.member ? Number(props.member.business_role_id) : null;
  if (editingRoleId && !result.some(r => Number(r.id) === editingRoleId)) {
    const editingRole = all.find(r => Number(r.id) === editingRoleId);
    if (editingRole) {
      result = [...result, editingRole];
    }
  }

  return result;
});

const ensureUserLoaded = async () => {
  if (!userStore.user) {
    await userStore.fetchCurrentUser();
  }
};

const loadCreateMeta = async () => {
  if (props.createData) {
    roles.value = props.createData.roles || props.createData.business_roles || [];
    prefetchedCountries.value = props.createData.countries || [];
    return;
  }
  try {
    const meta = await memberStore.fetchCreateData({ business_id: props.businessId });
    roles.value = meta?.roles || meta?.business_roles || [];
    prefetchedCountries.value = meta?.countries || [];
  } catch (error) {
    await loadRolesFallback();
  }
};

const loadRolesFallback = async () => {
  try {
    const response = await axios.get('/api/business-roles');
    roles.value = response.data.data || [];
  } catch (error) {
    console.error('Failed to load roles', error);
  }
};

onMounted(async () => {
  clearErrors();
  await ensureUserLoaded();
  await loadCreateMeta();
  if (props.member) {
    form.business_role_id = props.member.business_role_id;
  } else if (businessMember.value) {
    form.business_role_id = businessMember.value.business_role_id;
  }
  if (props.member) {
    Object.assign(form, {
      business_id: props.businessId,
      business_role_id: props.member.business_role_id || '',
      name: props.member.name || '',
      email: getUserEmail(props.member.user_id),   // <-- FIXED
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
const updateLocation = (newLocation) => {
  location.country_id = newLocation.country_id;
  location.state_id = newLocation.state_id;
  location.city_id = newLocation.city_id;
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
  form.country_id = location.country_id !== null && location.country_id !== '' ? location.country_id : '';
  form.state_id = location.state_id !== null && location.state_id !== '' ? location.state_id : '';
  form.city_id = location.city_id !== null && location.city_id !== '' ? location.city_id : '';
  if (!validateForm(form, validationRules)) {
    return;
  }
  loading.value = true;
  try {
    const formData = new FormData();
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
     console.log("ERROR RESPONSE:", err);
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
const getUserEmail = (userId) => {
  const users = businessStore.currentBusiness?.users || [];
  const u = users.find(x => x.id === userId);
  return u ? u.email : '';
};

</script>
