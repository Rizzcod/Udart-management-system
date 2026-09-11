<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Services\FailurePatternService;

class FailurePatternController extends Controller
{
    public function __construct(private FailurePatternService $service) {}

    public function index()
    {
        $topFailures   = $this->service->topFailureTypes(10);
        $highRiskBuses = $this->service->highRiskBuses(90, 3);
        $monthlyTrend  = $this->service->monthlyTrend();
        $fleetSummary  = $this->service->fleetAiSummary();
        $buses         = Bus::orderBy('registration_number')->get();

        return view('failure-patterns.index', compact(
            'topFailures', 'highRiskBuses', 'monthlyTrend', 'fleetSummary', 'buses'
        ));
    }

    public function show(Bus $bus)
    {
        $patterns    = $this->service->busFailurePattern($bus);
        $rootCauses  = $this->service->rootCauseAnalysis($bus);

        return view('failure-patterns.show', compact('bus', 'patterns', 'rootCauses'));
    }
}
