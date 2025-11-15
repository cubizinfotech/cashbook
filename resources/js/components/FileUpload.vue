<template>
  <div>
    <label class="label-field">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <div
      @drop.prevent="handleDrop"
      @dragover.prevent="dragover = true"
      @dragleave.prevent="dragover = false"
      :class="[
        'border-2 border-dashed rounded-lg p-6 text-center transition-colors cursor-pointer',
        dragover ? 'border-sky-500 bg-sky-50' : 'border-gray-300 hover:border-gray-400',
        error ? 'border-red-300' : ''
      ]"
    >
      <input
        ref="fileInput"
        type="file"
        :accept="accept"
        @change="handleFileSelect"
        class="hidden"
      />
      
      <div v-if="!preview && !file">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <div class="mt-4">
          <button
            type="button"
            @click="$refs.fileInput.click()"
            class="text-sky-600 hover:text-sky-800 font-medium"
          >
            Click to upload
          </button>
          <span class="text-gray-500"> or drag and drop</span>
        </div>
        <p class="text-xs text-gray-500 mt-2">{{ acceptText }}</p>
      </div>
      
      <div v-else class="space-y-4">
        <img v-if="preview" :src="preview" alt="Preview" class="mx-auto h-32 w-32 object-cover rounded-lg" />
        <div>
          <p class="text-sm font-medium text-gray-900">{{ file?.name || 'File selected' }}</p>
          <button
            type="button"
            @click="removeFile"
            class="mt-2 text-sm text-red-600 hover:text-red-800"
          >
            Remove
          </button>
        </div>
      </div>
    </div>
    
    <span v-if="error" class="error-message">{{ error }}</span>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: [File, String, null],
    default: null,
  },
  label: {
    type: String,
    default: 'Upload File',
  },
  accept: {
    type: String,
    default: 'image/*',
  },
  acceptText: {
    type: String,
    default: 'PNG, JPG, GIF up to 2MB',
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

const emit = defineEmits(['update:modelValue']);

const fileInput = ref(null);
const file = ref(null);
const preview = ref(null);
const dragover = ref(false);

const handleFileSelect = (event) => {
  const selectedFile = event.target.files[0];
  if (selectedFile) {
    processFile(selectedFile);
  }
};

const handleDrop = (event) => {
  dragover.value = false;
  const droppedFile = event.dataTransfer.files[0];
  if (droppedFile) {
    processFile(droppedFile);
  }
};

const processFile = (selectedFile) => {
  // Validate file size (2MB)
  if (selectedFile.size > 2 * 1024 * 1024) {
    alert('File size must be less than 2MB');
    return;
  }
  
  file.value = selectedFile;
  emit('update:modelValue', selectedFile);
  
  // Create preview for images
  if (selectedFile.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.value = e.target.result;
    };
    reader.readAsDataURL(selectedFile);
  }
};

const removeFile = () => {
  file.value = null;
  preview.value = null;
  emit('update:modelValue', null);
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

watch(() => props.modelValue, (newVal) => {
  if (typeof newVal === 'string' && newVal) {
    // Existing file URL
    preview.value = newVal.startsWith('http') ? newVal : `/storage/${newVal}`;
  } else if (!newVal) {
    file.value = null;
    preview.value = null;
  }
});
</script>

