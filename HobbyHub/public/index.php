<?php
require_once '../includes/header.php';
?>

<!-- Animated background gradient -->
<div class="relative overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-blue-50">
  <!-- Floating circles decoration -->
  <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-30">
    <div class="absolute rounded-full bg-indigo-200 w-64 h-64 -top-32 -left-32 animate-float"></div>
    <div class="absolute rounded-full bg-blue-200 w-96 h-96 top-1/4 -right-48 animate-float-delay"></div>
    <div class="absolute rounded-full bg-purple-200 w-80 h-80 bottom-32 left-1/4 animate-float-delay-2"></div>
  </div>

  <!-- Hero section -->
  <div class="relative pt-16 pb-24 sm:pb-32">
    <div class="mt-10 mx-auto max-w-7xl px-4 sm:mt-16 sm:px-6 lg:mt-20 lg:px-8">
      <div class="text-center backdrop-blur-sm bg-white/50 p-8 rounded-xl shadow-lg">
        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
          <span class="block transform transition duration-500 hover:scale-105 inline-block">Connect with fellow</span>
          <span class="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 transform transition duration-500 hover:scale-105 hover:from-indigo-700 hover:to-purple-700 inline-block">hobby enthusiasts</span>
        </h1>
        <p class="mt-5 max-w-md mx-auto text-lg text-gray-600 sm:text-xl md:mt-7 md:max-w-3xl">
          Join our vibrant community of hobby groups. Share your passion, organize events, and meet people who share your interests.
        </p>
        <div class="mt-8 max-w-md mx-auto sm:flex sm:justify-center md:mt-10 space-y-4 sm:space-y-0 sm:space-x-4">
          <?php if (!isLoggedIn()): ?>
          <div class="rounded-lg shadow-lg transform transition hover:-translate-y-1 hover:shadow-xl">
            <a href="register.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 md:py-4 md:text-lg md:px-10 transition-all duration-300">
              Get started
              <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </a>
          </div>
          <div class="rounded-lg shadow-lg transform transition hover:-translate-y-1 hover:shadow-xl">
            <a href="login.php" class="w-full flex items-center justify-center px-8 py-3 border border-gray-300 text-base font-medium rounded-lg text-indigo-700 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10 transition-all duration-300">
              Sign in
              <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
            </a>
          </div>
          <?php else: ?>
          <div class="rounded-lg shadow-lg transform transition hover:-translate-y-1 hover:shadow-xl">
            <a href="dashboard.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 md:py-4 md:text-lg md:px-10 transition-all duration-300">
              Go to Dashboard
              <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
              </svg>
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Feature section with cards -->
<div class="py-16 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="lg:text-center">
      <h2 class="text-base text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 font-semibold tracking-wide uppercase">Features</h2>
      <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
        Everything you need to manage your hobby group
      </p>
      <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
        Powerful tools designed to help you connect, organize, and grow your community.
      </p>
    </div>

    <div class="mt-16">
      <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
        <!-- Feature 1 -->
        <div class="group relative bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-gray-100 hover:border-indigo-200">
          <div class="absolute -top-6 left-6 flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg transform group-hover:-translate-y-1 transition-transform duration-300">
            <i class="fas fa-users text-xl"></i>
          </div>
          <h3 class="mt-8 text-lg font-medium text-gray-900 group-hover:text-indigo-600 transition-colors duration-300">Group Management</h3>
          <p class="mt-2 text-base text-gray-500">
            Create and manage hobby groups, invite members, and organize your community effectively.
          </p>
          <div class="mt-4">
            <a href="#" class="text-indigo-600 font-medium group-hover:text-indigo-800 transition-colors duration-300 flex items-center">
              Learn more
              <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>

        <!-- Feature 2 -->
        <div class="group relative bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-gray-100 hover:border-indigo-200">
          <div class="absolute -top-6 left-6 flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg transform group-hover:-translate-y-1 transition-transform duration-300">
            <i class="fas fa-calendar-alt text-xl"></i>
          </div>
          <h3 class="mt-8 text-lg font-medium text-gray-900 group-hover:text-indigo-600 transition-colors duration-300">Event Planning</h3>
          <p class="mt-2 text-base text-gray-500">
            Schedule meetups, workshops, and events. Keep track of RSVPs and send reminders.
          </p>
          <div class="mt-4">
            <a href="#" class="text-indigo-600 font-medium group-hover:text-indigo-800 transition-colors duration-300 flex items-center">
              Learn more
              <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>

        <!-- Feature 3 -->
        <div class="group relative bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-gray-100 hover:border-indigo-200">
          <div class="absolute -top-6 left-6 flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg transform group-hover:-translate-y-1 transition-transform duration-300">
            <i class="fas fa-comments text-xl"></i>
          </div>
          <h3 class="mt-8 text-lg font-medium text-gray-900 group-hover:text-indigo-600 transition-colors duration-300">Discussion Forums</h3>
          <p class="mt-2 text-base text-gray-500">
            Engage in discussions, share ideas, and connect with other members through topic-based forums.
          </p>
          <div class="mt-4">
            <a href="#" class="text-indigo-600 font-medium group-hover:text-indigo-800 transition-colors duration-300 flex items-center">
              Learn more
              <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>

        <!-- Feature 4 -->
        <div class="group relative bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-gray-100 hover:border-indigo-200">
          <div class="absolute -top-6 left-6 flex items-center justify-center h-12 w-12 rounded-md bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg transform group-hover:-translate-y-1 transition-transform duration-300">
            <i class="fas fa-user-friends text-xl"></i>
          </div>
          <h3 class="mt-8 text-lg font-medium text-gray-900 group-hover:text-indigo-600 transition-colors duration-300">Member Directory</h3>
          <p class="mt-2 text-base text-gray-500">
            Browse and connect with members who share your interests. Build meaningful relationships.
          </p>
          <div class="mt-4">
            <a href="#" class="text-indigo-600 font-medium group-hover:text-indigo-800 transition-colors duration-300 flex items-center">
              Learn more
              <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Testimonials section -->
