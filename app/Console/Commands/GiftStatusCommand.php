<?php

namespace App\Console\Commands;

use App\Models\App;
use App\Models\CorporateGiftApiHandle;
use App\Models\GiftOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GiftStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gift:fetch_gift_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $corporateGiftAPIHandler ;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->corporateGiftAPIHandler =  new CorporateGiftApiHandle();
    }

    /**
     * Execute the console command.
     *
     *
     */
    public function handle()
    {
        $gift_orders = GiftOrder::where('status','Not paid')->get();
        //dd($gift_orders);
        Log::channel('slack')->critical($gift_orders);
        
        if($gift_orders){
            foreach($gift_orders as $gift){
                $app = App::find($gift['app_id']);
                $this->corporateGiftAPIHandler->setAccessToken($app->corporate_gift_token);
                $get_gift = $this->corporateGiftAPIHandler->getGiftById($gift->gift_id);
                //dd($get_gift);
                
                if($get_gift && isset($get_gift['data'])){
                     $gift->status = $get_gift['data']['status'];
                   // $gift->status = 'NOT PAID';
                    $gift->save();
                    //dd('done');
                   // Log::channel('slack')->critical($gift->status);
                }

            }
        }
    }
}
