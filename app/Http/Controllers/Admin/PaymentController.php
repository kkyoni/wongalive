<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Diamond;
use App\Models\PaymentHistory;
use App\Models\CardDetails;
use App\Models\User;
use Helmesvs\Notify\Facades\Notify;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;
use Auth;
use Event,Str,Storage;
use Validator;
use Ixudra\Curl\Facades\Curl;
use config;
use Settings;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $authLayout = '';
    protected $pageLayout = '';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authLayout = 'admin.auth.';
        $this->pageLayout = 'admin.pages.transactions.';
        $this->middleware('auth');
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Index Page
    -------------------------------------------------------------------------------------------- */

    public function index(Builder $builder, Request $request)
    {
        try{
            $paymenthistory = PaymentHistory::with(['getUserName'])->orderBy('id','DESC');
            if (request()->ajax()) {
                return DataTables::of($paymenthistory->get())
                    ->addIndexColumn()
                    ->editColumn('payment_status', function (PaymentHistory $paymenthistory) {
                        if ($paymenthistory->payment_status == "success") {
                            return '<span class="label label-success">Success</span>';
                        } else {
                            return '<span class="label label-danger">'.$paymenthistory->payment_status.'</span>';
                        }
                    })

                    ->editColumn('user_id', function (PaymentHistory $paymenthistory) {
                        if(!empty($paymenthistory->getUserName)){
                            return $paymenthistory->getUserName->username;
                        }else{
                            return '';
                        }
                    })
                    ->editColumn('action', function (PaymentHistory $paymenthistory) {
                        $action  = '';
                        $action .= '<a href="javascript:void(0)" class="btn btn-primary btn-circle btn-sm ml-1 mr-2 ShowPayment" data-id="'.$paymenthistory->id.'" data-toggle="tooltip" title="View Payment History"><i class="fa fa-eye"></i></a>';
                        return $action;
                    })
                    ->rawColumns(['action','payment_status'])
                    ->make(true);
            }
            $html = $builder->columns([
                ['data' => 'DT_RowIndex', 'name' => '', 'title' => 'Sr no','width'=>'5%',"orderable" => false, "searchable" => false],
                ['data' => 'user_id', 'name' => 'user_id', 'title' => 'User','width'=>'20%',"orderable" => false, "searchable" => false],
                ['data' => 'packs','name' => 'packs','title' =>'Packs','width'=>'10%'],
                ['data' => 'transaction_id', 'name' => 'transaction_id', 'title' => 'Transaction Id','width'=>'20%'],
                ['data' => 'amount', 'name' => 'amount', 'title' => 'Amount','width'=>'10%'],
                ['data' => 'payment_status', 'name' => 'payment_status', 'title' => 'Status','width'=>'10%'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action','width'=>'5%',"orderable" => false],
            ])->parameters(['order' =>[]]);            
            return view($this->pageLayout.'index',compact('html'));
        }catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
            ]);
        }
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Create Page
    -------------------------------------------------------------------------------------------- */

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Store Page
    -------------------------------------------------------------------------------------------- */

    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Show Page
    -------------------------------------------------------------------------------------------- */

    public function show(Request $request) {
        $payment_history = PaymentHistory::find($request->id);
        $diamond_detail = Diamond::find($payment_history->diamonds_id);
        $card_detail = CardDetails::find($payment_history->card_id);
        $user_detail = User::find($payment_history->user_id);
        
        return view($this->pageLayout.'show',compact('payment_history','diamond_detail','card_detail','user_detail'));
   }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Edit Page
    -------------------------------------------------------------------------------------------- */

    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Update Page
    -------------------------------------------------------------------------------------------- */

    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Delete Page
    -------------------------------------------------------------------------------------------- */

    public function destroy($id)
    {
        //
    }
}
