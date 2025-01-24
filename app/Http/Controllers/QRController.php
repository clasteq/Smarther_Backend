<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;


use Session;
use Response;
use Log;
use DB;
use Input;
use Validator;
use Hash;
use Auth;
use Mail;
use Yajra\DataTables\DataTables;
use View;
use Excel;
use App\Imports\HelpImport;



class QRController extends Controller
{
	
	public function generateQrCode($ref_no) 
	{
	    \QrCode::size(500)
	            ->format('png')
	            ->generate($ref_no, public_path('uploads/qrs/'.$ref_no.'.png'));
	    return $ref_no.'.png';
	    //return view('qr-code');
	}
}