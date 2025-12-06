<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

     protected $table = 'businesses';

    protected $fillable = [
        'name',
        'description',
        'gst_number',
        'phone',
        'email',
        'website',
        'logo',
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
    ];

    // Relations
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
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function members() {
        return $this->hasMany(Member::class);
    }

    public function cashbooks() {
        return $this->hasMany(Cashbook::class);
    }
    public function users()
    {
        return $this->hasManyThrough(
        User::class,
        Member::class,
        'business_id', // Member table column that references businesses.id
        'id',          // User table primary key
        'id',          // Business table primary key
        'user_id'      // Member table column that references users
        );
    }

}
