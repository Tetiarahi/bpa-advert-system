<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BPA Advertisement Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        .fade-in { animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-black-800 rounded-full flex items-center justify-center">
                        {{-- <span class="text-white font-bold text-lg">BPA</span> --}}
                        <img class="h-15 w-15 bpa-logo" src="{{ asset('images/bpa-logo.png') }}" alt="BPA Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">


                    </div>

                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Broadcasting & Publications Authority</h1>
                        <p class="text-sm text-gray-500">Advertisement Management System</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('presenter.login.form') }}"
                       class="bg-purple-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-purple-700 transition duration-200 shadow-md hover:shadow-lg flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Presenter Login</span>
                    </a>
                    <a href="/admin/login"
                       class="bg-blue-800 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200 shadow-md hover:shadow-lg">
                        Admin Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center fade-in">
                <div class="mb-8">
                    <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-6 pulse">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    Advertisement<br>
                    <span class="text-blue-200">Management System</span>
                </h1>

                <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-3xl mx-auto leading-relaxed">
                    Streamline your radio advertisement operations with our comprehensive management platform designed for the Broadcasting & Publications Authority.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="/admin/login"
                       class="bg-white text-blue-800 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-50 transition duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        Access Admin Panel
                    </a>
                    <a href="#features"
                       class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-800 transition duration-200">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">System Features</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Comprehensive tools for managing radio advertisements, programs, and memorial services with professional documentation and reporting.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Advertisement Management -->
                <div class="bg-white rounded-xl p-8 shadow-lg card-hover">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Advertisement Management</h3>
                    <p class="text-gray-600 mb-6">
                        Manage radio advertisements with multi-band support (AM/FM/Uekera), customer tracking, payment status, and professional PDF exports.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li>✓ Multi-band broadcasting support</li>
                        <li>✓ Customer relationship management</li>
                        <li>✓ Payment tracking & invoicing</li>
                        <li>✓ Professional PDF documentation</li>
                    </ul>
                </div>

                <!-- Program Sponsorship -->
                <div class="bg-white rounded-xl p-8 shadow-lg card-hover">
                    <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Program Sponsorship</h3>
                    <p class="text-gray-600 mb-6">
                        Handle radio program sponsorships including Nimaua Akea, News Sponsor, Karaki Sponsor, and Live Sponsor programs with staff assignment.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li>✓ Multiple program types</li>
                        <li>✓ Staff assignment & tracking</li>
                        <li>✓ Schedule management</li>
                        <li>✓ Sponsorship documentation</li>
                    </ul>
                </div>

                <!-- Memorial Services -->
                <div class="bg-white rounded-xl p-8 shadow-lg card-hover">
                    <div class="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Memorial Services</h3>
                    <p class="text-gray-600 mb-6">
                        Manage Gong memorial services with respectful documentation, song selection, and commemorative broadcasting arrangements.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li>✓ Memorial service scheduling</li>
                        <li>✓ Song title management</li>
                        <li>✓ Respectful documentation</li>
                        <li>✓ Family communication tools</li>
                    </ul>
                </div>

                <!-- Customer Management -->
                <div class="bg-white rounded-xl p-8 shadow-lg card-hover">
                    <div class="w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Customer Management</h3>
                    <p class="text-gray-600 mb-6">
                        Comprehensive customer database with contact information, service history, and relationship tracking for all BPA services.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li>✓ Customer database management</li>
                        <li>✓ Service history tracking</li>
                        <li>✓ Contact information storage</li>
                        <li>✓ Relationship management</li>
                    </ul>
                </div>

                <!-- Reporting & Analytics -->
                <div class="bg-white rounded-xl p-8 shadow-lg card-hover">
                    <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Reporting & Analytics</h3>
                    <p class="text-gray-600 mb-6">
                        Generate professional reports, export documentation, and track performance metrics across all BPA advertisement services.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li>✓ Professional PDF exports</li>
                        <li>✓ Performance analytics</li>
                        <li>✓ Financial reporting</li>
                        <li>✓ Activity logging & audit trails</li>
                    </ul>
                </div>

                <!-- User Management -->
                <div class="bg-white rounded-xl p-8 shadow-lg card-hover">
                    <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">User Management</h3>
                    <p class="text-gray-600 mb-6">
                        Role-based access control with staff assignment capabilities, ensuring secure and organized workflow management.
                    </p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li>✓ Role-based access control</li>
                        <li>✓ Staff assignment tracking</li>
                        <li>✓ Secure authentication</li>
                        <li>✓ Activity monitoring</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-blue-800 text-white py-20">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold mb-6">Ready to Get Started?</h2>
            <p class="text-xl mb-8 text-blue-100">
                Access the BPA Advertisement Management System and streamline your radio advertisement operations today.
            </p>

            <a href="/admin/login"
               class="bg-white text-blue-800 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-50 transition duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 inline-block">
                Login to Admin Panel
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-blue-800 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-lg">BPA</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Broadcasting & Publications Authority</h3>
                        </div>
                    </div>
                    <p class="text-gray-400">
                        Professional advertisement management system for radio broadcasting services and memorial programs.
                    </p>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">System Features</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Advertisement Management</li>
                        <li>Program Sponsorship</li>
                        <li>Memorial Services</li>
                        <li>Customer Database</li>
                        <li>Professional Reporting</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Information</h4>
                    <div class="space-y-2 text-gray-400">
                        <p>Broadcasting & Publications Authority</p>
                        <p>Radio Advertisement Division</p>
                        <p>Professional Services Department</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Broadcasting & Publications Authority. All rights reserved.</p>
                <p class="mt-2">Advertisement Management System v1.0</p>
            </div>
        </div>
    </footer>
</body>
</html>
