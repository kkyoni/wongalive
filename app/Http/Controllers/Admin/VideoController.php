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
use App\Models\Videos;
use Event;
use Settings;
use Mail;
use App\Models\User;

class VideoController extends Controller
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
        $this->pageLayout = 'admin.pages.videos.';
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
            $videos = Videos::orderBy('id', 'DESC');
            if (request()->ajax()) {
                return DataTables::of($videos->get())
                ->addIndexColumn()
                // ->editColumn('user_id', function (Videos $videos) {
                //     return $videos->usersget->username;
                // })
                ->editColumn('video', function (Videos $videos) {
                    return $videos->video;
                })
                ->editColumn('thum', function (Videos $videos) {
                    return $videos->thum;
                })
                ->editColumn('action', function (Videos $videos) {
                    $action = '';

                    $action .='<a class="btn btn-danger btn-sm m-l-10 btn-circle deletevideo ml-2 mr-2" href="javascript:void(0)" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>';
                    if($videos->admin_flag == "no"){
                        $action .= '<a href="javascript:void(0)" data-value="1"   data-toggle="tooltip" title="Yes" class="btn btn-sm btn-dark btn-circle changeStatusRecord"><i class="fa fa-unlock"></i></a>';
                    }else{
                        $action .= '<a href="javascript:void(0)" data-value="0"  data-toggle="tooltip" title="No" class="btn btn-sm btn-dark btn-circle changeStatusRecord"><i class="fa fa-lock" ></i></a>';
                    }
                    return $action;
                })
                ->rawColumns(['action','admin_flag','avatar'])
                ->make(true);
            }
            $html = $builder->columns([
                ['data' => 'DT_RowIndex', 'name' => '', 'title' => 'Sr no','width'=>'2%',"orderable" => false, "searchable" => false],
                ['data' => 'username', 'name' => 'username', 'title' => 'Username','width'=>'5%'],
                ['data' => 'video', 'name' => 'video', 'title' => 'Video','width'=>'5%'],
                ['data' => 'thum', 'name' => 'thum', 'title' => 'Thumbnail','width'=>'5%'],
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
          //
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
    @Description: Function Update Report User Page
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
    @Description: Function Delete User Video
    -------------------------------------------------------------------------------------------- */
    public function delete($id){
        try{
            $checkVideo = Videos::where('id',$id)->first();
            $checkVideo->delete();
            Notify::success('Video deleted Successfully..!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'Video deleted Successfully..!'
                ]);
        }catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Change Status of User Video 
    -------------------------------------------------------------------------------------------- */

    public function change_status(Request $request){
        try{
            $videos = Videos::where('id',$request->id)->first();
            if($videos === null){
                return redirect()->back()->with([
                    'status'    => 'warning',
                    'title'     => 'Warning!!',
                    'message'   => 'videos not found !!'
                ]);
            }else{
                if($videos->admin_flag == "yes"){
                    Videos::where('id',$request->id)->update([
                        'status' => "no",
                    ]);
                }
                if($videos->admin_flag == "no"){
                    Videos::where('id',$request->id)->update([
                        'status'=> "yes",
                    ]);
                }
            }
            Notify::success('Video status updated successfully !!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'Video status updated successfully.'
            ]);
        }catch (Exception $e){
            return response()->json([
                'status'    => 'error',
                'title'     => 'Error!!',
                'message'   => $e->getMessage()
            ]);
        }
    }

}