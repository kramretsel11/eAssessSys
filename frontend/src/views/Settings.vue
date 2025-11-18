<template>
  <div class="container-fluid">
    <div class="card">
      <div class="card-header pb-0">
        <h6>Settings</h6>
      </div>
      <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#database" role="tab" aria-selected="true">
              Database
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#categories" role="tab" aria-selected="false">
              Categories
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#marketValues" role="tab" aria-selected="false">
              Market Values
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#assessmentLevels" role="tab" aria-selected="false">
              Assessment Levels
            </a>
          </li>
        </ul>
        
        <div class="tab-content">
          <!-- Database Tab -->
          <div class="tab-pane active" id="database" role="tabpanel">
            <div class="row mt-4">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h6>Backup Database</h6>
                    <button class="btn btn-primary mt-3" @click="backupDatabase">
                      Backup Database
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h6>Restore Database</h6>
                    <input type="file" class="form-control mt-3" @change="handleFileUpload" accept=".sql">
                    <button class="btn btn-warning mt-3" @click="restoreDatabase" :disabled="!selectedFile">
                      Restore Database
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Categories Tab -->
          <div class="tab-pane" id="categories" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mt-4">
              <div>
                <h6>Categories Management</h6>
                <small class="text-muted">{{ categories.length }} categories found</small>
              </div>
              <div>
                <button class="btn btn-outline-secondary me-2" @click="fetchData">
                  <i class="fas fa-refresh"></i> Refresh
                </button>
                <button class="btn btn-primary" @click="openCategoryModal(false)">Add Category</button>
              </div>
            </div>
            <div class="table-responsive mt-3">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="category in categories" :key="category.id">
                    <td>{{ category.name }}</td>
                    <td>{{ category.type }}</td>
                    <td>
                      <button class="btn btn-link text-secondary mb-0" @click="editCategory(category)">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button class="btn btn-link text-danger mb-0" @click="confirmDeleteCategory(category)">
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Market Values Tab -->
          <div class="tab-pane" id="marketValues" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mt-4">
              <div>
                <h6>Market Values Management</h6>
                <small class="text-muted">{{ marketValues.length }} market values found</small>
              </div>
              <div>
                <button class="btn btn-outline-secondary me-2" @click="fetchData">
                  <i class="fas fa-refresh"></i> Refresh
                </button>
                <button class="btn btn-primary" @click="openMarketValueModal(false)">Add Market Value</button>
              </div>
            </div>
            <div class="table-responsive mt-3">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th>Category</th>
                    <th>Municipality</th>
                    <th>Kind of Land</th>
                    <th>Class</th>
                    <th>Unit</th>
                    <th>Market Value</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="value in marketValues" :key="value.id">
                    <td>{{ value.category_name }}</td>
                    <td>{{ value.municipality }}</td>
                    <td>{{ value.kindOfLand }}</td>
                    <td>{{ value.categoryClass }}</td>
                    <td>{{ value.unit }}</td>
                    <td>{{ value.marketValue }}</td>
                    <td>
                      <button class="btn btn-link text-secondary mb-0" @click="editMarketValue(value)">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button class="btn btn-link text-danger mb-0" @click="confirmDeleteMarketValue(value)">
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Assessment Levels Tab -->
          <div class="tab-pane" id="assessmentLevels" role="tabpanel">
            <div class="d-flex justify-content-end mt-4">
              <button class="btn btn-primary" @click="openAssessmentLevelModal(false)">Add Assessment Level</button>
            </div>
            <div class="table-responsive mt-3">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th>Category</th>
                    <th>Assessment Class</th>
                    <th>Over Value</th>
                    <th>Not Over Value</th>
                    <th>Assessment Level</th>
                    <th>Description</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="level in assessmentLevels" :key="level.id">
                    <td>{{ level.category_name }}</td>
                    <td>{{ level.assessmentClass }}</td>
                    <td>{{ level.overValue }}</td>
                    <td>{{ level.notOverValue }}</td>
                    <td>{{ level.assessmentLevel }}%</td>
                    <td>{{ level.description }}</td>
                    <td>
                      <button class="btn btn-link text-secondary mb-0" @click="editAssessmentLevel(level)">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button class="btn btn-link text-danger mb-0" @click="confirmDeleteAssessmentLevel(level)">
                        <i class="fa fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editMode ? 'Edit Category' : 'Add Category' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCategory">
              <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" v-model="categoryForm.name" required>
              </div>
              <div class="form-group mt-3">
                <label>Type</label>
                <select class="form-control" v-model="categoryForm.type" required :disabled="isTypeDisabled">
                  <option value="">Select Type</option>
                  <option value="market">Market</option>
                  <option value="assessment">Assessment</option>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="saveCategory">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Market Value Modal -->
    <div class="modal fade" id="marketValueModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editMode ? 'Edit Market Value' : 'Add Market Value' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveMarketValue">
              <div class="form-group">
                <label>Category</label>
                <select class="form-control" v-model="marketValueForm.categoryId" required>
                  <option value="">Select Category</option>
                  <option v-for="category in marketCategories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              <div class="form-group mt-3">
                <label>Municipality</label>
                <input type="text" class="form-control" v-model="marketValueForm.municipality" required>
              </div>
              <div class="form-group mt-3">
                <label>Kind of Land</label>
                <input type="text" class="form-control" v-model="marketValueForm.kindOfLand" required>
              </div>
              <div class="form-group mt-3">
                <label>Class</label>
                <input type="text" class="form-control" v-model="marketValueForm.categoryClass" required>
              </div>
              <div class="form-group mt-3">
                <label>Unit</label>
                <input type="text" class="form-control" v-model="marketValueForm.unit" required>
              </div>
              <div class="form-group mt-3">
                <label>Market Value</label>
                <input type="number" class="form-control" v-model="marketValueForm.marketValue" required>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="saveMarketValue">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Assessment Level Modal -->
    <div class="modal fade" id="assessmentLevelModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editMode ? 'Edit Assessment Level' : 'Add Assessment Level' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveAssessmentLevel">
              <div class="form-group">
                <label>Category</label>
                <select class="form-control" v-model="assessmentLevelForm.categoryId" required>
                  <option value="">Select Category</option>
                  <option v-for="category in assessmentCategories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              <div class="form-group mt-3">
                <label>Assessment Class</label>
                <select class="form-control" v-model="assessmentLevelForm.assessmentClass" required>
                  <option value="">Select Assessment Class</option>
                  <option v-for="className in assessmentClasses" :key="className" :value="className">
                    {{ className }}
                  </option>
                </select>
              </div>
              <div class="form-group mt-3">
                <label>Over Value</label>
                <input type="number" class="form-control" v-model="assessmentLevelForm.overValue" required>
              </div>
              <div class="form-group mt-3">
                <label>Not Over Value</label>
                <input type="number" class="form-control" v-model="assessmentLevelForm.notOverValue" required>
              </div>
              <div class="form-group mt-3">
                <label>Assessment Level (%)</label>
                <input type="number" class="form-control" v-model="assessmentLevelForm.assessmentLevel" required>
              </div>
              <div class="form-group mt-3">
                <label>Description</label>
                <textarea class="form-control" v-model="assessmentLevelForm.description" required></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="saveAssessmentLevel">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {jwtDecode} from 'jwt-decode';
