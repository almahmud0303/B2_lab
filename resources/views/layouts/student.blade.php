<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Student Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('student.dashboard') }}" class="text-xl font-bold text-blue-600">
                                {{ config('app.name') }} - Student
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('student.dashboard') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 {{ request()->routeIs('student.dashboard') ? 'border-blue-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Dashboard
                            </a>
                            
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300">
                                    <span>Academics</span>
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                
                                <div x-show="open" @click.away="open = false" 
                                     class="absolute left-0 w-48 mt-2 py-2 bg-white rounded-md shadow-lg z-50">
                                    <a href="{{ route('student.courses.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Courses</a>
                                    <a href="{{ route('student.exams.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Exams</a>
                                    <a href="{{ route('student.exams.results') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Results</a>
                                    <a href="{{ route('student.exams.calendar') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Academic Calendar</a>
                                </div>
                            </div>

                            <a href="{{ route('student.fees.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 {{ request()->routeIs('student.fees.*') ? 'border-blue-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Fees
                            </a>

                            <a href="{{ route('student.library.index') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 {{ request()->routeIs('student.library.*') ? 'border-blue-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Library
                            </a>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <!-- Profile Dropdown -->
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                </button>
                            </div>

                            <div x-show="open" @click.away="open = false" 
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <a href="{{ route('student.profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="{{ route('student.profile.academic') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Academic Info</a>
                                <a href="{{ route('student.profile.notifications') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Notifications</a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="open" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('student.dashboard') }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('student.dashboard') ? 'border-blue-400 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('student.courses.index') }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('student.courses.*') ? 'border-blue-400 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300' }}">
                        Courses
                    </a>
                    <a href="{{ route('student.exams.index') }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('student.exams.*') ? 'border-blue-400 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300' }}">
                        Exams
                    </a>
                    <a href="{{ route('student.fees.index') }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('student.fees.*') ? 'border-blue-400 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300' }}">
                        Fees
                    </a>
                    <a href="{{ route('student.library.index') }}" 
                       class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('student.library.*') ? 'border-blue-400 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300' }}">
                        Library
                    </a>
                </div>
                
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="{{ route('student.profile.index') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>
