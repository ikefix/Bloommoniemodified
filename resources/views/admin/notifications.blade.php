@extends('layouts.adminapp')

@section('admincontent')
    <h1>Notifications</h1>

    <h3><i class='bx bx-bell'></i> Unread Notifications</h3>
    @if($unreadNotifications->count())
        <ul>
            @foreach($unreadNotifications as $notification)
                <li class="unread-notification">
                    <strong>{{ $notification->data['message'] ?? 'No message' }}</strong><br>
                    <small>Product: {{ $notification->data['product_name'] ?? 'N/A' }}</small><br>
                    <span>Remaining Quantity: {{ $notification->data['stock_quantity'] }}</span><br>
                    <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="mark-read-btn">Mark as read</a>

                    <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-btn" onclick="return confirm('Delete this notification?')">Delete</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @else
        <p>No unread notifications.</p>
    @endif

    <hr>

    <h3><i class='bx bx-receipt'></i> Read Notifications</h3>
    @if($readNotifications->count())
        <ul>
            @foreach($readNotifications as $notification)
                <li class="read-notification">
                    <strong>{{ $notification->data['message'] ?? 'No message' }}</strong><br>
                    <small>Product: {{ $notification->data['product_name'] ?? 'N/A' }}</small><br>
                    <span>Remaining Quantity: {{ $notification->data['stock_quantity'] }}</span><br>

                    <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-btn" onclick="return confirm('Delete this notification?')">Delete</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @else
        <p>No read notifications yet.</p>
    @endif
@endsection
