@extends('customer.kategori.base')

@section('category-title', 'Mainan')
@section('category-description', 'Temukan berbagai produk mainan edukatif dari UMKM lokal')

@section('category-products')
  <div class="product-card">
    <img src="https://picsum.photos/seed/mainan1/300/300" alt="Produk Mainan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Puzzle Kayu Anak</h3>
      <p class="product-description">Puzzle kayu dengan bentuk hewan untuk melatih motorik anak</p>
      <div class="product-price">Rp65.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/mainan2/300/300" alt="Produk Mainan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Boneka Wayang</h3>
      <p class="product-description">Boneka wayang dengan bahan kain halus dan motif tradisional</p>
      <div class="product-price">Rp45.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/mainan3/300/300" alt="Produk Mainan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Mobil Kayu</h3>
      <p class="product-description">Mobil kayu dengan roda kayu dan cat non-toxic</p>
      <div class="product-price">Rp55.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/mainan4/300/300" alt="Produk Mainan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Batu Cincin Anak</h3>
      <p class="product-description">Batu cincin anak dengan warna-warna cerah</p>
      <div class="product-price">Rp35.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/mainan5/300/300" alt="Produk Mainan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Kartu Edukasi</h3>
      <p class="product-description">Kartu edukasi dengan gambar dan huruf untuk belajar</p>
      <div class="product-price">Rp25.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/mainan6/300/300" alt="Produk Mainan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Bambu Melody</h3>
      <p class="product-description">Mainan bambu melody dengan suara merdu</p>
      <div class="product-price">Rp75.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/mainan7/300/300" alt="Produk Mainan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Balok Bangun</h3>
      <p class="product-description">Balok bangun dengan berbagai bentuk geometris</p>
      <div class="product-price">Rp85.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/mainan8/300/300" alt="Produk Mainan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Wayang Kulit Mini</h3>
      <p class="product-description">Wayang kulit mini dengan karakter wayang klasik</p>
      <div class="product-price">Rp120.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
@endsection

@section('page-1-products')
  [
    {name: "Puzzle Kayu Anak", description: "Puzzle kayu dengan bentuk hewan untuk melatih motorik anak", price: "Rp65.000", image: "https://picsum.photos/seed/mainan1/300/300"},
    {name: "Boneka Wayang", description: "Boneka wayang dengan bahan kain halus dan motif tradisional", price: "Rp45.000", image: "https://picsum.photos/seed/mainan2/300/300"},
    {name: "Mobil Kayu", description: "Mobil kayu dengan roda kayu dan cat non-toxic", price: "Rp55.000", image: "https://picsum.photos/seed/mainan3/300/300"},
    {name: "Batu Cincin Anak", description: "Batu cincin anak dengan warna-warna cerah", price: "Rp35.000", image: "https://picsum.photos/seed/mainan4/300/300"},
    {name: "Kartu Edukasi", description: "Kartu edukasi dengan gambar dan huruf untuk belajar", price: "Rp25.000", image: "https://picsum.photos/seed/mainan5/300/300"},
    {name: "Bambu Melody", description: "Mainan bambu melody dengan suara merdu", price: "Rp75.000", image: "https://picsum.photos/seed/mainan6/300/300"},
    {name: "Balok Bangun", description: "Balok bangun dengan berbagai bentuk geometris", price: "Rp85.000", image: "https://picsum.photos/seed/mainan7/300/300"},
    {name: "Wayang Kulit Mini", description: "Wayang kulit mini dengan karakter wayang klasik", price: "Rp120.000", image: "https://picsum.photos/seed/mainan8/300/300"}
  ]
@endsection

@section('page-2-products')
  [
    {name: "Robot Kayu", description: "Robot kayu dengan bentuk lucu dan cat cerah", price: "Rp95.000", image: "https://picsum.photos/seed/mainan9/300/300"},
    {name: "Bola Tenis Kayu", description: "Bola tenis kayu dengan tekstur halus", price: "Rp28.000", image: "https://picsum.photos/seed/mainan10/300/300"},
    {name: "Gitar Mini", description: "Gitar mini dengan senar nyaman dan suara jernih", price: "Rp135.000", image: "https://picsum.photos/seed/mainan11/300/300"},
    {name: "Kuda Kayu", description: "Kuda kayu dengan goyangan lembut", price: "Rp110.000", image: "https://picsum.photos/seed/mainan12/300/300"},
    {name: "Buku Cerita", description: "Buku cerita dengan ilustrasi indah dan moral", price: "Rp42.000", image: "https://picsum.photos/seed/mainan13/300/300"},
    {name: "Drum Mini", description: "Drum mini dengan suara merdu dan cat warna-warni", price: "Rp85.000", image: "https://picsum.photos/seed/mainan14/300/300"},
    {name: "Bola Dunia", description: "Bola dunia dengan peta benua dan warna cerah", price: "Rp58.000", image: "https://picsum.photos/seed/mainan15/300/300"},
    {name: "Boneka Tradisional", description: "Boneka tradisional dengan pakaian adat", price: "Rp72.000", image: "https://picsum.photos/seed/mainan16/300/300"}
  ]
