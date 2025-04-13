<?php
require_once '../includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to access groups', 'error');
}

$errors = [];
$success = '';

// Handle group creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = sanitizeInput($_POST['group_name'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');

    // Validate input
    if (empty($group_name)) {
        $errors[] = "Group name is required";
    }
    if (empty($description)) {
        $errors[] = "Description is required";
    }

    // Create group if no errors
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Create the group
            $stmt = $pdo->prepare("INSERT INTO `groups` (group_name, description, created_by) VALUES (?, ?, ?)");
            $stmt->execute([$group_name, $description, $_SESSION['user_id']]);
            $group_id = $pdo->lastInsertId();

            // Add creator as admin member
            $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'admin')");
            $stmt->execute([$group_id, $_SESSION['user_id']]);

            $pdo->commit();
            $success = "Group created successfully!";
            
            // Clear form data after successful submission
            $group_name = $description = '';
        } catch(PDOException $e) {
            $pdo->rollBack();
            $errors[] = "An error occurred. Please try again later.";
            error_log("Group creation error: " . $e->getMessage());
        }
    }
}

// Fetch all groups with member count and creator info
try {
    $stmt = $pdo->prepare("
        SELECT g.*, 
               u.name as creator_name,
               COUNT(DISTINCT gm.user_id) as member_count,
               EXISTS(
                   SELECT 1 FROM group_members 
                   WHERE group_id = g.group_id 
                   AND user_id = ?
               ) as is_member
        FROM `groups` g
        JOIN users u ON g.created_by = u.user_id
        LEFT JOIN group_members gm ON g.group_id = gm.group_id
        GROUP BY g.group_id
        ORDER BY g.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $groups = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Error fetching groups: " . $e->getMessage());
    $groups = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hobby Groups</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom Hover Effects */
        .group-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .group-card:hover {
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
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
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
        /* Background Decorations */
        .bg-decoration {
            animation: float 8s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0); }
            50% { transform: translateY(-30px); }
            100% { transform: translateY(0); }
        }
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(90deg, #4f46e5, #a855f7);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
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
                    <h1 class="text-3xl font-bold gradient-text">Hobby Groups</h1>
                    <p class="mt-2 text-gray-600 text-sm">Discover communities that share your passions</p>
                </div>
                <button onclick="document.getElementById('createGroupModal').classList.remove('hidden')"
                        class="btn-primary inline-flex items-center px-6 py-3 rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i> Create Group
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

            <!-- Groups Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($groups as $group): ?>
                    <div class="group-card bg-white rounded-xl shadow-md overflow-hidden">
                        <!-- Group Header Image -->
                        <div class="h-40 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-users text-white text-6xl opacity-30"></i>
                        </div>
                        <!-- Group Content -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xl font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    <?php echo htmlspecialchars($group['group_name']); ?>
                                </h3>
                                <?php if ($group['is_member']): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Member
                                    </span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                <?php echo htmlspecialchars($group['description']); ?>
                            </p>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-users mr-1"></i> <?php echo $group['member_count']; ?> members
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    <i class="fas fa-user mr-1"></i> <?php echo htmlspecialchars($group['creator_name']); ?>
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <?php if ($group['is_member']): ?>
                                    <a href="group_detail.php?id=<?php echo $group['group_id']; ?>" 
                                       class="btn-primary inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        <i class="fas fa-door-open mr-2"></i> View Group
                                    </a>
                                <?php else: ?>
                                    <form action="join_group.php" method="POST" class="inline">
                                        <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                        <button type="submit" 
                                                class="btn-primary inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                                            <i class="fas fa-user-plus mr-2"></i> Join Group
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <span class="text-xs text-gray-500">
                                    <?php echo date('M j, Y', strtotime($group['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Empty State -->
                <?php if (empty($groups)): ?>
                    <div class="col-span-full text-center py-16 bg-white rounded-xl shadow-md">
                        <i class="fas fa-users-slash text-6xl gradient-text mb-4"></i>
                        <h3 class="text-2xl font-semibold gradient-text">No Groups Found</h3>
                        <p class="mt-2 text-gray-500">Start a community around your favorite hobby!</p>
                        <button onclick="document.getElementById('createGroupModal').classList.remove('hidden')"
                                class="mt-6 btn-primary inline-flex items-center px-6 py-3 rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i> Create Group
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    <div id="createGroupModal" class="hidden fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="modal inline-block bg-white rounded-xl shadow-2xl transform transition-all sm:max-w-lg sm:w-full">
                <form action="groups.php" method="POST">
                    <div class="px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold gradient-text" id="modal-title">Create New Group</h3>
                            <button type="button" onclick="document.getElementById('createGroupModal').classList.add('hidden')"
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
                                <label for="group_name" class="block text-sm font-medium text-gray-700">Group Name</label>
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-users text-gray-400"></i>
                                    </div>
                                    <input type="text" name="group_name" id="group_name"
                                           value="<?php echo isset($group_name) ? htmlspecialchars($group_name) : ''; ?>"
                                           class="input-field block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="4"
                                          class="input-field p-3 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                                <p class="mt-2 text-xs text-gray-500">Describe what makes your group special</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('createGroupModal').classList.add('hidden')"
                                class="btn-primary px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary px-4 py-2 rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i> Create Group
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>