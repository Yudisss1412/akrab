@extends('customer.kategori.base')

@section('category-title', 'Produk Berkebun')
@section('category-description', 'Temukan berbagai produk berkebun alami dari UMKM lokal')

@section('category-products')
  <div class="product-card">
    <img src="https://picsum.photos/seed/berkebun1/300/300" alt="Produk Berkebun" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Pupuk Organik Cair</h3>
      <p class="product-description">Pupuk organik cair dengan bahan alami untuk tanaman sayur</p>
      <div class="product-price">Rp45.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/berkebun2/300/300" alt="Produk Berkebun" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Bibit Tanaman Hias</h3>
      <p class="product-description">Bibit tanaman hias dengan akar sehat dan daun subur</p>
      <div class="product-price">Rp25.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/berkebun3/300/300" alt="Produk Berkebun" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Media Tanam Coco Peat</h3>
      <p class="product-description">Media tanam coco peat dengan kualitas tinggi</p>
      <div class="product-price">Rp35.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/berkebun4/300/300" alt="Produk Berkebun" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Benih Cabai</h3>
      <p class="product-description">Benih cabai berkualitas dengan tingkat kecambah tinggi</p>
      <div class="product-price">Rp15.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/berkebun5/300/300" alt="Produk Berkebun" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Kompos Organik</h3>
      <p class="product-description">Kompos organik dengan proses fermentasi alami</p>
      <div class="product-price">Rp40.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/berkebun6/300/300" alt="Produk Berkebun" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Alat Penyiram Otomatis</h3>
      <p class="product-description">Alat penyiram otomatis untuk kebun rumah</p>
      <div class="product-price">Rp120.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/berkebun7/300/300" alt="Produk Berkebun" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Bibit Tomat</h3>
      <p class="product-description">Bibit tomat dengan varietas unggul</p>
      <div class="product-price">Rp20.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/berkebun8/300/300" alt="Produk Berkebun" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Pestisida Nabati</h3>
      <p class="product-description">Pestisida nabati ramah lingkungan</p>
      <div class="product-price">Rp30.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
@endsection

@section('page-1-products')
  [
    {name: "Pupuk Organik Cair", description: "Pupuk organik cair dengan bahan alami untuk tanaman sayur", price: "Rp45.000", image: "https://picsum.photos/seed/berkebun1/300/300"},
    {name: "Bibit Tanaman Hias", description: "Bibit tanaman hias dengan akar sehat dan daun subur", price: "Rp25.000", image: "https://picsum.photos/seed/berkebun2/300/300"},
    {name: "Media Tanam Coco Peat", description: "Media tanam coco peat dengan kualitas tinggi", price: "Rp35.000", image: "https://picsum.photos/seed/berkebun3/300/300"},
    {name: "Benih Cabai", description: "Benih cabai berkualitas dengan tingkat kecambah tinggi", price: "Rp15.000", image: "https://picsum.photos/seed/berkebun4/300/300"},
    {name: "Kompos Organik", description: "Kompos organik dengan proses fermentasi alami", price: "Rp40.000", image: "https://picsum.photos/seed/berkebun5/300/300"},
    {name: "Alat Penyiram Otomatis", description: "Alat penyiram otomatis untuk kebun rumah", price: "Rp120.000", image: "https://picsum.photos/seed/berkebun6/300/300"},
    {name: "Bibit Tomat", description: "Bibit tomat dengan varietas unggul", price: "Rp20.000", image: "https://picsum.photos/seed/berkebun7/300/300"},
    {name: "Pestisida Nabati", description: "Pestisida nabati ramah lingkungan", price: "Rp30.000", image: "https://picsum.photos/seed/berkebun8/300/300"}
  ]
@endsection

@section('page-2-products')
  [
    {name: "Bibit Kangkung", description: "Bibit kangkung dengan akar kuat dan daun subur", price: "Rp12.000", image: "https://picsum.photos/seed/berkebun9/300/300"},
    {name: "Pupuk Kandang", description: "Pupuk kandang dengan proses pembusukan alami", price: "Rp35.000", image: "https://picsum.photos/seed/berkebun10/300/300"},
    {name: "Benih Bayam", description: "Benih bayam berkualitas dengan tingkat kecambah tinggi", price: "Rp10.000", image: "https://picsum.photos/seed/berkebun11/300/300"},
    {name: "Media Tanam Arang", description: "Media tanam arang dengan pori-pori optimal", price: "Rp28.000", image: "https://picsum.photos/seed/berkebun12/300/300"},
    {name: "Bibit Mawar", description: "Bibit mawar dengan bunga berwarna cerah", price: "Rp32.000", image: "https://picsum.photos/seed/berkebun13/300/300"},
    {name: "Pupuk NPK", description: "Pupuk NPK dengan kandungan seimbang", price: "Rp22.000", image: "https://picsum.photos/seed/berkebun14/300/300"},
    {name: "Benih Kacang Panjang", description: "Benih kacang panjang dengan hasil panen tinggi", price: "Rp18.000", image: "https://picsum.photos/seed/berkebun15/300/300"},
    {name: "Alat Ukur pH", description: "Alat ukur pH tanah digital", price: "Rp85.000", image: "https://picsum.photos/seed/berkebun16/300/300"}
  ]
