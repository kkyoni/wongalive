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
use App\Models\Banner;
use Event;
use Settings;

class BannerController extends Controller
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
        $this->pageLayout = 'admin.pages.banner.';
        $this->middleware('auth');        
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function  Index Page
    -------------------------------------------------------------------------------------------- */

    public function index(Builder $builder, Request $request)
    {
        
        $banner = Banner::orderBy('updated_at','DESC');
        // dd($banner);
    if (request()->ajax()) {
        return DataTables::of($banner->get())
        ->addIndexColumn()
        
        ->editColumn('status', function (Banner $banner) {
                if ($banner->status == "active") {
                    return '<span class="label label-success">Active</span>';
                } else {
                    return '<span class="label label-danger">Block</span>';
                }
            })
        ->editColumn('banner_image', function (Banner $banner) {
                    if($banner->banner_image){
                        $i='';
                        if (file_exists( 'storage/banner/'.$banner->banner_image)) {
                            $i .= "<img src=".url("storage/banner/".$banner->banner_image)." style='max-width:50px;max-height:50px;'/> ";
                        }else{
                            $i .= "<img src=".url("storage/banner/default.png")."  style='max-width:50px;max-height:50px;'/> ";
                        }
                        return $i;
                    }else{
                        return "<img src=".url("storage/banner/default.png")."  style='max-width:50px;max-height:50px;'/> ";
                    }
                })
        ->editColumn('action', function (Banner $banner) {
            $action = '';
            $action .= '<a class="btn btn-warning btn-circle btn-sm" data-toggle="tooltip" title="Edit" href='.route('admin.banner.edit',[$banner->id]).'><i class="fa fa-pencil"></i></a>';
            $action .='<a class="btn btn-danger btn-sm m-l-10 btn-circle deletebanner ml-2 mr-2" data-id ="'.$banner->id.'" href="javascript:void(0)" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>';
            $action .= '<a href="javascript:void(0)" class="btn btn-primary btn-circle btn-sm ml-1 mr-2 ShowBanner" data-id="'.$banner->id.'" data-toggle="tooltip" title="View Banner"><i class="fa fa-eye"></i></a>';
            if($banner->status == "active"){
              $action .= '<a href="javascript:void(0)" data-value="1"   data-toggle="tooltip" title="Active" class="btn btn-sm btn-dark btn-circle changeStatusRecord" data-id="'.$banner->id.'"><i class="fa fa-unlock"></i></a>';
            }else{
              $action .= '<a href="javascript:void(0)" data-value="0"  data-toggle="tooltip" title="Block" class="btn btn-sm btn-dark btn-circle changeStatusRecord" data-id="'.$banner->id.'"><i class="fa fa-lock" ></i></a>';
            }
            return $action;
        })
        ->rawColumns(['status','action','banner_image'])
        ->make(true);
    }
    $html = $builder->columns([
        ['data' => 'DT_RowIndex', 'name' => '', 'title' => 'Sr no','width'=>'7%',"orderable" => false, "searchable" => false],
        ['data' => 'banner_image', 'name' => 'banner_image', 'title' => 'Banner Pic','width'=>'12%',"orderable" => false, "searchable" => false],
        ['data' => 'banner_name','name' => 'banner_name','title' =>'Name','width'=>'10%'],
        ['data' => 'details', 'name' => 'details', 'title' => 'Banner Content','width'=>'20%'],
        ['data' => 'status', 'name' => 'status', 'title' => 'Status','width'=>'10%'],
        ['data' => 'action', 'name' => 'action', 'title' => 'Action','width'=>'18%',"orderable" => false],
        ])->parameters(['order' =>[]]);
    return view($this->pageLayout.'index',compact('html'));
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function  Banner
    -------------------------------------------------------------------------------------------- */

    public function create(){
        $banners=array();
        return view($this->pageLayout.'create', compact('banners'));
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Edit Banner
    -------------------------------------------------------------------------------------------- */
    public function edit($id){
        $banners = Banner::where('id',$id)->first();
        if(!empty($banners)){
            return view($this->pageLayout.'edit',compact('banners','id'));
        }else{
            return redirect()->route('admin.banner.index');
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Store Banner
    -------------------------------------------------------------------------------------------- */

    public function store(Request $request){
        $customMessages = [
        'banner_image.required' => 'Banner Image is Required',
        'banner_name.required' => 'Banner Name is Required',
        'details.required' => 'Details is Required',
        'status.required' => 'Status is Required',
        ];
        $validatedData = Validator::make($request->all(),[
            'banner_name'        => 'required',
            'details'        => 'required',
            'status'          => 'required',
            ],$customMessages);
        if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        try{
            if($request->hasFile('banner_image')){
                $file = $request->file('banner_image');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(10).'.'.$extension;
                Storage::disk('public')->putFileAs('banner', $file,$filename);
            }else{
                $filename = 'default.png';
            }
            $bannerID=Banner::create([
                'banner_image'           => @$filename,
                'banner_name'            => @$request->get('banner_name'),
                'details'            => @$request->get('details'),
                'status'           => @$request->get('status'),
                ]);
            Notify::success('User Banner Created Successfully..!');
            return redirect()->route('admin.banner.index');
        }catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Update Banner
    -------------------------------------------------------------------------------------------- */

        public function update(Request $request,$id){
         $customMessages = [

        'banner_image.required' => 'Banner Image is Required',
        'banner_name.required' => 'Banner Name is Required',
        'details.required' => 'Details is Required',
        'status.required' => 'Status is Required',
         ];
         $validatedData = Validator::make($request->all(),[
            'banner_name'        => 'required',
            'details'        => 'required',
            'status'          => 'required',
        ],$customMessages);

         if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try{
            $oldDetails = Banner::find($id);
            if($request->hasFile('banner_image')){
                $file = $request->file('banner_image');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(10).'.'.$extension;
                \Storage::disk('public')->putFileAs('banner', $file,$filename);
            }else{
                if($oldDetails->banner_image !== null){
                    $filename = $oldDetails->banner_image;
                }else{
                    $filename = 'default.png';
                }
            }
            Banner::where('id',$id)->update([
                'banner_image'           => @$filename,
                'banner_name'         => @$request->get('banner_name'),
                'details'            => @$request->get('details'),
                'status'           => @$request->get('status'),
                    ]);
            Notify::success('Banner has been Updated Successfully..!');
            return redirect()->route('admin.banner.index');
        } catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Delete Banner
    -------------------------------------------------------------------------------------------- */

    public function delete($id){
        try{
            $checkBanner = Banner::where('id',$id)->first();
            $checkBanner->delete();
            Notify::success('Banner Deleted Successfully..!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'Banner deleted Successfully..!'
                ]);
        }catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Change Status of Banner
    -------------------------------------------------------------------------------------------- */

    public function status(Request $request){
        try{
            $banner = Banner::where('id',$request->id)->first();
            if($banner === null){
                return redirect()->back()->with([
                    'status'    => 'warning',
                    'title'     => 'Warning!!',
                    'message'   => 'Banner not found !!'
                ]);
            }else{
                if($banner->status == "active"){
                    Banner::where('id',$request->id)->update([
                        'status' => "block",
                    ]);
                }
                if($banner->status == "block"){
                    Banner::where('id',$request->id)->update([
                        'status'=> "active",
                    ]);
                }
            }
            Notify::success('Banner status updated successfully !!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'Banner status updated successfully.'
            ]);
        }catch (Exception $e){
            return response()->json([
                'status'    => 'error',
                'title'     => 'Error!!',
                'message'   => $e->getMessage()
            ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Show Banner
    -------------------------------------------------------------------------------------------- */

    public function show(Request $request) {
        $banner = Banner::find($request->id);
        // dd($banner);
        return view($this->pageLayout.'show',compact('banner'));
   }
}