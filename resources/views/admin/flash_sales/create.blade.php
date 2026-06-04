@extends('admin.layouts.admin')

@section('title', 'Tambah Flash Sale - Admin DjudasMS')

@section('admin_content')
<div class="space-y-6" x-data="flashSaleForm()">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Pemasaran</span>
            <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Tambah Flash Sale</h1>
        </div>
        <a href="{{ route('admin.flashSales') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-slate-200 bg-white rounded-xl text-xs font-semibold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2 text-slate-400"></i> Batal
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm">
        <form action="{{ route('admin.flashSales.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Name -->
            <div class="space-y-1.5">
                <label for="name" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Nama Campaign</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('name') border-rose-500 @enderror" 
                    placeholder="Contoh: Flash Sale Akhir Bulan Juni" />
                @error('name')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Start Time -->
                <div class="space-y-1.5">
                    <label for="start_time" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Waktu Mulai</label>
                    <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" required
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('start_time') border-rose-500 @enderror" />
                    @error('start_time')
                        <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- End Time -->
                <div class="space-y-1.5">
                    <label for="end_time" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Waktu Selesai</label>
                    <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" required
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('end_time') border-rose-500 @enderror" />
                    @error('end_time')
                        <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Status -->
                <div class="space-y-1.5">
                    <label for="status" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Status</label>
                    <select name="status" id="status" required
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <!-- Product Selection Section -->
            <div class="border-t border-slate-100 pt-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">Daftar Produk Promo</h3>
                    
                    <!-- Dropdown Add Product -->
                    <div class="w-64">
                        <select @change="addProduct($el)" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600">
                            <option value="">+ Tambah Produk Promo</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                                    {{ $product->name }} (Stok: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Error validation items -->
                @error('products')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror

                <!-- Items Table -->
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase tracking-wider text-slate-400 bg-slate-50 border-b border-slate-150">
                            <tr>
                                <th class="px-5 py-3">Produk</th>
                                <th class="px-5 py-3">Harga Normal</th>
                                <th class="px-5 py-3" style="width: 25%;">Harga Diskon</th>
                                <th class="px-5 py-3" style="width: 20%;">Stok Promo</th>
                                <th class="px-5 py-3 text-center" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="item.id">
                                <tr class="border-b border-slate-100 last:border-0">
                                    <td class="px-5 py-3.5">
                                        <input type="hidden" name="products[]" :value="item.id" />
                                        <span class="font-bold text-slate-800 text-xs block" x-text="item.name"></span>
                                        <span class="text-[0.6rem] text-slate-400 font-semibold">Total stok gudang: <span x-text="item.maxStock"></span></span>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-650 font-semibold">
                                        Rp <span x-text="formatNumber(item.price)"></span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="relative">
                                            <span class="absolute left-3 top-3 text-[0.7rem] text-slate-400 font-bold">Rp</span>
                                            <input type="number" name="discount_prices[]" required :max="item.price" min="0"
                                                class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white" />
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <input type="number" name="stocks[]" required :max="item.maxStock" min="1"
                                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white" />
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <button type="button" @click="removeItem(index)" class="text-rose-600 hover:text-rose-800 text-xs font-bold uppercase">Hapus</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="items.length === 0">
                                <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-400 font-semibold">Belum ada produk ditambahkan ke flash sale ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" 
                class="w-full py-4.5 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan Flash Sale
            </button>
        </form>
    </div>
</div>

<script>
    function flashSaleForm() {
        return {
            items: [],
            addProduct(el) {
                const select = el;
                const option = select.options[select.selectedIndex];
                if (!option.value) return;

                const productId = option.value;
                const productName = option.getAttribute('data-name');
                const productPrice = parseFloat(option.getAttribute('data-price'));
                const maxStock = parseInt(option.getAttribute('data-stock'));

                // Avoid duplicates
                if (this.items.some(item => item.id == productId)) {
                    alert('Produk sudah ditambahkan.');
                    select.value = '';
                    return;
                }

                this.items.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    maxStock: maxStock
                });

                select.value = '';
            },
            removeItem(index) {
                this.items.splice(index, 1);
            },
            formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        }
    }
</script>
@endsection
