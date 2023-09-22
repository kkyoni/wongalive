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
use App\Models\User;
use App\Models\CardDetails;
use App\Models\FollowUser;
use App\Models\ChatMessage;
use Event;
use Settings;


class UsersController extends Controller
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
        $this->pageLayout = 'admin.pages.user.';
        $this->middleware('auth');
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Index Page
    -------------------------------------------------------------------------------------------- */

    public function index(Builder $builder, Request $request)
    {
        $users = User::where('user_type','user')->orderBy('updated_at','desc');
        if (request()->ajax()) {
            return DataTables::of($users->get())
            ->addIndexColumn()
            ->editColumn('status', function (User $users) {
                if ($users->status == "active") {
                    return '<span class="label label-success">Active</span>';
                } else {
                    return '<span class="label label-danger">Block</span>';
                }
            })
            ->editColumn('avatar', function (User $users) {
                    if($users->avatar){
                        $i='';
                        if (file_exists( 'storage/avatar/'.$users->avatar)) {
                            $i .= "<img src=".url("storage/avatar/".$users->avatar)." style='max-width:40px;max-height:40px;'/> ";
                        }else{
                            $i .= "<img src=".url("storage/avatar/default.png")."  style='max-width:40px;max-height:40px;'/> ";
                        }
                        return $i;
                    }else{
                        return "<img src=".url("storage/avatar/default.png")."  style='max-width:40px;max-height:40px;'/> ";
                    }
                })
            ->editColumn('action', function (User $users) {
                $action  = '';
                $action .= '<a class="btn btn-warning btn-circle btn-sm" href='.route('admin.edit',[$users->id]).'><i class="fa fa-pencil" data-toggle="tooltip" title="Edit"></i></a>';

                $action .='<a class="btn btn-danger btn-circle btn-sm m-l-10 deleteuser ml-2 mr-2" data-id ="'.$users->id.'" href="javascript:void(0)" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></a>';

                $action .= '<a href="javascript:void(0)" class="btn btn-primary btn-circle btn-sm ml-1 mr-2 ShowUser" data-id="'.$users->id.'" data-toggle="tooltip" title="View User"><i class="fa fa-eye"></i></a>';

                 if($users->status == "active"){
                        $action .= '<a href="javascript:void(0)" data-value="1" data-toggle="tooltip" title="Active" class="btn btn-sm btn-dark  m-l-10 btn-circle changeStatusRecord" data-id="'.$users->id.'" href="javascript:void(0)"><i class="fa fa-unlock"></i></a>';
                    }else{
                        $action .= '<a href="javascript:void(0)" data-value="0"  data-toggle="tooltip" title="Block" class="btn btn-sm btn-dark m-l-10 btn-circle changeStatusRecord" data-id="'.$users->id.'" href="javascript:void(0)"><i class="fa fa-lock"></i></a>';
                    }
                $check = ChatMessage::where('sender_id',$users->id)->first();
                if(!empty($check)){
                $action .= '<a class="btn btn-warning btn-circle btn-sm ml-2 mr-2" href='.route('admin.conversations.show',[$users->id]).'><i class="fa fa-comments" data-toggle="tooltip" title="Comments"></i></a>';    
                }
                
                return $action;
            })
            ->rawColumns(['action','status','avatar'])
            ->make(true);
        }
        $html = $builder->columns([
            ['data' => 'DT_RowIndex', 'name' => '', 'title' => 'Sr no','width'=>'3%',"orderable" => false, "searchable" => false],
            ['data' => 'avatar', 'name' => 'avatar', 'title' => 'Avatar','width'=>'4%',"orderable" => false, "searchable" => false],
            ['data' => 'id', 'name' => 'id', 'title' => 'User_id','width'=>'4%'],
            ['data' => 'username', 'name' => 'username', 'title' => 'Username','width'=>'3%'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email-Id','width'=>'5%'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status','width'=>'3%'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action','width'=>'15%',"orderable" => false, "searchable" => false],
            ])
        ->parameters([ 'order' =>[] ]);
        return view($this->pageLayout.'index',compact('html'));
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Create New User
    -------------------------------------------------------------------------------------------- */

    public function create(){
        $users=array();
        return view($this->pageLayout.'create', compact('users'));
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Edit User
    -------------------------------------------------------------------------------------------- */

    public function edit($id){
        $users = User::with('card_details')->where('id',$id)->first();

        if(!empty($users)){
            return view($this->pageLayout.'edit',compact('users','id'));
        }else{
            return redirect()->route('admin.index');
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Store User
    -------------------------------------------------------------------------------------------- */
    public function store(Request $request){
        $customMessages = [
        'username.required' => 'User Name is Required',
        'first_name.required' => 'First Name is Required',
        'last_name.required' => 'Last Name is Required',
        'contact_number.required' => 'Number is Required',
        'email.required' => 'Email is Required',
        'status.required' => 'Status is Required',
        'user_type.required' => 'User type is Required',
        'password'       => 'Password Type Is Required',
        ];
        $validatedData = Validator::make($request->all(),[
            'username'        => 'required',
            'first_name'        => 'required',
            'last_name'         => 'required',
            'email'             => 'required|email|unique:users,email',
            'contact_number'         => 'required|numeric|digits:10',
            'status'          => 'required',
            'user_type'          => 'required',
            'password'          => 'required',
            ],$customMessages);
        if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try{
            if($request->hasFile('avatar')){
                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(10).'.'.$extension;
                Storage::disk('public')->putFileAs('avatar', $file,$filename);
            }else{
                $filename = 'default.png';
            }
            $userID=User::create([
                'username'         => @$request->get('username'),
                'avatar'           => @$filename,
                'first_name'            => @$request->get('first_name'),
                'last_name'            => @$request->get('last_name'),
                'contact_number'           => @$request->get('contact_number'),
                'email'            => @$request->get('email'),
                'password'         => \Hash::make($request->get('password')),
                'status'           => @$request->get('status'),
                'user_type'        => @$request->get('user_type'),
            ]);
            Notify::success('New User Created Successfully..!');
            return redirect()->route('admin.index');
        }catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Update User Profile
    -------------------------------------------------------------------------------------------- */

        public function update(Request $request,$id){
         $customMessages = [
         'username.required' => 'User Name is Required',
         'first_name.required' => 'First Name is Required',
         'last_name.required' => 'Last Name is Required',
         'contact_number.required' => 'Number is Required',
         'email.required' => 'Email is Required',
         'status.required' => 'Status is Required',
         'password'       => 'Password Type Is Required',
         ];
         $validatedData = Validator::make($request->all(),[
                'username'        => 'required',
                'first_name'        => 'required',
                'last_name'         => 'required',
                'email'             => 'required|email',
                'contact_number'         => 'required|numeric|digits:10',
                'status'          => 'required',
                'password'          => 'required',
        ],$customMessages);

         if($validatedData->fails()){
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        try{
            $oldDetails = User::find($id);
            if($request->hasFile('avatar')){
                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(10).'.'.$extension;
                \Storage::disk('public')->putFileAs('avatar', $file,$filename);
            }else{
                if($oldDetails->avatar !== null){
                    $filename = $oldDetails->avatar;
                }else{
                    $filename = 'default.png';
                }
            }
            $password = $request->get('password') === null ?
            $oldDetails->password : \Hash::make($request->get('password'));
            User::where('id',$id)->update([
                'username'         => @$request->get('username'),
                'avatar'               => @$filename,
                'password'             => \Hash::make($request->get('password')),
                'first_name'            => @$request->get('first_name'),
                'last_name'            => @$request->get('last_name'),
                'contact_number'           => @$request->get('contact_number'),
                'email'            => @$request->get('email'),
                'user_type'        => @$request->get('user_type'),
                'status'           => @$request->get('status')
                ]);
            Notify::success('User Updated Successfully..!');
            return redirect()->route('admin.index');
        } catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Delete User
    -------------------------------------------------------------------------------------------- */

    public function delete($id){
        try{
            $checkUser = User::where('id',$id)->first();
            $checkUser->delete();
            $deletecarddetails = CardDetails::where('user_id',$id)->get();
            if(isset($deletecarddetails)){
                foreach ($deletecarddetails as $key => $value) {
                    $value->delete();
                }
            }
            Notify::success('User Deleted Successfully..!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'User Deleted Successfully..!'
                ]);
        }catch(\Exception $e){
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
                ]);
        }
    }
    /* -----------------------------------------------------------------------------------------
    @Description: Function for Change User Status
    -------------------------------------------------------------------------------------------- */

            public function change_status(Request $request){
        try{
            $user = User::where('id',$request->id)->first();
            if($user === null){
                return redirect()->back()->with([
                    'status'    => 'warning',
                    'title'     => 'Warning!!',
                    'message'   => 'User not found !!'
                ]);
            }else{
                if($user->status == "active"){
                    User::where('id',$request->id)->update([
                        'status' => "block",
                    ]);
                }
                if($user->status == "block"){
                    User::where('id',$request->id)->update([
                        'status'=> "active",
                    ]);
                }
            }
            Notify::success('User status Updated Successfully..!');
            return response()->json([
                'status'    => 'success',
                'title'     => 'Success!!',
                'message'   => 'User Status Updated Successfully..!'
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
    @Description: Function for Update profile details
    @input: name,email.
    @Output: update profile details
    -------------------------------------------------------------------------------------------- */
    public function updateProfile()
    {
        $user = User::where(['status'=>'active','id'=>Auth::user()->id])->first();
        if(empty($user)){
            Notify::error('User not found.');
            return redirect()->to('admin/dashboard');
        }
        return view($this->pageLayout.'updateprofile',compact('user'));
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Update profile details
    @input: name,email.
    @Output: update profile details
    -------------------------------------------------------------------------------------------- */

    public function updateProfileDetail(Request $request){
        $validatedData = $request->validate([
            'email'         => 'required|unique:users,email,'.Auth::user()->id,
            'first_name'    => 'required',
            'last_name'     => 'required',
            'contact_number'=> 'required|numeric|digits:10',
            'avatar'        => 'sometimes|mimes:jpeg,jpg,png'
            ]);
        try{
            $allowedfileExtension=['pdf','jpg','png'];
            if($request->hasFile('avatar')){
                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $filename = Str::random(10).'.'.$extension;
                Storage::disk('public')->putFileAs('avatar', $file,$filename);
            }else{
                $userDetail=User::where('id',Auth::user()->id)->first()->avatar;
                $filename = $userDetail;
            }
            User::where('id',Auth::user()->id)->update([
                'avatar'         => $filename,
                'email'          => $request->email,
                'first_name'     => $request->first_name,
                'last_name'      => $request->last_name,
                'contact_number' => $request->contact_number,
                ]);
            return redirect()->back();
        }catch(\Exception $e){
            Notify::error($e->getMessage());
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for update Password
    @input: old_password,password,password_confirmation.
    @Output: update Password
    -------------------------------------------------------------------------------------------- */
    public function updatePassword(Request $request){
        try{
            $validatedData = Validator::make($request->all(),[
               'old_password'          => 'required',
               'password'              => 'required|min:6',
               'password_confirmation' => 'required|min:6',
               ],

               [
               'old_password.required'          => 'The current password field is required.',
               'password.required'              => 'The new password field is required.',
               'password_confirmation.required' => 'The confirm password field is required.'
               ]
               );
            $validatedData->after(function() use($validatedData,$request){
                if($request->get('password') !== $request->get('password_confirmation')){
                    $validatedData->errors()->add('password_confirmation','The Confirm Password does not match.');
                }
            });
            if ($validatedData->fails()) {
                return redirect()->back()
                ->withErrors($validatedData)
                ->withInput();
            }
            if (\Hash::check($request->get('old_password'),auth()->user()->password) === false) {
                Notify::error('Your current Password does not matches with the Previous Password. Please try again.');
                return redirect()->back();
            }
            $user = auth()->user();
            $user->password =\Hash::make($request->get('password'));
            $user->save();
            Notify::success('Password Updated Successfully..!');
            return redirect()->back();
        }catch(Exception $e){
            dd($e->getMessage());
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for show count and followers / following
    -------------------------------------------------------------------------------------------- */

    public function show(Request $request) {
        $user = User::find($request->id);
        $wallet = User::where('id',$request->id)->withPackageAmount()->get();
        $follow_count = FollowUser::where('followed_user_id',$request->id)->count();
        $following_count = FollowUser::where('user_id',$request->id)->count();
        return view($this->pageLayout.'show',compact('user','wallet','follow_count','following_count'));
   }

}
