@extends('customer.kategori.base')

@section('category-title', 'Hampers')
@section('category-description', 'Temukan berbagai produk hampers menarik dari UMKM lokal')

@section('category-products')
  <div class="product-card">
    <img src="https://picsum.photos/seed/hampers1/300/300" alt="Produk Hampers" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Hampers Lebaran</h3>
      <p class="product-description">Hampers lebaran dengan aneka kue kering dan camilan</p>
      <div class="product-price">Rp250.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/hampers2/300/300" alt="Produk Hampers" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Hampers Natal</h3>
      <p class="product-description">Hampers natal dengan coklat premium dan minuman hangat</p>
      <div class="product-price">Rp320.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/hampers3/300/300" alt="Produk Hampers" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Hampers Valentine</h3>
      <p class="product-description">Hampers valentine dengan bunga kering dan coklat</p>
      <div class="product-price">Rp180.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/hampers4/300/300" alt="Produk Hampers" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Hampers Ulang Tahun</h3>
      <p class="product-description">Hampers ulang tahun dengan kue homemade dan lilin</p>
      <div class="product-price">Rp150.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/hampers5/300/300" alt="Produk Hampers" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Hampers Baby Shower</h3>
      <p class="product-description">Hampers baby shower dengan perlengkapan bayi dan camilan</p>
      <div class="product-price">Rp280.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/hampers6/300/300" alt="Produk Hampers" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Hampers Corporate</h3>
      <p class="product-description">Hampers corporate dengan produk premium dan kemasan elegan</p>
      <div class="product-price">Rp450.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/hampers7/300/300" alt="Produk Hampers" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Hampers Wedding</h3>
      <p class="product-description">Hampers wedding dengan souvenir unik dan kemasan indah</p>
      <div class="product-price">Rp380.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
  
  <div class="product-card">
    <img src="https://picsum.photos/seed/hampers8/300/300" alt="Produk Hampers" class="product-image">
    <div class="product-info">
      <h3 class="product-name">Hampers Syukuran</h3>
      <p class="product-description">Hampers syukuran dengan aneka kue dan buah-buahan</p>
      <div class="product-price">Rp220.000</div>
      <button class="btn btn-primary view-product">Lihat Produk</button>
    </div>
  </div>
@endsection

@section('page-1-products')
  [
    {name: "Hampers Lebaran", description: "Hampers lebaran dengan aneka kue kering dan camilan", price: "Rp250.000", image: "https://picsum.photos/seed/hampers1/300/300"},
    {name: "Hampers Natal", description: "Hampers natal dengan coklat premium dan minuman hangat", price: "Rp320.000", image: "https://picsum.photos/seed/hampers2/300/300"},
    {name: "Hampers Valentine", description: "Hampers valentine dengan bunga kering dan coklat", price: "Rp180.000", image: "https://picsum.photos/seed/hampers3/300/300"},
    {name: "Hampers Ulang Tahun", description: "Hampers ulang tahun dengan kue homemade dan lilin", price: "Rp150.000", image: "https://picsum.photos/seed/hampers4/300/300"},
    {name: "Hampers Baby Shower", description: "Hampers baby shower dengan perlengkapan bayi dan camilan", price: "Rp280.000", image: "https://picsum.photos/seed/hampers5/300/300"},
    {name: "Hampers Corporate", description: "Hampers corporate dengan produk premium dan kemasan elegan", price: "Rp450.000", image: "https://picsum.photos/seed/hampers6/300/300"},
    {name: "Hampers Wedding", description: "Hampers wedding dengan souvenir unik dan kemasan indah", price: "Rp380.000", image: "https://picsum.photos/seed/hampers7/300/300"},
    {name: "Hampers Syukuran", description: "Hampers syukuran dengan aneka kue dan buah-buahan", price: "Rp220.000", image: "https://picsum.photos/seed/hampers8/300/300"}
  ]
@endsection

@section('page-2-products')
  [
    {name: "Hampers Anniversary", description: "Hampers anniversary dengan champagne dan coklat", price: "Rp420.000", image: "https://picsum.photos/seed/hampers9/300/300"},
    {name: "Hampers Graduation", description: "Hampers graduation dengan alat tulis dan camilan", price: "Rp190.000", image: "https://picsum.photos/seed/hampers10/300/300"},
    {name: "Hampers Mother's Day", description: "Hampers mother's day dengan produk perawatan dan bunga", price: "Rp260.000", image: "https://picsum.photos/seed/hampers11/300/300"},
    {name: "Hampers Father's Day", description: "Hampers father's day dengan alat cukur dan minuman", price: "Rp240.000", image: "https://picsum.photos/seed/hampers12/300/300"},
    {name: "Hampers New Year", description: "Hampers new year dengan minuman sparkling dan snack", price: "Rp310.000", image: "https://picsum.photos/seed/hampers13/300/300"},
    {name: "Hampers Teacher's Day", description: "Hampers teacher's day dengan alat tulis dan teh", price: "Rp170.000", image: "https://picsum.photos/seed/hampers14/300/300"},
    {name: "Hampers Friendship Day", description: "Hampers friendship day dengan produk handmade", price: "Rp160.000", image: "https://picsum.photos/seed/hampers15/300/300"},
    {name: "Hampers Housewarming", description: "Hampers housewarming dengan lilin aromaterapi", price: "Rp200.000", image: "https://picsum.photos/seed/hampers16/300/300"}
  ]
