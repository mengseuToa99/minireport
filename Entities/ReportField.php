<?php

namespace Modules\MiniReportB1\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportField extends Model
{
    use HasFactory;

    protected $fillable = ['report_id', 'table_name', 'field_name'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
