<template>
  <div>
    <label v-if="label" class="label-field">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <input
      ref="dateTimeInput"
      type="datetime-local"
      :value="modelValue"
      @input="handleInput"
      @blur="$emit('blur')"
      :class="['input-field', { 'input-error': error }]"
      :placeholder="placeholder"
    />
    <span v-if="error" class="error-message">{{ error }}</span>
  </div>
</template>

<script setup>
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
});

const emit = defineEmits(['update:modelValue', 'blur']);

const handleInput = (event) => {
  emit('update:modelValue', event.target.value);
};
</script>

