<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $products = DB::table('products')->get();
        
        foreach ($products as $product) {
            $cat = DB::table('categories')->where('id', $product->category_id)->first();
            $catName = strtolower($cat->name);
            
            // Delete existing specs for this product to prevent duplicates
            DB::table('product_specifications')->where('product_id', $product->id)->delete();
            
            $specs = [];
            
            // Base specs
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
            } elseif (strpos($catName, 'drum') !== false) {
                $specs['Material Shell'] = ['Birch Wood', 'Maple Wood', 'Poplar Wood', 'Mahogany'][rand(0, 3)];
                $specs['Konfigurasi'] = '5-Piece Drum Set';
                $specs['Termasuk Hardware'] = rand(0, 1) ? 'Ya' : 'Tidak';
            } elseif (strpos($catName, 'audio') !== false || strpos($catName, 'recording') !== false) {
                $specs['Resolusi Audio'] = '24-bit / 192 kHz';
                $specs['Phantom Power'] = '+48V Support';
            } else {
                $specs['Material Utama'] = 'Premium Grade Material';
            }
            
            $insertData = [];
            foreach ($specs as $key => $value) {
                $insertData[] = [
                    'product_id' => $product->id,
                    'spec_name' => $key,
                    'spec_value' => $value,
                ];
            }
            DB::table('product_specifications')->insert($insertData);
            
            // Also clean up redundant HTML tables from description
            $desc = $product->description;
            $pos = strpos($desc, '<br><br>' . "\n" . '<h3');
            if ($pos !== false) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['description' => trim(substr($desc, 0, $pos))]);
            }
        }
    }

    public function down(): void
    {
        // No down needed for data migration
    }
};
