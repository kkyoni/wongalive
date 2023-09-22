<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DataTables,Notify,Str,Storage;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Auth;
use App\Models\ReportUser;
use Event;
use Settings;
use Mail;
use App\Models\User;

class ReportUserController extends Controller
{
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
        $this->pageLayout = 'admin.pages.report_user.';
        $this->middleware('auth');

        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Index Page
    -------------------------------------------------------------------------------------------- */  
    public function index(Builder $builder, Request $request)
    {
        try {
            $report_user = ReportUser::orderBy('id', 'DESC');
            if (request()->ajax()) {
                return DataTables::of($report_user->get())
                ->addIndexColumn()
                ->editColumn('user_id', function (ReportUser $report_user) {
                    return $report_user->user_id;
                })
                ->editColumn('receive_id', function (ReportUser $report_user) {
                    return $report_user->receive_id;
                })
                ->editColumn('category_id', function (ReportUser $report_user) {
                    return $report_user->reportCategory->category;
                })
                ->editColumn('action', function (ReportUser $report_user) {
                    $action = '';
                    if($report_user->status == "pending"){
                    $action .= '<a class="btn btn-warning btn-circle btn-sm" data-toggle="tooltip" title="Edit" href='.route('admin.report_user.edit',[$report_user->id]).'><i class="fa fa-pencil"></i></a>';    
                    } else {
                        $action .= '<a href="javascript:void(0)" class="btn btn-primary btn-circle btn-sm ml-1 mr-2 ShowReport" data-id="'.$report_user->id.'" data-toggle="tooltip" title="View Report History"><i class="fa fa-eye"></i></a>';
                    }
                    return $action;
                })
                ->rawColumns(['action','user_id','receive_id','category_id'])
                    ->make(true);
            }
            $html = $builder->columns([
                ['data' => 'DT_RowIndex', 'name' => '', 'title' => 'Sr no','width'=>'2%',"orderable" => false, "searchable" => false],
                ['data' => 'user_id', 'name' => 'user_id', 'title' => 'Sender Id','width'=>'5%'],
                ['data' => 'receive_id', 'name' => 'receive_id', 'title' => 'Receiver Id','width'=>'5%'],
                ['data' => 'category_id', 'name' => 'category_id', 'title' => 'Category Name','width'=>'5%'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action','width'=>'5%',"orderable" => false],
            ])->parameters(['order' =>[]]);

            return view($this->pageLayout.'index', compact('html'));
        } catch (\Exception $e) {
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
    @Description: Function Create Report User Page
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
    @Description: Function Store Report User Page
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
    @Description: Function Show Report User Page
    -------------------------------------------------------------------------------------------- */

     public function show(Request $request)
        {
            $report_users = ReportUser::find($request->id);
            // $report_users = ReportUser::where('id',$request->id)->first();
            // $id = $request->id;
            // dd($report_users);
            return view($this->pageLayout.'show',compact('report_users'));
       }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Edit Report User Page
    -------------------------------------------------------------------------------------------- */

    public function edit(Request $request,$id)
    {
        $report_user = ReportUser::where('id',$id)->first();
        if($report_user->status == "resolved"){
             return redirect()->route('admin.report_user.index');
        }
        return view($this->pageLayout.'edit',compact('report_user','id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Update Report User Page
    -------------------------------------------------------------------------------------------- */

    public function update(Request $request, $id)
    {
         $validatedData = Validator::make($request->all(),[
            'status'           => 'required',
            'description'      => 'required',
            ]);
        if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        try{
            $reportuser = ReportUser::where('id', $id)->update([
                'status'           =>"resolved",
                'description'      =>@$request->description,
            ]);
            if(!empty($reportuser)){
                $report = ReportUser::where('id', $id)->first();
                $mail_check = User::where('id',$report->receive_id)->first();
                // dd($mail_check->email);
                $to_name = $mail_check->username;
                $to_email = $mail_check->email;
                $data = array('name'=>$mail_check->username, "body" => @$request->description, "subject"=> "Report Solution");
                Mail::send('emails.report', $data, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                    ->subject('Report Solution');
                    $message->from('php1@aistechnolabs.co.uk','wonga Live');
                });
                Notify::success($request->title.' Report Updated Successfully..!');
                return redirect()->route('admin.report_user.index');
            }
        }catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Delete Report User Page
    -------------------------------------------------------------------------------------------- */

    public function destroy($id)
    {
        //
    }
}