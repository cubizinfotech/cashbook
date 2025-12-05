<template>
  <div>
    <div class="mb-6">
      <router-link
        to="/businesses"
        class="text-sky-600 hover:text-sky-800 text-sm font-medium mb-4 inline-flex items-center"
      >
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Businesses
      </router-link>
    </div>

    <div v-if="businessStore.loading" class="bg-white rounded-lg shadow p-12 text-center">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
      <p class="text-gray-500 mt-4">Loading business details...</p>
    </div>

    <div v-else-if="businessStore.error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
      {{ businessStore.error }}
    </div>

    <div v-else-if="business" class="space-y-6" >
      <!-- Business Header -->
      <div class="card p-6" >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <div class="flex items-center space-x-3 mb-4">
              <h1 class="text-3xl font-bold text-gray-900">{{ business.name }}</h1>
              <span
                :class="{
                  'bg-emerald-100 text-emerald-800': business.status === 'active',
                  'bg-red-100 text-red-800': business.status === 'inactive',
                  'bg-yellow-100 text-yellow-800': business.status === 'pending',
                  'bg-gray-100 text-gray-800': business.status === 'suspended'
                }"
                class="px-3 py-1 text-sm font-medium rounded-full">
                {{ business.status }}
              </span>
            </div>
            <p class="text-gray-600 mb-6">{{ business.description || 'No description provided' }}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div v-if="business.email" class="flex items-center text-gray-600">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>{{ business.email }}</span>
              </div>
              <div v-if="business.phone" class="flex items-center text-gray-600">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <span>{{ business.phone }}</span>
              </div>
              <div v-if="business.address" class="flex items-start text-gray-600 md:col-span-2">
                <svg class="w-5 h-5 mr-2 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>{{ business.address }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Members Section -->
      <div v-for="member in business.members" :key="member.id"class="card" >
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center" v-if="member.business_role_id === 1  || member.business_role_id === 2">
          <h2 class="text-lg font-semibold text-gray-900">Members</h2>

          <button
            @click="showMemberForm = true"
            class="btn-primary text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Member</span>
          </button>
        </div>

        <div v-if="business.members && business.members.length > 0 " class="divide-y divide-gray-200">
          <div v-for="member in business.members" :key="member.id" class="px-6 py-4 hover:bg-gray-50 transition-colors" v-if="member.business_role_id === 1  || member.business_role_id === 2">
            <div class="flex justify-between items-center" >
              <div class="flex-1">
                <p class="font-medium text-gray-900">{{ member.name }}( {{ getRoleName(member.business_role_id) }})</p>
                <p class="text-sm text-gray-500 mt-1">
                    {{ getUserEmailForMember(member) }}
                </p>
                <p v-if="member.phone" class="text-sm text-gray-500">{{ member.phone }}</p>
              </div>
              <div class="flex space-x-3">
                <button v-if="!user?.member?.role?.name ||member.business_role_id === 1  || member.business_role_id === 2"
                  @click="editMember(member)"
                  class="text-sky-600 hover:text-sky-800 text-sm font-medium transition-colors"
                >
                  Edit
                </button>
                <button v-if="!user?.member?.role?.name ||member.business_role_id === 1"
                  @click="confirmDeleteMember(member)"
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="px-6 py-12 text-center text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          <p>No members yet. Add your first member!</p>
        </div>
      </div>

      <!-- Cashbooks Section -->
      <div class="card">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h2 class="text-lg font-semibold text-gray-900">Cashbooks</h2>
          <button
            @click="showCashbookForm = true"
            class="btn-primary text-sm"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Cashbook</span>
          </button>
        </div>

        <div v-if="business.cashbooks && business.cashbooks.length > 0" class="divide-y divide-gray-200">
          <div v-for="cashbook in business.cashbooks" :key="cashbook.id" class="px-6 py-4 hover:bg-gray-50 transition-colors">
            <div class="flex justify-between items-center">
              <div class="flex-1">
                <p class="font-medium text-gray-900">{{ cashbook.title }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ cashbook.description || 'No description' }}</p>
              </div>
              <div class="flex space-x-3">
                <button
                  @click="viewCashbook(cashbook)"
                  class="text-sky-600 hover:text-sky-800 text-sm font-medium transition-colors"
                >
                  View
                </button>
                <button
                  @click="editCashbook(cashbook)"
                  class="text-sky-600 hover:text-sky-800 text-sm font-medium transition-colors"
                >
                  Edit
                </button>
              <template v-for="member in business.members" :key="member.id">
                <button
                    v-if="member.business_role_id === 1 || member.business_role_id === 2"
                    @click="confirmDeleteCashbook(cashbook)"
                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                >
                    Delete
                </button>
                </template>

              </div>
            </div>
          </div>
        </div>
        <div v-else class="px-6 py-12 text-center text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p>No cashbooks yet. Add your first cashbook!</p>
        </div>
      </div>
    </div>

    <!-- Member Form Modal -->
    <MemberForm
      v-if="showMemberForm"
      :member="editingMember"
      :business-id="business?.id"
      @close="closeMemberForm"
      @saved="handleMemberSaved"
    />

    <!-- Cashbook Form Modal -->
    <CashbookForm
      v-if="showCashbookForm"
      :cashbook="editingCashbook"
      :business-id="business?.id"
      @close="closeCashbookForm"
      @saved="handleCashbookSaved"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useBusinessStore } from '../stores/business';
