<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ApartmentSponsor extends Pivot
{
    use HasFactory;

    protected $table = 'apartment_sponsors';

    protected $fillable = [
        'start_date',
        'end_date'
    ];
}
