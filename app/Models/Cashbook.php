<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cashbook extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cashbooks';

    protected $fillable = [
        'business_id',
        'title',
        'description',
        'custom_fields',
        'status',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    protected $casts = [
        'custom_fields' => 'array'
    ];

    // Relations
    public function cashbook() {
        return $this->belongsTo(Cashbook::class);
    }

    public function business() {
        return $this->belongsTo(Business::class);
    }

    public function members() {
        return $this->belongsToMany(Member::class, 'cashbook_member', 'cashbook_id', 'member_id');
    }

    public function entries() {
        return $this->hasMany(Transaction::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
