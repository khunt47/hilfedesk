<p><b>Features API</b></p>
<ol>
    <li><del>Login</del></li>
    <li><del>Signup</del></li>
    <li><del>Create Project</del></li>
    <li><del>View Projects</del></li>
    <li><del>Create Ticket (tickets will have two IDs. One will be actual ID and the other one will be two letters of project and id (eg: GD-11) if project name is Geedesk). See if we can use uuid for ticket IDs</del></li>
    <li><del>View Ticket</del></li>
    <li><del>Take ticket</del></li>
    <li><del>Change ticket status</del></li>
    <li><del>Change ticket priority</del></li>
    <li><del>Create new comment without attachments</del></li>
    <li><del>View ticket comments</del></li>
    <li><del>Create customer API</del></li>
    <li><del>View customers</del></li>
    <li><del>Link tickets to customers (should be optional field)</del></li>
    <li><del>Ticket from email address in ticket</del></li>
    <li><del>End user login with OAUTH2.0 (oauth not required for this use case)</del></li>
    <li><del>End user auth with JWT</del></li>
    <li><del>Reassign ticket in ticket details (web app)</del></li>
    <li><del>Create customers in bulk</del></li>
    <li><font color="red">Time worked calculation in resolved ticket (have an option to make it record on its own and also enter manually like Jira)</font></li>
    <li><font color="red">Dashboard with nice graphs</font></li>
    <li><font color="red">Fix email not going issue
        <ol>
            <li>Signup email</li>
            <li>Confirmation email</li>
            <li>When a new ticket is assigned by someone to you</li>
            <li>When a new comment is added by others to ticket assigned to you</li>
            <li>Send email to client when their ticket is updated (new comment, status change, priority change)</li>
        </ol>
    </font></li>
    <li><del>Admin API</del>
        <ol>
            <li><del>Set timezone</li>
            <li><del>View set timezone</del></li>
            <li><del>Create new user</del></li>
            <li><del>View created users</del></li>
        </ol>
    </li>
    <li><del>Change ticket owner (can be implemented after user module)</del></li>
    <li><del>Change user password</del></li>
    <li><del>API Authentication with OAUTH/JWT</del></li>
    <li><del>Map users to projects</del></li>
    <li><del>Later features</del>
        <ol>
            <li><del>A new logo</del></li>
            <li><del>View project Tickets (with optional parameter or status, if status empty show all)</del></li>
            <li><del>All date and time should be based on timezone</del></li>
            <li><del>Merge tickets (Ref: https://support.zendesk.com/hc/en-us/articles/4408882445594-Merging-tickets)</del></li>
            <li><del>Related tickets</del></li>
            <li><del>Blocking tickets</del></li>
            <li><del>Single landing page website</del></li>
        </ol>
    </li>
    <li>Phase 2 features
        <ol>
            
            <li>Should we have this feature?
                <ol>
                    <li>Update existing comment</li>
                    <li>Delete existing comment</li>
                </ol>
            </li>
            
            <li><font color="red">Billing module including trial</font></li>
            <li>Create new users in bulk</li>
            <li>Implement pagination if more than 100 tickets</li>
            <li>Create new ticket with attachments</li>
            <li>Create new comment with attachments</li>
            <li>Create ticket from email</li>
            <li>Create ticket comment from email</li>
            <li>Custom ticket status names (the underlying status will be same but display names will differ)</li>
            <li>Detailed developer documentation</li>
            <li>Mark ticket as spam</li>
            <li>Ticket cc email addresses in ticket (more than one emails can be in cc)</li>
            <li>Create Escalation policy</li>
            <li>View Escalation policies</li>
            <li>View Escalation policy details</li>
            <li>Edit Escalation policy</li>
            <li>Turn on/off escalation policy</li>
            <li>Enforce escalation policy</li>
            <li>Email templates
                <ol>
                    <li>Create ticket email (to customer)</li>
                    <li>Create ticket email (to agents)</li>
                </ol>
            </li>
            <li>Ticket activity log</li>
            <li>Edit user</li>
        </ol>
    </li>
</ol>
