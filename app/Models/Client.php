<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'notes',
        'created_at',
        'updated_at'
    ];

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }
}
