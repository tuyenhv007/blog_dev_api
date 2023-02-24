<?php

namespace App\Repository;

use App\Models\User;

class UserRepository extends BaseRepository implements UserInterface
{
    public function getModel()
    {
        return User::class;
    }


}
