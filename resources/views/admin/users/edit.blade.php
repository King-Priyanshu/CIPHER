@extends('components.layouts.admin')

@section('page_title', 'Edit User')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card">
            <h3 class="text-xl font-bold text-navy mb-6">Edit User Details</h3>
            
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-navy mb-1.5">Name</label>
                    <input id="name" type="text" name="name" 
                           value="{{ old('name', $user->name) }}" 
                           required autofocus 
                           class="input-field">
                    @error('name')
                        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-navy mb-1.5">Email</label>
                    <input id="email" type="email" name="email" 
                           value="{{ old('email', $user->email) }}" 
                           required 
                           class="input-field">
                    @error('email')
                        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Selection -->
                <div class="mb-6">
                    <label for="role" class="block text-sm font-medium text-navy mb-1.5">Role</label>
                    <select id="role" name="role" class="input-field">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.users.index') }}" class="btn-ghost mr-3">
                        Cancel
                    </a>
                    
                    <button type="submit" class="btn-primary">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
