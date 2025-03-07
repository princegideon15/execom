<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
        if(session('logged_in') == 1){  
            return redirect('/home');
        }else{
            return view('auth.login');
        }
});

// login
Route::any('/doLogin', 'Auth\LoginController@doLogin');
Route::any('/otp/{email}', 'Auth\LoginController@otp')->name('otp');
Route::any('/verify_otp', 'Auth\LoginController@verify_otp');

// forgot password
Route::get('/forgot/password', function (){
    return view('auth.passwords.reset');
});

Route::get('/check/inbox', function (){
    return view('auth.passwords.confirm');
});

// logout
Route::any('/logout', 'Auth\LoginController@logout');

// reset password
Route::any('/reset/password', 'Auth\ResetPasswordController@verify');
Route::any('/reset-password/{email}', 'Auth\ResetPasswordController@otp')->name('reset-otp');
Route::any('/reset/otp', 'Auth\ResetPasswordController@verify_otp');

// activate account
Route::any('/send/activation', 'Auth\ResetPasswordController@mail');
Route::any('/activate/account/{id}','Auth\ResetPasswordController@activate');

// resend otp
Route::any('/resend-login-otp/', 'Auth\LoginController@resend');
Route::any('/resend-reset-otp/', 'Auth\ResetPasswordController@resend');

Auth::routes();

// dashboard
Route::get('/home', 'HomeController@index')->name('home');

// memis basic bar graph
Route::get('/memis/{val}', 'MemisController@index');
Route::get('/memis/basic/per_div', 'MemisController@basic_per_div');
Route::get('/memis/basic/per_reg', 'MemisController@basic_per_reg');
Route::get('/memis/basic/per_cat', 'MemisController@basic_per_cat');
Route::get('/memis/basic/per_stat', 'MemisController@basic_per_stat');
Route::get('/memis/basic/per_sex', 'MemisController@basic_per_sex');
Route::get('/get_disc', 'MemisController@get_discrepancies'); 
Route::get('/get_disc_mem', 'MemisController@get_disc_mem'); 


// bris basic graph
Route::get('/bris/basic/per_proj', 'BrisController@basic_per_proj');
Route::get('/bris/basic/per_nibr', 'BrisController@basic_per_nibr');
Route::get('/bris/basic/per_prior', 'BrisController@basic_per_prior');
Route::get('/bris/basic/per_prog', 'BrisController@basic_per_prog');

// ejournal
Route::get('/ej/jby', 'EjournalController@journals_by_year');
Route::get('/ej/abj', 'EjournalController@articles_by_journal');
Route::get('/ej/pdf', 'EjournalController@pdf_downloads_by_journal');
Route::get('/ej/abs', 'EjournalController@abstract_views_by_journal');
Route::get('/ej/cbj', 'EjournalController@citations_by_journal');
Route::post('/ej/vby', 'EjournalController@visitors_by_year');

Route::get('/ej/pa', 'EjournalController@published_articles');
Route::get('/ej/ca', 'EjournalController@cited_articles');
Route::get('/ej/va', 'EjournalController@viewed_articles');
Route::get('/ej/da', 'EjournalController@downloaded_articles');
Route::get('/ej/mst', 'EjournalController@searched_topics');
Route::get('/ej/mtc', 'EjournalController@most_clients');
Route::get('/ej/cte', 'EjournalController@most_citees');
Route::get('/ej/vo', 'EjournalController@visitors_origin');

// lms
Route::get('/lms/abc', 'LmsController@articles_by_categories');
Route::get('/lms/abv', 'LmsController@articles_by_views');
Route::get('/lms/abd', 'LmsController@articles_by_downloads');
Route::post('/lms/cat', 'LmsController@categories');
Route::get('/lms/download_file/{id}', 'LmsController@download_file');
Route::get('/lms/view_pdf/{id}', 'LmsController@view_pdf');