@endsection

@section('page-3-products')
  [
    {name: "Hampers Retirement", description: "Hampers retirement dengan jam tangan dan kopi", price: "Rp350.000", image: "https://picsum.photos/seed/hampers17/300/300"},
    {name: "Hampers Promotion", description: "Hampers promotion dengan produk perawatan dan wine", price: "Rp290.000", image: "https://picsum.photos/seed/hampers18/300/300"},
    {name: "Hampers Get Well", description: "Hampers get well dengan buah-buahan dan teh herbal", price: "Rp180.000", image: "https://picsum.photos/seed/hampers19/300/300"},
    {name: "Hampers Congratulations", description: "Hampers congratulations dengan bunga dan coklat", price: "Rp230.000", image: "https://picsum.photos/seed/hampers20/300/300"},
    {name: "Hampers Thank You", description: "Hampers thank you dengan produk lokal dan kemasan indah", price: "Rp210.000", image: "https://picsum.photos/seed/hampers21/300/300"},
    {name: "Hampers Birthday Kids", description: "Hampers birthday kids dengan mainan dan camilan", price: "Rp160.000", image: "https://picsum.photos/seed/hampers22/300/300"},
    {name: "Hampers Romantic", description: "Hampers romantic dengan lilin dan parfum", price: "Rp270.000", image: "https://picsum.photos/seed/hampers23/300/300"},
    {name: "Hampers Spa Day", description: "Hampers spa day dengan produk perawatan tubuh", price: "Rp320.000", image: "https://picsum.photos/seed/hampers24/300/300"}
  ]
@endsection

@section('page-4-products')
  [
    {name: "Hampers Anniversary 10th", description: "Hampers anniversary 10th dengan champagne dan coklat premium", price: "Rp480.000", image: "https://picsum.photos/seed/hampers25/300/300"},
    {name: "Hampers Baby Boy", description: "Hampers baby boy dengan pakaian bayi dan mainan", price: "Rp260.000", image: "https://picsum.photos/seed/hampers26/300/300"},
    {name: "Hampers Baby Girl", description: "Hampers baby girl dengan pakaian bayi dan boneka", price: "Rp260.000", image: "https://picsum.photos/seed/hampers27/300/300"},
    {name: "Hampers Christmas", description: "Hampers christmas dengan dekorasi dan kue spesial", price: "Rp340.000", image: "https://picsum.photos/seed/hampers28/300/300"},
    {name: "Hampers New Baby", description: "Hampers new baby dengan perlengkapan bayi dan camilan", price: "Rp290.000", image: "https://picsum.photos/seed/hampers29/300/300"},
    {name: "Hampers Moving House", description: "Hampers moving house dengan lilin dan teh", price: "Rp190.000", image: "https://picsum.photos/seed/hampers30/300/300"},
    {name: "Hampers New Home", description: "Hampers new home dengan dekorasi dan minuman", price: "Rp220.000", image: "https://picsum.photos/seed/hampers31/300/300"},
    {name: "Hampers Engagement", description: "Hampers engagement dengan cincin display dan coklat", price: "Rp410.000", image: "https://picsum.photos/seed/hampers32/300/300"}
  ]
@endsection

@section('page-5-products')
  [
    {name: "Hampers Golden Anniversary", description: "Hampers golden anniversary dengan champagne dan souvenir", price: "Rp520.000", image: "https://picsum.photos/seed/hampers33/300/300"},
    {name: "Hampers New Job", description: "Hampers new job dengan alat tulis dan kopi", price: "Rp200.000", image: "https://picsum.photos/seed/hampers34/300/300"},
    {name: "Hampers Recovery", description: "Hampers recovery dengan buah-buahan dan suplemen", price: "Rp190.000", image: "https://picsum.photos/seed/hampers35/300/300"},
    {name: "Hampers Syukur", description: "Hampers syukur dengan aneka kue dan minuman", price: "Rp240.000", image: "https://picsum.photos/seed/hampers36/300/300"},
    {name: "Hampers Eid Al-Fitr", description: "Hampers eid al-fitr dengan kue kering dan kurma", price: "Rp270.000", image: "https://picsum.photos/seed/hampers37/300/300"},
    {name: "Hampers Eid Al-Adha", description: "Hampers eid al-adha dengan daging olahan dan kurma", price: "Rp310.000", image: "https://picsum.photos/seed/hampers38/300/300"},
    {name: "Hampers Corporate Gifting", description: "Hampers corporate gifting dengan produk branded", price: "Rp490.000", image: "https://picsum.photos/seed/hampers39/300/300"},
    {name: "Hampers Luxury", description: "Hampers luxury dengan produk premium dan kemasan eksklusif", price: "Rp650.000", image: "https://picsum.photos/seed/hampers40/300/300"}
  ]
@endsection