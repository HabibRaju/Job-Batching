<?php

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class ImportCsv implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $left, $right;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($left, $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...

            Log::alert("error");
        }
        $path = resource_path('temp');
        for($i = $this->left; $i < $this->right; $i++){
            $file = glob("$path/tmp".$i.".csv");
            $data = array_map('str_getcsv', file($file[0]));
            
            foreach($data as $customer)
            {
                Customer::create([
                    'name'  => $customer[0],
                    'email' => $customer[1]
                ]);
            }

            unlink($file[0]);
        }
    }
}
