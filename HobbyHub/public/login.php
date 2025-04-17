<?php
require_once '../includes/header.php';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        $errors[] = "Both email and password are required";
    } elseif (!isValidEmail($email)) {
        $errors[] = "Invalid email format";
    }

    // Check credentials
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT user_id, name, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                redirect('dashboard.php', 'Welcome back!', 'success');
            } else {
                $errors[] = "Invalid email or password";
            }
        } catch(PDOException $e) {
            $errors[] = "An error occurred. Please try again later.";
            error_log("Login error: " . $e->getMessage());
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
          <i class="fas fa-users text-white text-2xl"></i>
        </div>
      </div>
      <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
        Welcome back to <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">HobbyHub</span>
      </h2>
      <p class="mt-2 text-sm text-gray-600">
        Connect with your community of fellow enthusiasts
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
                      <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <form class="space-y-6" action="login.php" method="POST">
          <!-- Email Field -->
          <div class="space-y-1">
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email address
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
              </div>
              <input id="email" name="email" type="email" autocomplete="email" required
                     value="<?php echo htmlspecialchars($email); ?>"
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
            </div>
          </div>

          <!-- Password Field -->
          <div class="space-y-1">
            <label for="password" class="block text-sm font-medium text-gray-700">
              Password
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
              </div>
              <input id="password" name="password" type="password" autocomplete="current-password" required
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
            </div>
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember_me" name="remember_me" type="checkbox"
                     class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition duration-300 ease-in-out hover:border-indigo-400">
              <label for="remember_me" class="ml-2 block text-sm text-gray-700 hover:text-gray-900 transition-colors duration-300">
                Remember me
              </label>
            </div>

            <div class="text-sm">
              <a href="forgot_password.php" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-300">
                Forgot your password?
              </a>
            </div>
          </div>

          <!-- Submit Button -->
          <div>
            <button type="submit" 
                    class="group w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5">
              <span class="mr-2">Sign in</span>
              <svg class="h-4 w-4 group-hover:translate-x-1 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
              </svg>
            </button>
          </div>
        </form>

        <!-- Divider -->
        <div class="mt-8">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Don't have an account?
              </span>
            </div>
          </div>

          <!-- Register Button -->
          <div class="mt-6">
            <a href="register.php" 
               class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5">
              <span class="mr-2">Create new account</span>
              <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
            </a>
          </div>
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

<?php
require_once '../includes/footer.php';
?>