<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\CRMSyncLog;
use App\Libraries\Ltcrm;
use Illuminate\Validation\Rule;
use Validator;

class SyncCRMCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:customers-sync {company_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync LTCRM customers with LTHD';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //command to run command - php artisan crm:customers-sync 1
        
        $last_run_date = '';
        $company_id = $this->argument('company_id');
        $last_run_details_exists = CRMSyncLog::select('last_run')->where('company_id', $company_id)->exists();
        if ($last_run_details_exists) {
            $last_run_details = CRMSyncLog::select('last_run')->where('company_id', $company_id)->first();
            $last_run_date = $last_run_details->last_run;
        }
        else {
            $last_run_date = Carbon::now();
        }

        $ltcrm = new Ltcrm();
        $new_customers = $ltcrm->fetch_new_customers($last_run_date);
        if (isset($new_customers->data)) {
            $new_customers = $new_customers->data;
            foreach($new_customers as $value) {
                $cust_name = $value->cust_name;
                $crm_customer_id = $value->cust_id;

                $new_cust_data = array(
                        'company_id' => $company_id,
                        'cust_name' => $cust_name,
                        'crm_customer_id' => $crm_customer_id
                    );

                $cust_created = DB::table('customers')->insert($new_cust_data);
            }
        }
        

        $new_sync_log_data = array(
                'last_run' => Carbon::now()
            );

        $sync_log_updates = DB::table('crm_sync_log')->where('company_id', $company_id)->update($new_sync_log_data);
         
        /*
        run this twice command once everyday
        */
        return 0;
    }
}
