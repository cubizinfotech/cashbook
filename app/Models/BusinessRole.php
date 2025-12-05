<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'business_roles';

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    // Relations
    public function permissions()
    {
        return $this->belongsToMany(BusinessPermission::class, 'business_role_permission', 'business_role_id', 'business_permission_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater() {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function role()
    {
        return $this->belongsTo(BusinessRole::class, 'business_role_id');
    }
}
