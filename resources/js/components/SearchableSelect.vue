<template>
  <div>
    <label v-if="label" class="label-field">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <multiselect
      v-model="selectedValue"
      :options="options"
      :multiple="multiple"
      :searchable="true"
      :allow-empty="!required"
      :placeholder="placeholder || 'Select an option'"
      :disabled="disabled"
      :class="['multiselect-custom', { 'multiselect-error': error }]"
      :track-by="trackBy"
      :label="labelKey"
      @update:model-value="handleChange"
      @blur="$emit('blur')"
    >
      <template #noOptions>
        <span>{{ noOptionsText || 'No options available' }}</span>
      </template>
      <template #noResult>
        <span>{{ noResultText || 'No elements found. Consider changing the search query.' }}</span>
      </template>
    </multiselect>
    <span v-if="error" class="error-message">{{ error }}</span>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

const props = defineProps({
  modelValue: {
    type: [String, Number, Array, Object],
    default: null,
  },
  options: {
    type: Array,
    required: true,
    default: () => [],
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
  disabled: {
    type: Boolean,
    default: false,
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  trackBy: {
    type: String,
    default: 'id',
  },
  labelKey: {
    type: String,
    default: 'name',
  },
  noOptionsText: {
    type: String,
    default: '',
  },
  noResultText: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue', 'blur']);

const selectedValue = ref(props.modelValue);

const handleChange = (value) => {
  if (props.multiple) {
    // For multiple select, emit array of IDs
    const ids = value ? value.map(item => {
      return typeof item === 'object' ? item[props.trackBy] : item;
    }) : [];
    emit('update:modelValue', ids);
  } else {
    // For single select, emit the ID
    const id = value ? (typeof value === 'object' ? value[props.trackBy] : value) : null;
    emit('update:modelValue', id);
  }
};

watch(() => props.modelValue, (newValue) => {
  if (props.multiple) {
    // For multiple, find matching options
    if (Array.isArray(newValue) && newValue.length > 0) {
      selectedValue.value = props.options.filter(opt => {
        const optId = typeof opt === 'object' ? opt[props.trackBy] : opt;
        return newValue.includes(optId);
      });
    } else {
      selectedValue.value = [];
    }
  } else {
    // For single, find matching option
    if (newValue !== null && newValue !== '') {
      const found = props.options.find(opt => {
        const optId = typeof opt === 'object' ? opt[props.trackBy] : opt;
        return optId === newValue;
      });
      selectedValue.value = found || null;
    } else {
      selectedValue.value = null;
    }
  }
}, { immediate: true });

watch(() => props.options, () => {
  // Re-sync when options change
  if (props.multiple) {
    if (Array.isArray(props.modelValue) && props.modelValue.length > 0) {
      selectedValue.value = props.options.filter(opt => {
        const optId = typeof opt === 'object' ? opt[props.trackBy] : opt;
        return props.modelValue.includes(optId);
      });
    } else {
      selectedValue.value = [];
    }
  } else {
    if (props.modelValue !== null && props.modelValue !== '') {
      const found = props.options.find(opt => {
        const optId = typeof opt === 'object' ? opt[props.trackBy] : opt;
        return optId === props.modelValue;
      });
      selectedValue.value = found || null;
    } else {
      selectedValue.value = null;
    }
  }
});
</script>

<style>
.multiselect-custom {
  min-height: 42px;
}

/* Main field */
.multiselect-custom .multiselect__tags {
  min-height: 42px;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem; /* smoother corners */
  padding: 0.5rem 2.5rem 0.5rem 0.75rem;
  background: #ffffff;
  transition: all 0.2s ease-in-out;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

/* Focus */
.multiselect-custom .multiselect__tags:focus-within {
  border-color: #0ea5e9;
  box-shadow: 0 0 0 3px rgba(14,165,233,0.3);
}

/* Error */
.multiselect-error .multiselect__tags {
  border-color: #ef4444;
  box-shadow: 0 0 0 2px rgba(239,68,68,0.2);
}

/* Placeholder */
.multiselect-custom .multiselect__placeholder {
  color: #9ca3af;
  padding-top: 0;
  margin-bottom: 0;
  font-size: 0.875rem;
}

/* Selected item */
.multiselect-custom .multiselect__single {
  margin-bottom: 0;
  padding-top: 0;
  font-size: 0.875rem;
  color: #111827;
}

/* Input text inside select */
.multiselect-custom .multiselect__input {
  font-size: 0.875rem;
  line-height: 1.5rem;
}

/* Dropdown icon */
.multiselect-custom .multiselect__select {
  height: 40px;
  right: 6px;
  top: 2px;
  transition: transform 0.2s;
}

.multiselect-custom .multiselect__select:before {
  border-color: #6b7280 transparent transparent;
  border-width: 6px 6px 0;
}

/* Rotate icon when open */
.multiselect-custom.multiselect--active .multiselect__select:before {
  transform: rotate(180deg);
}

</style>