<div class="bg-gradient-to-br from-indigo-50 to-blue-50 py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="lg:text-center mb-12">
      <h2 class="text-base text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 font-semibold tracking-wide uppercase">Testimonials</h2>
      <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
        What our members say
      </p>
    </div>
    
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      <!-- Testimonial 1 -->
      <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 rounded-full overflow-hidden bg-indigo-100 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <div class="ml-4">
            <h4 class="text-lg font-medium text-gray-900">Sarah Johnson</h4>
            <p class="text-indigo-600">Photography Group</p>
          </div>
        </div>
        <p class="text-gray-600 italic">
          "This platform transformed how our photography club operates. We've doubled our membership and organized more events in 3 months than we did all last year!"
        </p>
        <div class="mt-4 flex text-yellow-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
        </div>
      </div>
      
      <!-- Testimonial 2 -->
      <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 rounded-full overflow-hidden bg-indigo-100 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <div class="ml-4">
            <h4 class="text-lg font-medium text-gray-900">Michael Chen</h4>
            <p class="text-indigo-600">Chess Club</p>
          </div>
        </div>
        <p class="text-gray-600 italic">
          "The event management tools saved us so much time. Now we can focus on playing chess instead of organizing logistics!"
        </p>
        <div class="mt-4 flex text-yellow-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
          </svg>
        </div>
      </div>
      
      <!-- Testimonial 3 -->
      <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-1">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 rounded-full overflow-hidden bg-indigo-100 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <div class="ml-4">
            <h4 class="text-lg font-medium text-gray-900">David & Emma Wilson</h4>
            <p class="text-indigo-600">Hiking Enthusiasts</p>
          </div>
        </div>
        <p class="text-gray-600 italic">
          "We've met our closest friends through this platform. The member directory helped us find people who share our passion for hiking."
        </p>
        <div class="mt-4 flex text-yellow-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
          </svg>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Stats section -->
