<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function personal(Request $request)
    {
        return view('calendar.personal');
    }
    
    // dashboard() method removed - calendar now integrated in main dashboard
}
