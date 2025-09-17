@extends('layouts.app')

@section('title', 'Header Compact Component Test')

@push('styles')
<style>
    .test-content {
        padding: 2rem;
        text-align: center;
    }
    .component-demo {
        margin: 2rem 0;
        padding: 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="test-content">
    <h1>Header Compact Component Test</h1>
    <p>This page demonstrates the header component without a search bar.</p>
    
    <div class="component-demo">
        <h2>Using the compact header component:</h2>
        <p>To use this component in any page, simply include:</p>
        <code>@include('components.header_compact')</code>
    </div>
    
    <!-- Example of including the compact header -->
    <div class="component-demo">
        <h3>Compact Header Example:</h3>
        @include('components.header_compact')
    </div>
    
    <div class="component-demo">
        <h2>Comparison with original header:</h2>
        <p>Original header (with search bar):</p>
        @include('components.header')
    </div>
</div>
@endsection