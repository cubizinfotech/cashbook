<template>
    <div
        class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div
                class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center z-10">
                <h3 class="text-xl font-bold text-gray-900">{{ cashbook ? 'Edit Cashbook' : 'Create Cashbook' }}</h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="handleSubmit" class="p-6">
                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="label-field">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.title" type="text" @blur="validateField('title')" class="input-field"
                            :class="{ 'input-error': errors.title && touched.title }"
                            placeholder="Enter cashbook title" />
                        <span v-if="errors.title && touched.title" class="error-message">{{ errors.title }}</span>
                    </div>
                    <!-- Members -->
                    <div>
                        <SearchableSelect
                            v-model="form.member_ids"
                             :options="filteredMembers"
                            label="Members"
                            :multiple="true"
                            :disabled="membersLoading"
                            placeholder="Select members"
                            :no-options-text="membersLoading ? 'Loading members...' : 'No members available'"
                            track-by="id"
                            label-key="name"
                        />
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="label-field">Description</label>
                        <Ckeditor
                            :editor="editor"
                            :config="editorConfig"

                            v-model="form.description"
                            @blur="validateField('description')"
                        />
                    </div>
                </div>
                <div v-if="error" class="mt-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ error }}
                </div>

                <div class="flex justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
                    <button type="button" @click="$emit('close')" class="btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" :disabled="loading"
                        class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                        <span v-if="loading"
                            class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                        <span>{{ loading ? 'Saving...' : 'Save Cashbook' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useCashbookStore } from '../stores/cashbook';
import { useMemberStore } from '../stores/member';
import { useUserStore } from '../stores/user';
import { useValidation } from '../composables/useValidation';
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
    cashbook: {
        type: Object,
        default: null,
    },
    businessId: {
        type: [String, Number],
        required: true,
    },
});

const emit = defineEmits(['close', 'saved']);

const cashbookStore = useCashbookStore();
const memberStore = useMemberStore();
const userStore = useUserStore();
const { errors, touched, validateField, validateForm, clearErrors, setErrors } = useValidation();
const loading = ref(false);
const error = ref(null);
const membersLoading = ref(false);
const membersRaw = ref([]);

// Format members for SearchableSelect (add display name with email)
const members = computed(() => {
  return membersRaw.value.map(member => ({
    ...member,
    name: `${member.name} `
  }));
});
const filteredMembers = computed(() => {
    if (!userStore.member) return members.value;
    if (userStore.member?.role?.id === 3 || (userStore.member?.businessRole?.name || '').toLowerCase() === 'staff') {
        return members.value.filter(m => m.id === userStore.member.id);
    }
    return members.value;
});

const ensureUserLoaded = async () => {
  if (!userStore.user) {
    await userStore.fetchCurrentUser();
  }
};

const form = reactive({
    business_id: props.businessId,
    title: '',
    description: '',
    status: 'active',
    member_ids: [],
});

const validationRules = {
    title: ['required', 'min:2', 'max:255'],
};

onMounted(async () => {
    clearErrors();
    await ensureUserLoaded();
    // Fetch members for the business
    await fetchBusinessMembers();
    console.log("MEMBERS RAW FROM API:", membersRaw.value);
    if (props.cashbook) {
        Object.assign(form, {
            business_id: props.businessId,
            title: props.cashbook.title || '',
            description: props.cashbook.description || '',
            status: props.cashbook.status || 'active',
            member_ids: props.cashbook.members?.map(m => m.id) || [],
        });
    }
});

const fetchBusinessMembers = async () => {
    membersLoading.value = true;
    try {
        await memberStore.fetchMembers({
            business_id: props.businessId,
            per_page: 1000, // Get all members for the business
        });
        membersRaw.value = memberStore.members || [];
    } catch (err) {
        console.error('Failed to fetch members', err);
        membersRaw.value = [];
    } finally {
        membersLoading.value = false;
    }
};

const handleSubmit = async () => {
    clearErrors();
    error.value = null;
    if (!validateForm(form, validationRules)) {
        return;
    }
    loading.value = true;
    try {
        const formData = { ...form };
        Object.keys(formData).forEach(key => {
            if (formData[key] === '') {
                formData[key] = null;
            }
        });

        // Ensure member_ids is an array
        if (!Array.isArray(formData.member_ids)) {
            formData.member_ids = form.member_ids.map(m => m.id);
        }

        if (props.cashbook) {
            await cashbookStore.updateCashbook(props.cashbook.id, formData);
        } else {
            await cashbookStore.createCashbook(formData);
        }
        emit('saved');
    } catch (err) {
        if (err.response?.data?.errors) {
            setErrors(err.response.data.errors);
        } else {
            error.value = err.message || 'Failed to save cashbook';
        }
    } finally {
        loading.value = false;
    }
};
</script>