<div class="bg-white py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="lg:text-center mb-12">
      <h2 class="text-base text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 font-semibold tracking-wide uppercase">Our Community</h2>
      <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
        Join thousands of hobby enthusiasts
      </p>
    </div>
    
    <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
      <!-- Stat 1 -->
      <div class="text-center p-6 rounded-xl bg-gradient-to-br from-indigo-50 to-blue-50 transform transition hover:scale-105 hover:shadow-md">
        <div class="text-4xl font-extrabold text-indigo-600">250+</div>
        <div class="mt-2 text-sm font-medium text-gray-500">ACTIVE GROUPS</div>
      </div>
      
      <!-- Stat 2 -->
      <div class="text-center p-6 rounded-xl bg-gradient-to-br from-indigo-50 to-blue-50 transform transition hover:scale-105 hover:shadow-md">
        <div class="text-4xl font-extrabold text-indigo-600">10K+</div>
        <div class="mt-2 text-sm font-medium text-gray-500">MEMBERS</div>
      </div>
      
      <!-- Stat 3 -->
      <div class="text-center p-6 rounded-xl bg-gradient-to-br from-indigo-50 to-blue-50 transform transition hover:scale-105 hover:shadow-md">
        <div class="text-4xl font-extrabold text-indigo-600">500+</div>
        <div class="mt-2 text-sm font-medium text-gray-500">EVENTS MONTHLY</div>
      </div>
      
      <!-- Stat 4 -->
      <div class="text-center p-6 rounded-xl bg-gradient-to-br from-indigo-50 to-blue-50 transform transition hover:scale-105 hover:shadow-md">
        <div class="text-4xl font-extrabold text-indigo-600">50+</div>
        <div class="mt-2 text-sm font-medium text-gray-500">HOBBY CATEGORIES</div>
      </div>
    </div>
  </div>
</div>

<!-- CTA Section with background image -->
<div class="relative bg-indigo-900">
  <!-- Background image with overlay -->
  <div class="absolute inset-0">
    <img class="w-full h-full object-cover opacity-20" src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?ixlib=rb-1.2.1&auto=format&fit=crop&w=1900&q=80" alt="People enjoying hobbies together">
    <div class="absolute inset-0 bg-indigo-900 mix-blend-multiply" aria-hidden="true"></div>
  </div>
  
  <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
    <div class="text-center">
      <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
        <span class="block">Ready to find your tribe?</span>
        <span class="block text-indigo-200">Start your hobby journey today.</span>
      </h2>
      <p class="mt-6 max-w-2xl mx-auto text-xl text-indigo-100">
        Whether you're into photography, chess, hiking, or something more unique, we've got a community waiting for you.
      </p>
      <div class="mt-10 flex justify-center space-x-4">
        <?php if (!isLoggedIn()): ?>
        <div class="rounded-lg shadow-lg transform transition hover:-translate-y-1 hover:shadow-xl">
          <a href="register.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-indigo-700 bg-white hover:bg-indigo-50 md:py-4 md:text-lg md:px-10 transition-all duration-300">
            Get started
            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </a>
        </div>
        <div class="rounded-lg shadow-lg transform transition hover:-translate-y-1 hover:shadow-xl">
          <a href="login.php" class="w-full flex items-center justify-center px-8 py-3 border border-white text-base font-medium rounded-lg text-white bg-transparent hover:bg-white hover:bg-opacity-10 md:py-4 md:text-lg md:px-10 transition-all duration-300">
            Sign in
            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
          </a>
        </div>
        <?php else: ?>
        <div class="rounded-lg shadow-lg transform transition hover:-translate-y-1 hover:shadow-xl">
          <a href="dashboard.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-indigo-700 bg-white hover:bg-indigo-50 md:py-4 md:text-lg md:px-10 transition-all duration-300">
            Go to Dashboard
            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php
require_once '../includes/footer.php';
?>

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