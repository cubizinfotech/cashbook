<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_methods';

    protected $fillable = [
        'cashbook_id',
        'name',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    // Relations
    public function cashbook() {
        return $this->belongsTo(Cashbook::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
