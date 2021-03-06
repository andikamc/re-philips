<?php

namespace App\Traits;

use App\Attendance;
use App\AttendanceDetail;
use App\EmployeeStore;
use App\Product;
use App\ProductFocuses;
use App\Reports\SalesmanSummarySales;
use App\Reports\SalesmanSummaryTargetActual;
use App\Reports\SummarySellIn;
use App\Reports\SummarySellOut;
use App\Reports\SummaryTargetActual;
use Carbon\Carbon;
use App\Store;

trait ActualTrait {

    use PromoterTrait;

    public function changePromoterTitle($userId, $storeId, $sellType){

        $target = SummaryTargetActual::where('user_id',$userId)->where('storeId', $storeId)->where('sell_type', $sellType)->first();
        $target->update(['title_of_promoter' => $this->getPromoterTitle($userId, $storeId, $sellType)]);

    }

    public function changeActual($data, $change)
    {

        $summary = SummaryTargetActual::where('user_id', $data['user_id'])->where('storeId', $data['store_id'])
            ->where('sell_type', $data['sell_type'])->first();

        if ($summary) {

            $sumStore = SummaryTargetActual::where('storeId', $summary->storeId)->where('sell_type', $data['sell_type']);
            $sumArea = SummaryTargetActual::where('area_id', $summary->area_id)->where('sell_type', $data['sell_type']);
            $sumRegion = SummaryTargetActual::where('region_id', $summary->region_id)->where('sell_type', $data['sell_type']);
            $sumActualStore = SummaryTargetActual::where('storeId', $summary->storeId)->where('sell_type', $data['sell_type'])->first()->sum_actual_store;
            $sumActualArea = SummaryTargetActual::where('area_id', $summary->area_id)->where('sell_type', $data['sell_type'])->first()->sum_actual_area;
            $sumActualRegion = SummaryTargetActual::where('region_id', $summary->region_id)->where('sell_type', $data['sell_type'])->first()->sum_actual_region;

            /* Add / Sum All Target */
            if ($change == 'change') { // INSERT / UPDATE

                /* DA */
                if ($data['group'] == 'DA') {

                    if ($summary->target_da > 0) {

                        if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                            $summary->update([ // SUBSTRACT OLD VALUE
                                'actual_da' => $summary->actual_da - $data['value_old'],
                                'sum_actual_store' => $sumActualStore - $data['value_old'],
                                'sum_actual_area' => $sumActualArea - $data['value_old'],
                                'sum_actual_region' => $sumActualRegion - $data['value_old'],
                            ]);

                            $summary->update([ // ADD NEW VALUE
                                'actual_da' => $summary->actual_da + $data['value'],
                                'sum_actual_store' => $summary->sum_actual_store + $data['value'],
                                'sum_actual_area' => $summary->sum_actual_area + $data['value'],
                                'sum_actual_region' => $summary->sum_actual_region + $data['value'],
                            ]);

                        } else { // INSERT

                            $summary->update([ // ADD NEW VALUE
                                'actual_da' => $summary->actual_da + $data['value'],
                                'sum_actual_store' => $sumActualStore + $data['value'],
                                'sum_actual_area' => $sumActualArea + $data['value'],
                                'sum_actual_region' => $sumActualRegion + $data['value'],
                            ]);

                        }

                    }

                    // PRODUCT FOCUS
                    if ($summary->target_pf_da > 0) {

                        if ($data['pf'] > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_pf_da' => $summary->actual_pf_da - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pf_da' => $summary->actual_pf_da + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pf_da' => $summary->actual_pf_da + $data['value']
                                ]);

                            }
                        }
                    }

