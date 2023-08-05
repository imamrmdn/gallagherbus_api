<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KoridorModel;

use DB;

class JadwalController extends Controller
{
    //
    function algorithm_greedy($req)
    {
        try {
            //code...
            // Pengurutan jadwal di setiap halte berdasarkan arrival_time_bus
            foreach ($req['koridor_schedule'] as &$schedule) {
                usort($schedule['schedule_koridor'], function ($a, $b) {
                    return strcmp($a['arrival_time_bus'], $b['arrival_time_bus']);
                });
            }

            // Mengurutkan data halte berdasarkan arrival_time_in_halte
            usort($req['koridor_schedule'], function ($a, $b) {
                return strcmp($a['arrival_time_in_halte'], $b['arrival_time_in_halte']);
            });

            // Memanipulasi jadwal bus agar tidak bentrok
            foreach ($req['koridor_schedule'] as &$schedule) {
                $scheduledBuses = [];
                $scheduledBuses[] = $schedule['schedule_koridor'][0];

                $lastBusIndex = 0;
                $totalBuses = count($schedule['schedule_koridor']);

                for ($i = 1; $i < $totalBuses; $i++) {
                    $currentBus = $schedule['schedule_koridor'][$i];
                    $lastScheduledBus = $scheduledBuses[$lastBusIndex];

                    if ($currentBus['arrival_time_bus'] >= $lastScheduledBus['departure_time_bus']) {
                        $scheduledBuses[] = $currentBus;
                        $lastBusIndex = $i;
                    }
                }

                $schedule['schedule_koridor'] = $scheduledBuses;
            }

            return $req;

        } catch (\Throwable $th) {
            //throw $th;
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }


    //
    public function proccess_to_algorithm(Request $request)
    {
        //
        $this->validate($request, [
            'koridor_name' => 'required|string',
            'koridor_schedule' => 'required|array'
        ]);

        $inputData = $request->all();

        // output data after process in algorithm greedy
        $data = $this->algorithm_greedy($inputData);

        //
        try {
            //
            DB::beginTransaction();

            // cek if koridor is exist
            $findKoridorName = DB::table('koridors')->where('koridor_name', $data['koridor_name'])->select(['koridor_name'])->get();

            if ($findKoridorName->isEmpty()) {

                // Simpan Koridor
                $koridor = KoridorModel::create([
                    'koridor_name' => $data['koridor_name'],
                ]);

                foreach ($data['koridor_schedule'] as $key => $koridorScheduleData) {
                    // Simpan Halte
                    $halte = $koridor->halte()->create([
                        'halte_name' => $koridorScheduleData['halte_name'],
                        'departure_time_in_halte' => $koridorScheduleData['departure_time_in_halte'],
                        'arrival_time_in_halte' => $koridorScheduleData['arrival_time_in_halte'],
                    ]);

                    foreach ($koridorScheduleData['schedule_koridor'] as $index => $scheduleData) {
                        // Simpan HalteSchedule
                        $halte->halte_schedule()->create([
                            'bus_queue' => $scheduleData['bus_queue'],
                            'bus_name' => $scheduleData['bus_name'],
                            'departure_time_bus' => $scheduleData['departure_time_bus'],
                            'arrival_time_bus' => $scheduleData['arrival_time_bus'],
                        ]);
                    }
                }

                DB::commit();

                return [
                    "success" => true,
                    "status" => 200,
                    "message" => "Success Process Data to Algorithm Greedy"
                ];

            } else {
                DB::rollBack();

                $responseError = [
                    "success" => false,
                    "status" => 500,
                    "message" => "Failed Process Data to Algorithm Greedy"
                ];

                return response()->json($responseError, 500);
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                "success" => false,
                "message" => $th->getMessage()
            ];
        }
    }

    //
    public function get_jadwal(Request $request)
    {

        try {

            $modelKoridors = KoridorModel::with([
                'halte' => function ($query) {
                    $query->with(['halte_schedule'])->get();
                }
            ])->select(['id', 'koridor_name'])->get();

            if (!$modelKoridors->isEmpty()) {

                return response()->json([
                    "success" => true,
                    "status" => 200,
                    "message" => "Success get jadwal all koridor",
                    "data" => $modelKoridors
                ], 200);

            } else {
                return response()->json([
                    "success" => false,
                    "status" => 404,
                    "message" => "Jadwal koridor not yet",
                    "data" => $modelKoridors
                ], 404);
            }


        } catch (\Throwable $th) {
            return [
                "success" => false,
                "message" => $th->getMessage()
            ];
        }

    }

    //
    public function get_koridor(Request $request)
    {
        // validate request
        $this->validate($request, [
            'koridor_name' => 'required|string',
            'all' => 'required|integer'
        ]);

        $koridor_name = $request->input('koridor_name');
        $all = (int) $request->input('all');

        //
        try {

            $modelKoridors = null;

            // cek if koridors is exist
            $findKoridorName = DB::table('koridors')->where('koridor_name', $koridor_name)->select(['koridor_name'])->get();

            if (!$findKoridorName->isEmpty()) {
                if (empty($all)) {
                    $modelKoridors = DB::table('koridors')->where('koridor_name', $koridor_name)->select(['id', 'koridor_name'])->get();
                    $message = "Success get koridor name";
                } else {
                    $modelKoridors = KoridorModel::with([
                        'halte' => function ($query) {
                            $query->with(['halte_schedule'])->get();
                        }
                    ])->where('koridor_name', $koridor_name)->select(['id', 'koridor_name'])->get();
                    $message = "Success get koridor";
                }

                if (empty($modelKoridors))
                    $message = "Data koridors not found";

                //
                $responseSuccess = [
                    "success" => true,
                    "status" => 200,
                    "message" => $message,
                    "data" => $modelKoridors
                ];

                return response()->json($responseSuccess, 200);

            } else {
                //
                $responseError = [
                    "success" => false,
                    "status" => 404,
                    "message" => 'Koridors not found',
                    "data" => []
                ];

                return response()->json($responseError, 200);
            }

        } catch (\Throwable $th) {
            return [
                "success" => false,
                "message" => $th->getMessage()
            ];
        }
    }

    //
    public function get_all_koridor_name(Request $request)
    {
        try {
            //code...
            $data = DB::table('koridors')->select(['id', 'koridor_name'])->orderBy('created_at', 'desc')->get();

            return [
                "success" => true,
                "status" => 200,
                "message" => 'Success get all koridor name',
                "data" => $data
            ];

        } catch (\Throwable $th) {
            //throw $th;
            return [
                "success" => false,
                "message" => $th->getMessage()
            ];
        }
    }

}