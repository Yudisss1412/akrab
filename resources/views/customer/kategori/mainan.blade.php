@extends('customer.kategori.base')

@section('category-title', 'Mainan')
@section('category-description', 'Temukan berbagai produk mainan dari UMKM lokal')

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