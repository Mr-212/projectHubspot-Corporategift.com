<?php

namespace App\Console\Commands;

use App\Models\App;
use App\Models\CorporateGiftApiHandle;
use Illuminate\Console\Command;
use App\Models\GiftProduct;
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
        
    }


    public function getGiftProducts($identifier){
        $apps = App::select('corporate_gift_token','id')
        ->whereNull('is_active')
        ->get();

        foreach($apps as $app){
            $CorporateGiftGet = null;
            if($app) {
                $CorporateGiftGet = $this->corporateGiftHandler->getGiftProducts();
                Log::info("gift products" . $CorporateGiftGet);
                if (isset($CorporateGiftGet['status']) && $CorporateGiftGet['status']) {
                    foreach ($CorporateGiftGet['data'] as $data) {
                        GiftProduct::updateOrCreate(['app_id'=>$app->id,'product_id'=>$data['id']], ['product_id' => $data['id'], 'data' => $data]);
                    }
                }
            }

        }

    }
}
