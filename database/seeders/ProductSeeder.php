<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Category;
use App\Models\ProductVariant;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure categories exist before creating products
        $categories = Category::pluck('id', 'name')->toArray();
        if (empty($categories)) {
            $this->command->info('No categories found, calling CategorySeeder...');
            $this->call(CategorySeeder::class);
            $categories = Category::pluck('id', 'name')->toArray();
        }

        // Ensure sellers exist before creating products
        $sellers = Seller::all();
        if ($sellers->count() === 0) {
            $this->command->info('No sellers found, calling UserSeeder to create sellers...');
            $this->call(UserSeeder::class);
            $sellers = Seller::all();
        }

        // Prepare products data with specific category assignments
        $productsData = [
            // Produk Kuliner
            [
                'name' => 'Keripik Singkong Balado',
                'description' => 'Keripik singkong renyah dengan bumbu balado pedas asli dari UMKM lokal. Dibuat dengan bahan pilihan dan bumbu segar.',
                'price' => 25000,
                'stock' => 100,
                'weight' => 200,
                'image' => 'products/keripik-singkong-balado.jpg',
                'status' => 'active',
                'specifications' => [
                    'Netto' => '100 gram',
                    'Kadaluarsa' => '6 bulan',
                    'Penyimpanan' => 'Tempat kering dan sejuk',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Singkong dan bumbu',
                'size' => '100 gram',
                'color' => 'Coklat kemerahan',
                'brand' => 'UMKM Kuliner Nusantara',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak berlaku',
                'min_order' => 1,
                'ready_stock' => 100,
                'features' => [
                    'Renyah dan gurih',
                    'Bumbu balado asli',
                    'Tidak mengandung pengawet',
                    'Dibuat dengan bahan lokal'
                ],
                'category_name' => 'Kuliner'
            ],
            [
                'name' => 'Oleh-oleh Khas Bandung',
                'description' => 'Paket oleh-oleh khas Bandung yang terdiri dari berbagai camilan khas seperti peuyeum, dodol, dan kue kering.',
                'price' => 85000,
                'stock' => 50,
                'weight' => 500,
                'image' => 'products/oleh-oleh-bandung.jpg',
                'status' => 'active',
                'specifications' => [
                    'Netto' => '500 gram',
                    'Kadaluarsa' => '30 hari',
                    'Penyimpanan' => 'Tempat sejuk dan kering',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Beras ketan, gula, dan bahan alami',
                'size' => '500 gram',
                'color' => 'Berbagai warna',
                'brand' => 'Sari Bandung',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak berlaku',
                'min_order' => 1,
                'ready_stock' => 50,
                'features' => [
                    'Paket lengkap oleh-oleh',
                    'Bahan lokal terpilih',
                    'Kemasan menarik',
                    'Cocok untuk oleh-oleh'
                ],
                'category_name' => 'Kuliner'
            ],
            [
                'name' => 'Sambal Terasi Mantap',
                'description' => 'Sambal terasi pedas dengan rasa autentik khas Indonesia. Dibuat dari bahan-bahan segar pilihan.',
                'price' => 30000,
                'stock' => 75,
                'weight' => 250,
                'image' => 'products/sambal-terasi.jpg',
                'status' => 'active',
                'specifications' => [
                    'Netto' => '250 gram',
                    'Kadaluarsa' => '6 bulan',
                    'Penyimpanan' => 'Setelah dibuka simpan di kulkas',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Cabai, terasi, bawang, garam',
                'size' => '250 gram',
                'color' => 'Merah',
                'brand' => 'Sambal Nusantara',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak berlaku',
                'min_order' => 1,
                'ready_stock' => 75,
                'features' => [
                    'Rasa pedas mantap',
                    'Bahan segar',
                    'Tidak mengandung pengawet buatan',
                    'Bisa bertahan lama jika disimpan di kulkas'
                ],
                'category_name' => 'Kuliner'
            ],
            // Produk Fashion
            [
                'name' => 'Kemeja Batik Premium',
                'description' => 'Kemeja batik tulis asli dari Solo dengan motif tradisional yang indah dan kualitas terbaik.',
                'price' => 180000,
                'stock' => 40,
                'weight' => 300,
                'image' => 'products/kemeja-batik.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Katun primisima',
                    'Jenis Kelamin' => 'Pria',
                    'Ukuran' => 'S, M, L, XL, XXL',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Katun primisima',
                'size' => 'S-XXL',
                'color' => 'Coklat, Biru, Merah',
                'brand' => 'Batik Heritage',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak ada',
                'min_order' => 1,
                'ready_stock' => 40,
                'features' => [
                    'Batik tulis asli',
                    'Motif tradisional',
                    'Bahan nyaman dan adem',
                    'Tahan lama dan tidak mudah luntur'
                ],
                'category_name' => 'Fashion'
            ],
            [
                'name' => 'Rok Kain Tenun',
                'description' => 'Rok cantik dari kain tenun ikat asli dari NTT, dengan motif tradisional yang unik dan elegan.',
                'price' => 120000,
                'stock' => 30,
                'weight' => 250,
                'image' => 'products/rok-tenun-ikat.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Kain tenun ikat',
                    'Jenis Kelamin' => 'Wanita',
                    'Ukuran' => 'S, M, L',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Kain tenun ikat',
                'size' => 'S-L',
                'color' => 'Berbagai warna tradisional',
                'brand' => 'Tribal Fashion',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak ada',
                'min_order' => 1,
                'ready_stock' => 30,
                'features' => [
                    'Kain asli tenun ikat',
                    'Motif unik dan tradisional',
                    'Tampilan elegan dan khas nusantara',
                    'Dukungan produk UKM lokal'
                ],
                'category_name' => 'Fashion'
            ],
            [
                'name' => 'Jas Koko Modern',
                'description' => 'Jas koko dengan desain modern yang cocok untuk acara formal atau harian dengan sentuhan etnik.',
                'price' => 220000,
                'stock' => 25,
                'weight' => 400,
                'image' => 'products/jas-koko-modern.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Wool premium',
                    'Jenis Kelamin' => 'Pria',
                    'Ukuran' => 'S, M, L, XL, XXL',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Wool premium',
                'size' => 'S-XXL',
                'color' => 'Hitam, Putih, Coklat',
                'brand' => 'Etnic Modern',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi jahitan 30 hari',
                'min_order' => 1,
                'ready_stock' => 25,
                'features' => [
                    'Desain modern dengan sentuhan etnik',
                    'Bahan berkualitas tinggi',
                    'Kenyamanan maksimal',
                    'Cocok untuk berbagai acara'
                ],
                'category_name' => 'Fashion'
            ],
            // Produk Kerajinan Tangan
            [
                'name' => 'Anyaman Bambu Multi Fungsi',
                'description' => 'Kerajinan anyaman bambu yang bisa digunakan sebagai keranjang penyimpanan, wadah buah, atau hiasan rumah.',
                'price' => 95000,
                'stock' => 35,
                'weight' => 800,
                'image' => 'products/anyaman-bambu.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Bambu pilihan',
                    'Dimensi' => '25 x 20 x 15 cm',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Bambu',
                'size' => '25 x 20 x 15 cm',
                'color' => 'Coklat alami',
                'brand' => 'Kerajinan Bambu Nusantara',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi produk 7 hari',
                'min_order' => 1,
                'ready_stock' => 35,
                'features' => [
                    'Anyaman halus dan kuat',
                    'Bahan alami dan tahan lama',
                    'Bebas bahan kimia berbahaya',
                    'Dukungan produk UKM lokal'
                ],
                'category_name' => 'Kerajinan Tangan'
            ],
            [
                'name' => 'Lukisan Khas Toraja',
                'description' => 'Lukisan khas Toraja dengan motif tradisional yang unik dan bercerita tentang kebudayaan Sulawesi Selatan.',
                'price' => 350000,
                'stock' => 15,
                'weight' => 1200,
                'image' => 'products/lukisan-toraja.jpg',
                'status' => 'active',
                'specifications' => [
                    'Media' => 'Kanvas dan cat minyak',
                    'Dimensi' => '60 x 40 cm',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Kanvas dan cat minyak',
                'size' => '60 x 40 cm',
                'color' => 'Merah, hitam, kuning',
                'brand' => 'Seni Toraja',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi produk 7 hari',
                'min_order' => 1,
                'ready_stock' => 15,
                'features' => [
                    'Karya seni asli',
                    'Motif khas Toraja',
                    'Cerita budaya lokal',
                    'Kualitas tinggi'
                ],
                'category_name' => 'Kerajinan Tangan'
            ],
            [
                'name' => 'Kerajinan dari Tanah Liat',
                'description' => 'Pot tanaman dari tanah liat yang unik dan menarik untuk dekorasi rumah atau taman.',
                'price' => 75000,
                'stock' => 50,
                'weight' => 600,
                'image' => 'products/pot-tanah-liat.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Tanah liat asli',
                    'Dimensi' => 'Diameter 15 cm, Tinggi 12 cm',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Tanah liat',
                'size' => '15 cm diameter',
                'color' => 'Coklat merah',
                'brand' => 'Keramik Nusantara',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi produk 7 hari',
                'min_order' => 1,
                'ready_stock' => 50,
                'features' => [
                    'Bahan alami',
                    'Desain unik',
                    'Membantu penyerapan air',
                    'Dukungan produk UKM lokal'
                ],
                'category_name' => 'Kerajinan Tangan'
            ],
            // Produk Berkebun
            [
                'name' => 'Pupuk Organik Kompos',
                'description' => 'Pupuk organik kompos berkualitas tinggi yang ramah lingkungan untuk tanaman hias dan sayur.',
                'price' => 45000,
                'stock' => 80,
                'weight' => 1000,
                'image' => 'products/pupuk-kompos.jpg',
                'status' => 'active',
                'specifications' => [
                    'Netto' => '1 kg',
                    'Kandungan' => 'Humus, mikroorganisme bermanfaat',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Sisa organik terkompos',
                'size' => '1 kg',
                'color' => 'Coklat gelap',
                'brand' => 'Tani Organik',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak berlaku',
                'min_order' => 1,
                'ready_stock' => 80,
                'features' => [
                    'Ramah lingkungan',
                    'Meningkatkan kesuburan tanah',
                    'Diperkaya mikroorganisme',
                    'Aman untuk tanaman'
                ],
                'category_name' => 'Berkebun'
            ],
            [
                'name' => 'Set Alat Berkebun',
                'description' => 'Set lengkap alat berkebun terdiri dari cangkul kecil, sekop, penyiraman, dan sarung tangan.',
                'price' => 120000,
                'stock' => 45,
                'weight' => 1200,
                'image' => 'products/set-alat-berkebun.jpg',
                'status' => 'active',
                'specifications' => [
                    'Komponen' => 'Cangkul, sekop, penyiram, sarung tangan',
                    'Bahan' => 'Besi dan plastik berkualitas',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Besi dan plastik',
                'size' => 'Ukuran standar',
                'color' => 'Biru dan hitam',
                'brand' => 'Taman Sejahtera',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi produk 6 bulan',
                'min_order' => 1,
                'ready_stock' => 45,
                'features' => [
                    'Set lengkap',
                    'Bahan kuat dan tahan karat',
                    'Ergonomis dan nyaman digunakan',
                    'Cocok untuk pemula dan profesional'
                ],
                'category_name' => 'Berkebun'
            ],
            [
                'name' => 'Bibit Tomat Cherry',
                'description' => 'Bibit tomat cherry berkualitas tinggi yang mudah dirawat dan cepat berbuah.',
                'price' => 15000,
                'stock' => 100,
                'weight' => 100,
                'image' => 'products/bibit-tomat-cherry.jpg',
                'status' => 'active',
                'specifications' => [
                    'Usia' => '14 hari',
                    'Tinggi' => '5-8 cm',
                    'Jenis' => 'Tomat cherry',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Tanaman tomat',
                'size' => '5-8 cm',
                'color' => 'Hijau segar',
                'brand' => 'Taman Tropis',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak berlaku',
                'min_order' => 1,
                'ready_stock' => 100,
                'features' => [
                    'Bibit sehat dan kuat',
                    'Cepat berbuah',
                    'Mudah dirawat',
                    'Buah manis dan lezat'
                ],
                'category_name' => 'Berkebun'
            ],
            // Produk Kesehatan
            [
                'name' => 'Madu Murni Asli',
                'description' => 'Madu murni asli dari lebah hutan yang dikumpulkan langsung dari hutan di Indonesia.',
                'price' => 80000,
                'stock' => 60,
                'weight' => 500,
                'image' => 'products/madu-murni.jpg',
                'status' => 'active',
                'specifications' => [
                    'Netto' => '500 gram',
                    'Jenis' => 'Madu hutan asli',
                    'Kadaluarsa' => '24 bulan',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Madu alami',
                'size' => '500 gram',
                'color' => 'Kuning keemasan',
                'brand' => 'Alam Nusantara',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak berlaku',
                'min_order' => 1,
                'ready_stock' => 60,
                'features' => [
                    'Murni 100% dari lebah hutan',
                    'Tidak melalui proses pemanasan',
                    'Kaya akan antioksidan',
                    'Khasiat alami'
                ],
                'category_name' => 'Kesehatan'
            ],
            [
                'name' => 'Minyak Kayu Putih',
                'description' => 'Minyak kayu putih asli Indonesia yang berkhasiat untuk menghangatkan tubuh dan meredakan nyeri otot.',
                'price' => 25000,
                'stock' => 120,
                'weight' => 100,
                'image' => 'products/minyak-kayu-putih.jpg',
                'status' => 'active',
                'specifications' => [
                    'Netto' => '100 ml',
                    'Kandungan' => 'Eucalyptus oil 100%',
                    'Kadaluarsa' => '36 bulan',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Minyak eucalyptus',
                'size' => '100 ml',
                'color' => 'Bening',
                'brand' => 'Herba Nusantara',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak berlaku',
                'min_order' => 1,
                'ready_stock' => 120,
                'features' => [
                    'Kandungan 100% eucalyptus',
                    'Aman untuk semua usia',
                    'Tidak mengandung bahan kimia',
                    'Khasiat alami'
                ],
                'category_name' => 'Kesehatan'
            ],
            [
                'name' => 'Jahe Merah Organik',
                'description' => 'Jahe merah organik kualitas terbaik dari petani lokal, bebas bahan kimia dan segar.',
                'price' => 35000,
                'stock' => 70,
                'weight' => 250,
                'image' => 'products/jahe-merah.jpg',
                'status' => 'active',
                'specifications' => [
                    'Netto' => '250 gram',
                    'Jenis' => 'Jahe merah organik',
                    'Kadaluarsa' => '14 hari',
                    'Penyimpanan' => 'Simpan di kulkas',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Jahe merah organik',
                'size' => '250 gram',
                'color' => 'Merah muda',
                'brand' => 'Tani Organik',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak berlaku',
                'min_order' => 1,
                'ready_stock' => 70,
                'features' => [
                    'Jahe merah organik',
                    'Bebas pestisida',
                    'Segar dan berkualitas',
                    'Khasiat tinggi'
                ],
                'category_name' => 'Kesehatan'
            ],
            // Produk Mainan
            [
                'name' => 'Boneka Kayu Edukatif',
                'description' => 'Boneka kayu edukatif untuk anak-anak yang aman dan mendukung perkembangan kreativitas.',
                'price' => 65000,
                'stock' => 40,
                'weight' => 300,
                'image' => 'products/boneka-kayu.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Kayu pinus',
                    'Ukuran' => '20 cm',
                    'Umur' => '3 tahun ke atas',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Kayu pinus',
                'size' => '20 cm',
                'color' => 'Berbagai warna',
                'brand' => 'Mainan Eduka',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi produk 30 hari',
                'min_order' => 1,
                'ready_stock' => 40,
                'features' => [
                    'Aman untuk anak',
                    'Bebas bahan kimia berbahaya',
                    'Mendukung perkembangan kreativitas',
                    'Dibuat oleh UKM lokal'
                ],
                'category_name' => 'Mainan'
            ],
            [
                'name' => 'Puzzle Kayu Anak',
                'description' => 'Puzzle kayu edukatif untuk anak usia 3-8 tahun, membantu melatih motorik dan kognitif.',
                'price' => 45000,
                'stock' => 60,
                'weight' => 250,
                'image' => 'products/puzzle-kayu-anak.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Kayu solid',
                    'Jumlah' => '9 keping',
                    'Umur' => '3-8 tahun',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Kayu solid',
                'size' => '20 x 20 cm',
                'color' => 'Berbagai warna cerah',
                'brand' => 'Fun Edukasi',
                'origin' => 'Indonesia',
                    'warranty' => 'Garansi produk 30 hari',
                'min_order' => 1,
                'ready_stock' => 60,
                'features' => [
                    'Melatih keterampilan anak',
                    'Bahan aman dan kuat',
                    'Didesain edukatif',
                    'Melatih konsentrasi'
                ],
                'category_name' => 'Mainan'
            ],
            [
                'name' => 'Robot Mainan dari Botol Bekas',
                'description' => 'Mainan robot unik yang terbuat dari botol bekas plastik, mengajarkan konsep daur ulang dan kreativitas.',
                'price' => 30000,
                'stock' => 80,
                'weight' => 200,
                'image' => 'products/robot-botol-bekas.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Botol plastik bekas',
                    'Ukuran' => '15 cm',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Botol plastik bekas',
                'size' => '15 cm',
                'color' => 'Berbagai warna',
                'brand' => 'Kreasi Ramah Bumi',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi produk 30 hari',
                'min_order' => 1,
                'ready_stock' => 80,
                'features' => [
                    'Mainan daur ulang ramah lingkungan',
                    'Mengajarkan konsep daur ulang',
                    'Kreatif dan imajinatif',
                    'Aman untuk anak'
                ],
                'category_name' => 'Mainan'
            ],
            // Produk Hampers
            [
                'name' => 'Hamper Lebaran Spesial',
                'description' => 'Paket hamper Lebaran premium yang berisi berbagai makanan khas dan produk spesial.',
                'price' => 250000,
                'stock' => 25,
                'weight' => 1500,
                'image' => 'products/hamper-lebaran.jpg',
                'status' => 'active',
                'specifications' => [
                    'Isi' => 'Kue kering, sirup, kacang, dll',
                    'Kemasan' => 'Box eksklusif',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Makanan dan kemasan',
                'size' => 'Box 30x20x15 cm',
                'color' => 'Merah dan emas',
                'brand' => 'Hamper Spesial',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak berlaku',
                'min_order' => 1,
                'ready_stock' => 25,
                'features' => [
                    'Paket lengkap untuk Lebaran',
                    'Kemasan eksklusif',
                    'Isi premium',
                    'Cocok untuk hadiah'
                ],
                'category_name' => 'Hampers'
            ],
            [
                'name' => 'Hamper Ulang Tahun Anak',
                'description' => 'Paket hamper ulang tahun anak yang berisi mainan, snack, dan perlengkapan seru.',
                'price' => 150000,
                'stock' => 35,
                'weight' => 1000,
                'image' => 'products/hamper-ultah-anak.jpg',
                'status' => 'active',
                'specifications' => [
                    'Isi' => 'Mainan, snack, dekorasi',
                    'Kemasan' => 'Keranjang eksklusif',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Mainan dan snack',
                'size' => 'Keranjang 25x20x10 cm',
                'color' => 'Berwarna-warni',
                'brand' => 'Happy Birthday Box',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak berlaku',
                'min_order' => 1,
                'ready_stock' => 35,
                'features' => [
                    'Paket lengkap untuk anak',
                    'Tema ulang tahun',
                    'Mainan edukatif dan snack sehat',
                    'Kemasan menarik'
                ],
                'category_name' => 'Hampers'
            ],
            [
                'name' => 'Hamper Natal dan Tahun Baru',
                'description' => 'Paket hamper Natal dan Tahun Baru dengan tema khas dan isi spesial untuk merayakan.',
                'price' => 300000,
                'stock' => 20,
                'weight' => 2000,
                'image' => 'products/hamper-natal.jpg',
                'status' => 'active',
                'specifications' => [
                    'Isi' => 'Makanan, minuman, hiasan',
                    'Kemasan' => 'Box tema natal',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Makanan, minuman, dan dekorasi',
                'size' => 'Box 35x25x20 cm',
                'color' => 'Merah dan hijau',
                'brand' => 'Seasonal Hamper',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak berlaku',
                'min_order' => 1,
                'ready_stock' => 20,
                'features' => [
                    'Tema Natal dan Tahun Baru',
                    'Isi premium dan lengkap',
                    'Kemasan eksklusif',
                    'Cocok untuk hadiah spesial'
                ],
                'category_name' => 'Hampers'
            ],
            // Produk Elektronik
            [
                'name' => 'Speaker Bluetooth Portabel',
                'description' => 'Speaker portabel dengan koneksi Bluetooth, suara jernih, dan baterai tahan lama.',
                'price' => 150000,
                'stock' => 45,
                'weight' => 500,
                'image' => 'products/speaker-bluetooth.jpg',
                'status' => 'active',
                'specifications' => [
                    'Konektivitas' => 'Bluetooth 5.0',
                    'Daya' => '10W',
                    'Baterai' => '12 jam pemutaran',
                    'Negara Asal' => 'China'
                ],
                'material' => 'Plastik dan logam',
                'size' => '15 x 8 x 8 cm',
                'color' => 'Hitam, Putih, Merah',
                'brand' => 'SoundPro',
                'origin' => 'China',
                'warranty' => 'Garansi 1 tahun',
                'min_order' => 1,
                'ready_stock' => 45,
                'features' => [
                    'Suara jernih dan bass kuat',
                    'Konektivitas Bluetooth',
                    'Baterai tahan lama',
                    'Desain portabel'
                ],
                'category_name' => 'Elektronik'
            ],
            [
                'name' => 'Power Bank Premium',
                'description' => 'Power bank dengan kapasitas tinggi, desain elegan, dan teknologi pengisian cepat.',
                'price' => 120000,
                'stock' => 60,
                'weight' => 300,
                'image' => 'products/power-bank.jpg',
                'status' => 'active',
                'specifications' => [
                    'Kapasitas' => '10000 mAh',
                    'Output' => '5V/2.4A',
                    'Input' => 'Micro USB & Type C',
                    'Negara Asal' => 'China'
                ],
                'material' => 'Plastik ABS tahan lama',
                'size' => '14 x 7 x 1.5 cm',
                'color' => 'Hitam, Putih, Biru',
                'brand' => 'PowerMax',
                'origin' => 'China',
                'warranty' => 'Garansi 1 tahun',
                'min_order' => 1,
                'ready_stock' => 60,
                'features' => [
                    'Kapasitas besar',
                    'Pengisian cepat',
                    'Desain elegan',
                    'Tahan lama'
                ],
                'category_name' => 'Elektronik'
            ],
            // Produk Aksesoris
            [
                'name' => 'Gelang Silikon Unisex',
                'description' => 'Gelang silikon fashion yang nyaman dan cocok untuk pria maupun wanita.',
                'price' => 25000,
                'stock' => 100,
                'weight' => 10,
                'image' => 'products/gelang-silikon.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Silikon food grade',
                    'Ukuran' => '22 cm x 1.2 cm',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Silikon food grade',
                'size' => '22 cm x 1.2 cm',
                'color' => 'Berbagai warna',
                'brand' => 'FlexStyle',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak ada',
                'min_order' => 1,
                'ready_stock' => 100,
                'features' => [
                    'Ringan dan nyaman',
                    'Bahan aman dan tidak mengganggu kulit',
                    'Tahan air',
                    'Cocok untuk aktivitas sehari-hari'
                ],
                'category_name' => 'Aksesoris'
            ],
            [
                'name' => 'Dompet Minimalis Kulit',
                'description' => 'Dompet minimalis dari kulit asli dengan desain simpel namun elegan.',
                'price' => 75000,
                'stock' => 50,
                'weight' => 80,
                'image' => 'products/dompet-minimalis.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Kulit sapi asli',
                    'Dimensi' => '11 x 7 x 1.5 cm',
                    'Kapasitas' => '6 slot kartu, 1 kompartemen uang',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Kulit sapi asli',
                'size' => '11 x 7 x 1.5 cm',
                'color' => 'Hitam, Coklat, Merah',
                'brand' => 'LeatherCraft',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi 6 bulan',
                'min_order' => 1,
                'ready_stock' => 50,
                'features' => [
                    'Desain minimalis',
                    'Bahan kulit asli',
                    'Cocok untuk pria dan wanita',
                    'Fungsional dan elegan'
                ],
                'category_name' => 'Aksesoris'
            ],
            // Produk Kulit
            [
                'name' => 'Tas Selempang Kulit Asli',
                'description' => 'Tas selempang dari kulit asli dengan model simpel namun elegan untuk pria dan wanita.',
                'price' => 280000,
                'stock' => 25,
                'weight' => 400,
                'image' => 'products/tas-selempang-kulit.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Kulit sapi asli',
                    'Dimensi' => '25 x 20 x 8 cm',
                    'Kompartemen' => 'Utama, saku dalam',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Kulit sapi asli',
                'size' => '25 x 20 x 8 cm',
                'color' => 'Hitam, Coklat',
                'brand' => 'LeatherArt',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi 1 tahun',
                'min_order' => 1,
                'ready_stock' => 25,
                'features' => [
                    'Kulit asli premium',
                    'Desain simpel dan elegan',
                    'Banyak kompartemen',
                    'Tahan lama dan tidak mudah rusak'
                ],
                'category_name' => 'Kulit'
            ],
            [
                'name' => 'Ikat Pinggang Kulit Premium',
                'description' => 'Ikat pinggang dari kulit asli dengan gesper logam premium untuk penampilan elegan.',
                'price' => 120000,
                'stock' => 40,
                'weight' => 150,
                'image' => 'products/ikat-pinggang-kulit.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Kulit sapi asli',
                    'Panjang' => '110-130 cm',
                    'Lebar' => '3.5 cm',
                    'Gesper' => 'Besi anti karat',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Kulit sapi asli dan besi',
                'size' => '110-130 cm',
                'color' => 'Hitam, Coklat, Navy',
                'brand' => 'BeltStyle',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi 6 bulan',
                'min_order' => 1,
                'ready_stock' => 40,
                'features' => [
                    'Kulit asli kualitas tinggi',
                    'Gesper anti karat',
                    'Fleksibel dan kuat',
                    'Cocok untuk formal dan kasual'
                ],
                'category_name' => 'Kulit'
            ],
        ];

        // Distribute products among different sellers and categories
        foreach ($productsData as $index => $productData) {
            $seller = $sellers->get($index % $sellers->count());
            $categoryName = $productData['category_name'];
            $categoryId = $categories[$categoryName] ?? $categories['Fashion']; // fallback to Fashion if category not found
            
            unset($productData['category_name']); // remove temporary category name
            
            $productData['seller_id'] = $seller->id;
            $productData['category_id'] = $categoryId;
            
            Product::create($productData);
        }
        
        // Call ProductVariantSeeder after creating products
        $this->call(ProductVariantSeeder::class);
    }
}
