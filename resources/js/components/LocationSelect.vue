<template>
  <div>
    <!-- Country -->
    <div class="mb-4">
      <label class="label-field">
        Country <span v-if="required" class="text-red-500">*</span>
      </label>
      <select
        v-model="selectedCountry"
        @change="handleCountryChange"
        :class="['input-field', { 'input-error': errors.country_id && touched.country_id }]"
      >
        <option value="">Select Country</option>
        <option v-for="country in countries" :key="country.id" :value="country.id">
          {{ country.name }}
        </option>
      </select>
      <span v-if="errors.country_id && touched.country_id" class="error-message">{{ errors.country_id }}</span>
    </div>

    <!-- State -->
    <div class="mb-4" v-if="selectedCountry">
      <label class="label-field">
        State <span v-if="required" class="text-red-500">*</span>
      </label>
      <select
        v-model="selectedState"
        @change="handleStateChange"
        :disabled="!selectedCountry || loadingStates"
        :class="['input-field', { 'input-error': errors.state_id && touched.state_id }]"
      >
        <option value="">{{ loadingStates ? 'Loading...' : 'Select State' }}</option>
        <option v-for="state in states" :key="state.id" :value="state.id">
          {{ state.name }}
        </option>
      </select>
      <span v-if="errors.state_id && touched.state_id" class="error-message">{{ errors.state_id }}</span>
    </div>

    <!-- City -->
    <div class="mb-4" v-if="selectedState">
      <label class="label-field">
        City <span v-if="required" class="text-red-500">*</span>
      </label>
      <select
        v-model="selectedCity"
        @change="handleCityChange"
        :disabled="!selectedState || loadingCities"
        :class="['input-field', { 'input-error': errors.city_id && touched.city_id }]"
      >
        <option value="">{{ loadingCities ? 'Loading...' : 'Select City' }}</option>
        <option v-for="city in cities" :key="city.id" :value="city.id">
          {{ city.name }}
        </option>
      </select>
      <span v-if="errors.city_id && touched.city_id" class="error-message">{{ errors.city_id }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({ country_id: null, state_id: null, city_id: null }),
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  touched: {
    type: Object,
    default: () => ({}),
  },
  required: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue']);

const countries = ref([]);
const states = ref([]);
const cities = ref([]);
const selectedCountry = ref(props.modelValue.country_id || '');
const selectedState = ref(props.modelValue.state_id || '');
const selectedCity = ref(props.modelValue.city_id || '');
const loadingStates = ref(false);
const loadingCities = ref(false);

onMounted(async () => {
  await loadCountries();
  if (selectedCountry.value) {
    await loadStates(selectedCountry.value);
    if (selectedState.value) {
      await loadCities(selectedState.value);
    }
  }
});

const loadCountries = async () => {
  try {
    const response = await axios.get('/api/countries');
    countries.value = response.data.data || [];
  } catch (error) {
    console.error('Failed to load countries', error);
  }
};

const loadStates = async (countryId) => {
  if (!countryId) return;
  loadingStates.value = true;
  try {
    const response = await axios.get('/api/states', { params: { country_id: countryId } });
    states.value = response.data.data || [];
  } catch (error) {
    console.error('Failed to load states', error);
  } finally {
    loadingStates.value = false;
  }
};

const loadCities = async (stateId) => {
  if (!stateId) return;
  loadingCities.value = true;
  try {
    const response = await axios.get('/api/cities', { params: { state_id: stateId } });
    cities.value = response.data.data || [];
  } catch (error) {
    console.error('Failed to load cities', error);
  } finally {
    loadingCities.value = false;
  }
};

const handleCountryChange = async () => {
  selectedState.value = '';
  selectedCity.value = '';
  states.value = [];
  cities.value = [];
  
  if (selectedCountry.value) {
    await loadStates(selectedCountry.value);
  }
  
  updateValue();
};

const handleStateChange = async () => {
  selectedCity.value = '';
  cities.value = [];
  
  if (selectedState.value) {
    await loadCities(selectedState.value);
  }
  
  updateValue();
};

const handleCityChange = () => {
  updateValue();
};

const updateValue = () => {
  emit('update:modelValue', {
    country_id: selectedCountry.value || null,
    state_id: selectedState.value || null,
    city_id: selectedCity.value || null,
  });
};

watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    selectedCountry.value = newVal.country_id || '';
    selectedState.value = newVal.state_id || '';
    selectedCity.value = newVal.city_id || '';
  }
}, { deep: true });
</script>

