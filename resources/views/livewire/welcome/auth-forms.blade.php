<script>console.log('/resources/views/livewire/welcome/auth-forms.blade.php');</script>

<div>
    <!-- Tab Navigation -->
    <div class="tabs tabs-boxed bg-base-200 bg-opacity-50 mb-6" id="authTabs">
        <a href="#" data-tab="login" class="tab tab-login tab-active">Login</a>
        <a href="#" data-tab="register" class="tab tab-register">Register</a>
    </div>

    <!-- Forms Container -->
    <div class="transition-all duration-300">

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div id="form-login" class="space-y-4">
                    <div>
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email_l" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                                class="input input-bordered w-full" />
                    </div>

                    <div>
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input id="password_l" type="password" name="password" required autocomplete="current-password"
                                class="input input-bordered w-full" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="label cursor-pointer">
                            <input type="checkbox" name="remember" class="checkbox checkbox-primary" />
                            <span class="label-text ml-2">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="link link-primary" href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    <x-button class="btn btn-primary w-full">
                        {{ __('Log in') }}
                    </x-button>
                </div>
            </form>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div id="form-register" class="space-y-4 hidden">
                    <div>
                        <x-label for="name" value="{{ __('Name') }}" />
                        <x-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                                class="input input-bordered w-full" />
                    </div>

                    <div>
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email_r" type="email" name="email" :value="old('email')" required autocomplete="username"
                                class="input input-bordered w-full" />
                    </div>

                    <div>
                        <x-label for="password" value="{{ __('Password') }}" />
                        <x-input id="password_r" type="password" name="password" required autocomplete="new-password"
                                class="input input-bordered w-full" />
                    </div>

                    <div>
                        <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                        <x-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                class="input input-bordered w-full" />
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div>
                            <x-label for="terms">
                                <div class="flex items-center">
                                    <x-checkbox name="terms" id="terms" required />
                                    <div class="ml-2">
                                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="link link-primary">'.__('Terms of Service').'</a>',
                                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="link link-primary">'.__('Privacy Policy').'</a>',
                                        ]) !!}
                                    </div>
                                </div>
                            </x-label>
                        </div>
                    @endif

                    <x-button class="btn btn-primary w-full">
                        {{ __('Register') }}
                    </x-button>
                </div>
            </form>

    </div>
</div>
<script>

document.addEventListener('DOMContentLoaded', () => {
    const loginTab = document.querySelector('.tab-login');
    const registerTab = document.querySelector('.tab-register');
    const loginForm = document.getElementById('form-login');
    const registerForm = document.getElementById('form-register');

    loginTab.addEventListener('click', (e) => {
        e.preventDefault();
        loginTab.classList.add('tab-active');
        registerTab.classList.remove('tab-active');
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
    });

    registerTab.addEventListener('click', (e) => {
        e.preventDefault();
        registerTab.classList.add('tab-active');
        loginTab.classList.remove('tab-active');
        registerForm.classList.remove('hidden');
        loginForm.classList.add('hidden');
    });
});
</script>
