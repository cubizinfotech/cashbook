<template>
  <div>
    <label v-if="label" class="label-field">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <input
        ref="dateInput"
        type="text"
        :value="displayValue"
        @blur="$emit('blur')"
        :class="['input-field', { 'input-error': error }]"
        :placeholder="placeholder || 'Select date'"
        readonly
      />
      <svg
        class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
        />
      </svg>
    </div>
    <span v-if="error" class="error-message">{{ error }}</span>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: '',
  },
  minDate: {
    type: String,
    default: null,
  },
  maxDate: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(['update:modelValue', 'blur']);

const dateInput = ref(null);
let flatpickrInstance = null;

const displayValue = computed(() => {
  if (!props.modelValue) return '';
  const date = new Date(props.modelValue);
  return date.toLocaleDateString('en-GB', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
});

onMounted(() => {
  if (dateInput.value) {
    flatpickrInstance = flatpickr(dateInput.value, {
      dateFormat: 'Y-m-d',
      altInput: false,
      allowInput: false,
      clickOpens: true,
      defaultDate: props.modelValue || null,
      minDate: props.minDate || null,
      maxDate: props.maxDate || null,
      onChange: (selectedDates, dateStr) => {
        emit('update:modelValue', dateStr);
      },
      onClose: () => {
        emit('blur');
      },
    });
  }
});

watch(() => props.modelValue, (newValue) => {
  if (flatpickrInstance) {
    flatpickrInstance.setDate(newValue || null, false);
  }
});

onUnmounted(() => {
  if (flatpickrInstance) {
    flatpickrInstance.destroy();
  }
});
</script>

