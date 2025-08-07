<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['logout'] = 'login/logout';
$route['login/social/google'] 	= 'login/google_login';
$route['login/social/google/redirect'] 	= 'login/google_login_redirect';
$route['login'] 	= 'login/index';


//Signup
$route['signup'] = 'signup/index';
$route['signup/confirm/(:any)'] = 'signup/confirm/$1';

//Customers
$route['customers'] 	= 'add-ons/customers/index';
$route['customers/create'] = 'add-ons/customers/create_customers';


//Contacts
$route['contacts'] 	= 'add-ons/contacts/index';


$route['my-profile'] 	= 'home/my_profile';
$route['my-profile/password/change'] 	= 'home/change_password';

//Home
$route['home'] = 'home/index';

//Tickets
$route['tickets/all'] = 'tickets/index';
$route['tickets/new'] 	= 'tickets/create';
$route['tickets/projects'] 	= 'tickets/projects';
$route['tickets/projects/new'] 	= 'tickets/create_project';
$route['projects/tickets/(:num)'] 	= 'tickets/project_tickets/$1';
$route['tickets/details/(:num)/(:num)'] = 'tickets/ticket_details/$1/$2';


//Repots
$route['reports'] = 'reports/index';


//Admin Core
$route['admin'] = 'admin/index';
$route['admin/projects'] = 'admin/projects';
$route['admin/users'] = 'admin/manage_users';
$route['admin/users/new'] = 'admin/create_user';
$route['admin/timezone'] = 'admin/timezone';


//Admin advanced
$route['admin/groups'] = 'groups/index';
$route['admin/groups/create'] = 'groups/create_group';
$route['admin/holidays'] = 'holidays/index';
$route['admin/holidays/create'] = 'holidays/create';
$route['admin/business-hours'] = 'businesshours/index';
$route['admin/business-hours/create'] = 'businesshours/create';
$route['admin/escalation-policies'] = 'escalationpolicies/index';
$route['admin/escalation-policies/create'] = 'escalationpolicies/create';

//Dashboard
$route['dashboard'] = 'dashboard/index';

//Calls
$route['calls'] = 'add-ons/calls/index';


$route['suggestions'] = 'suggestions/index';
$route['suggestions/create'] = 'suggestions/create_suggestion';

$route['default_controller'] 	= 'login';
$route['404_override'] 			= '';
$route['translate_uri_dashes'] 	= FALSE;

