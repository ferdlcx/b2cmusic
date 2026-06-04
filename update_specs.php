<?php

use App\Models\Product;
use App\Models\ProductSpecification;

// Clear all existing specs to prevent duplicates
ProductSpecification::truncate();

$products = Product::with('category')->get();

foreach ($products as $product) {
    $catName = strtolower($product->category->name);
    
    $specs = [];
    
    // Base specs for all products
    $specs['Berat Bersih'] = number_format(rand(15, 60) / 10, 1) . ' kg';
    $specs['Dimensi (P x L x T)'] = rand(90, 120) . ' x ' . rand(30, 50) . ' x ' . rand(5, 20) . ' cm';
    $specs['Garansi Resmi'] = '1 Tahun DjudasMS';
    
    // Category specific specs
    if (strpos($catName, 'gitar') !== false || strpos($catName, 'bass') !== false || strpos($catName, 'ukulele') !== false) {
        $materials = ['Solid Spruce', 'Mahogany', 'Alder Wood', 'Rosewood', 'Maple Wood', 'Ash', 'Basswood'];
        $specs['Material Bodi'] = $materials[array_rand($materials)];
        $specs['Material Neck'] = $materials[array_rand($materials)];
        $specs['Material Fretboard'] = ['Rosewood', 'Ebony', 'Maple', 'Pau Ferro', 'Jatoba'][rand(0, 4)];
        $specs['Jumlah Fret'] = rand(0, 1) ? '21 Frets' : '22 Frets';
        
        if (strpos($catName, 'elektrik') !== false || strpos($catName, 'bass') !== false) {
            $pickups = ['HSS Configuration', 'Dual Humbuckers', 'SSS Single-Coils', 'PJ Configuration', 'Active EMG'];
            $specs['Konfigurasi Pickup'] = $pickups[array_rand($pickups)];
            $specs['Kontrol Elektrik'] = '1x Volume, 2x Tone, 5-Way Switch';
        } else {
            $specs['Tipe String'] = 'Nylon / Steel Acoustic Strings';
        }
        
    } elseif (strpos($catName, 'keyboard') !== false || strpos($catName, 'piano') !== false) {
        $specs['Jumlah Tuts'] = rand(0, 1) ? '61 Tuts' : '88 Tuts';
        $specs['Polyphony'] = rand(0, 1) ? '128 Notes' : '256 Notes';
        $specs['Touch Response'] = 'Soft, Medium, Hard, Fixed';
        $specs['Konektivitas'] = 'USB to Host, MIDI In/Out, Headphone Jack';
        
    } elseif (strpos($catName, 'drum') !== false) {
        $specs['Material Shell'] = ['Birch Wood', 'Maple Wood', 'Poplar Wood', 'Mahogany'][rand(0, 3)];
        $specs['Konfigurasi'] = '5-Piece (Bass, 2x Toms, Floor Tom, Snare)';
        $specs['Termasuk Hardware'] = rand(0, 1) ? 'Ya (Stand & Pedal)' : 'Tidak (Hanya Shell Pack)';
        
    } elseif (strpos($catName, 'audio') !== false || strpos($catName, 'recording') !== false) {
        $specs['Resolusi Audio'] = '24-bit / 192 kHz';
        $specs['Phantom Power'] = '+48V Support';
        $specs['Koneksi PC/Mac'] = 'USB Type-C';
        
    } else {
        // Fallback for others (Biola, Tiup, Aksesoris)
        $specs['Material Utama'] = 'Premium Grade Material';
        $specs['Asal Pembuatan'] = ['Jepang', 'Amerika Serikat', 'Indonesia', 'Korea Selatan'][rand(0, 3)];
    }
    
    // Save to DB
    foreach ($specs as $key => $value) {
        ProductSpecification::create([
            'product_id' => $product->id,
            'spec_name' => $key,
            'spec_value' => $value,
        ]);
    }
}

echo "Berhasil meng-generate tabel spesifikasi detail untuk " . count($products) . " produk!\n";
