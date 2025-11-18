<template>
  <div class="configuration-management">
    <div class="row mb-4">
      <div class="col-md-8">
        <h6 class="text-dark font-weight-bold">
          <i class="fas fa-cogs me-2"></i>
          System Configuration
        </h6>
        <p class="text-sm text-muted">Manage Market Value, Assessment Level, and Location settings</p>
      </div>
    </div>

    <!-- Configuration Tabs -->
    <div class="card">
      <div class="card-header">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              @click="activeConfigTab = 'market-values'"
              :class="{ active: activeConfigTab === 'market-values' }"
            >
              <i class="fas fa-dollar-sign me-2"></i>Market Values
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              @click="activeConfigTab = 'assessment-levels'"
              :class="{ active: activeConfigTab === 'assessment-levels' }"
            >
              <i class="fas fa-percentage me-2"></i>Assessment Levels
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              @click="activeConfigTab = 'categories'"
              :class="{ active: activeConfigTab === 'categories' }"
            >
              <i class="fas fa-tags me-2"></i>Property Categories
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              @click="activeConfigTab = 'locations'"
              :class="{ active: activeConfigTab === 'locations' }"
            >
              <i class="fas fa-map-marker-alt me-2"></i>Location Management
            </button>
          </li>
        </ul>
      </div>
      
      <div class="card-body">
        <!-- Market Values Configuration -->
        <div v-show="activeConfigTab === 'market-values'" class="config-section">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Market Value Configuration</h6>
            <button class="btn btn-primary btn-sm" @click="showMarketValueForm = true">
              <i class="fas fa-plus me-1"></i>Add Market Value
            </button>
          </div>
          
          <!-- Market Values Table -->
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Property Type</th>
                  <th>Location</th>
                  <th>Base Value (₱/sq.m)</th>
                  <th>Effective Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="marketValue in marketValues" :key="marketValue.id">
                  <td>{{ marketValue.propertyType }}</td>
                  <td>{{ marketValue.municipality }}, {{ marketValue.barangay }}</td>
                  <td>₱{{ formatCurrency(marketValue.baseValue) }}</td>
                  <td>{{ formatDate(marketValue.effectiveDate) }}</td>
                  <td>
                    <span class="badge" :class="marketValue.isActive ? 'bg-success' : 'bg-secondary'">
                      {{ marketValue.isActive ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button class="btn btn-info" @click="editMarketValue(marketValue)">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button class="btn btn-danger" @click="deleteMarketValue(marketValue)">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Market Value Form Modal -->
          <div class="modal fade" id="marketValueModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">
                    {{ editingMarketValue ? 'Edit' : 'Add' }} Market Value
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <form @submit.prevent="saveMarketValue">
                    <div class="mb-3">
                      <label class="form-label">Property Type</label>
                      <select v-model="marketValueForm.propertyType" class="form-select" required>
                        <option value="">Select Property Type</option>
                        <option value="Residential">Residential</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Industrial">Industrial</option>
                        <option value="Agricultural">Agricultural</option>
                      </select>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label">Municipality</label>
                      <select v-model="marketValueForm.municipality" class="form-select" required>
                        <option value="">Select Municipality</option>
                        <option v-for="municipality in auroraMunicipalities" :key="municipality" :value="municipality">
                          {{ municipality }}
                        </option>
                      </select>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label">Barangay (Optional)</label>
                      <input
                        v-model="marketValueForm.barangay"
                        type="text"
                        class="form-control"
                        placeholder="Leave empty for municipality-wide"
                      />
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label">Base Value (₱/sq.m)</label>
                      <input
                        v-model="marketValueForm.baseValue"
                        type="number"
                        step="0.01"
                        class="form-control"
                        required
                      />
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label">Effective Date</label>
                      <input
                        v-model="marketValueForm.effectiveDate"
                        type="date"
                        class="form-control"
                        required
                      />
                    </div>
                    
                    <div class="mb-3 form-check">
                      <input
                        v-model="marketValueForm.isActive"
                        type="checkbox"
                        class="form-check-input"
                        id="isActive"
                      />
                      <label class="form-check-label" for="isActive">Active</label>
                    </div>
                    
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">
                        {{ editingMarketValue ? 'Update' : 'Save' }}
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Assessment Levels Configuration -->
        <div v-show="activeConfigTab === 'assessment-levels'" class="config-section">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Assessment Level Configuration</h6>
            <button class="btn btn-primary btn-sm" @click="showAssessmentLevelForm = true">
              <i class="fas fa-plus me-1"></i>Add Assessment Level
            </button>
          </div>
          
          <!-- Assessment Levels Table -->
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Property Classification</th>
                  <th>Assessment Level (%)</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="level in assessmentLevels" :key="level.id">
                  <td>{{ level.classification }}</td>
                  <td>{{ level.percentage }}%</td>
                  <td>{{ level.description }}</td>
                  <td>
                    <span class="badge" :class="level.isActive ? 'bg-success' : 'bg-secondary'">
                      {{ level.isActive ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button class="btn btn-info" @click="editAssessmentLevel(level)">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button class="btn btn-danger" @click="deleteAssessmentLevel(level)">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Assessment Level Form Modal -->
          <div class="modal fade" id="assessmentLevelModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">
                    {{ editingAssessmentLevel ? 'Edit' : 'Add' }} Assessment Level
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <form @submit.prevent="saveAssessmentLevel">
                    <div class="mb-3">
                      <label class="form-label">Property Classification</label>
                      <input
                        v-model="assessmentLevelForm.classification"
                        type="text"
                        class="form-control"
                        required
                        placeholder="e.g., Residential, Commercial"
                      />
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label">Assessment Level (%)</label>
                      <input
                        v-model="assessmentLevelForm.percentage"
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        class="form-control"
                        required
                      />
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label">Description</label>
                      <textarea
                        v-model="assessmentLevelForm.description"
                        class="form-control"
                        rows="3"
                        placeholder="Description of this assessment level"
                      ></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                      <input
                        v-model="assessmentLevelForm.isActive"
                        type="checkbox"
                        class="form-check-input"
                        id="assessmentActive"
                      />
                      <label class="form-check-label" for="assessmentActive">Active</label>
                    </div>
                    
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">
                        {{ editingAssessmentLevel ? 'Update' : 'Save' }}
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Property Categories Configuration -->
        <div v-show="activeConfigTab === 'categories'" class="config-section">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Property Categories</h6>
            <button class="btn btn-primary btn-sm" @click="showCategoryForm = true">
              <i class="fas fa-plus me-1"></i>Add Category
            </button>
          </div>
          
          <!-- Categories Table -->
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Category Name</th>
                  <th>Code</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="category in categories" :key="category.id">
                  <td>{{ category.name }}</td>
                  <td>{{ category.code }}</td>
                  <td>{{ category.description }}</td>
                  <td>
                    <span class="badge" :class="category.isActive ? 'bg-success' : 'bg-secondary'">
                      {{ category.isActive ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button class="btn btn-info" @click="editCategory(category)">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button class="btn btn-danger" @click="deleteCategory(category)">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Location Management Configuration -->
        <div v-show="activeConfigTab === 'locations'" class="config-section">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Aurora Province Location Management</h6>
          </div>
          
          <!-- Aurora Municipalities Info -->
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h6 class="mb-0">Aurora Municipalities</h6>
                </div>
                <div class="card-body">
                  <ul class="list-group list-group-flush">
                    <li v-for="municipality in auroraMunicipalities" :key="municipality" 
                        class="list-group-item d-flex justify-content-between align-items-center">
                      {{ municipality }}
                      <span class="badge bg-primary rounded-pill">
                        {{ getBarangayCount(municipality) }} barangays
                      </span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h6 class="mb-0">Province Statistics</h6>
                </div>
                <div class="card-body">
                  <div class="row text-center">
                    <div class="col-6">
                      <h4 class="text-primary">{{ auroraMunicipalities.length }}</h4>
                      <small>Total Municipalities</small>
                    </div>
                    <div class="col-6">
                      <h4 class="text-success">{{ totalBarangays }}</h4>
                      <small>Total Barangays</small>
                    </div>
                  </div>
                  
                  <hr>
                  
                  <div class="text-center">
                    <p class="mb-1"><strong>Province:</strong> Aurora</p>
                    <p class="mb-1"><strong>Region:</strong> III (Central Luzon)</p>
                    <p class="mb-0"><strong>Capital:</strong> Baler</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, nextTick } from 'vue';
import AuthService from '@/services/auth.js';
import AddressService from '@/services/auroraAddress.js';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

export default {
  name: 'ConfigurationManagement',
  emits: ['config-updated'],
  setup(props, { emit }) {
    const activeConfigTab = ref('market-values');
    
    // Market Values
    const marketValues = ref([]);
    const marketValueForm = ref({
      propertyType: '',
      municipality: '',
      barangay: '',
      baseValue: 0,
      effectiveDate: '',
      isActive: true
    });
    const showMarketValueForm = ref(false);
    const editingMarketValue = ref(null);

    // Assessment Levels
    const assessmentLevels = ref([]);
    const assessmentLevelForm = ref({
      classification: '',
      percentage: 0,
      description: '',
      isActive: true
    });
    const showAssessmentLevelForm = ref(false);
    const editingAssessmentLevel = ref(null);

    // Categories
    const categories = ref([]);
    const showCategoryForm = ref(false);

    // Aurora Locations
    const auroraData = computed(() => AddressService.getAllAuroraData());
    const auroraMunicipalities = computed(() => auroraData.value.municipalities.map(m => m.name));
    const totalBarangays = computed(() => auroraData.value.statistics.totalBarangays);

    const getBarangayCount = (municipality) => {
      return AddressService.getBarangaysByCity(municipality).length;
    };

    const formatCurrency = (value) => {
      return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(value);
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      return new Date(dateString).toLocaleDateString();
    };

    // Market Value Methods
    const fetchMarketValues = async () => {
      try {
        const headers = AuthService.getAuthHeader();
        const response = await fetch('http://localhost:8080/e_assessment/api/v1/admin/market-values', {
          headers
        });

        if (response.ok) {
          marketValues.value = await response.json();
        }
      } catch (error) {
        console.error('Error fetching market values:', error);
      }
    };

    const saveMarketValue = async () => {
      try {
        const headers = AuthService.getAuthHeader();
        const method = editingMarketValue.value ? 'PUT' : 'POST';
        const url = editingMarketValue.value 
          ? `http://localhost:8080/e_assessment/api/v1/admin/market-values/${editingMarketValue.value.id}`
          : 'http://localhost:8080/e_assessment/api/v1/admin/market-values';

        const response = await fetch(url, {
          method,
          headers: {
            ...headers,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(marketValueForm.value)
        });

        if (!response.ok) {
          throw new Error('Failed to save market value');
        }

        await fetchMarketValues();
        
        // Close modal
        const modal = Modal.getInstance(document.getElementById('marketValueModal'));
        modal?.hide();
        
        // Reset form
        Object.assign(marketValueForm.value, {
          propertyType: '',
          municipality: '',
          barangay: '',
          baseValue: 0,
          effectiveDate: '',
          isActive: true
        });
        // Use nextTick to avoid race conditions
        nextTick(() => {
          editingMarketValue.value = null;
        });

        emit('config-updated', 'Market Values');

        Swal.fire({
          title: 'Success!',
          text: 'Market value saved successfully',
          icon: 'success',
          timer: 3000
        });

      } catch (error) {
        console.error('Error saving market value:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to save market value',
          icon: 'error'
        });
      }
    };

    const editMarketValue = (marketValue) => {
      editingMarketValue.value = marketValue;
      Object.assign(marketValueForm.value, marketValue);
      const modal = new Modal(document.getElementById('marketValueModal'));
      modal.show();
    };

    const deleteMarketValue = async (marketValue) => {
      const result = await Swal.fire({
        title: 'Delete Market Value?',
        text: 'Are you sure you want to delete this market value?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      });

      if (result.isConfirmed) {
        try {
          const headers = AuthService.getAuthHeader();
          const response = await fetch(`http://localhost:8080/e_assessment/api/v1/admin/market-values/${marketValue.id}`, {
            method: 'DELETE',
            headers
          });

          if (!response.ok) {
            throw new Error('Failed to delete market value');
          }

          await fetchMarketValues();
          emit('config-updated', 'Market Values');

          Swal.fire({
            title: 'Deleted!',
            text: 'Market value has been deleted.',
            icon: 'success',
            timer: 2000
          });

        } catch (error) {
          console.error('Error deleting market value:', error);
          Swal.fire({
            title: 'Error',
            text: 'Failed to delete market value',
            icon: 'error'
          });
        }
      }
    };

    // Assessment Level Methods
    const fetchAssessmentLevels = async () => {
      try {
        const headers = AuthService.getAuthHeader();
        const response = await fetch('http://localhost:8080/e_assessment/api/v1/admin/assessment-levels', {
          headers
        });

        if (response.ok) {
          assessmentLevels.value = await response.json();
        }
      } catch (error) {
        console.error('Error fetching assessment levels:', error);
      }
    };

    const saveAssessmentLevel = async () => {
      try {
        const headers = AuthService.getAuthHeader();
        const method = editingAssessmentLevel.value ? 'PUT' : 'POST';
        const url = editingAssessmentLevel.value 
          ? `http://localhost:8080/e_assessment/api/v1/admin/assessment-levels/${editingAssessmentLevel.value.id}`
          : 'http://localhost:8080/e_assessment/api/v1/admin/assessment-levels';

        const response = await fetch(url, {
          method,
          headers: {
            ...headers,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(assessmentLevelForm.value)
        });

        if (!response.ok) {
          throw new Error('Failed to save assessment level');
        }

        await fetchAssessmentLevels();
        
        // Close modal
        const modal = Modal.getInstance(document.getElementById('assessmentLevelModal'));
        modal?.hide();
        
        // Reset form
        Object.assign(assessmentLevelForm.value, {
          classification: '',
          percentage: 0,
          description: '',
          isActive: true
        });
        // Use nextTick to avoid race conditions
        nextTick(() => {
          editingAssessmentLevel.value = null;
        });

        emit('config-updated', 'Assessment Levels');

        Swal.fire({
          title: 'Success!',
          text: 'Assessment level saved successfully',
          icon: 'success',
          timer: 3000
        });

      } catch (error) {
        console.error('Error saving assessment level:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to save assessment level',
          icon: 'error'
        });
      }
    };

    const editAssessmentLevel = (level) => {
      editingAssessmentLevel.value = level;
      Object.assign(assessmentLevelForm.value, level);
      const modal = new Modal(document.getElementById('assessmentLevelModal'));
      modal.show();
    };

    const deleteAssessmentLevel = async (level) => {
      const result = await Swal.fire({
        title: 'Delete Assessment Level?',
        text: 'Are you sure you want to delete this assessment level?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      });

      if (result.isConfirmed) {
        try {
          const headers = AuthService.getAuthHeader();
          const response = await fetch(`http://localhost:8080/e_assessment/api/v1/admin/assessment-levels/${level.id}`, {
            method: 'DELETE',
            headers
          });

          if (!response.ok) {
            throw new Error('Failed to delete assessment level');
          }

          await fetchAssessmentLevels();
          emit('config-updated', 'Assessment Levels');

          Swal.fire({
            title: 'Deleted!',
            text: 'Assessment level has been deleted.',
            icon: 'success',
            timer: 2000
          });

        } catch (error) {
          console.error('Error deleting assessment level:', error);
          Swal.fire({
            title: 'Error',
            text: 'Failed to delete assessment level',
            icon: 'error'
          });
        }
      }
    };

    // Categories Methods
    const fetchCategories = async () => {
      try {
        const headers = AuthService.getAuthHeader();
        const response = await fetch('http://localhost:8080/e_assessment/api/v1/admin/categories', {
          headers
        });

        if (response.ok) {
          categories.value = await response.json();
        }
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    };

    const editCategory = (category) => {
      Swal.fire({
        title: 'Edit Category',
        text: `Editing category: ${category.name}`,
        icon: 'info',
        confirmButtonText: 'OK'
      });
    };

    const deleteCategory = async (category) => {
      const result = await Swal.fire({
        title: 'Delete Category?',
        text: `Are you sure you want to delete "${category.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      });

      if (result.isConfirmed) {
        try {
          const headers = AuthService.getAuthHeader();
          const response = await fetch(`http://localhost:8080/e_assessment/api/v1/admin/categories/${category.id}`, {
            method: 'DELETE',
            headers
          });

          if (!response.ok) {
            throw new Error('Failed to delete category');
          }

          await fetchCategories();
          emit('config-updated', 'Categories');

          Swal.fire({
            title: 'Deleted!',
            text: 'Category has been deleted.',
            icon: 'success',
            timer: 2000
          });

        } catch (error) {
          console.error('Error deleting category:', error);
          Swal.fire({
            title: 'Error',
            text: 'Failed to delete category',
            icon: 'error'
          });
        }
      }
    };

    // Watch for form shows to open modals
    const showMarketValueFormWatcher = computed(() => {
      if (showMarketValueForm.value) {
        setTimeout(() => {
          const modal = new Modal(document.getElementById('marketValueModal'));
          modal.show();
          showMarketValueForm.value = false;
        }, 100);
      }
    });

    const showAssessmentLevelFormWatcher = computed(() => {
      if (showAssessmentLevelForm.value) {
        setTimeout(() => {
          const modal = new Modal(document.getElementById('assessmentLevelModal'));
          modal.show();
          showAssessmentLevelForm.value = false;
        }, 100);
      }
    });

    onMounted(() => {
      fetchMarketValues();
      fetchAssessmentLevels();
      fetchCategories();
    });

    return {
      activeConfigTab,
      marketValues,
      marketValueForm,
      showMarketValueForm,
      editingMarketValue,
      assessmentLevels,
      assessmentLevelForm,
      showAssessmentLevelForm,
      editingAssessmentLevel,
      categories,
      showCategoryForm,
      auroraMunicipalities,
      totalBarangays,
      getBarangayCount,
      formatCurrency,
      formatDate,
      saveMarketValue,
      editMarketValue,
      deleteMarketValue,
      saveAssessmentLevel,
      editAssessmentLevel,
      deleteAssessmentLevel,
      editCategory,
      deleteCategory,
      showMarketValueFormWatcher,
      showAssessmentLevelFormWatcher
    };
  }
};
</script>

<style scoped>
.config-section {
  min-height: 400px;
}

.nav-tabs .nav-link {
  border: none;
  color: #67748e;
  font-weight: 600;
  padding: 1rem 1.5rem;
}

.nav-tabs .nav-link.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 8px 8px 0 0;
}

.nav-tabs .nav-link:hover {
  border: none;
  background: rgba(102, 126, 234, 0.1);
  color: #667eea;
}

.table th {
  font-weight: 700;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-group-sm .btn {
  padding: 0.375rem 0.75rem;
}

.modal-content {
  border-radius: 12px;
}

.card {
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.list-group-item {
  border: none;
  padding: 0.75rem 1rem;
}

.badge {
  font-size: 0.75rem;
}
</style>