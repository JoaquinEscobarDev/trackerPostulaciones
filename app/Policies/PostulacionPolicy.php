<?php

namespace App\Policies;

use App\Models\Postulacion;
use App\Models\User;

class PostulacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Postulacion $postulacion): bool
    {
        return $user->id === $postulacion->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Postulacion $postulacion): bool
    {
        return $user->id === $postulacion->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Postulacion $postulacion): bool
    {
        return $user->id === $postulacion->user_id;
    }
}
