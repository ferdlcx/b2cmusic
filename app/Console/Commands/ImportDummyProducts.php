<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportDummyProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-sweelee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import scraped products from sweelee-scraper directory to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Try reading from seeders directory (for Render deployment) or fallback to scraper path
        $jsonPath = base_path('database/seeders/products.json');
        
        $imagesDestDir = storage_path('app/public/products');

        if (!File::exists($jsonPath)) {
            $this->error("File $jsonPath tidak ditemukan. Harap pastikan file ada.");
            return;
        }

        if (!File::exists($imagesDestDir)) {
            File::makeDirectory($imagesDestDir, 0755, true);
        }

        $this->info("Membaca data produk...");
        $productsData = json_decode(File::get($jsonPath), true);

        if (!$productsData) {
            $this->error("Gagal membaca atau mem-parsing products.json");
            return;
        }

        $this->info("Menghapus data produk lama (dan variannya)...");
        // Disable foreign key checks to allow truncating
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ProductImage::truncate();
        Product::truncate();
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Track mapped categories
        $categoryMap = [];

        $this->info("Mengimpor " . count($productsData) . " produk baru...");
        
        $bar = $this->output->createProgressBar(count($productsData));
        $bar->start();

        foreach ($productsData as $item) {
            // 1. Create or get Category
            $catName = $item['category_name'];
            if (!isset($categoryMap[$catName])) {
                $category = Category::create([
                    'name' => $catName,
                    'slug' => Str::slug($catName),
                    'description' => "Kategori untuk " . $catName,
                    'status' => true
                ]);
                $categoryMap[$catName] = $category->id;
            }

            // 2. Insert Product
            $product = Product::create([
                'category_id' => $categoryMap[$catName],
                'name' => $item['name'],
                'slug' => $item['slug'] . '-' . Str::random(4), // Prevent duplicate slugs
                'brand' => $item['brand'],
                'short_description' => "Alat musik {$catName} berkualitas dari {$item['brand']}.",
                'description' => $item['description'],
                'price' => $item['price'],
                'weight' => $item['weight'],
                'stock' => $item['stock'],
                'sku' => 'SKU-' . strtoupper(Str::random(8)),
                'status' => true
            ]);

            // 3. Find image and insert ProductImage
            $imageFilename = $item['primary_image'];
            if ($imageFilename && $imageFilename !== 'default.webp') {
                // Find existing file matching the name in storage/app/public/products
                $existingFiles = glob($imagesDestDir . '/*' . $imageFilename);
                if (count($existingFiles) > 0) {
                    $foundFile = basename($existingFiles[0]);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => '/storage/products/' . $foundFile,
                        'is_primary' => true
                    ]);
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Import selesai! Data dummy berhasil diganti. Silakan periksa di website/admin.");
    }
}
