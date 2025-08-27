<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.notifications', function ($user) {
    return $user && ($user->role === 'admin' || (method_exists($user, 'isAdmin') && $user->isAdmin()));
});