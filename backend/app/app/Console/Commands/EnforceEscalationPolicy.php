<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tickets;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Libraries\SendGridEmail;


class EnforceEscalationPolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daysupport:escalate-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daysupport ticket escalation command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting ticket escalation process...');        

        $current_date_time = Carbon::now();
        $current_date_time_ts = strtotime($current_date_time);
        $ticket_statuses = [Tickets::STATUS_Resolved, Tickets::STATUS_Deleted];

        $unescalated_tickets_exist = Tickets::select('id')->where('escalated', Tickets::TICKET_NOT_ESCALATED)
        ->whereNotIn('status', $ticket_statuses)->exists();

        if ($unescalated_tickets_exist === 0) {
            exit();
        }

        $unescalated_tickets = Tickets::select('id', 'created_on', 'priority', 'heading', 'display_ticket_id')
        ->where('escalated', Tickets::TICKET_NOT_ESCALATED)
        ->whereNotIn('status', $ticket_statuses)->get();

        foreach ($unescalated_tickets as $unescalated_ticket) {
            $ticket_id = $unescalated_ticket->id;
            $display_ticket_id = $unescalated_ticket->display_ticket_id;
            $heading = $unescalated_ticket->heading;
            $ticket_priority = $unescalated_ticket->priority;
            $ticket_created_on = $unescalated_ticket->created_on;
            $ticket_created_on_ts = strtotime($ticket_created_on);
            $time_difference_ts = $current_date_time_ts - $ticket_created_on_ts;
            $time_difference = ($time_difference_ts % 60);
            $threshold_time = '';

            if ($ticket_priority === Tickets::PRIORITY_CRITICAL) {
                $threshold_time = env('CRITICAL_TICKETS');
            }
            elseif ($ticket_priority === Tickets::PRIORITY_HIGH) {
                $threshold_time = env('HIGH_TICKETS');
            }
            elseif ($ticket_priority === Tickets::PRIORITY_MEDIUM) {
                $threshold_time = env('MEDIUM_TICKETS');
            }
            elseif ($ticket_priority === Tickets::PRIORITY_LOW) {
                $threshold_time = env('LOW_TICKETS');
            }

            if ($time_difference >= $threshold_time) {               

                $update_details = array(
                    'escalated' => Tickets::TICKET_ESCALATED,
                    'escalated_on' => Carbon::now()
                );
                $ticket_escalated = DB::table('tickets')->where('id', $ticket_id)->update($update_details);

                $to = 'daysupportticketescalation@geedesk.com';
                $from = 'noreply@geedesk.com';
                $from_name = 'Daysupport';
                $to_name = 'Geedesk Team';

                $subject = 'Ticket escalation alert - '.$display_ticket_id;
                $body = '<html><head>Ticket Escalation Alert</head><body><p>Hello!</p><p>A ticket in Daysupport is escalated. Please find the snapshot of the ticket below.</p><p>Ticket: '.$heading.'<br>Ticket ID: '.$display_ticket_id.'<br></p><p>Thanks,<br>Daysupport</p></body></html>';

                $send_email = new SendGridEmail();
                $send_email->send_email($to, $to_name, $from, $from_name, $subject, $body);
            }
        }

        $this->info('Ticket escalation process completed.');
    }
}
