<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Portfolio demo mode
    |--------------------------------------------------------------------------
    |
    | When enabled, the landing page and the sign-in page list the seeded
    | demo accounts below so visitors can explore each role. Only enable this
    | on a database populated by
    | `php artisan migrate:fresh --seed --seeder=DemoSeeder` — never on a
    | database holding real users.
    |
    */

    'enabled' => (bool) env('DEMO_MODE', false),

    // Must match the password set for these accounts in RoleSeeder.
    'password' => 'password',

    'accounts' => [
        [
            'key'     => 'admin',
            'role'    => 'Administrator',
            'email'   => 'admin@udart.co.tz',
            'icon'    => 'bi-shield-lock',
            'summary' => 'Full access, including user accounts, role permissions and per-user overrides.',
        ],
        [
            'key'     => 'supervisor',
            'role'    => 'Supervisor',
            'email'   => 'supervisor@udart.co.tz',
            'icon'    => 'bi-clipboard-check',
            'summary' => 'Daily dispatch, work-order approvals, preventive maintenance and reports.',
        ],
        [
            'key'     => 'technician',
            'role'    => 'Technician',
            'email'   => 'tech@udart.co.tz',
            'icon'    => 'bi-wrench-adjustable',
            'summary' => 'Assigned work orders, maintenance records, IoT readings and predictions.',
        ],
        [
            'key'     => 'storekeeper',
            'role'    => 'Storekeeper',
            'email'   => 'store@udart.co.tz',
            'icon'    => 'bi-box-seam',
            'summary' => 'Spare-parts catalogue, stock levels and inventory reports.',
        ],
        [
            'key'     => 'driver',
            'role'    => 'Driver',
            'email'   => 'driver@udart.co.tz',
            'icon'    => 'bi-bus-front',
            'summary' => 'Separate driver portal: today\'s bus, trips and breakdown reports.',
        ],
    ],

];
