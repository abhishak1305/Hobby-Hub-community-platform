<?php
require_once '../includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view group details', 'error');
}

// Check if group ID is provided
if (!isset($_GET['id'])) {
    redirect('groups.php', 'No group specified', 'error');
}

$group_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($group_id === false) {
    redirect('groups.php', 'Invalid group ID', 'error');
}

try {
    // Fetch group details with creator info and member count
    $stmt = $pdo->prepare("
        SELECT g.*, 
               u.name as creator_name,
               COUNT(DISTINCT gm.user_id) as member_count,
               (SELECT role FROM group_members WHERE group_id = g.group_id AND user_id = ?) as user_role
        FROM `groups` g
        JOIN users u ON g.created_by = u.user_id
        LEFT JOIN group_members gm ON g.group_id = gm.group_id
        WHERE g.group_id = ?
        GROUP BY g.group_id
    ");
    $stmt->execute([$_SESSION['user_id'], $group_id]);
    $group = $stmt->fetch();

    if (!$group) {
        redirect('groups.php', 'Group not found', 'error');
    }

    // Fetch group members
    $stmt = $pdo->prepare("
        SELECT u.user_id, u.name, u.email, gm.role, gm.joined_at
        FROM group_members gm
        JOIN users u ON gm.user_id = u.user_id
        WHERE gm.group_id = ?
        ORDER BY gm.role = 'admin' DESC, gm.joined_at ASC
    ");
    $stmt->execute([$group_id]);
    $members = $stmt->fetchAll();

    // Fetch upcoming events
    $stmt = $pdo->prepare("
        SELECT e.*, 
               u.name as creator_name,
               COUNT(er.response_id) as rsvp_count
        FROM events e
        JOIN users u ON e.created_by = u.user_id
        LEFT JOIN event_responses er ON e.event_id = er.event_id AND er.response = 'attending'
        WHERE e.group_id = ? AND e.event_date >= CURDATE()
        GROUP BY e.event_id
        ORDER BY e.event_date ASC, e.event_time ASC
        LIMIT 5
    ");
    $stmt->execute([$group_id]);
    $events = $stmt->fetchAll();

    // Fetch recent discussions
    $stmt = $pdo->prepare("
        SELECT dp.*, 
               u.name as author_name,
               COUNT(c.comment_id) as comment_count
        FROM discussion_posts dp
        JOIN users u ON dp.user_id = u.user_id
        LEFT JOIN comments c ON dp.post_id = c.post_id
        WHERE dp.group_id = ?
        GROUP BY dp.post_id
        ORDER BY dp.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$group_id]);
    $discussions = $stmt->fetchAll();

} catch(PDOException $e) {
    error_log("Error fetching group details: " . $e->getMessage());
    redirect('groups.php', 'An error occurred while fetching group details', 'error');
}
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <!-- Group Header -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            <?php echo htmlspecialchars($group['group_name']); ?>
                        </h1>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Created by <?php echo htmlspecialchars($group['creator_name']); ?>
                        </p>
                    </div>
                    <?php if ($group['user_role'] === 'admin'): ?>
                        <button onclick="document.getElementById('editGroupModal').classList.remove('hidden')"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-edit mr-2"></i> Edit Group
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <div class="text-gray-700">
                    <?php echo nl2br(htmlspecialchars($group['description'])); ?>
                </div>
                <div class="mt-4 flex space-x-4">
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                        <i class="fas fa-users mr-1"></i> <?php echo $group['member_count']; ?> members
                    </span>
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        <i class="fas fa-calendar mr-1"></i> <?php echo count($events); ?> upcoming events
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Upcoming Events Section -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg leading-6 font-medium text-gray-900">Upcoming Events</h2>
                            <?php if ($group['user_role']): ?>
                                <a href="events.php?group_id=<?php echo $group_id; ?>" 
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                    <i class="fas fa-plus mr-1"></i> Create Event
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="border-t border-gray-200">
                        <?php if (empty($events)): ?>
                            <div class="px-4 py-5 sm:px-6 text-gray-500">
                                No upcoming events scheduled.
                            </div>
                        <?php else: ?>
                            <ul role="list" class="divide-y divide-gray-200">
                                <?php foreach ($events as $event): ?>
                                    <li>
                                        <a href="event_detail.php?id=<?php echo $event['event_id']; ?>" class="block hover:bg-gray-50">
                                            <div class="px-4 py-4 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <div class="text-sm font-medium text-indigo-600 truncate">
                                                        <?php echo htmlspecialchars($event['title']); ?>
                                                    </div>
                                                    <div class="ml-2 flex-shrink-0 flex">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            <?php echo $event['rsvp_count']; ?> attending
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="mt-2 sm:flex sm:justify-between">
                                                    <div class="sm:flex">
                                                        <p class="flex items-center text-sm text-gray-500">
                                                            <i class="fas fa-map-marker-alt flex-shrink-0 mr-1.5 text-gray-400"></i>
                                                            <?php echo htmlspecialchars($event['location']); ?>
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                        <i class="fas fa-calendar flex-shrink-0 mr-1.5 text-gray-400"></i>
                                                        <p>
                                                            <?php echo formatDate($event['event_date']); ?> at <?php echo formatTime($event['event_time']); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Discussions Section -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg leading-6 font-medium text-gray-900">Recent Discussions</h2>
                            <?php if ($group['user_role']): ?>
                                <a href="discussion.php"
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                    <i class="fas fa-plus mr-1"></i> Start Discussion
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="border-t border-gray-200">
                        <?php if (empty($discussions)): ?>
                            <div class="px-4 py-5 sm:px-6 text-gray-500">
                                No discussions yet.
                            </div>
                        <?php else: ?>
                            <ul role="list" class="divide-y divide-gray-200">
                                <?php foreach ($discussions as $discussion): ?>
                                    <li>
                                        <a href="discussion.php?id=<?php echo $discussion['post_id']; ?>" class="block hover:bg-gray-50">
                                            <div class="px-4 py-4 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <div class="text-sm font-medium text-indigo-600 truncate">
                                                        <?php echo htmlspecialchars($discussion['title']); ?>
                                                    </div>
                                                    <div class="ml-2 flex-shrink-0 flex">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            <?php echo $discussion['comment_count']; ?> comments
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="mt-2 sm:flex sm:justify-between">
                                                    <div class="sm:flex">
                                                        <p class="flex items-center text-sm text-gray-500">
                                                            <i class="fas fa-user flex-shrink-0 mr-1.5 text-gray-400"></i>
                                                            <?php echo htmlspecialchars($discussion['author_name']); ?>
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                        <i class="fas fa-clock flex-shrink-0 mr-1.5 text-gray-400"></i>
                                                        <p>
                                                            Posted <?php echo formatDate($discussion['created_at']); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <!-- Members Section -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h2 class="text-lg leading-6 font-medium text-gray-900">Members</h2>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            <?php echo $group['member_count']; ?> members in this group
                        </p>
                    </div>
                    <div class="border-t border-gray-200">
                        <ul role="list" class="divide-y divide-gray-200">
                            <?php foreach ($members as $member): ?>
                                <li class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span class="text-indigo-800 font-medium">
                                                        <?php echo strtoupper(substr($member['name'], 0, 2)); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($member['name']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Joined <?php echo formatDate($member['joined_at']); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <?php if ($member['role'] === 'admin'): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    Admin
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>
