<template>
  <div>
    <label v-if="label" class="label-field">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <input
        ref="dateTimeInput"
        type="text"
        :value="displayValue"
        @blur="$emit('blur')"
        :class="['input-field', { 'input-error': error }]"
        :placeholder="placeholder || 'Select date and time'"
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

const dateTimeInput = ref(null);
let flatpickrInstance = null;

const displayValue = computed(() => {
  if (!props.modelValue) return '';
  // Convert ISO string or datetime-local format to display format
  let date;
  if (props.modelValue.includes('T')) {
    // ISO format
    date = new Date(props.modelValue);
  } else if (props.modelValue.includes(' ')) {
    // Already in Y-m-d H:i format
    const [datePart, timePart] = props.modelValue.split(' ');
    const [year, month, day] = datePart.split('-');
    const [hours, minutes] = timePart.split(':');
    date = new Date(year, month - 1, day, hours, minutes);
  } else {
    // datetime-local format
    date = new Date(props.modelValue);
  }
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day} ${hours}:${minutes}`;
});

onMounted(() => {
  if (dateTimeInput.value) {
    // Convert modelValue to format flatpickr expects (YYYY-MM-DD HH:mm)
    let initialDate = null;
    if (props.modelValue) {
      let date;
      if (props.modelValue.includes('T')) {
        // ISO format
        date = new Date(props.modelValue);
      } else if (props.modelValue.includes(' ')) {
        // Already in Y-m-d H:i format
        const [datePart, timePart] = props.modelValue.split(' ');
        const [year, month, day] = datePart.split('-');
        const [hours, minutes] = timePart.split(':');
        date = new Date(year, month - 1, day, hours, minutes);
      } else {
        // datetime-local format
        date = new Date(props.modelValue);
      }
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      initialDate = `${year}-${month}-${day} ${hours}:${minutes}`;
    }

    flatpickrInstance = flatpickr(dateTimeInput.value, {
      enableTime: true,
      dateFormat: 'Y-m-d H:i',
      time_24hr: false,
      allowInput: false,
      clickOpens: true,
      defaultDate: initialDate || null,
      minDate: props.minDate || null,
      maxDate: props.maxDate || null,
      onChange: (selectedDates, dateStr) => {
        // Convert to datetime-local format for modelValue (YYYY-MM-DDTHH:mm)
        if (dateStr && selectedDates.length > 0) {
          const date = selectedDates[0];
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const day = String(date.getDate()).padStart(2, '0');
          const hours = String(date.getHours()).padStart(2, '0');
          const minutes = String(date.getMinutes()).padStart(2, '0');
          const datetimeLocal = `${year}-${month}-${day}T${hours}:${minutes}`;
          emit('update:modelValue', datetimeLocal);
        } else {
          emit('update:modelValue', '');
        }
      },
      onClose: () => {
        emit('blur');
      },
    });
  }
});

watch(() => props.modelValue, (newValue) => {
  if (flatpickrInstance && newValue) {
    let date;
    if (newValue.includes('T')) {
      // ISO or datetime-local format
      date = new Date(newValue);
    } else if (newValue.includes(' ')) {
      // Already in Y-m-d H:i format
      const [datePart, timePart] = newValue.split(' ');
      const [year, month, day] = datePart.split('-');
      const [hours, minutes] = timePart.split(':');
      date = new Date(year, month - 1, day, hours, minutes);
    } else {
      date = new Date(newValue);
    }
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const flatpickrFormat = `${year}-${month}-${day} ${hours}:${minutes}`;
    flatpickrInstance.setDate(flatpickrFormat, false);
  } else if (flatpickrInstance && !newValue) {
    flatpickrInstance.clear();
  }
});

onUnmounted(() => {
  if (flatpickrInstance) {
    flatpickrInstance.destroy();
  }
});
</script>

