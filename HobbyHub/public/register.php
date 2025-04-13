<?php
require_once '../includes/header.php';

$errors = [];
$name = '';
$email = '';
$otp_sent = false;
$otp_verified = false;

// Function to generate OTP
function generateOTP($length = 6) {
    $characters = '0123456789';
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $otp;
}

// Function to send OTP email
function sendOTPEmail($email, $name, $otp) {
    $subject = "HobbyHub - Email Verification Code";
    $message = "Hello " . $name . ",\n\n";
    $message .= "Thank you for registering with HobbyHub. Your verification code is: " . $otp . "\n\n";
    $message .= "This code will expire in 15 minutes.\n\n";
    $message .= "If you didn't request this code, please ignore this email.\n\n";
    $message .= "Regards,\nHobbyHub Team";
    $headers = "From: noreply@hobbyhub.com";
    
    return mail($email, $subject, $message, $headers);
}

// Initial registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === 'register') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($name)) {
        $errors[] = "Name is required";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!isValidEmail($email)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if email already exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Email already registered";
            }
        } catch(PDOException $e) {
            $errors[] = "An error occurred. Please try again later.";
            error_log("Registration error: " . $e->getMessage());
        }
    }

    // If no errors, generate and send OTP
    if (empty($errors)) {
        try {
            // Generate OTP
            $otp = generateOTP();
            $otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Store user data and OTP in session for verification
            $_SESSION['temp_registration'] = [
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'otp' => $otp,
                'otp_expiry' => $otp_expiry
            ];
            
            // Send OTP via email
            if (sendOTPEmail($email, $name, $otp)) {
                $otp_sent = true;
            } else {
                $errors[] = "Failed to send verification email. Please try again.";
                error_log("Failed to send OTP email to: " . $email);
            }
        } catch(Exception $e) {
            $errors[] = "An error occurred. Please try again later.";
            error_log("OTP generation error: " . $e->getMessage());
        }
    }
}

// OTP verification form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === 'verify_otp') {
    $submitted_otp = sanitizeInput($_POST['otp'] ?? '');
    
    if (empty($submitted_otp)) {
        $errors[] = "Verification code is required";
    } elseif (!isset($_SESSION['temp_registration'])) {
        $errors[] = "Session expired. Please try registering again.";
    } else {
        $temp_data = $_SESSION['temp_registration'];
        $name = $temp_data['name'];
        $email = $temp_data['email'];
        $otp_sent = true;
        
        // Check if OTP has expired
        if (strtotime($temp_data['otp_expiry']) < time()) {
            $errors[] = "Verification code has expired. Please request a new one.";
        }
        // Verify OTP
        elseif ($submitted_otp !== $temp_data['otp']) {
            $errors[] = "Invalid verification code";
        } else {
            // OTP verified, create user account
            try {
                // First verify the table structure
                $stmt = $pdo->query("DESCRIBE users");
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (!in_array('is_verified', $columns)) {
                    throw new PDOException("Database column 'is_verified' is missing");
                }
                
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_verified) VALUES (?, ?, ?, 1)");
                $stmt->execute([$temp_data['name'], $temp_data['email'], $temp_data['password']]);

                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $temp_data['name'];
                
                // Clear temporary registration data
                unset($_SESSION['temp_registration']);
                
                redirect('dashboard.php', 'Registration successful! Email verified. Welcome to HobbyHub.', 'success');
            } catch(PDOException $e) {
                $errors[] = "An error occurred. Please try again later.";
                error_log("Registration error after OTP verification: " . $e->getMessage());
                
                // Additional debug logging
                error_log("Current columns in users table: " . print_r($columns, true));
            }
        }
    }
}

// Rest of your file remains the same...

// Resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === 'resend_otp') {
    if (!isset($_SESSION['temp_registration'])) {
        $errors[] = "Session expired. Please try registering again.";
    } else {
        $temp_data = $_SESSION['temp_registration'];
        $name = $temp_data['name'];
        $email = $temp_data['email'];
        
        // Generate new OTP
        $otp = generateOTP();
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Update OTP in session
        $_SESSION['temp_registration']['otp'] = $otp;
        $_SESSION['temp_registration']['otp_expiry'] = $otp_expiry;
        
        // Send new OTP via email
        if (sendOTPEmail($email, $name, $otp)) {
            $otp_sent = true;
            $success_message = "Verification code has been resent to your email.";
        } else {
            $errors[] = "Failed to send verification email. Please try again.";
            error_log("Failed to resend OTP email to: " . $email);
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
    <img src="https://png.pngtree.com/png-clipart/20230824/original/pngtree-kids-hobbies-child-hobby-kid-picture-image_8441717.png" 
         alt="People enjoying hobbies together" 
         class="w-full h-full object-cover opacity-10">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/10 to-purple-900/10"></div>
  </div>

  <div class="relative z-10 w-full max-w-md px-4 py-12">
    <!-- Logo/Header -->
    <div class="text-center mb-10">
      <div class="flex justify-center mb-4">
        <div class="w-16 h-16 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center shadow-lg transform rotate-0 hover:rotate-12 transition-transform duration-500">
          <i class="fas fa-user-plus text-white text-2xl"></i>
        </div>
      </div>
      <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
        Join <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">HobbyHub</span>
      </h2>
      <p class="mt-2 text-sm text-gray-600">
        Connect with fellow enthusiasts and grow your passion
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

        <?php if (isset($success_message)): ?>
          <div class="mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500 shadow-sm">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                  <?php echo $success_message; ?>
                </p>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!$otp_sent): ?>
        <!-- Step 1: Registration Form -->
        <form class="space-y-5" action="register.php" method="POST">
          <input type="hidden" name="step" value="register">
          
          <!-- Name Field -->
          <div class="space-y-1">
            <label for="name" class="block text-sm font-medium text-gray-700">
              Full name
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
              </div>
              <input id="name" name="name" type="text" autocomplete="name" required
                     value="<?php echo htmlspecialchars($name); ?>"
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
            </div>
          </div>

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
              Password <span class="text-xs text-gray-500">(min 8 characters)</span>
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
              </div>
              <input id="password" name="password" type="password" autocomplete="new-password" required
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
            </div>
          </div>

          <!-- Confirm Password Field -->
          <div class="space-y-1">
            <label for="confirm_password" class="block text-sm font-medium text-gray-700">
              Confirm Password
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd" />
                </svg>
              </div>
              <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-2">
            <button type="submit" 
                    class="group w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5">
              <span class="mr-2">Continue</span>
              <svg class="h-4 w-4 group-hover:translate-x-1 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </button>
          </div>
        </form>
        <?php else: ?>
        <!-- Step 2: OTP Verification Form -->
        <div class="text-center mb-6">
          <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900">Verify your email</h3>
          <p class="mt-1 text-sm text-gray-600">
            We've sent a verification code to<br>
            <span class="font-medium text-indigo-600"><?php echo htmlspecialchars($email); ?></span>
          </p>
        </div>
        
        <form class="space-y-5" action="register.php" method="POST">
          <input type="hidden" name="step" value="verify_otp">
          
          <!-- OTP Field -->
          <div class="space-y-1">
            <label for="otp" class="block text-sm font-medium text-gray-700">
              Enter verification code
            </label>
            <div class="mt-1">
              <input id="otp" name="otp" type="text" autocomplete="one-time-code" required
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full text-center py-3 px-4 border-gray-300 rounded-md text-lg tracking-widest font-medium transition duration-300 ease-in-out hover:border-indigo-300"
                     maxlength="6" inputmode="numeric" placeholder="000000">
            </div>
            <p class="mt-1 text-xs text-gray-500">The code will expire in 15 minutes</p>
          </div>

          <!-- Submit Button -->
          <div class="pt-2">
            <button type="submit" 
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5">
              <span>Verify and Complete Registration</span>
            </button>
          </div>
        </form>
        
        <!-- Resend OTP Form -->
        <div class="mt-4 text-center">
          <p class="text-sm text-gray-600">Didn't receive the code?</p>
          <form action="register.php" method="POST" class="mt-1">
            <input type="hidden" name="step" value="resend_otp">
            <button type="submit" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition duration-300 ease-in-out">
              Resend code
            </button>
          </form>
        </div>
        <?php endif; ?>

        <!-- Divider -->
        <div class="mt-8">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Already have an account?
              </span>
            </div>
          </div>

          <!-- Login Button -->
          <div class="mt-6">
            <a href="login.php" 
               class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5">
              <span class="mr-2">Sign in instead</span>
              <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
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