@endsection

@section('page-3-products')
  [
    {name: "Perahu Kayu", description: "Perahu kayu dengan detail tinggi dan cat tahan air", price: "Rp68.000", image: "https://picsum.photos/seed/mainan17/300/300"},
    {name: "Alat Musik Angin", description: "Alat musik angin dengan suara merdu", price: "Rp45.000", image: "https://picsum.photos/seed/mainan18/300/300"},
    {name: "Kartu Domino", description: "Kartu domino dengan gambar lucu", price: "Rp32.000", image: "https://picsum.photos/seed/mainan19/300/300"},
    {name: "Bebek Kayu", description: "Bebek kayu dengan suara bebek asli", price: "Rp52.000", image: "https://picsum.photos/seed/mainan20/300/300"},
    {name: "Buku Mewarnai", description: "Buku mewarnai dengan gambar karakter lucu", price: "Rp25.000", image: "https://picsum.photos/seed/mainan21/300/300"},
    {name: "Catur Kayu", description: "Catur kayu dengan kotak dan bidak berkualitas", price: "Rp95.000", image: "https://picsum.photos/seed/mainan22/300/300"},
    {name: "Kereta Api Kayu", description: "Kereta api kayu dengan gerbong dan figur", price: "Rp145.000", image: "https://picsum.photos/seed/mainan23/300/300"},
    {name: "Bola Sepak Kayu", description: "Bola sepak kayu dengan tekstur ringan", price: "Rp38.000", image: "https://picsum.photos/seed/mainan24/300/300"}
  ]
@endsection

@section('page-4-products')
  [
    {name: "Kucing Kayu", description: "Kucing kayu dengan suara meong dan gerakan kepala", price: "Rp62.000", image: "https://picsum.photos/seed/mainan25/300/300"},
    {name: "Alat Musik Gesek", description: "Alat musik gesek dengan suara melodius", price: "Rp78.000", image: "https://picsum.photos/seed/mainan26/300/300"},
    {name: "Kartu Uno", description: "Kartu uno dengan gambar cerah dan kertas tebal", price: "Rp28.000", image: "https://picsum.photos/seed/mainan27/300/300"},
    {name: "Anjing Kayu", description: "Anjing kayu dengan suara gonggongan asli", price: "Rp56.000", image: "https://picsum.photos/seed/mainan28/300/300"},
    {name: "Buku Teka-Teki", description: "Buku teka-teki dengan soal menarik", price: "Rp35.000", image: "https://picsum.photos/seed/mainan29/300/300"},
    {name: "Dadu Kayu", description: "Dadu kayu dengan angka jelas dan cat tahan lama", price: "Rp22.000", image: "https://picsum.photos/seed/mainan30/300/300"},
    {name: "Kapal Kayu", description: "Kapal kayu dengan layar dan pelaut figur", price: "Rp88.000", image: "https://picsum.photos/seed/mainan31/300/300"},
    {name: "Bola Basket Kayu", description: "Bola basket kayu dengan tekstur ringan", price: "Rp40.000", image: "https://picsum.photos/seed/mainan32/300/300"}
  ]
@endsection

@section('page-5-products')
  [
    {name: "Burung Kayu", description: "Burung kayu dengan suara kicau dan gerakan sayap", price: "Rp66.000", image: "https://picsum.photos/seed/mainan33/300/300"},
    {name: "Alat Musik Petik", description: "Alat musik petik dengan suara harmonis", price: "Rp72.000", image: "https://picsum.photos/seed/mainan34/300/300"},
    {name: "Kartu Memory", description: "Kartu memory dengan gambar lucu dan kertas tebal", price: "Rp30.000", image: "https://picsum.photos/seed/mainan35/300/300"},
    {name: "Gajah Kayu", description: "Gajah kayu dengan suara terompet dan gerakan kepala", price: "Rp76.000", image: "https://picsum.photos/seed/mainan36/300/300"},
    {name: "Buku Cerita Bergambar", description: "Buku cerita bergambar dengan ilustrasi indah", price: "Rp38.000", image: "https://picsum.photos/seed/mainan37/300/300"},
    {name: "Yoyo Kayu", description: "Yoyo kayu dengan tali kuat dan gerakan halus", price: "Rp26.000", image: "https://picsum.photos/seed/mainan38/300/300"},
    {name: "Pesawat Kayu", description: "Pesawat kayu dengan detail tinggi dan cat cerah", price: "Rp82.000", image: "https://picsum.photos/seed/mainan39/300/300"},
    {name: "Bola Voli Kayu", description: "Bola voli kayu dengan tekstur ringan", price: "Rp36.000", image: "https://picsum.photos/seed/mainan40/300/300"}
  ]
@endsection