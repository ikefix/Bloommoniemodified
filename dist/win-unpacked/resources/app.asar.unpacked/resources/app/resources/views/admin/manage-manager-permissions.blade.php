@extends('layouts.adminapp')

@section('admincontent')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Grant or Revoke Product Access to Managers</h4>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.give-product-access') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-10">
                    <label for="manager_id" class="form-label">Select Manager</label>
                    <select name="manager_id" id="manager_id" class="form-select" required>
                        <option value="">-- Choose Manager --</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}">
                                {{ $manager->name }} ({{ $manager->email }})
                                @if($manager->shop) ({{ $manager->shop->name }}) @endif
                                @if(in_array($manager->id, $permissions)) - Already Has Access @endif
                            </option>                        
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">Grant Access</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Managers With Product Access</h5>
        </div>

        <div class="card-body">
            @if(count(array_intersect(array_column($managers->toArray(), 'id'), $permissions)) === 0)
                <p class="text-muted">No managers currently have access.</p>
            @else
                <ul class="list-group">
                    @foreach($managers as $manager)
                        @if(in_array($manager->id, $permissions))
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    {{ $manager->name }} ({{ $manager->email }}) ✅
                                </span>
                                <form action="{{ route('admin.revoke-product-access') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="manager_id" value="{{ $manager->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Revoke Access</button>
                                </form>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
