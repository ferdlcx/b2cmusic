import re

with open('resources/views/cart/checkout.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Replace x-data logic
new_xdata = """          x-data="{ 
              addressId: '{{ $defaultAddress ? $defaultAddress->id : '' }}',
              selectedCourier: '',
              selectedService: '',
              paymentMethod: 'va',
              packingOption: 'standard',
              packingCost: 0,
              shippingCost: 0,
              shippingLoading: false,
              courierOptions: [],
              totalWeight: {{ $cartItems->sum(function($item) { return ($item->product->weight ?: 1000) * $item->quantity; }) }},
              selectedCityId: '{{ $defaultAddress ? ($defaultAddress->city_id ?: "") : "" }}',
              couponCode: '',
              discount: 0,
              subtotal: {{ $subtotal }},
              
              // Emsifa & RajaOngkir States
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
                  this.fetchEmsifaProvinces();
              },
              async fetchEmsifaProvinces() {
                  try {
                      let res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                      if (res.ok) this.provinces = await res.json();
                  } catch(e) {}
              },
              async fetchEmsifaRegencies() {
                  this.regencies = [];
                  this.districts = [];
                  this.selectedRegency = '';
                  this.selectedDistrict = '';
                  this.postalCode = '';
                  this.selectedCityId = '';
                  if (!this.selectedProv) return;
                  
                  let provObj = this.provinces.find(p => p.id == this.selectedProv);
                  if (provObj) this.province = provObj.name;

                  try {
                      let res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/' + this.selectedProv + '.json');
                      if (res.ok) this.regencies = await res.json();
                  } catch(e) {}
              },
              async fetchEmsifaDistricts() {
                  this.districts = [];
                  this.selectedDistrict = '';
                  this.postalCode = '';
                  this.selectedCityId = '';
                  if (!this.selectedRegency) return;
                  
                  let regObj = this.regencies.find(r => r.id == this.selectedRegency);
                  if (regObj) this.city = regObj.name;

                  try {
                      let res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/districts/' + this.selectedRegency + '.json');
                      if (res.ok) this.districts = await res.json();
                  } catch(e) {}
              },
              async resolveRajaOngkir() {
                  this.postalCode = '';
                  this.selectedCityId = '';
                  if (!this.selectedDistrict) return;
                  
                  let regObj = this.regencies.find(r => r.id == this.selectedRegency);
                  if (!regObj) return;

                  try {
                      let res = await fetch('{{ route("api.rajaongkir.resolve") }}?city_name=' + encodeURIComponent(regObj.name));
                      if (res.ok) {
                          let data = await res.json();
                          this.postalCode = data.postal_code;
                          this.selectedCityId = data.city_id;
                          this.fetchCourierOptions();
                      } else {
                          alert('Area tidak didukung oleh RajaOngkir. Silakan pilih area lain.');
                      }
                  } catch(e) {}
              },
              async fetchCourierOptions() {
                  if (!this.selectedCityId) return;
                  this.shippingLoading = true;
                  this.courierOptions = [];
                  this.selectedCourier = '';
                  this.selectedService = '';
                  this.shippingCost = 0;
                  
                  try {
                      let res = await fetch('{{ route("api.rajaongkir.rates") }}', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-TOKEN': '{{ csrf_token() }}'
                          },
                          body: JSON.stringify({
                              destination_city_id: this.selectedCityId,
                              weight: this.totalWeight
                          })
                      });
                      if (res.ok) {
                          let data = await res.json();
                          if (data.costs) {
                              data.costs.forEach(c => {
                                  this.courierOptions.push({
                                      courier: c.service.split(' - ')[0] || 'COURIER',
                                      courier_name: c.service.split(' - ')[0] || 'COURIER',
                                      service: c.description,
                                      description: c.description,
                                      cost: c.cost,
                                      etd: c.etd
                                  });
                              });
                          }
                      }
                  } catch(e) { console.error(e); }
                  this.shippingLoading = false;
              },
              calculateTotal() {
                  return this.subtotal - this.discount + this.shippingCost + this.packingCost;
              }
          }"""

content = re.sub(r'          x-data="{[\s\S]*?calculateTotal\(\) {[\s\S]*?return this\.subtotal - this\.discount \+ this\.shippingCost \+ this\.packingCost;\n              }\n          }"', new_xdata, content)

# Replace new_address logic blocks (it's tricky because there are 2, but let's replace by finding the areaSearchQuery input block)
new_input_html = """                                          <div class="space-y-4">
                                              <input type="hidden" name="new_address_city" x-model="city" />
                                              <input type="hidden" name="new_address_province" x-model="province" />
                                              <input type="hidden" name="new_address_area_id" x-model="selectedCityId" />

                                              <select x-model="selectedProv" @change="fetchEmsifaRegencies()" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium cursor-pointer">
                                                  <option value="">Pilih Provinsi</option>
                                                  <template x-for="p in provinces" :key="p.id">
                                                      <option :value="p.id" x-text="p.name"></option>
                                                  </template>
                                              </select>

                                              <select x-model="selectedRegency" @change="fetchEmsifaDistricts()" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium cursor-pointer" x-show="regencies.length > 0">
                                                  <option value="">Pilih Kabupaten / Kota</option>
                                                  <template x-for="r in regencies" :key="r.id">
                                                      <option :value="r.id" x-text="r.name"></option>
                                                  </template>
                                              </select>

                                              <select x-model="selectedDistrict" @change="resolveRajaOngkir()" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium cursor-pointer" x-show="districts.length > 0">
                                                  <option value="">Pilih Kecamatan</option>
                                                  <template x-for="d in districts" :key="d.id">
                                                      <option :value="d.id" x-text="d.name"></option>
                                                  </template>
                                              </select>
                                          </div>"""

# Replace in block 1
content = re.sub(
    r'<div class="relative w-full">\s*<input type="hidden" name="new_address_area_id" x-model="selectedNewAreaId" />[\s\S]*?<div x-show="areaSearchResults\.length > 0"[\s\S]*?</div>\s*</div>\s*</div>',
    new_input_html,
    content
)

# Replace in block 2 (if it exists differently)
content = re.sub(
    r'<div class="relative w-full">\s*<input type="hidden" name="new_address_city" x-model="city" />[\s\S]*?<div x-show="areaSearchResults\.length > 0"[\s\S]*?</div>\s*</div>\s*</div>',
    new_input_html,
    content
)

# Replace routes in HTML
content = content.replace("api.biteship.rates", "api.rajaongkir.rates")

with open('resources/views/cart/checkout.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
