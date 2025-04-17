<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Hobby Hub</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <main class="container mx-auto px-4 py-8">
        <section class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">Welcome to Hobby Hub</h1>
            <p class="text-xl text-gray-600">Where Passion Meets People</p>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Our Mission</h2>
            <p class="text-gray-700 leading-relaxed">
                Hobby Hub is a vibrant social platform crafted to unite individuals through their shared passions. 
                Whether you're a musician, coder, photographer, gamer, writer, fitness enthusiast, or have a unique hobby like chess or anime, 
                Hobby Hub is your space to connect, collaborate, and celebrate what you love. 
                Our mission is to make finding like-minded people effortless, fostering meaningful connections both online and in real life.
            </p>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Why Hobby Hub?</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                We understand the struggle of finding people who share your interests. Scattered WhatsApp groups, inactive Discord servers, 
                or niche forums often fall short. Hobby Hub solves this by bringing everything into one dynamic, user-friendly platform. 
                Discover friends, join lively groups, engage in real-time discussions, and stay updated on exciting events – all tailored to your passions.
            </p>
            <ul class="list-disc list-inside text-gray-700">
                <li>Create a personalized profile showcasing your hobbies and interests.</li>
                <li>Join or create groups dedicated to your favorite activities.</li>
                <li>Participate in real-time discussions with enthusiasts from around the world.</li>
                <li>Discover and attend local or virtual events that match your passions.</li>
                <li>Connect with people who share your vibe, whether for collaboration or friendship.</li>
            </ul>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Our Story</h2>
            <p class="text-gray-700 leading-relaxed">
                Hobby Hub was born from a simple idea: to create a space where hobbies take center stage. 
                We noticed how challenging it was for students and hobbyists to find communities that truly resonate with their interests. 
                So, we set out to build a platform that’s more than just a website – it’s a community hub where people can connect, create, and grow together. 
                From casual hobbyists to dedicated enthusiasts, Hobby Hub welcomes everyone to share their passions and find their tribe.
            </p>
        </section>

        <section class="text-center">
            <h2 class="text-2xl font-semibold mb-4">Join the Hobby Hub Community</h2>
            <p class="text-gray-700 mb-6">
                Ready to dive into a world of shared passions? Sign up today and start connecting with people who get you. 
                Your hobbies deserve a community, and Hobby Hub is here to make it happen.
            </p>
            <a href="register.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Get Started Now
            </a>
        </section>
    </main>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>