<?php

namespace App\Reports;

use Illuminate\Database\Eloquent\Model;

class SummaryDisplayShare extends Model
{
    protected $fillable = [
        'displayshare_detail_id', 'region_id', 'area_id', 'district_id', 'storeId', 'user_id', 'week',
        'distributor_code', 'distributor_name', 'region', 'channel', 'sub_channel', 'area', 'district', 'store_name_1',
        'store_name_2', 'store_id', 'nik', 'promoter_name', 'date', 'category', 'philips', 'all', 'percentage', 'role', 'spv_name', 'dm_name', 'trainer_name'
    ];
}
