<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Edit Role') }}: {{ $role->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 shadow-xl rounded-lg p-6">
                <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-label for="name" value="{{ __('Role Name') }}" />
                        <x-input id="name" type="text" name="name" :value="old('name', $role->name)" required 
                                class="input input-bordered w-full mt-1" />
                        @error('name')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-label value="{{ __('Permissions') }}" class="mb-3" />
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($permissions as $permission)
                                <label class="label cursor-pointer justify-start gap-3">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}"
                                           class="checkbox checkbox-primary"
                                           @checked($role->permissions->contains($permission)) />
                                    <span class="label-text">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('permissions')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <x-button class="btn btn-primary">
                            {{ __('Update Role') }}
                        </x-button>
                        <a href="{{ route('admin.roles.index') }}" class="btn">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 