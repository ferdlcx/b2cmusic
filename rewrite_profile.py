import re

with open('resources/views/auth/profile.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update x-data
xdata_pattern = r"""\s*provinces: \[\],\s*cities: \[\],\s*rajaongkirFailed: false,\s*addMap: null,\s*addMarker: null,\s*editMap: null,\s*editMarker: null,\s*editAddressData: {[\s\S]*?async init\(\) {[\s\S]*?this\.editAddressData\.city = cityName;\n\s*},\s*"""
# wait, it might be safer to replace the whole x-data block

old_xdata_str = r"""        provinces: [],
        cities: [],
        rajaongkirFailed: false,"""

new_xdata_str = r"""        areaSearchQuery: '',
        areaSearchResults: [],
        isSearchingArea: false,
        addMap: null,
        addMarker: null,
        editMap: null,
        editMarker: null,
        editAddressData: {
            id: '',
            label: '',
            name: '',
            phone: '',
            address: '',
            province_id: '',
            province: '',
            city_id: '',
            city: '',
            district: '',
            village: '',
            postal_code: '',
            latitude: '',
            longitude: '',
            is_default: false
        },
        async searchArea() {
            if (this.areaSearchQuery.length < 3) {
                this.areaSearchResults = [];
                return;
            }
            this.isSearchingArea = true;
            try {
                let res = await fetch('/api/rajaongkir/search-area?q=' + encodeURIComponent(this.areaSearchQuery));
                if (res.ok) {
                    this.areaSearchResults = await res.json();
                }
            } catch(e) {}
            this.isSearchingArea = false;
        },
        selectArea(area, type) {
            if (type === 'add') {
                this.areaSearchQuery = area.text;
                document.getElementById('add-city-id').value = area.id;
                document.getElementById('add-city').value = area.city;
                document.getElementById('add-province').value = area.province;
                document.getElementById('add-postal').value = area.postal_code;
            } else {
                this.areaSearchQuery = area.text;
                this.editAddressData.city_id = area.id;
                this.editAddressData.city = area.city;
                this.editAddressData.province = area.province;
                this.editAddressData.postal_code = area.postal_code;
            }
            this.areaSearchResults = [];
        },"""

# I need to do a more controlled replace. I'll just rewrite the whole file using a python script.
