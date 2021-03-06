<?php

namespace App\Http\Controllers\Api\Master;

use App\AttendanceDetail;
use App\Reports\SummaryTargetActual;
use App\Store;
use App\Place;
use App\Traits\TargetTrait;
use App\VisitPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\Traits\UploadTrait;
use App\Traits\StringTrait;
use Geotools;
use App\Attendance;
use DB;

class AttendanceController extends Controller
{
    use TargetTrait;

    public function attendance(Request $request, $param){

        // Decode buat inputan raw body
        $content = json_decode($request->getContent(), true);
        $user = JWTAuth::parseToken()->authenticate();

        // Check header
        $attendanceHeader = Attendance::where('user_id', $user->id)->where('date', '=', date('Y-m-d'))->first();

//        return response()->json($attendanceHeader);

        // Response if header was not set (command -> init:attendance)
        if($user->role == 'Supervisor' || $user->role == 'Supervisor Hybrid' || $user->role == 'DM' || $user->role == 'Trainer' || $user->role == 'RSM' || $user->role == 'Salesman Explorer'){

            if(!$attendanceHeader) {
                $attendanceHeader = Attendance::create([
                    'user_id' => $user->id,
                    'date' => Carbon::now(),
                    'status' => 'Alpha'
                ]);
            }

        }else{

            if(!$attendanceHeader) {
                return response()->json(['status' => false, 'message' => 'Tidak menemukan data absensi anda, silahkan hubungi administrator'], 500);
            }

        }


        // Count Attendance Details
        $attendanceDetailsCount = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->count();

        /*
         * Checking mode of attendance input
         * 1 => Check In
         * 2 => Check Out
         * 3 => Sakit
         * 4 => Izin
         * 5 => Off
         */

        if($param == 1){ /* CHECK IN */

            if($user->role != 'Salesman Explorer') {

                if ($content['is_store'] == 1) {
                    $location = Store::where('id', $content['id'])->first();
                } else if ($content['is_store'] == 0) {
                    $location = Place::where('id', $content['id'])->first();
                }

                // Return if location longi and lati was not set
                if ($location->longitude == null || $location->latitude == null) {
                    return response()->json(['status' => false, 'message' => 'Tempat absensi yang anda pilih belum terkonfigurasi, silahkan hubungi administrator'], 500);
                }

                $coordStore = Geotools::coordinate([$location->latitude, $location->longitude]);
                $coordNow = Geotools::coordinate([$content['latitude'], $content['longitude']]);
                $distance = Geotools::distance()->setFrom($coordStore)->setTo($coordNow)->flat();

                // Check distance if above 250 meter(s)
                if ($distance > 250) {
                    return response()->json(['status' => false, 'message' => 'Jarak anda terlalu jauh dari tempat absensi'], 200);
                }

            }

            // If promoter still didn't do check out
            if($attendanceDetailsCount > 0){

                // Get last attendance detail
                $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                if($attendanceDetail->check_out == null){
                    return response()->json(['status' => false, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 200);
                }

            }

            // Add attendance detail
            try {
                DB::transaction(function () use ($content, $attendanceHeader, $user) {

                    // Attendance Header Update
                    $attendanceHeader->update([
                        'status' => 'Masuk'
                    ]);

                    $detail = ($content['other_store'] == 1) ? 'User melakukan absensi di toko lain' : null;

                    // Attendance Detail Add
                    AttendanceDetail::create([
                        'attendance_id' => $attendanceHeader->id,
                        'store_id' => $content['id'],
                        'is_store' => $content['is_store'],
                        'check_in' => Carbon::now(),
                        'check_in_longitude' => $content['longitude'],
                        'check_in_latitude' => $content['latitude'],
                        'check_in_location' => $content['location'],
                        'detail' => $detail
                    ]);

                    // Change Actual Call - SEE
                    $this->changeActualCall($user->id);

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
            }

            if($user->role == 'Salesman Explorer' || $user->role == 'Supervisor' || $user->role == 'Supervisor Hybrid') {

                /* Set Visit Status */
                $visit = VisitPlan::where('user_id', $user->id)->where('store_id', $content['id'])->where('date', Carbon::now()->format('Y-m-d'))->first();

                if ($visit) {
                    $visit->update(['visit_status' => 1]);
                }

            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi berhasil (Check In)']);

        } elseif ($param == 2){ /* CHECK OUT */

            // If promoter hasn't data
            if($attendanceDetailsCount == 0){
                return response()->json(['status' => false, 'message' => 'Anda belum berada dalam status check in'], 200);
            }

            // Get last attendance detail
            $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

            // If promoter hasn't check in
            if($attendanceDetailsCount > 0){

                if($attendanceDetail->check_out != null){
                    return response()->json(['status' => false, 'message' => 'Anda belum berada dalam status check in'], 200);
                }

            }

            // Update attendance detail
            try {
                DB::transaction(function () use ($content, $attendanceDetail) {

                    // Attendance Detail Update
                    $attendanceDetail->update([
                        'check_out' => Carbon::now(),
                        'check_out_longitude' => $content['longitude'],
                        'check_out_latitude' => $content['latitude'],
                        'check_out_location' => $content['location']
                    ]);


                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Check Out)']);

        } elseif ($param == 3){ /* SAKIT */

            // Check if promoter has approvement
            if($attendanceHeader->status == 'Sakit'){
                return response()->json(['status' => false, 'message' => 'Status anda (sakit) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 200);
            }
            if($attendanceHeader->status == 'Izin'){
                return response()->json(['status' => false, 'message' => 'Status anda (izin) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 200);
            }

            // If promoter has attendance data
            if($attendanceDetailsCount > 0){

                // Get last attendance detail
                $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                if($attendanceDetail->check_out == null){
                    return response()->json(['status' => false, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 200);
                }

            }

            // Update if no data in attendance detail
            if($attendanceDetailsCount == 0) {
                // Update attendance header
                try {
                    DB::transaction(function () use ($content, $attendanceHeader) {

                        // Attendance Header Update
                        $attendanceHeader->update([
                            'status' => 'Pending Sakit',
                        ]);

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                }

                return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Sakit : masih dalam tahap verifikasi)']);
            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda sudah terhitung masuk untuk hari ini, status sakit tidak akan terhitung didalam data absensi']);

        } elseif ($param == 4){ /* IZIN */

            // Check if promoter has approvement
            if($attendanceHeader->status == 'Sakit'){
                return response()->json(['status' => false, 'message' => 'Status anda (sakit) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 200);
            }
            if($attendanceHeader->status == 'Izin'){
                return response()->json(['status' => false, 'message' => 'Status anda (izin) telah diverifikasi, anda tidak bisa mengganti status anda ke (sakit atau izin)'], 200);
            }

            // If promoter has attendance data
            if($attendanceDetailsCount > 0){

                // Get last attendance detail
                $attendanceDetail = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC')->first();

                if($attendanceDetail->check_out == null){
                    return response()->json(['status' => false, 'message' => 'Anda masih berada dalam status check in, silahkan check out terlebih dahulu'], 200);
                }

            }

            // Update if no data in attendance detail
            if($attendanceDetailsCount == 0) {
                // Update attendance header
                try {
                    DB::transaction(function () use ($content, $attendanceHeader) {

                        // Attendance Header Update
                        $attendanceHeader->update([
                            'status' => 'Pending Izin',
                        ]);

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
                }

                return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Izin : masih dalam tahap verifikasi)']);
            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Anda sudah terhitung masuk untuk hari ini, status izin tidak akan terhitung didalam data absensi']);

        } elseif ($param == 5){ /* OFF || LIBUR */

            // Check if promoter has already in off status
            if($attendanceHeader->status == 'Off'){
                return response()->json(['status' => false, 'message' => 'Anda sudah berada dalam status off(libur)'], 200);
            }

            // Promoter can set status to 'Off' just if in 'Alpha'
            if($attendanceHeader->status != 'Alpha' && $attendanceHeader->status != 'Pending Sakit' && $attendanceHeader->status != 'Pending Izin'){
                return response()->json(['status' => false, 'message' => 'Maaf, anda tidak bisa mengganti status menjadi off(libur)'], 200);
            }

            try {
                DB::transaction(function () use ($content, $attendanceHeader) {

                    // Attendance Header Update
                    $attendanceHeader->update([
                        'status' => 'Off'
                    ]);

                });
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => 'Gagal melakukan absensi'], 500);
            }

            /* Change Weekly Target */
            $target = SummaryTargetActual::where('user_id', $user->id)->get();

            if($target){ // If Had

                foreach ($target as $data){

                    /* Change Weekly Target */
                    $total['da'] = $data['target_da'];
                    $total['pc'] = $data['target_pc'];
                    $total['mcc'] = $data['target_mcc'];

                    $this->changeWeekly($data, $total);

                }

            }

            return response()->json(['status' => true, 'id_attendance' => $attendanceHeader->id, 'message' => 'Absensi Berhasil (Off)']);
        }

    }

    /* GET DISTANCE METHOD */
    public function getDistance( $latitude1, $longitude1, $latitude2, $longitude2 ) {
        $earth_radius = 6371;

        $dLat = deg2rad( $latitude2 - $latitude1 );
        $dLon = deg2rad( $longitude2 - $longitude1 );

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;

        return $d;
    }

    /* Check if promoter had checked in */
    public function getCheckIn(){

        $user = JWTAuth::parseToken()->authenticate();

        $attendanceHeader = Attendance::where('user_id', $user->id)->where('date', '=', date('Y-m-d'))->first();

        if($attendanceHeader){

            $attendanceDetails = AttendanceDetail::where('attendance_id', $attendanceHeader->id)->orderBy('id', 'DESC');

            if($attendanceDetails->count() > 0){

                if($attendanceDetails->first()->check_out == null) {

                    $store = Store::find($attendanceDetails->first()->store_id);

                    return response()->json(['status' => true, 'id_store' => $store->id, 'nama_store' => $store->store_name_1, 'jam_check_in' => $attendanceDetails->first()->check_in]);
                }

            }

        }

        return response()->json(['status' => false, 'message' => 'Tidak berada dalam status check in']);

    }

    public function getTotalHK($id){

        $user = User::where('id', $id)->first();

        $countHK = Attendance::where('user_id', $user->id)
                    ->whereMonth('date', Carbon::now()->format('m'))
                    ->whereYear('date', Carbon::now()->format('Y'))
                    ->whereDate('date', '<=', Carbon::now()->format('Y-m-d'))
                    ->where('status', '<>', 'Off')->count('id');

        if($countHK > 26){
            $countHK = 26;
        }

        return $countHK;

    }

}
