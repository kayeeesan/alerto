<?php

return [
    'models' => [
        'users' => \App\Models\User::class,
        'roles' => \App\Models\Role::class,
        'user_roles' => \App\Models\UserRole::class,
        'regions' => \App\Models\Region::class,
        'provinces' => \App\Models\Province::class,
        'municipalities' => \App\Models\Municipality::class,
        'rivers' => \App\Models\River::class,
        'sensors_under_alerto' => \App\Models\SensorUnderAlerto::class,
        'sensors_under_ph' => \App\Models\SensorUnderPh::class,
        'responses' => \App\Models\Response::class,
        'thresholds' => \App\Models\Threshold::class,
        'alerts' => \App\Models\Alert::class,
        'user_logs' => \App\Models\UserLog::class,
        'staffs' => \App\Models\Staff::class,
        // 'contact_messages' => \App\Models\ContactMessage::class,
        'notifications' => \App\Models\Notification::class,
        'series' => \App\Models\Series::class,
    ],
];