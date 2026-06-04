<?php

use App\Models\Product;

$products = Product::all();
$count = 0;

foreach ($products as $product) {
    $desc = $product->description;
    
    // Find where the raw HTML table starts
    $pos = strpos($desc, '<br><br>' . "\n" . '<h3 class="font-bold mb-3');
    if ($pos !== false) {
        // Strip everything from that point onwards
        $cleanDesc = substr($desc, 0, $pos);
        $product->description = trim($cleanDesc);
        $product->save();
        $count++;
    }
}

echo "Berhasil menghapus tabel deskripsi duplikat dari $count produk!\n";
