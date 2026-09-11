<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\DailyBusAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Portfolio demo dataset: the full sample data plus today's dispatch, with the
 * featured demo driver (config/demo.php) guaranteed a bus so the driver portal
 * has a live trip to run.
 *
 *   php artisan migrate:fresh --seed --seeder=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DatabaseSeeder::class);

        $email  = collect(config('demo.accounts'))->firstWhere('key', 'driver')['email'] ?? null;
        $driver = User::where('email', $email)->first();

        if ($driver) {
            // Prefer the driver's own bus; otherwise the first bus that can be dispatched.
            $bus = Bus::orderByRaw('driver_id = ? desc', [$driver->id])
                ->orderBy('registration_number')
                ->get()
                ->first(fn (Bus $bus) => $bus->isDispatchable());

            if ($bus) {
                DailyBusAssignment::create([
                    'bus_id'        => $bus->id,
                    'driver_id'     => $driver->id,
                    'assigned_date' => today(),
                    'assigned_by'   => null,
                    'status'        => 'active',
                    'route'         => config('bus_routes')[0],
                ]);
            }
        }

        // Assign the remaining dispatchable buses to the other drivers.
        Artisan::call('buses:assign-daily', ['--force' => true]);
    }
}
