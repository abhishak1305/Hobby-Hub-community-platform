<?php
require_once '../includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view members', 'error');
}

// Get search parameters
$search = sanitizeInput($_GET['search'] ?? '');
$group_id = filter_var($_GET['group_id'] ?? 0, FILTER_VALIDATE_INT);

try {
    // Fetch user's groups for filter dropdown
    $stmt = $pdo->prepare("
        SELECT g.* FROM `groups` g
        JOIN group_members gm ON g.group_id = gm.group_id
        WHERE gm.user_id = ?
        ORDER BY g.group_name
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user_groups = $stmt->fetchAll();

    // Build the query based on filters
    $query = "
        SELECT DISTINCT 
            u.user_id,
            u.name,
            u.email,
            u.created_at,
            COUNT(DISTINCT gm.group_id) as group_count,
            GROUP_CONCAT(DISTINCT g.group_name SEPARATOR ', ') as groups
        FROM users u
        LEFT JOIN group_members gm ON u.user_id = gm.user_id
        LEFT JOIN `groups` g ON gm.group_id = g.group_id
    ";
    
    $params = [];
    $where_conditions = [];

    // Add search condition if provided
    if ($search) {
        $where_conditions[] = "u.name LIKE ?";
        $params[] = "%$search%";
    }

    // Add group filter if provided
    if ($group_id) {
        $where_conditions[] = "EXISTS (
            SELECT 1 FROM group_members gm2 
            WHERE gm2.user_id = u.user_id 
            AND gm2.group_id = ?
        )";
        $params[] = $group_id;
    }

    // Add where clause if conditions exist
    if (!empty($where_conditions)) {
        $query .= " WHERE " . implode(" AND ", $where_conditions);
    }

    $query .= " GROUP BY u.user_id ORDER BY u.name";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $members = $stmt->fetchAll();

} catch(PDOException $e) {
    error_log("Error fetching members: " . $e->getMessage());
    $members = [];
}

// Get total number of members for statistics
try {
    $totalMembersStmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as total FROM users");
    $totalMembers = $totalMembersStmt->fetch()['total'];
    
    $totalGroupsStmt = $pdo->query("SELECT COUNT(DISTINCT group_id) as total FROM `groups`");
    $totalGroups = $totalGroupsStmt->fetch()['total'];
} catch(PDOException $e) {
    error_log("Error fetching stats: " . $e->getMessage());
    $totalMembers = 0;
    $totalGroups = 0;
}
?>

<!-- Custom CSS for enhanced styling -->
<style>
    .member-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .member-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #4f46e5;
        background-color: #f9fafb;
    }
    
    .avatar-circle {
        transition: all 0.3s ease;
    }
    
    .member-card:hover .avatar-circle {
        transform: scale(1.05);
        background-color: #818cf8;
        color: white;
    }
    
    .group-badge {
        transition: all 0.2s ease;
    }
    
    .member-card:hover .group-badge {
        background-color: #059669;
        color: white;
    }
    
    .search-input:focus {
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }
    
    .filter-btn {
        transition: all 0.2s ease;
    }
    
    .filter-btn:hover {
        transform: translateY(-1px);
    }
    
    .group-tag {
        transition: all 0.2s ease;
        display: inline-block;
        margin: 0.1rem 0.2rem;
        padding: 0.1rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        background-color: #e5e7eb;
    }
    
    .group-tag:hover {
        background-color: #4f46e5;
        color: white;
    }
    
    .stats-card {
        transition: all 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .pagination-btn {
        transition: all 0.2s ease;
    }
    
    .pagination-btn:hover:not(.disabled) {
        transform: translateY(-1px);
        background-color: #4f46e5;
        color: white;
    }
    
    .custom-select {
        /* background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); */
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
        transition: all 0.2s ease;
    }
    
    .custom-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }
</style>

