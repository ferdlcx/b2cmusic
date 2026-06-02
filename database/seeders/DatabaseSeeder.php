<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\ProductVideo;
use App\Models\Coupon;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users (Admin & Customer)
        $admin = User::create([
            'name' => 'Admin MusicStore',
            'email' => 'admin@musicstore.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $customer = User::create([
            'name' => 'Aria Customer',
            'email' => 'user@musicstore.com',
            'password' => Hash::make('password'),
            'phone' => '089876543210',
            'role' => 'customer',
            'status' => 'active',
        ]);

        // 2. Seed Customer Addresses
        $address1 = Address::create([
            'user_id' => $customer->id,
            'label' => 'Rumah',
            'name' => 'Aria Customer',
            'phone' => '089876543210',
            'address' => 'Jl. Harmony Raya No. 45, Kebayoran Baru',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'postal_code' => '12110',
            'is_default' => true,
        ]);

        $address2 = Address::create([
            'user_id' => $customer->id,
            'label' => 'Kantor',
            'name' => 'Aria Customer (C/O Studio)',
            'phone' => '0217654321',
            'address' => 'Gedung Melodi Lt. 4, Jl. Sudirman Kav. 21',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal_code' => '10220',
            'is_default' => false,
        ]);

        // 3. Seed Categories
        $categoriesData = [
            [
                'name' => 'Vinyl Records',
                'description' => 'Piringan hitam klasik dan modern untuk pengalaman mendengarkan musik analog terbaik.',
                'image' => 'https://images.unsplash.com/photo-1539628399283-a66940213b41?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Guitars',
                'description' => 'Gitar elektrik dan akustik premium dari merek legendaris dengan pengerjaan artisan.',
                'image' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Audio Gear',
                'description' => 'Mikrofon studio, headphone high-fidelity, dan audio interface untuk kualitas rekaman murni.',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Accessories',
                'description' => 'Kabel instrumen, strap kulit premium, senar, dan pilihan pick gitar untuk menunjang performa Anda.',
                'image' => 'https://images.unsplash.com/photo-1618609377864-68609b857e90?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'image' => $cat['image'],
                'status' => true,
            ]);
        }

        // 4. Seed Products with Specifications, Images, and Videos
        $productsData = [
            'Vinyl Records' => [
                [
                    'name' => 'Abbey Road Anniversary Edition Vinyl',
                    'brand' => 'The Beatles',
                    'short_description' => 'Album legendaris The Beatles dalam format piringan hitam 180 gram.',
                    'description' => 'Edisi ulang tahun ke-50 album Abbey Road dari The Beatles. Diproduksi ulang menggunakan kompresi pita analog asli untuk menghasilkan kedalaman suara yang sangat hangat dan autentik.',
                    'price' => 650000,
                    'stock' => 15,
                    'sku' => 'VIN-ABBEY-50',
                    'image' => 'https://images.unsplash.com/photo-1539628399283-a66940213b41?auto=format&fit=crop&w=800&q=80',
                    'specs' => [
                        'Format' => 'LP, 180 Gram',
                        'Merek Rekaman' => 'Apple Records',
                        'Kecepatan Putar' => '33 1/3 RPM',
                        'Rilis Pertama' => '1969 (Reissue 2019)'
                    ],
                ],
                [
                    'name' => 'The Dark Side of the Moon Reissue LP',
                    'brand' => 'Pink Floyd',
                    'short_description' => 'Piringan hitam mahakarya Pink Floyd dengan poster eksklusif.',
                    'description' => 'Album konsep legendaris Pink Floyd yang mendefinisikan progressive rock. Menghadirkan lagu-lagu masterpiece dengan kualitas audio stereo remastered terbaik.',
                    'price' => 720000,
                    'stock' => 8,
                    'sku' => 'VIN-DSOTM-RE',
                    'image' => 'https://images.unsplash.com/photo-1542208998-f6dbbb27a72f?auto=format&fit=crop&w=800&q=80',
                    'specs' => [
                        'Format' => 'Gatefold LP',
                        'Merek Rekaman' => 'Pink Floyd Records',
                        'Kecepatan Putar' => '33 1/3 RPM',
                        'Genre' => 'Progressive Rock'
                    ],
                ]
            ],
            'Guitars' => [
                [
                    'name' => 'Fender Stratocaster Player Series',
                    'brand' => 'Fender',
                    'short_description' => 'Gitar elektrik Fender ikonik dengan tone Stratocaster yang legendaris.',
                    'description' => 'Gitar listrik yang menginspirasi banyak musisi dunia. Seri Player ini mempertahankan nuansa klasik Fender dengan tambahan modern pickup Alnico V untuk karakter suara yang lebih jernih dan sustain panjang.',
                    'price' => 14500000,
                    'stock' => 5,
                    'sku' => 'GTR-FEN-STRAT',
                    'image' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=800&q=80',
                    'specs' => [
                        'Bahan Body' => 'Alder Wood',
                        'Bahan Neck' => 'Maple Wood',
                        'Bentuk Neck' => 'Modern C',
                        'Jumlah Fret' => '22 Medium Jumbo',
                        'Konfigurasi Pickup' => 'SSS (3 Player Series Single-Coil)'
                    ],
                ],
                [
                    'name' => 'Martin D-28 Dreadnought Acoustic',
                    'brand' => 'Martin Guitar',
                    'short_description' => 'Gitar akustik Dreadnought legendaris, standar industri musik akustik.',
                    'description' => 'Martin D-28 adalah gitar legendaris pilihan para musisi papan atas. Menggunakan kayu Sitka Spruce padat pada top dan East Indian Rosewood pada back & sides untuk suara yang sangat kaya, dalam, dan resonansi megah.',
                    'price' => 28500000,
                    'stock' => 2,
                    'sku' => 'GTR-MAR-D28',
                    'image' => 'https://images.unsplash.com/photo-1511376777868-611b54f68947?auto=format&fit=crop&w=800&q=80',
                    'specs' => [
                        'Bentuk Body' => 'Dreadnought',
                        'Bahan Top' => 'Solid Sitka Spruce',
                        'Bahan Back/Sides' => 'Solid East Indian Rosewood',
                        'Bahan Fingerboard' => 'Solid Black Ebony',
                        'Scale Length' => '25.4 inches'
                    ],
                ]
            ],
            'Audio Gear' => [
                [
                    'name' => 'Neumann TLM 103 Studio Condenser',
                    'brand' => 'Neumann',
                    'short_description' => 'Mikrofon kondensor studio kelas profesional untuk rekaman suara vokal murni.',
                    'description' => 'Mikrofon kondensor diafragma besar dengan kapsul cardioid legendaris Neumann. Memiliki tingkat self-noise yang sangat rendah dan respon transien yang luar biasa akurat, menjadikannya pilihan utama studio rekaman profesional.',
                    'price' => 22900000,
                    'stock' => 4,
                    'sku' => 'AUD-NEU-TLM103',
                    'image' => 'https://images.unsplash.com/photo-1512525541699-20fcb1c1ec5d?auto=format&fit=crop&w=800&q=80',
                    'specs' => [
                        'Pola Kutub (Pattern)' => 'Cardioid',
                        'Rentang Frekuensi' => '20 Hz - 20 kHz',
                        'Sensitivitas' => '23 mV/Pa',
                        'Konektor' => 'XLR 3-pin Male'
                    ],
                ],
                [
                    'name' => 'Sennheiser HD 600 Open-Back Headphones',
                    'brand' => 'Sennheiser',
                    'short_description' => 'Headphone open-back legendaris untuk audiophile dan professional mixing/mastering.',
                    'description' => 'Standard emas untuk monitoring audio dan mixing. Headphone open-back dengan respon frekuensi datar dan kejernihan spasial yang mengagumkan, memberikan sensasi audio sealami mungkin.',
                    'price' => 6400000,
                    'stock' => 12,
                    'sku' => 'AUD-SEN-HD600',
                    'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80',
                    'specs' => [
                        'Tipe Desain' => 'Open-back, Circumaural',
                        'Impedansi nominal' => '300 Ohms',
                        'Respon Frekuensi' => '12 Hz - 40.5 kHz',
                        'Panjang Kabel' => '3 meter (Detachable)'
                    ],
                ]
            ],
            'Accessories' => [
                [
                    'name' => 'Premium Leather Guitar Strap',
                    'brand' => 'Luxe Strap',
                    'short_description' => 'Strap gitar kulit asli kualitas premium dengan busa empuk demi kenyamanan bahu Anda.',
                    'description' => 'Strap gitar kulit asli dengan jahitan rapi yang sangat kuat. Dilengkapi lapisan padding busa tebal untuk mencegah lelah saat bermain gitar berjam-jam di panggung.',
                    'price' => 450000,
                    'stock' => 30,
                    'sku' => 'ACC-STR-LEATH',
                    'image' => 'https://images.unsplash.com/photo-1618609377864-68609b857e90?auto=format&fit=crop&w=800&q=80',
                    'specs' => [
                        'Bahan' => 'Genuine Top-Grain Cowhide Leather',
                        'Lebar Strap' => '3 inches (7.6 cm)',
                        'Rentang Panjang' => '42 to 55 inches (Adjustable)',
                        'Warna' => 'Vintage Chocolate Brown'
                    ],
                ]
            ]
        ];

        foreach ($productsData as $catName => $products) {
            $cat = $categories[$catName];
            foreach ($products as $pData) {
                // Create Product
                $product = Product::create([
                    'category_id' => $cat->id,
                    'name' => $pData['name'],
                    'slug' => Str::slug($pData['name']),
                    'brand' => $pData['brand'],
                    'short_description' => $pData['short_description'],
                    'description' => $pData['description'],
                    'price' => $pData['price'],
                    'stock' => $pData['stock'],
                    'sku' => $pData['sku'],
                    'status' => true,
                ]);

                // Create primary product image
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $pData['image'],
                    'is_primary' => true,
                ]);

                // Create some secondary image for details
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=800&q=80',
                    'is_primary' => false,
                ]);

                // Create specifications
                foreach ($pData['specs'] as $name => $value) {
                    ProductSpecification::create([
                        'product_id' => $product->id,
                        'spec_name' => $name,
                        'spec_value' => $value,
                    ]);
                }

                // Create product videos
                ProductVideo::create([
                    'product_id' => $product->id,
                    'title' => 'Official Demo & Sound Check',
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', // Dummy video link
                ]);

                // Create reviews for some products
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $customer->id,
                    'rating' => 5,
                    'comment' => 'Kualitasnya sangat luar biasa, layak dengan harganya! Pengiriman juga aman.',
                ]);
            }
        }

        // 5. Seed Coupons
        Coupon::create([
            'code' => 'MUSIC10',
            'type' => 'percent',
            'value' => 10.00,
            'min_purchase' => 500000,
            'max_discount' => 1000000,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonths(6),
            'status' => true,
        ]);

        Coupon::create([
            'code' => 'HEBATSOUND',
            'type' => 'fixed',
            'value' => 100000.00,
            'min_purchase' => 1000000,
            'max_discount' => 100000.00,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonths(6),
            'status' => true,
        ]);
    }
}
