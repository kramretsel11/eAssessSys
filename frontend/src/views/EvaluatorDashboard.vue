<template>
  <div class="container-fluid py-4">
    <!-- Dashboard Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex align-items-center">
              <div>
                <h5 class="mb-0">
                  <i class="fas fa-user-check text-info me-2"></i>
                  Evaluator Dashboard
                </h5>
                <p class="text-sm mb-0">Property Assessment and Certificate Management</p>
              </div>
              <div class="ms-auto">
                <div class="badge bg-gradient-info">
                  <i class="fas fa-clock me-1"></i>{{ currentTime }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Pending Reviews</p>
                  <h5 class="font-weight-bolder mb-0">{{ stats.pendingReviews }}</h5>
                  <small class="text-warning">
                    <i class="fas fa-hourglass-half me-1"></i>Requires Action
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                  <i class="fas fa-clipboard-list text-lg opacity-10"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Completed Today</p>
                  <h5 class="font-weight-bolder mb-0">{{ stats.completedToday }}</h5>
                  <small class="text-success">
                    <i class="fas fa-check me-1"></i>Evaluations
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                  <i class="fas fa-check-circle text-lg opacity-10"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Certificates</p>
                  <h5 class="font-weight-bolder mb-0">{{ stats.certificates }}</h5>
                  <small class="text-info">
                    <i class="fas fa-certificate me-1"></i>Generated
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                  <i class="fas fa-certificate text-lg opacity-10"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Average Time</p>
                  <h5 class="font-weight-bolder mb-0">{{ stats.averageTime }}</h5>
                  <small class="text-dark">
                    <i class="fas fa-clock me-1"></i>Per Evaluation
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-dark shadow text-center border-radius-md">
                  <i class="fas fa-stopwatch text-lg opacity-10"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <h6>Quick Actions</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3 mb-3">
                <button class="btn btn-primary w-100 h-100" @click="goToEvaluations">
                  <div class="d-flex flex-column align-items-center py-2">
                    <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                    <span>Review Evaluations</span>
                    <small class="text-light opacity-8">{{ stats.pendingReviews }} pending</small>
                  </div>
                </button>
              </div>
              <div class="col-md-3 mb-3">
                <button class="btn btn-success w-100 h-100" @click="goToCertificates">
                  <div class="d-flex flex-column align-items-center py-2">
                    <i class="fas fa-certificate fa-2x mb-2"></i>
                    <span>Generate Certificates</span>
                    <small class="text-light opacity-8">Create new certificates</small>
                  </div>
                </button>
              </div>
              <div class="col-md-3 mb-3">
                <button class="btn btn-info w-100 h-100" @click="viewReports">
                  <div class="d-flex flex-column align-items-center py-2">
                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                    <span>View Reports</span>
                    <small class="text-light opacity-8">Assessment reports</small>
                  </div>
                </button>
              </div>
              <div class="col-md-3 mb-3">
                <button class="btn btn-warning w-100 h-100" @click="goToSettings">
                  <div class="d-flex flex-column align-items-center py-2">
                    <i class="fas fa-cog fa-2x mb-2"></i>
                    <span>Settings</span>
                    <small class="text-light opacity-8">Configure preferences</small>
                  </div>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activities and Upcoming Tasks -->
    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h6>Recent Evaluations</h6>
              <router-link to="/evaluations" class="text-primary text-sm">
                View All <i class="fas fa-arrow-right ms-1"></i>
              </router-link>
            </div>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Request</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Property</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                    <th class="text-secondary opacity-7">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="evaluation in recentEvaluations" :key="evaluation.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ evaluation.requestNumber }}</h6>
                          <p class="text-xs text-secondary mb-0">{{ evaluation.requestType }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ evaluation.propertyAddress }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ evaluation.municipality }}</p>
                      </div>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="badge badge-sm" :class="getStatusClass(evaluation.status)">
                        {{ evaluation.status }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">
                        {{ formatDate(evaluation.submittedDate) }}
                      </span>
                    </td>
                    <td class="align-middle">
                      <button class="btn btn-sm btn-outline-primary" @click="viewEvaluation(evaluation)">
                        <i class="fas fa-eye me-1"></i>View
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-header pb-0">
            <h6>Today's Tasks</h6>
          </div>
          <div class="card-body">
            <div class="timeline timeline-one-side">
              <div class="timeline-block mb-3" v-for="task in todaysTasks" :key="task.id">
                <span class="timeline-step">
                  <i class="fas fa-tasks" :class="task.completed ? 'text-success' : 'text-warning'"></i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">{{ task.title }}</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">{{ task.time }}</p>
                  <p class="text-sm mt-3 mb-2">{{ task.description }}</p>
                  <button 
                    v-if="!task.completed" 
                    class="btn btn-sm btn-success" 
                    @click="completeTask(task)">
                    Mark Complete
                  </button>
                  <span v-else class="badge bg-success">Completed</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mt-4">
          <div class="card-header pb-0">
            <h6>Quick Stats</h6>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <span>This Week</span>
              <span class="font-weight-bold">{{ weeklyStats.evaluations }} evaluations</span>
            </div>
            <div class="progress mb-3">
              <div class="progress-bar bg-gradient-success" :style="`width: ${weeklyStats.progress}%`"></div>
            </div>
            
            <div class="d-flex justify-content-between">
              <span>This Month</span>
              <span class="font-weight-bold">{{ monthlyStats.evaluations }} evaluations</span>
            </div>
            <div class="progress mb-3">
              <div class="progress-bar bg-gradient-info" :style="`width: ${monthlyStats.progress}%`"></div>
            </div>

            <div class="d-flex justify-content-between">
              <span>Success Rate</span>
              <span class="font-weight-bold text-success">{{ successRate }}%</span>
            </div>
            <div class="progress">
              <div class="progress-bar bg-gradient-success" :style="`width: ${successRate}%`"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import PermissionService from '@/services/permissions.js';
import AuthService from '@/services/auth.js';
import Swal from 'sweetalert2';

export default {
  name: 'EvaluatorDashboard',
  setup() {
    const router = useRouter();
    const currentTime = ref(new Date().toLocaleTimeString());
    const timeInterval = ref(null);

    const stats = ref({
      pendingReviews: 5,
      completedToday: 3,
      certificates: 12,
      averageTime: '2.5h'
    });

    const recentEvaluations = ref([
      {
        id: 1,
        requestNumber: 'REQ-2025-001',
        requestType: 'Property Assessment',
        propertyAddress: 'Lot 1 Block 2, Barangay Suklayin',
        municipality: 'Baler',
        status: 'pending',
        submittedDate: '2025-01-15'
      },
      {
        id: 2,
        requestNumber: 'REQ-2025-002',
        requestType: 'Property Revaluation',
        propertyAddress: 'Zone 3, Barangay Sabang',
        municipality: 'Baler',
        status: 'in-progress',
        submittedDate: '2025-01-14'
      },
      {
        id: 3,
        requestNumber: 'REQ-2025-003',
        requestType: 'Property Assessment',
        propertyAddress: 'Purok 1, Barangay Zabali',
        municipality: 'Baler',
        status: 'approved',
        submittedDate: '2025-01-13'
      }
    ]);

    const todaysTasks = ref([
      {
        id: 1,
        title: 'Review property assessments',
        time: '9:00 AM',
        description: 'Review 5 pending property assessment requests',
        completed: false
      },
      {
        id: 2,
        title: 'Generate certificates',
        time: '11:00 AM',
        description: 'Generate certificates for approved evaluations',
        completed: false
      },
      {
        id: 3,
        title: 'Site visit - Barangay Suklayin',
        time: '2:00 PM',
        description: 'Conduct on-site property evaluation',
        completed: false
      },
      {
        id: 4,
        title: 'Update assessment records',
        time: '4:00 PM',
        description: 'Update property assessment database',
        completed: true
      }
    ]);

    const weeklyStats = ref({
      evaluations: 15,
      progress: 75
    });

    const monthlyStats = ref({
      evaluations: 45,
      progress: 60
    });

    const successRate = computed(() => {
      const total = recentEvaluations.value.length;
      const approved = recentEvaluations.value.filter(e => e.status === 'approved').length;
      return total > 0 ? Math.round((approved / total) * 100) : 0;
    });

    const startTimeUpdater = () => {
      timeInterval.value = setInterval(() => {
        currentTime.value = new Date().toLocaleTimeString();
      }, 1000);
    };

    const goToEvaluations = () => {
      router.push('/evaluations');
    };

    const goToCertificates = () => {
      router.push('/certificates');
    };

    const viewReports = () => {
      Swal.fire({
        title: 'Reports',
        text: 'Reports feature coming soon!',
        icon: 'info'
      });
    };

    const goToSettings = () => {
      router.push('/settings');
    };

    const viewEvaluation = (evaluation) => {
      router.push(`/evaluations?view=${evaluation.id}`);
    };

    const completeTask = (task) => {
      task.completed = true;
      Swal.fire({
        title: 'Task Completed',
        text: `"${task.title}" has been marked as completed`,
        icon: 'success',
        timer: 2000
      });
    };

    const getStatusClass = (status) => {
      switch (status) {
        case 'pending': return 'bg-gradient-warning';
        case 'in-progress': return 'bg-gradient-info';
        case 'approved': return 'bg-gradient-success';
        case 'rejected': return 'bg-gradient-danger';
        default: return 'bg-gradient-secondary';
      }
    };

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString();
    };

    const fetchDashboardData = async () => {
      try {
        // Fetch dashboard statistics
        const statsResponse = await fetch('http://localhost:8080/e_assessment/api/v1/evaluator/stats', {
          headers: {
            ...AuthService.getAuthHeader(),
            'Content-Type': 'application/json'
          }
        });
        
        if (statsResponse.ok) {
          const statsResult = await statsResponse.json();
          if (statsResult.status === 'success') {
            stats.value = statsResult.data;
          }
        }

        // Fetch recent evaluations
        const evaluationsResponse = await fetch('http://localhost:8080/e_assessment/api/v1/evaluator/recent', {
          headers: {
            ...AuthService.getAuthHeader(),
            'Content-Type': 'application/json'
          }
        });
        
        if (evaluationsResponse.ok) {
          const evaluationsResult = await evaluationsResponse.json();
          if (evaluationsResult.status === 'success') {
            recentEvaluations.value = evaluationsResult.data;
          }
        }
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
        Swal.fire({
          title: 'Warning',
          text: 'Some dashboard data could not be loaded',
          icon: 'warning',
          timer: 3000
        });
      }
    };

    onMounted(() => {
      // Check permissions
      if (!PermissionService.hasRole('evaluator')) {
        Swal.fire({
          title: 'Access Denied',
          text: 'You do not have evaluator permissions',
          icon: 'error'
        }).then(() => {
          router.push('/dashboard');
        });
        return;
      }

      startTimeUpdater();
      fetchDashboardData();
    });

    onUnmounted(() => {
      if (timeInterval.value) {
        clearInterval(timeInterval.value);
      }
    });

    return {
      currentTime,
      stats,
      recentEvaluations,
      todaysTasks,
      weeklyStats,
      monthlyStats,
      successRate,
      goToEvaluations,
      goToCertificates,
      viewReports,
      goToSettings,
      viewEvaluation,
      completeTask,
      getStatusClass,
      formatDate
    };
  }
};
</script>

<style scoped>
.card {
  border-radius: 12px;
}

.icon-shape {
  width: 56px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.timeline-one-side .timeline-step {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border: 2px solid #dee2e6;
  position: relative;
}

.timeline-content {
  margin-left: 15px;
}

.progress {
  height: 8px;
}
</style>