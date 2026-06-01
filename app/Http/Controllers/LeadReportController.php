<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\LeadEvent;
use App\Models\LeadPipeline;
use App\Models\LeadScore;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeadReportController extends Controller
{
    public function index(): View
    {
        return view('reports.leads', [
            'topLeads' => Client::with('leadScore')->whereHas('leadScore')->get()->sortByDesc('leadScore.score')->take(10),
            'byCity' => Client::select('city', DB::raw('count(*) as total'))->groupBy('city')->orderByDesc('total')->get(),
            'byIntent' => LeadEvent::select('evento', DB::raw('count(distinct client_id) as total'))->groupBy('evento')->orderByDesc('total')->get(),
            'byStage' => LeadPipeline::select('stage', DB::raw('count(*) as total'))->groupBy('stage')->get(),
            'salesConversion' => LeadPipeline::where('stage', 'Cerrado ganado')->count(),
            'lostConversion' => LeadPipeline::where('stage', 'Cerrado perdido')->count(),
            'hotCount' => LeadScore::whereIn('categoria', ['Caliente', 'Muy Caliente'])->count(),
        ]);
    }
}
