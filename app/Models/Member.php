<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

     protected $table = 'members';

    protected $fillable = [
        'user_id',
        'business_id',
        'business_role_id',
        'name',
        'date_of_birth',
        'gender',
        'phone',
        'profile_pic',
        'description',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'zip_code',
        'custom_fields',
        'status',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    protected $casts = [
        'custom_fields' => 'array',
        'date_of_birth' => 'date',
    ];

    // Relations
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function business() {
        return $this->belongsTo(Business::class);
    }

    public function businessRole() {
        return $this->belongsTo(BusinessRole::class, 'business_role_id');
    }

    public function cashbooks()
    {
        return $this->belongsToMany(Cashbook::class, 'cashbook_member', 'member_id', 'cashbook_id');
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function state() {
        return $this->belongsTo(State::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater() {
        return $this->belongsTo(User::class, 'created_by');
    }

}
