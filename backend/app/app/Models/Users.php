<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;

    const ADMIN_ROLE = 'admin';
    const MANAGER_ROLE = 'manager';
    const USER_ROLE = 'user';

    const STATUS_ACTIVE = 1;
    const STATUS_SUSPENDED = 2;
    const STATUS_DELETED = 3;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
}
