<?php
// Start output buffering
ob_start();

// Include configuration and functions
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check for active page to highlight in navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HobbyHub - Connect with Passionate People</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #4f46e5;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after { width: 100%; }
        .nav-link.active {
            color: #4f46e5;
            font-weight: 500;
        }
        .nav-link.active::after {
            width: 100%;
            background-color: #4f46e5;
        }
        .logo-text {
            background: linear-gradient(90deg, #4f46e5, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: all 0.3s ease;
        }
        .logo-container:hover .logo-text {
            background: linear-gradient(90deg, #4338ca, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn-primary {
            transition: all 0.3s ease;
            background-size: 200% auto;
            background-image: linear-gradient(to right, #4f46e5 0%, #6366f1 50%, #4f46e5 100%);
        }
        .btn-primary:hover {
            background-position: right center;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2), 0 4px 6px -2px rgba(79, 70, 229, 0.1);
        }
        .mobile-menu-transition {
            transition: max-height 0.5s ease-in-out, opacity 0.4s ease-in-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        .mobile-menu-open {
            max-height: 500px;
            opacity: 1;
        }
        .notification-badge { animation: pulse 2s infinite; }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .user-dropdown {
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
        }
        .user-dropdown.active {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

                /* Dark mode styles */
        .dark {
            --tw-bg-opacity: 1;
            background-color: rgba(17, 24, 39, var(--tw-bg-opacity));
            color: rgba(229, 231, 235, var(--tw-text-opacity));
        }

        .dark .bg-white {
            background-color: rgba(31, 41, 55, var(--tw-bg-opacity));
        }

        .dark .text-gray-700 {
            color: rgba(209, 213, 219, var(--tw-text-opacity));
        }

        .dark .text-gray-900 {
            color: rgba(243, 244, 246, var(--tw-text-opacity));
        }

        .dark .border-gray-300 {
            border-color: rgba(55, 65, 81, var(--tw-border-opacity));
        }

        .dark .hover\:bg-gray-100:hover {
            background-color: rgba(55, 65, 81, var(--tw-bg-opacity));
        }

        .dark .bg-gray-50 {
            background-color: rgba(31, 41, 55, var(--tw-bg-opacity));
        }

        .dark .bg-indigo-100 {
            background-color: rgba(67, 56, 202, var(--tw-bg-opacity));
        }

        .dark .text-indigo-700 {
            color: rgba(199, 210, 254, var(--tw-text-opacity));
        }

        .dark .hover\:bg-indigo-200:hover {
            background-color: rgba(79, 70, 229, var(--tw-bg-opacity));
        }

        .dark .nav-link:hover::after {
            background-color: #818cf8;
        }

        .dark .nav-link.active {
            color: #818cf8;
        }

        .dark .nav-link.active::after {
            background-color: #818cf8;
        }
    </style>
</head>
<body class="bg-gray-50">
    <header>
        <!-- Announcement banner -->
        <div class="bg-indigo-600" id="announcement-banner">
            <div class="max-w-7xl mx-auto py-2 px-3 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center">
                        <span class="flex p-1 rounded-lg bg-indigo-800">
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </span>
                        <p class="ml-3 font-medium text-white truncate text-sm">
                            <span class="md:inline">Join our upcoming Photography Workshop on April 20th!</span>
                        </p>
                    </div>
                    <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                        <a href="events.php" class="flex items-center justify-center px-4 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-indigo-50">
                            Learn more
                        </a>
                    </div>
                    <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
                        <button type="button" class="banner-close -mr-1 flex p-2 rounded-md hover:bg-indigo-500 focus:outline-none" id="close-banner">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="index.php" class="logo-container flex items-center">
                            <svg class="h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="ml-2 text-xl font-bold logo-text">HobbyHub</span>
                        </a>
                        <div class="hidden sm:ml-8 sm:flex sm:space-x-6">
                            <?php if (isLoggedIn()): ?>
                                <a href="dashboard.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600 <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                                    <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Dashboard
                                </a>
                                <a href="groups.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600 <?php echo ($current_page == 'groups.php') ? 'active' : ''; ?>">
                                    <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Groups
                                </a>
                                <a href="discussion.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600 <?php echo ($current_page == 'discussion.php') ? 'active' : ''; ?>">
                                    <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    Discussion
                                </a>
                                <a href="events.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600 <?php echo ($current_page == 'events.php') ? 'active' : ''; ?>">
                                    <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Events
                                </a>
                                <a href="members.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600 <?php echo ($current_page == 'members.php') ? 'active' : ''; ?>">
                                    <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Members
                                </a>
                            <?php else: ?>
                                <a href="index.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600 <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                                    <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Home
                                </a>
                                <a href="features.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600 <?php echo ($current_page == 'features.php') ? 'active' : ''; ?>">
                                    <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Features
                                </a>
                                <a href="about_us.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-indigo-600 <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">
                                    <svg class="mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    About Us
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                        <!-- Search bar -->
                        <div class="relative">
                            <input type="text" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300">
                            <div class="absolute left-3 top-2.5">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <?php if (isLoggedIn()): ?>
                            <!-- Notifications -->
                            <div class="relative">
                                <button class="notification-button p-1 rounded-full text-gray-600 hover:text-indigo-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-300">
                                    <span class="sr-only">View notifications</span>
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 notification-badge"></span>
                                </button>
                                <!-- Dropdown menu -->
                                <div class="notification-dropdown origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-10 hidden">
                                    <div class="py-1">
                                        <div class="px-4 py-2 text-sm text-gray-700 font-medium">Notifications</div>
                                    </div>
                                    <div class="py-1">
                                        <a href="#" class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <p class="font-medium">New comment on your discussion</p>
                                                <p class="text-gray-500 text-xs mt-1">5 minutes ago</p>
                                            </div>
                                        </a>
                                        <a href="#" class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <p class="font-medium">You were added to Photography Group</p>
                                                <p class="text-gray-500 text-xs mt-1">1 hour ago</p>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="py-1">
                                        <a href="#" class="block px-4 py-2 text-sm text-center font-medium text-indigo-600 hover:bg-gray-100">
                                            View all notifications
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- User dropdown -->
                            <div class="relative">
                                <div>
                                    <button type="button" class="user-menu-button flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-300" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Open user menu</span>
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 hover:bg-indigo-200">
                                            <span class="text-sm font-medium leading-none text-indigo-700"><i class="fa-solid fa-user"></i></span>
                                        </span>
                                    </button>
                                </div>
                                <!-- User dropdown menu -->
                                <div class="user-dropdown origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 focus:outline-none z-10 hidden">
                                    <a href="profile.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="mr-3 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Your Profile
                                    </a>
                                    <a href="settings.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="mr-3 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Settings
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <a href="logout.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="mr-3 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </a>
                                </div>
                                
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="nav-link text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium transition-all duration-300">Login</a>
                            <a href="register.php" class="btn-primary text-white px-4 py-2 rounded-md text-sm font-medium hover:shadow-md">Register</a>
                        <?php endif; ?>
                    </div>

                    
                    <?php if (isLoggedIn()): ?>
                        <!-- Dark/Light mode toggle -->
                        <div class="relative">
                            <button id="theme-toggle" type="button" class="mt-2 p-2 rounded-full text-gray-500 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-300">
                                <svg id="theme-toggle-dark-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                                <svg id="theme-toggle-light-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-indigo-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition-all duration-300" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="mobile-menu-transition sm:hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    <div class="px-4 py-2">
                        <div class="relative">
                            <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <div class="absolute left-3 top-2.5">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="nav-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
                        <a href="groups.php" class="nav-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo ($current_page == 'groups.php') ? 'active' : ''; ?>">Groups</a>
                        <a href="discussion.php" class="nav-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo ($current_page == 'discussion.php') ? 'active' : ''; ?>">Discussion</a>
                        <a href="events.php" class="nav-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo ($current_page == 'events.php') ? 'active' : ''; ?>">Events</a>
                        <a href="members.php" class="nav-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo ($current_page == 'members.php') ? 'active' : ''; ?>">Members</a>
                    <?php else: ?>
                        <a href="index.php" class="nav-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                        <a href="features.php" class="nav-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo ($current_page == 'features.php') ? 'active' : ''; ?>">Features</a>
                        <a href="about.php" class="nav-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About Us</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.querySelector('.mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('mobile-menu-open');
        });

        // User dropdown toggle
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.querySelector('.user-dropdown');
        userMenuButton.addEventListener('click', () => {
            userDropdown.classList.toggle('active');
        });

        // Notification dropdown toggle
        const notificationButton = document.querySelector('.notification-button');
        const notificationDropdown = document.querySelector('.notification-dropdown');
        notificationButton.addEventListener('click', () => {
            notificationDropdown.classList.toggle('hidden');
        });

        // Announcement banner close
        const closeBanner = document.getElementById('close-banner');
        const announcementBanner = document.getElementById('announcement-banner');
        if (closeBanner && announcementBanner) {
            closeBanner.addEventListener('click', () => {
                announcementBanner.classList.add('transition-all', 'duration-300', 'ease-in-out');
                announcementBanner.style.maxHeight = announcementBanner.scrollHeight + 'px';
                void announcementBanner.offsetHeight;
                announcementBanner.style.maxHeight = '0';
                announcementBanner.style.overflow = 'hidden';
                announcementBanner.addEventListener('transitionend', () => {
                    announcementBanner.remove();
                }, { once: true });
            });
        }

        // User menu accessibility
        document.addEventListener('DOMContentLoaded', () => {
            const userMenuButton = document.querySelector('.user-menu-button');
            const userDropdown = document.querySelector('.user-dropdown');
            userMenuButton.addEventListener('click', () => {
                userDropdown.classList.toggle('hidden');
                const isExpanded = !userDropdown.classList.contains('hidden');
                userMenuButton.setAttribute('aria-expanded', isExpanded.toString());
            });
            document.addEventListener('click', (event) => {
                if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                    userMenuButton.setAttribute('aria-expanded', 'false');
                }
            });
        });

        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Check for saved theme preference or use system preference
        if (localStorage.getItem('color-theme') === 'dark' || (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            themeToggleDarkIcon.classList.remove('hidden');
        }

        // Toggle theme
        themeToggle.addEventListener('click', function() {
            // Toggle icons
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');
            
            // Toggle theme
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        });
    </script>
    </body>
    </html>
    