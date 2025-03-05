<?php

namespace Modules\MiniReportB1\Entities;

use Illuminate\Database\Eloquent\Model;

class MiniReportB1Category extends Model
{
    protected $guarded = ['*']; // Protect all fields

    protected $table = 'minireportb1_category'; // Specify the table name

    public static function forDropdown($business_id)
    {
        $categories = self::where('business_id', $business_id)
            ->pluck('name', 'id');

        return $categories->toArray();
    }
}