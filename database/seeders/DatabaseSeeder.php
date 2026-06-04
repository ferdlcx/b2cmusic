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
        // 1. Seed Users (Super Admin, Admin & Customer)
        $superAdmin = User::create([
            'name' => 'Super Admin MusicStore',
            'email' => 'superadmin@musicstore.com',
            'password' => Hash::make('password'),
            'phone' => '081122334455',
            'role' => 'super_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $admin = User::create([
            'name' => 'Admin MusicStore',
            'email' => 'admin@musicstore.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $customer = User::create([
            'name' => 'Aria Customer',
            'email' => 'user@musicstore.com',
            'password' => Hash::make('password'),
            'phone' => '089876543210',
            'role' => 'customer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // 2. Seed Customer Addresses
        $address1 = Address::create([
            'user_id' => $customer->id,
            'label' => 'Rumah',
            'name' => 'Aria Customer',
            'phone' => '089876543210',
            'address' => 'Jl. Harmony Raya No. 45, Kebayoran Baru',
            'city' => 'Jakarta Selatan',
            'city_id' => 153,
            'province' => 'DKI Jakarta',
            'province_id' => 6,
            'district' => 'Kebayoran Baru',
            'village' => 'Melawai',
            'latitude' => -6.244589,
            'longitude' => 106.800534,
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
            'city_id' => 152,
            'province' => 'DKI Jakarta',
            'province_id' => 6,
            'district' => 'Tanah Abang',
            'village' => 'Karet Tengsin',
            'latitude' => -6.213589,
            'longitude' => 106.818534,
            'postal_code' => '10220',
            'is_default' => false,
        ]);

        // 2.5 Seed Brands (25 Brands for B2C Requirement)
        $brandsList = [
            'Yamaha', 'Fender', 'Gibson', 'Ibanez', 'Cort', 
            'Roland', 'Korg', 'Boss', 'Marshall', 'Shure', 
            'Audio-Technica', 'Sennheiser', 'Focusrite', 'Pioneer', 'Kala', 
            'Hohner', 'D\'Addario', 'Elixir', 'Hercules', 'Pearl', 
            'Genta', 'Taylor', 'Martin', 'Epiphone', 'Lokal'
        ];
        
        $brands = [];
        foreach ($brandsList as $bName) {
            $brands[$bName] = \App\Models\Brand::create([
                'name' => $bName,
                'slug' => Str::slug($bName),
                'description' => "Produsen instrumen musik dan aksesoris audio premium merek {$bName}.",
                'status' => true,
            ]);
        }

        // 3. Seed Categories (13 Categories from User Request)
        $categoriesData = [
            [
                'name' => 'Gitar Akustik',
                'description' => 'Gitar akustik premium dari merek lokal dan internasional untuk kualitas melodi murni.',
                'image' => 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Gitar Elektrik',
                'description' => 'Gitar elektrik dengan pickup dinamis untuk solo rock dan petikan blues modern.',
                'image' => 'https://images.unsplash.com/photo-1564186763535-ebb21ef5277f?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Bass',
                'description' => 'Bass elektrik dan akustik untuk getaran nada rendah yang tebal dan solid.',
                'image' => 'https://images.unsplash.com/photo-1583000292278-59a019403d57?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Keyboard & Piano',
                'description' => 'Keyboard digital dan grand piano untuk komposisi orkestra maupun pop modern.',
                'image' => 'https://images.unsplash.com/photo-1552422535-c45813c61732?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Drum',
                'description' => 'Drum akustik perkusi dan electronic drum set untuk ritme musik yang bertenaga.',
                'image' => 'https://images.unsplash.com/photo-1519892300165-cb5542fb47c7?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Biola',
                'description' => 'Biola klasik dengan busur rambut kuda asli untuk suara string yang emosional.',
                'image' => 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Alat Musik Tiup',
                'description' => 'Saxophone, trumpet, dan flute untuk nada kuningan dan hembusan kayu yang elegan.',
                'image' => 'https://images.unsplash.com/photo-1528390979304-43f500cc66c6?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Audio & Recording',
                'description' => 'Mikrofon studio condenser, audio interface USB, dan monitor studio active.',
                'image' => 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Effect & Pedal',
                'description' => 'Pedal efek stompbox analog dan modul multi-effect untuk modifikasi suara gitar.',
                'image' => 'https://images.unsplash.com/photo-1598517707371-519c4e69d8fc?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Ukulele',
                'description' => 'Ukulele soprano, concert, dan tenor dengan tone ceria khas pantai tropis.',
                'image' => 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Harmonica',
                'description' => 'Harmonika diatonic dan chromatic untuk suara folk, blues, dan country tradisional.',
                'image' => 'https://images.unsplash.com/photo-1601004890684-d8cbf643f5f2?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Alat Musik Tradisional',
                'description' => 'Alat musik tradisional Indonesia yang kaya nilai budaya, terbuat dari bambu dan logam.',
                'image' => 'https://images.unsplash.com/photo-1629896791456-c73dbca6b86f?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Aksesoris',
                'description' => 'Senar instrumen, pick, capo, strap gitar, stand, tuner, dan gig bag pelindung.',
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

        // 4. Products Data mapping
        $productsData = [
            'Gitar Akustik' => [
                [
                    'name' => 'Cort AD810 Acoustic Guitar',
                    'brand' => 'Cort',
                    'short_description' => 'Gitar akustik lokal berkualitas standar internasional untuk pemula.',
                    'description' => 'Cort AD810 adalah gitar akustik entry-level terpopuler di Indonesia. Menggunakan top Spruce dan back & sides Mahogany, menghasilkan suara yang seimbang dan nyaring.',
                    'price' => 1550000,
                    'discount_price' => null,
                    'stock' => 20,
                    'sku' => 'GTR-CRT-AD810',
                    'image' => 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&w=800&q=80',
                    'weight' => 4000,
                    'specs' => [
                        'Body Shape' => 'Dreadnought',
                        'Top Wood' => 'Spruce',
                        'Back & Sides' => 'Mahogany',
                        'Neck Wood' => 'Mahogany',
                        'Fretboard' => 'Merbau'
                    ]
                ],
                [
                    'name' => 'Genta AG-100 Local Premium',
                    'brand' => 'Genta',
                    'short_description' => 'Gitar akustik buatan pengrajin lokal Bandung dengan pengerjaan halus.',
                    'description' => 'Genta AG-100 dibuat secara manual oleh pengrajin ahli di Bandung. Menggunakan kayu lokal pilihan menghasilkan suara mid-range yang hangat dan sustain menawan.',
                    'price' => 2400000,
                    'discount_price' => null,
                    'stock' => 10,
                    'sku' => 'GTR-GNT-AG100',
                    'image' => 'https://images.unsplash.com/photo-1550985616-10810253b84d?auto=format&fit=crop&w=800&q=80',
                    'weight' => 4000,
                    'specs' => [
                        'Body Shape' => 'Concert',
                        'Top Wood' => 'Solid Spruce',
                        'Back & Sides' => 'Rosewood',
                        'Made In' => 'Bandung, Indonesia'
                    ]
                ],
                [
                    'name' => 'Yamaha F310 Folk Acoustic',
                    'brand' => 'Yamaha',
                    'short_description' => 'Gitar akustik legendaris Yamaha dengan kenyamanan grip luar biasa.',
                    'description' => 'Yamaha F310 menawarkan kualitas, desain, dan suara khas Yamaha dalam paket terjangkau. Bodi berukuran sedikit lebih ramping mempermudah permainan bagi pemula.',
                    'price' => 1750000,
                    'discount_price' => null,
                    'stock' => 15,
                    'sku' => 'GTR-YAM-F310',
                    'image' => 'https://images.unsplash.com/photo-1511376777868-611b54f68947?auto=format&fit=crop&w=800&q=80',
                    'weight' => 4000,
                    'specs' => [
                        'Body Shape' => 'Traditional Western',
                        'Top Wood' => 'Spruce',
                        'Back & Sides' => 'Locally Sourced Tonewood',
                        'Neck Wood' => 'Locally Sourced Tonewood'
                    ]
                ],
                [
                    'name' => 'Taylor GS Mini Mahogany',
                    'brand' => 'Taylor',
                    'short_description' => 'Gitar akustik travel premium berskala ringkas dengan proyeksi suara megah.',
                    'description' => 'Taylor GS Mini Mahogany menghadirkan suara penuh yang luar biasa dalam ukuran ringkas yang mudah dibawa ke mana saja. Menggunakan kayu Mahogany padat pada bagian top.',
                    'price' => 10200000,
                    'discount_price' => 9800000,
                    'stock' => 5,
                    'sku' => 'GTR-TAY-GSMINI',
                    'image' => 'https://images.unsplash.com/photo-1605020482762-689584b27f55?auto=format&fit=crop&w=800&q=80',
                    'weight' => 3500,
                    'specs' => [
                        'Body Shape' => 'GS Mini (Scaled-down Symphony)',
                        'Top Wood' => 'Solid Tropical Mahogany',
                        'Back & Sides' => 'Layered Sapele',
                        'Fretboard' => 'West African Crelicam Ebony'
                    ]
                ]
            ],
            'Gitar Elektrik' => [
                [
                    'name' => 'Cort G110 Electric Guitar',
                    'brand' => 'Cort',
                    'short_description' => 'Gitar elektrik lokal dengan konfigurasi pickup HSS serbaguna.',
                    'description' => 'Cort G110 menghadirkan desain double-cutaway klasik dengan bodi tipis yang nyaman dimainkan. Konfigurasi pickup HSS (Humbucker-Single-Single) ideal untuk berbagai genre musik.',
                    'price' => 2150000,
                    'discount_price' => null,
                    'stock' => 12,
                    'sku' => 'GTR-CRT-G110',
                    'image' => 'https://images.unsplash.com/photo-1564186763535-ebb21ef5277f?auto=format&fit=crop&w=800&q=80',
                    'weight' => 4500,
                    'specs' => [
                        'Body Wood' => 'Poplar',
                        'Neck Wood' => 'Hard Maple',
                        'Fretboard' => 'Jatoba',
                        'Pickup Configuration' => 'H-S-S'
                    ]
                ],
                [
                    'name' => 'Fender Player Stratocaster HSS',
                    'brand' => 'Fender',
                    'short_description' => 'Suara Stratocaster autentik dengan sentuhan humbucker modern.',
                    'description' => 'Menghadirkan nuansa klasik Fender dengan performa modern. Seri Player ini memadukan suara single-coil yang jernih di posisi neck & middle dengan humbucker bertenaga di bridge.',
                    'price' => 14800000,
                    'discount_price' => 13999000,
                    'stock' => 4,
                    'sku' => 'GTR-FEN-STRATHSS',
                    'image' => 'https://images.unsplash.com/photo-1550985616-10810253b84d?auto=format&fit=crop&w=800&q=80',
                    'weight' => 4500,
                    'specs' => [
                        'Body Wood' => 'Alder Wood',
                        'Neck Wood' => 'Maple Wood',
                        'Fretboard' => 'Pau Ferro',
                        'Pickup Configuration' => 'H-S-S'
                    ]
                ],
                [
                    'name' => 'Gibson Les Paul Standard 60s',
                    'brand' => 'Gibson',
                    'short_description' => 'Gitar elektrik legendaris dengan sustain panjang dan tone tebal.',
                    'description' => 'Les Paul Standard 60s memiliki bodi mahoni tanpa lubang peredam bobot, dengan top maple bermotif indah. Menggunakan sepasang pickup humbucker Burstbucker 60s untuk karakter klasik Gibson.',
                    'price' => 42500000,
                    'discount_price' => null,
                    'stock' => 2,
                    'sku' => 'GTR-GIB-LP60S',
                    'image' => 'https://images.unsplash.com/photo-1514649923863-ceaf75b7ec00?auto=format&fit=crop&w=800&q=80',
                    'weight' => 5000,
                    'specs' => [
                        'Body Wood' => 'Mahogany',
                        'Top Wood' => 'AA Figured Maple',
                        'Neck Wood' => 'Mahogany',
                        'Fretboard' => 'Rosewood',
                        'Pickups' => 'Burstbucker 61R & 61T'
                    ]
                ]
            ],
            'Bass' => [
                [
                    'name' => 'Cort Action Bass Plus PJ',
                    'brand' => 'Cort',
                    'short_description' => 'Bass elektrik lokal 4-senar aktif dengan fleksibilitas suara PJ.',
                    'description' => 'Cort Action Bass Plus dilengkapi set pickup tipe PJ yang fleksibel untuk musik rock, jazz, maupun funk. Karakter elektronik aktif menghasilkan sound yang bersih dan punchy.',
                    'price' => 2750000,
                    'discount_price' => null,
                    'stock' => 10,
                    'sku' => 'BSS-CRT-ACTPJ',
                    'image' => 'https://images.unsplash.com/photo-1583000292278-59a019403d57?auto=format&fit=crop&w=800&q=80',
                    'weight' => 5000,
                    'specs' => [
                        'Number of Strings' => '4 Strings',
                        'Body Wood' => 'Poplar',
                        'Neck Wood' => 'Hard Maple',
                        'Electronics' => 'Active 2-Band EQ'
                    ]
                ],
                [
                    'name' => 'Fender Player Jazz Bass V',
                    'brand' => 'Fender',
                    'short_description' => 'Bass 5-senar legendaris dengan dua pickup single-coil berkarakter growl.',
                    'description' => 'Dengan dua pickup single-coil Player Series Jazz Bass, bass 5-senar ini menawarkan kombinasi growl tinggi yang ikonik dan dentuman rendah yang presisi.',
                    'price' => 16500000,
                    'discount_price' => null,
                    'stock' => 3,
                    'sku' => 'BSS-FEN-JBASS5',
                    'image' => 'https://images.unsplash.com/photo-1561777848-6f58e26be780?auto=format&fit=crop&w=800&q=80',
                    'weight' => 5500,
                    'specs' => [
                        'Number of Strings' => '5 Strings',
                        'Body Wood' => 'Alder Wood',
                        'Neck Wood' => 'Maple Wood',
                        'Pickups' => '2 Player Series Alnico 5 Single-Coil'
                    ]
                ]
            ],
            'Keyboard & Piano' => [
                [
                    'name' => 'Yamaha PSR-E373 Keyboard',
                    'brand' => 'Yamaha',
                    'short_description' => 'Keyboard portabel 61-tuts dengan touch-sensitive keys.',
                    'description' => 'Yamaha PSR-E373 dilengkapi dengan generator nada LSI yang dikembangkan baru, memberikan peningkatan kualitas suara yang luar biasa. Sempurna untuk belajar maupun performa kasual.',
                    'price' => 3450000,
                    'discount_price' => null,
                    'stock' => 15,
                    'sku' => 'KEY-YAM-PSRE373',
                    'image' => 'https://images.unsplash.com/photo-1552422535-c45813c61732?auto=format&fit=crop&w=800&q=80',
                    'weight' => 6000,
                    'specs' => [
                        'Number of Keys' => '61 Keys',
                        'Touch Response' => 'Yes (Soft, Medium, Hard, Fixed)',
                        'Polyphony' => '48 Notes',
                        'Number of Voices' => '622 Voices'
                    ]
                ],
                [
                    'name' => 'Roland FP-30X Digital Piano',
                    'brand' => 'Roland',
                    'short_description' => 'Digital piano portabel premium dengan sound engine SuperNATURAL.',
                    'description' => 'FP-30X memadukan estetika minimalis dengan performa instrumen premium. Fitur tuts PHA-4 Standard menyajikan nuansa sentuhan grand piano akustik asli.',
                    'price' => 11500000,
                    'discount_price' => null,
                    'stock' => 6,
                    'sku' => 'KEY-ROL-FP30X',
                    'image' => 'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?auto=format&fit=crop&w=800&q=80',
                    'weight' => 18000,
                    'specs' => [
                        'Number of Keys' => '88 Keys',
                        'Keyboard Action' => 'PHA-4 Standard Keyboard with Escapement & Ivory Feel',
                        'Sound Engine' => 'SuperNATURAL Piano',
                        'Bluetooth' => 'Audio & MIDI (Ver 3.0/4.0)'
                    ]
                ]
            ],
            'Drum' => [
                [
                    'name' => 'Yamaha Stage Custom Birch Acoustic Set',
                    'brand' => 'Yamaha',
                    'short_description' => 'Drum akustik kayu Birch 100% dengan standard kualitas studio.',
                    'description' => 'Yamaha Stage Custom Birch menggunakan kayu Birch penuh untuk menghasilkan proyeksi punchy dan resonansi bodi yang kaya. (Termasuk hardware, cymbal dijual terpisah).',
                    'price' => 13500000,
                    'discount_price' => null,
                    'stock' => 3,
                    'sku' => 'DRM-YAM-SCBIRCH',
                    'image' => 'https://images.unsplash.com/photo-1519892300165-cb5542fb47c7?auto=format&fit=crop&w=800&q=80',
                    'weight' => 35000,
                    'specs' => [
                        'Shell Material' => '100% Birch (6-ply)',
                        'Configuration' => '5-Piece Drum Set (Bass, 2 Toms, Floor Tom, Snare)',
                        'Hardware' => 'Yamaha HW780 Hardware Pack'
                    ]
                ],
                [
                    'name' => 'Roland TD-07KV V-Drums',
                    'brand' => 'Roland',
                    'short_description' => 'Electronic drum kit kompak dengan mesh head double-ply sensitif.',
                    'description' => 'Roland TD-07KV menghadirkan ekspresi drum elektrik kelas atas dalam paket rumah tangga yang senyap. Pad mesh head ganda khas Roland memberikan feedback pukulan yang alami.',
                    'price' => 14200000,
                    'discount_price' => 13500000,
                    'stock' => 5,
                    'sku' => 'DRM-ROL-TD07KV',
                    'image' => 'https://images.unsplash.com/photo-1524230507-68609b857e90?auto=format&fit=crop&w=800&q=80',
                    'weight' => 22000,
                    'specs' => [
                        'Snare Pad' => 'PDX-8 (8-inch double-mesh)',
                        'Tom Pads' => '3x PDX-6A (6-inch single-mesh)',
                        'Cymbal Pads' => 'Crash & Ride with Choke Support',
                        'Drum Module' => 'TD-07 with 25 Preset Kits & Bluetooth'
                    ]
                ]
            ],
            'Biola' => [
                [
                    'name' => 'Yamaha V5SG Acoustic Violin',
                    'brand' => 'Yamaha',
                    'short_description' => 'Biola akustik pemula ukuran 4/4 dengan case, bow, dan rosin.',
                    'description' => 'Biola Yamaha V5SG sangat cocok untuk siswa pemula. Bodi kayu Spruce padat di top dan Maple di bagian belakang dibuat secara hand-crafted untuk kualitas nada prima.',
                    'price' => 7400000,
                    'discount_price' => null,
                    'stock' => 8,
                    'sku' => 'VLN-YAM-V5SG',
                    'image' => 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?auto=format&fit=crop&w=800&q=80',
                    'weight' => 2500,
                    'specs' => [
                        'Size' => '4/4 Full Size',
                        'Top Material' => 'Spruce Wood',
                        'Back & Sides' => 'Maple Wood',
                        'Fretboard & Pegs' => 'Ebony Wood'
                    ]
                ]
            ],
            'Alat Musik Tiup' => [
                [
                    'name' => 'Yamaha YAS-280 Alto Saxophone',
                    'brand' => 'Yamaha',
                    'short_description' => 'Saxophone alto terpopuler dengan intonasi akurat.',
                    'description' => 'Saxophone Yamaha YAS-280 dirancang khusus untuk pemula. Desain bodi ringan dan ergonomis mempermudah genggaman serta tiupan nada rendah maupun tinggi yang stabil.',
                    'price' => 15800000,
                    'discount_price' => 14900000,
                    'stock' => 4,
                    'sku' => 'WND-YAM-YAS280',
                    'image' => 'https://images.unsplash.com/photo-1528390979304-43f500cc66c6?auto=format&fit=crop&w=800&q=80',
                    'weight' => 5000,
                    'specs' => [
                        'Key' => 'Eb (Alto)',
                        'Body Finish' => 'Gold Lacquer',
                        'Auxiliary Keys' => 'High F#, Front F',
                        'Mouthpiece' => 'Yamaha 4C'
                    ]
                ],
                [
                    'name' => 'Yamaha YTR-2330 Bb Trumpet',
                    'brand' => 'Yamaha',
                    'short_description' => 'Trumpet Bb dengan tiupan ringan dan respon tone yang cerah.',
                    'description' => 'Trumpet ideal untuk siswa belajar. Didesain tanpa brace pada bell untuk mempermudah pemain menghasilkan tiupan nada yang stabil dan intonasi yang pas.',
                    'price' => 7950000,
                    'discount_price' => null,
                    'stock' => 5,
                    'sku' => 'WND-YAM-YTR2330',
                    'image' => 'https://images.unsplash.com/photo-1573006939324-641d31296356?auto=format&fit=crop&w=800&q=80',
                    'weight' => 4000,
                    'specs' => [
                        'Key' => 'Bb',
                        'Bell Material' => 'Yellow Brass (Two-piece)',
                        'Valves/Pistons' => 'Monel Alloy',
                        'Bell Diameter' => '123 mm'
                    ]
                ]
            ],
            'Audio & Recording' => [
                [
                    'name' => 'Focusrite Scarlett 2i2 4th Gen',
                    'brand' => 'Focusrite',
                    'short_description' => 'USB audio interface 2-in/2-out terlaris untuk home studio.',
                    'description' => 'Generasi ke-4 Scarlett 2i2 membawa kualitas studio profesional ke meja Anda. Dilengkapi preamp ultra-low-noise dengan mode Air untuk vokal yang berkilau.',
                    'price' => 3850000,
                    'discount_price' => null,
                    'stock' => 15,
                    'sku' => 'AUD-FOC-2I2G4',
                    'image' => 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=800&q=80',
                    'weight' => 1500,
                    'specs' => [
                        'Inputs/Outputs' => '2 Inputs / 2 Outputs',
                        'Resolution' => '24-bit / 192 kHz',
                        'Preamp' => '2x Scarlett Preamp with Air Mode',
                        'Phantom Power' => 'Yes (+48V)'
                    ]
                ],
                [
                    'name' => 'Shure SM58 Dynamic Vocal Microphone',
                    'brand' => 'Shure',
                    'short_description' => 'Mikrofon vokal legendaris untuk panggung live dan studio.',
                    'description' => 'Mikrofon dinamis terpopuler di dunia. Memiliki pola cardioid yang meminimalkan feedback, serta konstruksi bodi tangguh berlapis logam antikarat.',
                    'price' => 1850000,
                    'discount_price' => null,
                    'stock' => 25,
                    'sku' => 'AUD-SHU-SM58',
                    'image' => 'https://images.unsplash.com/photo-1512525541699-20fcb1c1ec5d?auto=format&fit=crop&w=800&q=80',
                    'weight' => 800,
                    'specs' => [
                        'Type' => 'Dynamic',
                        'Polar Pattern' => 'Cardioid',
                        'Frequency Response' => '50 Hz - 15 kHz',
                        'Connector' => 'XLR 3-Pin'
                    ]
                ]
            ],
            'Effect & Pedal' => [
                [
                    'name' => 'Boss DS-1 Distortion Pedal',
                    'brand' => 'Boss',
                    'short_description' => 'Pedal distorsi gitar oranye legendaris standar industri musik rock.',
                    'description' => 'Boss DS-1 menyajikan distorsi klasik berkarakter tegas. Menghasilkan overdrive hangat hingga distorsi kencang tanpa menghilangkan karakter unik gitar Anda.',
                    'price' => 1150000,
                    'discount_price' => null,
                    'stock' => 30,
                    'sku' => 'PED-BOS-DS1',
                    'image' => 'https://images.unsplash.com/photo-1598517707371-519c4e69d8fc?auto=format&fit=crop&w=800&q=80',
                    'weight' => 600,
                    'specs' => [
                        'Input Impedance' => '1 M ohms',
                        'Output Impedance' => '10 k ohms',
                        'Controls' => 'TONE knob, LEVEL knob, DIST knob',
                        'Power Supply' => '9V Battery or AC Adapter'
                    ]
                ]
            ],
            'Ukulele' => [
                [
                    'name' => 'Kala KA-15S Mahogany Soprano',
                    'brand' => 'Kala',
                    'short_description' => 'Ukulele soprano kayu mahoni dengan tone akustik cerah.',
                    'description' => 'Kala KA-15S adalah standar industri untuk ukulele belajar. Terbuat dari bodi Mahoni penuh dengan senar Aquila Super Nylgut asli.',
                    'price' => 1250000,
                    'discount_price' => null,
                    'stock' => 12,
                    'sku' => 'UKU-KAL-KA15S',
                    'image' => 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=800&q=80',
                    'weight' => 1000,
                    'specs' => [
                        'Size' => 'Soprano',
                        'Body Material' => 'Mahogany',
                        'Strings' => 'Aquila Super Nylgut',
                        'Finish' => 'Satin'
                    ]
                ]
            ],
            'Harmonica' => [
                [
                    'name' => 'Hohner Marine Band 1896 Classic',
                    'brand' => 'Hohner',
                    'short_description' => 'Harmonika diatonic blues legendaris buatan Jerman.',
                    'description' => 'Hohner Marine Band adalah instrumen utama musisi blues dunia sejak 1896. Dibuat dengan comb kayu pir pernis ganda untuk intonasi legendaris.',
                    'price' => 750000,
                    'discount_price' => null,
                    'stock' => 15,
                    'sku' => 'HRM-HOH-MB1896',
                    'image' => 'https://images.unsplash.com/photo-1601004890684-d8cbf643f5f2?auto=format&fit=crop&w=800&q=80',
                    'weight' => 300,
                    'specs' => [
                        'Type' => 'Diatonic',
                        'Key' => 'C',
                        'Number of Holes' => '10 Holes',
                        'Reed Plates' => '0.9 mm Brass, Made in Germany'
                    ]
                ]
            ],
            'Alat Musik Tradisional' => [
                [
                    'name' => 'Angklung Sunda Set 8 Nada (Do-Do)',
                    'brand' => 'Lokal',
                    'short_description' => 'Angklung bambu hitam tradisional Sunda 1 oktaf lengkap.',
                    'description' => 'Dibuat dari bambu wulung hitam pilihan yang dikeringkan secara alami. Disetel presisi oleh seniman angklung untuk melodi tradisional yang indah.',
                    'price' => 450000,
                    'discount_price' => null,
                    'stock' => 10,
                    'sku' => 'TRD-LKL-ANGK8',
                    'image' => 'https://images.unsplash.com/photo-1629896791456-c73dbca6b86f?auto=format&fit=crop&w=800&q=80',
                    'weight' => 5000,
                    'specs' => [
                        'Material' => 'Bambu Wulung (Black Bamboo)',
                        'Octave' => '1 Octave (8 Notes: C to C)',
                        'Origin' => 'Jawa Barat, Indonesia'
                    ]
                ],
                [
                    'name' => 'Sasando Rote Tradisional 28 Dawai',
                    'brand' => 'Lokal',
                    'short_description' => 'Alat musik petik khas Rote NTT berbahan daun lontar asli.',
                    'description' => 'Sasando petik autentik buatan pengrajin Kupang, Nusa Tenggara Timur. Resonator terbuat dari anyaman daun lontar pilihan dengan 28 dawai kuningan.',
                    'price' => 3500000,
                    'discount_price' => null,
                    'stock' => 3,
                    'sku' => 'TRD-LKL-SAS28',
                    'image' => 'https://images.unsplash.com/photo-1618609377864-68609b857e90?auto=format&fit=crop&w=800&q=80',
                    'weight' => 6000,
                    'specs' => [
                        'Type' => 'Sasando Petik',
                        'Strings' => '28 Strings',
                        'Material' => 'Daun Lontar, Bambu, Brass Strings',
                        'Origin' => 'Rote, Nusa Tenggara Timur'
                    ]
                ]
            ],
            'Aksesoris' => [
                [
                    'name' => 'D\'Addario EXL110 Electric Guitar Strings',
                    'brand' => 'D\'Addario',
                    'short_description' => 'Senar gitar elektrik nickel wound ukuran 10-46.',
                    'description' => 'Senar gitar listrik terlaris di dunia. Memberikan tone yang sangat jernih dan fleksibilitas tinggi dengan ketahanan karat premium.',
                    'price' => 85000,
                    'discount_price' => null,
                    'stock' => 100,
                    'sku' => 'ACC-DAD-EXL110',
                    'image' => 'https://images.unsplash.com/photo-1618609377864-68609b857e90?auto=format&fit=crop&w=800&q=80',
                    'weight' => 100,
                    'specs' => [
                        'Gauge' => 'Regular Light (10-13-17-26-36-46)',
                        'Material' => 'Nickel Wound',
                        'Country' => 'Made in USA'
                    ]
                ],
                [
                    'name' => 'Elixir Nanoweb Phosphor Bronze 11-52',
                    'brand' => 'Elixir',
                    'short_description' => 'Senar gitar akustik dengan teknologi coating Nanoweb tahan lama.',
                    'description' => 'Elixir Nanoweb memberikan rasa sentuhan senar tanpa coating namun tahan karat 3 hingga 5 kali lebih lama dari senar akustik konvensional.',
                    'price' => 245000,
                    'discount_price' => null,
                    'stock' => 50,
                    'sku' => 'ACC-ELX-NW1152',
                    'image' => 'https://images.unsplash.com/photo-1618609377864-68609b857e90?auto=format&fit=crop&w=800&q=80',
                    'weight' => 100,
                    'specs' => [
                        'Gauge' => 'Custom Light (11-15-22-32-42-52)',
                        'Coating' => 'Ultra-thin Nanoweb Coating',
                        'Material' => 'Phosphor Bronze'
                    ]
                ],
                [
                    'name' => 'Hercules GS414B Plus Guitar Stand',
                    'brand' => 'Hercules',
                    'short_description' => 'Stand gitar gantung premium dengan sistem Auto Grip System (AGS).',
                    'description' => 'Hercules GS414B Plus mengamankan gitar Anda secara instan menggunakan sistem kunci gravitasi otomatis AGS. Ketinggian tiang mudah disesuaikan.',
                    'price' => 680000,
                    'discount_price' => null,
                    'stock' => 15,
                    'sku' => 'ACC-HER-GS414B',
                    'image' => 'https://images.unsplash.com/photo-1618609377864-68609b857e90?auto=format&fit=crop&w=800&q=80',
                    'weight' => 3000,
                    'specs' => [
                        'System' => 'Auto Grip System (AGS) with Auto Shield',
                        'Height Range' => '950 mm - 1150 mm',
                        'Load Capacity' => '15 kg',
                        'Base Radius' => '310 mm'
                    ]
                ]
            ]
        ];

        foreach ($productsData as $catName => $products) {
            $cat = $categories[$catName];
            foreach ($products as $pData) {
                // Determine discount dates if there's a discount price
                $discountStart = null;
                $discountEnd = null;
                if ($pData['discount_price']) {
                    $discountStart = now()->subDays(2);
                    $discountEnd = now()->addDays(30);
                }

                $brandModel = $brands[$pData['brand']] ?? \App\Models\Brand::firstOrCreate([
                    'name' => $pData['brand'],
                    'slug' => Str::slug($pData['brand']),
                    'description' => "Produsen instrumen musik dan aksesoris audio premium merek {$pData['brand']}.",
                    'status' => true,
                ]);

                // Create Product
                $product = Product::create([
                    'category_id' => $cat->id,
                    'brand_id' => $brandModel->id,
                    'condition' => 'new',
                    'sold_count' => rand(5, 45),
                    'name' => $pData['name'],
                    'slug' => Str::slug($pData['name']),
                    'short_description' => $pData['short_description'],
                    'description' => $pData['description'],
                    'price' => $pData['price'],
                    'discount_price' => $pData['discount_price'],
                    'discount_start' => $discountStart,
                    'discount_end' => $discountEnd,
                    'weight' => $pData['weight'],
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

                // Create secondary image for details
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
                    'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                ]);

                // Create reviews for each product
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $customer->id,
                    'rating' => rand(4, 5),
                    'comment' => 'Produk sangat memuaskan, kualitas bahan premium dan suara sangat mantap! Pengiriman cepat.',
                ]);
            }
        }

        // 4.5 Programmatically expand to 100+ products (total ~117 products)
        $extraBrands = ['Yamaha', 'Fender', 'Gibson', 'Ibanez', 'Cort', 'Roland', 'Korg', 'Boss'];
        foreach ($categories as $catName => $cat) {
            $existingCount = Product::where('category_id', $cat->id)->count();
            $needed = 9 - $existingCount;
            for ($i = 1; $i <= $needed; $i++) {
                $brandName = $extraBrands[array_rand($extraBrands)];
                $brandModel = $brands[$brandName];
                $name = $catName . ' ' . $brandName . ' Premium Edition ' . $i;
                $price = rand(15, 120) * 100000;
                
                $product = Product::create([
                    'category_id' => $cat->id,
                    'brand_id' => $brandModel->id,
                    'condition' => array_rand(['new' => 0, 'used' => 1, 'refurbished' => 2]),
                    'sold_count' => rand(0, 30),
                    'name' => $name,
                    'slug' => Str::slug($name) . '-' . Str::random(3),
                    'short_description' => "Instrumen {$catName} edisi khusus dengan kualitas akustik profesional.",
                    'description' => "Edisi khusus {$name} menawarkan nilai performa luar biasa bagi musisi profesional maupun pemula. Dibuat dengan presisi tinggi menggunakan material terpilih.",
                    'price' => $price,
                    'discount_price' => rand(1, 10) > 7 ? $price * 0.9 : null,
                    'weight' => rand(1000, 15000),
                    'stock' => rand(2, 20),
                    'sku' => strtoupper(substr($catName, 0, 3)) . '-' . strtoupper(substr($brandName, 0, 3)) . '-' . rand(100, 999) . $i,
                    'status' => true,
                ]);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=800&q=80',
                    'is_primary' => true,
                ]);

                ProductSpecification::create([
                    'product_id' => $product->id,
                    'spec_name' => 'Edisi',
                    'spec_value' => 'Premium Studio Edition',
                ]);

                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $customer->id,
                    'rating' => rand(4, 5),
                    'comment' => 'Kualitas instrumen sangat bagus untuk harganya. Sangat direkomendasikan!',
                    'status' => 'approved',
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
