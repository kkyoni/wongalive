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
use App\Models\ReportCategory;
use Event;
use Settings;


class ReportCategoryController extends Controller
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
        $this->pageLayout = 'admin.pages.report_category.';
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
            $report_category = ReportCategory::orderBy('id', 'DESC');
            if (request()->ajax()) {
                return DataTables::of($report_category->get())
                    ->addIndexColumn()
                
                ->editColumn('status', function (ReportCategory $report_category) {
                    if ($report_category->status == "active") {
                        return '<span class="label label-success">Active</span>';
                    } else {
                        return '<span class="label label-danger">Block</span>';
                    }
                })
                
                ->editColumn('action', function (ReportCategory $report_category) {
                        
                $action = '';
                $action .= '<a class="btn btn-warning btn-circle btn-sm" data-toggle="tooltip" title="Edit" href='.route('admin.report_category.edit',[$report_category->id]).'><i class="fa fa-pencil"></i></a>';
                $action .='<a class="btn btn-danger btn-sm m-l-10 btn-circle deletecategory ml-2 mr-2" data-id ="'.$report_category->id.'" href="javascript:void(0)" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>';
                $action .= '<a href="javascript:void(0)" class="btn btn-primary btn-circle btn-sm ml-1 mr-2 ShowCategory" data-id="'.$report_category->id.'" data-toggle="tooltip" title="View Category"><i class="fa fa-eye"></i></a>';
                if($report_category->status == "active"){
                  $action .= '<a href="javascript:void(0)" data-value="1"   data-toggle="tooltip" title="Active" class="btn btn-sm btn-dark btn-circle changeStatusRecord" data-id="'.$report_category->id.'"><i class="fa fa-unlock"></i></a>';
                }else{
                  $action .= '<a href="javascript:void(0)" data-value="0"  data-toggle="tooltip" title="Block" class="btn btn-sm btn-dark btn-square btn-circle changeStatusRecord" data-id="'.$report_category->id.'"><i class="fa fa-lock" ></i></a>';
                }
                return $action;
                    })
            ->rawColumns(['action','status','avatar'])
                    ->make(true);
            }
            $html = $builder->columns([
                ['data' => 'DT_RowIndex', 'name' => '', 'title' => 'Sr no','width'=>'2%',"orderable" => false, "searchable" => false],
                // ['data' => 'id', 'name' => 'id', 'title' => 'ID No.','width'=>'5%'],
                ['data' => 'category', 'name' => 'category', 'title' => 'Category','width'=>'20%'],
                ['data' => 'status', 'name' => 'status', 'title' => 'Status','width'=>'3%'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action','width'=>'7%',"orderable" => false],
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
    @Description: Function Create Category Page
    -------------------------------------------------------------------------------------------- */

    public function create()
    {
        $report_category=array();
        return view($this->pageLayout.'create', compact('report_category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Store Category Page
    -------------------------------------------------------------------------------------------- */

    public function store(Request $request){
        $customMessages = [
        'category.required' => 'Category is Required',
        'status.required' => 'Status is Required',
        ];
        $validatedData = Validator::make($request->all(),[
            'category'        => 'required',
            'status'          => 'required',
            ],$customMessages);
        if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try{
            
            $reportcategoryID=ReportCategory::create([
                'category'         => @$request->get('category'),
                'status'           => @$request->get('status'),
                ]);
            Notify::success('Report Category Created Successfully..!');
            return redirect()->route('admin.report_category.index');
        }catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Show Category Page
    -------------------------------------------------------------------------------------------- */

   public function show(Request $request)
    {
        $report_category = ReportCategory::find($request->id);
        return view($this->pageLayout.'show',compact('report_category'));
   }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Edit Category Page
    -------------------------------------------------------------------------------------------- */

    public function edit($id){
        $report_category = ReportCategory::where('id',$id)->first();

        if(!empty($report_category)){
            return view($this->pageLayout.'edit',compact('report_category','id'));
        }else{
            return redirect()->route('admin.report_category.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function Update Category Page
    -------------------------------------------------------------------------------------------- */

    public function update(Request $request,$id){
         $customMessages = [

        'category.required' => 'Category is Required',
        'status.required' => 'Status is Required',
         ];
         $validatedData = Validator::make($request->all(),[
            'category'        => 'required',
            'status'          => 'required',
        ],$customMessages);

         if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try{
            ReportCategory::where('id',$id)->update([
                'category'         => @$request->get('category'),
                'status'           => @$request->get('status'),
                    ]);
            Notify::success('Report Category Updated Successfully..!');
            return redirect()->route('admin.report_category.index');
        } catch(\Exception $e){
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
    @Description: Function Delete Category Page
    -------------------------------------------------------------------------------------------- */

    public function delete($id){
        try{
            $checkCategory = ReportCategory::where('id',$id)->first();
            $checkCategory->delete();
            Notify::success('Report Category Deleted Successfully..!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'Category Deleted Successfully..!'
                ]);
        }catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function CHange Status of Category Page
    -------------------------------------------------------------------------------------------- */

    public function change_status(Request $request){
        try{
            $report_category = ReportCategory::where('id',$request->id)->first();
            if($report_category === null){
                return redirect()->back()->with([
                    'status'    => 'warning',
                    'title'     => 'Warning!!',
                    'message'   => 'Report Category not found !!'
                ]);
            }else{
                if($report_category->status == "active"){
                    ReportCategory::where('id',$request->id)->update([
                        'status' => "block",
                    ]);
                }
                if($report_category->status == "block"){
                    ReportCategory::where('id',$request->id)->update([
                        'status'=> "active",
                    ]);
                }
            }
            Notify::success('Category Status Updated Successfully..!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'Category Status Updated Successfully..!'
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
