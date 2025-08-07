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
use Illuminate\Support\Facades\Cache;
use App\Libraries\SendGridEmail;
use App\Libraries\GeedeskEmailAPI;
use App\Models\Tickets;
use App\Models\Projects;
use App\Models\Comments;


class SendCommentCreationAlertToRequestor implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public $timeout = 45;

    protected $requestor_data_json;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($requestor_data_json)
    {
        $this->requestor_data_json = $requestor_data_json;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $requestor_data_json = $this->requestor_data_json;
        if ($requestor_data_json !== '' && $requestor_data_json !== null) {
            $requestor_data = json_decode($requestor_data_json);
        }
        else {
            //add log here
            $error_message = 'Job from queue is empty';
            Log::channel('daysupport_email_parser')->error($error_message);
            //delete job
            return false;
        }

        $ticket_id = $requestor_data->ticket_id;
        $to_email = $requestor_data->to_email; //the person who sent the email to create ticket
        $to_name = $requestor_data->to_name;
        $new_comment = $requestor_data->new_comment;
        $company_id = $requestor_data->company_id;
        $attachment_present = $requestor_data->attachment_present;
        if ($attachment_present === Comments::ATTACHMENT_YES) {
            $attachment_path = env('LINODE_BUCKET_URL').'/'.$requestor_data->attachment;
        }
        else {
            $attachment_path = null;
        }
        
        $ticket = Tickets::select('heading', 'project_id')->where('id', $ticket_id)->where('company_id', $company_id)->first();
        $ticket_heading = $ticket->heading;
        $project_id = $ticket->project_id;

        

        $from = env('MAIL_FROM_ADDRESS');
        $project = Projects::select('email')->where('id', $project_id)->where('company_id', $company_id)->first();
        $reply_to = $project->email;
        $from_name = env('MAIL_FROM_NAME');
        $subject = $ticket_heading.' [Ticket#'.$ticket_id.']' ;
        $body = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>DaySupport Ticket Created</title></head><body><p>Hi</p><p>New comment for ticket ID - '.$ticket_id.'.</p><p><b>New comment:</b></p>'.$new_comment.'<br><p>Thank you,<br> Geedesk Technologies.</p></body></html>';

        $email = new GeedeskEmailAPI();
        if ($attachment_present === Comments::ATTACHMENT_YES) {
            $x = $email->send_attachment_email($to_email, $to_name, $from, $from_name, $subject, $body, $attachment_path, $reply_to);
        }
        else {
            $x = $email->send_email($to_email, $to_name, $from, $from_name, $subject, $body, $reply_to);
        }
        Log::info($x);
    }
}
