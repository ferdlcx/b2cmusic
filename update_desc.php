<?php

use App\Models\Product;

$products = Product::all();

foreach ($products as $product) {
    // Basic description
    $desc = $product->description;
    
    // Check if it already has a table
    if (strpos($desc, '<table') !== false) {
        continue;
    }

    // Generate random realistic specs
    $materials = ['Solid Spruce', 'Mahogany', 'Alder', 'Rosewood', 'Maple', 'Ash', 'Basswood'];
    $pickups = ['Seymour Duncan Humbuckers', 'Fender Single-Coil', 'Fishman Fluence', 'EMG Active', 'N/A (Acoustic)'];
    $finishes = ['Gloss Polyurethane', 'Satin Nitrocellulose', 'Matte Black', 'Sunburst', 'Natural Wood'];
    
    $mat = $materials[array_rand($materials)];
    $neck = $materials[array_rand($materials)];
    $pickup = $pickups[array_rand($pickups)];
    $finish = $finishes[array_rand($finishes)];
    
    $weight = number_format(rand(25, 45) / 10, 1) . ' kg';
    $dimensions = rand(95, 110) . ' x ' . rand(30, 45) . ' x ' . rand(5, 15) . ' cm';
    
    $htmlTable = "
<br><br>
<h3 class=\"font-bold mb-3 text-slate-800 uppercase tracking-widest text-xs\">Spesifikasi Teknis Tambahan</h3>
<div class=\"overflow-hidden rounded-xl border border-slate-200\">
    <table class=\"w-full text-sm text-left text-slate-600\">
        <tbody>
            <tr class=\"border-b border-slate-200 bg-slate-50/50\">
                <th class=\"px-4 py-3 font-bold text-slate-900 w-1/3\">Dimensi Produk</th>
                <td class=\"px-4 py-3\">{$dimensions}</td>
            </tr>
            <tr class=\"border-b border-slate-200\">
                <th class=\"px-4 py-3 font-bold text-slate-900\">Berat Kotor</th>
                <td class=\"px-4 py-3\">{$weight}</td>
            </tr>
            <tr class=\"border-b border-slate-200 bg-slate-50/50\">
                <th class=\"px-4 py-3 font-bold text-slate-900\">Material Bodi</th>
                <td class=\"px-4 py-3\">{$mat}</td>
            </tr>
            <tr class=\"border-b border-slate-200\">
                <th class=\"px-4 py-3 font-bold text-slate-900\">Material Neck</th>
                <td class=\"px-4 py-3\">{$neck}</td>
            </tr>
            <tr class=\"border-b border-slate-200 bg-slate-50/50\">
                <th class=\"px-4 py-3 font-bold text-slate-900\">Konfigurasi Pickup</th>
                <td class=\"px-4 py-3\">{$pickup}</td>
            </tr>
            <tr>
                <th class=\"px-4 py-3 font-bold text-slate-900\">Finishing</th>
                <td class=\"px-4 py-3\">{$finish}</td>
            </tr>
        </tbody>
    </table>
</div>
";

    $product->description = $desc . $htmlTable;
    $product->save();
}

echo "Updated " . count($products) . " products with HTML tables!\n";
