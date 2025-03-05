<?php

namespace Modules\MiniReportB1\Entities;

use Illuminate\Database\Eloquent\Model;

namespace Modules\MiniReportB1\Entities;

use Illuminate\Database\Eloquent\Model;

class MiniReportB1Layout extends Model
{
    protected $table = 'minireportb1_layout';
    
    protected $fillable = [
        'layout_name',
        'type',
        'content',
        'x',
        'y',
        'position'
    ];

    protected $casts = [
        'content' => 'json',
        'x' => 'float',
        'y' => 'float',
        'position' => 'float'
    ];
}