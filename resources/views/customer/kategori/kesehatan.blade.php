@extends('customer.kategori.base')

@section('category-title', 'Produk Kesehatan')
@section('category-description', 'Temukan berbagai produk kesehatan alami dari UMKM lokal')

@section('category-products')
  <div class="product-card">
    <img src="https://picsum.photos/seed/kesehatan1/300/300" alt="Produk Kesehatan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Minyak Telon Bayi</h3>
      <p class="product-description">Minyak telon bayi dengan bahan alami dan aman untuk kulit</p>
      <div class="product-price">Rp45.000</div>
      <button class="btn btn-primary view-product">Pratinjau</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kesehatan2/300/300" alt="Produk Kesehatan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Balsam Gurah</h3>
      <p class="product-description">Balsam gurah dengan khasiat menghangatkan dan menyegarkan</p>
      <div class="product-price">Rp25.000</div>
      <button class="btn btn-primary view-product">Pratinjau</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kesehatan3/300/300" alt="Produk Kesehatan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Sirup Herbal</h3>
      <p class="product-description">Sirup herbal dengan campuran rempah alami</p>
      <div class="product-price">Rp35.000</div>
      <button class="btn btn-primary view-product">Pratinjau</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kesehatan4/300/300" alt="Produk Kesehatan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Salep Kulit</h3>
      <p class="product-description">Salep kulit dengan bahan alami untuk mengatasi iritasi</p>
      <div class="product-price">Rp30.000</div>
      <button class="btn btn-primary view-product">Pratinjau</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kesehatan5/300/300" alt="Produk Kesehatan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Minyak Kayu Putih</h3>
      <p class="product-description">Minyak kayu putih dengan khasiat menyembuhkan luka</p>
      <div class="product-price">Rp28.000</div>
      <button class="btn btn-primary view-product">Pratinjau</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kesehatan6/300/300" alt="Produk Kesehatan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Jahe Merah Bubuk</h3>
      <p class="product-description">Jahe merah bubuk dengan khasiat meningkatkan stamina</p>
      <div class="product-price">Rp40.000</div>
      <button class="btn btn-primary view-product">Pratinjau</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kesehatan7/300/300" alt="Produk Kesehatan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Kapsul Temu Lawak</h3>
      <p class="product-description">Kapsul temu lawak dengan khasiat menjaga kesehatan pencernaan</p>
      <div class="product-price">Rp55.000</div>
      <button class="btn btn-primary view-product">Pratinjau</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/kesehatan8/300/300" alt="Produk Kesehatan" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Teh Daun Senna</h3>
      <p class="product-description">Teh daun senna dengan khasiat alami untuk kesehatan</p>
      <div class="product-price">Rp22.000</div>
      <button class="btn btn-primary view-product">Pratinjau</button>
    </div>
  </div>
@endsection

@section('page-1-products')
  [
    {name: "Minyak Telon Bayi", description: "Minyak telon bayi dengan bahan alami dan aman untuk kulit", price: "Rp45.000", image: "https://picsum.photos/seed/kesehatan1/300/300"},
    {name: "Balsam Gurah", description: "Balsam gurah dengan khasiat menghangatkan dan menyegarkan", price: "Rp25.000", image: "https://picsum.photos/seed/kesehatan2/300/300"},
    {name: "Sirup Herbal", description: "Sirup herbal dengan campuran rempah alami", price: "Rp35.000", image: "https://picsum.photos/seed/kesehatan3/300/300"},
    {name: "Salep Kulit", description: "Salep kulit dengan bahan alami untuk mengatasi iritasi", price: "Rp30.000", image: "https://picsum.photos/seed/kesehatan4/300/300"},
    {name: "Minyak Kayu Putih", description: "Minyak kayu putih dengan khasiat menyembuhkan luka", price: "Rp28.000", image: "https://picsum.photos/seed/kesehatan5/300/300"},
    {name: "Jahe Merah Bubuk", description: "Jahe merah bubuk dengan khasiat meningkatkan stamina", price: "Rp40.000", image: "https://picsum.photos/seed/kesehatan6/300/300"},
    {name: "Kapsul Temu Lawak", description: "Kapsul temu lawak dengan khasiat menjaga kesehatan pencernaan", price: "Rp55.000", image: "https://picsum.photos/seed/kesehatan7/300/300"},
    {name: "Teh Daun Senna", description: "Teh daun senna dengan khasiat alami untuk kesehatan", price: "Rp22.000", image: "https://picsum.photos/seed/kesehatan8/300/300"}
  ]
@endsection

@section('page-2-products')
  [
    {name: "Minyak Zaitun", description: "Minyak zaitun murni dengan khasiat untuk kesehatan jantung", price: "Rp65.000", image: "https://picsum.photos/seed/kesehatan9/300/300"},
    {name: "Madu Hutan", description: "Madu hutan murni dengan khasiat meningkatkan daya tahan tubuh", price: "Rp85.000", image: "https://picsum.photos/seed/kesehatan10/300/300"},
    {name: "Bubuk Kunyit Asam", description: "Bubuk kunyit asam dengan khasiat anti inflamasi", price: "Rp32.000", image: "https://picsum.photos/seed/kesehatan11/300/300"},
    {name: "Kapsul Spirulina", description: "Kapsul spirulina dengan kandungan nutrisi lengkap", price: "Rp75.000", image: "https://picsum.photos/seed/kesehatan12/300/300"},
    {name: "Essential Oil Lavender", description: "Essential oil lavender dengan khasiat menenangkan", price: "Rp58.000", image: "https://picsum.photos/seed/kesehatan13/300/300"},
    {name: "Teh Ginseng", description: "Teh ginseng dengan khasiat meningkatkan energi", price: "Rp28.000", image: "https://picsum.photos/seed/kesehatan14/300/300"},
    {name: "Salep Luka Bakar", description: "Salep luka bakar dengan bahan alami menyembuhkan", price: "Rp42.000", image: "https://picsum.photos/seed/kesehatan15/300/300"},
    {name: "Minyak Serai Wangi", description: "Minyak serai wangi dengan khasiat mengusir nyamuk", price: "Rp24.000", image: "https://picsum.photos/seed/kesehatan16/300/300"}
  ]
@endsection

@section('page-3-products')
  [
    {name: "Kapsul Omega-3", description: "Kapsul omega-3 dengan khasiat untuk kesehatan otak", price: "Rp95.000", image: "https://picsum.photos/seed/kesehatan17/300/300"},
    {name: "Teh Chamomile", description: "Teh chamomile dengan khasiat menenangkan dan anti stres", price: "Rp26.000", image: "https://picsum.photos/seed/kesehatan18/300/300"},
    {name: "Bubuk Jahe", description: "Bubuk jahe dengan khasiat menghangatkan tubuh", price: "Rp20.000", image: "https://picsum.photos/seed/kesehatan19/300/300"},
    {name: "Minyak Kemiri", description: "Minyak kemiri dengan khasiat untuk kesehatan rambut", price: "Rp38.000", image: "https://picsum.photos/seed/kesehatan20/300/300"},
    {name: "Kapsul Vitamin C", description: "Kapsul vitamin C dengan kandungan alami tinggi", price: "Rp68.000", image: "https://picsum.photos/seed/kesehatan21/300/300"},
    {name: "Teh Rooibos", description: "Teh rooibos tanpa kafein dengan antioksidan tinggi", price: "Rp34.000", image: "https://picsum.photos/seed/kesehatan22/300/300"},
    {name: "Salep Anti Jamur", description: "Salep anti jamur dengan bahan alami alami", price: "Rp36.000", image: "https://picsum.photos/seed/kesehatan23/300/300"},
    {name: "Minyak Kelapa Murni", description: "Minyak kelapa murni dengan khasiat multi manfaat", price: "Rp48.000", image: "https://picsum.photos/seed/kesehatan24/300/300"}
  ]
@endsection

@section('page-4-products')
  [
    {name: "Kapsul Probiotik", description: "Kapsul probiotik dengan bakteri baik untuk pencernaan", price: "Rp82.000", image: "https://picsum.photos/seed/kesehatan25/300/300"},
    {name: "Teh Peppermint", description: "Teh peppermint dengan khasiat menyegarkan dan mengurangi mual", price: "Rp23.000", image: "https://picsum.photos/seed/kesehatan26/300/300"},
    {name: "Bubuk Kurma", description: "Bubuk kurma dengan kandungan serat tinggi", price: "Rp45.000", image: "https://picsum.photos/seed/kesehatan27/300/300"},
    {name: "Minyak Bulus", description: "Minyak bulus dengan khasiat untuk kesehatan kulit", price: "Rp32.000", image: "https://picsum.photos/seed/kesehatan28/300/300"},
    {name: "Kapsul Glukosamin", description: "Kapsul glukosamin dengan khasiat untuk kesehatan sendi", price: "Rp88.000", image: "https://picsum.photos/seed/kesehatan29/300/300"},
    {name: "Teh Hibiscus", description: "Teh hibiscus dengan khasiat menurunkan tekanan darah", price: "Rp27.000", image: "https://picsum.photos/seed/kesehatan30/300/300"},
    {name: "Salep Anti Bakteri", description: "Salep anti bakteri dengan bahan alami alami", price: "Rp39.000", image: "https://picsum.photos/seed/kesehatan31/300/300"},
    {name: "Minyak Angin", description: "Minyak angin dengan khasiat menghangatkan badan", price: "Rp29.000", image: "https://picsum.photos/seed/kesehatan32/300/300"}
  ]
@endsection

@section('page-5-products')
  [
    {name: "Kapsul Kalsium", description: "Kapsul kalsium dengan khasiat untuk kesehatan tulang", price: "Rp72.000", image: "https://picsum.photos/seed/kesehatan33/300/300"},
    {name: "Teh Lemon Balm", description: "Teh lemon balm dengan khasiat menenangkan saraf", price: "Rp31.000", image: "https://picsum.photos/seed/kesehatan34/300/300"},
    {name: "Bubuk Matcha", description: "Bubuk matcha dengan antioksidan tinggi", price: "Rp58.000", image: "https://picsum.photos/seed/kesehatan35/300/300"},
    {name: "Minyak Ikan", description: "Minyak ikan dengan kandungan omega-3 tinggi", price: "Rp92.000", image: "https://picsum.photos/seed/kesehatan36/300/300"},
    {name: "Kapsul Collagen", description: "Kapsul collagen dengan khasiat untuk kesehatan kulit", price: "Rp105.000", image: "https://picsum.photos/seed/kesehatan37/300/300"},
    {name: "Teh Echinacea", description: "Teh echinacea dengan khasiat meningkatkan imun", price: "Rp33.000", image: "https://picsum.photos/seed/kesehatan38/300/300"},
    {name: "Salep Anti Radang", description: "Salep anti radang dengan bahan alami alami", price: "Rp41.000", image: "https://picsum.photos/seed/kesehatan39/300/300"},
    {name: "Minyak Wangi Terapi", description: "Minyak wangi terapi dengan khasiat relaksasi", price: "Rp36.000", image: "https://picsum.photos/seed/kesehatan40/300/300"}
  ]
@endsection