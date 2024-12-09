<x-app-layout>
    <div class="container mx-auto mt-8">
        <div class="card shadow-md bg-white p-6">
            <h2 class="text-2xl font-bold mb-4">Assegna Permessi a un Utente</h2>
            <form action="{{ route('admin.assign.permissions') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Utente</label>
                    <input type="email" name="email" id="email"
                           class="input input-bordered w-full mt-1"
                           placeholder="Inserisci l'indirizzo email" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Permessi</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($permissions as $permission)
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox"
                                       name="permissions[]"
                                       value="{{ $permission->name }}"
                                       class="checkbox checkbox-primary" />
                                <span class="label-text">{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('permissions')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-full">
                    Assegna Permessi
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
