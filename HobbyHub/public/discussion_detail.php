<?php
require_once '../includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view discussions', 'error');
}

// Check if discussion ID is provided
if (!isset($_GET['id'])) {
    redirect('discussion.php', 'No discussion specified', 'error');
}

$discussion_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($discussion_id === false) {
    redirect('discussion.php', 'Invalid discussion ID', 'error');
}

$errors = [];
$success = '';

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_content = sanitizeInput($_POST['content'] ?? '');

    if (empty($comment_content)) {
        $errors[] = "Comment content is required";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$discussion_id, $_SESSION['user_id'], $comment_content]);
            $success = "Comment added successfully!";
        } catch(PDOException $e) {
            error_log("Error adding comment: " . $e->getMessage());
            $errors[] = "An error occurred while adding your comment";
        }
    }
}

try {
    // Fetch discussion details with author info and group info
    $stmt = $pdo->prepare("
        SELECT 
            dp.*,
            u.name as author_name,
            g.group_name,
            g.group_id
        FROM discussion_posts dp
        JOIN users u ON dp.user_id = u.user_id
        LEFT JOIN `groups` g ON dp.group_id = g.group_id
        WHERE dp.post_id = ?
    ");
    $stmt->execute([$discussion_id]);
    $discussion = $stmt->fetch();

    if (!$discussion) {
        redirect('discussion.php', 'Discussion not found', 'error');
    }

    // Check if user has access to this discussion
    if ($discussion['group_id']) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM group_members 
            WHERE group_id = ? AND user_id = ?
        ");
        $stmt->execute([$discussion['group_id'], $_SESSION['user_id']]);
        if ($stmt->fetchColumn() == 0) {
            redirect('discussion.php', 'You do not have access to this discussion', 'error');
        }
    }

    // Fetch comments with author info
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            u.name as author_name
        FROM comments c
        JOIN users u ON c.user_id = u.user_id
        WHERE c.post_id = ?
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$discussion_id]);
    $comments = $stmt->fetchAll();

} catch(PDOException $e) {
    error_log("Error fetching discussion details: " . $e->getMessage());
    redirect('discussion.php', 'An error occurred while fetching the discussion', 'error');
}
?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <!-- Discussion Header -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            <?php echo htmlspecialchars($discussion['title']); ?>
                        </h1>
                        <div class="mt-1 flex items-center">
                            <p class="text-sm text-gray-500">
                                Posted by <?php echo htmlspecialchars($discussion['author_name']); ?>
                                on <?php echo formatDate($discussion['created_at']); ?>
                            </p>
                            <?php if ($discussion['group_name']): ?>
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <?php echo htmlspecialchars($discussion['group_name']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($discussion['user_id'] === $_SESSION['user_id']): ?>
                        <button onclick="document.getElementById('editDiscussionModal').classList.remove('hidden')"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <div class="prose max-w-none text-gray-700">
                    <?php echo nl2br(htmlspecialchars($discussion['content'])); ?>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="mt-8">
            <h2 class="text-lg font-medium text-gray-900">
                Comments (<?php echo count($comments); ?>)
            </h2>

            <!-- Success Message -->
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

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="mt-4 bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                There were <?php echo count($errors); ?> errors with your submission
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

            <!-- Add Comment Form -->
            <div class="mt-4">
                <form action="discussion_detail.php?id=<?php echo $discussion_id; ?>" method="POST">
                    <div>
                        <label for="content" class="sr-only">Comment</label>
                        <textarea rows="3" name="content" id="content"
                                  class="shadow-sm block w-full  p-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border border-gray-300 rounded-md"
                                  placeholder="Add a comment..."></textarea>
                    </div>
                    <div class="mt-3 flex items-center justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Post Comment
                        </button>
                    </div>
                </form>
            </div>

            <!-- Comments List -->
            <div class="mt-6 space-y-6">
                <?php if (empty($comments)): ?>
                    <p class="text-gray-500 text-center py-4">No comments yet. Be the first to comment!</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-800 font-medium">
                                                <?php echo strtoupper(substr($comment['author_name'], 0, 2)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($comment['author_name']); ?>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <?php echo formatDate($comment['created_at']); ?>
                                        </p>
                                        <div class="mt-2 text-sm text-gray-700">
                                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                        </div>
                                    </div>
                                    <?php if ($comment['user_id'] === $_SESSION['user_id']): ?>
                                        <div class="flex-shrink-0 self-center flex">
                                            <div class="relative inline-block text-left">
                                                <button type="button"
                                                        class="text-gray-400 hover:text-gray-500"
                                                        onclick="toggleCommentOptions(<?php echo $comment['comment_id']; ?>)">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div id="comment-options-<?php echo $comment['comment_id']; ?>"
                                                     class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                                     role="menu">
                                                    <div class="py-1" role="none">
                                                        <button type="button"
                                                                class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                                                onclick="editComment(<?php echo $comment['comment_id']; ?>)">
                                                            Edit
                                                        </button>
                                                        <button type="button"
                                                                class="text-red-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                                                onclick="deleteComment(<?php echo $comment['comment_id']; ?>)">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCommentOptions(commentId) {
    const options = document.getElementById(`comment-options-${commentId}`);
    options.classList.toggle('hidden');
}

// Close comment options when clicking outside
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('[id^="comment-options-"]');
    dropdowns.forEach(dropdown => {
        if (!dropdown.contains(event.target) && 
            !event.target.matches('button') && 
            !event.target.matches('.fa-ellipsis-v')) {
            dropdown.classList.add('hidden');
        }
    });
});
</script>

<?php
require_once '../includes/footer.php';
?>
