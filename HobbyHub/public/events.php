<?php
require_once '../includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view events', 'error');
}

$errors = [];
$success = '';

// Handle event creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $location = sanitizeInput($_POST['location'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $group_id = filter_var($_POST['group_id'] ?? 0, FILTER_VALIDATE_INT);

    // Validate input
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    if (empty($description)) {
        $errors[] = "Description is required";
    }
    if (empty($location)) {
        $errors[] = "Location is required";
    }
    if (empty($event_date)) {
        $errors[] = "Event date is required";
    }
    if (empty($event_time)) {
        $errors[] = "Event time is required";
    }
    if (!$group_id) {
        $errors[] = "Group selection is required";
    }

    // Validate date and time
    $event_datetime = strtotime($event_date . ' ' . $event_time);
    if ($event_datetime === false) {
        $errors[] = "Invalid date or time format";
    } elseif ($event_datetime < time()) {
        $errors[] = "Event cannot be scheduled in the past";
    }

    // Verify user is member of the group
    if ($group_id) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ? AND user_id = ?");
            $stmt->execute([$group_id, $_SESSION['user_id']]);
            if ($stmt->fetchColumn() == 0) {
                $errors[] = "You must be a member of the group to create an event";
            }
        } catch(PDOException $e) {
            error_log("Error checking group membership: " . $e->getMessage());
            $errors[] = "An error occurred. Please try again later.";
        }
    }

    // Create event if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO events (title, description, location, event_date, event_time, group_id, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $title,
                $description,
                $location,
                $event_date,
                $event_time,
                $group_id,
                $_SESSION['user_id']
            ]);
            
            $success = "Event created successfully!";
            $title = $description = $location = $event_date = $event_time = '';
            $group_id = null;
        } catch(PDOException $e) {
            error_log("Error creating event: " . $e->getMessage());
            $errors[] = "An error occurred while creating the event";
        }
    }
}

