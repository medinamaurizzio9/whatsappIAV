<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\LeadAlert;
use App\Models\LeadEvent;
use App\Models\LeadPipeline;
use App\Models\LeadScore;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CommercialDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('commercial.dashboard', [
            'newLeads' => Client::whereDate('created_at', today())->count(),
            'hotLeads' => LeadScore::where('categoria', 'Caliente')->count(),
            'veryHotLeads' => LeadScore::where('categoria', 'Muy Caliente')->count(),
            'buyers' => LeadEvent::whereIn('evento', ['Quiere comprar', 'Consulta precio', 'Consulta catálogo'])->distinct('client_id')->count('client_id'),
            'investors' => LeadEvent::where('evento', 'Solicita inversión')->distinct('client_id')->count('client_id'),
            'affiliates' => LeadEvent::where('evento', 'Solicita afiliación')->distinct('client_id')->count('client_id'),
            'alerts' => LeadAlert::with(['client', 'intention'])->where('status', 'pendiente')->latest()->limit(8)->get(),
            'conversionByIntent' => LeadEvent::select('evento', DB::raw('count(*) as total'))->groupBy('evento')->orderByDesc('total')->limit(8)->get(),
            'scoreEvolution' => LeadEvent::selectRaw('date(created_at) as date, sum(puntos) as total')->groupBy('date')->orderBy('date')->limit(14)->get(),
            'derivationsByArea' => LeadEvent::with('derivationArea')->select('derivation_area_id', DB::raw('count(*) as total'))->whereNotNull('derivation_area_id')->groupBy('derivation_area_id')->get(),
        ]);
    }
}
