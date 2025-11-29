@extends('customer.kategori.base')

@section('category-title', 'Mainan')
@section('category-description', 'Temukan berbagai produk mainan dari UMKM lokal')

@push('scripts')
  <script>
    // Siapkan semua data produk untuk modal
    window.allProductData = [
      @if(isset($page_1_products))
        @foreach($page_1_products as $product)
          {
            id: {{ $product['id'] }},
            name: {{ json_encode($product['name']) }},
            description: {{ json_encode($product['description']) }},
            price: {{ json_encode($product['price']) }},
            image: {{ json_encode($product['image']) }},
            specifications: []
          },
        @endforeach
      @endif
      @if(isset($page_2_products))
        @foreach($page_2_products as $product)
          {
            id: {{ $product['id'] }},
            name: {{ json_encode($product['name']) }},
            description: {{ json_encode($product['description']) }},
            price: {{ json_encode($product['price']) }},
            image: {{ json_encode($product['image']) }},
            specifications: []
          },
        @endforeach
      @endif
      @if(isset($page_3_products))
        @foreach($page_3_products as $product)
          {
            id: {{ $product['id'] }},
            name: {{ json_encode($product['name']) }},
            description: {{ json_encode($product['description']) }},
            price: {{ json_encode($product['price']) }},
            image: {{ json_encode($product['image']) }},
            specifications: []
          },
        @endforeach
      @endif
      @if(isset($page_4_products))
        @foreach($page_4_products as $product)
          {
            id: {{ $product['id'] }},
            name: {{ json_encode($product['name']) }},
            description: {{ json_encode($product['description']) }},
            price: {{ json_encode($product['price']) }},
            image: {{ json_encode($product['image']) }},
            specifications: []
          },
        @endforeach
      @endif
      @if(isset($page_5_products))
        @foreach($page_5_products as $product)
          {
            id: {{ $product['id'] }},
            name: {{ json_encode($product['name']) }},
            description: {{ json_encode($product['description']) }},
            price: {{ json_encode($product['price']) }},
            image: {{ json_encode($product['image']) }},
            specifications: []
          },
        @endforeach
      @endif
    ];
  </script>
@endpush

@section('category-products')
  @if(isset($page_1_products))
    @foreach($page_1_products as $product)
      <div class="product-card" data-product-id="{{ $product['id'] }}">
        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="product-image">
        <div class="product-info">
          <h3 class="product-name">{{ $product['name'] }}</h3>
          <p class="product-description">{{ $product['description'] }}</p>
          <div class="product-price">{{ $product['price'] }}</div>
          <button class="btn btn-primary view-product" data-product-id="{{ $product['id'] }}">Pratinjau</button>
        </div>
      </div>
    @endforeach
  @endif
@endsection

@if(isset($page_1_products))
@section('page-1-products', json_encode($page_1_products))
@endif

@if(isset($page_2_products))
@section('page-2-products', json_encode($page_2_products))
@endif

@if(isset($page_3_products))
@section('page-3-products', json_encode($page_3_products))
@endif

@if(isset($page_4_products))
@section('page-4-products', json_encode($page_4_products))
@endif

@if(isset($page_5_products))
@section('page-5-products', json_encode($page_5_products))
@endif