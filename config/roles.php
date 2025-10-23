<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Role Definitions
    |--------------------------------------------------------------------------
    |
    | Define all available roles with their labels and styling.
    | Used across the application for consistent role display.
    |
    */

    'definitions' => [
        'hr' => [
            'label' => 'HR',
            'color' => 'purple',
            'description' => 'Human Resources - Mengelola member dan rekrutmen',
        ],
        'pm' => [
            'label' => 'PM',
            'color' => 'blue',
            'description' => 'Project Manager - Mengelola project dan tiket',
        ],
        'sekretaris' => [
            'label' => 'Sekretaris',
            'color' => 'cyan',
            'description' => 'Sekretaris - Dokumentasi dan administrasi',
        ],
        'bendahara' => [
            'label' => 'Bendahara',
            'color' => 'green',
            'description' => 'Bendahara - Mengelola keuangan dan RAB',
        ],
        'media' => [
            'label' => 'Media',
            'color' => 'pink',
            'description' => 'Media - Konten dan publikasi',
        ],
        'pr' => [
            'label' => 'PR',
            'color' => 'orange',
            'description' => 'Public Relations - Komunikasi eksternal',
        ],
        'bisnis_manager' => [
            'label' => 'Bisnis Manager',
            'color' => 'yellow',
            'description' => 'Bisnis Manager - Strategi bisnis',
        ],
        'talent_manager' => [
            'label' => 'Talent Manager',
            'color' => 'indigo',
            'description' => 'Talent Manager - Mengelola talent',
        ],
        'researcher' => [
            'label' => 'Researcher',
            'color' => 'teal',
            'description' => 'Researcher - Riset dan evaluasi',
        ],
        'talent' => [
            'label' => 'Talent',
            'color' => 'rose',
            'description' => 'Talent - Performer dan kreator',
        ],
        'member' => [
            'label' => 'Member',
            'color' => 'gray',
            'description' => 'Member - Anggota umum',
        ],
        'guest' => [
            'label' => 'Guest',
            'color' => 'slate',
            'description' => 'Guest - Akses terbatas',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tailwind Color Classes
    |--------------------------------------------------------------------------
    |
    | Predefined Tailwind CSS classes for badges and buttons.
    | Ensures consistent styling across the application.
    |
    */

    'badge_classes' => [
        'purple' => 'bg-purple-100 text-purple-700 border-purple-200',
        'blue' => 'bg-blue-100 text-blue-700 border-blue-200',
        'cyan' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
        'green' => 'bg-green-100 text-green-700 border-green-200',
        'pink' => 'bg-pink-100 text-pink-700 border-pink-200',
        'orange' => 'bg-orange-100 text-orange-700 border-orange-200',
        'yellow' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'indigo' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
        'teal' => 'bg-teal-100 text-teal-700 border-teal-200',
        'rose' => 'bg-rose-100 text-rose-700 border-rose-200',
        'gray' => 'bg-gray-100 text-gray-700 border-gray-200',
        'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
    ],

    'badge_classes_dark' => [
        'purple' => 'bg-purple-500 text-white border-purple-600',
        'blue' => 'bg-blue-500 text-white border-blue-600',
        'cyan' => 'bg-cyan-500 text-white border-cyan-600',
        'green' => 'bg-green-500 text-white border-green-600',
        'pink' => 'bg-pink-500 text-white border-pink-600',
        'orange' => 'bg-orange-500 text-white border-orange-600',
        'yellow' => 'bg-yellow-500 text-white border-yellow-600',
        'indigo' => 'bg-indigo-500 text-white border-indigo-600',
        'teal' => 'bg-teal-500 text-white border-teal-600',
        'rose' => 'bg-rose-500 text-white border-rose-600',
        'gray' => 'bg-gray-500 text-white border-gray-600',
        'slate' => 'bg-slate-500 text-white border-slate-600',
    ],
];
