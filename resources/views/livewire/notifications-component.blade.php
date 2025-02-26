<div>
    <h2 class="text-lg font-bold mb-2">Notifications</h2>
    <ul>
        @foreach ($notifications as $notification)
            <li class="border-b py-2 flex justify-between items-center">
                <span>{{ $notification['data']['message'] }}</span>
                <button wire:click="markAsRead('{{ $notification->id }}')" class="text-blue-500">Mark as read</button>
            </li>
        @endforeach
    </ul>
</div>
