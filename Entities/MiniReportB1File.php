<?php

namespace Modules\MiniReportB1\Entities;

use Illuminate\Database\Eloquent\Model;

class MiniReportB1File extends Model
{
    protected $table = 'minireportb1_files';
    
    protected $fillable = [
        'business_id',
        'file_name',
        'parent_id',
        'layout'
    ];

    protected $casts = [
        'layout' => 'array'
    ];

    public function folder()
    {
        return $this->belongsTo(MiniReportB1Folder::class, 'parent_id');
    }
} 