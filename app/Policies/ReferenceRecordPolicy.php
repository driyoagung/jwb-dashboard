<?php

namespace App\Policies;

use App\Models\ReferenceRecord;
use App\Models\User;

class ReferenceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ReferenceRecord $record): bool
    {
        return $record->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ReferenceRecord $record): bool
    {
        return $record->user_id === $user->id;
    }

    public function delete(User $user, ReferenceRecord $record): bool
    {
        return $record->user_id === $user->id;
    }
}
