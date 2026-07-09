<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('tasks', function (User $user) {
    return $user->hasRole(['admin', 'employee']);
});

Broadcast::channel('purchases', function (User $user) {
    return $user->hasRole(['admin', 'employee']);
});

Broadcast::channel('school.{schoolId}', function (User $user, $schoolId) {
    return (int) $user->id === (int) $schoolId && $user->role === 'school';
});

Broadcast::channel('teacher.{teacherId}', function (User $user, $teacherId) {
    return (int) $user->id === (int) $teacherId;
});
