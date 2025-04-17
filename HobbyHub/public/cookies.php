<?php
require_once '../includes/header.php';
require_once '../includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cookies - Hobby Hub</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <main class="container mx-auto px-4 py-8">
        <section class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">Manage Your Cookie Preferences</h1>
            <p class="text-xl text-gray-600">Control How We Use Cookies to Enhance Your Experience</p>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Your Cookie Choices</h2>
            <p class="text-gray-700 leading-relaxed mb-4">
                At Hobby Hub, we use cookies to provide a seamless and personalized experience. You can customize your cookie preferences below. Essential cookies are required for the platform to function and cannot be disabled. For more details, see our <a href="cookie_policy.php" class="text-blue-600 hover:underline">Cookie Policy</a>.
            </p>
            <form id="cookiePreferencesForm" class="bg-white p-6 rounded-lg shadow-md">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Essential Cookies</h3>
                    <p class="text-gray-700">
                        These cookies are necessary for Hobby Hub to function, such as maintaining your login session. They are always enabled.
                    </p>
                    <input type="checkbox" checked disabled class="mt-2">
                </div>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Performance Cookies</h3>
                    <p class="text-gray-700">
                        These cookies help us analyze how users interact with the platform, allowing us to improve performance and usability.
                    </p>
                    <input type="checkbox" id="performanceCookies" name="performance" class="mt-2">
                </div>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Functionality Cookies</h3>
                    <p class="text-gray-700">
                        These cookies remember your preferences, such as language or theme settings, for a personalized experience.
                    </p>
                    <input type="checkbox" id="functionalityCookies" name="functionality" class="mt-2">
                </div>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Advertising Cookies</h3>
                    <p class="text-gray-700">
                        These cookies enable us to show you relevant ads based on your interests, both on and off Hobby Hub.
                    </p>
                    <input type="checkbox" id="advertisingCookies" name="advertising" class="mt-2">
                </div>
                <div class="flex space-x-3">
                    <button type="button" id="savePreferences" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded hover:from-indigo-700 hover:to-purple-700 transition-colors duration-300">
                        Save Preferences
                    </button>
                    <button type="button" id="acceptAll" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors duration-300">
                        Accept All
                    </button>
                    <button type="button" id="rejectNonEssential" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors duration-300">
                        Reject Non-Essential
                    </button>
                </div>
            </form>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">How We Use Cookies</h2>
            <p class="text-gray-700 leading-relaxed">
                Cookies are small text files stored on your device to help us provide a better experience. They enable features like secure logins, personalized content, and performance analytics. You can change your preferences at any time by revisiting this page.
            </p>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Contact Us</h2>
            <p class="text-gray-700 leading-relaxed">
                If you have questions about our use of cookies or this page, please contact us at <a href="mailto:support@hobbyhub.com" class="text-blue-600 hover:underline">support@hobbyhub.com</a>.
            </p>
        </section>
    </main>

    <script>
        // Function to set a cookie with a given name, value, and expiration in days
        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + date.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }

        // Function to get a cookie by name
        function getCookie(name) {
            const nameEQ = name + "=";
            const cookies = document.cookie.split(';');
            for (let i = 0; i < cookies.length; i++) {
                let cookie = cookies[i];
                while (cookie.charAt(0) === ' ') {
                    cookie = cookie.substring(1, cookie.length);
                }
                if (cookie.indexOf(nameEQ) === 0) {
                    return cookie.substring(nameEQ.length, cookie.length);
                }
            }
            return null;
        }

        // Function to hide the cookie notice
        function hideCookieNotice() {
            const cookieNotice = document.getElementById('cookieNotice');
            if (cookieNotice) {
                cookieNotice.classList.add('hidden');
            }
        }

        // Load saved preferences into the form
        function loadPreferences() {
            const performance = getCookie('performanceCookies');
            const functionality = getCookie('functionalityCookies');
            const advertising = getCookie('advertisingCookies');

            document.getElementById('performanceCookies').checked = performance === 'true';
            document.getElementById('functionalityCookies').checked = functionality === 'true';
            document.getElementById('advertisingCookies').checked = advertising === 'true';
        }

        // Check if consent has been given and hide notice if so
        window.onload = function() {
            const consent = getCookie('cookieConsent');
            if (consent) {
                hideCookieNotice();
                loadPreferences();
            }
        };

        // Handle "Accept All" button in cookies.php form
        document.getElementById('acceptAll')?.addEventListener('click', function() {
            setCookie('cookieConsent', 'accepted', 365);
            setCookie('performanceCookies', 'true', 365);
            setCookie('functionalityCookies', 'true', 365);
            setCookie('advertisingCookies', 'true', 365);
            hideCookieNotice();
            alert('All cookies have been accepted.');
        });

        // Handle "Reject Non-Essential" button
        document.getElementById('rejectNonEssential')?.addEventListener('click', function() {
            setCookie('cookieConsent', 'rejected', 365);
            setCookie('performanceCookies', 'false', 365);
            setCookie('functionalityCookies', 'false', 365);
            setCookie('advertisingCookies', 'false', 365);
            hideCookieNotice();
            alert('Non-essential cookies have been rejected.');
        });

        // Handle "Save Preferences" button
        document.getElementById('savePreferences')?.addEventListener('click', function() {
            const performance = document.getElementById('performanceCookies').checked;
            const functionality = document.getElementById('functionalityCookies').checked;
            const advertising = document.getElementById('advertisingCookies').checked;

            setCookie('cookieConsent', 'custom', 365);
            setCookie('performanceCookies', performance, 365);
            setCookie('functionalityCookies', functionality, 365);
            setCookie('advertisingCookies', advertising, 365);
            hideCookieNotice();
            alert('Your cookie preferences have been saved.');
        });

        // Handle "Accept All" button in cookie notice (footer.php)
        document.getElementById('acceptCookies')?.addEventListener('click', function() {
            setCookie('cookieConsent', 'accepted', 365);
            setCookie('performanceCookies', 'true', 365);
            setCookie('functionalityCookies', 'true', 365);
            setCookie('advertisingCookies', 'true', 365);
            hideCookieNotice();
            alert('All cookies have been accepted.');
        });

        // Handle "Reject" button in cookie notice (footer.php)
        document.getElementById('rejectCookies')?.addEventListener('click', function() {
            setCookie('cookieConsent', 'rejected', 365);
            setCookie('performanceCookies', 'false', 365);
            setCookie('functionalityCookies', 'false', 365);
            setCookie('advertisingCookies', 'false', 365);
            hideCookieNotice();
            alert('Non-essential cookies have been rejected.');
        });
    </script>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>