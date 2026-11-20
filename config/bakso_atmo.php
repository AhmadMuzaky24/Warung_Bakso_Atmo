<?php

return [
    'name'       => env('BRAND_NAME', 'Warung Pak Atmo'),
    'gofood'     => env('GOFOOD_URL', 'https://gofood.co.id/karawang/restaurant/mie-ayam-bakso-pak-atmo-jl-manggis-no-2-fd6afb45-a183-413a-aef8-167f3ba89b12'),
    'shopeefood' => env('SHOPEEFOOD_URL', 'https://shopee.co.id/universal-link/now-food/shop/21330945?deep_and_deferred=1&shareChannel=copy_link'),
    'hide_unavailable' => env('MENU_HIDE_UNAVAILABLE', false),

'branches' => [
    [
        'name'    => 'Cabang 1: Jl. Manggis No. 2, Nagasari, Kec. Karawang Barat',
        'address' => 'Jl. Manggis No. 2, Nagasari, Kec. Karawang Barat',
        'hours'   => 'Setiap hari, 10.00 - 21.30', 
        'maps'    => 'https://share.google/Mdurb5tWMaYUns7Ty',
    ],
    [
        'name'    => 'Cabang 2: Jl. Raya Syeh Quro, Palumbonsari, Kec. Karawang Timur',
        'address' => 'Jl. Raya Syeh Quro, Palumbonsari, Kec. Karawang Timur',        
        'hours'   => 'Setiap hari, 10.00 - 22.00',  
        'maps'    => 'https://share.google/jE7rzirmrKY51aFe0',
    ],
    ],

    'booking' => [
        'min_days_before' => (int) env('BOOKING_MIN_DAYS_BEFORE', 1),
        'dp_percent'      => (int) env('BOOKING_DP_PERCENT', 50),
        'cutoff_time'     => env('BOOKING_CUTOFF_TIME', '14:30'),
    ],

    'whatsapp' => env('BOOKING_WHATSAPP_NUMBER'),

    'registration' => [
        // Email akun customer dibuat otomatis dari nomor HP (email sintetis, tidak dikirimi surat).
        'email_placeholder' => 'pelanggan.baksoatmo.local',
    ],
];