<x-guest-welcome-layout>
    <div class="relative min-h-screen">
        <!-- Hero Section -->
        <div class="container mx-auto px-4 py-12">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <!-- Left Side - Welcome Content -->
                <div class="space-y-8">
                    <h1 class="text-5xl font-bold text-white">
                        Welcome to Florence EGI
                    </h1>
                    <p class="text-xl text-gray-300">
                        Your gateway to the next generation of digital assets.
                    </p>

                    <!-- Features Grid -->
                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <div class="card bg-base-100 bg-opacity-10 p-4">
                            <div class="mb-2 text-2xl text-primary">🔒</div>
                            <h3 class="font-semibold">Secure Platform</h3>
                            <p class="text-sm text-gray-400">Enhanced with 2FA protection</p>
                        </div>
                        <div class="card bg-base-100 bg-opacity-10 p-4">
                            <div class="mb-2 text-2xl text-primary">👥</div>
                            <h3 class="font-semibold">Team Management</h3>
                            <p class="text-sm text-gray-400">Collaborate seamlessly</p>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Auth Forms -->
                <div class="card bg-base-100 bg-opacity-10 shadow-xl backdrop-blur-lg">
                    <div class="card-body">
                        <livewire:welcome.auth-forms />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-welcome-layout>
