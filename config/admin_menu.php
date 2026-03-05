<?php

return [
    [
        'label' => 'Manage Products',
        'icon' => 'fa-box',
        'url' => '#',
    ],
    [
        'label' => 'Manage Orders',
        'icon' => 'fa-receipt',
        'url' => '#',
    ],
    [
        'label' => 'Manage Users',
        'icon' => 'fa-users',
        'url' => fn() => route('admin.users.index'),
    ],
    [
        'label' => 'Manage Promotions',
        'icon' => 'fa-tag',
        'url' => '#',
    ],
    [
        'label' => 'Manage Reviews',
        'icon' => 'fa-star',
        'url' => '#',
    ],
    [
        'label' => 'Manage Categories',
        'icon' => 'fa-list',
        'url' => fn() => route('admin.categories.index'),
    ],
    [
        'label' => 'Manage News',
        'icon' => 'fa-newspaper',
        'url' => fn() => route('admin.news.index'),
    ],
    [
        'label' => 'Manage Contacts',
        'icon' => 'fa-envelope',
        'url' => fn() => route('admin.contacts.index'),
    ],
    [
        'label' => 'Refunds & Info',
        'icon' => 'fa-arrow-rotate-left',
        'url' => '#',
    ],
    [
        'label' => 'Permissions',
        'icon' => 'fa-lock',
        'url' => fn() => route('admin.permissions.index'),
    ],
    [
        'label' => 'Wallets',
        'icon' => 'fa-wallet',
        'url' => '#',
    ],
    [
        'label' => 'Reports & Stats',
        'icon' => 'fa-chart-bar',
        'url' => '#',
    ],
];