                    // WEEKLY PROCESS
                    if ($data['week'] == 1) { // WEEK 1

                        if ($summary->target_da_w1 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_da_w1' => $summary->actual_da_w1 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_da_w1' => $summary->actual_da_w1 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_da_w1' => $summary->actual_da_w1 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 2) { // WEEK 2

                        if ($summary->target_da_w2 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_da_w2' => $summary->actual_da_w2 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_da_w2' => $summary->actual_da_w2 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_da_w2' => $summary->actual_da_w2 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 3) { // WEEK 3

                        if ($summary->target_da_w3 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_da_w3' => $summary->actual_da_w3 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_da_w3' => $summary->actual_da_w3 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_da_w3' => $summary->actual_da_w3 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 4) { // WEEK 4

                        if ($summary->target_da_w4 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_da_w4' => $summary->actual_da_w4 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_da_w4' => $summary->actual_da_w4 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_da_w4' => $summary->actual_da_w4 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 5) { // WEEK 5

                        if ($summary->target_da_w5 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_da_w5' => $summary->actual_da_w5 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_da_w5' => $summary->actual_da_w5 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_da_w5' => $summary->actual_da_w5 + $data['value']
                                ]);

                            }

                        }

                    }

                    /* PC */
                } else if ($data['group'] == 'PC') {

                    if ($summary->target_pc > 0) {

                        if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                            $summary->update([ // SUBSTRACT OLD VALUE
                                'actual_pc' => $summary->actual_pc - $data['value_old'],
                                'sum_actual_store' => $sumActualStore - $data['value_old'],
                                'sum_actual_area' => $sumActualArea - $data['value_old'],
                                'sum_actual_region' => $sumActualRegion - $data['value_old'],
                            ]);

                            $summary->update([ // ADD NEW VALUE
                                'actual_pc' => $summary->actual_pc + $data['value'],
                                'sum_actual_store' => $summary->sum_actual_store + $data['value'],
                                'sum_actual_area' => $summary->sum_actual_area + $data['value'],
                                'sum_actual_region' => $summary->sum_actual_region + $data['value'],
                            ]);

                        } else { // INSERT

                            $summary->update([ // ADD NEW VALUE
                                'actual_pc' => $summary->actual_pc + $data['value'],
                                'sum_actual_store' => $sumActualStore + $data['value'],
                                'sum_actual_area' => $sumActualArea + $data['value'],
                                'sum_actual_region' => $sumActualRegion + $data['value'],
                            ]);

                        }

                    }

