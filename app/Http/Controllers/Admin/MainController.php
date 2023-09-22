<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReportUser;
use App\Models\PaymentHistory;
use App\Models\Gift;
use Carbon\Carbon;
use Response;
class MainController extends Controller
{
    protected $authLayout = '';
    protected $pageLayout = 'admin.pages.';

    public function __construct()
    {
        $this->authLayout = 'admin.auth.';
        $this->pageLayout = 'admin.pages.';
        $this->middleware('auth');
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Index Page
    -------------------------------------------------------------------------------------------- */


    public function index()
    {
        return view('front.auth.login');
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function Dashboard Page
    -------------------------------------------------------------------------------------------- */


    public function dashboard(){
      $gift_count = Gift::where('status','active')->count();
      $totalUsers = User::where('user_type','user')->count();
      $report_count = ReportUser::count();
      $paymenthistory_count = PaymentHistory::where('payment_status','success')->count();
      $graphCount = $this->getGraphCount();
        return view('admin.pages.dashboard',compact('totalUsers','report_count','paymenthistory_count','gift_count'));
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function GraphSection
    -------------------------------------------------------------------------------------------- */

    public function getGraphCount()
    {
        try {
            $start_user = User::orderBy('created_at', 'ASC')->first();
            $start_date = date("Y-m-d", strtotime($start_user->created_at));
            $end_user = User::orderBy('created_at', 'DESC')->first();
            //$end_date = date("Y-m-d");
            $end_date =  date("Y-m-d", strtotime('+1 day'));

            $formatted_dt1=Carbon::parse($start_date);
            $formatted_dt2=Carbon::parse($end_date);
            $date_diff=$formatted_dt1->diffInDays($formatted_dt2);
            $checkArray = $makeArray = $finalArray = array();

            for ($i = 0; (int)$i < (int)$date_diff; $i++) {
                $users = $report_count = $paymenthistory_count = 0;
                $startdate = $start_date;
                $date = date("Y-m-d", strtotime('+'.$i.' days', strtotime($startdate)));
                $users =  User::whereDate('created_at', $date)->get()->count();
                $report_count =  ReportUser::whereDate('created_at', $date)->get()->count();
                $gift_count =  Gift::where('status','active')->whereDate('created_at', $date)->get()->count();

                $arrayValues = $date.'.'.$users.'.'.$report_count.'.'.$paymenthistory_count.'.'.$gift_count;
                array_push($makeArray, $arrayValues);
            }
            $listIndex = array (
                array('Day Index', 'Users', 'Report', 'Transaction', 'Gift'),
            );

            foreach ($makeArray as $value) {
                $valuenext = explode('.', $value);
                array_push($finalArray, $valuenext);
            }
            $list = $finalArray;
            $fp = fopen('analytics_data.csv', 'w');
            foreach ($listIndex as $fields) {
                fputcsv($fp, $fields);
            }
            foreach ($list as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);
            return true;
        } catch (\Exception $e) {
            return back()->with([
                'alert-type'    => 'danger',
                'message'       => $e->getMessage()
            ]);
        }
    }

}
