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
use App\Models\Gift;
use Event;
use Settings;

class GiftController extends Controller
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
        $this->pageLayout = 'admin.pages.gift.';
        $this->middleware('auth');        
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function  Index Page
    -------------------------------------------------------------------------------------------- */

    public function index(Builder $builder, Request $request)
    {
        
        $gift = Gift::orderBy('updated_at','DESC');
    if (request()->ajax()) {
        return DataTables::of($gift->get())
        ->addIndexColumn()
        
        ->editColumn('name', function (Gift $gift) {
            return strip_tags(str_limit($gift->name, $limit = 100, $end = '...'));
        })
        ->editColumn('status', function (Gift $gift) {
                if ($gift->status == "active") {
                    return '<span class="label label-success">Active</span>';
                } else {
                    return '<span class="label label-danger">Block</span>';
                }
            })
        ->editColumn('avatar', function (Gift $gift) {
                    if($gift->avatar){
                        $i='';
                        if (file_exists( 'storage/gift/'.$gift->avatar)) {
                            $i .= "<img src=".url("storage/gift/".$gift->avatar)." style='max-width:50px;max-height:50px;'/> ";
                        }else{
                            $i .= "<img src=".url("storage/gift/default.png")."  style='max-width:50px;max-height:50px;'/> ";
                        }
                        return $i;
                    }else{
                        return "<img src=".url("storage/gift/default.png")."  style='max-width:50px;max-height:50px;'/> ";
                    }
                })
        ->editColumn('action', function (Gift $gift) {
            $action = '';
            $action .= '<a class="btn btn-warning btn-circle btn-sm" data-toggle="tooltip" title="Edit" href='.route('admin.gift.edit',[$gift->id]).'><i class="fa fa-pencil"></i></a>';
            $action .='<a class="btn btn-danger btn-sm m-l-10 btn-circle deletepack ml-2 mr-2" data-id ="'.$gift->id.'" href="javascript:void(0)" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>';
            $action .= '<a href="javascript:void(0)" class="btn btn-primary btn-circle btn-sm ml-1 mr-2 ShowGift" data-id="'.$gift->id.'" data-toggle="tooltip" title="View Gift"><i class="fa fa-eye"></i></a>';
            if($gift->status == "active"){
              $action .= '<a href="javascript:void(0)" data-value="1"   data-toggle="tooltip" title="Active" class="btn btn-sm btn-dark btn-circle changeStatusRecord" data-id="'.$gift->id.'"><i class="fa fa-unlock"></i></a>';
            }else{
              $action .= '<a href="javascript:void(0)" data-value="0"  data-toggle="tooltip" title="Block" class="btn btn-sm btn-dark btn-circle changeStatusRecord" data-id="'.$gift->id.'"><i class="fa fa-lock" ></i></a>';
            }
            return $action;
        })
        ->rawColumns(['action','status','avatar','name'])
        ->make(true);
    }
    $html = $builder->columns([
        ['data' => 'DT_RowIndex', 'name' => '', 'title' => 'Sr no','width'=>'7%',"orderable" => false, "searchable" => false],
        ['data' => 'avatar', 'name' => 'avatar', 'title' => 'Gift','width'=>'12%',"orderable" => false, "searchable" => false],
        ['data' => 'name','name' => 'name','title' =>'Name','width'=>'10%'],
        ['data' => 'price', 'name' => 'price', 'title' => 'Price','width'=>'10%'],
        ['data' => 'status', 'name' => 'status', 'title' => 'Status','width'=>'10%'],
        ['data' => 'action', 'name' => 'action', 'title' => 'Action','width'=>'18%',"orderable" => false],
        ])->parameters(['order' =>[]]);
    return view($this->pageLayout.'index',compact('html'));
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function  Diamond Pack
    -------------------------------------------------------------------------------------------- */

    public function create(){
        $gift=array();
        return view($this->pageLayout.'create', compact('gift'));
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Edit Diamond Pack
    -------------------------------------------------------------------------------------------- */
    public function edit($id){
        $gift = Gift::where('id',$id)->first();

        if(!empty($gift)){
            return view($this->pageLayout.'edit',compact('gift','id'));
        }else{
            return redirect()->route('admin.gift.index');
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Store Gift
    -------------------------------------------------------------------------------------------- */

    public function store(Request $request){
        $customMessages = [
        'name.required' => 'Name is Required',
        'price.required' => 'Price is Required',
        ];
        $validatedData = Validator::make($request->all(),[
            'name'        => 'required',
            'price'             => 'required',
            ],$customMessages);
        if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try{
            if($request->hasFile('avatar')){
                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(10).'.'.$extension;
                Storage::disk('public')->putFileAs('gift', $file,$filename);
            }else{
                $filename = 'default.png';
            }
            $giftID=Gift::create([
                'avatar'          => @$filename,
                'name'            => @$request->get('name'),
                'price'           => @$request->get('price'),
                ]);
            Notify::success('User Gift Created Successfully..!');
            return redirect()->route('admin.gift.index');
        }catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Update Diamond Pack
    -------------------------------------------------------------------------------------------- */

        public function update(Request $request,$id){
         $customMessages = [
        'name.required' => 'Packs is Required',
        'price.required' => 'Price is Required',
         ];
         $validatedData = Validator::make($request->all(),[
            'name'        => 'required',
            'price'             => 'required',
        ],$customMessages);

         if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try{
            $oldDetails = Gift::find($id);
            if($request->hasFile('avatar')){
                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(10).'.'.$extension;
                \Storage::disk('public')->putFileAs('gift', $file,$filename);
            }else{
                if($oldDetails->avatar !== null){
                    $filename = $oldDetails->avatar;
                }else{
                    $filename = 'default.png';
                }
            }
            Gift::where('id',$id)->update([
                'avatar'           => @$filename,
                'name'             => @$request->get('name'),
                'price'            => @$request->get('price'),
            ]);
            Notify::success('Gift has been Updated Successfully..!');
            return redirect()->route('admin.gift.index');
        } catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Delete Diamond Pack
    -------------------------------------------------------------------------------------------- */

    public function delete($id){
        try{
            $checkPack = Gift::where('id',$id)->first();
            $checkPack->delete();
            Notify::success('Gift deleted Successfully..!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'Gift deleted Successfully..!'
                ]);
        }catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Change Status of Diamond Pack
    -------------------------------------------------------------------------------------------- */

            public function change_status(Request $request){
        try{
            $gift = Gift::where('id',$request->id)->first();
            if($gift === null){
                return redirect()->back()->with([
                    'status'    => 'warning',
                    'title'     => 'Warning!!',
                    'message'   => 'Gift not found !!'
                ]);
            }else{
                if($gift->status == "active"){
                    Gift::where('id',$request->id)->update([
                        'status' => "block",
                    ]);
                }
                if($gift->status == "block"){
                    Gift::where('id',$request->id)->update([
                        'status'=> "active",
                    ]);
                }
            }
            Notify::success('Gift status updated successfully !!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'Gift status updated successfully.'
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
    @Description: Function Show Diamond Pack
    -------------------------------------------------------------------------------------------- */

    public function show(Request $request) {
        $gift = Gift::find($request->id);
        return view($this->pageLayout.'show',compact('gift'));
   }
}