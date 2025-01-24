<?php
namespace App\Http\Controllers;

use App\Http\Controllers\CommonController;
use App\User;
use App\NotificationsAdmin;

use Auth;
use DB;
use Illuminate\Http\Request;
use Input;
use Response;
use Session;
use Validator;
use View;
use DatePeriod;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;

class NotificationController extends Controller
{

    //Notifications

    public function viewNotifications()
    {
        if (Auth::check()) {
            return view('admin.notifications');

        } else {
            return redirect('/admin/login');
        }
    }

    public function getNotifications(Request $request)
    {

        if (Auth::check()) {
            $input = $request->all();
            $start = $input['start'];
            $length = $input['length'];

            $input = $request->all();
            $columns = $request->get('columns');
            $dir = $request->input('order.0.dir');
            $order = $request->input('order.0.column');
            $status = $request->get('status','');

            $notesqry = NotificationsAdmin::where('id', '>', 0);
            $filteredqry = NotificationsAdmin::where('id', '>', 0);

            if (count($columns) > 0) {
                foreach ($columns as $key => $value) {
                    if (!empty($value['name']) && !empty($value['search']['value'])) {
                        if ($value['name'] == 'status') {
                            $notesqry->where($value['name'], 'like', $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', $value['search']['value'] . '%');
                        } else {
                            $notesqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                            $filteredqry->where($value['name'], 'like', '%' . $value['search']['value'] . '%');
                        }
                    }
                }
            }

            if(!empty($status)){
                $notesqry->where('status',$status);
                $filteredqry->where('status',$status);
            }
            if (!empty($order)) {
                $orderby = $columns[$order]['name'];
            } else {
                $orderby = 'id';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }
 

            $notes = $notesqry->skip($start)->take($length)->orderby($orderby, $dir)->get();
            $filters = $filteredqry->select('id')->count();

            $totalDataqry = DB::table('notifications_admin')->orderby('id', 'asc');
            $totalData = $totalDataqry->select('id')->count();

            $totalFiltered = $totalData;
            if (!empty($filters)) {
                $totalFiltered = $filters;
            }

            $data = [];
            if (!empty($notes)) {
                foreach ($notes as $post) {
                    $data[] = $post;
                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);

        } else {
            return redirect('/admin/login');
        }

    }

    public function postNotifications(Request $request)
    {
    	if (Auth::check()) {

	        $id = $request->id;

	        $notification_title = $request->notification_title;

	        $notification_message = $request->notification_message; 

	        $user_type = $request->user_type;

	        $user_ids = $request->user_ids;

	        $send_date = $request->send_date;

	        $status = $request->status; 

	        $validator = Validator::make($request->all(), [
	            'notification_title' => 'required',
	            'notification_message' => 'required',
	            'user_type' => 'required',
	            'user_ids' => 'required',
	            'send_date' => 'required',
	            'status' => 'required',
	        ]);

	        if ($validator->fails()) {

	            $msg = $validator->errors()->all();

	            return response()->json([ 
	                'status' => "FAILED",
	                'message' => "Please check your all inputs".implode(', ', $msg)
	            ]);
	        }
 

	        if ($id > 0) {
	            $notes = NotificationsAdmin::find($id);
	            $notes->updated_by = Auth::User()->id;
	            $notes->updated_at = date('Y-m-d H:i:s');
	        } else {
	            $notes = new NotificationsAdmin;
	            $notes->created_by = Auth::User()->id;
	            $notes->created_at = date('Y-m-d H:i:s');
	        } 

	        $notes->notification_title  = $notification_title;

	        $notes->notification_message = $notification_message; 

	        $notes->user_type = $user_type;

	        $notes->user_ids = implode(',',$user_ids); 

	        $notes->send_date = $send_date;

	        $notes->status = $status;  

        	$notes->save();

        	return response()->json(['status' => 'SUCCESS', 'message' => 'Notification has been saved'], 201);

	    } else {
	        return redirect('/admin/login');
	    }
    }

    public function editNotifications(Request $request)
    {
        if (Auth::check()) {
            $notes = NotificationsAdmin::where('id', $request->code)->get();
            if ($notes->isNotEmpty()) {
                return response()->json(['status' => 'SUCCESS', 'data' => $notes[0], 'message' => 'Notification Detail']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Notification Detail']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

    public function loadNotificationUsers(Request $request)
    {
        if (Auth::check()) {
            $user_type = $request->user_type;

            $users = DB::table('users')->where('status', 'ACTIVE')->where('approval_status', 'APPROVED')
            	->select('id', 'name', 'email', 'mobile');
            if($user_type == 'BOTH') {
            	$users->whereIN('user_type',['STUDENT', 'TEACHER']);
            }	elseif ($user_type == 'STUDENT') {
            	$users->whereIN('user_type',['STUDENT']);
            }  	elseif ($user_type == 'TEACHER') {
            	$users->whereIN('user_type',['TEACHER']);
            } 	else {
            	$users->whereIN('user_type',['STUDENT', 'TEACHER']);
            }
            $users = $users->get();

            if ($users->isNotEmpty()) {
            	$html = '';
            	foreach($users as $user) {
            		$html .= '<option value="'.$user->id.'">'.$user->name.' - '.$user->mobile.'</option>';
            	}
                return response()->json(['status' => 'SUCCESS', 'data' => $html, 'message' => 'Notification Users']);
            } else {
                return response()->json(['status' => 'FAILED', 'data' => [], 'message' => 'No Notification Users']);
            }
        } else {
            return redirect('/admin/login');
        }
    }

}