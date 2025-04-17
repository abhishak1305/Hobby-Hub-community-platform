<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];
$success = '';

// At the top of reset_password.php, after checking for reset_attempt
if (!isset($_SESSION['reset_otp_verified'])) {
  redirect('forgot_password.php');
}
// Get email from session
$email = $_SESSION['reset_email'] ?? '';
if (empty($email)) {
    redirect('forgot_password.php');
}

// Process password reset form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate passwords
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Update password
    if (empty($errors)) {
        try {
            // Hash new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Update user's password
            $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $updateStmt->execute([$hashed_password, $email]);
            
            // Clear reset session
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_attempt']);
            
            $success = "Your password has been successfully reset. You can now log in with your new password.";
            
        } catch(PDOException $e) {
            error_log("Password update error: " . $e->getMessage());
            $errors[] = "An error occurred while resetting your password.";
        }
    }
}
?>

<!-- Background with gradient overlay -->
<div class="min-h-screen flex items-center justify-center bg-gray-50 relative overflow-hidden">
  <!-- Decorative background elements -->
  <div class="absolute inset-0 overflow-hidden opacity-20">
    <div class="absolute rounded-full bg-indigo-200 w-64 h-64 -top-32 -left-32 animate-float"></div>
    <div class="absolute rounded-full bg-purple-200 w-96 h-96 top-1/4 -right-48 animate-float-delay"></div>
    <div class="absolute rounded-full bg-blue-200 w-80 h-80 bottom-32 left-1/4 animate-float-delay-2"></div>
  </div>
  
  <!-- Background image with overlay -->
  <div class="absolute inset-0 z-0">
    <img src="https://thumbs.dreamstime.com/b/set-people-enjoying-their-favorite-hobbies-enjoy-flat-vector-cartoon-modern-illustration-knitting-drawing-potter-cooking-play-184010278.jpg" 
         alt="People enjoying hobbies together" 
         class="w-full h-full object-cover opacity-10">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/10 to-purple-900/10"></div>
  </div>

  <div class="relative z-10 w-full max-w-md px-4 py-12">
    <!-- Logo/Header -->
    <div class="text-center mb-10">
      <div class="flex justify-center mb-4">
        <div class="w-16 h-16 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center shadow-lg transform rotate-0 hover:rotate-12 transition-transform duration-500">
          <i class="fas fa-lock text-white text-2xl"></i>
        </div>
      </div>
      <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
        Reset Your Password
      </h2>
      <p class="mt-2 text-sm text-gray-600">
        Create a new password for your account
      </p>
    </div>

    <!-- Card Container -->
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden transform transition-all duration-300 hover:shadow-2xl">
      <!-- Gradient top border -->
      <div class="h-2 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
      
      <div class="px-8 py-8 sm:px-10 sm:py-10">
        <?php if (!empty($errors)): ?>
          <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 shadow-sm">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                  There were <?php echo count($errors); ?> errors with your submission
                </h3>
                <div class="mt-2 text-sm text-red-700">
                  <ul class="list-disc pl-5 space-y-1">
                    <?php foreach ($errors as $error): ?>
                      <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
          <div class="mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500 shadow-sm">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-green-800">
                  <?php echo htmlspecialchars($success); ?>
                </p>
                <div class="mt-4">
                  <a href="login.php" class="text-sm font-medium text-green-600 hover:text-green-500 transition-colors duration-300">
                    Go to login page <span aria-hidden="true">→</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php else: ?>
          <form class="space-y-6" action="reset_password.php" method="POST">
            <!-- Password Field -->
            <div class="space-y-1">
              <label for="password" class="block text-sm font-medium text-gray-700">
                New Password
              </label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input id="password" name="password" type="password" required
                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
              </div>
              <p class="mt-1 text-xs text-gray-500">
                Must be at least 8 characters with one uppercase letter, one lowercase letter, and one number
              </p>
            </div>

            <!-- Confirm Password Field -->
            <div class="space-y-1">
              <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                Confirm Password
              </label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input id="confirm_password" name="confirm_password" type="password" required
                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
              </div>
            </div>

            <!-- Submit Button -->
            <div>
              <button type="submit" 
                      class="group w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5">
                <span class="mr-2">Reset Password</span>
                <svg class="h-4 w-4 group-hover:translate-x-1 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </button>
            </div>
          </form>
        <?php endif; ?>

        <!-- Back to Login Link -->
        <div class="mt-6 text-center">
          <a href="login.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-300">
            Back to login
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  @keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
  }
  .animate-float {
    animation: float 6s ease-in-out infinite;
  }
  .animate-float-delay {
    animation: float 6s ease-in-out infinite 2s;
  }
  .animate-float-delay-2 {
    animation: float 6s ease-in-out infinite 4s;
  }
</style>

<?php require_once '../includes/footer.php'; ?>
