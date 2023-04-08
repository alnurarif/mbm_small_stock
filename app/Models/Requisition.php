<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function details()
    {
        return $this->hasMany(RequisitionDetail::class);
    }
}
