<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - Hobby Hub</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <main class="container mx-auto px-4 py-8">
        <section class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">Discover Hobby Hub Features</h1>
            <p class="text-xl text-gray-600">Connect, Collaborate, and Celebrate Your Passions</p>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Why Hobby Hub?</h2>
            <p class="text-gray-700 leading-relaxed">
                Hobby Hub is a fully responsive, dynamic, and real-time platform designed to connect you with friends and strangers who share your hobbies and interests. Whether you're passionate about music, coding, gaming, photography, or any other pursuit, our features make it easy to find your community, engage in meaningful interactions, and stay updated on exciting opportunities. Explore the key features below that make Hobby Hub the ultimate space for hobbyists.
            </p>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-6">Core Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-2">Landing Page</h3>
                    <p class="text-gray-700">
                        A vibrant, user-friendly landing page welcomes you to Hobby Hub, showcasing the platform’s purpose and inviting you to join a community of passionate hobbyists.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-2">Secure Login</h3>
                    <p class="text-gray-700">
                        Access your account with a secure login system, ensuring your data is protected while you connect with others.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-2">Register with OTP Verification</h3>
                    <p class="text-gray-700">
                        Sign up easily with email-based OTP verification, providing a secure and seamless registration process.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-2">Personalized Dashboard</h3>
                    <p class="text-gray-700">
                        Your dashboard is your hub for activity updates, group notifications, upcoming events, and personalized recommendations based on your interests.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-2">Groups</h3>
                    <p class="text-gray-700">
                        Join or create groups dedicated to specific hobbies, from anime to fitness, to connect with like-minded individuals and share your passion.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-2">Real-Time Discussions</h3>
                    <p class="text-gray-700">
                        Engage in lively, real-time discussions with group members, sharing ideas, tips, and experiences instantly.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-2">Events</h3>
                    <p class="text-gray-700">
                        Discover and participate in local or virtual events, from workshops to meetups, tailored to your hobbies and interests.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-2">Members Directory</h3>
                    <p class="text-gray-700">
                        Explore a directory of members to find and connect with others who share your passions, fostering new friendships and collaborations.
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-2">Your Profile</h3>
                    <p class="text-gray-700">
                        Create a personalized profile that showcases your hobbies, interests, and personality, making it easy for others to connect with you.
                    </p>
                </div>
            </div>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Additional Features</h2>
            <ul class="list-disc list-inside text-gray-700">
                <li><strong>Real-Time Notifications:</strong> Stay updated with instant alerts for new group posts, event invites, or messages.</li>
                <li><strong>Search & Discovery:</strong> Easily find groups, events, or members based on keywords, locations, or interests.</li>
                <li><strong>Private Messaging:</strong> Connect one-on-one with other members through secure, private chats.</li>
                <li><strong>Content Sharing:</strong> Share photos, videos, or articles related to your hobbies within groups or on your profile.</li>
                <li><strong>Customizable Settings:</strong> Tailor your notification preferences, privacy settings, and profile visibility to suit your needs.</li>
            </ul>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Built for You</h2>
            <p class="text-gray-700 leading-relaxed">
                Hobby Hub is designed to be fully responsive, ensuring a seamless experience across desktops, tablets, and mobile devices. Our dynamic platform updates in real-time, so you never miss a moment of connection. Whether you’re looking to collaborate on a project, join a local meetup, or simply chat with others who share your interests, Hobby Hub has the tools to make it happen.
            </p>
        </section>

        <section class="text-center">
            <h2 class="text-2xl font-semibold mb-4">Ready to Explore?</h2>
            <p class="text-gray-700 mb-6">
                Join Hobby Hub today and unlock a world of connections, creativity, and community. Your passions deserve a platform that brings them to life.
            </p>
            <a href="register.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Sign Up Now
            </a>
        </section>
    </main>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>