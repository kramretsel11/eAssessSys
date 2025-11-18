<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <!-- Header -->
        <div class="card mb-4">
          <div class="card-header pb-0">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="mb-0">User Management</h6>
                <p class="text-sm mb-0">Manage users across all municipalities</p>
              </div>
              <div class="col-auto">
                <button class="btn btn-primary btn-sm" @click="showAddUserModal = true">
                  <i class="fas fa-plus me-1"></i>
                  Add New User
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Filter by Municipality</label>
                <select v-model="filters.municipality" class="form-select">
                  <option value="">All Municipalities</option>
                  <option value="BALER">BALER</option>
                  <option value="CASIGURAN">CASIGURAN</option>
                  <option value="DIPACULAO">DIPACULAO</option>
                  <option value="DILASAG">DILASAG</option>
                  <option value="DINALUNGAN">DINALUNGAN</option>
                  <option value="DINGALAN">DINGALAN</option>
                  <option value="MARIA AURORA">MARIA AURORA</option>
                  <option value="SAN LUIS">SAN LUIS</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Filter by Role</label>
                <select v-model="filters.role" class="form-control">
                  <option value="">All Roles</option>
                  <option v-for="userType in userTypes" :key="userType.id" :value="userType.id">
                    {{ userType.description }}
                  </option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Search</label>
                <input 
                  v-model="filters.search" 
                  type="text" 
                  class="form-control" 
                  placeholder="Search by name, email, or username..."
                >
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-outline-secondary" @click="clearFilters">
                  <i class="fas fa-times me-1"></i>
                  Clear
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Users Table -->
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>User Info</th>
                    <th>Municipality</th>
                    <th>Role</th>
                    <th>Permissions</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in filteredUsers" :key="user.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-3">
                          <i class="fas fa-user text-white text-xs"></i>
                        </div>
                        <div>
                          <h6 class="mb-0 text-sm">{{ user.firstName }} {{ user.lastName }}</h6>
                          <p class="text-xs text-muted mb-0">{{ user.email }}</p>
                          <p class="text-xs text-muted mb-0">@{{ user.username }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-info">{{ user.municipality }}</span>
                    </td>
                    <td>
                      <span class="badge" :class="getRoleBadgeClass(user.userType)">
                        {{ getRoleDisplayName(user.userType) }}
                      </span>
                    </td>
                    <td>
                      <div class="permissions-list">
                        <small v-for="permission in getUserPermissions(user.userType)" :key="permission" class="d-block text-xs">
                          • {{ permission }}
                        </small>
                      </div>
                    </td>
                    <td>
                      <span class="badge" :class="user.status === 1 ? 'bg-success' : 'bg-danger'">
                        {{ user.status === 1 ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td>
                      <div class="dropdown">
                        <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown">
                          <i class="fa fa-ellipsis-v text-xs"></i>
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#" @click="editUser(user)">
                            <i class="fas fa-edit me-2"></i>Edit
                          </a>
                          <a class="dropdown-item" href="#" @click="toggleUserStatus(user)">
                            <i class="fas fa-power-off me-2"></i>
                            {{ user.status === 1 ? 'Deactivate' : 'Activate' }}
                          </a>
                          <a class="dropdown-item text-danger" href="#" @click="deleteUser(user)">
                            <i class="fas fa-trash me-2"></i>Delete
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Users pagination" v-if="totalPages > 1">
              <ul class="pagination justify-content-center">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                  <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">Previous</a>
                </li>
                <li 
                  v-for="page in totalPages" 
                  :key="page" 
                  class="page-item" 
                  :class="{ active: currentPage === page }"
                >
                  <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                </li>
                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                  <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">Next</a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div class="modal fade" :class="{ show: showAddUserModal }" tabindex="-1" :style="{ display: showAddUserModal ? 'block' : 'none' }">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingUser ? 'Edit User' : 'Add New User' }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveUser">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">First Name *</label>
                    <input 
                      v-model="userForm.firstName" 
                      type="text" 
                      class="form-control" 
                      required
                    >
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Last Name *</label>
                    <input 
                      v-model="userForm.lastName" 
                      type="text" 
                      class="form-control" 
                      required
                    >
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Username *</label>
                    <input 
                      v-model="userForm.username" 
                      type="text" 
                      class="form-control" 
                      required
                    >
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input 
                      v-model="userForm.email" 
                      type="email" 
                      class="form-control" 
                      required
                    >
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Municipality *</label>
                    <select v-model="userForm.municipality" class="form-select" required>
                      <option value="">Select Municipality</option>
                      <option value="BALER">BALER</option>
                      <option value="CASIGURAN">CASIGURAN</option>
                      <option value="DIPACULAO">DIPACULAO</option>
                      <option value="DILASAG">DILASAG</option>
                      <option value="DINALUNGAN">DINALUNGAN</option>
                      <option value="DINGALAN">DINGALAN</option>
                      <option value="MARIA AURORA">MARIA AURORA</option>
                      <option value="SAN LUIS">SAN LUIS</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Role *</label>
                    <select v-model="userForm.role" class="form-select" required>
                      <option value="">Select Role</option>
                      <option value="municipality_user">Municipality User</option>
                      <option value="evaluator">Evaluator</option>
                      <option value="admin">Super Admin</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="mb-3" v-if="!editingUser">
                <label class="form-label">Password *</label>
                <input 
                  v-model="userForm.password" 
                  type="password" 
                  class="form-control" 
                  :required="!editingUser"
                >
              </div>

              <!-- Permissions Preview -->
              <div class="mb-3" v-if="userForm.userType">
                <label class="form-label">Permissions for {{ getRoleDisplayName(userForm.userType) }}</label>
                <div class="bg-light p-3 rounded">
                  <ul class="mb-0">
                    <li v-for="permission in getUserPermissions(userForm.userType)" :key="permission">
                      {{ permission }}
                    </li>
                  </ul>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveUser">
              {{ editingUser ? 'Update User' : 'Create User' }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showAddUserModal" class="modal-backdrop fade show"></div>
  </div>
</template>

<script>
import UserService from '@/services/userService';
import Swal from 'sweetalert2';

export default {
  name: 'UserManagement',
  emits: ['user-created', 'user-updated', 'user-deleted'],
  data() {
    return {
      users: [],
      userTypes: [],
      isLoading: false,
      filters: {
        municipality: '',
        role: '',
        search: ''
      },
      showAddUserModal: false,
      editingUser: null,
      userForm: {
        firstName: '',
        lastName: '',
        username: '',
        email: '',
        municipality: '',
        userType: '',
        password: ''
      },
      currentPage: 1,
      perPage: 10
    };
  },
  computed: {
    filteredUsers() {
      let filtered = this.users;

      if (this.filters.municipality) {
        filtered = filtered.filter(user => user.municipality === this.filters.municipality);
      }

      if (this.filters.role) {
        filtered = filtered.filter(user => user.userType == this.filters.role);
      }

      if (this.filters.search) {
        const search = this.filters.search.toLowerCase();
        filtered = filtered.filter(user => 
          (user.firstName || '').toLowerCase().includes(search) ||
          (user.lastName || '').toLowerCase().includes(search) ||
          (user.username || '').toLowerCase().includes(search) ||
          (user.email || '').toLowerCase().includes(search)
        );
      }

      return filtered;
    },
    totalPages() {
      return Math.ceil(this.filteredUsers.length / this.perPage);
    },
    municipalities() {
      return UserService.getMunicipalities();
    }
  },
  async mounted() {
    await this.fetchUsers();
    await this.fetchUserTypes();
  },
  methods: {
    async fetchUsers() {
      try {
        this.isLoading = true;
        const response = await UserService.getUsers(this.filters);
        this.users = response.data || [];
      } catch (error) {
        console.error('Error fetching users:', error);
        Swal.fire({
          title: 'Error',
          text: error.message || 'Failed to fetch users',
          icon: 'error'
        });
      } finally {
        this.isLoading = false;
      }
    },
    async fetchUserTypes() {
      try {
        const response = await UserService.getUserTypes();
        this.userTypes = response.data || [];
      } catch (error) {
        console.error('Error fetching user types:', error);
      }
    },
    showAddModal() {
      this.editingUser = null;
      this.userForm = {
        firstName: '',
        lastName: '',
        username: '',
        email: '',
        municipality: '',
        userType: '',
        password: ''
      };
      this.showAddUserModal = true;
    },
    getRoleDisplayName(roleId) {
      const userType = this.userTypes.find(type => type.id == roleId);
      return userType ? userType.description : UserService.getRoleDisplayName(roleId);
    },
    getRoleBadgeClass(roleId) {
      return UserService.getRoleBadgeClass(roleId);
    },
    getUserPermissions(roleId) {
      return UserService.getUserPermissions(roleId);
    },
    clearFilters() {
      this.filters = {
        municipality: '',
        role: '',
        search: ''
      };
      this.fetchUsers();
    },
    editUser(user) {
      this.editingUser = user;
      this.userForm = { 
        ...user,
        userType: user.userType.toString() // Convert to string for select binding
      };
      delete this.userForm.password; // Don't prefill password
      this.showAddUserModal = true;
    },
    async deleteUser(user) {
      const result = await Swal.fire({
        title: 'Are you sure?',
        text: `This will delete ${user.firstName} ${user.lastName}. This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete user!'
      });

      if (result.isConfirmed) {
        try {
          await UserService.deleteUser(user.id);
          
          // Remove user from local array
          const index = this.users.findIndex(u => u.id === user.id);
          if (index > -1) {
            this.users.splice(index, 1);
          }

          // Emit event to parent
          this.$emit('user-deleted', user);

          Swal.fire({
            title: 'Deleted!',
            text: 'User has been deleted successfully.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
          });
        } catch (error) {
          Swal.fire({
            title: 'Error',
            text: error.message || 'Failed to delete user',
            icon: 'error'
          });
        }
      }
    },
    async toggleUserStatus(user) {
      try {
        const newStatus = user.status === 1 ? 0 : 1;
        const userData = { ...user, status: newStatus };
        
        await UserService.updateUser(userData);
        
        // Update local data safely to avoid race condition
        const userIndex = this.users.findIndex(u => u.id === user.id);
        if (userIndex > -1) {
          this.users[userIndex] = { ...this.users[userIndex], status: newStatus };
        }

        Swal.fire({
          title: 'Success',
          text: `User ${newStatus === 1 ? 'activated' : 'deactivated'} successfully`,
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        });
      } catch (error) {
        Swal.fire({
          title: 'Error',
          text: error.message || 'Failed to update user status',
          icon: 'error'
        });
      }
    },
    async saveUser() {
      // Validation
      if (!this.userForm.firstName || !this.userForm.lastName || !this.userForm.username || 
          !this.userForm.email || !this.userForm.municipality || !this.userForm.userType) {
        Swal.fire({
          title: 'Validation Error',
          text: 'Please fill all required fields',
          icon: 'warning'
        });
        return;
      }

      // Password validation for new users
      if (!this.editingUser && !this.userForm.password) {
        Swal.fire({
          title: 'Validation Error',
          text: 'Password is required for new users',
          icon: 'warning'
        });
        return;
      }

      try {
        let result;
        if (this.editingUser) {
          // Update existing user
          result = await UserService.updateUser({
            ...this.userForm,
            id: this.editingUser.id
          });
          
          // Update local data
          const index = this.users.findIndex(u => u.id === this.editingUser.id);
          if (index > -1) {
            this.users[index] = { 
              ...this.users[index], 
              ...this.userForm,
              userType: parseInt(this.userForm.userType)
            };
          }

          // Emit event to parent
          this.$emit('user-updated', this.users[index]);

          Swal.fire({
            title: 'Success',
            text: 'User updated successfully',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
          });
        } else {
          // Create new user
          result = await UserService.createUser({
            ...this.userForm,
            userType: parseInt(this.userForm.userType)
          });
          
          // Refresh users list
          await this.fetchUsers();

          // Emit event to parent
          this.$emit('user-created', result);

          Swal.fire({
            title: 'Success',
            text: 'User created successfully',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
          });
        }

        this.closeModal();
      } catch (error) {
        Swal.fire({
          title: 'Error',
          text: error.message || 'Failed to save user',
          icon: 'error'
        });
      }
    },
    closeModal() {
      this.showAddUserModal = false;
      this.editingUser = null;
      this.userForm = {
        firstName: '',
        lastName: '',
        username: '',
        email: '',
        municipality: '',
        userType: '',
        password: ''
      };
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    }
  }
};
</script>

<style scoped>
.permissions-list {
  max-width: 200px;
  font-size: 0.75rem;
}

.modal.show {
  background-color: rgba(0, 0, 0, 0.5);
}

.table th {
  font-weight: 600;
  font-size: 0.75rem;
  text-transform: uppercase;
}

.badge {
  font-size: 0.65rem;
}
</style>