// bris
Route::get('/bris/rt', 'BrisController@research_type');
Route::get('/bris/ps', 'BrisController@project_status');
Route::get('/bris/hnrda', 'BrisController@hnrda');
Route::get('/bris/prexc', 'BrisController@prexc');
Route::get('/bris/pag', 'BrisController@pag');
Route::get('/bris/dost', 'BrisController@dost');
Route::get('/bris/strat', 'BrisController@strat');
Route::get('/bris/pdp', 'BrisController@pdp');
Route::get('/bris/nibra', 'BrisController@nibra');
Route::get('/bris/nsub', 'BrisController@nsub');
Route::get('/bris/nsea', 'BrisController@nsea');
Route::get('/bris/snt', 'BrisController@snt');
Route::get('/bris/sdg', 'BrisController@sdg');
Route::post('/bris/gps', 'BrisController@get_project_status');
Route::post('/bris/gbi', 'BrisController@get_nibra_by_id');
Route::post('/bris/age', 'BrisController@get_dost_agenda_by_id');
Route::post('/bris/prg', 'BrisController@get_program_status');
Route::get('/bris/coor', 'BrisController@get_coordinator');

// memis
Route::post('/memis/div', 'MemisController@per_division');
Route::post('/memis/reg', 'MemisController@per_region');
Route::post('/memis/cat', 'MemisController@per_category');
Route::post('/memis/stat', 'MemisController@per_status');
Route::post('/memis/sex', 'MemisController@per_sex');
Route::post('/memis/all', 'MemisController@all_members');
Route::post('/memis/awa', 'MemisController@get_awards');
Route::post('/memis/gb', 'MemisController@get_gb');
Route::post('/memis/pp', 'MemisController@get_province');
Route::get('/memis_divs', 'MemisController@get_all_division');

// thesis and disertation
Route::get('/thds/cat/{id}', 'ThdsController@get_thds');
Route::get('/thds/action/{id}', 'ThdsController@get_action_thds');

// rdlip
Route::get('/rdlip/grant/{id}', 'GrantController@get_grant');

// memis csf and graphs
Route::get('/csf_quest', 'MemisController@get_questions');
Route::get('/csf_list_memis', 'MemisController@get_csf_list');
Route::get('/csf_memis_desc/{id}/{user}', 'MemisController@get_csf_desc');
Route::get('/csf_memis_answers/{user}', 'MemisController@get_csf_answers');
Route::get('/memis/basic/csf/{chart}', 'MemisController@get_csf');

// bris csf and graphs
Route::get('/csf_list_bris', 'BrisController@get_csf_list');
Route::get('/csf_bris_desc/{id}/{user}', 'BrisController@get_csf_desc');
Route::get('/csf_bris_answers/{user}', 'BrisController@get_csf_answers');
Route::get('/bris/basic/csf/{chart}', 'BrisController@get_csf');

// ej csf and graphs
Route::get('/csf_list_ej', 'EjournalController@get_csf_list');
Route::get('/csf_ej_desc/{id}/{user}', 'EjournalController@get_csf_desc');
Route::get('/csf_ej_answers/{user}', 'EjournalController@get_csf_answers');
Route::get('/ej/basic/csf/{chart}', 'EjournalController@get_csf');

// lms csf and graphs
Route::get('/csf_list_lms', 'LmsController@get_csf_list');
Route::get('/csf_lms_desc/{id}/{user}', 'LmsController@get_csf_desc');
Route::get('/csf_lms_answers/{user}', 'LmsController@get_csf_answers');
Route::get('/lms/basic/csf/{chart}', 'LmsController@get_csf');

// lms csf and graphs
Route::get('/csf_list_thds', 'ThdsController@get_csf_list');
Route::get('/csf_thds_desc/{id}/{user}', 'ThdsController@get_csf_desc');
Route::get('/csf_thds_answers/{user}', 'ThdsController@get_csf_answers');
Route::get('/thds/basic/csf/{chart}', 'ThdsController@get_csf');

// rdlip csf and graphs
Route::get('/csf_list_rdlip', 'GrantController@get_csf_list');
Route::get('/csf_rdlip_desc/{id}/{user}', 'GrantController@get_csf_desc');
Route::get('/csf_rdlip_answers/{user}', 'GrantController@get_csf_answers');
Route::get('/rdlip/basic/csf/{chart}', 'GrantController@get_csf');

