/**
 * Aurora Province Address Service
 * Specifically designed for Aurora Province e-Assessment System
 * Uses static data for Aurora municipalities and barangays
 */
class AuroraAddressService {
  constructor() {
    this.AURORA_PROVINCE = 'Aurora';
    this.REGION_III = 'Region III';
    
    // Aurora municipalities (official list)
    this.AURORA_MUNICIPALITIES = [
      'Baler',        // Capital
      'Casiguran',    
      'Dilasag',      
      'Dinalungan',   
      'Dingalan',     
      'Dipaculao',    
      'Maria Aurora', 
      'San Luis'      
    ];
    
    // Aurora barangays by municipality (comprehensive list)
    this.AURORA_BARANGAYS = {
      'Baler': [
        'Poblacion', 'Buhangin', 'Calabuanan', 'Pingit', 'Reserva', 
        'Sabang', 'Suklayin', 'Zabali'
      ],
      'Casiguran': [
        'Poblacion', 'Biangonan', 'Cozo', 'Culat', 'Dibet', 'Esperanza', 
        'Lual', 'Marikit', 'Tinib'
      ],
      'Dilasag': [
        'Poblacion', 'Dicabasan', 'Dilaguidi', 'Dimaseset', 'Lawang', 
        'Maligaya', 'Remedios'
      ],
      'Dinalungan': [
        'Poblacion', 'Abuleg', 'Dibaraybay', 'Ditawini', 'Mapalad', 
        'Nipoo', 'Paleg', 'Simbahan'
      ],
      'Dingalan': [
        'Poblacion', 'Aplaya', 'Butas Na Bato', 'Cabog', 'Davildavilan', 
        'Dikapanikian', 'Ibona', 'Paltic', 'Tanawan', 'Umiray', 'White Beach'
      ],
      'Dipaculao': [
        'Poblacion', 'Borlongan', 'Dimapnat', 'Gupa', 'Ipil', 
        'Laboy', 'Lobbot', 'Mamali', 'North Poblacion', 'Toytoyan'
      ],
      'Maria Aurora': [
        'Poblacion', 'Bagtu', 'Bangtan', 'Bazal', 'Capassan', 'Decoliat', 
        'Diaat', 'Dialambot', 'Dimanayat', 'Diteki', 'Malasin', 
        'Ramot', 'San Juan', 'Suklayin', 'Villa Aurora', 'Wenceslao'
      ],
      'San Luis': [
        'Poblacion', 'Dibalo', 'L. Pimentel', 'Nonong Senior', 'Real', 
        'San Isidro', 'Zarah'
      ]
    };
  }

  /**
   * Get Aurora Province only (locked selection)
   * @returns {Object} Aurora province object
   */
  getAuroraProvince() {
    return {
      name: this.AURORA_PROVINCE,
      region: this.REGION_III,
      code: 'PH-AUR',
      locked: true // Indicates this is the only option
    };
  }

  /**
   * Get all provinces (Aurora only for this system)
   * @returns {Array} Array containing only Aurora province
   */
  getProvinces() {
    return [this.getAuroraProvince()];
  }

  /**
   * Get Aurora municipalities
   * @returns {Array} Array of Aurora municipalities
   */
  getAuroraMunicipalities() {
    return this.AURORA_MUNICIPALITIES.map(name => ({
      name,
      province: this.AURORA_PROVINCE,
      type: 'municipality',
      code: `AUR-${name.toUpperCase().replace(/\s+/g, '_')}`
    }));
  }

  /**
   * Get cities/municipalities (Aurora municipalities only)
   * @param {string} provinceName - Should always be 'Aurora'
   * @returns {Array} Array of Aurora municipalities
   */
  getCitiesByProvince(provinceName) {
    if (!provinceName || provinceName.toLowerCase() !== 'aurora') {
      return [];
    }
    return this.getAuroraMunicipalities();
  }

  /**
   * Get barangays by municipality in Aurora
   * @param {string} municipalityName - Name of the Aurora municipality
   * @returns {Array} Array of barangays in the specified municipality
   */
  getBarangaysByCity(municipalityName) {
    if (!this.isValidAuroraMunicipality(municipalityName)) {
      console.warn(`${municipalityName} is not a valid Aurora municipality`);
      return [];
    }

    const barangayNames = this.AURORA_BARANGAYS[municipalityName] || ['Poblacion'];
    
    return barangayNames.map(name => ({
      name,
      city: municipalityName,
      province: this.AURORA_PROVINCE,
      code: `${municipalityName.toUpperCase().replace(/\s+/g, '_')}-${name.toUpperCase().replace(/\s+/g, '_')}`,
      type: 'barangay'
    }));
  }

  /**
   * Validate if a municipality belongs to Aurora
   * @param {string} municipalityName - Municipality name to validate
   * @returns {boolean} True if valid Aurora municipality
   */
  isValidAuroraMunicipality(municipalityName) {
    if (!municipalityName) return false;
    return this.AURORA_MUNICIPALITIES.some(municipality => 
      municipality.toLowerCase() === municipalityName.toLowerCase()
    );
  }

  /**
   * Get complete Aurora address hierarchy
   * @param {string} barangayName - Name of the barangay
   * @param {string} municipalityName - Name of the municipality (must be Aurora municipality)
   * @returns {Object} Complete Aurora address information
   */
  getCompleteAuroraAddress(barangayName, municipalityName) {
    // Validate municipality is in Aurora
    if (!this.isValidAuroraMunicipality(municipalityName)) {
      throw new Error(`${municipalityName} is not a valid Aurora municipality`);
    }

    return {
      region: { name: this.REGION_III, code: '03' },
      province: this.getAuroraProvince(),
      municipality: { 
        name: municipalityName, 
        province: this.AURORA_PROVINCE,
        type: 'municipality'
      },
      barangay: { 
        name: barangayName, 
        city: municipalityName,
        province: this.AURORA_PROVINCE,
        type: 'barangay'
      },
      formatted: `${barangayName}, ${municipalityName}, Aurora`,
      fullFormatted: `${barangayName}, ${municipalityName}, Aurora, Region III (Central Luzon)`
    };
  }