// Fetch user's groups for the dropdown
try {
    $stmt = $pdo->prepare("
        SELECT g.* FROM `groups` g
        JOIN group_members gm ON g.group_id = gm.group_id
        WHERE gm.user_id = ?
        ORDER BY g.group_name
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user_groups = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Error fetching user groups: " . $e->getMessage());
    $user_groups = [];
}

// Fetch upcoming events for user's groups
try {
    $stmt = $pdo->prepare("
        SELECT 
            e.*,
            g.group_name,
            u.name as creator_name,
            COUNT(DISTINCT er.response_id) as rsvp_count,
            EXISTS(
                SELECT 1 FROM event_responses 
                WHERE event_id = e.event_id 
                AND user_id = ? 
                AND response = 'attending'
            ) as is_attending
        FROM events e
        JOIN `groups` g ON e.group_id = g.group_id
        JOIN users u ON e.created_by = u.user_id
        LEFT JOIN event_responses er ON e.event_id = er.event_id AND er.response = 'attending'
        WHERE e.group_id IN (
            SELECT group_id FROM group_members WHERE user_id = ?
        )
        AND e.event_date >= CURDATE()
        GROUP BY e.event_id
        ORDER BY e.event_date ASC, e.event_time ASC
    ");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
    $events = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Error fetching events: " . $e->getMessage());
    $events = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom Hover Effects */
        .event-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #4f46e5;
            transform: scale(1.05);
        }
        .modal {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .input-field {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .input-field:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        /* Gradient Border for Empty State */
        .empty-state {
            background: linear-gradient(135deg, #6366f1, #a855f7);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        /* Smooth Scroll Behavior */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Upcoming Events</h1>
                <button onclick="document.getElementById('createEventModal').classList.remove('hidden')"
                        class="btn-primary inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-md text-base font-medium text-white bg-indigo-600">
                    <i class="fas fa-plus mr-2"></i> Create Event
                </button>
            </div>

            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-fade-in">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <p class="text-green-700 font-medium"><?php echo $success; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Events List -->
            <div class="mt-8">
                <?php if (empty($events)): ?>
                    <div class="text-center py-16 bg-white rounded-lg shadow-md">
                        <i class="fas fa-calendar-day text-6xl empty-state mb-4"></i>
                        <h3 class="text-2xl font-semibold empty-state">No Upcoming Events</h3>
                        <p class="mt-2 text-gray-500">Create an event to bring your community together!</p>
                        <button onclick="document.getElementById('createEventModal').classList.remove('hidden')"
                                class="mt-4 btn-primary inline-flex items-center px-4 py-2 rounded-lg text-white bg-indigo-600">
                            Start Planning
                        </button>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($events as $event): ?>
                            <div class="event-card bg-white rounded-lg shadow-md overflow-hidden">
                                <a href="event_detail.php?id=<?php echo $event['event_id']; ?>">
                                    <div class="p-6">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-indigo-600 truncate">
                                                <?php echo htmlspecialchars($event['title']); ?>
                                            </h3>
                                            <?php if ($event['is_attending']): ?>
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Attending
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                                            <?php echo htmlspecialchars($event['description']); ?>
                                        </p>
                                        <div class="mt-4 space-y-2">
                                            <p class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-map-marker-alt mr-2 text-indigo-400"></i>
                                                <?php echo htmlspecialchars($event['location']); ?>
                                            </p>
                                            <p class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-user mr-2 text-indigo-400"></i>
                                                <?php echo htmlspecialchars($event['creator_name']); ?>
                                            </p>
                                            <p class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-calendar mr-2 text-indigo-400"></i>
                                                <?php echo formatDate($event['event_date']) . ' at ' . formatTime($event['event_time']); ?>
                                            </p>
                                            <p class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-users mr-2 text-indigo-400"></i>
                                                <?php echo $event['rsvp_count']; ?> attending
                                            </p>
                                        </div>
                                        <span class="mt-4 inline-block px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                            <?php echo htmlspecialchars($event['group_name']); ?>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Create Event Modal -->
    <div id="createEventModal" class="hidden fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="modal inline-block bg-white rounded-xl shadow-2xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="events.php" method="POST">
                    <div class="px-6 pt-6 pb-4">
                        <h3 class="text-2xl font-bold text-gray-900" id="modal-title">Create New Event</h3>
                        <?php if (!empty($errors)): ?>
                            <div class="mt-4 bg-red-100 border-l-4 border-red-500 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                                    <div>
                                        <h3 class="text-sm font-medium text-red-800">
                                            There were <?php echo count($errors); ?> errors
                                        </h3>
                                        <ul class="mt-2 text-sm text-red-700 list-disc pl-5">
                                            <?php foreach ($errors as $error): ?>
                                                <li><?php echo $error; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="mt-6 space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Event Title</label>
                                <input type="text" name="title" id="title"
                                       value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"
                                       class="input-field mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="4"
                                          class="input-field mt-1 p-3 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                            </div>
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                <input type="text" name="location" id="location"
                                       value="<?php echo isset($location) ? htmlspecialchars($location) : ''; ?>"
                                       class="input-field mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="event_date" class="block text-sm font-medium text-gray-700">Date</label>
                                    <input type="date" name="event_date" id="event_date"
                                           value="<?php echo isset($event_date) ? htmlspecialchars($event_date) : ''; ?>"
                                           min="<?php echo date('Y-m-d'); ?>"
                                           class="input-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="event_time" class="block text-sm font-medium text-gray-700">Time</label>
                                    <input type="time" name="event_time" id="event_time"
                                           value="<?php echo isset($event_time) ? htmlspecialchars($event_time) : ''; ?>"
                                           class="input-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="group_id" class="block text-sm font-medium text-gray-700">Group</label>
                                <select name="group_id" id="group_id" required
                                        class="input-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select a group</option>
                                    <?php foreach ($user_groups as $group): ?>
                                        <option value="<?php echo $group['group_id']; ?>"
                                                <?php echo (isset($group_id) && $group_id == $group['group_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($group['group_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                        <button type="button"
                                onclick="document.getElementById('createEventModal').classList.add('hidden')"
                                class="btn-primary px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="btn-primary px-4 py-2 rounded-lg text-white bg-indigo-600">
                            Create Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>