import Swal from 'sweetalert2';
import * as bootstrap from 'bootstrap';

export default {
  name: 'Settings',
  data() {
    return {
      categories: [],
      marketValues: [],
      assessmentLevels: [],
      editMode: false,
      selectedFile: null,
      assessmentClasses: [
        'Residential',
        'Agricultural',
        'Commercial',
        'Industrial',
        'Mineral',
        'Timberland'
      ],
      categoryForm: {
        id: null,
        name: '',
        type: '',
      },
      marketValueForm: {
        id: null,
        categoryId: '',
        municipality: '',
        kindOfLand: '',
        categoryClass: '',
        unit: '',
        marketValue: '',
      },
      assessmentLevelForm: {
        id: null,
        categoryId: '',
        assessmentClass: '',
        overValue: '',
        notOverValue: '',
        assessmentLevel: '',
        description: '',
      },
    };
  },
    computed: {
    user: function() {
      let token = localStorage.getItem('userToken')
      return jwtDecode(token);
    },
    marketCategories() {
      return this.categories.filter(category => category.type.toLowerCase() === 'market');
    },
    assessmentCategories() {
      return this.categories.filter(category => category.type.toLowerCase() === 'assessment');
    },
    isTypeDisabled() {
      // Type should be disabled when adding from market/assessment tabs
      if (this.editMode) return false; // Allow editing type when editing existing category
      const marketTab = document.querySelector('#marketValues.active');
      const assessmentTab = document.querySelector('#assessmentLevels.active');
      return marketTab || assessmentTab;
    }
  },
    created() {
        this.fetchData();
    },
    methods: {
        async fetchData() {
            try {
                // Show loading state
                Swal.fire({
                    title: 'Loading data...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                const [categoriesRes, marketValuesRes, assessmentLevelsRes] = await Promise.all([
                    this.$api.get('/e_assessment/api/v1/misc/categories'),
                    this.$api.get('/e_assessment/api/v1/misc/market-values'),
                    this.$api.get('/e_assessment/api/v1/misc/assessment-levels'),
                ]);
                
                // Close loading toast
                Swal.close();
                
                // Handle response data structure
                this.categories = categoriesRes.data?.data || categoriesRes.data || [];
                this.marketValues = marketValuesRes.data?.data || marketValuesRes.data || [];
                this.assessmentLevels = assessmentLevelsRes.data?.data || assessmentLevelsRes.data || [];
                
                console.log('Data loaded successfully:', {
                    categories: this.categories.length,
                    marketValues: this.marketValues.length,
                    assessmentLevels: this.assessmentLevels.length
                });
                
            } catch (error) {
                console.error('Error fetching data:', error);
                
                // Close loading if still open
                Swal.close();
                
                // Show detailed error message
                let errorMessage = 'Failed to fetch data';
                if (error.response) {
                    errorMessage = error.response.data?.message || `Server error: ${error.response.status}`;
                } else if (error.request) {
                    errorMessage = 'No response from server. Please check if the backend is running.';
                } else {
                    errorMessage = error.message;
                }
                
                Swal.fire({
                    title: 'Error Loading Data',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'Retry',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.fetchData(); // Retry loading
                    }
                });
            }
        },

        // Database Methods
        handleFileUpload(event) {
            this.selectedFile = event.target.files[0];
        },

        async backupDatabase() {
            try {
                // Ask for password confirmation
                const { value: password } = await Swal.fire({
                    title: 'Enter Your Password',
                    input: 'password',
                    inputLabel: 'Password',
                    inputPlaceholder: 'Enter your password',
                    showCancelButton: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Password is required!'
                        }
                    }
                });

                if (password) {
                    // Send password and userId for verification and get the response as blob
                    const response = await this.$api.post('/e_assessment/api/v1/misc/database/backup', 
                        {
                            password: password,
                            userId: this.user.userId
                        },
                        { 
                            responseType: 'blob' // Important: tells axios to handle the response as a binary blob
                        }
                    );

                    // Create a blob from the response data
                    const blob = new Blob([response.data], { type: 'application/sql' });
                    
                    // Create a temporary URL for the blob
                    const url = window.URL.createObjectURL(blob);
                    
                    // Create a temporary link element
                    const link = document.createElement('a');
                    link.href = url;
                    
                    // Get current date for filename
                    const date = new Date().toISOString().slice(0, 10);
                    link.download = `ascot_db_backup_${date}.sql`; // Set the download filename
                    
                    // Append the link to the document, click it, and remove it
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Clean up the temporary URL
                    window.URL.revokeObjectURL(url);
                    
                    Swal.fire('Success', 'Database backup downloaded successfully', 'success');
                }
            } catch (error) {
                console.error('Backup error:', error);
                const errorMessage = error.response?.data?.message || 'Failed to backup database';
                Swal.fire('Error', errorMessage, 'error');
            }
        },

        async restoreDatabase() {
            if (!this.selectedFile) return;

            try {
                // Ask for password confirmation
                const { value: password } = await Swal.fire({
                    title: 'Enter Your Password',
                    input: 'password',
                    inputLabel: 'Password',
                    inputPlaceholder: 'Enter your password',
                    showCancelButton: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Password is required!'
                        }
                    }
                });

                if (password) {
                    const formData = new FormData();
                    formData.append('backup_file', this.selectedFile);
                    formData.append('password', password);
                    formData.append('userId', this.user.userId);

                    await this.$api.post('/e_assessment/api/v1/misc/database/restore', formData);
                    Swal.fire('Success', 'Database restored successfully', 'success');
                }
            } catch (error) {
                console.error('Restore error:', error);
                const errorMessage = error.response?.data?.message || 'Failed to restore database';
                Swal.fire('Error', errorMessage, 'error');
            }
        },

        // Category Methods
        openCategoryModal(category = null) {
            this.editMode = !!category;
            if (category) {
                this.categoryForm = { ...category };
            } else {
                // Determine type based on active tab
                const marketTab = document.querySelector('#marketValues.active');
                const assessmentTab = document.querySelector('#assessmentLevels.active');
                const type = marketTab ? 'market' : (assessmentTab ? 'assessment' : '');
                
                this.categoryForm = { id: null, name: '', type };
            }
            const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
            modal.show();
        },

        async saveCategory() {
            try {
                const url = this.editMode
                ? '/e_assessment/api/v1/misc/categories/update'
                : '/e_assessment/api/v1/misc/categories/create';

                const response = await this.$api.post(url, {
                ...this.categoryForm,
                created_by: this.user.userId,
                });

                await this.fetchData();
                bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
                Swal.fire('Success', response.data.message, 'success');
            } catch (error) {
                console.error('Category save error:', error);
                Swal.fire('Error', 'Failed to save category', 'error');
            }
        },

        async confirmDeleteCategory(category) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                try {
                await this.$api.post('/e_assessment/api/v1/misc/categories/delete', { id: category.id });
                await this.fetchData();
                Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                } catch (error) {
                console.error('Delete error:', error);
                Swal.fire('Error', 'Failed to delete category', 'error');
                }
            }
        },

        // Market Value Methods
        openMarketValueModal(marketValue = null) {
            this.editMode = !!marketValue;
            this.marketValueForm = marketValue 
                ? { ...marketValue }
                : {
                    id: null,
                    categoryId: '',
                    municipality: '',
                    kindOfLand: '',
                    categoryClass: '',
                    unit: '',
                    marketValue: '',
                };
            const modal = new bootstrap.Modal(document.getElementById('marketValueModal'));
            modal.show();
        },

        async saveMarketValue() {
            try {
                const url = this.editMode
                ? '/e_assessment/api/v1/misc/market-values/update'
                : '/e_assessment/api/v1/misc/market-values/create';

                const response = await this.$api.post(url, {
                ...this.marketValueForm,
                created_by: this.user.userId,
                });

                await this.fetchData();
                bootstrap.Modal.getInstance(document.getElementById('marketValueModal')).hide();
                Swal.fire('Success', response.data.message, 'success');
            } catch (error) {
                console.error('Market value save error:', error);
                Swal.fire('Error', 'Failed to save market value', 'error');
            }
        },

        async confirmDeleteMarketValue(marketValue) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                try {
                await this.$api.post('/e_assessment/api/v1/misc/market-values/delete', { id: marketValue.id });
                await this.fetchData();
                Swal.fire('Deleted!', 'Market value has been deleted.', 'success');
                } catch (error) {
                console.error('Delete error:', error);
                Swal.fire('Error', 'Failed to delete market value', 'error');
                }
            }
        },

        // Assessment Level Methods
        openAssessmentLevelModal(assessmentLevel = null) {
            this.editMode = !!assessmentLevel;
            this.assessmentLevelForm = assessmentLevel
                ? { ...assessmentLevel }
                : {
                    id: null,
                    categoryId: '',
                    assessmentClass: '',
                    overValue: '',
                    notOverValue: '',
                    assessmentLevel: '',
                    description: '',
                };
            const modal = new bootstrap.Modal(document.getElementById('assessmentLevelModal'));
            modal.show();
        },

        async saveAssessmentLevel() {
            try {
                const url = this.editMode
                ? '/e_assessment/api/v1/misc/assessment-levels/update'
                : '/e_assessment/api/v1/misc/assessment-levels/create';

                const response = await this.$api.post(url, {
                ...this.assessmentLevelForm,
                created_by: this.user.userId,
                });

                await this.fetchData();
                bootstrap.Modal.getInstance(document.getElementById('assessmentLevelModal')).hide();
                Swal.fire('Success', response.data.message, 'success');
            } catch (error) {
                console.error('Assessment level save error:', error);
                Swal.fire('Error', 'Failed to save assessment level', 'error');
            }
        },

        async confirmDeleteAssessmentLevel(assessmentLevel) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                try {
                await this.$api.post('/e_assessment/api/v1/misc/assessment-levels/delete', { id: assessmentLevel.id });
                await this.fetchData();
                Swal.fire('Deleted!', 'Assessment level has been deleted.', 'success');
                } catch (error) {
                console.error('Delete error:', error);
                Swal.fire('Error', 'Failed to delete assessment level', 'error');
                }
            }
        },
    },
};
</script>