<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCommentFiles extends Model
{
    use HasFactory;

    const COMMENT = 1;
    const TICKET = 2;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ticket_files';
}
