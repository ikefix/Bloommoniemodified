@extends('layouts.adminapp')

@section('admincontent')
<div class="container">
    <h3><i class='bx bx-dollar' ></i> Expenses List</h3>

    <a href="{{ route('expenses.create') }}" class="btn btn-primary mb-3">+ Add Expense</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-exp">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Added By</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $expense->title }}</td>
                        <td>₦{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}</td>
                        <td>{{ $expense->added_by }}</td>
                        <td>{{ $expense->description ?? '—' }}</td>
                        <td>
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Delete this expense?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No expenses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $expenses->links() }}
</div>
@endsection
