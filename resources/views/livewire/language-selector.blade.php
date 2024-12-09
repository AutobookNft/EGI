<div class="dropdown">
    <div tabindex="0" role="button" class="m-1 btn btn-sm">
        <span class="fi fi-{{ $currentLocale === 'en' ? 'gb' : $currentLocale }}"></span>
        {{ config('app.languages')[$currentLocale] }}
    </div>
    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box">
        @foreach ($languages as $code => $name)
            <li>
                <a wire:click="$set('currentLocale', '{{ $code }}')" class="flex items-center gap-2">
                    <span class="fi fi-{{ $code === 'en' ? 'gb' : $code }}"></span>
                    {{ $name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
