<?php

namespace App\Console\Commands;

use App\Models\App;
use App\Models\CorporateGiftApiHandle;
use Illuminate\Console\Command;
use App\Models\GiftProduct;
use Carbon\Carbon;
use Fruitcake\Cors\CorsServiceProvider;
use Illuminate\Support\Facades\Log;

class FetchGiftProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gift:fetch_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $corporateGiftAPIHandler ;
    public function __construct()
    {
        parent::__construct();
        $this->corporateGiftAPIHandler =  new CorporateGiftApiHandle();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->getGiftProducts();
    }


    public function getGiftProducts(){
        $apps = App::select('corporate_gift_token','id')
        ->active()
        ->where('updated_at','<=',Carbon::now()->subDays(30)->toDateTimeString())
        ->get();
        Log::info("apps" . $apps);

        foreach($apps as $app){
            $CorporateGiftGet = null;
            if($app) {
                $CorporateGiftGet = $this->corporateGiftAPIHandler->setAccessToken($app->corporate_gift_token);
                $CorporateGiftGet = $this->corporateGiftAPIHandler->getGiftProducts();
                Log::info("gift products:");
                // Log::info($CorporateGiftGet);
                if (isset($CorporateGiftGet['status']) && $CorporateGiftGet['status']) {
                    foreach ($CorporateGiftGet['data'] as $data) {
                        $update = GiftProduct::updateOrCreate(['app_id'=>$app->id,'product_id'=>$data['id']], ['product_id' => $data['id'], 'data' => $data]);
                        Log::info('updated-'.$update->id);
                    }
                }
            }

        }

    }
}
