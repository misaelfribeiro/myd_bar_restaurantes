<?php
return [
 'enabled' => true,
 'default_layout' => 'layouts.app',
 'routes_with_modern_layout' => [
 'app.*',
 'dashboard.main',
 ],
 'exclude_routes' => [
 'garcom.*',
 'api.*',
 'login*',
 'register*',
 ],
 'features' => [
 'dark_mode' => true,
 'sidebar_collapse' => true,
 'breadcrumbs' => true,
 'notifications' => true,
 'auto_refresh' => true,
 'keyboard_shortcuts' => true,
 ],
];