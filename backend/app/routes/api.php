<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('dummy', 'App\Http\Controllers\DummyController@index');


Route::prefix('v1')->group(function () {
    Route::group(['middleware' => 'cors'], function() {

        //Unauthenticated routes
        Route::prefix('/login')->group(function () {
            Route::post('auth', 'App\Http\Controllers\LoginController@auth_user');
            Route::prefix('/social')->group(function () {
                Route::post('google', 'App\Http\Controllers\LoginController@login_with_google');
            });
        });

        Route::prefix('/signup')->group(function () {
            Route::post('register', 'App\Http\Controllers\SignupController@register');
        });

        //Authenticated routes

        Route::group(['middleware' => 'validateusertoken'], function() {
            Route::group(['middleware' => 'setimezone'], function() {

                Route::post('/logout', 'App\Http\Controllers\LoginController@logout_user');

                //Logged user
                Route::prefix('users')->group(function () {
                    Route::get('my-profile', 'App\Http\Controllers\UserApiController@user_profile');
                    Route::post('password/change', 'App\Http\Controllers\UserApiController@change_password');
                });
                //Logged user

                //Projects
                Route::prefix('projects')->group(function () {
                    Route::get('/', 'App\Http\Controllers\ProjectController@get_projects');
                    Route::post('create', 'App\Http\Controllers\ProjectController@create_project');
                    Route::post('edit', 'App\Http\Controllers\ProjectController@edit_project');
                    Route::get('{id}', 'App\Http\Controllers\ProjectController@get_project');
                    Route::get('tickets/{id}', 'App\Http\Controllers\ProjectController@project_tickets');
                    Route::get('tickets/filter/{id}', 'App\Http\Controllers\ProjectController@project_filter_tickets');
                    Route::post('map-users', 'App\Http\Controllers\ProjectController@map_users');
                    Route::get('mapped-users/{id}', 'App\Http\Controllers\ProjectController@mapped_users');
                });
                //Projects

                //Tickets
                Route::prefix('/tickets')->group(function () {
                    Route::get('/', 'App\Http\Controllers\TicketsController@all_tickets');
                    Route::post('create', 'App\Http\Controllers\TicketsController@create_ticket');
                    Route::get('filter', 'App\Http\Controllers\TicketsController@filter_tickets');
                    Route::get('{id}', 'App\Http\Controllers\TicketsController@get_ticket');
                    Route::post('take', 'App\Http\Controllers\TicketsController@take_ticket');
                    Route::post('status/change', 'App\Http\Controllers\TicketsController@change_ticket_status');
                    Route::post('priority/change', 'App\Http\Controllers\TicketsController@change_ticket_priority');
                    Route::post('owner/change', 'App\Http\Controllers\TicketsController@change_ticket_owner');
                    Route::post('merge', 'App\Http\Controllers\TicketsController@merge_tickets');
                    Route::post('blocking-tickets/add', 'App\Http\Controllers\TicketsController@add_blocking_tickets');
                    Route::post('related-tickets/add', 'App\Http\Controllers\TicketsController@add_related_tickets');
                    Route::post('comments/add', 'App\Http\Controllers\TicketsController@new_ticket_comment');
                    Route::get('comments/{id}/{list_type}', 'App\Http\Controllers\TicketsController@ticket_comments');
                });
                //Tickets

                //Customer tickets
                Route::get('/customers/tickets/{id}', 'App\Http\Controllers\CustomerController@customer_tickets');

                //Customers
                Route::prefix('customers')->group(function () {
                    Route::get('/', 'App\Http\Controllers\CustomerController@get_customers');
                    Route::get('{id}', 'App\Http\Controllers\CustomerController@get_customer');
                    Route::post('create', 'App\Http\Controllers\CustomerController@create_customer');
                    Route::get('contacts/{id}', 'App\Http\Controllers\CustomerController@get_customer_contacts');
                });
                //Customers

                //Suggestions
                Route::prefix('suggestions')->group(function() {
                    Route::post('create', 'App\Http\Controllers\SuggestionsController@create_suggestion');
                    Route::get('/', 'App\Http\Controllers\SuggestionsController@get_suggestions');
                });
                //Suggestions

                //Reports
                Route::prefix('reports')->group(function() {
                    Route::get('ticket-metrics', 'App\Http\Controllers\ReportsController@get_ticket_metrics');
                    Route::get('agent-workload', 'App\Http\Controllers\ReportsController@get_agent_workload');
                    Route::get('ticket-trends', 'App\Http\Controllers\ReportsController@get_ticket_trends');
                });

                //Contacts
                Route::prefix('contacts')->group(function () {
                    Route::get('/', 'App\Http\Controllers\ContactsController@get_contacts');
                    Route::get('{id}', 'App\Http\Controllers\ContactsController@get_contact');
                    Route::post('create', 'App\Http\Controllers\ContactsController@create_contact');
                });
                //Contacts

                //Admin access
                Route::group(['middleware' => 'validateadminuser'], function() {

                    Route::prefix('/admin')->group(function () {
                        Route::get('all-timezones','App\Http\Controllers\AdminAPIController@get_all_timezones');
                        Route::get('timezone', 'App\Http\Controllers\AdminAPIController@get_timezone');
                        Route::post('timezone/update', 'App\Http\Controllers\AdminAPIController@update_timezone');
                        Route::get('users', 'App\Http\Controllers\AdminAPIController@users');
                        Route::post('users/create', 'App\Http\Controllers\AdminAPIController@create_user');
                        Route::post('users/create/bulk', 'App\Http\Controllers\AdminAPIController@create_users');
                        Route::post('users/update', 'App\Http\Controllers\AdminAPIController@update_user');
                        Route::post('users/delete', 'App\Http\Controllers\AdminAPIController@delete_user');
                        Route::post('users/change-password', 'App\Http\Controllers\AdminAPIController@change_user_password');

                        Route::prefix('holidays')->group(function () {
                            Route::get('/', 'App\Http\Controllers\HolidaysController@get');
                            Route::post('/create', 'App\Http\Controllers\HolidaysController@create');
                            Route::delete('/delete', 'App\Http\Controllers\HolidaysController@delete');
                        });

                        //Groups API
                        Route::prefix('groups')->group(function () {
                            Route::get('/', 'App\Http\Controllers\GroupsController@get');
                            Route::post('/create', 'App\Http\Controllers\GroupsController@create');
                            Route::get('/mapped-users/{id}', 'App\Http\Controllers\GroupsController@mapped_users');
                            Route::post('/map-users/{id}', 'App\Http\Controllers\GroupsController@map_users');
                        });


                        //Business hours
                        Route::prefix('business-hours')->group(function () {
                            Route::get('/', 'App\Http\Controllers\BusinessHoursController@get');
                            Route::post('/create', 'App\Http\Controllers\BusinessHoursController@create');
                            Route::delete('/delete', 'App\Http\Controllers\BusinessHoursController@delete');
                            Route::get('{id}', 'App\Http\Controllers\BusinessHoursController@details');
                            Route::post('/holiday-mapping', 'App\Http\Controllers\BusinessHoursController@holiday_mapping');

                        });

                    });
                });
                //Admin access

            });


        });

        /*
        Following APIs can be accessed from third party applications
        */
        Route::prefix('public')->group(function () {
            Route::group(['middleware' => 'validateapikey'], function() {
                Route::group(['middleware' => 'setimezone'], function() {

                    Route::prefix('/tickets')->group(function () {
                        Route::post('create', 'App\Http\Controllers\TicketsController@create_ticket');
                    });

                    Route::prefix('customers')->group(function () {
                        Route::get('{cust_id}/tickets', 'App\Http\Controllers\GeedeskCustomerTicketsController@get');
                        Route::get('{cust_id}/tickets/{ticket_id}', 'App\Http\Controllers\GeedeskCustomerTicketsController@show');
                    });
                });
            });
        });

        
        Route::group(['middleware' => 'newvalidateapikey'], function() {
            Route::group(['middleware' => 'setimezone'], function() {
                Route::prefix('customers')->group(function () {
                    Route::get('{cust_id}/tickets', 'App\Http\Controllers\GeedeskCustomerTicketsController@get');
                    Route::get('{cust_id}/tickets/{ticket_id}', 'App\Http\Controllers\GeedeskCustomerTicketsController@show');
                    Route::post('/tickets/create', 'App\Http\Controllers\GeedeskCustomerTicketsController@create');
                    Route::get('{cust_id}/closed-tickets', 'App\Http\Controllers\GeedeskCustomerTicketsController@get_closed_tickets');
                });
            });
        });

        /*
        Above APIs can be accessed from third party applications
        */
        
    });
});




