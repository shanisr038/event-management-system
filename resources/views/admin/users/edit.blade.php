@extends('layouts.app')

@section('title', 'Edit User Roles')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-pencil"></i> Edit Roles for: {{ $user->name }}
                </h5>
            </div>
            
            <div class="card-body">
                <div class="mb-4">
                    <h6>User Information:</h6>
                    <p>
                        <strong>Name:</strong> {{ $user->name }}<br>
                        <strong>Email:</strong> {{ $user->email }}<br>
                        <strong>Current Roles:</strong>
                        @foreach($user->roles as $role)
                            <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'organizer' ? 'warning' : 'info') }}">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </p>
                </div>
                
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Roles</label>
                        @foreach($roles as $role)
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="roles[]" 
                                       value="{{ $role->name }}"
                                       id="role_{{ $role->id }}"
                                       {{ in_array($role->name, $userRoles) ? 'checked' : '' }}
                                       {{ $role->name === 'admin' && $user->id === auth()->id() ? 'disabled' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'organizer' ? 'warning' : 'info') }}">
                                        {{ $role->name }}
                                    </span>
                                    @if($role->name === 'admin' && $user->id === auth()->id())
                                        <small class="text-muted">(You cannot remove your own admin role)</small>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                        
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Select multiple roles as needed.
                        </small>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Roles
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection