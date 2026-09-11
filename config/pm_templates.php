<?php

/*
 * Standard preventive maintenance service types for the bus fleet.
 * interval_days: how often the service should recur (in days)
 * interval_km:   how often the service should recur (in km), null if km-based tracking is not applicable
 */
return [
    'Oil & Filter Change'        => ['interval_days' => 90,  'interval_km' => 5000],
    'Brake Inspection'           => ['interval_days' => 180, 'interval_km' => 10000],
    'Tire Rotation & Inspection' => ['interval_days' => 90,  'interval_km' => 8000],
    'Air Filter Replacement'     => ['interval_days' => 180, 'interval_km' => 15000],
    'Battery Inspection'         => ['interval_days' => 180, 'interval_km' => null],
    'Coolant Level Check'        => ['interval_days' => 90,  'interval_km' => null],
    'Transmission Service'       => ['interval_days' => 365, 'interval_km' => 30000],
    'Full Vehicle Inspection'    => ['interval_days' => 365, 'interval_km' => null],
    'Wheel Alignment'            => ['interval_days' => 180, 'interval_km' => 15000],
    'Wiper Blade Replacement'    => ['interval_days' => 365, 'interval_km' => null],
];
