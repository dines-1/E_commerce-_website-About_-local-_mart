const { ref, reactive, computed, onMounted } = Vue;

const categoriesSection = {
  setup() {
    const categories = ref([]);
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const deletingCategory = ref(null);

    const form = reactive({
      id: null,
      category_name: '',
      image_url: '',
      display_order: 0
    });

    const toast = reactive({
      show: false,
      title: '',
      message: '',
      type: 'success'
    });

    const toastClass = computed(() => ({
      'bg-green-600': toast.type === 'success',
      'bg-red-600': toast.type === 'error',
      'bg-orange-600': toast.type === 'warning'
    }));

    const toastIcon = computed(() => ({
      'fas fa-check-circle': toast.type === 'success',
      'fas fa-exclamation-circle': toast.type === 'error',
      'fas fa-exclamation-triangle': toast.type === 'warning'
    }));

    const loadCategories = async () => {
      try {
        const res = await axios.get('includes/categories.php?action=get_all');
        categories.value = res.data;
      } catch (err) {
        showToast('Error', 'Failed to load categories', 'error');
      }
    };

    const openAdd = () => {
      isEditing.value = false;
      resetForm();
      showModal.value = true;
    };

    const edit = (cat) => {
      isEditing.value = true;
      Object.assign(form, cat);
      showModal.value = true;
    };

    const resetForm = () => {
      form.id = null;
      form.category_name = '';
      form.image_url = '';
      form.display_order = 0;
    };

    const save = async () => {
      if (!form.category_name.trim()) {
        showToast('Error', 'Category name is required', 'error');
        return;
      }

      const data = new FormData();
      data.append('action', isEditing.value ? 'update' : 'create');
      data.append('category_name', form.category_name);
      data.append('image_url', form.image_url);
      data.append('display_order', form.display_order);
      if (isEditing.value) data.append('id', form.id);

      try {
        const res = await axios.post('includes/categories.php', data);
        if (res.data.success) {
          showToast('Success', `Category ${isEditing.value ? 'updated' : 'created'}!`, 'success');
          showModal.value = false;
          loadCategories();
        }
      } catch (err) {
        showToast('Error', err.response?.data?.message || 'Save failed', 'error');
      }
    };

    const confirmDelete = (cat) => {
      deletingCategory.value = cat;
      showDeleteModal.value = true;
    };

    const deleteCategory = async () => {
      try {
        const res = await axios.post('includes/categories.php', {
          action: 'delete',
          id: deletingCategory.value.id
        });
        if (res.data.success) {
          showToast('Success', 'Category deleted', 'success');
          showDeleteModal.value = false;
          loadCategories();
        } else {
          showToast('Error', res.data.message, 'error');
        }
      } catch (err) {
        showToast('Error', 'Delete failed', 'error');
      }
    };

    const showToast = (title, message, type = 'success') => {
      toast.title = title;
      toast.message = message;
      toast.type = type;
      toast.show = true;
      setTimeout(() => toast.show = false, 4000);
    };

    const formatDate = (date) => new Date(date).toLocaleDateString('en-US', {
      year: 'numeric', month: 'short', day: 'numeric'
    });

    onMounted(loadCategories);

    return {
      categories, showModal, showDeleteModal, isEditing, deletingCategory,
      form, toast, toastClass, toastIcon,
      openAdd, edit, save, confirmDelete, deleteCategory, formatDate
    };
  },

  template: `
    <div class="max-w-7xl mx-auto">

      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Categories Management</h1>
        <button @click="openAdd" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center gap-2">
          <i class="fas fa-plus"></i> Add Category
        </button>
      </div>

      <!-- Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="c in categories" :key="c.id" class="bg-white rounded-xl shadow hover:shadow-lg transition">
          <div class="h-48 bg-gray-200 relative overflow-hidden">
            <img v-if="c.image_url" :src="c.image_url" class="w-full h-full object-cover">
            <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
              <i class="fas fa-tag text-6xl text-gray-300"></i>
            </div>
            <div class="absolute top-3 right-3 flex gap-2">
              <button @click="edit(c)" class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="confirmDelete(c)" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
          <div class="p-5">
            <h3 class="font-semibold text-lg">{{ c.category_name }}</h3>
            <div class="text-sm text-gray-500 mt-2 flex justify-between">
              <span>Order: {{ c.display_order }}</span>
              <span>{{ formatDate(c.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="categories.length === 0" class="text-center py-20">
        <i class="fas fa-tags text-8xl text-gray-300 mb-6"></i>
        <p class="text-2xl text-gray-500">No categories found</p>
        <button @click="openAdd" class="mt-6 bg-green-600 text-white px-8 py-3 rounded-lg">Create First Category</button>
      </div>

      <!-- Add/Edit Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
          <h3 class="text-2xl font-bold mb-6">{{ isEditing ? 'Edit' : 'Add' }} Category</h3>
          <form @submit.prevent="save">
            <input v-model="form.category_name" required placeholder="Category Name" class="w-full px-4 py-3 border rounded-lg mb-4">
            <input v-model="form.image_url" placeholder="Image URL (optional)" class="w-full px-4 py-3 border rounded-lg mb-4">
            <input v-model.number="form.display_order" type="number" placeholder="Display Order" class="w-full px-4 py-3 border rounded-lg mb-6">
            <div class="flex gap-3">
              <button type="button" @click="showModal = false" class="flex-1 py-3 border rounded-lg">Cancel</button>
              <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
                {{ isEditing ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Confirm -->
      <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl p-8 max-w-sm w-full text-center">
          <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>
          <h3 class="text-xl font-bold mb-2">Delete Category?</h3>
          <p class="text-gray-600 mb-6">"{{ deletingCategory?.category_name }}" will be deleted permanently.</p>
          <div class="flex gap-3">
            <button @click="showDeleteModal = false" class="flex-1 py-3 border rounded-lg">Cancel</button>
            <button @click="deleteCategory" class="flex-1 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700">Delete</button>
          </div>
        </div>
      </div>

      <!-- Toast -->
      <div v-if="toast.show" :class="toastClass" class="fixed top-4 right-4 text-white p-4 rounded-lg shadow-lg z-50 flex items-center gap-3">
        <i :class="toastIcon"></i>
        <div>
          <div class="font-bold">{{ toast.title }}</div>
          <div>{{ toast.message }}</div>
        </div>
      </div>
    </div>
  `
};