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

class ConversationsController extends Controller
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
        $this->pageLayout = 'admin.pages.conversation.';
        $this->middleware('auth');
    }


    public function index(Builder $builder, Request $request)
    {
        try {
            $chatmessage = ChatMessage::groupBy('sender_id')->orderBy('id','desc');
            if (request()->ajax()) {
                return DataTables::of($chatmessage->get())
                    ->addIndexColumn()
                    ->editColumn('sender_id', function (ChatMessage $chatmessage) {
                        $use_img = User::where('id',$chatmessage->sender_id)->first();
                        if(ucfirst($use_img['avatar'] === '' || $use_img['avatar'] === 'default.png')){
                            return '<img src="'.url("storage/avatar/default.png").'" style="width: 45px;border-radius: 50%;height: 45px;border: 3px solid transparent;padding: 0px;box-shadow: 2px 2px 10px;">'."&nbsp;&nbsp;".'<a href='.route('admin.conversations.show',[$chatmessage->sender_id]).'  data-id="'.$chatmessage->sender_id.'" title="Chat Message">'.$use_img['email'].'</a>';
                        } else {
                            return '<img src="'.url("storage/avatar/".User::find($chatmessage->sender_id)->avatar).'" style="width: 45px;border-radius: 50%;height: 45px;border: 3px solid transparent;padding: 0px;box-shadow: 2px 2px 10px;">'."&nbsp;&nbsp;".'<a href='.route('admin.conversations.show',[$chatmessage->sender_id]).'  data-id="'.$chatmessage->sender_id.'" title="Chat Message">'.$use_img['email'].'</a>';
                        }
                    })
                    ->rawColumns(['sender_id'])
                    ->make(true);
                }
                $html = $builder->columns([
                ['data' => 'sender_id', 'name' => 'sender_id', 'title' => 'Sent From'],
            ])->parameters(['order' =>[]]);

            return view($this->pageLayout.'index', compact('html'));
        } catch (\Exception $e) {
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
            ]);
        }
    }
    public function show($sender_id){
    	$not_page =ChatMessage::where('sender_id',$sender_id)->pluck('sender_id')->toArray();
    	if(empty($not_page )){
    		return redirect()->route('admin.conversations.index');
    	} else{
    		$userID = $sender_id;
    		$allMessages = ChatMessage::where('sender_id',$userID)->pluck('receiver_id')->toArray();
    		$newuser = User::whereIn('id',$allMessages)->get();
    		return view('admin.pages.conversation.view',compact('newuser','userID'));
        }
    }

    public function converstionList(Request $request){
        // check for webmaster permission for access
        $use = $request->sent;
        $userID = $request->from;
        $allMessages = ChatMessage::with('userInfoFrom')
            ->where(function($query) use ($userID,$use){
                return $query->where('receiver_id',$use)
                    ->where('sender_id',$userID);
            })
            ->orWhere(function($query) use ($userID,$use){
                return $query->where('sender_id',$use)
                    ->where('receiver_id',$userID);
            })
            ->get();
            // dd($allMessages);
        $renderedView = view("admin.pages.conversation.list",compact('allMessages','use','userID'))->render();
        return response()->json(['html'=>$renderedView]);
    }
}