  /**
   * Search within Aurora locations only
   * @param {string} searchTerm - Search term
   * @param {string} type - Type ('municipality', 'barangay', 'all')
   * @returns {Array} Array of matching Aurora locations
   */
  searchAuroraLocations(searchTerm, type = 'all') {
    const results = [];
    const term = searchTerm.toLowerCase();

    if (type === 'all' || type === 'municipality') {
      const municipalities = this.getAuroraMunicipalities().filter(municipality =>
        municipality.name.toLowerCase().includes(term)
      );
      results.push(...municipalities);
    }

    if (type === 'all' || type === 'barangay') {
      // Search barangays across all Aurora municipalities
      this.AURORA_MUNICIPALITIES.forEach(municipality => {
        const barangays = this.getBarangaysByCity(municipality).filter(barangay =>
          barangay.name.toLowerCase().includes(term)
        );
        results.push(...barangays);
      });
    }

    return results;
  }

  /**
   * Validate Aurora-specific address
   * @param {Object} address - Address object to validate
   * @returns {Object} Validation result
   */
  validateAuroraAddress(address) {
    const errors = [];
    const warnings = [];

    // Province validation
    if (!address.province) {
      errors.push('Province is required');
    } else if (address.province.toLowerCase() !== 'aurora') {
      errors.push('Province must be Aurora');
    }

    // Municipality validation
    if (!address.municipality) {
      errors.push('Municipality is required');
    } else if (!this.isValidAuroraMunicipality(address.municipality)) {
      errors.push(`Municipality '${address.municipality}' is not a valid Aurora municipality`);
    }

    // Barangay validation
    if (!address.barangay) {
      warnings.push('Barangay is recommended for complete address');
    }

    // Check if barangay exists for the municipality
    if (address.municipality && address.barangay) {
      const barangays = this.getBarangaysByCity(address.municipality);
      const barangayExists = barangays.some(b => 
        b.name.toLowerCase() === address.barangay.toLowerCase()
      );
      
      if (barangays.length > 0 && !barangayExists) {
        warnings.push(`Barangay '${address.barangay}' not found in ${address.municipality}`);
      }
    }

    return {
      isValid: errors.length === 0,
      isComplete: errors.length === 0 && address.barangay,
      errors,
      warnings,
      score: this.calculateAddressScore(errors, warnings, address)
    };
  }

  /**
   * Calculate address completion score
   * @private
   */
  calculateAddressScore(errors, warnings, address) {
    if (errors.length > 0) return 0;
    
    let score = 70; // Base score for valid Aurora address
    
    if (address.municipality) score += 20;
    if (address.barangay) score += 10;
    
    // Deduct for warnings
    score -= warnings.length * 5;
    
    return Math.max(0, Math.min(100, score));
  }

  /**
   * Aurora municipalities with fallback data
   * @returns {Array} Predefined Aurora municipalities
   */
  getDefaultAuroraMunicipalities() {
    return this.getAuroraMunicipalities();
  }

  /**
   * Get Aurora region information
   * @returns {Object} Aurora region details
   */
  getAuroraRegion() {
    return {
      name: 'Region III (Central Luzon)',
      code: '03',
      fullName: 'Central Luzon',
      provinces: [this.AURORA_PROVINCE]
    };
  }

  /**
   * Format Aurora address for official documents
   * @param {Object} address - Address components
   * @returns {string} Formatted address string
   */
  formatOfficialAddress(address) {
    const parts = [];
    
    if (address.street) parts.push(address.street);
    if (address.barangay) parts.push(`Barangay ${address.barangay}`);
    if (address.municipality) parts.push(address.municipality);
    parts.push('Aurora');
    
    return parts.join(', ');
  }

  /**
   * Get municipality statistics
   * @param {string} municipalityName - Municipality name
   * @returns {Object} Municipality statistics
   */
  getMunicipalityStats(municipalityName) {
    if (!this.isValidAuroraMunicipality(municipalityName)) {
      return null;
    }

    const barangays = this.getBarangaysByCity(municipalityName);
    
    return {
      municipality: municipalityName,
      barangayCount: barangays.length,
      isCapital: municipalityName === 'Baler',
      province: this.AURORA_PROVINCE,
      region: this.REGION_III
    };
  }

  /**
   * Get all Aurora address data for export
   * @returns {Object} Complete Aurora address data
   */
  getAllAuroraData() {
    return {
      province: this.getAuroraProvince(),
      region: this.getAuroraRegion(),
      municipalities: this.getAuroraMunicipalities(),
      barangays: this.AURORA_BARANGAYS,
      statistics: {
        municipalityCount: this.AURORA_MUNICIPALITIES.length,
        totalBarangays: Object.values(this.AURORA_BARANGAYS).reduce((sum, brgy) => sum + brgy.length, 0)
      }
    };
  }

  // Legacy methods for backward compatibility
  validateAddress(address) {
    return this.validateAuroraAddress(address);
  }

  searchLocations(searchTerm, type) {
    return this.searchAuroraLocations(searchTerm, type);
  }

  getCompleteAddress(barangayName, cityName, provinceName) {
    if (provinceName && provinceName.toLowerCase() !== 'aurora') {
      throw new Error('This system only supports Aurora Province');
    }
    return this.getCompleteAuroraAddress(barangayName, cityName);
  }
}

// Export singleton instance
export default new AuroraAddressService();