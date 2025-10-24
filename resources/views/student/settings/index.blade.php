<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings & Security') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Settings Navigation -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <nav class="flex space-x-8" aria-label="Tabs">
                        <a href="#profile" class="settings-tab active border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            Profile
                        </a>
                        <a href="#security" class="settings-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            Security
                        </a>
                        <a href="#preferences" class="settings-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            Preferences
                        </a>
                        <a href="#privacy" class="settings-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            Privacy
                        </a>
                        <a href="#account" class="settings-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            Account
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Profile Settings -->
            <div id="profile-section" class="settings-section">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Profile Information</h3>
                        
                        <form method="POST" action="{{ route('student.settings.update-profile') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Profile Image -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Image</label>
                                <div class="flex items-center space-x-4">
                                    @if($user->profile_image)
                                        <img src="{{ asset('storage/' . $user->profile_image) }}" 
                                             alt="Profile Image" 
                                             class="h-20 w-20 rounded-full object-cover">
                                    @else
                                        <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-gray-600 font-medium text-2xl">
                                                {{ substr($user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <input type="file" name="profile_image" accept="image/*" 
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        <p class="text-xs text-gray-500 mt-1">JPG, PNG or GIF (max 2MB)</p>
                                    </div>
                                </div>
                                @error('profile_image')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Personal Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                    <select name="gender" id="gender"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('date_of_birth')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                <textarea name="address" id="address" rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div id="security-section" class="settings-section hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
                        
                        <form method="POST" action="{{ route('student.settings.update-password') }}">
                            @csrf
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('current_password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="password" id="password" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Preferences -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Security Preferences</h3>
                        
                        <form method="POST" action="{{ route('student.settings.update-security') }}">
                            @csrf
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Email Notifications</h4>
                                        <p class="text-sm text-gray-600">Receive security notifications via email</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="email_notifications" value="1" 
                                               {{ $student->email_notifications ?? true ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">SMS Notifications</h4>
                                        <p class="text-sm text-gray-600">Receive security notifications via SMS</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="sms_notifications" value="1"
                                               {{ $student->sms_notifications ?? false ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Two-Factor Authentication</h4>
                                        <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="two_factor_enabled" value="1"
                                               {{ $student->two_factor_enabled ?? false ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Login Alerts</h4>
                                        <p class="text-sm text-gray-600">Get notified of new login attempts</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="login_alerts" value="1"
                                               {{ $student->login_alerts ?? true ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Update Security Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Login History & Devices -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="{{ route('student.settings.login-history') }}" 
                       class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Login History</h3>
                                    <p class="text-sm text-gray-600">View recent login attempts</p>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('student.settings.connected-devices') }}" 
                       class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center">
                                <svg class="h-8 w-8 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Connected Devices</h3>
                                    <p class="text-sm text-gray-600">Manage your devices</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Preferences Settings -->
            <div id="preferences-section" class="settings-section hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Preferences</h3>
                        
                        <form method="POST" action="{{ route('student.settings.update-preferences') }}">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                                    <select name="language" id="language"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="en" {{ ($student->language ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                                        <option value="es" {{ ($student->language ?? 'en') === 'es' ? 'selected' : '' }}>Spanish</option>
                                        <option value="fr" {{ ($student->language ?? 'en') === 'fr' ? 'selected' : '' }}>French</option>
                                        <option value="de" {{ ($student->language ?? 'en') === 'de' ? 'selected' : '' }}>German</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                                    <select name="timezone" id="timezone"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="UTC" {{ ($student->timezone ?? 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="America/New_York" {{ ($student->timezone ?? 'UTC') === 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                        <option value="America/Chicago" {{ ($student->timezone ?? 'UTC') === 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                        <option value="America/Denver" {{ ($student->timezone ?? 'UTC') === 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                        <option value="America/Los_Angeles" {{ ($student->timezone ?? 'UTC') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="date_format" class="block text-sm font-medium text-gray-700">Date Format</label>
                                    <select name="date_format" id="date_format"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="Y-m-d" {{ ($student->date_format ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                        <option value="m/d/Y" {{ ($student->date_format ?? 'Y-m-d') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                        <option value="d/m/Y" {{ ($student->date_format ?? 'Y-m-d') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="theme" class="block text-sm font-medium text-gray-700">Theme</label>
                                    <select name="theme" id="theme"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="light" {{ ($student->theme ?? 'light') === 'light' ? 'selected' : '' }}>Light</option>
                                        <option value="dark" {{ ($student->theme ?? 'light') === 'dark' ? 'selected' : '' }}>Dark</option>
                                        <option value="auto" {{ ($student->theme ?? 'light') === 'auto' ? 'selected' : '' }}>Auto</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="notifications_frequency" class="block text-sm font-medium text-gray-700">Notification Frequency</label>
                                <select name="notifications_frequency" id="notifications_frequency"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="immediate" {{ ($student->notifications_frequency ?? 'daily') === 'immediate' ? 'selected' : '' }}>Immediate</option>
                                    <option value="daily" {{ ($student->notifications_frequency ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ ($student->notifications_frequency ?? 'daily') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="never" {{ ($student->notifications_frequency ?? 'daily') === 'never' ? 'selected' : '' }}>Never</option>
                                </select>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Update Preferences
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div id="privacy-section" class="settings-section hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Privacy Settings</h3>
                        
                        <form method="POST" action="{{ route('student.settings.update-security') }}">
                            @csrf
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="privacy_level" class="block text-sm font-medium text-gray-700">Privacy Level</label>
                                    <select name="privacy_level" id="privacy_level"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="private" {{ ($student->privacy_level ?? 'private') === 'private' ? 'selected' : '' }}>Private - Only you can see your information</option>
                                        <option value="friends" {{ ($student->privacy_level ?? 'private') === 'friends' ? 'selected' : '' }}>Friends - Only friends can see your information</option>
                                        <option value="public" {{ ($student->privacy_level ?? 'private') === 'public' ? 'selected' : '' }}>Public - Everyone can see your information</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Update Privacy Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Data Export -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Data Export</h3>
                        <p class="text-sm text-gray-600 mb-4">Download a copy of all your data stored in the system.</p>
                        
                        <a href="{{ route('student.settings.export-data') }}" 
                           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Export My Data
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Settings -->
            <div id="account-section" class="settings-section hidden">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-red-600 mb-4">Danger Zone</h3>
                        
                        <div class="border border-red-200 rounded-lg p-4">
                            <h4 class="text-md font-medium text-gray-900 mb-2">Deactivate Account</h4>
                            <p class="text-sm text-gray-600 mb-4">Deactivate your account. You can reactivate it by contacting support.</p>
                            
                            <button onclick="toggleDeactivationForm()" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Deactivate Account
                            </button>

                            <div id="deactivation-form" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <form method="POST" action="{{ route('student.settings.deactivate-account') }}">
                                    @csrf
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label for="password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                            <input type="password" name="password" id="password" required
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Deactivation</label>
                                            <textarea name="reason" id="reason" rows="3" required
                                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                      placeholder="Please tell us why you want to deactivate your account..."></textarea>
                                        </div>

                                        <div class="flex items-center">
                                            <input type="checkbox" name="confirm_deactivation" id="confirm_deactivation" required
                                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                            <label for="confirm_deactivation" class="ml-2 block text-sm text-gray-900">
                                                I understand that deactivating my account will prevent me from accessing the system.
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex space-x-3">
                                        <button type="submit" 
                                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Yes, Deactivate My Account
                                        </button>
                                        <button type="button" onclick="toggleDeactivationForm()"
                                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.settings-tab');
            const sections = document.querySelectorAll('.settings-section');

            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active', 'border-blue-500', 'text-blue-600'));
                    tabs.forEach(t => t.classList.add('border-transparent', 'text-gray-500');
                    
                    // Add active class to clicked tab
                    this.classList.remove('border-transparent', 'text-gray-500');
                    this.classList.add('active', 'border-blue-500', 'text-blue-600');
                    
                    // Hide all sections
                    sections.forEach(section => section.classList.add('hidden'));
                    
                    // Show target section
                    const targetId = this.getAttribute('href').substring(1) + '-section';
                    const targetSection = document.getElementById(targetId);
                    if (targetSection) {
                        targetSection.classList.remove('hidden');
                    }
                });
            });
        });

        function toggleDeactivationForm() {
            const form = document.getElementById('deactivation-form');
            form.classList.toggle('hidden');
        }
    </script>
</x-student-layout>
