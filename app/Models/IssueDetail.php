<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'requisition_id',
        'item_id',
        'quantity',
        'price',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
