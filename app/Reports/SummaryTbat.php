<?php

namespace App\Reports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SummaryTbat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tbat_detail_id', 'region_id', 'area_id', 'district_id', 'storeId', 'storeDestinationId', 'user_id', 'week',
        'distributor_code', 'distributor_name', 'region', 'channel', 'sub_channel', 'area', 'district', 'store_name_1',
        'store_name_2', 'store_id', 'store_destination_name_1', 'store_destination_name_2', 'store_destination_id', 'nik', 'promoter_name', 'date', 'model', 'group', 'category', 'product_name',
        'quantity', 'unit_price', 'value', 'value_pf_mr', 'value_pf_tr', 'value_pf_ppe', 'role', 'spv_name', 'dm_name', 'trainer_name'
    ];
}
