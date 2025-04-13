<?php
require_once '../includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view discussions', 'error');
}

$errors = [];
$success = '';

// Handle new discussion post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    $group_id = filter_var($_POST['group_id'] ?? null, FILTER_VALIDATE_INT);

    // Validate input
    if (empty($title)) {
        $errors[] = "Title is required";
    }
    if (empty($content)) {
        $errors[] = "Content is required";
    }
    if ($group_id !== null) {
        // Verify group exists and user is a member
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM group_members 
                WHERE group_id = ? AND user_id = ?
            ");
            $stmt->execute([$group_id, $_SESSION['user_id']]);
            if ($stmt->fetchColumn() == 0) {
                $errors[] = "You must be a member of the group to post";
            }
        } catch(PDOException $e) {
            error_log("Error checking group membership: " . $e->getMessage());
            $errors[] = "An error occurred. Please try again later.";
        }
    }

    // Create discussion if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO discussion_posts (title, content, user_id, group_id) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$title, $content, $_SESSION['user_id'], $group_id]);
            
            $success = "Discussion created successfully!";
            // Clear form data after successful submission
            $title = $content = '';
            $group_id = null;
        } catch(PDOException $e) {
            error_log("Error creating discussion: " . $e->getMessage());
            $errors[] = "An error occurred while creating the discussion";
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

// Fetch discussions
try {
    $stmt = $pdo->prepare("
        SELECT 
            dp.*,
            u.name as author_name,
            g.group_name,
            COUNT(DISTINCT c.comment_id) as comment_count
        FROM discussion_posts dp
        JOIN users u ON dp.user_id = u.user_id
        LEFT JOIN `groups` g ON dp.group_id = g.group_id
        LEFT JOIN comments c ON dp.post_id = c.post_id
        WHERE dp.group_id IN (
            SELECT group_id FROM group_members WHERE user_id = ?
        ) OR dp.group_id IS NULL
        GROUP BY dp.post_id
        ORDER BY dp.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $discussions = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Error fetching discussions: " . $e->getMessage());
    $discussions = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom Hover Effects */
        .discussion-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .discussion-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .btn-primary {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            transform: scale(1.05);
        }
        .modal {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .input-field {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .input-field:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(90deg, #4f46e5, #a855f7);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        /* Background Decorations */
        .bg-decoration {
            animation: float 8s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0); }
            50% { transform: translateY(-30px); }
            100% { transform: translateY(0); }
        }
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen relative overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute inset-0 opacity-5 pointer-events-none">
            <div class="bg-decoration absolute rounded-full bg-indigo-200 w-80 h-80 -top-40 -left-40"></div>
            <div class="bg-decoration absolute rounded-full bg-purple-200 w-96 h-96 top-1/3 -right-48" style="animation-delay: 2s;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-12 bg-white rounded-xl shadow-md p-6 sticky top-0 z-10">
                <div class="mb-6 md:mb-0">
                    <h1 class="text-3xl font-bold gradient-text">Discussions</h1>
                    <p class="mt-2 text-gray-600 text-sm">Share ideas and connect with your community</p>
                </div>
                <button onclick="document.getElementById('createDiscussionModal').classList.remove('hidden')"
                        class="btn-primary inline-flex items-center px-6 py-3 rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i> Start Discussion
                </button>
            </div>

            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="mb-8 bg-green-100 border-l-4 border-green-500 p-4 rounded-lg shadow-sm animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3 text-xl"></i>
                        <p class="text-green-700 font-medium"><?php echo $success; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Discussions List -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($discussions)): ?>
                    <div class="col-span-full text-center py-16 bg-white rounded-xl shadow-md">
                        <i class="fas fa-comments text-6xl gradient-text mb-4"></i>
                        <h3 class="text-2xl font-semibold gradient-text">No Discussions Yet</h3>
                        <p class="mt-2 text-gray-500">Kick off the conversation with a new topic!</p>
                        <button onclick="document.getElementById('createDiscussionModal').classList.remove('hidden')"
                                class="mt-6 btn-primary inline-flex items-center px-6 py-3 rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i> Start Discussion
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($discussions as $discussion): ?>
                        <div class="discussion-card bg-white rounded-xl shadow-md overflow-hidden">
                            <a href="discussion_detail.php?id=<?php echo $discussion['post_id']; ?>">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-lg font-semibold text-indigo-600 truncate group-hover:text-indigo-800 transition-colors">
                                            <?php echo htmlspecialchars($discussion['title']); ?>
                                        </h3>
                                        <?php if ($discussion['group_name']): ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                <?php echo htmlspecialchars($discussion['group_name']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                        <?php echo htmlspecialchars($discussion['content']); ?>
                                    </p>
                                    <div class="space-y-2">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-user mr-2 text-indigo-400"></i>
                                            <?php echo htmlspecialchars($discussion['author_name']); ?>
                                        </p>
                                        <p class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-clock mr-2 text-indigo-400"></i>
                                            Posted <?php echo formatDate($discussion['created_at']); ?>
                                        </p>
                                        <p class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-comments mr-2 text-indigo-400"></i>
                                            <?php echo $discussion['comment_count']; ?> comments
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Create Discussion Modal -->
    <div id="createDiscussionModal" class="hidden fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="modal inline-block bg-white rounded-xl shadow-2xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="discussion.php" method="POST">
                    <div class="px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold gradient-text" id="modal-title">Start New Discussion</h3>
                            <button type="button" onclick="document.getElementById('createDiscussionModal').classList.add('hidden')"
                                    class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
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
                        <div class="mt-6 space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-pen text-gray-400"></i>
                                    </div>
                                    <input type="text" name="title" id="title"
                                           value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"
                                           class="input-field block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                                <textarea name="content" id="content" rows="5"
                                          class="input-field block w-full rounded-md border-gray-300 shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"><?php echo isset($content) ? htmlspecialchars($content) : ''; ?></textarea>
                                <p class="mt-2 text-xs text-gray-500">Share your thoughts or questions</p>
                            </div>
                            <div>
                                <label for="group_id" class="block text-sm font-medium text-gray-700">Post in Group (Optional)</label>
                                <select name="group_id" id="group_id"
                                        class="input-field block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">General Discussion</option>
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
                        <button type="button" onclick="document.getElementById('createDiscussionModal').classList.add('hidden')"
                                class="btn-primary px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary px-4 py-2 rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i> Create Discussion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>