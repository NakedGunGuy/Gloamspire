<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationsComponent extends Component
{
    public $notifications = [];

    protected $listeners = ['echo:notifications,OrderStatusChanged' => 'fetchNotifications'];

    public function mount()
    {
        $this->fetchNotifications();
    }

    public function fetchNotifications()
    {
        $this->notifications = Auth::user()->unreadNotifications;
    }

    public function markAsRead($notificationId)
    {
        Auth::user()->notifications()->where('id', $notificationId)->update(['read_at' => now()]);
        $this->fetchNotifications();
    }

    public function render()
    {
        return view('livewire.notifications-component', [
            'notifications' => $this->notifications
        ]);
    }
}
