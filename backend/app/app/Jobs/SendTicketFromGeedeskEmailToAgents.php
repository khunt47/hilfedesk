<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Libraries\SendGridEmail;
use App\Models\ProjectMappedUsers;


class SendTicketFromGeedeskEmailToAgents implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $timeout = 45;

    protected $new_ticket_data_json;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($new_ticket_data_json)
    {
        $this->new_ticket_data_json = $new_ticket_data_json;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tos = [];
        $comment_details = [];
        $new_ticket_data_json = $this->new_ticket_data_json;
        if ($new_ticket_data_json !== '' && $new_ticket_data_json !== null) {
            $new_ticket_data = json_decode($new_ticket_data_json);
        }
        else {
            //add log here
            $error_message = 'Job from queue is empty';
            Log::error($error_message);
            //delete job
            return false;
        }

        if (empty($new_ticket_data) && count($new_ticket_data) < 0) {
            $error_message = 'Job from queue is empty';
            Log::error($error_message);
            //delete job
            return false;
        }

        $ticket_id = $new_ticket_data->ticket_id;
        $project_id = $new_ticket_data->project_id;
        $queue_email = $new_ticket_data->queue_email;

        /*
        Get mapped user emails
        */
        $mapped_users =DB::table('project_users_mapping AS pm')
                ->join('users', 'pm.user_id', '=', 'users.id')
                ->select('users.email', 'users.fname')
                ->where('pm.project_id', $project_id)
                ->get();

        foreach ($mapped_users as $value) {
            $user_fname = $value->fname;
            $user_email = $value->email;
            if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                /*
                Creating an array of all recepients in a format sendgrid understands. 
                The idea is not to send emails one by one and instead send emails to everyone at once.
                */
                $tos[$user_email] = $user_fname;
            }
        }

        if (count($tos) > 0) {
            $subject = 'New ticket alert [Ticket#'.$ticket_id.']' ;
            $body = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>DaySupport Ticket Created</title></head><body><p>Hi,</p><p>A new ticket has been created by customer via Geedesk console with ID - '.$ticket_id.'.</p><p>Thank you,<br>Geedesk Technologies.</p></body></html>';

            $from_name = env('APP_NAME');
            $from_email = $queue_email; 
            $sendgrid = new SendGridEmail();
            $sendgrid->send_emails($tos, $from_email, $from_name, $subject, $body);
        }
    }
}
