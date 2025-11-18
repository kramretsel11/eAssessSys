// Test file for Philippine Address Service
import AddressService from './src/services/address.js';

console.log('Testing Philippine Address Service...');

// Test 1: Get provinces
console.log('\n1. Testing getProvinces():');
const provinces = AddressService.getProvinces();
console.log(`Found ${provinces.length} provinces`);
if (provinces.length > 0) {
  console.log('First 5 provinces:', provinces.slice(0, 5).map(p => p.name));
}

// Test 2: Get Aurora province specifically
console.log('\n2. Testing getAuroraProvince():');
const aurora = AddressService.getAuroraProvince();
console.log('Aurora province:', aurora);

// Test 3: Get Aurora municipalities
console.log('\n3. Testing getAuroraMunicipalities():');
const auroraMunicipalities = AddressService.getAuroraMunicipalities();
console.log('Aurora municipalities:', auroraMunicipalities.map(m => m.name));

// Test 4: Get default Aurora municipalities (fallback)
console.log('\n4. Testing getDefaultAuroraMunicipalities():');
const defaultMunicipalities = AddressService.getDefaultAuroraMunicipalities();
console.log('Default Aurora municipalities:', defaultMunicipalities.map(m => m.name));

// Test 5: Get barangays for Baler
console.log('\n5. Testing getBarangaysByCity("Baler"):');
const balerBarangays = AddressService.getBarangaysByCity('Baler');
console.log(`Found ${balerBarangays.length} barangays in Baler`);
if (balerBarangays.length > 0) {
  console.log('First 5 barangays:', balerBarangays.slice(0, 5).map(b => b.name));
}

// Test 6: Address validation
console.log('\n6. Testing address validation:');
const testAddress = {
  province: 'Aurora',
  municipality: 'Baler',
  barangay: 'Poblacion'
};
const validation = AddressService.validateAddress(testAddress);
console.log('Validation result:', validation);

// Test 7: Search locations
console.log('\n7. Testing location search for "Aurora":');
const searchResults = AddressService.searchLocations('Aurora', 'province');
console.log('Search results:', searchResults);

console.log('\nAddress Service testing completed!');