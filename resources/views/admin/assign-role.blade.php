<x-app-layout>
<div class="container mx-auto mt-8">
    <div class="card shadow-md bg-white p-6">
        <h2 class="text-2xl font-bold mb-4">Assegna Ruolo a un Utente</h2>
        <form action="{{ route('admin.assign.role') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email Utente</label>
                <input type="email" name="email" id="email"
                       class="input input-bordered w-full mt-1"
                       placeholder="Inserisci l'indirizzo email" required>
            </div>
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">Ruolo</label>
                <select name="role" id="role"
                        class="select select-bordered w-full mt-1">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-full">
                Assegna Ruolo
            </button>
        </form>
    </div>
</div>
</x-app-layout>
