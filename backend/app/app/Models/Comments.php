<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    use HasFactory;

    const USER_CREATED = 1; //by user
    const CUSTOMER_CREATED = 2; //by customer

    const STATUS_PUBLISHED = 'published';
    const STATUS_DELETED = 'deleted';

    const PUBLIC_YES = 'yes';
    const PUBLIC_NO = 'no';

    const ATTACHMENT_YES = 'yes';
    const ATTACHMENT_NO = 'no';

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comments';
}
