<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tickets extends Model
{
    use HasFactory;

    const STATUS_ALL = 'all';
    const STATUS_New = 'new';
    const STATUS_Inprogress = 'inprogress';
    const STATUS_Onhold = 'onhold';
    const STATUS_Resolved = 'resolved';
    const STATUS_Deleted = 'deleted';
    const STATUS_Merged = 'merged';

    const PRIORITY_ALL = 'all';
    const PRIORITY_CRITICAL = 'critical';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_LOW = 'low';

    const USER_CREATED = 1; //by user
    const CUSTOMER_CREATED = 2; //by customer

    const ATTACHMENT_YES = 1;
    const ATTACHMENT_NO = 0;

    const TICKET_NOT_ESCALATED = 0;
    const TICKET_ESCALATED = 1;


    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tickets';

    /**
     * Get the comments for the ticket.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class);
    }

    /**
     * Get the blocking tickets for the ticket.
     */
    public function blocking_tickets(): HasMany
    {
        return $this->hasMany(BlockingTickets::class);
    }

    /**
     * Get the related tickets for the ticket.
     */
    public function related_tickets(): HasMany
    {
        return $this->hasMany(RelatedTickets::class);
    }

}

