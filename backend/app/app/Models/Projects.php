<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Projects extends Model
{
    use HasFactory;

    const STATUS_Active = 'active';
    const STATUS_Deleted = 'deleted';
    const STATUS_Archived = 'archived';

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * Get the mapped users for the project
     */
    public function mapped_users(): HasMany
    {
        return $this->hasMany(ProjectMappedUsers::class);
    }
}
