<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model OrderItem - Item Pesanan
 * 
 * Model ini merepresentasikan setiap item dalam sebuah pesanan.
 * Satu order dapat memiliki multiple order items (produk yang berbeda).
 * 
 * Setiap item menyimpan:
 * - Produk yang dipesan
 * - Varian produk (jika ada)
 * - Quantity
 * - Harga satuan (unit_price)
 * - Subtotal (quantity × unit_price)
 * 
 * Harga disimpan snapshot-nya saat order dibuat, sehingga perubahan
 * harga produk di masa depan tidak mempengaruhi order yang sudah ada.
 */
class OrderItem extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'order_id',       // ID pesanan (relasi ke orders)
        'product_id',     // ID produk yang dipesan
        'variant_id',     // ID varian produk (opsional)
        'quantity',       // Jumlah produk yang dipesan
        'unit_price',     // Harga satuan produk saat order dibuat
        'subtotal',       // Subtotal (quantity × unit_price)
    ];

    /**
     * Relasi ke pesanan (order)
     * 
     * Setiap item dimiliki oleh satu order.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke produk
     * 
     * Setiap item merepresentasikan satu produk yang dipesan.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke varian produk
     * 
     * Varian produk bersifat opsional. Digunakan jika produk
     * memiliki pilihan seperti ukuran, warna, dll.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}