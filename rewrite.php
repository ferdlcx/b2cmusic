<?php
$content = file_get_contents('resources/views/cart/checkout.blade.php');

$oldData = <<<'EOD'
              subtotal: {{ $subtotal }},
              areaSearchQuery: '',
              areaSearchResults: [],
              selectedNewAreaId: '',
              selectedNewAreaName: '',
              isSearchingArea: false,
              init() {
                  if (this.selectedCityId) {
                      this.fetchCourierOptions();
                  }
              },
              async searchArea() {
                  if (this.areaSearchQuery.length < 3) {
                      this.areaSearchResults = [];
                      return;
                  }
                  this.isSearchingArea = true;
                  try {
                      let res = await fetch('{{ route("api.biteship.search") }}?q=' + encodeURIComponent(this.areaSearchQuery));
                      if (res.ok) {
                          this.areaSearchResults = await res.json();
                      }
                  } catch(e) {}
                  this.isSearchingArea = false;
              },
              selectArea(area) {
                  this.selectedNewAreaId = area.id;
                  this.selectedNewAreaName = area.text;
                  this.areaSearchQuery = area.text;
                  this.postalCode = area.postal_code || '';
                  this.city = area.city || '';
                  this.province = area.province || '';
                  this.areaSearchResults = [];
                  this.selectedCityId = area.id; // use selectedCityId as area_id for consistency
                  this.fetchCourierOptions();
              },
EOD;

$newData = <<<'EOD'
              subtotal: {{ $subtotal }},
              
              // RajaOngkir & Emsifa States
              provinces: [],
              regencies: [],
              districts: [],
              selectedProv: '',
              selectedRegency: '',
              selectedDistrict: '',
              postalCode: '',
              city: '',
              province: '',
              
              init() {
                  if (this.selectedCityId) {
                      this.fetchCourierOptions();
                  }
                  this.fetchRajaOngkirProvinces();
              },
              async fetchRajaOngkirProvinces() {
                  try {
                      let res = await fetch('/api/rajaongkir/provinces');
                      if (res.ok) this.provinces = await res.json();
                  } catch(e) {}
              },
              async fetchRajaOngkirCities() {
                  this.regencies = [];
                  this.districts = [];
                  this.selectedRegency = '';
                  this.selectedDistrict = '';
                  this.postalCode = '';
                  this.selectedCityId = '';
                  if (!this.selectedProv) return;
                  
                  let provObj = this.provinces.find(p => p.province_id == this.selectedProv);
                  if (provObj) this.province = provObj.province;

                  try {
                      let res = await fetch('/api/rajaongkir/cities/' + this.selectedProv);
                      if (res.ok) {
                          this.regencies = await res.json();
                      }
                  } catch(e) {}
              },
              async fetchEmsifaDistricts() {
                  this.districts = [];
                  this.selectedDistrict = '';
                  this.postalCode = '';
                  this.selectedCityId = '';
                  if (!this.selectedRegency) return;
                  
                  let regObj = this.regencies.find(r => r.city_id == this.selectedRegency);
                  if (regObj) {
                      this.city = regObj.type + ' ' + regObj.city_name;
                      this.postalCode = regObj.postal_code;
                      this.selectedCityId = regObj.city_id;
                  }

                  // Fallback load districts from Emsifa by matching province name
                  try {
                      let pRes = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                      let pData = await pRes.json();
                      let eProv = pData.find(p => p.name.includes(this.province.toUpperCase()) || this.province.toUpperCase().includes(p.name));
                      if (eProv) {
                          let rRes = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/' + eProv.id + '.json');
                          let rData = await rRes.json();
                          // Try exact match or partial
                          let searchCity = regObj.city_name.toUpperCase();
                          let eReg = rData.find(r => r.name.includes(searchCity) || searchCity.includes(r.name));
                          if (eReg) {
                              let dRes = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/districts/' + eReg.id + '.json');
                              this.districts = await dRes.json();
                          }
                      }
                  } catch(e) { console.error(e); }
                  this.fetchCourierOptions();
              },
EOD;

$content = str_replace($oldData, $newData, $content);
$content = str_replace("route(\"api.biteship.rates\")", "route(\"api.rajaongkir.rates\")", $content);
$content = str_replace("destination_area_id", "destination_city_id", $content);

