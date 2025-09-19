@extends('customer.kategori.base')

@section('category-title', 'Kuliner')
@section('category-description', 'Temukan berbagai produk kuliner menarik dari UMKM lokal')

@section('category-products')
  <div class="product-card">
    <img src="https://picsum.photos/seed/kuliner1/300/300" alt="Produk Kuliner" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Kue Tradisional Bali</h3>
      <p class="product-description">Kue tradisional khas Bali dengan rasa otentik dan bahan alami pilihan</p>
      <div class="product-price">Rp45.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kuliner2/300/300" alt="Produk Kuliner" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Kopi Arabica Gayo</h3>
      <p class="product-description">Kopi arabica premium dari pegunungan Gayo dengan aroma khas</p>
      <div class="product-price">Rp85.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kuliner3/300/300" alt="Produk Kuliner" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Madu Hutan Asli</h3>
      <p class="product-description">Madu murni dari hutan tropis dengan khasiat alami tinggi</p>
      <div class="product-price">Rp120.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kuliner4/300/300" alt="Produk Kuliner" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Keripik Tempe</h3>
      <p class="product-description">Keripik tempe renyah dengan bumbu tradisional khas Indonesia</p>
      <div class="product-price">Rp25.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kuliner5/300/300" alt="Produk Kuliner" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Minyak Kelapa Murni</h3>
      <p class="product-description">Minyak kelapa murni dengan proses pemanasan dingin</p>
      <div class="product-price">Rp65.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kuliner6/300/300" alt="Produk Kuliner" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Sambal Lombok</h3>
      <p class="product-description">Sambal pedas khas Lombok dengan cabai pilihan</p>
      <div class="product-price">Rp30.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kuliner7/300/300" alt="Produk Kuliner" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Teh Herbal</h3>
      <p class="product-description">Teh herbal dengan campuran rempah alami</p>
      <div class="product-price">Rp40.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kuliner8/300/300" alt="Produk Kuliner" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Kerupuk Udang</h3>
      <p class="product-description">Kerupuk udang renyah dengan rasa autentik</p>
      <div class="product-price">Rp20.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
@endsection

@section('page-1-products')
  [
    {name: "Kue Tradisional Bali", description: "Kue tradisional khas Bali dengan rasa otentik dan bahan alami pilihan", price: "Rp45.000", image: "https://picsum.photos/seed/kuliner1/300/300"},
    {name: "Kopi Arabica Gayo", description: "Kopi arabica premium dari pegunungan Gayo dengan aroma khas", price: "Rp85.000", image: "https://picsum.photos/seed/kuliner2/300/300"},
    {name: "Madu Hutan Asli", description: "Madu murni dari hutan tropis dengan khasiat alami tinggi", price: "Rp120.000", image: "https://picsum.photos/seed/kuliner3/300/300"},
    {name: "Keripik Tempe", description: "Keripik tempe renyah dengan bumbu tradisional khas Indonesia", price: "Rp25.000", image: "https://picsum.photos/seed/kuliner4/300/300"},
    {name: "Minyak Kelapa Murni", description: "Minyak kelapa murni dengan proses pemanasan dingin", price: "Rp65.000", image: "https://picsum.photos/seed/kuliner5/300/300"},
    {name: "Sambal Lombok", description: "Sambal pedas khas Lombok dengan cabai pilihan", price: "Rp30.000", image: "https://picsum.photos/seed/kuliner6/300/300"},
    {name: "Teh Herbal", description: "Teh herbal dengan campuran rempah alami", price: "Rp40.000", image: "https://picsum.photos/seed/kuliner7/300/300"},
    {name: "Kerupuk Udang", description: "Kerupuk udang renyah dengan rasa autentik", price: "Rp20.000", image: "https://picsum.photos/seed/kuliner8/300/300"}
  ]
@endsection

@section('page-2-products')
  [
    {name: "Kue Lumpur", description: "Kue lumpur dengan tekstur lembut dan rasa manis khas", price: "Rp35.000", image: "https://picsum.photos/seed/kuliner9/300/300"},
    {name: "Jahe Instan", description: "Jahe instan dengan kualitas premium", price: "Rp28.000", image: "https://picsum.photos/seed/kuliner10/300/300"},
    {name: "Dodol Kacang", description: "Dodol kacang dengan tekstur kenyal dan rasa manis", price: "Rp32.000", image: "https://picsum.photos/seed/kuliner11/300/300"},
    {name: "Kopi Robusta", description: "Kopi robusta dengan cita rasa khas dan aroma kuat", price: "Rp75.000", image: "https://picsum.photos/seed/kuliner12/300/300"},
    {name: "Keripik Singkong", description: "Keripik singkong renyah dengan berbagai varian rasa", price: "Rp22.000", image: "https://picsum.photos/seed/kuliner13/300/300"},
    {name: "Saus Tomat", description: "Saus tomat dengan bahan alami tanpa pengawet", price: "Rp25.000", image: "https://picsum.photos/seed/kuliner14/300/300"},
    {name: "Kue Putu", description: "Kue putu tradisional dengan gula merah asli", price: "Rp18.000", image: "https://picsum.photos/seed/kuliner15/300/300"},
    {name: "Teh Tarik", description: "Teh tarik khas Malaysia dengan rasa autentik", price: "Rp38.000", image: "https://picsum.photos/seed/kuliner16/300/300"}
  ]
