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
use App\Models\Diamond;
use Event;
use Settings;

class DiamondController extends Controller
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
        $this->pageLayout = 'admin.pages.diamond.';
        $this->middleware('auth');        
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function  Index Page
    -------------------------------------------------------------------------------------------- */

    public function index(Builder $builder, Request $request)
    {
        
        $diamond = Diamond::orderBy('updated_at','DESC');
    if (request()->ajax()) {
        return DataTables::of($diamond->get())
        ->addIndexColumn()
        
        ->editColumn('description', function (Diamond $diamond) {
            return strip_tags(str_limit($diamond->description, $limit = 100, $end = '...'));
        })
        ->editColumn('status', function (Diamond $diamond) {
                if ($diamond->status == "active") {
                    return '<span class="label label-success">Active</span>';
                } else {
                    return '<span class="label label-danger">Block</span>';
                }
            })
        ->editColumn('avatar', function (Diamond $diamond) {
                    if($diamond->avatar){
                        $i='';
                        if (file_exists( 'storage/diamond/'.$diamond->avatar)) {
                            $i .= "<img src=".url("storage/diamond/".$diamond->avatar)." style='max-width:50px;max-height:50px;'/> ";
                        }else{
                            $i .= "<img src=".url("storage/diamond/default.png")."  style='max-width:50px;max-height:50px;'/> ";
                        }
                        return $i;
                    }else{
                        return "<img src=".url("storage/diamond/default.png")."  style='max-width:50px;max-height:50px;'/> ";
                    }
                })
        ->editColumn('action', function (Diamond $diamond) {
            $action = '';
            $action .= '<a class="btn btn-warning btn-circle btn-sm" data-toggle="tooltip" title="Edit" href='.route('admin.diamond.edit',[$diamond->id]).'><i class="fa fa-pencil"></i></a>';
            $action .='<a class="btn btn-danger btn-sm m-l-10 btn-circle deletepack ml-2 mr-2" data-id ="'.$diamond->id.'" href="javascript:void(0)" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>';
            $action .= '<a href="javascript:void(0)" class="btn btn-primary btn-circle btn-sm ml-1 mr-2 ShowDiamond" data-id="'.$diamond->id.'" data-toggle="tooltip" title="View Diamond"><i class="fa fa-eye"></i></a>';
            if($diamond->status == "active"){
              $action .= '<a href="javascript:void(0)" data-value="1"   data-toggle="tooltip" title="Active" class="btn btn-sm btn-dark btn-circle changeStatusRecord" data-id="'.$diamond->id.'"><i class="fa fa-unlock"></i></a>';
            }else{
              $action .= '<a href="javascript:void(0)" data-value="0"  data-toggle="tooltip" title="Block" class="btn btn-sm btn-dark btn-circle changeStatusRecord" data-id="'.$diamond->id.'"><i class="fa fa-lock" ></i></a>';
            }
            return $action;
        })
        ->rawColumns(['action','status','avatar'])
        ->make(true);
    }
    $html = $builder->columns([
        ['data' => 'DT_RowIndex', 'name' => '', 'title' => 'Sr no','width'=>'7%',"orderable" => false, "searchable" => false],
        ['data' => 'avatar', 'name' => 'avatar', 'title' => 'Diamond','width'=>'12%',"orderable" => false, "searchable" => false],
        ['data' => 'packs','name' => 'packs','title' =>'Packs','width'=>'10%'],
        ['data' => 'pack_name','name' => 'pack_name','title' =>'Package Name','width'=>'10%'],
        ['data' => 'details', 'name' => 'details', 'title' => 'Page Content','width'=>'20%'],
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
        $diamonds=array();
        return view($this->pageLayout.'create', compact('diamonds'));
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Edit Diamond Pack
    -------------------------------------------------------------------------------------------- */
    public function edit($id){
        $diamonds = Diamond::where('id',$id)->first();

        if(!empty($diamonds)){
            return view($this->pageLayout.'edit',compact('diamonds','id'));
        }else{
            return redirect()->route('admin.diamond.index');
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Store Diamond Pack
    -------------------------------------------------------------------------------------------- */

    public function store(Request $request){
        $customMessages = [
        'packs.required' => 'Packs is Required',
        'pack_name.required' => 'Package Name is Required',
        'details.required' => 'Details is Required',
        'rewards.required' => 'Rewards is Required',
        'price.required' => 'Price is Required',
        'status.required' => 'Status is Required',
        ];
        $validatedData = Validator::make($request->all(),[
            'packs'        => 'required|numeric',
            'pack_name'        => 'required',
            'details'        => 'required',
            'rewards'         => 'nullable',
            'price'             => 'required',
            'status'          => 'required',
            ],$customMessages);
        if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try{
            if($request->hasFile('avatar')){
                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(10).'.'.$extension;
                Storage::disk('public')->putFileAs('diamond', $file,$filename);
            }else{
                $filename = 'default.png';
            }
            $diamondID=Diamond::create([
                'packs'         => @$request->get('packs'),
                'avatar'           => @$filename,
                'pack_name'            => @$request->get('pack_name'),
                'details'            => @$request->get('details'),
                'rewards'            => @$request->get('rewards'),
                'price'           => @$request->get('price'),
                'status'           => @$request->get('status'),
                ]);
            Notify::success('User Diamond Pack Created Successfully..!');
            return redirect()->route('admin.diamond.index');
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

        'packs.required' => 'Packs is Required',
        'pack_name.required' => 'Packs is Required',
        'details.required' => 'Details is Required',
        'rewards.required' => 'Rewards is Required',
        'price.required' => 'Price is Required',
        'status.required' => 'Status is Required',
         ];
         $validatedData = Validator::make($request->all(),[
            'packs'        => 'required|numeric',
            'pack_name'        => 'required',
            'details'        => 'required',
            'rewards'         => 'nullable',
            'price'             => 'required',
            'status'          => 'required',
        ],$customMessages);

         if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try{
            $oldDetails = Diamond::find($id);
            if($request->hasFile('avatar')){
                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(10).'.'.$extension;
                \Storage::disk('public')->putFileAs('diamond', $file,$filename);
            }else{
                if($oldDetails->avatar !== null){
                    $filename = $oldDetails->avatar;
                }else{
                    $filename = 'default.png';
                }
            }
            Diamond::where('id',$id)->update([
                'avatar'           => @$filename,
                'packs'         => @$request->get('packs'),
                'pack_name'         => @$request->get('pack_name'),
                'details'            => @$request->get('details'),
                'rewards'            => @$request->get('rewards'),
                'price'           => @$request->get('price'),
                'status'           => @$request->get('status'),
                    ]);
            Notify::success('Diamond Pack has been Updated Successfully..!');
            return redirect()->route('admin.diamond.index');
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
            $checkPack = Diamond::where('id',$id)->first();
            $checkPack->delete();
            Notify::success('Pack deleted Successfully..!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'Pack deleted Successfully..!'
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
            $diamond = Diamond::where('id',$request->id)->first();
            if($diamond === null){
                return redirect()->back()->with([
                    'status'    => 'warning',
                    'title'     => 'Warning!!',
                    'message'   => 'Diamond not found !!'
                ]);
            }else{
                if($diamond->status == "active"){
                    Diamond::where('id',$request->id)->update([
                        'status' => "block",
                    ]);
                }
                if($diamond->status == "block"){
                    Diamond::where('id',$request->id)->update([
                        'status'=> "active",
                    ]);
                }
            }
            Notify::success('Pack status updated successfully !!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'Pack status updated successfully.'
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
        $diamond = Diamond::find($request->id);
        return view($this->pageLayout.'show',compact('diamond'));
   }
}