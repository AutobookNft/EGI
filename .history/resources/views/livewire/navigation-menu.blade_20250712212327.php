<div class="navbar bg-base-100 shadow-lg">
    <div class="flex-none lg:hidden">
        <label for="main-drawer" class="btn btn-square btn-ghost drawer-button">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                class="inline-block h-5 w-5 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </label>
    </div>

    <div class="flex-1">
        <a href="{{ route('dashboard') }}" class="btn btn-ghost text-xl normal-case">
            {{ config('app.name') }}
        </a>
    </div>

    <div class="flex-none">
        @if ($user)
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="avatar btn btn-circle btn-ghost">
                    <div class="w-10 rounded-full">
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                    </div>
                </label>
                <ul tabindex="0"
                    class="menu dropdown-content menu-sm z-[1] mt-3 w-52 rounded-box bg-base-100 p-2 shadow">
                    <li>
                        <a href="{{ route('profile.show') }}" class="justify-between">
                            Profile
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Logout
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn btn-ghost">
                Login
            </a>
        @endif
    </div>
</div>
