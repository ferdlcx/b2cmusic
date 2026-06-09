<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class RealProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Fender Stratocaster American Professional II',
                'category' => 'Gitar Elektrik',
                'brand' => 'Fender',
                'price' => 28500000,
                'image' => 'https://media.guitarcenter.com/is/image/MMGS7/H74848000002000-01-600x600.jpg',
                'description' => 'The American Professional II Stratocaster draws from more than sixty years of innovation, inspiration and evolution to meet the demands of today’s working player.'
            ],
            [
                'name' => 'Fender Telecaster Player Series',
                'category' => 'Gitar Elektrik',
                'brand' => 'Fender',
                'price' => 14500000,
                'image' => 'https://nafiriguitar.com/cdn/shop/files/6B084A41-5C1D-41D4-A7A3-9947C879DA11.jpg?v=1748427120&width=1946',
                'description' => 'Bold, innovative and rugged, the Player Telecaster is pure Fender, through and through. The feel, the style and, most importantly, the sound—they’re all there, waiting for you to make them whisper or wail for your music.'
            ],
            [
                'name' => 'Fender FA-25 Dreadnought Acoustic Guitar, 3-Color Sunburst',
                'category' => 'Gitar Akustik',
                'brand' => 'Fender',
                'price' => 2370000,
                'image' => 'https://www.sweelee.co.id/cdn/shop/files/products_2FF03-097-1910-032_2FF03-097-1910-032_1719213023050_1200x1200.jpg?v=1719213202',
                'description' => 'Designed for beginners, the FA-25 Collection has all the sound and style of classic Fender acoustics with super-comfortable necks and lightweight bodies for more comfort and playability.'
            ],
            [
                'name' => 'Fender Player Precision Bass',
                'category' => 'Bass Elektrik',
                'brand' => 'Fender',
                'price' => 15200000,
                'image' => 'https://nafiriguitar.com/cdn/shop/files/04CDD7A6-330D-4BD4-BF17-079C85EA915F.jpg?v=1748859760&width=1946',
                'description' => 'There’s nothing more classic than a Fender electric bass, and the Player Precision Bass is as authentic as it gets.'
            ],
            [
                'name' => 'Fender Made In Japan Traditional 60s Jazz Bass',
                'category' => 'Bass Elektrik',
                'brand' => 'Fender',
                'price' => 21000000,
                'image' => 'https://nafiriguitar.com/cdn/shop/products/230725154457_IMG_0734_ori_28b8a938-dd17-4ff2-b12c-f7ab8b966142.jpg?v=1708491211',
                'description' => 'Made in Japan Traditional series was created by fusing Fender\'s traditional instrument-making aesthetics with sophisticated Japanese craftsmanship.'
            ],
            [
                'name' => 'Fender Professional Pedal Board Small',
                'category' => 'Aksesoris & Pedal',
                'brand' => 'Fender',
                'price' => 3500000,
                'image' => 'https://flipside-music.com/cdn/shop/products/Fender-Professional-Pedal-Board-Small-0991084001-b.jpg?v=1628443397',
                'description' => 'Low profile and precision-machined from anodized aluminum, the Fender Professional Pedal Board offers a strong and lightweight foundation for your pedals.'
            ],
            [
                'name' => 'Fender Tone Master Twin Reverb',
                'category' => 'Amplifier',
                'brand' => 'Fender',
                'price' => 22000000,
                'image' => 'https://www.sweelee.co.id/cdn/shop/files/products_2FF03-227-4904-000_2FF03-227-4904-000_1697167662660.jpg?v=1733885825&width=2048',
                'description' => 'The Tone Master Twin Reverb amplifier uses massive digital processing power to achieve a single remarkable sonic feat.'
            ],
            [
                'name' => 'Ibanez RG550 Genesis Collection',
                'category' => 'Gitar Elektrik',
                'brand' => 'Ibanez',
                'price' => 18000000,
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzDeQ2txbzV_5TMaF8wvwsG1t_BUyUHKovpQ&s',
                'description' => 'The Ibanez Genesis Collection reflects the origins of the RG series, maintaining the same features that made the original RG a legend.'
            ],
        ];

        foreach ($products as $item) {
            // Find or create category
            $categorySlug = Str::slug($item['category']);
            $category = Category::firstOrCreate(
                ['slug' => $categorySlug],
                ['name' => $item['category']]
            );

            // Find or create brand
            $brandSlug = Str::slug($item['brand']);
            $brand = Brand::firstOrCreate(
                ['slug' => $brandSlug],
                ['name' => $item['brand']]
            );

            // Create product
            $product = Product::firstOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'name' => $item['name'],
                    'short_description' => Str::limit($item['description'], 100),
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'weight' => 5000, // Default 5kg
                    'stock' => 10,
                    'sku' => strtoupper(Str::random(8)),
                    'status' => true,
                ]
            );

            // Add image if not exists
            if ($product->images()->count() == 0) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $item['image'],
                    'is_primary' => true,
                ]);
            }
        }
    }
}
