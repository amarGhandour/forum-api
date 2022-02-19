<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    public function likeable()
    {
        return $this->morphTo();
    }
}
