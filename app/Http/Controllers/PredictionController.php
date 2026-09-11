<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\FailurePrediction;
use App\Services\PredictionService;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function __construct(private PredictionService $predictionService) {}

    public function index(Request $request)
    {
        $query = FailurePrediction::with('bus')
            ->latest('predicted_at');

        if ($request->filled('bus_id')) {
            $query->where('bus_id', $request->bus_id);
        }

        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }

        $predictions = $query->paginate(20)->withQueryString();
        $buses = Bus::orderBy('registration_number')->get();

        $riskSummary = FailurePrediction::selectRaw('risk_level, COUNT(*) as count')
            ->groupBy('risk_level')
            ->pluck('count', 'risk_level');

        return view('predictions.index', compact('predictions', 'buses', 'riskSummary'));
    }

    public function runForBus(Bus $bus)
    {
        $prediction = $this->predictionService->predictForBus($bus);

        if (! $prediction) {
            return back()->with('error', 'AI service unavailable. Ensure Flask is running on port 5000.');
        }

        return redirect()
            ->route('buses.show', $bus)
            ->with('success', "Prediction complete: {$prediction->risk_level} risk detected.");
    }

    public function runAll()
    {
        $buses = Bus::where('status', 'active')->get();
        $count = 0;

        foreach ($buses as $bus) {
            if ($this->predictionService->predictForBus($bus)) {
                $count++;
            }
        }

        return redirect()
            ->route('predictions.index')
            ->with('success', "Ran AI predictions for {$count} buses.");
    }

    public function show(FailurePrediction $prediction)
    {
        $prediction->load('bus');
        return view('predictions.show', compact('prediction'));
    }
}
