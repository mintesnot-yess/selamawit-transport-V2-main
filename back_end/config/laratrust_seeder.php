<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'admin' => [
            'profile' => 'r,u',
            'users' => 'c,r,u,d',
            'order' => 'c,r,u,d',
            'payment_collection' => 'c,r,u,d',
            'commission' => 'c,r,u,d',
            'expense' => 'c,r,u,d',
            'vehicles' => 'c,r,u,d',
            'drivers' => 'c,r,u,d',
            'location' => 'c,r,u,d',
            'load_type' => 'c,r,u,d',
            'bank' => 'c,r,u,d',
            'staff_user' => 'c,r,u,d',
            'role' => 'c,r,u,d',
            'give_permission' => 'c,r,u,d',
            'report' => 'c,r,u,d',
            'expense_type' => 'c,r,u,d',
            'client' => 'c,r,u,d',
        ],
    ],
    'user' => [
        'profile' => 'r,u',
        'users' => 'c,r,u,d',
        'order' => 'c,r,u,d',
        'payment_collection' => 'c,r,u,d',
        'commission' => 'c,r,u,d',
        'expense' => 'c,r,u,d',
        'vehicles' => 'c,r,u,d',
        'drivers' => 'c,r,u,d',
        'location' => 'c,r,u,d',
        'load_type' => 'c,r,u,d',
        'bank' => 'c,r,u,d',
        'staff_user' => 'c,r,u,d',
        'role' => 'c,r,u,d',
        'give_permission' => 'c,r,u,d',
        'report' => 'c,r,u,d',
        'expense_type' => 'c,r,u,d',
        'client' => 'c,r,u,d',
    ],




    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];
