// Add this to your Vue app setup
const categoriesSection = {
  setup() {
    const categories = ref([]);
    const showCategoryModal = ref(false);
    const showDeleteCategoryModal = ref(false);
    const isEditingCategory = ref(false);
    const categoryToDelete = ref(null);
    
    const categoryForm = reactive({
      id: null,
      category_name: '',
      image_url: '',
      display_order: 0
    });

    const toaster = reactive({
      show: false,
      title: '',
      message: '',
      type: 'success' // success, error, warning
    });

    // Computed properties
    const toasterClass = computed(() => {
      const classes = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-orange-500 text-white'
      };
      return classes[toaster.type] || classes.success;
    });

    const toasterIcon = computed(() => {
      const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle'
      };
      return icons[toaster.type] || icons.success;
    });

    // Methods
    const loadCategories = async () => {
      try {
        const response = await axios.get('includes/categories.php?action=get_all');
        categories.value = response.data;
      } catch (error) {
        console.error('Failed to load categories:', error);
        showToaster('Error', 'Failed to load categories', 'error');
      }
    };

    const openAddCategoryModal = () => {
      isEditingCategory.value = false;
      resetCategoryForm();
      showCategoryModal.value = true;
    };

    const editCategory = (category) => {
      isEditingCategory.value = true;
      Object.assign(categoryForm, {
        id: category.id,
        category_name: category.category_name,
        image_url: category.image_url,
        display_order: category.display_order
      });
      showCategoryModal.value = true;
    };

    const closeCategoryModal = () => {
      showCategoryModal.value = false;
      resetCategoryForm();
    };

    const resetCategoryForm = () => {
      Object.assign(categoryForm, {
        id: null,
        category_name: '',
        image_url: '',
        display_order: 0
      });
    };

    const saveCategory = async () => {
      try {
        const url = 'includes/categories.php';
        const data = new FormData();
        
        data.append('action', isEditingCategory.value ? 'update' : 'create');
        data.append('category_name', categoryForm.category_name);
        data.append('image_url', categoryForm.image_url);
        data.append('display_order', categoryForm.display_order);
        
        if (isEditingCategory.value) {
          data.append('id', categoryForm.id);
        }

        const response = await axios.post(url, data);
        
        if (response.data.success) {
          showToaster(
            'Success', 
            `Category ${isEditingCategory.value ? 'updated' : 'created'} successfully`, 
            'success'
          );
          closeCategoryModal();
          loadCategories();
        } else {
          throw new Error(response.data.message || 'Operation failed');
        }
      } catch (error) {
        console.error('Failed to save category:', error);
        showToaster('Error', 'Failed to save category', 'error');
      }
    };

    const confirmDeleteCategory = (category) => {
      categoryToDelete.value = category;
      showDeleteCategoryModal.value = true;
    };

    const closeDeleteCategoryModal = () => {
      showDeleteCategoryModal.value = false;
      categoryToDelete.value = null;
    };

    const deleteCategory = async () => {
      if (!categoryToDelete.value) return;

      try {
        const response = await axios.post('includes/categories.php', {
          action: 'delete',
          id: categoryToDelete.value.id
        });

        if (response.data.success) {
          showToaster('Success', 'Category deleted successfully', 'success');
          closeDeleteCategoryModal();
          loadCategories();
        } else {
          throw new Error(response.data.message || 'Delete failed');
        }
      } catch (error) {
        console.error('Failed to delete category:', error);
        showToaster('Error', 'Failed to delete category', 'error');
      }
    };

    const showToaster = (title, message, type = 'success') => {
      toaster.title = title;
      toaster.message = message;
      toaster.type = type;
      toaster.show = true;
      
      setTimeout(() => {
        hideToaster();
      }, 5000);
    };

    const hideToaster = () => {
      toaster.show = false;
    };

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    };

    // Lifecycle
    onMounted(() => {
      loadCategories();
    });

    return {
      categories,
      showCategoryModal,
      showDeleteCategoryModal,
      isEditingCategory,
      categoryToDelete,
      categoryForm,
      toaster,
      toasterClass,
      toasterIcon,
      openAddCategoryModal,
      editCategory,
      closeCategoryModal,
      saveCategory,
      confirmDeleteCategory,
      closeDeleteCategoryModal,
      deleteCategory,
      showToaster,
      hideToaster,
      formatDate
    };
  }
};