<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RKAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.7.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="font-poppins h-screen overflow-hidden">
    <div class="flex flex-col lg:flex-row h-screen">
        <!-- Left Side - Background Image (70% on desktop, 40% on mobile) -->
        <div class="h-[40%] lg:h-auto lg:w-[70%] relative bg-cover bg-center bg-no-repeat" style="background-image: url('/images/bg-login.jpg');">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        </div>
        
        <!-- Right Side - Login Form (30% on desktop, 60% on mobile) -->
        <div class="flex-1 lg:w-[30%] bg-base-100 flex flex-col items-center justify-center p-4 lg:p-8">
            <!-- Logo -->
            <div class="text-center mb-4 lg:mb-8">
                <img src="/images/logo.png" alt="RKAN Logo" class="w-16 lg:w-20 h-auto mx-auto">
            </div>
            
            <!-- Login Form -->
            <div class="w-full max-w-sm px-4 lg:px-0">

                
                <form method="POST" action="{{ url('/login') }}" class="space-y-5">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-sm lg:text-xs font-normal">Email</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email" name="email" placeholder="Enter your email" 
                                   class="w-full pl-10 text-base lg:text-sm h-12 lg:h-10 rounded-lg lg:rounded-sm border border-gray-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" required autofocus>
                        </div>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text text-sm lg:text-xs font-normal">Password</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" name="password" placeholder="Enter your password" 
                                   class="w-full pl-10 text-base lg:text-sm h-12 lg:h-10 rounded-lg lg:rounded-sm border border-gray-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" required>
                        </div>
                    </div>
                    
                    <!-- Forgot Password -->
                    <div class="text-right">
                        <a href="#" class="text-primary text-sm lg:text-xs no-underline hover:underline transition-all duration-200">Forgot password?</a>
                    </div>
                    
                    <!-- Login Button -->
                    <button type="submit" class="w-full h-12 lg:h-10 bg-primary text-primary-content font-medium rounded-lg lg:rounded-sm hover:bg-primary-focus transition-colors duration-200 text-base lg:text-sm">
                        Sign In
                    </button>
                </form>
                
                <!-- Divider -->
                <div class="divider text-sm lg:text-xs text-gray-500">or</div>
                
                <!-- Social Media Icons -->
                <div class="flex justify-center space-x-4 lg:space-x-4">
                    <!-- X (Twitter) -->
                    <button class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </button>
                    
                    <!-- GitHub -->
                    <button class="w-10 h-10 bg-gray-800 text-white rounded-full flex items-center justify-center hover:bg-gray-700 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </button>
                    
                    <!-- Instagram -->
                    <button class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full flex items-center justify-center hover:from-purple-600 hover:to-pink-600 transition-all duration-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </button>
                    
                    <!-- Google -->
                    <button class="w-10 h-10 bg-white text-gray-700 border border-gray-300 rounded-full flex items-center justify-center hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="mt-auto pt-6 lg:pt-8 text-center">
                <div class="flex flex-wrap justify-center items-center gap-2 lg:gap-3 text-xs lg:text-[11px] text-gray-500 px-4">
                    <a href="#" class="hover:text-gray-700 transition-colors duration-200">Rkan Apps</a>
                    <span class="text-gray-300 hidden lg:inline">|</span>
                    <a href="#" class="hover:text-gray-700 transition-colors duration-200">Privacy Policy</a>
                    <span class="text-gray-300 hidden lg:inline">|</span>
                    <a href="#" class="hover:text-gray-700 transition-colors duration-200">Terms of Service</a>
                    <span class="text-gray-300 hidden lg:inline">|</span>
                    <a href="#" class="hover:text-gray-700 transition-colors duration-200">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 