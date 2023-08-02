<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InformasiModel;
use Illuminate\Support\Str;

use DB;

class InformasiController extends Controller
{
    //
    public function get_informasi(Request $request)
    {
        //
        try {

            //
            $modelInformasi = DB::table('informasi')->get();

            //
            return [
                "success" => true,
                "message" => "Success get informasi",
                "data" => $modelInformasi
            ];

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function post_informasi(Request $request)
    {
        // validate request
        $this->validate($request, [
            'url' => 'required|string',
            'title' => 'required|string',
            'desc_informasi' => 'required|string',
        ]);

        $url = $request->input('url');
        $title = $request->input('title');
        $desc_informasi = $request->input('desc_informasi');

        //
        try {

            //
            $modelInformasi = new InformasiModel();
            $modelInformasi->id = Str::uuid()->toString();
            $modelInformasi->url = $url;
            $modelInformasi->title = $title;
            $modelInformasi->desc_informasi = $desc_informasi;

            if ($modelInformasi->save()) {
                //
                return [
                    "success" => true,
                    "message" => "Success submit informasi",
                    "data" => $modelInformasi
                ];
            }

        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}