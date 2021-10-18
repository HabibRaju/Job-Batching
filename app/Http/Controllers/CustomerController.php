<?php

namespace App\Http\Controllers;

use App\Jobs\ImportCsv;
use App\Jobs\SalesCsvProcess;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CustomerController extends Controller
{
    public function index()
    {
        return view('upload_file');
    }

    public function upload(Request $request)
    {
        if($request->has('mycsv')){
            // $data = array_map('str_getcsv', file($request->mycsv));
            $data = file($request->mycsv);
            $parts = array_chunk($data, 1000);

            //1000 data convert to new csv file
            foreach($parts as $key => $data)
            {
                $name = "/tmp{$key}.csv";
                $path = resource_path('temp');

                //1000 data convert to new csv file
                file_put_contents($path. $name, $data);
            }
            return "done";
        }

        return 'file not found!';
    }

    public function store()
    {
        $batch = Bus::batch([
            new ImportCsv(0, 10),
            new ImportCsv(10, 20),
            new ImportCsv(200, 201),
            new ImportCsv(30, 40),
            new ImportCsv(40, 50),
        ])->allowFailures()
        ->then(function (Batch $batch) {
            return "all done";
        })->catch(function (Batch $batch, Throwable $e) {
            return "fist job faild";
        })->finally(function (Batch $batch) {
            return "exe done";
        })->dispatch();
    }
}
