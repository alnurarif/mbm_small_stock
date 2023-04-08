<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'item_id',
        'quantity',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
}
