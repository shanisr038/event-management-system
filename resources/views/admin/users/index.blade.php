@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1>
            <i class="bi bi-people"></i> Manage Users
        </h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-repeat"></i> Refresh
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Current Roles</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-info">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'organizer' ? 'warning' : 'info') }}">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                        
                                        @if($user->roles->isEmpty())
                                            <span class="badge bg-secondary">No role</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="btn btn-outline-primary"
                                               title="Edit Roles">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            
                                            @if(!$user->hasRole('organizer'))
                                                <form action="{{ route('admin.users.make-organizer', $user) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Make {{ $user->name }} an organizer?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Make Organizer">
                                                        <i class="bi bi-person-plus"></i> Make Organizer
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.remove-organizer', $user) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Remove organizer role from {{ $user->name }}?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger" title="Remove Organizer">
                                                        <i class="bi bi-person-dash"></i> Remove Organizer
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if(!$user->hasRole('admin') && $user->id !== auth()->id())
                                                <form action="{{ route('admin.users.make-admin', $user) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Make {{ $user->name }} an admin?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger" title="Make Admin">
                                                        <i class="bi bi-shield"></i> Make Admin
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection