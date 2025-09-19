@extends('customer.kategori.base')

@section('category-title', 'Fashion')
@section('category-description', 'Temukan berbagai produk fashion menarik dari UMKM lokal')

@section('category-products')
  <div class="product-card">
    <img src="https://picsum.photos/seed/fashion1/300/300" alt="Produk Fashion" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Baju Batik Wanita</h3>
      <p class="product-description">Baju batik wanita dengan motif tradisional dan bahan nyaman</p>
      <div class="product-price">Rp180.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/fashion2/300/300" alt="Produk Fashion" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Sarung Tangan Kulit</h3>
      <p class="product-description">Sarung tangan kulit dengan kualitas premium dan desain elegan</p>
      <div class="product-price">Rp95.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/fashion3/300/300" alt="Produk Fashion" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Tas Tenun</h3>
      <p class="product-description">Tas tenun dengan motif khas daerah dan bahan tahan lama</p>
      <div class="product-price">Rp150.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/fashion4/300/300" alt="Produk Fashion" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Sepatu Sandal</h3>
      <p class="product-description">Sepatu sandal nyaman dengan desain modern</p>
      <div class="product-price">Rp120.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/fashion5/300/300" alt="Produk Fashion" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Kerudung Instan</h3>
      <p class="product-description">Kerudung instan dengan bahan adem dan mudah dipakai</p>
      <div class="product-price">Rp65.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/fashion6/300/300" alt="Produk Fashion" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Dompet Kulit</h3>
      <p class="product-description">Dompet kulit asli dengan desain minimalis</p>
      <div class="product-price">Rp85.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/fashion7/300/300" alt="Produk Fashion" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Baju Kemeja Pria</h3>
      <p class="product-description">Kemeja pria dengan bahan katun premium dan jahitan rapi</p>
      <div class="product-price">Rp160.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/fashion8/300/300" alt="Produk Fashion" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Jilbab Segi Empat</h3>
      <p class="product-description">Jilbab segi empat dengan bahan lembut dan warna menarik</p>
      <div class="product-price">Rp55.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
@endsection

@section('page-1-products')
  [
    {name: "Baju Batik Wanita", description: "Baju batik wanita dengan motif tradisional dan bahan nyaman", price: "Rp180.000", image: "https://picsum.photos/seed/fashion1/300/300"},
    {name: "Sarung Tangan Kulit", description: "Sarung tangan kulit dengan kualitas premium dan desain elegan", price: "Rp95.000", image: "https://picsum.photos/seed/fashion2/300/300"},
    {name: "Tas Tenun", description: "Tas tenun dengan motif khas daerah dan bahan tahan lama", price: "Rp150.000", image: "https://picsum.photos/seed/fashion3/300/300"},
    {name: "Sepatu Sandal", description: "Sepatu sandal nyaman dengan desain modern", price: "Rp120.000", image: "https://picsum.photos/seed/fashion4/300/300"},
    {name: "Kerudung Instan", description: "Kerudung instan dengan bahan adem dan mudah dipakai", price: "Rp65.000", image: "https://picsum.photos/seed/fashion5/300/300"},
    {name: "Dompet Kulit", description: "Dompet kulit asli dengan desain minimalis", price: "Rp85.000", image: "https://picsum.photos/seed/fashion6/300/300"},
    {name: "Baju Kemeja Pria", description: "Kemeja pria dengan bahan katun premium dan jahitan rapi", price: "Rp160.000", image: "https://picsum.photos/seed/fashion7/300/300"},
    {name: "Jilbab Segi Empat", description: "Jilbab segi empat dengan bahan lembut dan warna menarik", price: "Rp55.000", image: "https://picsum.photos/seed/fashion8/300/300"}
  ]
@endsection

@section('page-2-products')
  [
    {name: "Gaun Pesta", description: "Gaun pesta dengan desain elegan dan bahan berkualitas", price: "Rp250.000", image: "https://picsum.photos/seed/fashion9/300/300"},
    {name: "Topi Petani", description: "Topi petani dengan bahan tahan panas dan nyaman", price: "Rp75.000", image: "https://picsum.photos/seed/fashion10/300/300"},
    {name: "Ikat Pinggang Kulit", description: "Ikat pinggang kulit asli dengan aksen logam", price: "Rp110.000", image: "https://picsum.photos/seed/fashion11/300/300"},
    {name: "Baju Kaos", description: "Kaos dengan sablon berkualitas dan bahan adem", price: "Rp85.000", image: "https://picsum.photos/seed/fashion12/300/300"},
    {name: "Celana Pendek", description: "Celana pendek dengan bahan katun dan desain modern", price: "Rp130.000", image: "https://picsum.photos/seed/fashion13/300/300"},
    {name: "Scarf", description: "Scarf dengan motif cantik dan bahan lembut", price: "Rp60.000", image: "https://picsum.photos/seed/fashion14/300/300"},
    {name: "Tas Selempang", description: "Tas selempang dengan desain trendy dan tahan lama", price: "Rp95.000", image: "https://picsum.photos/seed/fashion15/300/300"},
    {name: "Sepatu Sneakers", description: "Sepatu sneakers nyaman dengan sol empuk", price: "Rp220.000", image: "https://picsum.photos/seed/fashion16/300/300"}
  ]
@endsection

@section('page-3-products')
  [
    {name: "Baju Gamis", description: "Gamis dengan desain syar'i dan bahan adem", price: "Rp190.000", image: "https://picsum.photos/seed/fashion17/300/300"},
    {name: "Sandal Jepit", description: "Sandal jepit nyaman dengan bahan berkualitas", price: "Rp45.000", image: "https://picsum.photos/seed/fashion18/300/300"},
    {name: "Tas Ransel", description: "Tas ransel dengan banyak kompartemen dan bahan tahan air", price: "Rp175.000", image: "https://picsum.photos/seed/fashion19/300/300"},
    {name: "Baju Hoodie", description: "Hoodie dengan bahan fleece dan desain stylish", price: "Rp145.000", image: "https://picsum.photos/seed/fashion20/300/300"},
    {name: "Celana Jeans", description: "Celana jeans dengan potongan modern dan bahan stretch", price: "Rp165.000", image: "https://picsum.photos/seed/fashion21/300/300"},
    {name: "Topi Baseball", description: "Topi baseball dengan bordiran rapi dan visor kaku", price: "Rp65.000", image: "https://picsum.photos/seed/fashion22/300/300"},
    {name: "Tas Tote", description: "Tas tote dengan desain minimalis dan bahan kanvas", price: "Rp80.000", image: "https://picsum.photos/seed/fashion23/300/300"},
    {name: "Sepatu Boots", description: "Sepatu boots dengan bahan sintetis dan sol anti selip", price: "Rp280.000", image: "https://picsum.photos/seed/fashion24/300/300"}
  ]
@endsection

@section('page-4-products')
  [
    {name: "Baju Tunik", description: "Tunik dengan desain longgar dan bahan katun", price: "Rp135.000", image: "https://picsum.photos/seed/fashion25/300/300"},
    {name: "Kacamata Hitam", description: "Kacamata hitam dengan lensa UV protection", price: "Rp120.000", image: "https://picsum.photos/seed/fashion26/300/300"},
    {name: "Tas Clutch", description: "Tas clutch dengan desain elegan dan bahan kulit sintetis", price: "Rp110.000", image: "https://picsum.photos/seed/fashion27/300/300"},
    {name: "Baju Cardigan", description: "Cardigan dengan bahan rajut dan desain trendy", price: "Rp105.000", image: "https://picsum.photos/seed/fashion28/300/300"},
    {name: "Celana Legging", description: "Celana legging dengan bahan stretch dan nyaman dipakai", price: "Rp85.000", image: "https://picsum.photos/seed/fashion29/300/300"},
    {name: "Topi Fedora", description: "Topi fedora dengan bahan felt dan desain klasik", price: "Rp90.000", image: "https://picsum.photos/seed/fashion30/300/300"},
    {name: "Tas Waist Bag", description: "Tas waist bag dengan desain fashionable dan tahan lama", price: "Rp75.000", image: "https://picsum.photos/seed/fashion31/300/300"},
    {name: "Sepatu Loafers", description: "Sepatu loafers dengan bahan kulit sintetis dan desain formal", price: "Rp200.000", image: "https://picsum.photos/seed/fashion32/300/300"}
  ]
@endsection

@section('page-5-products')
  [
    {name: "Baju Blazer", description: "Blazer dengan bahan wool dan desain professional", price: "Rp320.000", image: "https://picsum.photos/seed/fashion33/300/300"},
    {name: "Sarung Tangan Katun", description: "Sarung tangan katun dengan motif cantik", price: "Rp35.000", image: "https://picsum.photos/seed/fashion34/300/300"},
    {name: "Tas Backpack", description: "Tas backpack dengan laptop compartment dan bahan waterproof", price: "Rp195.000", image: "https://picsum.photos/seed/fashion35/300/300"},
    {name: "Baju Sweater", description: "Sweater dengan bahan wol dan desain cozy", price: "Rp155.000", image: "https://picsum.photos/seed/fashion36/300/300"},
    {name: "Celana Chino", description: "Celana chino dengan potongan slim fit dan bahan katun", price: "Rp125.000", image: "https://picsum.photos/seed/fashion37/300/300"},
    {name: "Topi Bucket", description: "Topi bucket dengan bahan katun dan desain casual", price: "Rp55.000", image: "https://picsum.photos/seed/fashion38/300/300"},
    {name: "Tas Crossbody", description: "Tas crossbody dengan desain compact dan bahan sintetis", price: "Rp95.000", image: "https://picsum.photos/seed/fashion39/300/300"},
    {name: "Sepatu Formal", description: "Sepatu formal dengan bahan kulit asli dan sol karet", price: "Rp350.000", image: "https://picsum.photos/seed/fashion40/300/300"}
  ]
@endsection