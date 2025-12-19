<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Country -->
    <div class="mb-4">
      <SearchableSelect
        v-model="selectedCountryId"
        :options="countries"
        label="Country"
        :required="required"
        :error="errors.country_id && touched.country_id ? errors.country_id : ''"
        placeholder="Select Country"
        :preselect-first="false"
        :disabled="loadingCountries"
        track-by="id"
        label-key="name"
        @update:model-value="handleCountryChange"
        @blur="$emit('blur')"
      />
      <span v-if="errors.country_id && touched.country_id" class="error-message">{{ errors.country_id }}</span>
    </div>

    <!-- State -->
    <div class="mb-4" v-if="selectedCountryId">
      <SearchableSelect
        v-model="selectedStateId"
        :options="states"
        label="State"
        :required="required"
        :error="errors.state_id && touched.state_id ? errors.state_id : ''"
        placeholder="Select State"
        :disabled="!selectedCountryId || loadingStates"
        :no-options-text="loadingStates ? 'Loading...' : 'No states available'"
        track-by="id"
        label-key="name"
        @update:model-value="handleStateChange"
        @blur="$emit('blur')"
      />
      <span v-if="errors.state_id && touched.state_id" class="error-message">{{ errors.state_id }}</span>
    </div>

    <!-- City -->
    <div class="mb-4" v-if="selectedStateId">
      <SearchableSelect
        v-model="selectedCityId"
        :options="cities"
        label="City"
        :required="required"
        :error="errors.city_id && touched.city_id ? errors.city_id : ''"
        placeholder="Select City"
        :disabled="!selectedStateId || loadingCities"
        :no-options-text="loadingCities ? 'Loading...' : 'No cities available'"
        track-by="id"
        label-key="name"
        @update:model-value="handleCityChange"
        @blur="$emit('blur')"
      />
      <span v-if="errors.city_id && touched.city_id" class="error-message">{{ errors.city_id }}</span>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import SearchableSelect from './SearchableSelect.vue';

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
  prefetchedCountries: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['update:modelValue', 'blur']);

const countries = ref([]);
const states = ref([]);
const cities = ref([]);
const selectedCountryId = ref(props.modelValue?.country_id || null);
const selectedStateId = ref(props.modelValue?.state_id || null);
const selectedCityId = ref(props.modelValue?.city_id || null);
const loadingCountries = ref(false);
const loadingStates = ref(false);
const loadingCities = ref(false);

onMounted(async () => {
  await loadCountries();

  // After countries are loaded, check if we have initial values
  const countryId = props.modelValue?.country_id;
  const stateId = props.modelValue?.state_id;
  const cityId = props.modelValue?.city_id;

  if (countryId) {
    selectedCountryId.value = countryId;
    await loadStates(countryId);

    if (stateId) {
      selectedStateId.value = stateId;
      await loadCities(stateId);

      if (cityId) {
        selectedCityId.value = cityId;
      }
    }
  }
});

const loadCountries = async () => {
  if (props.prefetchedCountries && props.prefetchedCountries.length) {
    countries.value = props.prefetchedCountries;
    return;
  }
  loadingCountries.value = true;
  try {
    const response = await axios.get('/api/countries');
    countries.value = response.data.data || [];
  } catch (error) {
    console.error('Failed to load countries', error);
  } finally {
    loadingCountries.value = false;
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

const handleCountryChange = async (countryId) => {
  selectedCountryId.value = countryId;
  selectedStateId.value = null;
  selectedCityId.value = null;
  states.value = [];
  cities.value = [];

  if (countryId) {
    await loadStates(countryId);
  }

  updateValue();
};

const handleStateChange = async (stateId) => {
  selectedStateId.value = stateId;
  selectedCityId.value = null;
  cities.value = [];

  if (stateId) {
    await loadCities(stateId);
  }

  updateValue();
};

const handleCityChange = () => {
  updateValue();
};

const updateValue = () => {
  emit('update:modelValue', {
    country_id: selectedCountryId.value || null,
    state_id: selectedStateId.value || null,
    city_id: selectedCityId.value || null,
  });
};

watch(() => props.prefetchedCountries, (val) => {
  if (val && val.length) {
    countries.value = val;
  }
});

watch(() => props.modelValue, async (newVal) => {
  if (!newVal) return;

  const countryId = newVal.country_id || null;
  const stateId = newVal.state_id || null;
  const cityId = newVal.city_id || null;

  // Only update if values have actually changed
  if (countryId && countryId !== selectedCountryId.value) {
    selectedCountryId.value = countryId;
    await loadStates(countryId);

    // If state is provided, load it
    if (stateId) {
      selectedStateId.value = stateId;
      await loadCities(stateId);

      // If city is provided, set it
      if (cityId) {
        selectedCityId.value = cityId;
      }
    }
  } else if (!countryId && selectedCountryId.value) {
    // Clear all if country is removed
    selectedCountryId.value = null;
    selectedStateId.value = null;
    selectedCityId.value = null;
    states.value = [];
    cities.value = [];
    updateValue();
  } else if (countryId && stateId && stateId !== selectedStateId.value) {
    // Country unchanged but state changed
    selectedStateId.value = stateId;
    await loadCities(stateId);

    if (cityId) {
      selectedCityId.value = cityId;
    }
    updateValue();
  } else if (stateId && cityId && cityId !== selectedCityId.value) {
    // State unchanged but city changed
    selectedCityId.value = cityId;
    updateValue();
  }
}, { deep: true });
</script>