@endsection

@section('page-3-products')
  [
    {name: "Bibit Pepaya", description: "Bibit pepaya dengan buah manis dan daging tebal", price: "Rp28.000", image: "https://picsum.photos/seed/berkebun17/300/300"},
    {name: "Pupuk Hayati", description: "Pupuk hayati dengan bakteri pengurai organik", price: "Rp50.000", image: "https://picsum.photos/seed/berkebun18/300/300"},
    {name: "Benih Terong", description: "Benih terong unggul dengan hasil panen melimpah", price: "Rp16.000", image: "https://picsum.photos/seed/berkebun19/300/300"},
    {name: "Media Tanam Vermikulit", description: "Media tanam vermiculite dengan kapasitas air tinggi", price: "Rp42.000", image: "https://picsum.photos/seed/berkebun20/300/300"},
    {name: "Bibit Lavender", description: "Bibit lavender dengan aroma harum khas", price: "Rp38.000", image: "https://picsum.photos/seed/berkebun21/300/300"},
    {name: "Pupuk Daun", description: "Pupuk daun dengan kandungan mikro nutrien lengkap", price: "Rp26.000", image: "https://picsum.photos/seed/berkebun22/300/300"},
    {name: "Benih Timun", description: "Benih timun hibrida dengan hasil panen berkualitas", price: "Rp20.000", image: "https://picsum.photos/seed/berkebun23/300/300"},
    {name: "Alat Semprot Manual", description: "Alat semprot manual dengan tekanan tinggi", price: "Rp65.000", image: "https://picsum.photos/seed/berkebun24/300/300"}
  ]
@endsection

@section('page-4-products')
  [
    {name: "Bibit Jeruk", description: "Bibit jeruk dengan buah manis dan vitamin C tinggi", price: "Rp45.000", image: "https://picsum.photos/seed/berkebun25/300/300"},
    {name: "Pupuk Mikro", description: "Pupuk mikro dengan unsur hara mikro lengkap", price: "Rp32.000", image: "https://picsum.photos/seed/berkebun26/300/300"},
    {name: "Benih Sawi", description: "Benih sawi dengan daun lebar dan tekstur renyah", price: "Rp14.000", image: "https://picsum.photos/seed/berkebun27/300/300"},
    {name: "Media Tanam Rockwool", description: "Media tanam rockwool dengan porositas optimal", price: "Rp38.000", image: "https://picsum.photos/seed/berkebun28/300/300"},
    {name: "Bibit Mint", description: "Bibit mint dengan aroma segar dan daun hijau", price: "Rp22.000", image: "https://picsum.photos/seed/berkebun29/300/300"},
    {name: "Pupuk Bokashi", description: "Pupuk bokashi dengan mikroorganisme aktif", price: "Rp28.000", image: "https://picsum.photos/seed/berkebun30/300/300"},
    {name: "Benih Wortel", description: "Benih wortel dengan bentuk lurus dan manis", price: "Rp16.000", image: "https://picsum.photos/seed/berkebun31/300/300"},
    {name: "Alat Cangkul", description: "Alat cangkul dengan mata baja tajam", price: "Rp95.000", image: "https://picsum.photos/seed/berkebun32/300/300"}
  ]
@endsection

@section('page-5-products')
  [
    {name: "Bibit Mangga", description: "Bibit mangga dengan buah manis dan aroma khas", price: "Rp55.000", image: "https://picsum.photos/seed/berkebun33/300/300"},
    {name: "Pupuk Slow Release", description: "Pupuk slow release dengan pelepasan nutrisi bertahap", price: "Rp42.000", image: "https://picsum.photos/seed/berkebun34/300/300"},
    {name: "Benih Buncis", description: "Benih buncis dengan hasil panen cepat dan berkualitas", price: "Rp18.000", image: "https://picsum.photos/seed/berkebun35/300/300"},
    {name: "Media Tanam Hidroponik", description: "Media tanam hidroponik dengan stabilitas tinggi", price: "Rp35.000", image: "https://picsum.photos/seed/berkebun36/300/300"},
    {name: "Bibit Basil", description: "Bibit basil dengan aroma kuat dan daun hijau segar", price: "Rp25.000", image: "https://picsum.photos/seed/berkebun37/300/300"},
    {name: "Pupuk Cair Bunga", description: "Pupuk cair khusus untuk tanaman berbunga", price: "Rp30.000", image: "https://picsum.photos/seed/berkebun38/300/300"},
    {name: "Benih Selada", description: "Benih selada dengan daun renyah dan tekstur lembut", price: "Rp15.000", image: "https://picsum.photos/seed/berkebun39/300/300"},
    {name: "Alat Potong Rumput", description: "Alat potong rumput dengan mata pisau tajam", price: "Rp120.000", image: "https://picsum.photos/seed/berkebun40/300/300"}
  ]
@endsection