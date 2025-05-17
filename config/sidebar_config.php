<?php
return [
    'admin' => [
        'dashboard' => [
            'title' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'url' => 'dashboard.php',
            'role' => ['ADMIN']
        ],
        'livelihood' => [
            'title' => 'Livelihood',
            'icon' => 'fas fa-hand-holding-usd',
            'submenu' => [
                'records' => [
                    'title' => 'Livelihood Records',
                    'url' => 'livelihood_records.php',
                    'role' => ['ADMIN']
                ],
                'monitoring' => [
                    'title' => 'Livelihood Monitoring',
                    'url' => 'livelihood_monitoring.php',
                    'role' => ['ADMIN']
                ]
            ]
        ],
        'household' => [
            'title' => 'Household',
            'icon' => 'fas fa-home',
            'submenu' => [
                'records' => [
                    'title' => 'Household Records',
                    'url' => 'household_records.php',
                    'role' => ['ADMIN']
                ],
                'assessment' => [
                    'title' => 'Household Assessment',
                    'url' => 'household_assessment.php',
                    'role' => ['ADMIN']
                ]
            ]
        ],
        'reports' => [
            'title' => 'Reports',
            'icon' => 'fas fa-chart-bar',
            'url' => 'reports.php',
            'role' => ['ADMIN']
        ],
        'users' => [
            'title' => 'Users',
            'icon' => 'fas fa-users',
            'url' => 'users.php',
            'role' => ['ADMIN']
        ],
        'settings' => [
            'title' => 'Settings',
            'icon' => 'fas fa-cog',
            'url' => 'settings.php',
            'role' => ['ADMIN']
        ]
    ],
    'member' => [
        'dashboard' => [
            'title' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'url' => 'dashboard.php',
            'role' => ['MEMBER']
        ],
        'livelihood' => [
            'title' => 'Livelihood',
            'icon' => 'fas fa-hand-holding-usd',
            'submenu' => [
                'records' => [
                    'title' => 'Livelihood Records',
                    'url' => 'livelihood_records.php',
                    'role' => ['MEMBER']
                ]
            ]
        ],
        'household' => [
            'title' => 'Household',
            'icon' => 'fas fa-home',
            'submenu' => [
                'records' => [
                    'title' => 'Household Records',
                    'url' => 'household_records.php',
                    'role' => ['MEMBER']
                ]
            ]
        ],
        'profile' => [
            'title' => 'Profile',
            'icon' => 'fas fa-user',
            'url' => 'profile.php',
            'role' => ['MEMBER']
        ]
    ]
];
