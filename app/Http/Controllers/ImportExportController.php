<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Imports\BulkExport;
use App\Imports\BulkImport;
use App\Imports\BulkStock;
use App\Imports\BulkImportJobCards;
use App\Models\QuestionBanks;
use App\Imports\BulkQb;
use App\Imports\BulkScholars;
use App\Imports\BulkStaffs;

use Maatwebsite\Excel\Facades\Excel;
use Response;
class ImportExportController extends Controller
{
    /**
    * 
    */
    public function importExportView()
    {
       return view('importexport');
    }
    public function import() 
    {
        Excel::import(new BulkImport,request()->file('file'));
           
        //return back();
    }

    public function export() 
    {
        return Excel::download(new BulkExport, 'bulkData.xlsx');
    }


    public function importExcelImport() 
    {
        Excel::import(new BulkImport,request()->file('excel_file'));
        
        return response()->json(['status' => 'SUCCESS', 'message' => 'Excel Uploaded']);
        //return back();
    }

    public function importExcelImportJobcards() 
    {
        Excel::import(new BulkImportJobCards,request()->file('excel_file'));
        
        return response()->json(['status' => 'SUCCESS', 'message' => 'Excel Uploaded']);
        //return back();
    }

    public function importbulkproductsstock() 
    {
        Excel::import(new BulkStock,request()->file('price_file'));
         return response()->json(['status' => 'SUCCESS', 'message' => 'Excel Uploaded']);
        //return back();
    }

    public function importQuestionbankExcel() 
    {
        Excel::import(new BulkQb,request()->file('importqb'));
        return response()->json(['status' => 'SUCCESS', 'message' => 'Excel Uploaded']);
      
    }


    public function getQuestionbankExcel(Request $request)
    {
        $checkedqb = $request->selected_ids;
       return Excel::download(new BulkExport($checkedqb),'qbs.xlsx');
        
    }

    public function importScholarslistExcel() 
    {
        Excel::import(new BulkScholars,request()->file('importqb'));
        return response()->json(['status' => 'SUCCESS', 'message' => 'Excel Uploaded']);
      
    }

    public function importStaffslistExcel() 
    {
        Excel::import(new BulkStaffs,request()->file('importqb'));
        return response()->json(['status' => 'SUCCESS', 'message' => 'Excel Uploaded']);
      
    }

}