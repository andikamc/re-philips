<?php

namespace App\Traits;

use App\EmployeeStore;
use App\Store;
use App\Target;

trait PromoterTrait {

    public function getPromoterTitle($user_id, $store_id, $sell_type){

        $target = Target::where('user_id', $user_id)->where('store_id', $store_id)->where('sell_type', $sell_type)->first();

        $result = '';

        if($target->target_da > 0){
            $result = 'DA';
        }else if($target->target_pc > 0){
            $result = 'PC';
        }else if($target->target_mcc > 0){
            $result = 'MCC';
        }

        if($target->target_da > 0 && $target->target_pc > 0){
            $result = 'HYBRID';
        }

        return $result;

    }

}