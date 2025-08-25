<script>console.log('resources/views/navigation-menu.blade.php');</script>
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 dark:border-gray-700 dark:bg-gray-800">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block w-auto h-9" />
                    </a>
                </div>

                {{-- @if (Auth::user()->can('superadmin')) --}}
                {{-- @endif --}}

                <!-- Navigation Links Desktop -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link href="{{ route('home.collections.index') }}" :active="request()->routeIs('home.collections.*')">
                        {{ __('Collections') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('epps.index') }}" :active="request()->routeIs('epps.*')">
                        {{ __('EPPS') }}
                    </x-nav-link>
                </div>

            </div>

            <div class="flex">
                <div class="flex items-center text-4xl text-gray-700 shrink-0 dark:text-gray-500">
                    {{ Auth::user()?->name ?? '' }}
                </div>
            </div>


            <div class="hidden sm:ms-6 sm:flex sm:items-center">

                <!-- Settings Dropdown -->
                <div class="relative ms-3">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button
                                    class="flex text-sm transition border-2 border-transparent rounded-full focus:border-gray-300 focus:outline-none">
                                    <img class="object-cover rounded-full size-8"
                                        src="{{ Auth::user()?->profile_photo_url ?? null }}"
                                        alt="{{ Auth::user()?->name ?? '' }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:bg-gray-50 focus:outline-none active:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:bg-gray-700 dark:active:bg-gray-700">
                                        {{ Auth::user()?->name ?? '' }}

                                        <svg class="-me-0.5 ms-2 size-4" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('menu.manage_account') }}
                            </div>

                            <x-dropdown-link href="{{ route('user.domains.personal-data') }}">
                                {{ __('menu.edit_personal_data') }}
                            </x-dropdown-link>

                            @can('manage_profile')
                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                            @endcan

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Privacy & GDPR Section -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('menu.gdpr_privacy') }}
                            </div>

                            @can('manage_consents')
                                <x-dropdown-link href="{{ route('gdpr.consent') }}">
                                    {{ __('gdpr.menu.gdpr_center') }}
                                </x-dropdown-link>
                            @endcan

                            <x-dropdown-link href="{{ route('gdpr.security') }}">
                                {{ __('menu.security_password') }}
                            </x-dropdown-link>

                            <x-dropdown-link href="{{ route('gdpr.profile-images') }}">
                                {{ __('menu.profile_images') }}
                            </x-dropdown-link>

                            @can('gdpr.export_data')
                                <x-dropdown-link href="{{ route('gdpr.export-data') }}">
                                    {{ __('menu.export_data') }}
                                </x-dropdown-link>
                            @endcan

                            @can('gdpr.limit_processing')
                                <x-dropdown-link href="{{ route('gdpr.limit-processing') }}">
                                    {{ __('menu.limit_processing') }}
                                </x-dropdown-link>
                            @endcan

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Collections & Assets Section -->
                            @can('create_collection')
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('menu.collections') }}
                                </div>

                                <x-dropdown-link href="{{ route('collections.index') }}">
                                    {{ __('menu.my_collections') }}
                                </x-dropdown-link>

                                <x-dropdown-link href="{{ route('collections.create') }}">
                                    {{ __('menu.new_collection') }}
                                </x-dropdown-link>

                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                            @endcan

                            <!-- Activity & Notifications -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('menu.activity') }}
                            </div>

                            @can('view_activity_log')
                                <x-dropdown-link href="{{ route('gdpr.activity-log') }}">
                                    {{ __('menu.activity_log') }}
                                </x-dropdown-link>
                            @endcan

                            @can('view_notifications')
                                <x-dropdown-link href="{{ route('notifications.index') }}">
                                    {{ __('menu.notifications') }}
                                </x-dropdown-link>
                            @endcan

                            <!-- Admin Tools Section -->
                            @can('manage_roles')
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('menu.admin_tools') }}
                                </div>

                                <x-dropdown-link href="{{ route('admin.roles.index') }}">
                                    {{ __('menu.permissions_roles') }}
                                </x-dropdown-link>

                                <x-dropdown-link href="{{ route('admin.users.index') }}">
                                    {{ __('menu.user_management') }}
                                </x-dropdown-link>
                            @endcan

                            @can('view_statistics')
                                <x-dropdown-link href="{{ route('statistics.index') }}">
                                    {{ __('menu.statistics') }}
                                </x-dropdown-link>
                            @endcan

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Support & Legal -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('menu.support_legal') }}
                            </div>

                            <x-dropdown-link href="{{ route('gdpr.privacy-policy') }}">
                                {{ __('menu.privacy_policy') }}
                            </x-dropdown-link>

                            <x-dropdown-link href="{{ route('gdpr.terms') }}">
                                {{ __('menu.terms_of_service') }}
                            </x-dropdown-link>

                            @can('contact_dpo')
                                <x-dropdown-link href="{{ route('gdpr.contact-dpo') }}">
                                    {{ __('menu.contact_dpo') }}
                                </x-dropdown-link>
                            @endcan

                            <!-- Dangerous Actions -->
                            @can('gdpr.delete_account')
                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                <div class="block px-4 py-2 text-xs text-red-500">
                                    {{ __('menu.danger_zone') }}
                                </div>

                                <x-dropdown-link href="{{ route('gdpr.delete-account') }}" class="text-red-600 hover:text-red-700">
                                    {{ __('menu.delete_account') }}
                                </x-dropdown-link>
                            @endcan

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('menu.logout') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Sezione mobile (schermi piccoli) con due pulsanti: uno per la navbar, uno per la sidebar -->
            <div class="flex items-center -me-2 sm:hidden">
                <!-- Pulsante per togglare la SIDEBAR (Drawer DaisyUI) -->
                <!-- Usa un'icona diversa per distinguere questo dal menu navbar -->
                <label for="main-drawer"
                    class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md ms-2 hover:bg-gray-100 hover:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <!-- Ad esempio puntini che simboleggiano la sidebar -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6h9M10 12h9M10 18h9M4 6h.01M4 12h.01M4 18h.01" />
                    </svg>
                </label>

                <!-- Pulsante per togglare il menu della NAVBAR (Alpine) -->
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:bg-gray-100 hover:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <!-- Icona hamburger (visibile quando open = false) -->
                        <path :class="{ 'inline-flex': !open, 'hidden': open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <!-- Icona X (visibile quando open = true) -->
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>


            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Alpine) -->
    <!-- Menu responsive della NAVBAR con tutti i link come nella versione desktop -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('home.collections.index') }}" :active="request()->routeIs('home.collections.*')">
                {{ __('Collections') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('epps.index') }}" :active="request()->routeIs('epps.*')">
                {{ __('EPPS') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="me-3 shrink-0">
                        <img class="object-cover rounded-full size-10"
                            src="{{ Auth::user()?->profile_photo_url ?? null }}"
                            alt="{{ Auth::user()?->name ?? '' }}" />
                    </div>
                @endif

                <div>
                    <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()?->name ?? '' }}
                    </div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()?->email ?? '' }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <div class="px-4 py-2 text-xs text-gray-400">
                    {{ __('menu.manage_account') }}
                </div>

                <x-responsive-nav-link href="{{ route('user.domains.personal-data') }}" :active="request()->routeIs('user.domains.personal-data')">
                    {{ __('menu.edit_personal_data') }}
                </x-responsive-nav-link>

                @can('manage_profile')
                    <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                @endcan

                <!-- Privacy & GDPR Section -->
                <div class="px-4 py-2 mt-4 text-xs text-gray-400">
                    {{ __('menu.gdpr_privacy') }}
                </div>

                @can('manage_consents')
                    <x-responsive-nav-link href="{{ route('gdpr.consent') }}" :active="request()->routeIs('gdpr.consent')">
                        {{ __('gdpr.menu.gdpr_center') }}
                    </x-responsive-nav-link>
                @endcan

                <x-responsive-nav-link href="{{ route('gdpr.security') }}" :active="request()->routeIs('gdpr.security')">
                    {{ __('menu.security_password') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('gdpr.profile-images') }}" :active="request()->routeIs('gdpr.profile-images')">
                    {{ __('menu.profile_images') }}
                </x-responsive-nav-link>

                @can('gdpr.export_data')
                    <x-responsive-nav-link href="{{ route('gdpr.export-data') }}" :active="request()->routeIs('gdpr.export-data')">
                        {{ __('menu.export_data') }}
                    </x-responsive-nav-link>
                @endcan

                @can('gdpr.limit_processing')
                    <x-responsive-nav-link href="{{ route('gdpr.limit-processing') }}" :active="request()->routeIs('gdpr.limit-processing')">
                        {{ __('menu.limit_processing') }}
                    </x-responsive-nav-link>
                @endcan

                <!-- Collections & Assets Section -->
                @can('create_collection')
                    <div class="px-4 py-2 mt-4 text-xs text-gray-400">
                        {{ __('menu.collections') }}
                    </div>

                    <x-responsive-nav-link href="{{ route('collections.index') }}" :active="request()->routeIs('collections.index')">
                        {{ __('menu.my_collections') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link href="{{ route('collections.create') }}" :active="request()->routeIs('collections.create')">
                        {{ __('menu.new_collection') }}
                    </x-responsive-nav-link>
                @endcan

                <!-- Activity & Notifications -->
                <div class="px-4 py-2 mt-4 text-xs text-gray-400">
                    {{ __('menu.activity') }}
                </div>

                @can('view_activity_log')
                    <x-responsive-nav-link href="{{ route('gdpr.activity-log') }}" :active="request()->routeIs('gdpr.activity-log')">
                        {{ __('menu.activity_log') }}
                    </x-responsive-nav-link>
                @endcan

                @can('view_notifications')
                    <x-responsive-nav-link href="{{ route('notifications.index') }}" :active="request()->routeIs('notifications.*')">
                        {{ __('menu.notifications') }}
                    </x-responsive-nav-link>
                @endcan

                <!-- Admin Tools Section -->
                @can('manage_roles')
                    <div class="px-4 py-2 mt-4 text-xs text-gray-400">
                        {{ __('menu.admin_tools') }}
                    </div>

                    <x-responsive-nav-link href="{{ route('admin.roles.index') }}" :active="request()->routeIs('admin.roles.*')">
                        {{ __('menu.permissions_roles') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                        {{ __('menu.user_management') }}
                    </x-responsive-nav-link>
                @endcan

                @can('view_statistics')
                    <x-responsive-nav-link href="{{ route('statistics.index') }}" :active="request()->routeIs('statistics.*')">
                        {{ __('menu.statistics') }}
                    </x-responsive-nav-link>
                @endcan

                <!-- Support & Legal -->
                <div class="px-4 py-2 mt-4 text-xs text-gray-400">
                    {{ __('menu.support_legal') }}
                </div>

                <x-responsive-nav-link href="{{ route('gdpr.privacy-policy') }}" :active="request()->routeIs('gdpr.privacy-policy')">
                    {{ __('menu.privacy_policy') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('gdpr.terms') }}" :active="request()->routeIs('gdpr.terms')">
                    {{ __('menu.terms_of_service') }}
                </x-responsive-nav-link>

                @can('contact_dpo')
                    <x-responsive-nav-link href="{{ route('gdpr.contact-dpo') }}" :active="request()->routeIs('gdpr.contact-dpo')">
                        {{ __('menu.contact_dpo') }}
                    </x-responsive-nav-link>
                @endcan

                <!-- Dangerous Actions -->
                @can('gdpr.delete_account')
                    <div class="px-4 py-2 mt-4 text-xs text-red-500">
                        {{ __('menu.danger_zone') }}
                    </div>

                    <x-responsive-nav-link href="{{ route('gdpr.delete-account') }}" :active="request()->routeIs('gdpr.delete-account')" class="text-red-600">
                        {{ __('menu.delete_account') }}
                    </x-responsive-nav-link>
                @endcan

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        {{ __('menu.logout') }}
                    </x-responsive-nav-link>
                </form>

            </div>
        </div>
    </div>
</nav>
