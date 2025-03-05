<?php

namespace Modules\MiniReportB1\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $table = 'minireportb1_reports';

    public function fields()
    {
        return $this->hasMany(ReportField::class);
    }
}