// overall csf graph
Route::get('/skms/csf/{chart}', 'FeedbackController@get_overall_csf_graph');


// memis graph
Route::post('/memis/bar_graph', 'MemisController@do_bar_graph');
// Route::any('/graph/stack_graph', 'MemisController@do_stack_graph'); for testing
Route::any('/memis/stack_graph', 'MemisController@do_stack_graph');
Route::any('/memis/column_graph', 'MemisController@do_column_graph');
Route::any('/memis/stack_column_graph', 'MemisController@do_column_graph');
Route::any('/memis/advance_stack_column_graph', 'MemisController@do_advance_stack_column_graph');
Route::post('/memis/drilldown/region', 'MemisController@drilldown_region');
Route::post('/memis/line_graph', 'MemisController@do_line_graph');

// memis graph specific
Route::post('/memis/bar_graph_by_id', 'MemisController@do_bar_graph_by_id');

// nrcpnet
Route::post('/nrcpnet/plant', 'NrcpnetController@get_plant');
Route::post('/nrcpnet/cont', 'NrcpnetController@get_cont');
Route::post('/nrcpnet/jo', 'NrcpnetController@get_jo');
Route::post('/nrcpnet/vac', 'NrcpnetController@get_vac');
Route::get('/nrcpnet/divs', 'NrcpnetController@get_divs');

// skms
Route::get('/csf_list_skms', 'HomeController@get_csf_list');

// search
Route::post('/search', 'SearchController@search');
Route::post('/search/overall/memis/spec', 'SearchController@search_overall_memis');
Route::post('/search/overall/memis/memb', 'SearchController@search_overall_memis');
Route::post('/search/overall/memis/awa', 'SearchController@search_overall_memis');
Route::post('/search/overall/memis/gb', 'SearchController@search_overall_memis');
Route::post('/search/overall/bris', 'SearchController@search_overall_bris');
Route::post('/search/overall/ejournal', 'SearchController@search_overall_ejournal');
Route::post('/search/overall/lms', 'SearchController@search_overall_lms');
Route::post('/search/overall/nrcpnet', 'SearchController@search_overall_nrcpnet');
Route::get('/search/reg', 'SearchController@get_region');
Route::post('/search/prov', 'SearchController@get_province');
Route::post('/search/city', 'SearchController@get_city');
Route::get('/search/divs',  'SearchController@get_divisions');
// Route::post('/search/brgy', 'SearchController@get_brgy');

// users
Route::get('/skms_users', 'MemisController@get_users');
Route::get('/execom_users', 'HomeController@get_users');
Route::get('/execom/get/{id}','HomeController@get_users');
Route::post('/execom/edit', 'HomeController@edit_user');
Route::post('/execom/update', 'HomeController@update_user');
Route::post('/execom/add', 'HomeController@add_user');
Route::post('/execom/remove', 'HomeController@remove_user');
Route::post('/execom/create', 'HomeController@create_user');
Route::get('/execom/logs', 'HomeController@activity_logs');

// ui/ux feedbacks of ExeCom IS
Route::post('/submit_feedback', 'FeedbackController@store');
Route::get('/verify_feedback', 'FeedbackController@verify');
Route::get('/feedbacks_chart', 'FeedbackController@show');
Route::get('/feedbacks', 'FeedbackController@all');
Route::post('/update_feedbacks', 'FeedbackController@update');

// backup and import
Route::post('/backup/export', 'BackupController@export');
Route::post('/backup/import', 'BackupController@import');
Route::get('/backup/export_logs', 'BackupController@export_logs');
Route::get('/backup/clear_with_export_logs', 'BackupController@clear_with_export_logs');

// save logs
Route::post('/save_logs', 'HomeController@save_logs');










// tests

Route::get('/send-mail2','HomeController@send_email_test');

Route::get('/send-mail', function () {

    $email_data = [ 'name' => 'Gerard Balde',
        'email' => 'test@mail.com', 
        'password' => 'test pass'];

    return new \App\Mail\AccountCreated($email_data);
});