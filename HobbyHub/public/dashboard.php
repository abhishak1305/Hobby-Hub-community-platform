<?php
require_once '../includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to access your dashboard', 'error');
}

// Fetch user data and statistics (example queries)
try {
    // Fetch user details
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT name, email FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    // Fetch group count
    $stmt = $pdo->prepare("SELECT COUNT(*) as group_count FROM group_members WHERE user_id = ?");
    $stmt->execute([$userId]);
    $groupCount = $stmt->fetchColumn();

    // Fetch discussion count
    $stmt = $pdo->prepare("SELECT COUNT(*) as discussion_count FROM discussion_posts WHERE user_id = ?");
    $stmt->execute([$userId]);
    $discussionCount = $stmt->fetchColumn();

    // Fetch event count
    $stmt = $pdo->prepare("SELECT COUNT(*) as event_count FROM events WHERE created_by = ?");
    $stmt->execute([$userId]);
    $eventCount = $stmt->fetchColumn();

} catch(PDOException $e) {
    error_log("Error fetching dashboard data: " . $e->getMessage());
    redirect('dashboard.php', 'An error occurred while fetching your data', 'error');
}

// Add custom CSS for the enhanced dashboard
?>

<style>
    .stats-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .groups-card {
        border-left-color: #3b82f6;
    }
    
    .groups-card:hover {
        background-color: rgba(59, 130, 246, 0.05);
    }
    
    .discussions-card {
        border-left-color: #10b981;
    }
    
    .discussions-card:hover {
        background-color: rgba(16, 185, 129, 0.05);
    }
    
    .events-card {
        border-left-color: #f59e0b;
    }
    
    .events-card:hover {
        background-color: rgba(245, 158, 11, 0.05);
    }
    
    .activity-item {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }
    
    .activity-item:hover {
        background-color: #f9fafb;
        border-left-color: #6366f1;
        padding-left: 8px;
    }
    
    .activity-item.discussion:hover {
    border-left-color: #10b981; /* Green border for discussions */
    }

    .activity-item.event:hover {
    border-left-color: #f59e0b; /* Amber border for events */
    }


    .dashboard-header {
        background: linear-gradient(90deg, #f3f4f6 0%, #ffffff 100%);
        border-bottom: 1px solid #e5e7eb;
    }
    
    .highlight {
        color: #4f46e5;
        font-weight: 500;
    }
    
    .card-icon {
        transition: transform 0.3s ease;
    }
    
    .stats-card:hover .card-icon {
        transform: scale(1.2);
    }
    
    .btn-action {
        transition: all 0.2s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    
    .welcome-pulse {
        animation: pulse 2s infinite;
    }
</style>

<div class="dashboard-header py-6 mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm text-indigo-600 font-medium">Dashboard</span>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">Welcome, <span class="highlight"><?php echo htmlspecialchars($user['name']); ?></span>!</h1>
                <p class="text-gray-500 mt-1">Here's what's happening with your account today.</p>
            </div>
            <div>
                <a href="groups.php" class="inline-block">
                    <button class="btn-action bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg flex items-center">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create New
                    </button>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- User Statistics Cards -->
        <div class="stats-card groups-card bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-md p-3 mr-4">
                        <svg class="card-icon h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Your Groups</h3>
                        <p class="mt-1 text-3xl font-semibold text-blue-600"><?php echo $groupCount; ?></p>
                        <p class="mt-1 text-sm text-gray-500">Active memberships</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="groups.php" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center">
                        View all groups
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="stats-card discussions-card bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-md p-3 mr-4">
                        <svg class="card-icon h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Your Discussions</h3>
                        <p class="mt-1 text-3xl font-semibold text-green-600"><?php echo $discussionCount; ?></p>
                        <p class="mt-1 text-sm text-gray-500">Started conversations</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="discussion.php" class="text-sm font-medium text-green-600 hover:text-green-800 flex items-center">
                        View all discussions
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="stats-card events-card bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="bg-amber-100 rounded-md p-3 mr-4">
                        <svg class="card-icon h-6 w-6 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Your Events</h3>
                        <p class="mt-1 text-3xl font-semibold text-amber-600"><?php echo $eventCount; ?></p>
                        <p class="mt-1 text-sm text-gray-500">Created events</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="events.php" class="text-sm font-medium text-amber-600 hover:text-amber-800 flex items-center">
                        View all events
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-medium text-gray-900">Recent Activity</h2>
            <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center">
                View all activity
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <ul class="divide-y divide-gray-200">
                <!-- Activity items with enhanced styling -->
                <li class="activity-item py-4 px-4">
                    <div class="flex items-start">
                        <div class="bg-blue-100 rounded-full p-2 mr-4">
                            <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div >
                            <p class="text-sm font-medium text-gray-900">Joined a new group: <span class="font-semibold text-blue-600">Photography Club</span></p>
                            <p class="text-sm text-gray-500 mt-1">2 days ago</p>
                            <p class="text-sm text-gray-600 mt-2">Connect with fellow photography enthusiasts and share your work.</p>
                        </div>
                    </div>
                </li>
                <li class="activity-item discussion py-4 px-4">
                    <div class="flex items-start">
                        <div class="bg-green-100 rounded-full p-2 mr-4">
                            <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Created a discussion: <span class="font-semibold text-green-600">Best Camera for Beginners</span></p>
                            <p class="text-sm text-gray-500 mt-1">3 days ago</p>
                            <p class="text-sm text-gray-600 mt-2">Your discussion has received 8 replies so far.</p>
                            <div class="mt-3">
                                <a href="discussion.php" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none">
                                    View discussion
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="activity-item event py-4 px-4">
                    <div class="flex items-start">
                        <div class="bg-amber-100 rounded-full p-2 mr-4">
                            <svg class="h-5 w-5 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">RSVP'd to event: <span class="font-semibold text-amber-600">Monthly Meetup</span></p>
                            <p class="text-sm text-gray-500 mt-1">1 week ago</p>
                            <p class="text-sm text-gray-600 mt-2">Event scheduled for April 15, 2023 at 6:00 PM.</p>
                            <div class="mt-3 flex space-x-2">
                                <a href="events.php?id=456" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-amber-700 bg-amber-100 hover:bg-amber-200 focus:outline-none">
                                    View event details
                                </a>
                                <button class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none">
                                    Cancel RSVP
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="mt-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="groups.php?action=create" class="btn-action bg-white overflow-hidden shadow rounded-lg hover:shadow-md border-t-4 border-indigo-500 p-5 flex flex-col items-center justify-center text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <h3 class="text-gray-900 font-medium">Create Group</h3>
                <p class="text-sm text-gray-500 mt-1">Start a new community</p>
            </a>

            <a href="events.php?action=create" class="btn-action bg-white overflow-hidden shadow rounded-lg hover:shadow-md border-t-4 border-amber-500 p-5 flex flex-col items-center justify-center text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="text-gray-900 font-medium">Schedule Event</h3>
                <p class="text-sm text-gray-500 mt-1">Plan a gathering</p>
            </a>

            <a href="discussion.php" class="btn-action bg-white overflow-hidden shadow rounded-lg hover:shadow-md border-t-4 border-green-500 p-5 flex flex-col items-center justify-center text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <h3 class="text-gray-900 font-medium">Start Discussion</h3>
                <p class="text-sm text-gray-500 mt-1">Share your thoughts</p>
            </a>

            <a href="#" class="btn-action bg-white overflow-hidden shadow rounded-lg hover:shadow-md border-t-4 border-purple-500 p-5 flex flex-col items-center justify-center text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h3 class="text-gray-900 font-medium">Edit Profile</h3>
                <p class="text-sm text-gray-500 mt-1">Update your information</p>
            </a>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="mt-8 mb-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Upcoming Events</h2>
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="p-4 flex items-center justify-between border-b border-gray-200">
                <div class="flex items-center">
                    <div class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium mr-3">
                        Tomorrow
                    </div>
                    <h3 class="text-base font-medium text-gray-900">Photography Workshop</h3>
                </div>
                <div class="text-sm text-gray-500">
                    April 18, 2025 • 3:00 PM
                </div>
            </div>
            <div class="p-4 flex items-center justify-between border-b border-gray-200">
                <div class="flex items-center">
                    <div class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-xs font-medium mr-3">
                        Next Week
                    </div>
                    <h3 class="text-base font-medium text-gray-900">Monthly Meetup</h3>
                </div>
                <div class="text-sm text-gray-500">
                    April 22, 2025 • 6:00 PM
                </div>
            </div>
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium mr-3">
                        2 Weeks
                    </div>
                    <h3 class="text-base font-medium text-gray-900">Nature Photography Hike</h3>
                </div>
                <div class="text-sm text-gray-500">
                    April 25, 2025 • 9:00 AM
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>