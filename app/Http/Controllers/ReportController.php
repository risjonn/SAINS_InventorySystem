<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Division;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * Only authenticated users may access the report.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the unified stock report.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */

     public function stockReport(Request $request)
     {
         $reportType = $request->get('report_type','available');
 
         if ($reportType === 'loaned') {
             $loanedStocks = Stock::with(['product','division','latestTransaction'])
                                  ->where('status','LOAN')
                                  ->get();
 
             return view('stocks.stock_report', compact('loanedStocks','reportType'));
         }
 
         $availableStocks = Stock::with(['product','division'])
                                 ->where('status','available')
                                 ->get();
 
         return view('stocks.stock_report', compact('availableStocks','reportType'));
     }
 }
