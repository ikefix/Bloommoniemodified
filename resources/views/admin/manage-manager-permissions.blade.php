@extends('layouts.adminapp')

@section('admincontent')
<div class="container">
    <h2>Grant or Revoke Product Access to Managers</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.give-product-access') }}" method="POST">
        @csrf

        <label for="manager_id">Select Manager to Grant Access:</label>
        <select name="manager_id" required>
            <option value="">-- Choose Manager --</option>
            @foreach($managers as $manager)
                <option value="{{ $manager->id }}">
                    {{ $manager->name }} ({{ $manager->email }})
                    @if(in_array($manager->id, $permissions)) - Already Has Access @endif
                </option>
            @endforeach
        </select>

        <button type="submit">Grant Access</button>
    </form>

    <h4 class="mt-4">Managers With Access</h4>
    <ul>
        @foreach($managers as $manager)
            @if(in_array($manager->id, $permissions))
                <li>
                    {{ $manager->name }} ({{ $manager->email }}) ✅
                    <form action="{{ route('admin.revoke-product-access') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="manager_id" value="{{ $manager->id }}">
                        <button type="submit" class="btn btn-danger btn-sm">Revoke Access</button>
                    </form>
                </li>
            @endif
        @endforeach
    </ul>
</div>
@endsection
