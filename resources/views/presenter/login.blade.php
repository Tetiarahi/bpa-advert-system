<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radio Presenter Login - BPA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 min-h-screen flex items-center justify-center">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    
    <div class="relative z-10 max-w-md w-full mx-4">
        <!-- Logo and Header -->
        <div class="text-center mb-8">
            <div class="bg-white rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center shadow-lg">
                <i class="fas fa-microphone text-3xl text-blue-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Radio Presenter</h1>
            <p class="text-blue-200">BPA Broadcasting System</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <form method="POST" action="{{ route('presenter.login') }}">
                @csrf
                
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email Address
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                           placeholder="presenter@bpa.com"
                           required>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                           placeholder="Enter your password"
                           required>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 transform hover:scale-105">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In to Dashboard
                </button>
            </form>

            <!-- Back to Main Site -->
            <div class="mt-6 text-center">
                <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-blue-600 transition duration-200">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to Main Site
                </a>
            </div>
        </div>

        <!-- Features -->
        <div class="mt-8 text-center">
            <div class="grid grid-cols-3 gap-4 text-white">
                <div class="bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                    <i class="fas fa-sticky-note text-2xl mb-2"></i>
                    <p class="text-xs">Sticky Notes</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                    <i class="fas fa-clock text-2xl mb-2"></i>
                    <p class="text-xs">Real-time</p>
                </div>
                <div class="bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                    <i class="fas fa-check-circle text-2xl mb-2"></i>
                    <p class="text-xs">Read Status</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Animation -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute top-40 left-40 w-80 h-80 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</body>
</html>
