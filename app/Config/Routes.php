<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->post('login/authenticate', 'Auth::attemptLogin');
$routes->get('auth/refresh-captcha', 'Auth::refreshCaptcha');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->post('register/save', 'Auth::attemptRegister');
$routes->get('logout', 'Auth::logout');
$routes->get('auth/google', 'Auth::googleLogin');
$routes->get('auth/googleCallback', 'Auth::googleCallback');

// Profile Complete (wajib isi profil - dikecualikan dari profilecomplete filter)
$routes->get('profile/complete', 'Profile::complete', ['filter' => 'auth']);
$routes->post('profile/save-complete', 'Profile::saveComplete', ['filter' => 'auth']);

// Dashboard & Profile
$routes->group('', ['filter' => ['auth', 'rolecheck', 'profilecomplete']], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('profile', 'Profile::index');
    $routes->post('profile/update', 'Profile::update');
    $routes->post('profile/change-password', 'Profile::changePassword');
    $routes->post('profile/update-photo', 'Profile::updateProfilePic');
    $routes->post('profile/delete-photo', 'Profile::deleteProfilePic');

    // Notifications
    $routes->get('notifications', 'Notifications::index');
    $routes->get('notifications/mark-read/(:num)', 'Notifications::markRead/$1');
    $routes->get('notifications/unread-count', 'Notifications::getUnreadCount');
    $routes->get('notifications/mark-all-read', 'Notifications::markAllAsRead');
    $routes->post('notifications/bulk-mark-read', 'Notifications::bulkMarkRead');

    // Ticket routes
    $routes->group(
        'tickets',
        function ($routes) {
            $routes->get('/', 'Tickets::index');
            $routes->get('create', 'Tickets::create');
            $routes->post('store', 'Tickets::store');
            $routes->get('detail/(:segment)', 'Tickets::detail/$1');
            $routes->get('view/(:segment)', 'Tickets::detail/$1');
            $routes->post('reply/(:segment)', 'Tickets::reply/$1');
            $routes->post('update-status/(:segment)', 'Tickets::updateStatus/$1');
            $routes->post('assign/(:segment)', 'Tickets::assign/$1');
            $routes->post('rate/(:segment)', 'Tickets::rate/$1');
            $routes->get('export', 'Tickets::export');
            $routes->get('print', 'Tickets::printReport');
            $routes->post('delete/(:segment)', 'Tickets::delete/$1');
            $routes->post('update-link/(:segment)', 'Tickets::updateLink/$1');
            $routes->post('bulk-update-status', 'Tickets::bulkUpdateStatus');
        }
    );

    // Reports routes (accessible by everyone, scoped inside controller)
    $routes->get('admin/reports', 'Admin\Reports::index');
    $routes->get('admin/reports/excel', 'Admin\Reports::excel');
    $routes->get('admin/reports/pdf', 'Admin\Reports::pdf');
    $routes->get('admin/reports/print', 'Admin\Reports::printReport');
    $routes->get('admin/reports/printReport', 'Admin\Reports::printReport');
    $routes->post('admin/reports/update-link/(:segment)', 'Admin\Reports::updateLink/$1');
});

// Admin routes
$routes->group('admin', ['filter' => ['admin', 'rolecheck']], function ($routes) {
    $routes->get('users', 'Admin\Users::index');
    $routes->post('users/save', 'Admin\Users::save');
    $routes->post('users/delete', 'Admin\Users::delete');
    $routes->post('users/toggle_status', 'Admin\Users::toggleStatus');

    $routes->get('departments', 'Admin\Departments::index');
    $routes->post('departments/save', 'Admin\Departments::save');
    $routes->post('departments/delete', 'Admin\Departments::delete');
    $routes->post('departments/toggle_status', 'Admin\Departments::toggleStatus');

    $routes->get('categories', 'Admin\Categories::index');
    $routes->post('categories/save', 'Admin\Categories::save');
    $routes->post('categories/delete', 'Admin\Categories::delete');
    $routes->post('categories/toggle_status', 'Admin\Categories::toggleStatus');

    $routes->get('roles', 'Admin\Roles::index');
    $routes->post('roles/save', 'Admin\Roles::save');
    $routes->post('roles/delete', 'Admin\Roles::delete');

    $routes->get('audit-logs', 'Admin\AuditLogs::index');
});

// Admin & Staff (Operator) routes
$routes->group('admin', ['filter' => ['staff', 'rolecheck']], function ($routes) {
    // Knowledge Base Admin
    $routes->get('knowledge-base', 'Admin\KnowledgeBase::index');
    $routes->get('knowledge-base/create', 'Admin\KnowledgeBase::create');
    $routes->post('knowledge-base/store', 'Admin\KnowledgeBase::store');
    $routes->get('knowledge-base/(:num)/edit', 'Admin\KnowledgeBase::edit/$1');
    $routes->get('knowledge-base/(:num)/preview', 'Admin\KnowledgeBase::preview/$1');
    $routes->post('knowledge-base/(:num)/update', 'Admin\KnowledgeBase::update/$1');
    $routes->post('knowledge-base/(:num)/delete', 'Admin\KnowledgeBase::delete/$1');
    $routes->post('knowledge-base/(:num)/reembed', 'Admin\KnowledgeBase::reembed/$1');
    $routes->post('knowledge-base/reembed-all', 'Admin\KnowledgeBase::reembedAll');
    // Category JSON API
    $routes->get('knowledge-base/categories', 'Admin\KnowledgeBase::getCategories');
    $routes->post('knowledge-base/categories', 'Admin\KnowledgeBase::storeCategory');
    $routes->post('knowledge-base/categories/(:num)', 'Admin\KnowledgeBase::updateCategory/$1');
    $routes->delete('knowledge-base/categories/(:num)', 'Admin\KnowledgeBase::deleteCategory/$1');
});

// Knowledge Base (user)
$routes->group('knowledge-base', ['filter' => ['auth', 'rolecheck']], function ($routes) {
    $routes->get('/', 'KnowledgeBase::index');
    $routes->get('search', 'KnowledgeBase::search');
    $routes->get('(:segment)', 'KnowledgeBase::show/$1');
});

// AI Assistant
$routes->post('ai/chat', 'AiAssistant::chat', ['filter' => 'auth']);
$routes->get('ai/models', 'AiAssistant::models', ['filter' => 'auth']);
$routes->post('ai/clear-cache', 'AiAssistant::clearCache', ['filter' => 'admin']);

// Cron / Scheduled Tasks (diamankan dengan CRON_SECRET token, tanpa auth session)
$routes->get('cron/fetch-email-replies', 'Cron::fetchEmailReplies');
