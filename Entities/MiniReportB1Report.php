<?php

namespace Modules\MiniReportB1\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MiniReportB1Report extends Model
{
    use HasFactory;

    protected $table = 'minireportb1_reports';
    
    protected $fillable = [
        'business_id',
        'report_name',
        'visible_columns',
        'filters',
        'created_by'
    ];

    protected $casts = [
        'visible_columns' => 'array',
        'filters' => 'array'
    ];

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
