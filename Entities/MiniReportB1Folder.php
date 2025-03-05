<?php

namespace Modules\MiniReportB1\Entities;

use Illuminate\Database\Eloquent\Model;

class MiniReportB1Folder extends Model
{
    protected $table = 'minireportb1_folders';
    
    protected $fillable = [
        'business_id',
        'folder_name',
        'parent_id'
    ];

    public $timestamps = false;

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    public function children()
    {
        return $this->hasMany(MiniReportB1Folder::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(MiniReportB1File::class, 'parent_id');
    }
} 