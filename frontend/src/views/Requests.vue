<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card shadow-lg border-0">
          <div class="card-header bg-gradient-primary">
            <div class="row align-items-center">
              <div class="col">
                <h5 class="text-white mb-0">
                  <i class="fas fa-clipboard-list me-2"></i>
                  Assessment Requests Management
                </h5>
                <p class="text-white-75 mb-0 small">Manage property assessment requests</p>
              </div>
              <div class="col-auto">
                <div class="d-flex gap-2">
                  <button class="btn btn-white btn-sm" @click="startNew">
                    <i class="fas fa-plus me-1"></i>
                    Add New Request
                  </button>
                  <button class="btn btn-outline-light btn-sm" @click="logout" title="Logout">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Logout
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body p-4">
            
            <!-- Enhanced Form Section -->
            <div v-if="showForm" class="card shadow-sm border-0 mb-4">
              <div class="card-header bg-light">
                <div class="row align-items-center">
                  <div class="col">
                    <h6 class="text-dark mb-0">
                      <i class="fas fa-edit me-2"></i>
                      {{ editing ? 'Edit Assessment Request' : 'New Assessment Request' }}
                    </h6>
                  </div>
                  <div class="col-auto">
                    <div class="progress" style="width: 200px; height: 6px;">
                      <div class="progress-bar bg-success" 
                           :style="{ width: (step/3*100) + '%' }"
                           role="progressbar"></div>
                    </div>
                    <small class="text-muted d-block mt-1">Step {{ step }} of 3</small>
                  </div>
                </div>
              </div>
              <div class="card-body p-4">
                
                <!-- Step 1: Property & Owner Information -->
                <div v-show="step === 1" class="step-content">
                  <div class="row mb-4">
                    <div class="col-12">
                      <h6 class="text-primary border-bottom pb-2 mb-4">
                        <i class="fas fa-home me-2"></i>
                        Property & Owner Information
                      </h6>
                    </div>
                  </div>
                  
                  <!-- Property Details -->
                  <div class="row g-3 mb-4">
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-hashtag me-1 text-primary"></i>
                        ARP Number *
                      </label>
                      <input v-model="form.arpNo" 
                             class="form-control form-control-lg border-2" 
                             placeholder="Enter ARP Number"
                             required />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-map-pin me-1 text-primary"></i>
                        PIN *
                      </label>
                      <input v-model="form.pin" 
                             class="form-control form-control-lg border-2" 
                             placeholder="Property Identification Number"
                             required />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-list-alt me-1 text-primary"></i>
                        Assessment Type *
                      </label>
                      <select v-model="form.categoryId" 
                              class="form-select form-select-lg border-2"
                              required>
                        <option value="">Choose Assessment Type</option>
                        <option v-for="category in categories" 
                                :key="category.id" 
                                :value="category.id">
                          {{ category.name }}
                        </option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-user me-1 text-primary"></i>
                        Owner Name *
                      </label>
                      <input v-model="form.ownerName" 
                             class="form-control form-control-lg border-2" 
                             placeholder="Property Owner Full Name"
                             required />
                    </div>
                  </div>

                  <!-- Owner Information -->
                  <div class="row mb-4">
                    <div class="col-12">
                      <h6 class="text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-users me-2"></i>
                        Owner Details
                      </h6>
                    </div>
                  </div>
                  
                  <div class="row g-3 mb-4">
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-heart me-1 text-danger"></i>
                        Married To
                      </label>
                      <input v-model="form.marriedTo" 
                             class="form-control border-2" 
                             placeholder="Spouse Name (if applicable)" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-phone me-1 text-success"></i>
                        Contact Number *
                      </label>
                      <input v-model="form.contactNo" 
                             class="form-control border-2" 
                             placeholder="Mobile/Phone Number"
                             required />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-map-marker-alt me-1 text-warning"></i>
                        Owner Address *
                      </label>
                      <textarea v-model="form.ownerAddress" 
                                class="form-control border-2" 
                                rows="2"
                                placeholder="Complete Address"
                                required></textarea>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-receipt me-1 text-info"></i>
                        TIN
                      </label>
                      <input v-model="form.tin" 
                             class="form-control border-2" 
                             placeholder="Tax Identification Number" />
                    </div>
                  </div>

                  <!-- Administrator Information -->
                  <div class="row mb-4">
                    <div class="col-12">
                      <h6 class="text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-user-tie me-2"></i>
                        Administrator Information (if applicable)
                      </h6>
                    </div>
                  </div>
                  
                  <div class="row g-3 mb-4">
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-user-shield me-1 text-primary"></i>
                        Administrator Name
                      </label>
                      <input v-model="form.adminName" 
                             class="form-control border-2" 
                             placeholder="Administrator Full Name" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-heart me-1 text-danger"></i>
                        Admin Married To
                      </label>
                      <input v-model="form.adminMarriedTo" 
                             class="form-control border-2" 
                             placeholder="Administrator's Spouse" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-map-marker-alt me-1 text-warning"></i>
                        Administrator Address
                      </label>
                      <textarea v-model="form.adminAddress" 
                                class="form-control border-2" 
                                rows="2"
                                placeholder="Administrator's Address"></textarea>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-receipt me-1 text-info"></i>
                        Administrator TIN
                      </label>
                      <input v-model="form.adminTin" 
                             class="form-control border-2" 
                             placeholder="Administrator's TIN" />
                    </div>
                  </div>

                  <!-- Location Information -->
                  <div class="row mb-4">
                    <div class="col-12">
                      <h6 class="text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-map me-2"></i>
                        Property Location
                      </h6>
                    </div>
                  </div>
                  
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-road me-1 text-primary"></i>
                        Street
                      </label>
                      <input v-model="form.street" 
                             class="form-control border-2" 
                             placeholder="Street Name/Number" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-map-signs me-1 text-success"></i>
                        Barangay *
                      </label>
                      <input v-model="form.barangay" 
                             class="form-control border-2" 
                             placeholder="Barangay Name"
                             required />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-city me-1 text-warning"></i>
                        Municipality *
                      </label>
                      <input v-model="form.municipality" 
                             class="form-control border-2" 
                             placeholder="Municipality"
                             required />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-flag me-1 text-danger"></i>
                        Province *
                      </label>
                      <input v-model="form.province" 
                             class="form-control border-2" 
                             placeholder="Province"
                             required />
                    </div>
                  </div>

                  <!-- Legal Documents -->
                  <div class="row mb-4 mt-4">
                    <div class="col-12">
                      <h6 class="text-secondary border-bottom pb-2 mb-3">
                        <i class="fas fa-file-contract me-2"></i>
                        Legal Documents & Survey Details
                      </h6>
                    </div>
                  </div>
                  
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-certificate me-1 text-primary"></i>
                        OCT/TCT/CLOA Number
                      </label>
                      <input v-model="form.octTctCloaNo" 
                             class="form-control border-2" 
                             placeholder="Title Number" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-search-location me-1 text-success"></i>
                        Survey Number
                      </label>
                      <input v-model="form.surveyNo" 
                             class="form-control border-2" 
                             placeholder="Survey Number" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-map-marked me-1 text-warning"></i>
                        Lot Number
                      </label>
                      <input v-model="form.lotNo" 
                             class="form-control border-2" 
                             placeholder="Lot Number" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label text-dark fw-bold">
                        <i class="fas fa-ruler-combined me-1 text-info"></i>
                        Area (sq.m.)
                      </label>
                      <input v-model="form.areaNo" 
                             type="number"
                             step="0.01"
                             class="form-control border-2" 
                             placeholder="Total Area in Square Meters" />
                    </div>
                  </div>
                </div>

                <!-- Step 2: Building & Technical Details -->
                <div v-show="step === 2" class="step-content">
                  <div class="row mb-4">
                    <div class="col-12">
                      <h6 class="text-primary border-bottom pb-2 mb-4">
                        <i class="fas fa-building me-2"></i>
                        Building & Technical Information
                      </h6>
                    </div>
                  </div>

                  <!-- General Description -->
                  <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                      <h6 class="text-dark mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        General Description
                      </h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-home me-1 text-primary"></i>
                            Kind of Building *
                          </label>
                          <select v-model="form.generalDescription.kindOfBldg" 
                                  class="form-select form-select-lg border-2"
                                  required>
                            <option value="">Select Building Type</option>
                            <option value="Residential">🏠 Residential</option>
                            <option value="Agricultural">🌾 Agricultural</option>
                            <option value="Commercial">🏢 Commercial</option>
                            <option value="Industrial">🏭 Industrial</option>
                            <option value="Mineral">⛏️ Mineral</option>
                            <option value="Timberland">🌲 Timberland</option>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-tools me-1 text-warning"></i>
                            Structural Type
                          </label>
                          <input v-model="form.generalDescription.structuralType" 
                                 class="form-control border-2" 
                                 placeholder="e.g., Concrete, Wood, Mixed" />
                        </div>
                        <div class="col-md-6">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-file-contract me-1 text-success"></i>
                            Building Permit Number
                          </label>
                          <input v-model="form.generalDescription.bldgPermit" 
                                 class="form-control border-2" 
                                 placeholder="Building Permit Number" />
                        </div>
                        <div class="col-md-6">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-calendar me-1 text-info"></i>
                            Date Issued
                          </label>
                          <input v-model="form.generalDescription.dateIssued" 
                                 type="date" 
                                 class="form-control border-2" />
                        </div>
                        <div class="col-md-6">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-certificate me-1 text-primary"></i>
                            CCT Number
                          </label>
                          <input v-model="form.generalDescription.cct" 
                                 class="form-control border-2" 
                                 placeholder="Certificate of Completion & Testing" />
                        </div>
                        <div class="col-md-6">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-calendar-check me-1 text-success"></i>
                            Certificate Completion Date
                          </label>
                          <input v-model="form.generalDescription.certificateCompletionIssuedOn" 
                                 type="date" 
                                 class="form-control border-2" />
                        </div>
                        <div class="col-md-6">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-calendar-alt me-1 text-warning"></i>
                            Certificate of Occupancy Issued On
                          </label>
                          <input v-model="form.generalDescription.certificateOccupancyIssuedOn" 
                                 type="date" 
                                 class="form-control border-2" />
                        </div>
                        <div class="col-md-6">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-calendar me-1 text-primary"></i>
                            Date Constructed
                          </label>
                          <input v-model="form.generalDescription.dateConstructed" 
                                 type="date" 
                                 class="form-control border-2" />
                        </div>
                        <div class="col-md-6">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-calendar-check me-1 text-success"></i>
                            Date Occupied
                          </label>
                          <input v-model="form.generalDescription.dateOccupied" 
                                 type="date" 
                                 class="form-control border-2" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Building Details -->
                  <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                      <h6 class="text-dark mb-0">
                        <i class="fas fa-ruler-combined me-2"></i>
                        Building Specifications
                      </h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-4">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-birthday-cake me-1 text-warning"></i>
                            Building Age (Years)
                          </label>
                          <input v-model="form.generalDescription.bldgAge" 
                                 type="number" 
                                 class="form-control border-2"
                                 placeholder="Building Age" />
                        </div>
                        <div class="col-md-4">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-layer-group me-1 text-info"></i>
                            No. of Storeys
                          </label>
                          <input v-model="form.generalDescription.noOfStoreys" 
                                 type="number" 
                                 class="form-control border-2"
                                 placeholder="Number of Floors" />
                        </div>
                        <div class="col-md-4">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-square me-1 text-primary"></i>
                            1st Floor Area (sq.m.)
                          </label>
                          <input v-model="form.generalDescription.area1st" 
                                 type="number" 
                                 step="0.01"
                                 class="form-control border-2" 
                                 @input="calculateTotalFloorArea"
                                 placeholder="1st Floor Area" />
                        </div>
                        <div class="col-md-4">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-square me-1 text-success"></i>
                            2nd Floor Area (sq.m.)
                          </label>
                          <input v-model="form.generalDescription.area2nd" 
                                 type="number" 
                                 step="0.01"
                                 class="form-control border-2" 
                                 @input="calculateTotalFloorArea"
                                 placeholder="2nd Floor Area" />
                        </div>
                        <div class="col-md-4">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-square me-1 text-warning"></i>
                            3rd Floor Area (sq.m.)
                          </label>
                          <input v-model="form.generalDescription.area3rd" 
                                 type="number" 
                                 step="0.01"
                                 class="form-control border-2" 
                                 @input="calculateTotalFloorArea"
                                 placeholder="3rd Floor Area" />
                        </div>
                        <div class="col-md-4">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-square me-1 text-danger"></i>
                            4th Floor Area (sq.m.)
                          </label>
                          <input v-model="form.generalDescription.area4th" 
                                 type="number" 
                                 step="0.01"
                                 class="form-control border-2" 
                                 @input="calculateTotalFloorArea"
                                 placeholder="4th Floor Area" />
                        </div>
                        <div class="col-md-4">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-calculator me-1 text-info"></i>
                            Total Floor Area (sq.m.)
                          </label>
                          <input v-model="form.generalDescription.totalFloorArea" 
                                 type="number" 
                                 step="0.01"
                                 class="form-control border-2" 
                                 readonly
                                 placeholder="Auto-calculated" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Structural Checklist -->
                  <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                      <h6 class="text-dark mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Structural Checklist
                      </h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-4">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-home me-1 text-danger"></i>
                            Roof
                          </label>
                          <textarea v-model="form.structuralChecklist.Roof" 
                                    class="form-control border-2" 
                                    rows="3" 
                                    placeholder="Enter roof materials and specifications"></textarea>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-layer-group me-1 text-warning"></i>
                            Flooring
                          </label>
                          <textarea v-model="form.structuralChecklist.Flooring" 
                                    class="form-control border-2" 
                                    rows="3" 
                                    placeholder="Enter flooring materials and specifications"></textarea>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-border-all me-1 text-info"></i>
                            Walls & Partitions
                          </label>
                          <textarea v-model="form.structuralChecklist.WallsPartions" 
                                    class="form-control border-2" 
                                    rows="3" 
                                    placeholder="Enter walls and partitions specifications"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Additional Items -->
                  <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                      <h6 class="text-dark mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Additional Items
                      </h6>
                    </div>
                    <div class="card-body">
                      <label class="form-label text-dark fw-bold">Additional Items or Features</label>
                      <textarea v-model="form.additionalItems" 
                                class="form-control border-2" 
                                rows="3" 
                                placeholder="Enter additional items, features, or improvements"></textarea>
                    </div>
                  </div>

                  <!-- Property Appraisal -->
                  <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                      <h6 class="text-dark mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Property Appraisal
                      </h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-3">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-cube me-1 text-primary"></i>
                            Unit
                          </label>
                          <input v-model="form.propertyAppraisal.unit" 
                                 class="form-control border-2"
                                 placeholder="Unit of measurement" />
                        </div>
                        <div class="col-md-3">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-dollar-sign me-1 text-success"></i>
                            Unit Cost
                          </label>
                          <input v-model="form.propertyAppraisal.unitCost" 
                                 type="number" 
                                 step="0.01"
                                 class="form-control border-2"
                                 placeholder="Cost per unit" />
                        </div>
                        <div class="col-md-3">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-calculator me-1 text-warning"></i>
                            Computation
                          </label>
                          <input v-model="form.propertyAppraisal.computation" 
                                 class="form-control border-2"
                                 placeholder="Computation formula" />
                        </div>
                        <div class="col-md-3">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-sum me-1 text-info"></i>
                            Sub Total
                          </label>
                          <input v-model="form.propertyAppraisal.subTotal" 
                                 type="number" 
                                 step="0.01"
                                 class="form-control border-2" 
                                 readonly
                                 placeholder="Auto-calculated" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Property Boundaries -->
                  <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                      <h6 class="text-dark mb-0">
                        <i class="fas fa-map me-2"></i>
                        Property Boundaries
                      </h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-3">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-compass me-1 text-primary"></i>
                            North
                          </label>
                          <input v-model="form.propertyBounderies.north" 
                                 class="form-control border-2"
                                 placeholder="North boundary" />
                        </div>
                        <div class="col-md-3">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-compass me-1 text-success"></i>
                            East
                          </label>
                          <input v-model="form.propertyBounderies.east" 
                                 class="form-control border-2"
                                 placeholder="East boundary" />
                        </div>
                        <div class="col-md-3">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-compass me-1 text-warning"></i>
                            West
                          </label>
                          <input v-model="form.propertyBounderies.west" 
                                 class="form-control border-2"
                                 placeholder="West boundary" />
                        </div>
                        <div class="col-md-3">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-compass me-1 text-danger"></i>
                            South
                          </label>
                          <input v-model="form.propertyBounderies.south" 
                                 class="form-control border-2"
                                 placeholder="South boundary" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Property Assessment -->
                  <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                      <h6 class="text-dark mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Property Assessment
                      </h6>
                    </div>
                    <div class="card-body">
                      <div class="row g-3 mb-3">
                        <div class="col-md-4">
                          <div class="form-check form-switch">
                            <input v-model="form.propertyAssessment.taxable" 
                                   type="checkbox" 
                                   class="form-check-input" 
                                   id="taxable">
                            <label class="form-check-label text-dark fw-bold" for="taxable">
                              <i class="fas fa-coins me-1 text-warning"></i>
                              Taxable
                            </label>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-check form-switch">
                            <input v-model="form.propertyAssessment.exempt" 
                                   type="checkbox" 
                                   class="form-check-input" 
                                   id="exempt">
                            <label class="form-check-label text-dark fw-bold" for="exempt">
                              <i class="fas fa-shield-alt me-1 text-info"></i>
                              Exempt
                            </label>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label text-dark fw-bold">
                            <i class="fas fa-calendar-alt me-1 text-primary"></i>
                            Effectivity
                          </label>
                          <input v-model="form.propertyAssessment.effectivity" 
                                 type="date" 
                                 class="form-control border-2" />
                        </div>
                      </div>

                      <!-- Assessment Values -->
                      <div class="mt-4">
                        <h6 class="text-secondary mb-3">
                          <i class="fas fa-chart-line me-2"></i>
                          Assessment Values
                        </h6>
                        <div v-for="(value, index) in form.propertyAssessment.values" 
                             :key="index" 
                             class="row g-3 mb-3 p-3 border rounded">
                          <div class="col-md-3">
                            <label class="form-label text-dark fw-bold">Actual Use</label>
                            <input v-model="value.actualUse" 
                                   class="form-control border-2"
                                   placeholder="Actual use" />
                          </div>
                          <div class="col-md-3">
                            <label class="form-label text-dark fw-bold">Market Value</label>
                            <input v-model="value.marketValue" 
                                   type="number" 
                                   step="0.01"
                                   class="form-control border-2"
                                   placeholder="Market value" />
                          </div>
                          <div class="col-md-2">
                            <label class="form-label text-dark fw-bold">Assessment Level (%)</label>
                            <input v-model="value.assessmentLevel" 
                                   type="number" 
                                   step="0.01"
                                   class="form-control border-2"
                                   placeholder="Level %" />
                          </div>
                          <div class="col-md-3">
                            <label class="form-label text-dark fw-bold">Assessed Value</label>
                            <input v-model="value.assessedValue" 
                                   type="number" 
                                   step="0.01"
                                   class="form-control border-2"
                                   placeholder="Assessed value" />
                          </div>
                          <div class="col-md-1 d-flex align-items-end">
                            <button @click="removeAssessmentValue(index)" 
                                    type="button" 
                                    class="btn btn-sm btn-outline-danger">
                              <i class="fas fa-times"></i>
                            </button>
                          </div>
                        </div>
                        <button @click="addAssessmentValue" 
                                type="button" 
                                class="btn btn-sm btn-outline-primary mt-2">
                          <i class="fas fa-plus me-1"></i>
                          Add Assessment Value
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Step 3: Summary & Memoranda -->
                <div v-show="step === 3" class="step-content">
                  <div class="row mb-4">
                    <div class="col-12">
                      <h6 class="text-primary border-bottom pb-2 mb-4">
                        <i class="fas fa-check-circle me-2"></i>
                        Summary & Memoranda
                      </h6>
                    </div>
                  </div>
              
              <!-- Step 1 Summary -->
              <div class="card mb-3">
                <div class="card-header">
                  <h6 class="mb-0">Property & Owner Information Summary</h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <p><strong>ARP No:</strong> {{ form.arpNo }}</p>
                      <p><strong>PIN:</strong> {{ form.pin }}</p>
                      <p><strong>Owner Name:</strong> {{ form.ownerName }}</p>
                      <p><strong>Married To:</strong> {{ form.marriedTo }}</p>
                      <p><strong>Contact No:</strong> {{ form.contactNo }}</p>
                      <p><strong>TIN:</strong> {{ form.tin }}</p>
                    </div>
                    <div class="col-md-6">
                      <p><strong>Admin Name:</strong> {{ form.adminName }}</p>
                      <p><strong>Admin Contact:</strong> {{ form.adminContactNo }}</p>
                      <p><strong>Street:</strong> {{ form.street }}</p>
                      <p><strong>Barangay:</strong> {{ form.barangay }}</p>
                      <p><strong>Owner Address:</strong> {{ form.ownerAddress }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Step 2 Summary -->
              <div class="card mb-3">
                <div class="card-header">
                  <h6 class="mb-0">Building & Assessment Summary</h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <!-- General Description Summary -->
                    <div class="col-12">
                      <h6>General Description</h6>
                      <div class="row">
                        <div class="col-md-4">
                          <p><strong>Kind of Building:</strong> {{ form.generalDescription.kindOfBldg }}</p>
                          <p><strong>Structural Type:</strong> {{ form.generalDescription.structuralType }}</p>
                          <p><strong>Building Permit:</strong> {{ form.generalDescription.bldgPermit }}</p>
                          <p><strong>Building Age:</strong> {{ form.generalDescription.bldgAge }} years</p>
                        </div>
                        <div class="col-md-4">
                          <p><strong>No. of Storeys:</strong> {{ form.generalDescription.noOfStoreys }}</p>
                          <p><strong>Total Floor Area:</strong> {{ form.generalDescription.totalFloorArea }} sq.m.</p>
                          <p><strong>Date Constructed:</strong> {{ form.generalDescription.dateConstructed }}</p>
                          <p><strong>Date Occupied:</strong> {{ form.generalDescription.dateOccupied }}</p>
                        </div>
                      </div>
                    </div>

                    <!-- Property Assessment Summary -->
                    <div class="col-12 mt-3">
                      <h6>Property Assessment</h6>
                      <div class="row">
                        <div class="col-md-6">
                          <p><strong>Status:</strong> {{ form.propertyAssessment.taxable ? 'Taxable' : 'Non-Taxable' }}</p>
                          <p><strong>Effectivity:</strong> {{ form.propertyAssessment.effectivity }}</p>
                        </div>
                        <div class="col-md-6">
                          <p><strong>Total Assessment Values:</strong> {{ form.propertyAssessment.values.length }}</p>
                          <p><strong>Property Appraisal Total:</strong> {{ form.propertyAppraisal.subTotal }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Memoranda -->
              <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                  <h6 class="text-dark mb-0">
                    <i class="fas fa-sticky-note me-2"></i>
                    Memoranda
                  </h6>
                </div>
                <div class="card-body">
                  <label class="form-label text-dark fw-bold">Additional Notes or Memoranda</label>
                  <textarea v-model="form.memorada" 
                            class="form-control border-2" 
                            rows="4" 
                            placeholder="Enter any additional notes, memoranda, or special instructions"></textarea>
                </div>
              </div>
            </div>
          </div>
            
            <!-- Form Navigation -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
              <button class="btn btn-lg btn-outline-secondary" 
                      @click="prevStep" 
                      :disabled="step===1">
                <i class="fas fa-arrow-left me-2"></i>
                Previous
              </button>
              <div class="d-flex gap-2">
                <button class="btn btn-lg btn-outline-danger" @click="cancelForm">
                  <i class="fas fa-times me-2"></i>
                  Cancel
                </button>
                <button class="btn btn-lg btn-primary" @click="nextStep">
                  <i class="fas fa-arrow-right me-2"></i>
                  {{ buttonText(step) }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Data Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-light">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="text-dark mb-0">
                  <i class="fas fa-table me-2"></i>
                  Assessment Requests List
                </h6>
                <small class="text-muted">{{ requests.length }} total requests</small>
              </div>
              <div class="col-auto">
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="fas fa-search"></i>
                  </span>
                  <input type="text" 
                         class="form-control" 
                         placeholder="Search requests..."
                         v-model="searchTerm">
                </div>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th class="text-dark fw-bold border-0 ps-4">
                      <i class="fas fa-hashtag me-1"></i>
                      ARP No
                    </th>
                    <th class="text-dark fw-bold border-0">
                      <i class="fas fa-map-pin me-1"></i>
                      PIN
                    </th>
                    <th class="text-dark fw-bold border-0">
                      <i class="fas fa-user me-1"></i>
                      Owner
                    </th>
                    <th class="text-dark fw-bold border-0">
                      <i class="fas fa-map-marker-alt me-1"></i>
                      Municipality
                    </th>
                    <th class="text-dark fw-bold border-0">
                      <i class="fas fa-flag me-1"></i>
                      Status
                    </th>
                    <th class="text-dark fw-bold border-0 pe-4">
                      <i class="fas fa-cogs me-1"></i>
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="r in filteredRequests" :key="r.id" class="border-bottom">
                    <td class="ps-4">
                      <strong class="text-primary">{{ r.arpNo }}</strong>
                    </td>
                    <td>
                      <span class="text-dark">{{ r.pin }}</span>
                    </td>
                    <td>
                      <div>
                        <strong class="text-dark">{{ r.ownerName }}</strong>
                        <br>
                        <small class="text-muted">{{ r.contactNo }}</small>
                      </div>
                    </td>
                    <td>
                      <span class="text-dark">{{ r.municipality }}</span>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(r.requestStatus)" 
                            class="badge">
                        {{ tryParseRequestStatus(r.requestStatus) }}
                      </span>
                    </td>
                    <td class="pe-4">
                      <div class="btn-group" role="group">
                        <!-- Status-specific buttons -->
                        <button v-if="r.requestStatus === 'Pending'" 
                                class="btn btn-sm btn-warning" 
                                @click="updateRequestStatus(r.id, 'Under Review', 'Moved to review phase')"
                                title="Start Review">
                          <i class="fas fa-search"></i>
                          Review
                        </button>
                        
                        <button v-if="r.requestStatus === 'Under Review'" 
                                class="btn btn-sm btn-success" 
                                @click="updateRequestStatus(r.id, 'Approved', 'Request approved after review')"
                                title="Approve Request">
                          <i class="fas fa-check"></i>
                          Approve
                        </button>
                        
                        <button v-if="r.requestStatus === 'Under Review'" 
                                class="btn btn-sm btn-danger" 
                                @click="updateRequestStatus(r.id, 'Rejected', 'Request rejected after review')"
                                title="Reject Request">
                          <i class="fas fa-times"></i>
                          Reject
                        </button>
                        
                        <!-- Certificate generation for approved requests -->
                        <div v-if="r.requestStatus === 'Approved'" class="btn-group">
                          <button class="btn btn-sm btn-info dropdown-toggle" 
                                  data-bs-toggle="dropdown" 
                                  title="Generate Certificates">
                            <i class="fas fa-certificate"></i>
                            Certificates
                          </button>
                          <ul class="dropdown-menu">
                            <li>
                              <a class="dropdown-item" href="#" @click="generateCertificate(r.id, 'ownership')">
                                <i class="fas fa-home me-2"></i>
                                Property Ownership
                              </a>
                            </li>
                            <li>
                              <a class="dropdown-item" href="#" @click="generateCertificate(r.id, 'tax_declaration')">
                                <i class="fas fa-file-invoice-dollar me-2"></i>
                                Tax Declaration
                              </a>
                            </li>
                          </ul>
                        </div>
                        
                        <!-- Standard actions (always available) -->
                        <button class="btn btn-sm btn-outline-primary" 
                                @click="editRequest(r)"
                                title="Edit Request">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info" 
                                @click="viewRequest(r)"
                                title="View Details">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" 
                                @click="printRequest(r)"
                                title="Print">
                          <i class="fas fa-print"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredRequests.length === 0">
                    <td colspan="6" class="text-center py-5">
                      <div class="text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <h6>No requests found</h6>
                        <p class="mb-0">{{ searchTerm ? 'Try adjusting your search terms' : 'Start by adding a new assessment request' }}</p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2';
import AuthService from '@/services/auth.js';

export default {
  name: 'Requests',
  data() {
    return {
      requests: [],
      assessmentLevels: [],
      categories: [],
      showForm: false,
      step: 1,
      editing: false,
      searchTerm: '',
      form: {
        arpNo: '',
        pin: '',
        categoryId: '',
        ownerName: '',
        marriedTo: '',
        ownerAddress: '',
        contactNo: '',
        tin: '',
        adminName: '',
        adminContactNo: '',
        adminMarriedTo: '',
        adminAddress: '',
        adminTin: '',
        street: '',
        barangay: '',
        municipality: '',
        province: '',
        octTctCloaNo: '',
        surveyNo: '',
        lotNo: '',
        areaNo: '',
        memorada: '',
        generalDescription: {
          kindOfBldg: '',
          structuralType: '',
          bldgPermit: '',
          dateIssued: '',
          cct: '',
          certificateCompletionIssuedOn: '',
          certificateOccupancyIssuedOn: '',
          dateConstructed: '',
          dateOccupied: '',
          bldgAge: 0,
          noOfStoreys: 0,
          area1st: 0,
          area2nd: 0,
          area3rd: 0,
          area4th: 0,
          totalFloorArea: 0
        },
        structuralChecklist: {
          Roof: [],
          Flooring: [],
          WallsPartions: []
        },
        additionalItems: [],
        propertyAppraisal: {
          unit: '',
          unitCost: 0,
          computation: '',
          subTotal: 0
        },
        propertyBounderies: {
          north: '',
          east: '',
          west: '',
          south: ''
        },
        landAppraisal: [],
        otherImprovements: [],
        propertyAssessment: {
          taxable: false,
          exempt: false,
          effectivity: '',
          values: []
        }
      },
      requestStatusText: '',
    };
  },
  computed: {
    filteredRequests() {
      if (!this.searchTerm) return this.requests;
      const term = this.searchTerm.toLowerCase();
      return this.requests.filter(request => 
        request.arpNo?.toLowerCase().includes(term) ||
        request.pin?.toLowerCase().includes(term) ||
        request.ownerName?.toLowerCase().includes(term) ||
        request.municipality?.toLowerCase().includes(term)
      );
    }
  },
  created() {
    this.checkAuthentication();
    this.fetch();
    this.fetchAssessmentLevels();
    this.fetchCategories();
  },
  mounted() {
    // Double check authentication on component mount
    this.checkAuthentication();
  },
  methods: {
    checkAuthentication() {
      // Use AuthService for authentication check
      if (!AuthService.isAuthenticated()) {
        // No token found or token expired, redirect to login
        Swal.fire({
          title: 'Authentication Required',
          text: 'Please log in to access the Assessment Requests page.',
          icon: 'warning',
          confirmButtonText: 'Go to Login',
          allowOutsideClick: false,
          allowEscapeKey: false
        }).then(() => {
          this.$router.push('/sign-in');
        });
        return;
      }
      
      console.log('User authenticated successfully');
    },
    
    handleAuthenticationError() {
      // Clear invalid tokens using AuthService
      AuthService.clearAuth();
      
      Swal.fire({
        title: 'Session Expired',
        text: 'Your session has expired. Please log in again.',
        icon: 'error',
        confirmButtonText: 'Go to Login',
        allowOutsideClick: false,
        allowEscapeKey: false
      }).then(() => {
        this.$router.push('/sign-in');
      });
    },

    logout() {
      Swal.fire({
        title: 'Confirm Logout',
        text: 'Are you sure you want to log out?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Logout',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // Clear all authentication data using AuthService
          AuthService.logout();
          
          Swal.fire({
            title: 'Logged Out',
            text: 'You have been logged out successfully.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            this.$router.push('/sign-in');
          });
        }
      });
    },

    async fetch() {
      try {
        // Add authentication header to API requests
        const headers = AuthService.getAuthHeader();
        const res = await this.$api.get('/e_assessment/api/v1/misc/assessment-requests', { headers });
        this.requests = res.data.data || [];
      } catch (e) {
        console.error(e);
        // Check if error is due to authentication
        if (e.response && (e.response.status === 401 || e.response.status === 403)) {
          this.handleAuthenticationError();
        }
      }
    },
    async fetchAssessmentLevels() {
      try {
        const headers = AuthService.getAuthHeader();
        const res = await this.$api.get('/e_assessment/api/v1/misc/assessment-levels', { headers });
        this.assessmentLevels = res.data.data || [];
      } catch (e) { 
        console.error(e);
        if (e.response && (e.response.status === 401 || e.response.status === 403)) {
          this.handleAuthenticationError();
        }
      }
    },
    async fetchCategories() {
      try {
        const headers = AuthService.getAuthHeader();
        const res = await this.$api.get('/e_assessment/api/v1/misc/categories', { headers });
        // Filter only assessment type categories
        this.categories = (res.data.data || []).filter(cat => cat.type.toLowerCase() === 'assessment');
      } catch (e) { 
        console.error(e);
        if (e.response && (e.response.status === 401 || e.response.status === 403)) {
          this.handleAuthenticationError();
        }
      }
    },
    startNew() {
      this.resetForm();
      this.showForm = true;
      this.step = 1;
      this.editing = false;
    },
    resetForm() {
      this.form = {
        arpNo: '',
        pin: '',
        categoryId: '',
        ownerName: '',
        marriedTo: '',
        ownerAddress: '',
        contactNo: '',
        tin: '',
        adminName: '',
        adminContactNo: '',
        street: '',
        barangay: '',
        municipality: '',
        province: '',
        memorada: '',
        generalDescription: {
          kindOfBldg: '',
          structuralType: '',
          bldgPermit: '',
          dateIssued: '',
          cct: '',
          certificateCompletionIssuedOn: '',
          certificateOccupancyIssuedOn: '',
          dateConstructed: '',
          dateOccupied: '',
          bldgAge: 0,
          noOfStoreys: 0,
          area1st: 0,
          area2nd: 0,
          area3rd: 0,
          area4th: 0,
          totalFloorArea: 0
        },
        structuralChecklist: {
          Roof: [],
          Flooring: [],
          WallsPartions: []
        },
        additionalItems: [],
        propertyAppraisal: {
          unit: '',
          unitCost: 0,
          computation: '',
          subTotal: 0
        },
        propertyBounderies: {
          north: '',
          east: '',
          west: '',
          south: ''
        },
        landAppraisal: [],
        otherImprovements: [],
        propertyAssessment: {
          taxable: false,
          exempt: false,
          effectivity: '',
          values: []
        }
      };
      this.requestStatusText = '';
    },
    addAssessmentValue() {
      this.form.propertyAssessment.values.push({
        actualUse: '',
        marketValue: 0,
        assessmentLevel: 0,
        assessedValue: 0
      });
    },
    removeAssessmentValue(index) {
      this.form.propertyAssessment.values.splice(index, 1);
    },
    // Convert textarea input to array for structural checklist
    handleStructuralChecklistInput(field, value) {
      this.form.structuralChecklist[field] = value.split('\n').filter(item => item.trim());
    },
    // Calculate sub total when unit cost changes
    calculateSubTotal() {
      const { unit, unitCost } = this.form.propertyAppraisal;
      if (unit && unitCost) {
        this.form.propertyAppraisal.subTotal = parseFloat(unitCost);
      }
    },
    calculateTotalFloorArea() {
      const { area1st, area2nd, area3rd, area4th } = this.form.generalDescription;
      this.form.generalDescription.totalFloorArea = [area1st, area2nd, area3rd, area4th]
        .map(area => parseFloat(area) || 0)
        .reduce((sum, area) => sum + area, 0);
    },
    buttonText(step) {
      if (step < 3) return 'Next';
      return this.editing ? 'Update' : 'Submit';
    },
    prevStep() { if (this.step > 1) this.step--; },
    async nextStep() {
      if (this.step < 3) { this.step++; return; }

      // submit
      try {
        const payload = { ...this.form };
        const headers = AuthService.getAuthHeader();
        
        // Ensure arrays are properly formatted
        payload.structuralChecklist = {
          Roof: Array.isArray(payload.structuralChecklist.Roof) ? payload.structuralChecklist.Roof : payload.structuralChecklist.Roof.split('\n').filter(item => item.trim()),
          Flooring: Array.isArray(payload.structuralChecklist.Flooring) ? payload.structuralChecklist.Flooring : payload.structuralChecklist.Flooring.split('\n').filter(item => item.trim()),
          WallsPartions: Array.isArray(payload.structuralChecklist.WallsPartions) ? payload.structuralChecklist.WallsPartions : payload.structuralChecklist.WallsPartions.split('\n').filter(item => item.trim())
        };
        payload.additionalItems = Array.isArray(payload.additionalItems) ? payload.additionalItems : payload.additionalItems.split('\n').filter(item => item.trim());
        payload.requestStatus = this.safeParse(this.requestStatusText);

        if (this.editing) {
          await this.$api.post('/e_assessment/api/v1/misc/assessment-requests/update', payload, { headers });
          Swal.fire('Updated', 'Request updated', 'success');
        } else {
          await this.$api.post('/e_assessment/api/v1/misc/assessment-requests/create', payload, { headers });
          Swal.fire('Created', 'Request created', 'success');
        }
        this.showForm = false;
        this.fetch();
      } catch (e) {
        console.error(e);
        if (e.response && (e.response.status === 401 || e.response.status === 403)) {
          this.handleAuthenticationError();
        } else {
          Swal.fire('Error', 'Save failed', 'error');
        }
      }
    },
    cancelForm() { this.showForm = false; },
    editRequest(r) {
      this.editing = true;
      this.showForm = true;
      this.step = 1;
      // populate
      this.form = { ...r };
      // parse JSON fields if present
      this.generalDescriptionText = this.safeStringify(r.generalDescription);
      this.structuralChecklistText = this.safeStringify(r.structuralChecklist);
      this.propertyAppraisalText = this.safeStringify(r.propertyAppraisal);
      this.requestStatusText = this.safeStringify(r.requestStatus);
    },
    safeParse(text) {
      if (!text) return null;
      try { return JSON.parse(text); } catch (e) {
        // if user entered JSON-like in textarea as string, attempt to parse
        try { return JSON.parse(text.replaceAll("'", '"')); } catch (_) { return text; }
      }
    },
    safeStringify(val) {
      if (!val) return '';
      try { return typeof val === 'string' ? val : JSON.stringify(JSON.parse(val), null, 2); } catch (e) {
        try { return JSON.stringify(val, null, 2); } catch (_) { return String(val); }
      }
    },
    tryParseRequestStatus(txt) {
      if (!txt) return '';
      try { const parsed = JSON.parse(txt); return typeof parsed === 'string' ? parsed : JSON.stringify(parsed); } catch (e) { return txt; }
    },
    getStatusBadgeClass(status) {
      const statusText = this.tryParseRequestStatus(status).toLowerCase();
      const statusClasses = {
        'pending': 'bg-warning text-dark',
        'under review': 'bg-info text-white',
        'approved': 'bg-success text-white',
        'rejected': 'bg-danger text-white',
        'completed': 'bg-primary text-white'
      };
      return statusClasses[statusText] || 'bg-secondary text-white';
    },
    viewRequest(request) {
      // Open view modal or navigate to detail page
      Swal.fire({
        title: 'Request Details',
        html: `
          <div class="text-start">
            <p><strong>ARP No:</strong> ${request.arpNo}</p>
            <p><strong>Owner:</strong> ${request.ownerName}</p>
            <p><strong>Municipality:</strong> ${request.municipality}</p>
            <p><strong>Status:</strong> ${this.tryParseRequestStatus(request.requestStatus)}</p>
          </div>
        `,
        showCloseButton: true,
        showConfirmButton: false,
        width: '600px'
      });
    },
    printRequest(request) {
      // Print functionality
      Swal.fire({
        title: 'Print Request',
        text: `Print assessment request for ${request.ownerName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Print',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // Implement print logic here
          window.print();
        }
      });
    },

    // Status update method
    async updateRequestStatus(requestId, newStatus, remarks = '') {
      try {
        // Show confirmation dialog
        const result = await Swal.fire({
          title: `Confirm Status Update`,
          text: `Are you sure you want to change the status to "${newStatus}"?`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: `Yes, Update to ${newStatus}`,
          cancelButtonText: 'Cancel',
          input: 'textarea',
          inputPlaceholder: 'Add remarks (optional)',
          inputValue: remarks
        });

        if (result.isConfirmed) {
          const headers = AuthService.getAuthHeader();
          const response = await this.$api.post('/e_assessment/api/v1/transactions/update-status', {
            id: requestId,
            status: newStatus,
            remarks: result.value || remarks
          }, { headers });

          if (response.data.success) {
            // Update the local data
            const requestIndex = this.requests.findIndex(r => r.id === requestId);
            if (requestIndex !== -1) {
              this.requests[requestIndex].requestStatus = newStatus;
            }

            Swal.fire({
              title: 'Status Updated!',
              text: `Request has been ${newStatus.toLowerCase()} successfully.`,
              icon: 'success',
              timer: 2000,
              showConfirmButton: false
            });

            // Refresh the data
            this.loadRequests();
          } else {
            throw new Error(response.data.message || 'Failed to update status');
          }
        }
      } catch (error) {
        console.error('Error updating status:', error);
        
        let errorMessage = 'Failed to update request status. Please try again.';
        if (error.response?.status === 401) {
          errorMessage = 'Authentication failed. Please log in again.';
          this.handleAuthenticationError();
          return;
        } else if (error.response?.data?.message) {
          errorMessage = error.response.data.message;
        }

        Swal.fire({
          title: 'Error',
          text: errorMessage,
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    },

    // Certificate generation method
    async generateCertificate(requestId, type) {
      try {
        const headers = AuthService.getAuthHeader();
        const endpoint = type === 'ownership' 
          ? `/e_assessment/api/v1/certificates/ownership/${requestId}`
          : `/e_assessment/api/v1/certificates/tax-declaration/${requestId}`;
        
        // Show loading
        Swal.fire({
          title: 'Generating Certificate...',
          text: 'Please wait while we generate your certificate.',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        const response = await this.$api.get(endpoint, { 
          headers,
          responseType: 'blob' // Important for PDF downloads
        });
        
        // Create blob URL and trigger download
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        
        // Create temporary download link
        const link = document.createElement('a');
        link.href = url;
        const certificateType = type === 'ownership' ? 'Property_Ownership' : 'Tax_Declaration';
        link.download = `${certificateType}_Certificate_${requestId}_${new Date().toISOString().split('T')[0]}.pdf`;
        document.body.appendChild(link);
        link.click();
        
        // Cleanup
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        Swal.fire({
          title: 'Certificate Generated!',
          text: `${type === 'ownership' ? 'Property Ownership' : 'Tax Declaration'} certificate has been downloaded successfully.`,
          icon: 'success',
          timer: 3000,
          showConfirmButton: false
        });
        
      } catch (error) {
        console.error('Error generating certificate:', error);
        
        let errorMessage = 'Failed to generate certificate. Please try again.';
        if (error.response?.status === 404) {
          errorMessage = 'Assessment request not found.';
        } else if (error.response?.status === 400) {
          errorMessage = 'Certificate can only be generated for approved requests.';
        } else if (error.response?.status === 401) {
          errorMessage = 'Authentication failed. Please log in again.';
          this.handleAuthenticationError();
          return;
        }
        
        Swal.fire({
          title: 'Error',
          text: errorMessage,
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    }
  }
}
</script>

<style scoped>
/* Enhanced styling for better text visibility and modern look */
.step-content {
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form-label {
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
  color: #2d3748 !important;
  font-weight: 600 !important;
}

.form-control, .form-select {
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-size: 0.95rem;
  color: #2d3748 !important;
  background-color: #ffffff;
  transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
  color: #2d3748 !important;
}

.form-control::placeholder {
  color: #a0aec0 !important;
}

.border-2 {
  border-width: 2px !important;
}

.card {
  border-radius: 12px;
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
}

.btn {
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-1px);
}

.table th {
  font-weight: 700;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  color: #2d3748 !important;
  border-bottom: 2px solid #e2e8f0;
  padding: 1rem 0.75rem;
}

.table td {
  padding: 1rem 0.75rem;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
  color: #2d3748 !important;
}

.table-hover tbody tr:hover {
  background-color: #f8fafc;
}

.badge {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
}

.text-dark {
  color: #2d3748 !important;
}

.text-muted {
  color: #718096 !important;
}

.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.text-white-75 {
  color: rgba(255, 255, 255, 0.75) !important;
}

/* Ensure all text is visible */
* {
  color: inherit;
}

h1, h2, h3, h4, h5, h6 {
  color: #2d3748 !important;
}

p, span, div {
  color: #4a5568 !important;
}

.card-header h6 {
  color: #2d3748 !important;
}

textarea.form-control { 
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
}
</style>
