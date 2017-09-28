<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Payment;

class GenerateInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:invoice {transaction_id? : 交易id} {--force : 强制生成}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成交易票据，交易id可选，如果不填，将逐个生成所有交易的票据。交易id在票据中不会被体现，票据对应的交易必须是completed状态。';

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
     * @return mixed
     */
    public function handle()
    {
        $tids = $this->argument('transaction_id')?:[];
        $isforce = $this->option('force');
        $path = env('INVOICE_SAVE_PATH');
        if(empty($tids)){
            $payments = Payment::where('payments.status','completed');
            //空交易id参数的情况下，强制生成=全表生成，不强制生成=生成表中invoice_id为空的记录的票据
            //调用命令时统一加第二个参数，强制生成
            if(!$isforce)$payments->whereNull('invoice_id');//强制更新没有这个条件
            $payments->chunk(10, function ($payments) {
                $service = app(\App\Contracts\PaymentService::class);
                foreach ($payments as $p) {
                    $this->comment("generate $p->number's invoice...");
                    $service->generateInvoice($p->number,true);
                }
            });
        }else{
            $paymentService = app(\App\Contracts\PaymentService::class);
            if($isforce){
                //强制更新，重新生成一个票据
                $this->comment("the invoice was re-generate");
                $paymentService->generateInvoice($tids,true);
            }else{
                //不强制，如果已经生成，给予提示
                $invoice_id = Payment::where('number',$tids)->value('invoice_id');
                if(!empty($invoice_id)){
                    $this->comment("$tids has a invoice,that id is $invoice_id,file path is storage/".$path."/".$invoice_id.".pdf");
                    return;
                }else{
                    //如果没生成执行生成
                    $paymentService->generateInvoice($tids);
                }
            }
        }      
    }
}
