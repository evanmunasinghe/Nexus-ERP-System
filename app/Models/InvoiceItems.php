<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

class InvoiceItems extends Model
{

    protected $Fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
