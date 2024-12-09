<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ __('Roles & Permissions Management') }}
            </h2>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                {{ __('New Role') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 shadow-xl rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <!-- head -->
                        <thead class="bg-base-200 text-base-content">
                            <tr>
                                <th class="bg-base-200">Role</th>
                                <th class="bg-base-200">Permissions</th>
                                <th class="bg-base-200 w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr class="hover">
                                <td class="font-medium">
                                    <div class="flex items-center gap-2">
                                        <span class="badge badge-neutral">{{ $role->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions as $permission)
                                            <span class="badge badge-ghost badge-sm">
                                                {{ str_replace('_', ' ', $permission->name) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.roles.edit', $role) }}"
                                           class="btn btn-ghost btn-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        @unless($role->name === 'superadmin')
                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this role?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-sm text-error">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