@endsection

@section('page-3-products')
  [
    {name: "Kue Lapis", description: "Kue lapis dengan warna-warna cantik dan rasa manis", price: "Rp50.000", image: "https://picsum.photos/seed/kuliner17/300/300"},
    {name: "Brownies Kukus", description: "Brownies kukus dengan tekstur lembut dan coklat berkualitas", price: "Rp42.000", image: "https://picsum.photos/seed/kuliner18/300/300"},
    {name: "Kopi Luwak", description: "Kopi luwak premium dengan proses tradisional", price: "Rp250.000", image: "https://picsum.photos/seed/kuliner19/300/300"},
    {name: "Kerupuk Ikan", description: "Kerupuk ikan dengan rasa gurih dan renyah", price: "Rp28.000", image: "https://picsum.photos/seed/kuliner20/300/300"},
    {name: "Sambal Terasi", description: "Sambal terasi dengan rasa pedas dan gurih khas", price: "Rp35.000", image: "https://picsum.photos/seed/kuliner21/300/300"},
    {name: "Minuman Keras Fermentasi", description: "Minuman keras alami hasil fermentasi tradisional", price: "Rp180.000", image: "https://picsum.photos/seed/kuliner22/300/300"},
    {name: "Kue Cubit", description: "Kue cubit dengan berbagai topping menarik", price: "Rp15.000", image: "https://picsum.photos/seed/kuliner23/300/300"},
    {name: "Keripik Pisang", description: "Keripik pisang renyah dengan rasa manis alami", price: "Rp24.000", image: "https://picsum.photos/seed/kuliner24/300/300"}
  ]
@endsection

@section('page-4-products')
  [
    {name: "Kue Putri Salju", description: "Kue putri salju dengan taburan gula halus", price: "Rp38.000", image: "https://picsum.photos/seed/kuliner25/300/300"},
    {name: "Teh Celup", description: "Teh celup dengan berbagai varian rasa", price: "Rp12.000", image: "https://picsum.photos/seed/kuliner26/300/300"},
    {name: "Kopi Tubruk", description: "Kopi tubruk khas Jawa dengan cita rasa kuat", price: "Rp22.000", image: "https://picsum.photos/seed/kuliner27/300/300"},
    {name: "Keripik Kentang", description: "Keripik kentang renyah dengan bumbu racikan khusus", price: "Rp26.000", image: "https://picsum.photos/seed/kuliner28/300/300"},
    {name: "Saus Sambal", description: "Saus sambal dengan tingkat kepedasan yang bervariasi", price: "Rp23.000", image: "https://picsum.photos/seed/kuliner29/300/300"},
    {name: "Kue Nastar", description: "Kue nastar dengan selai nanas asli", price: "Rp48.000", image: "https://picsum.photos/seed/kuliner30/300/300"},
    {name: "Minyak wijen", description: "Minyak wijen dengan aroma khas dan kualitas premium", price: "Rp55.000", image: "https://picsum.photos/seed/kuliner31/300/300"},
    {name: "Kerupuk Mie", description: "Kerupuk mie dengan rasa gurih dan tekstur renyah", price: "Rp20.000", image: "https://picsum.photos/seed/kuliner32/300/300"}
  ]
@endsection

@section('page-5-products')
  [
    {name: "Kue Kastengel", description: "Kue kastengel dengan keju berkualitas tinggi", price: "Rp45.000", image: "https://picsum.photos/seed/kuliner33/300/300"},
    {name: "Teh Botol", description: "Teh botol dengan rasa tradisional", price: "Rp18.000", image: "https://picsum.photos/seed/kuliner34/300/300"},
    {name: "Kopi Capuccino", description: "Kopi cappuccino dengan susu segar", price: "Rp30.000", image: "https://picsum.photos/seed/kuliner35/300/300"},
    {name: "Keripik Bayam", description: "Keripik bayam renyah dengan nutrisi tinggi", price: "Rp25.000", image: "https://picsum.photos/seed/kuliner36/300/300"},
    {name: "Sambal Matah", description: "Sambal matah khas Bali dengan bahan segar", price: "Rp32.000", image: "https://picsum.photos/seed/kuliner37/300/300"},
    {name: "Kue Lumpur Surabaya", description: "Kue lumpur khas Surabaya dengan tekstur lembut", price: "Rp35.000", image: "https://picsum.photos/seed/kuliner38/300/300"},
    {name: "Minuman Jahe", description: "Minuman jahe hangat dengan madu alami", price: "Rp28.000", image: "https://picsum.photos/seed/kuliner39/300/300"},
    {name: "Kerupuk Tempe", description: "Kerupuk tempe dengan rasa gurih khas", price: "Rp18.000", image: "https://picsum.photos/seed/kuliner40/300/300"}
  ]
@endsection