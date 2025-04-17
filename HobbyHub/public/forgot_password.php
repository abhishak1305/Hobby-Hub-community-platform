<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];
$success = '';
$email = '';
$showOtpField = false;

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
function sendOTPEmail($email, $otp) {
    $subject = "HobbyHub - Password Reset OTP";
    $message = "Hello,\n\n";
    $message .= "Your OTP for password reset is: $otp\n\n";
    $message .= "This OTP is valid for 10 minutes. Do not share it with anyone.\n\n";
    $message .= "If you didn't request this, please ignore this email.\n\n";
    $message .= "Regards,\nHobbyHub Team";
    $headers = "From: noreply@hobbyhub.com";
    
    return mail($email, $subject, $message, $headers);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle OTP verification
    if (isset($_POST['verify_otp'])) {
        $userOtp = sanitizeInput($_POST['otp'] ?? '');
        $storedOtp = $_SESSION['reset_otp'] ?? '';
        $otpExpiry = $_SESSION['otp_expiry'] ?? 0;
        
        if (empty($userOtp)) {
            $errors[] = "OTP is required";
        } elseif ($userOtp !== $storedOtp) {
            $errors[] = "Invalid OTP";
        } elseif (time() > $otpExpiry) {
            $errors[] = "OTP has expired. Please request a new one.";
        }
        
        if (empty($errors)) {
            // OTP verified, redirect to reset password
            $_SESSION['reset_attempt'] = true;
            redirect('reset_password.php');
        }
    }
    // Handle email submission and OTP sending
    elseif (isset($_POST['send_otp'])) {
        $email = sanitizeInput($_POST['email'] ?? '');
        
        // Validate email
        if (empty($email)) {
            $errors[] = "Email address is required";
        } elseif (!isValidEmail($email)) {
            $errors[] = "Invalid email format";
        }
        
        if (empty($errors)) {
            try {
                // Check if email exists
                $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Generate and store OTP
                    $otp = generateOTP();
                    $_SESSION['reset_otp'] = $otp;
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['otp_expiry'] = time() + 600; // 10 minutes expiry
                    
                    // Send OTP email
                    if (sendOTPEmail($email, $otp)) {
                        $showOtpField = true;
                        $success = "OTP has been sent to your email. Please check your inbox.";
                    } else {
                        $errors[] = "Failed to send OTP. Please try again.";
                    }
                } else {
                    // Show success message even if email doesn't exist (security measure)
                    $success = "If your email exists in our system, you will receive an OTP.";
                }
            } catch(PDOException $e) {
                error_log("Password reset error: " . $e->getMessage());
                $errors[] = "An error occurred. Please try again later.";
            }
        }
    }
}

$_SESSION['reset_otp_verified'] = true;
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
          <i class="fas fa-key text-white text-2xl"></i>
        </div>
      </div>
      <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
        Forgot Your Password?
      </h2>
      <p class="mt-2 text-sm text-gray-600">
        <?php echo $showOtpField ? 'Enter the OTP sent to your email' : 'Enter your email address to receive an OTP'; ?>
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
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($showOtpField): ?>
          <!-- OTP Verification Form -->
          <form class="space-y-6" action="forgot_password.php" method="POST">
            <!-- OTP Field -->
            <div class="space-y-1">
              <label for="otp" class="block text-sm font-medium text-gray-700">
                Enter OTP
              </label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input id="otp" name="otp" type="text" required maxlength="6"
                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
              </div>
              <p class="mt-1 text-xs text-gray-500">
                Check your email for the 6-digit OTP
              </p>
            </div>

            <!-- Verify Button -->
            <div>
              <button type="submit" name="verify_otp"
                      class="group w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5">
                <span class="mr-2">Verify OTP</span>
                <svg class="h-4 w-4 group-hover:translate-x-1 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
              </button>
            </div>
          </form>
        <?php else: ?>
          <!-- Email Form -->
          <form class="space-y-6" action="forgot_password.php" method="POST">
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

            <!-- Submit Button -->
            <div>
              <button type="submit" name="send_otp"
                      class="group w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5">
                <span class="mr-2">Send OTP</span>
                <svg class="h-4 w-4 group-hover:translate-x-1 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
              </button>
            </div>
          </form>
        <?php endif; ?>

        <!-- Divider -->
        <div class="mt-8">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Or return to
              </span>
            </div>
          </div>

          <!-- Login Button -->
          <div class="mt-6">
            <a href="login.php" 
               class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5">
              <span class="mr-2">Sign in</span>
              <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14" />
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