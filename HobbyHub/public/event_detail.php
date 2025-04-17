<?php
require_once '../includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view event details', 'error');
}

// Check if event ID is provided
if (!isset($_GET['id'])) {
    redirect('events.php', 'No event specified', 'error');
}

$event_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($event_id === false) {
    redirect('events.php', 'Invalid event ID', 'error');
}

$success = '';
$errors = [];

// Handle RSVP submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = sanitizeInput($_POST['response'] ?? '');
    
    if (!in_array($response, ['attending', 'maybe', 'not_attending'])) {
        $errors[] = "Invalid RSVP response";
    }

    if (empty($errors)) {
        try {
            // Check if user already has an RSVP
            $stmt = $pdo->prepare("SELECT response_id FROM event_responses WHERE event_id = ? AND user_id = ?");
            $stmt->execute([$event_id, $_SESSION['user_id']]);
            $existing_response = $stmt->fetch();

            if ($existing_response) {
                // Update existing RSVP
                $stmt = $pdo->prepare("UPDATE event_responses SET response = ? WHERE response_id = ?");
                $stmt->execute([$response, $existing_response['response_id']]);
            } else {
                // Create new RSVP
                $stmt = $pdo->prepare("INSERT INTO event_responses (event_id, user_id, response) VALUES (?, ?, ?)");
                $stmt->execute([$event_id, $_SESSION['user_id'], $response]);
            }

            $success = "Your RSVP has been updated!";
        } catch(PDOException $e) {
            error_log("Error updating RSVP: " . $e->getMessage());
            $errors[] = "An error occurred while updating your RSVP";
        }
    }
}

try {
    // Fetch event details with creator info and group info
    $stmt = $pdo->prepare("
        SELECT 
            e.*,
            g.group_name,
            u.name as creator_name,
            (
                SELECT response 
                FROM event_responses 
                WHERE event_id = e.event_id 
                AND user_id = ?
            ) as user_response
        FROM events e
        JOIN `groups` g ON e.group_id = g.group_id
        JOIN users u ON e.created_by = u.user_id
        WHERE e.event_id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $event_id]);
    $event = $stmt->fetch();

    if (!$event) {
        redirect('events.php', 'Event not found', 'error');
    }

    // Check if user has access to this event
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM group_members 
        WHERE group_id = ? AND user_id = ?
    ");
    $stmt->execute([$event['group_id'], $_SESSION['user_id']]);
    if ($stmt->fetchColumn() == 0) {
        redirect('events.php', 'You do not have access to this event', 'error');
    }

    // Fetch RSVP counts
    $stmt = $pdo->prepare("
        SELECT 
            response,
            COUNT(*) as count
        FROM event_responses
        WHERE event_id = ?
        GROUP BY response
    ");
    $stmt->execute([$event_id]);
    $rsvp_counts = [];
    while ($row = $stmt->fetch()) {
        $rsvp_counts[$row['response']] = $row['count'];
    }

    // Fetch attendees
    $stmt = $pdo->prepare("
        SELECT 
            u.name,
            er.response,
            er.responded_at
        FROM event_responses er
        JOIN users u ON er.user_id = u.user_id
        WHERE er.event_id = ?
        ORDER BY er.responded_at ASC
    ");
    $stmt->execute([$event_id]);
    $attendees = $stmt->fetchAll();

} catch(PDOException $e) {
    error_log("Error fetching event details: " . $e->getMessage());
    redirect('events.php', 'An error occurred while fetching event details', 'error');
}
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <!-- Event Header -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            <?php echo htmlspecialchars($event['title']); ?>
                        </h1>
                        <div class="mt-1 flex items-center">
                            <p class="text-sm text-gray-500">
                                Organized by <?php echo htmlspecialchars($event['creator_name']); ?>
                                in <?php echo htmlspecialchars($event['group_name']); ?>
                            </p>
                        </div>
                    </div>
                    <?php if ($event['created_by'] === $_SESSION['user_id']): ?>
                        <button onclick="document.getElementById('editEventModal').classList.remove('hidden')"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-edit mr-2"></i> Edit Event
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            <i class="fas fa-calendar mr-1"></i> Date
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php echo formatDate($event['event_date']); ?>
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            <i class="fas fa-clock mr-1"></i> Time
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php echo formatTime($event['event_time']); ?>
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">
                            <i class="fas fa-map-marker-alt mr-1"></i> Location
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php echo htmlspecialchars($event['location']); ?>
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- RSVP Section -->
        <div class="mt-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        RSVP to this event
                    </h3>

                    <?php if ($success): ?>
                        <div class="mt-4 bg-green-50 border-l-4 border-green-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">
                                        <?php echo $success; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="mt-4 bg-red-50 border-l-4 border-red-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        There were errors with your submission
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            <?php foreach ($errors as $error): ?>
                                                <li><?php echo $error; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="event_detail.php?id=<?php echo $event_id; ?>" method="POST" class="mt-5">
                        <div class="flex items-center space-x-4">
                            <button type="submit" name="response" value="attending"
                                    class="<?php echo $event['user_response'] === 'attending' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'; ?> inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-check mr-2"></i> Attending
                            </button>
                            <button type="submit" name="response" value="maybe"
                                    class="<?php echo $event['user_response'] === 'maybe' ? 'bg-yellow-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'; ?> inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <i class="fas fa-question mr-2"></i> Maybe
                            </button>
                            <button type="submit" name="response" value="not_attending"
                                    class="<?php echo $event['user_response'] === 'not_attending' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'; ?> inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-times mr-2"></i> Not Attending
                            </button>
                        </div>
                    </form>

                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-900">Current RSVPs</h4>
                        <dl class="mt-2 grid grid-cols-3 gap-4">
                            <div class="bg-green-50 px-4 py-5 sm:p-6 rounded-lg">
                                <dt class="text-sm font-medium text-green-800">Attending</dt>
                                <dd class="mt-1 text-3xl font-semibold text-green-900">
                                    <?php echo $rsvp_counts['attending'] ?? 0; ?>
                                </dd>
                            </div>
                            <div class="bg-yellow-50 px-4 py-5 sm:p-6 rounded-lg">
                                <dt class="text-sm font-medium text-yellow-800">Maybe</dt>
                                <dd class="mt-1 text-3xl font-semibold text-yellow-900">
                                    <?php echo $rsvp_counts['maybe'] ?? 0; ?>
                                </dd>
                            </div>
                            <div class="bg-red-50 px-4 py-5 sm:p-6 rounded-lg">
                                <dt class="text-sm font-medium text-red-800">Not Attending</dt>
                                <dd class="mt-1 text-3xl font-semibold text-red-900">
                                    <?php echo $rsvp_counts['not_attending'] ?? 0; ?>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendees List -->
        <div class="mt-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Responses
                    </h3>
                    <div class="mt-4 divide-y divide-gray-200">
                        <?php foreach ($attendees as $attendee): ?>
                            <div class="py-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-800 font-medium">
                                                <?php echo strtoupper(substr($attendee['name'], 0, 2)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($attendee['name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Responded <?php echo formatDate($attendee['responded_at']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <?php
                                    $response_classes = [
                                        'attending' => 'bg-green-100 text-green-800',
                                        'maybe' => 'bg-yellow-100 text-yellow-800',
                                        'not_attending' => 'bg-red-100 text-red-800'
                                    ];
                                    $response_text = [
                                        'attending' => 'Attending',
                                        'maybe' => 'Maybe',
                                        'not_attending' => 'Not Attending'
                                    ];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $response_classes[$attendee['response']]; ?>">
                                        <?php echo $response_text[$attendee['response']]; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>