import { useMemberStore } from '../stores/member';
import { useCashbookStore } from '../stores/cashbook';
import MemberForm from '../components/MemberForm.vue';
import CashbookForm from '../components/CashbookForm.vue';

const props = defineProps({
  id: {
    type: [String, Number],
    required: true,
  },
});
const roles = ref([]);
const loadRoles = async () => {
  try {
    const response = await axios.get('/api/business-roles');
    roles.value = response.data.data || [];
  } catch (error) {
    console.error('Failed to load roles', error);
  }
};
const router = useRouter();
const businessStore = useBusinessStore();
const memberStore = useMemberStore();
const cashbookStore = useCashbookStore();
const showMemberForm = ref(false);
const showCashbookForm = ref(false);
const editingMember = ref(null);
const editingCashbook = ref(null);
const business = computed(() => businessStore.currentBusiness);
onMounted(async () => {
    console.log('id'.id);
  await businessStore.fetchBusiness(props.id);
});
const editMember = (member) => {
  editingMember.value = member;
  showMemberForm.value = true;
};

const user = ref(null);
onMounted(async () => {
  try {
    const response = await axios.get('/api/user');
    user.value = response.data;
    console.log(response.data);

      await loadRoles(); // <-- LOAD ROLES HERE

  } catch (error) {
    console.error('Failed to fetch user', error);
  }
});
const confirmDeleteMember = async (member) => {
  if (confirm(`Are you sure you want to delete "${member.name}"? This action cannot be undone.`)) {
    try {
      await memberStore.deleteMember(member.id);
      await businessStore.fetchBusiness(props.id);
    } catch (error) {
      alert('Failed to delete member. Please try again.');
    }
  }
};
// console.log("Role ID:", roles);
const viewCashbook = (cashbook) => {
  router.push(`/cashbooks/${cashbook.id}`);
};
const editCashbook = (cashbook) => {
  editingCashbook.value = cashbook;
  showCashbookForm.value = true;
};
const confirmDeleteCashbook = async (cashbook) => {
  if (confirm(`Are you sure you want to delete "${cashbook.title}"? This action cannot be undone.`)) {
    try {
      await cashbookStore.deleteCashbook(cashbook.id);
      await businessStore.fetchBusiness(props.id);
    } catch (error) {
      alert('Failed to delete cashbook. Please try again.');
    }
  }
};
const closeMemberForm = () => {
  showMemberForm.value = false;
  editingMember.value = null;
};

const closeCashbookForm = () => {
  showCashbookForm.value = false;
  editingCashbook.value = null;
};

const handleMemberSaved = async () => {
  closeMemberForm();
  await businessStore.fetchBusiness(props.id);
};

const handleCashbookSaved = async () => {
  closeCashbookForm();
  await businessStore.fetchBusiness(props.id);
};
const getRoleName = (roleId) => {
  const role = roles.value.find(r => r.id === roleId);
  return role ? role.name : "Unknown";
};
const getUserEmailForMember = (member) => {
  const users = businessStore.currentBusiness?.users || [];
  const matchedUser = users.find(u => u.id === member.user_id);
  return matchedUser ? matchedUser.email : "No email";
};


</script>
