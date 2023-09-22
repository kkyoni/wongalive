<?php
namespace App\Http\Controllers\Admin;
use App\Models\Cms;
use App\Http\Controllers\Controller;
use Helmesvs\Notify\Facades\Notify;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;
use Illuminate\Http\Request;
use Auth;
use Event,Str,Storage;
use Validator;
use Ixudra\Curl\Facades\Curl;
use config;
use Settings;

class CmsController extends Controller
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
      $this->pageLayout = 'admin.pages.cms.';
      $this->middleware('auth');
  }

    /* -----------------------------------------------------------------------------------------
    @Description: Function  Index Page
    -------------------------------------------------------------------------------------------- */

  public function index(Builder $builder, Request $request){
    $cms = Cms::orderBy('updated_at','DESC');
    if (request()->ajax()) {
        return DataTables::of($cms->get())
        ->addIndexColumn()
        
        ->editColumn('description', function (Cms $cms) {
            return strip_tags(str_limit($cms->description, $limit = 100, $end = '...'));
        })
        ->editColumn('status', function (Cms $cms) {
                if ($cms->status == "active") {
                    return '<span class="label label-success">Active</span>';
                } else {
                    return '<span class="label label-danger">Block</span>';
                }
            })
        ->editColumn('action', function (Cms $cms) {
            $action = '';
            $action .= '<a class="btn btn-warning btn-circle btn-sm" data-toggle="tooltip" title="Edit" href='.route('admin.cms.edit',[$cms->id]).'><i class="fa fa-pencil"></i></a>';
            if($cms->status == "active"){
              $action .= '<a href="javascript:void(0)" data-value="1" data-toggle="tooltip" title="Active" class="btn btn-sm btn-circle  btn-dark ml-1 mr-1 changeStatusRecord" data-id="'.$cms->id.'"><i class="fa fa-unlock"></i></a>';
            }else{
              $action .= '<a href="javascript:void(0)" data-value="0" data-toggle="tooltip" title="Block" class="btn btn-sm btn-circle  btn-dark ml-1 mr-1 changeStatusRecord" data-id="'.$cms->id.'"><i class="fa fa-lock" ></i></a>';
            }
            return $action;
        })
        ->rawColumns(['action','status'])
        ->make(true);
    }
    $html = $builder->columns([
        ['data' => 'DT_RowIndex', 'name' => '', 'title' => 'Sr no','width'=>'5%',"orderable" => false, "searchable" => false],
        ['data' => 'title','name' => 'title','title' =>'Title','width'=>'20%'],
        ['data' => 'description', 'name' => 'description', 'title' => 'Page Content','width'=>'40%'],
        ['data' => 'status', 'name' => 'status', 'title' => 'Status','width'=>'8%'],
        ['data' => 'action', 'name' => 'action', 'title' => 'Action','width'=>'10%',"orderable" => false],
        ])->parameters(['order' =>[]]);
    return view($this->pageLayout.'index',compact('html'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function  Create Page
    -------------------------------------------------------------------------------------------- */
    
    public function create()
    {
    //  return view($this->pageLayout.'create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Store 
    -------------------------------------------------------------------------------------------- */
    
    public function store(Request $request){
  //       $validatedData = Validator::make($request->all(),[
  //           'title'           => 'required',
  //           'description'     => 'required',
  //           ]);
  //       if($validatedData->fails()){
  //           return redirect()->back()->withErrors($validatedData)->withInput();
  //       }
  //       try{
  //           $emergency = Cms::create([
  //               'title'          => @$request->title,
  //               'description'    => @$request->get('page_description'),
  //               'status'           =>@$request->status,
  //               ]);
  //           Notify::success($request->title.' CMS Created Successfully.');
  //           return redirect()->route('admin.cms.index');
  //       }catch(\Exception $e){
  //         return back()->with([
  //           'alert-type'    => 'danger',
  //           'message'       => $e->getMessage()
  //           ]);
  //     }
         }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cms  $cms
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function  Show Cms
    -------------------------------------------------------------------------------------------- */

    public function show(Cms $cms)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cms  $cms
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function  Edit CMS
    -------------------------------------------------------------------------------------------- */

    public function edit(Cms $cms, $id)
    {
        $cms = Cms::where('id',$id)->first();
        return view($this->pageLayout.'edit',compact('cms','id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cms  $cms
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function  Update CMS
    -------------------------------------------------------------------------------------------- */

    public function update(Request $request, Cms $cms, $id)
    {
        $validatedData = Validator::make($request->all(),[
            'title'           => 'required',
            'description'     => 'required',
            
            ]);
        if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        try{
            $emergency = Cms::where('id', $id)->update([
                'title'          => @$request->title,
                'description'    => @$request->get('description'),
                'status'           =>@$request->status,
                ]);
            Notify::success($request->title.' CMS Updated Successfully..!');
            return redirect()->route('admin.cms.index');
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
     * @param  \App\Cms  $cms
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cms $cms)
    {
        //
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Change Status of CMS
    -------------------------------------------------------------------------------------------- */

    public function change_status(Request $request){
        try{
            $cms = Cms::where('id',$request->id)->first();
            if($cms->status == "active"){
                Cms::where('id',$request->id)->update([
                    'status' => "block",
                ]);
            }else{
                Cms::where('id',$request->id)->update([
                    'status'=> "active",
                ]);
            }
            Notify::success('CMS Status Updated Successfully..!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'CMS Status Updated Successfully..!'
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