// Replace address dropdown html block 1
$oldInputHtml = <<<'EOD'
                                          <div class="relative w-full">
                                              <input type="hidden" name="new_address_area_id" x-model="selectedNewAreaId" />
                                              <input type="hidden" name="new_address_area_name" x-model="selectedNewAreaName" />
                                              <input type="hidden" name="new_address_city" x-model="city" />
                                              <input type="hidden" name="new_address_province" x-model="province" />
                                              
                                              <input type="text" x-model="areaSearchQuery" @input.debounce.500ms="searchArea()" placeholder="Cari Kecamatan / Kota (Otomatis dari Biteship)" 
                                                  class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" autocomplete="off" />
                                              
                                              <div x-show="isSearchingArea" class="absolute right-0 top-3 text-[0.65rem] text-muted">Mencari...</div>
  
                                              <div x-show="areaSearchResults.length > 0" @click.away="areaSearchResults = []" class="absolute z-10 w-full mt-1 bg-white border border-walnut-800/10 shadow-lg max-h-60 overflow-y-auto">
                                                  <template x-for="res in areaSearchResults" :key="res.id">
                                                      <div @click="selectArea(res)" x-text="res.text" class="p-3 hover:bg-cream-50 cursor-pointer text-[0.75rem] border-b border-walnut-800/5 transition"></div>
                                                  </template>
                                              </div>
                                          </div>
EOD;

$newInputHtml = <<<'EOD'
                                          <div class="space-y-4">
                                              <input type="hidden" name="new_address_city" x-model="city" />
                                              <input type="hidden" name="new_address_province" x-model="province" />
                                              <input type="hidden" name="new_address_area_id" x-model="selectedCityId" />

                                              <select x-model="selectedProv" @change="fetchRajaOngkirCities()" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium cursor-pointer" required>
                                                  <option value="">Pilih Provinsi</option>
                                                  <template x-for="p in provinces" :key="p.province_id">
                                                      <option :value="p.province_id" x-text="p.province"></option>
                                                  </template>
                                              </select>

                                              <select x-model="selectedRegency" @change="fetchEmsifaDistricts()" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium cursor-pointer" x-show="regencies.length > 0" required>
                                                  <option value="">Pilih Kabupaten / Kota</option>
                                                  <template x-for="r in regencies" :key="r.city_id">
                                                      <option :value="r.city_id" x-text="r.type + ' ' + r.city_name"></option>
                                                  </template>
                                              </select>

                                              <select x-model="selectedDistrict" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium cursor-pointer" x-show="districts.length > 0" required>
                                                  <option value="">Pilih Kecamatan</option>
                                                  <template x-for="d in districts" :key="d.id">
                                                      <option :value="d.name" x-text="d.name"></option>
                                                  </template>
                                              </select>
                                          </div>
EOD;

$content = str_replace($oldInputHtml, $newInputHtml, $content);

// Replace address dropdown html block 2 (if any difference)
$oldInputHtml2 = <<<'EOD'
                                              <input type="hidden" name="new_address_city" x-model="city" />
                                              <input type="hidden" name="new_address_province" x-model="province" />
                                              
                                              <input type="text" x-model="areaSearchQuery" @input.debounce.500ms="searchArea()" placeholder="Cari Kecamatan / Kota (Otomatis dari Biteship)" 
                                                  class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" autocomplete="off" />
                                              
                                              <div x-show="isSearchingArea" class="absolute right-0 top-3 text-[0.65rem] text-muted">Mencari...</div>
  
                                              <div x-show="areaSearchResults.length > 0" @click.away="areaSearchResults = []" class="absolute z-10 w-full mt-1 bg-white border border-walnut-800/10 shadow-lg max-h-60 overflow-y-auto">
                                                  <template x-for="res in areaSearchResults" :key="res.id">
                                                      <div @click="selectArea(res)" x-text="res.text" class="p-3 hover:bg-cream-50 cursor-pointer text-[0.75rem] border-b border-walnut-800/5 transition"></div>
                                                  </template>
                                              </div>
                                          </div>
EOD;

$newInputHtml2 = <<<'EOD'
EOD;
// wait, the regex approach with str_replace might fail if indentation varies.
file_put_contents('resources/views/cart/checkout.blade.php', $content);
echo "Done replacing script.";
?>