                    // PRODUCT FOCUS
                    if ($summary->target_pf_pc > 0) {

                        if ($data['pf'] > 0){

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_pf_pc' => $summary->actual_pf_pc - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pf_pc' => $summary->actual_pf_pc + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pf_pc' => $summary->actual_pf_pc + $data['value']
                                ]);

                            }
                        }
                    }

                    // WEEKLY PROCESS
                    if ($data['week'] == 1) { // WEEK 1

                        if ($summary->target_pc_w1 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_pc_w1' => $summary->actual_pc_w1 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pc_w1' => $summary->actual_pc_w1 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pc_w1' => $summary->actual_pc_w1 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 2) { // WEEK 2

                        if ($summary->target_pc_w2 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_pc_w2' => $summary->actual_pc_w2 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pc_w2' => $summary->actual_pc_w2 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pc_w2' => $summary->actual_pc_w2 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 3) { // WEEK 3

                        if ($summary->target_pc_w3 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_pc_w3' => $summary->actual_pc_w3 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pc_w3' => $summary->actual_pc_w3 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pc_w3' => $summary->actual_pc_w3 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 4) { // WEEK 4

                        if ($summary->target_pc_w4 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_pc_w4' => $summary->actual_pc_w4 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pc_w4' => $summary->actual_pc_w4 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pc_w4' => $summary->actual_pc_w4 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 5) { // WEEK 5

                        if ($summary->target_pc_w5 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_pc_w5' => $summary->actual_pc_w5 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pc_w5' => $summary->actual_pc_w5 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pc_w5' => $summary->actual_pc_w5 + $data['value']
                                ]);

                            }

                        }

                    }

                    /* MCC */
                } else if ($data['group'] == 'MCC') {

                    if ($summary->target_mcc > 0) {

                        if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                            $summary->update([ // SUBSTRACT OLD VALUE
                                'actual_mcc' => $summary->actual_mcc - $data['value_old'],
                                'sum_actual_store' => $sumActualStore - $data['value_old'],
                                'sum_actual_area' => $sumActualArea - $data['value_old'],
                                'sum_actual_region' => $sumActualRegion - $data['value_old'],
                            ]);

                            $summary->update([ // ADD NEW VALUE
                                'actual_mcc' => $summary->actual_mcc + $data['value'],
                                'sum_actual_store' => $summary->sum_actual_store + $data['value'],
                                'sum_actual_area' => $summary->sum_actual_area + $data['value'],
                                'sum_actual_region' => $summary->sum_actual_region + $data['value'],
                            ]);

                        } else { // INSERT

                            $summary->update([ // ADD NEW VALUE
                                'actual_mcc' => $summary->actual_mcc + $data['value'],
                                'sum_actual_store' => $sumActualStore + $data['value'],
                                'sum_actual_area' => $sumActualArea + $data['value'],
                                'sum_actual_region' => $sumActualRegion + $data['value'],
                            ]);

                        }

                    }

                    // PRODUCT FOCUS
                    if ($summary->target_pf_mcc > 0) {

                        if ($data['pf'] > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_pf_mcc' => $summary->actual_pf_mcc - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pf_mcc' => $summary->actual_pf_mcc + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_pf_mcc' => $summary->actual_pf_mcc + $data['value']
                                ]);

                            }
                        }
                    }

                    // WEEKLY PROCESS
                    if ($data['week'] == 1) { // WEEK 1

                        if ($summary->target_mcc_w1 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_mcc_w1' => $summary->actual_mcc_w1 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_mcc_w1' => $summary->actual_mcc_w1 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_mcc_w1' => $summary->actual_mcc_w1 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 2) { // WEEK 2

                        if ($summary->target_mcc_w2 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_mcc_w2' => $summary->actual_mcc_w2 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_mcc_w2' => $summary->actual_mcc_w2 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_mcc_w2' => $summary->actual_mcc_w2 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 3) { // WEEK 3

                        if ($summary->target_mcc_w3 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_mcc_w3' => $summary->actual_mcc_w3 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_mcc_w3' => $summary->actual_mcc_w3 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_mcc_w3' => $summary->actual_mcc_w3 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 4) { // WEEK 4

                        if ($summary->target_mcc_w4 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_mcc_w4' => $summary->actual_mcc_w4 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_mcc_w4' => $summary->actual_mcc_w4 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_mcc_w4' => $summary->actual_mcc_w4 + $data['value']
                                ]);

                            }

                        }

                    } else if ($data['week'] == 5) { // WEEK 5

                        if ($summary->target_mcc_w5 > 0) {

                            if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                                $summary->update([ // SUBSTRACT OLD VALUE
                                    'actual_mcc_w5' => $summary->actual_mcc_w5 - $data['value_old']
                                ]);

                                $summary->update([ // ADD NEW VALUE
                                    'actual_mcc_w5' => $summary->actual_mcc_w5 + $data['value']
                                ]);

                            } else { // INSERT

                                $summary->update([ // ADD NEW VALUE
                                    'actual_mcc_w5' => $summary->actual_mcc_w5 + $data['value']
                                ]);

                            }

                        }

                    }

                }

            } else { // DELETE

                /* DA */
                if ($data['group'] == 'DA') {

                    if ($summary->target_da > 0) {

                        $summary->update([
                            'actual_da' => $summary->actual_da - $data['value'],
                            'sum_actual_store' => $sumActualStore - $data['value'],
                            'sum_actual_area' => $sumActualArea - $data['value'],
                            'sum_actual_region' => $sumActualRegion - $data['value'],
                        ]);

                    }

                    // PRODUCT FOCUS
                    if ($summary->target_pf_da > 0) {

                        if ($data['pf'] > 0) {

                            $summary->update([
                                'actual_pf_da' => $summary->actual_pf_da - $data['value']
                            ]);

                        }
                    }

                    // WEEKLY PROCESS
                    if ($data['week'] == 1) { // WEEK 1

                        if ($summary->target_da_w1 > 0) {

                            $summary->update([
                                'actual_da_w1' => $summary->actual_da_w1 - $data['value']
                            ]);

                        }

                    } else if ($data['week'] == 2) { // WEEK 2

                        if ($summary->target_da_w2 > 0) {

                            $summary->update([
                                'actual_da_w2' => $summary->actual_da_w2 - $data['value']
                            ]);


                        }

                    } else if ($data['week'] == 3) { // WEEK 3

                        if ($summary->target_da_w3 > 0) {

                            $summary->update([
                                'actual_da_w3' => $summary->actual_da_w3 - $data['value']
                            ]);

                        }

                    } else if ($data['week'] == 4) { // WEEK 4

                        if ($summary->target_da_w4 > 0) {

                            $summary->update([
                                'actual_da_w4' => $summary->actual_da_w4 - $data['value']
                            ]);

                        }

                    } else if ($data['week'] == 5) { // WEEK 5

                        if ($summary->target_da_w5 > 0) {

                            $summary->update([
                                'actual_da_w5' => $summary->actual_da_w5 - $data['value']
                            ]);

                        }

                    }

                    /* PC */
                } else if ($data['group'] == 'PC') {

                    if ($summary->target_pc > 0) {

                        $summary->update([
                            'actual_pc' => $summary->actual_pc - $data['value'],
                            'sum_actual_store' => $sumActualStore - $data['value'],
                            'sum_actual_area' => $sumActualArea - $data['value'],
                            'sum_actual_region' => $sumActualRegion - $data['value'],
                        ]);

                    }

                    // PRODUCT FOCUS
                    if ($summary->target_pf_pc > 0) {

                        if ($data['pf'] > 0){
                            $summary->update([
                                'actual_pf_pc' => $summary->actual_pf_pc - $data['value']
                            ]);
                        }
                    }

                    // WEEKLY PROCESS
                    if ($data['week'] == 1) { // WEEK 1

                        if ($summary->target_pc_w1 > 0) {

                            $summary->update([
                                'actual_pc_w1' => $summary->actual_pc_w1 - $data['value']
                            ]);

                        }

                    } else if ($data['week'] == 2) { // WEEK 2

                        if ($summary->target_pc_w2 > 0) {

                            $summary->update([
                                'actual_pc_w2' => $summary->actual_pc_w2 - $data['value']
                            ]);

                        }

                    } else if ($data['week'] == 3) { // WEEK 3

                        if ($summary->target_pc_w3 > 0) {

                            $summary->update([
                                'actual_pc_w3' => $summary->actual_pc_w3 - $data['value']
                            ]);

                        }

                    } else if ($data['week'] == 4) { // WEEK 4

                        if ($summary->target_pc_w4 > 0) {

                            $summary->update([
                                'actual_pc_w4' => $summary->actual_pc_w4 - $data['value']
                            ]);

                        }

                    } else if ($data['week'] == 5) { // WEEK 5

                        if ($summary->target_pc_w5 > 0) {

                            $summary->update([
                                'actual_pc_w5' => $summary->actual_pc_w5 - $data['value']
                            ]);

                        }

                    }

                    /* MCC */
                } else if ($data['group'] == 'MCC') {

                    if ($summary->target_mcc > 0) {

                        $summary->update([
                            'actual_mcc' => $summary->actual_mcc - $data['value'],
                            'sum_actual_store' => $sumActualStore - $data['value'],
                            'sum_actual_area' => $sumActualArea - $data['value'],
                            'sum_actual_region' => $sumActualRegion - $data['value'],
                        ]);

                    }

                    // PRODUCT FOCUS
                    if ($summary->target_pf_mcc > 0) {

                        if ($data['pf'] > 0) {

                            $summary->update([
                                'actual_pf_mcc' => $summary->actual_pf_mcc - $data['value']
                            ]);

                        }
                    }

                    // WEEKLY PROCESS
                    if ($data['week'] == 1) { // WEEK 1

                        if ($summary->target_mcc_w1 > 0) {

                            $summary->update([
                                'actual_mcc_w1' => $summary->actual_mcc_w1 - $data['value']
                            ]);

                        }

                    } else if ($data['week'] == 2) { // WEEK 2

                        if ($summary->target_mcc_w2 > 0) {

                            $summary->update([
                                'actual_mcc_w2' => $summary->actual_mcc_w2 - $data['value']
                            ]);

                        }

                    } else if ($data['week'] == 3) { // WEEK 3

                        if ($summary->target_mcc_w3 > 0) {

                            $summary->update([
                                'actual_mcc_w3' => $summary->actual_mcc_w3 - $data['value']
                            ]);

                        }

                    } else if ($data['week'] == 4) { // WEEK 4

                        if ($summary->target_mcc_w4 > 0) {

                            $summary->update([
                                'actual_mcc_w4' => $summary->actual_mcc_w4 - $data['value']
                            ]);

                        }

                    } else if ($data['week'] == 5) { // WEEK 5

                        if ($summary->target_mcc_w5 > 0) {

                                $summary->update([
                                    'actual_mcc_w5' => $summary->actual_mcc_w5 - $data['value']
                                ]);

                        }

                    }

                }

            }

            // Update Sum Target Store to All Summary
            $sumStore->update([
                'sum_actual_store' => $summary->sum_actual_store,
            ]);

            $sumArea->update([
                'sum_actual_area' => $summary->sum_actual_area,
            ]);

            $sumRegion->update([
                'sum_actual_region' => $summary->sum_actual_region,
            ]);

            /* Check if Promoter was hybrid or not */
            if ($summary->title_of_promoter == 'HYBRID') {

                if ($summary->target_dapc > 0) {
                    $summary->update([
                        'actual_dapc' => $summary->actual_da + $summary->actual_pc
                    ]);
                }

            } else {

                if ($summary->actual_dapc > 0) {
                    $summary->update([
                        'actual_dapc' => 0
                    ]);
                }

            }

        }

    }

    public function resetActual($userId, $storeId, $sellType){

        /* Delete Actual Data in Summary Target Actuals */
        $summary_ta = SummaryTargetActual::where('user_id', $userId)->where('storeId', $storeId)
            ->where('sell_type', $sellType)->first();

        if($summary_ta) {

            $sumActualStore = SummaryTargetActual::where('storeId', $summary_ta->storeId)->where('sell_type', $sellType)->where('id', '!=', $summary_ta->id)->first();
            $sumActualArea = SummaryTargetActual::where('area_id', $summary_ta->area_id)->where('sell_type', $sellType)->where('id', '!=', $summary_ta->id)->first();
            $sumActualRegion = SummaryTargetActual::where('region_id', $summary_ta->region_id)->where('sell_type', $sellType)->where('id', '!=', $summary_ta->id)->first();

            $totalActual = $summary_ta->actual_da + $summary_ta->actual_pc + $summary_ta->actual_mcc;

            $sumActualStore = ($sumActualStore) ? $sumActualStore->sum_actual_store - $totalActual : 0;
            $sumActualArea = ($sumActualArea) ? $sumActualArea->sum_actual_area - $totalActual : 0;
            $sumActualRegion = ($sumActualRegion) ? $sumActualRegion->sum_actual_region - $totalActual : 0;

            /* Delete Actual Data */
            $summary_ta->update([
                'actual_dapc' => 0,
                'actual_da' => 0,
                'actual_pc' => 0,
                'actual_mcc' => 0,
                'actual_pf_da' => 0,
                'actual_pf_pc' => 0,
                'actual_pf_mcc' => 0,
                'actual_da_w1' => 0,
                'actual_da_w2' => 0,
                'actual_da_w3' => 0,
                'actual_da_w4' => 0,
                'actual_da_w5' => 0,
                'actual_pc_w1' => 0,
                'actual_pc_w2' => 0,
                'actual_pc_w3' => 0,
                'actual_pc_w4' => 0,
                'actual_pc_w5' => 0,
                'actual_mcc_w1' => 0,
                'actual_mcc_w2' => 0,
                'actual_mcc_w3' => 0,
                'actual_mcc_w4' => 0,
                'actual_mcc_w5' => 0,
                'sum_actual_store' => $sumActualStore,
                'sum_actual_area' => $sumActualArea,
                'sum_actual_region' => $sumActualRegion,
            ]);

            /* Update all summary for store, area, region */
            $sumStore = SummaryTargetActual::where('storeId', $summary_ta->storeId)->where('sell_type', $sellType);
            $sumArea = SummaryTargetActual::where('area_id', $summary_ta->area_id)->where('sell_type', $sellType);
            $sumRegion = SummaryTargetActual::where('region_id', $summary_ta->region_id)->where('sell_type', $sellType);

            $sumStore->update([
                'sum_actual_store' => $sumActualStore,
            ]);

            $sumArea->update([
                'sum_actual_area' => $sumActualArea,
            ]);

            $sumRegion->update([
                'sum_actual_region' => $sumActualRegion,
            ]);

            /* Add Summary from User */
            $summaryData = SummarySellIn::where('user_id', $userId)->where('storeId', $storeId)->get();

            if ($sellType == 'Sell Out') {
                $summaryData = SummarySellOut::where('user_id', $userId)->where('storeId', $storeId)->get();
            }

            if ($summaryData) {

                foreach ($summaryData as $data) {

                    $detail['user_id'] = $userId;
                    $detail['store_id'] = $storeId;
                    $detail['week'] = $data['week'];
                    $detail['pf'] = $data['value_pf_mr'] + $data['value_pf_tr'] + $data['value_pf_ppe'];
                    $detail['value'] = $data['value'];
                    $detail['group'] = $data['group'];
                    $detail['sell_type'] = $sellType;

                    $this->changeActual($detail, 'change');

                }

            }

        }

    }

    public function changeActualSalesman($data, $change)
    {

        $summary = SalesmanSummaryTargetActual::where('user_id', $data['user_id'])->first();

        if ($summary) {

            /* Target Add and/or Sum */
            $targetOther = SalesmanSummaryTargetActual::where('id', '!=', $summary->id);
            $sumNationalActualActiveOutlet = SalesmanSummaryTargetActual::first()->sum_national_actual_active_outlet;
            $sumNationalActualEffectiveCall = SalesmanSummaryTargetActual::first()->sum_national_actual_effective_call;
            $sumNationalActualSales = SalesmanSummaryTargetActual::first()->sum_national_actual_sales;
            $sumNationalActualSalesPf = SalesmanSummaryTargetActual::first()->sum_national_actual_sales_pf;

            /* Add / Sum All Target */
            if ($change == 'change') { // INSERT / UPDATE

                if ($summary->target_sales > 0) {

                    if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                        $summary->update([ // SUBSTRACT OLD VALUE
                            'actual_sales' => $summary->actual_sales - $data['value_old'],
                            'sum_national_actual_sales' => $sumNationalActualSales - $data['value_old'],
                        ]);

                        $summary->update([ // ADD NEW VALUE
                            'actual_sales' => $summary->actual_sales + $data['value'],
                            'sum_national_actual_sales' => $summary->sum_national_actual_sales + $data['value'],
                        ]);

                    } else { // INSERT

                        $summary->update([ // ADD NEW VALUE
                            'actual_sales' => $summary->actual_sales + $data['value'],
                            'sum_national_actual_sales' => $sumNationalActualSales + $data['value'],
                        ]);

                    }

                }

                // PRODUCT FOCUS
                if ($summary->target_sales_pf > 0) {

                    if ($data['pf'] > 0) {

                        if (isset($data['value_old']) && $data['value_old'] > 0) { // UPDATE

                            $summary->update([ // SUBSTRACT OLD VALUE
                                'actual_sales_pf' => $summary->actual_sales_pf - $data['value_old'],
                                'sum_national_actual_sales_pf' => $sumNationalActualSalesPf - $data['value_old'],
                            ]);

                            $summary->update([ // ADD NEW VALUE
                                'actual_sales_pf' => $summary->actual_sales_pf + $data['value'],
                                'sum_national_actual_sales_pf' => $summary->sum_national_actual_sales_pf + $data['value'],
                            ]);

                        } else { // INSERT

                            $summary->update([ // ADD NEW VALUE
                                'actual_sales_pf' => $summary->actual_sales_pf + $data['value'],
                                'sum_national_actual_sales_pf' => $sumNationalActualSalesPf + $data['value'],
                            ]);

                        }
                    }
                }

                // EFFECTIVE CALL
                $countEC = SalesmanSummarySales::where('user_id', $data['user_id'])->where('storeId', $data['store_id'])
                    ->whereDate('date', Carbon::now()->format('Y-m-d'))->count();

                if ($countEC == 1) { // 1 Count Per Transaction Per Day
                    if (!isset($data['value_old'])) {
                        $summary->update([ // ADD NEW VALUE
                            'actual_effective_call' => $summary->actual_effective_call + 1,
                            'sum_national_actual_effective_call' => $sumNationalActualEffectiveCall + 1,
                        ]);
                    }
                }

                // ACTIVE OUTLET
                $countAO = SalesmanSummarySales::where('user_id', $data['user_id'])->where('storeId', $data['store_id'])
                    ->whereMonth('date', Carbon::now()->format('m'))->whereYear('date', Carbon::now()->format('Y'))->count();

                if ($countAO == 1) { // 1 Count Per Transaction Per Month
                    if (!isset($data['value_old'])) {
                        $summary->update([ // ADD NEW VALUE
                            'actual_active_outlet' => $summary->actual_active_outlet + 1,
                            'sum_national_actual_active_outlet' => $sumNationalActualActiveOutlet + 1,
                        ]);
                    }
                }


            } else { // DELETE
                // ON PROGRESS
            }

            // Update Sum Target Store to All Summary
            $targetOther->update([
                'sum_national_actual_call' => $summary->sum_national_actual_call,
                'sum_national_actual_active_outlet' => $summary->sum_national_actual_active_outlet,
                'sum_national_actual_effective_call' => $summary->sum_national_actual_effective_call,
                'sum_national_actual_sales' => $summary->sum_national_actual_sales,
                'sum_national_actual_sales_pf' => $summary->sum_national_actual_sales_pf,
            ]);


        }

    }

    public function resetActualSalesman($userId){

        /* Delete Actual Data in Summary Target Actuals */
        $summary_ta = SalesmanSummaryTargetActual::where('user_id', $userId)->first();

        if($summary_ta) {

            $sumNationalActualCall = SalesmanSummaryTargetActual::first()->sum_national_actual_call;
            $sumNationalActualActiveOutlet = SalesmanSummaryTargetActual::first()->sum_national_actual_active_outlet;
            $sumNationalActualEffectiveCall = SalesmanSummaryTargetActual::first()->sum_national_actual_effective_call;
            $sumNationalActualSales = SalesmanSummaryTargetActual::first()->sum_national_actual_sales;
            $sumNationalActualSalesPf = SalesmanSummaryTargetActual::first()->sum_national_actual_sales_pf;

            $sumNationalActualCall = ($sumNationalActualCall) ? $sumNationalActualCall - $summary_ta->sum_national_actual_call : 0;
            $sumNationalActualActiveOutlet = ($sumNationalActualActiveOutlet) ? $sumNationalActualActiveOutlet - $summary_ta->sum_national_actual_active_outlet : 0;
            $sumNationalActualEffectiveCall = ($sumNationalActualEffectiveCall) ? $sumNationalActualEffectiveCall - $summary_ta->sum_national_actual_effective_call : 0;
            $sumNationalActualSales = ($sumNationalActualSales) ? $sumNationalActualSales - $summary_ta->sum_national_actual_sales : 0;
            $sumNationalActualSalesPf = ($sumNationalActualSalesPf) ? $sumNationalActualSalesPf - $summary_ta->sum_national_actual_sales_pf : 0;

            /* Delete Actual Data */
            $summary_ta->update([
                'actual_call' => 0,
                'actual_active_outlet' => 0,
                'actual_effective_call' => 0,
                'actual_sales' => 0,
                'actual_sales_pf' => 0,
                'sum_national_actual_call' => $sumNationalActualCall,
                'sum_national_actual_active_outlet' => $sumNationalActualActiveOutlet,
                'sum_national_actual_effective_call' => $sumNationalActualEffectiveCall,
                'sum_national_actual_sales' => $sumNationalActualSales,
                'sum_national_actual_sales_pf' => $sumNationalActualSalesPf,
            ]);

            // Update Sum Target Store to All Summary
            $targetOther = SalesmanSummaryTargetActual::where('id', '!=', $summary_ta->id);

            $targetOther->update([
                'sum_national_target_call' => $summary_ta->sum_national_target_call,
                'sum_national_target_active_outlet' => $summary_ta->sum_national_target_active_outlet,
                'sum_national_target_effective_call' => $summary_ta->sum_national_target_effective_call,
                'sum_national_target_sales' => $summary_ta->sum_national_target_sales,
                'sum_national_target_sales_pf' => $summary_ta->sum_national_target_sales_pf,
            ]);

            /* Add Summary from User */
            $summaryData = SalesmanSummarySales::where('user_id', $userId)->get();

            if ($summaryData) {

                foreach ($summaryData as $data) {

                    $detail['user_id'] = $userId;
                    $detail['store_id'] = $data['storeId'];
                    $detail['pf'] = $data['value_pf'];
                    $detail['value'] = $data['value'];

                    $this->changeActualSalesman($detail, 'change');

                }

            }

            // CALL, EFFECTIVE CALL, ACTIVE OUTLET
            $storeIds = EmployeeStore::where('user_id', $userId)->pluck('store_id');
            $store = Store::whereIn('id', $storeIds)->get();

            $call = 0;
            $effectiveCall = 0;
            $activeOutlet = 0;

            $attendance = Attendance::where('user_id', $userId)->whereMonth('date', Carbon::now()->format('m'))
                ->whereYear('date', Carbon::now()->format('Y'))->get();

            if($attendance) {

                foreach ($attendance as $header) {

                    // CALL
                    $attendanceDetailCount = AttendanceDetail::where('attendance_id', $header->id)->count();
                    $call += $attendanceDetailCount;

                    // EFFECTIVE CALL
                    if ($store) { // If has store

                        foreach ($store as $data){

                            $transactionCount = SalesmanSummarySales::where('user_id',  $userId)->where('storeId', $data->id)
                                ->whereDate('date', $header->date)->count();

                            if($transactionCount >= 1){
                                $effectiveCall += 1;
                            }

                        }

                    }

                }

            }

            // ACTIVE OUTLET
            if($store){

                foreach ($store as $data){

                     $transactionCount = SalesmanSummarySales::where('user_id',  $userId)->where('storeId', $data->id)
                                ->whereMonth('date', Carbon::now()->format('m'))->count();

                    if($transactionCount >= 1){
                        $activeOutlet += 1;
                    }

                }

            }

            // UPDATE CALL, EC, AO
            $summary_ta->update([
                'actual_call' => $summary_ta->actual_call + $call,
                'actual_active_outlet' => $summary_ta->actual_active_outlet + $activeOutlet,
                'actual_effective_call' => $summary_ta->effective_call + $effectiveCall,
                'sum_national_actual_call' => $summary_ta->sum_national_actual_call + $call,
                'sum_national_actual_active_outlet' => $summary_ta->sum_national_actual_active_outlet + $activeOutlet,
                'sum_national_actual_effective_call' => $summary_ta->sum_national_actual_effective_call + $effectiveCall,
            ]);

            // Update Sum Target Store to All Summary
            $targetOther = SalesmanSummaryTargetActual::where('id', '!=', $summary_ta->id);

            $targetOther->update([
                'sum_national_actual_call' => $summary_ta->sum_national_actual_call,
                'sum_national_actual_active_outlet' => $summary_ta->sum_national_actual_active_outlet,
                'sum_national_actual_effective_call' => $summary_ta->sum_national_actual_effective_call,
            ]);

        }

    }

    public function changeActualCall($userId){

        $summary = SalesmanSummaryTargetActual::where('user_id', $userId)->first();

        if($summary){

            $summary->update([
                'actual_call' => $summary->actual_call + 1,
                'sum_national_actual_call' => $summary->sum_national_actual_call + 1,
            ]);

            // Update Sum Target Store to All Summary
            $targetOther = SalesmanSummaryTargetActual::where('id', '!=', $summary->id);

            $targetOther->update([
                'sum_national_actual_call' => $summary->sum_national_actual_call,
            ]);

        }

    }

}