<div class="py-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <!-- Header with breadcrumbs -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4 md:mb-0">
                <a href="dashboard.php" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                <span><i class="fas fa-chevron-right text-xs"></i></span>
                <span class="font-medium text-gray-900">Member Directory</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-users text-indigo-600 mr-3"></i>
                Member Directory
            </h1>
        </div>

        <!-- Stats Cards -->
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="bg-white overflow-hidden shadow rounded-lg stats-card">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                            <i class="fas fa-user-friends text-indigo-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Members</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo number_format($totalMembers); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg stats-card">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                            <i class="fas fa-layer-group text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Groups</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo number_format($totalGroups); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg stats-card">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <i class="fas fa-search text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Search Results</dt>
                                <dd class="text-3xl font-semibold text-gray-900"><?php echo count($members); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Filters -->
        <div class="mt-8 bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-filter mr-2 text-indigo-500"></i>
                    Filter Members
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Use the filters below to find specific members.
                </p>
            </div>
            
            <div class="px-4 py-5 sm:p-6 bg-gray-50">
                <form action="members.php" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
                    <div class="w-full md:w-2/5">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search by name</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" id="search"
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   class="search-input focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-12 py-3 sm:text-sm border-gray-300 rounded-lg"
                                   placeholder="Enter member name...">
                            <?php if ($search): ?>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <a href="<?php echo 'members.php' . ($group_id ? "?group_id=$group_id" : ''); ?>" class="text-gray-400 hover:text-gray-500">
                                        <i class="fas fa-times-circle"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="w-full md:w-1/3">
                        <label for="group_id" class="block text-sm font-medium text-gray-700 mb-1">Filter by group</label>
                        <select name="group_id" id="group_id"
                                class="custom-select mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg">
                            <option value="">All Groups</option>
                            <?php foreach ($user_groups as $group): ?>
                                <option value="<?php echo $group['group_id']; ?>"
                                        <?php echo $group_id == $group['group_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($group['group_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit"
                                class="filter-btn inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-search mr-2"></i>
                            Apply Filters
                        </button>

                        <?php if ($search || $group_id): ?>
                            <a href="members.php"
                               class="filter-btn inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-undo mr-2"></i>
                                Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Enhanced Members List -->
        <div class="mt-8">
            <?php if (empty($members)): ?>
                <div class="bg-white shadow rounded-lg text-center py-16">
                    <div class="inline-block p-6 rounded-full bg-gray-100 text-indigo-500 mb-4">
                        <i class="fas fa-users text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900">No members found</h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                        We couldn't find any members matching your current filters. Try adjusting your search criteria or clear filters to see all members.
                    </p>
                    <div class="mt-6">
                        <a href="members.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            View All Members
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white shadow-lg overflow-hidden rounded-lg">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Member Results
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Showing <?php echo count($members); ?> members
                                <?php if ($search): ?>
                                    matching "<?php echo htmlspecialchars($search); ?>"
                                <?php endif; ?>
                                <?php if ($group_id): ?>
                                    <?php 
                                        $group_name = "";
                                        foreach ($user_groups as $group) {
                                            if ($group['group_id'] == $group_id) {
                                                $group_name = $group['group_name'];
                                                break;
                                            }
                                        }
                                    ?>
                                    in group "<?php echo htmlspecialchars($group_name); ?>"
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                <i class="fas fa-users mr-1"></i> <?php echo count($members); ?> results
                            </div>
                        </div>
                    </div>
                    
                    <ul role="list" class="divide-y divide-gray-200">
                        <?php foreach ($members as $member): ?>
                            <li class="member-card">
                                <div class="px-6 py-5 flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-circle h-14 w-14 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-xl font-bold text-indigo-700">
                                                <?php echo strtoupper(substr($member['name'], 0, 2)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-5 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900 hover:text-indigo-600">
                                                    <?php echo htmlspecialchars($member['name']); ?>
                                                </h3>
                                                <div class="mt-1 flex items-center text-sm text-gray-500">
                                                    <i class="far fa-envelope mr-1.5 text-gray-400"></i>
                                                    <span><?php echo htmlspecialchars($member['email']); ?></span>
                                                </div>
                                                <div class="mt-1 flex items-center text-sm text-gray-500">
                                                    <i class="far fa-calendar-alt mr-1.5 text-gray-400"></i>
                                                    <span>Member since <?php echo formatDate($member['created_at']); ?></span>
                                                </div>
                                            </div>
                                            <div class="ml-4 flex items-center space-x-2">
                                                <span class="group-badge px-3 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-layer-group mr-1"></i>
                                                    <?php echo $member['group_count']; ?> groups
                                                </span>
                                                <div class="flex space-x-1">
                                                    <button type="button" class="inline-flex items-center p-1.5 border border-transparent rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" title="Send message">
                                                        <i class="fas fa-envelope text-xs"></i>
                                                    </button>
                                                    <button type="button" class="inline-flex items-center p-1.5 border border-transparent rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" title="View profile">
                                                        <i class="fas fa-user text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if ($member['groups']): ?>
                                            <div class="mt-2">
                                                <div class="text-xs text-gray-500 flex flex-wrap items-center">
                                                    <span class="font-medium mr-2">Groups:</span>
                                                    <?php 
                                                        $groupNames = explode(', ', $member['groups']);
                                                        foreach ($groupNames as $groupName):
                                                    ?>
                                                        <span class="group-tag">
                                                            <?php echo htmlspecialchars($groupName); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <!-- Simple pagination for demo purposes -->
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">1</span> to <span class="font-medium"><?php echo count($members); ?></span> of <span class="font-medium"><?php echo count($members); ?></span> results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <a href="#" class="pagination-btn disabled relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                        <span class="sr-only">Previous</span>
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                    <a href="#" class="pagination-btn relative inline-flex items-center px-4 py-2 border border-gray-300 bg-indigo-50 text-sm font-medium text-indigo-600 hover:bg-indigo-100">
                                        1
                                    </a>
                                    <a href="#" class="pagination-btn disabled relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                        <span class="sr-only">Next</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions Card -->
        <div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-bolt text-yellow-500 mr-2"></i> Quick Actions
                </h3>
                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <a href="#" class="filter-btn relative block p-6 border border-gray-300 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 text-center">
                        <i class="fas fa-user-plus text-3xl text-indigo-600 mb-3"></i>
                        <span class="block text-sm font-medium text-gray-900">Add New Member</span>
                    </a>
                    <a href="#" class="filter-btn relative block p-6 border border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 text-center">
                        <i class="fas fa-layer-group text-3xl text-purple-600 mb-3"></i>
                        <span class="block text-sm font-medium text-gray-900">Create Group</span>
                    </a>
                    <a href="#" class="filter-btn relative block p-6 border border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 text-center">
                        <i class="fas fa-file-export text-3xl text-green-600 mb-3"></i>
                        <span class="block text-sm font-medium text-gray-900">Export Members</span>
                    </a>
                    <a href="#" class="filter-btn relative block p-6 border border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 text-center">
                        <i class="fas fa-envelope text-3xl text-blue-600 mb-3"></i>
                        <span class="block text-sm font-medium text-gray-900">Email All Members</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>