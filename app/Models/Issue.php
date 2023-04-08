<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'employee_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function details()
    {
        return $this->hasMany(IssueDetail::class);
    }
}
