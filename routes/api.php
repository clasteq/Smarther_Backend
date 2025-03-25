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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
 

// Login

Route::post('login','App\Http\Controllers\ApiController@postUserLogin');

Route::post('logout','App\Http\Controllers\ApiController@userLogout'); 

// Student Functionalities

Route::post('profile_change_password','App\Http\Controllers\ApiController@postProfileChangePassword');

Route::post('change_password','App\Http\Controllers\ApiController@postChangePassword');

Route::post('forgot_password','App\Http\Controllers\ApiController@postForgotPassword');

Route::post('verify_otp','App\Http\Controllers\ApiController@otpVerification');

Route::post('resend_otp','App\Http\Controllers\ApiController@resendOtp');

Route::post('reset_password','App\Http\Controllers\ApiController@postResetPassword');

Route::post('getmobilescholars','App\Http\Controllers\ApiController@getMobileScholars');

Route::post('homecontents','App\Http\Controllers\ApiController@getHomeContents');

Route::post('circulars','App\Http\Controllers\ApiController@getCirculars');

Route::post('homeworks','App\Http\Controllers\ApiController@getHomeworks');

Route::post('homeworkswithdate','App\Http\Controllers\ApiController@getHomeworksDate');

Route::post('posthomeworkacknowledge','App\Http\Controllers\ApiController@acknowledgePostHomeworks');

Route::post('homework_submit','App\Http\Controllers\ApiController@postHomeworkSubmit');

Route::post('attendance','App\Http\Controllers\ApiController@getAttendance');

Route::post('present_days','App\Http\Controllers\ApiController@getPresentDays');

Route::post('calendar','App\Http\Controllers\ApiController@getCalendar');

Route::post('apply_leave','App\Http\Controllers\ApiController@postApplyLeave');

Route::post('applied_leave','App\Http\Controllers\ApiController@getAppliedLeaves');

Route::post('unapproved_leaves','App\Http\Controllers\ApiController@getUnApprovedLeaves');

Route::post('cancel_leave','App\Http\Controllers\ApiController@postCancelLeave');

Route::post('states','App\Http\Controllers\ApiController@getStates');

Route::post('cities','App\Http\Controllers\ApiController@getCities');

Route::post('profile_details','App\Http\Controllers\ApiController@getUserDetails');

Route::post('update_profile','App\Http\Controllers\ApiController@postUpdateProfile');

Route::post('update_profileimage','App\Http\Controllers\ApiController@postUpdateProfileImage');

Route::post('delete_profileimage','App\Http\Controllers\ApiController@postDeleteProfileImage');


Route::post('chapters','App\Http\Controllers\ApiController@getChapters');

Route::post('chapterstopics','App\Http\Controllers\ApiController@getChaptersTopics');

Route::post('topics','App\Http\Controllers\ApiController@getTopics');

Route::post('topicview','App\Http\Controllers\ApiController@getTopicView');

Route::post('homeworkview','App\Http\Controllers\ApiController@getHomework');


Route::post('subjectlist','App\Http\Controllers\ApiController@getSubjectList');

Route::post('termslist','App\Http\Controllers\ApiController@getTermsList');

Route::post('subjectbooklist','App\Http\Controllers\ApiController@getSubjectBookList');

Route::post('books','App\Http\Controllers\ApiController@getBook');

Route::post('booklist','App\Http\Controllers\ApiController@getBookList');

Route::post('testlist','App\Http\Controllers\ApiController@getTestList');

Route::post('testdetails','App\Http\Controllers\ApiController@getTestDetails');

Route::post('submittestdetails','App\Http\Controllers\ApiController@submitTestDetails');

Route::post('studenttestlist','App\Http\Controllers\ApiController@getStudentsTestList');

Route::post('newstudenttestlist','App\Http\Controllers\ApiController@newStudentTestList');

Route::post('studenttestresult','App\Http\Controllers\ApiController@getStudentsTestList');

Route::post('qblist','App\Http\Controllers\ApiController@getQbList');

Route::post('userqblist','App\Http\Controllers\ApiController@getUserQbList');

Route::post('qbsummary','App\Http\Controllers\ApiController@getQbSummary');

Route::post('generateselftest','App\Http\Controllers\ApiController@generateSelfTest');

Route::post('submitselftest','App\Http\Controllers\ApiController@submitSelfTest');

Route::post('examslist','App\Http\Controllers\ApiController@getExamsList');

Route::post('examdetails','App\Http\Controllers\ApiController@getExamsList');

Route::post('examtimetable','App\Http\Controllers\ApiController@getExamTimetable');

Route::post('newstudentresult','App\Http\Controllers\ApiController@getnewStudentResult');

Route::post('postCommunications','App\Http\Controllers\ApiController@getPostCommunications');

Route::post('postCommunicationacknowledge','App\Http\Controllers\ApiController@acknowledgePostCommunications');

Route::post('getscholarbatches','App\Http\Controllers\ApiController@getScholarBatches'); 

Route::post('getscholarfeespayments','App\Http\Controllers\ApiController@getScholarFeesPayments');

Route::post('getscholarfeestransactions','App\Http\Controllers\ApiController@getScholarFeesTransactions');

Route::post('getcontactslist','App\Http\Controllers\ApiController@getContactsList');

Route::post('getbankslist','App\Http\Controllers\ApiController@getBanksList');

Route::post('postsurveys','App\Http\Controllers\ApiController@getPostSurveys');

Route::post('postsurveyrespond','App\Http\Controllers\ApiController@respondPostSurvey');

Route::post('getgallerylist','App\Http\Controllers\ApiController@getGalleyList');

Route::post('getrewardslist','App\Http\Controllers\ApiController@getRewardsList');

// Admin panel apis

Route::group(['prefix' => 'admin'], function () {

    Route::post('login','App\Http\Controllers\ApiAdminController@postSchoolLogin');

    Route::post('home','App\Http\Controllers\ApiAdminController@getHomeContents');

    Route::post('getgsettings','App\Http\Controllers\ApiAdminController@getGeneralSettings');

    Route::post('savegsettings','App\Http\Controllers\ApiAdminController@postGeneralSettings');

    Route::post('gcontent','App\Http\Controllers\ApiAdminController@getContent');

    Route::post('savegcontent','App\Http\Controllers\ApiAdminController@postContent');

    //Contacts For
    Route::post('/contactsfor', 'App\Http\Controllers\ApiAdminController@getContactsFor');

    Route::post('/save/contactsfor', 'App\Http\Controllers\ApiAdminController@postContactsFor'); 

    //Contacts List
    Route::post('/contactslist', 'App\Http\Controllers\ApiAdminController@getContactsList');

    Route::post('/save/contactslist', 'App\Http\Controllers\ApiAdminController@postContactsList'); 

    //Master 

    //Classes
    Route::post('/mclasses', 'App\Http\Controllers\ApiAdminController@getClasses');

    Route::post('/save/mclasses', 'App\Http\Controllers\ApiAdminController@postClasses'); 

    //Sections
    Route::post('/msections', 'App\Http\Controllers\ApiAdminController@getSections');

    Route::post('/save/msections', 'App\Http\Controllers\ApiAdminController@postSections'); 

    //Subjects
    Route::post('/msubjects', 'App\Http\Controllers\ApiAdminController@getSubjects');

    Route::post('/save/msubjects', 'App\Http\Controllers\ApiAdminController@postSubjects'); 

    //Section Subject Mappings 
    Route::post('/section_subject_mappings', 'App\Http\Controllers\ApiAdminController@getSectionSubjectMappings'); 

    Route::post('/save/section_subject_mappings', 'App\Http\Controllers\ApiAdminController@postSectionSubjectMappings'); 

    //Class Timings
    Route::post('/class_timings', 'App\Http\Controllers\ApiAdminController@getClassTimings'); 

    Route::post('/save/class_timings', 'App\Http\Controllers\ApiAdminController@postClassTimings'); 

    //Circulars
    Route::post('/circulars', 'App\Http\Controllers\ApiAdminController@getCirculars'); 

    Route::post('/save/circulars', 'App\Http\Controllers\ApiAdminController@postCirculars'); 

    //Events
    Route::post('/events', 'App\Http\Controllers\ApiAdminController@getEvents'); 

    Route::post('/save/events', 'App\Http\Controllers\ApiAdminController@postEvents'); 

    // Holidays
    Route::post('/holidays', 'App\Http\Controllers\ApiAdminController@getHolidays');

    Route::post('/save/holidays', 'App\Http\Controllers\ApiAdminController@postHolidays'); 

    Route::post('/delete/holidays', 'App\Http\Controllers\ApiAdminController@postDeleteHolidays'); 

    // Time Table

    Route::post('/load/timetable', 'App\Http\Controllers\ApiAdminController@getTimetable'); 

    // Chapters

    Route::post('/chapters_list', 'App\Http\Controllers\ApiAdminController@getChaptersList'); 

    Route::post('/chapter_topics_list', 'App\Http\Controllers\ApiAdminController@getChapterTopicsList'); 

    Route::post('/books_list', 'App\Http\Controllers\ApiAdminController@getBooksList'); 

    //Background Themes
    Route::post('/bgthemes', 'App\Http\Controllers\ApiAdminController@getBgthemes');

    Route::post('/save/bgthemes', 'App\Http\Controllers\ApiAdminController@postBgthemes'); 

    //Communication Category
    Route::post('/categories', 'App\Http\Controllers\ApiAdminController@getCategories');

    Route::post('/save/categories', 'App\Http\Controllers\ApiAdminController@postCategories'); 

    //Communication Group
    Route::post('/groups', 'App\Http\Controllers\ApiAdminController@getGroups');

    Route::post('/save/groups', 'App\Http\Controllers\ApiAdminController@postGroups'); 

    //Communication POST
    Route::post('/commn_selects', 'App\Http\Controllers\ApiAdminController@getCommnSelects');

    Route::post('/commn_cc_staffs', 'App\Http\Controllers\ApiAdminController@getCommnCCStaffs');

    Route::post('/commn_staff_selects', 'App\Http\Controllers\ApiAdminController@getCommnStaffSelects');

    Route::post('/class_sections', 'App\Http\Controllers\ApiAdminController@getCommnClassSections');

    Route::post('/class_section_scholars', 'App\Http\Controllers\ApiAdminController@getCommnClassSectionScholars');

    Route::post('/commn_sms_templates', 'App\Http\Controllers\ApiAdminController@getCommnSMSTemplates');

    Route::post('/save/communication_post', 'App\Http\Controllers\ApiAdminController@postCommunicationPost'); 

    Route::post('/update/communication_post', 'App\Http\Controllers\ApiAdminController@updateCommunicationPost'); 

    Route::post('/save/communication_sms', 'App\Http\Controllers\ApiAdminController@postCommunicationSms'); 

    Route::post('/save/communication_post_staff', 'App\Http\Controllers\ApiAdminController@postCommunicationPostStaff'); 

    Route::post('/save/communication_hws', 'App\Http\Controllers\ApiAdminController@postCommunicationHws');  

    Route::post('/commn_post_list', 'App\Http\Controllers\ApiAdminController@getCommnPostList');

    Route::post('/commn_sms_list', 'App\Http\Controllers\ApiAdminController@getCommnSMSList');

    Route::post('/commn_hws_list', 'App\Http\Controllers\ApiAdminController@getCommnHWSList');

    Route::post('/commn_post_status_list', 'App\Http\Controllers\ApiAdminController@getCommnPostStatusList');

    Route::post('/commn_post_staff_list', 'App\Http\Controllers\ApiAdminController@getCommnPostStaffList');

    Route::post('/delete/communication_post', 'App\Http\Controllers\ApiAdminController@deleteCommunicationPost'); 

    Route::post('/delete/communication_post_staff', 'App\Http\Controllers\ApiAdminController@deleteCommunicationPostStaff'); 

    Route::post('/fetch_section', 'App\Http\Controllers\ApiAdminController@getClassSections');

    Route::post('/fetch_subject', 'App\Http\Controllers\ApiAdminController@getClassSecSubjects');

    // Survey

    Route::post('/commn_survey_list', 'App\Http\Controllers\ApiAdminController@getCommnSurveyList');

    Route::post('/save/communication_survey', 'App\Http\Controllers\ApiAdminController@postCommunicationSurvey'); 

    Route::post('/delete/communication_survey', 'App\Http\Controllers\ApiAdminController@deleteCommunicationSurvey');

    Route::post('/update/communication_survey', 'App\Http\Controllers\ApiAdminController@updateCommunicationSurvey');  

    // Scholar Remarks / Rewards List

    Route::post('/commn_remark_rewards_list', 'App\Http\Controllers\ApiAdminController@getCommnRemarkRewardsList');

    // Scholars
    Route::post('/scholars_list', 'App\Http\Controllers\ApiAdminController@getScholarsList'); 

    Route::post('/scholars_add', 'App\Http\Controllers\ApiAdminController@postScholarsadd');

    Route::post('/scholars_additional', 'App\Http\Controllers\ApiAdminController@postScholarsadditional');

    Route::post('/preadmin_scholars_list', 'App\Http\Controllers\ApiAdminController@getPreScholarsList');

    Route::post('/alumni_scholars_list', 'App\Http\Controllers\ApiAdminController@getAlumniScholarsList');

    Route::post('/scholars_details', 'App\Http\Controllers\ApiAdminController@getScholarsDetails');

    // Staffs
    Route::post('/staffs_list', 'App\Http\Controllers\ApiAdminController@getStaffsList');

    Route::post('/ctutors_list', 'App\Http\Controllers\ApiAdminController@getClassTutorsList');

    Route::post('/save/ctutors', 'App\Http\Controllers\ApiAdminController@postClassTutor');

    Route::post('/subject_staffs_list', 'App\Http\Controllers\ApiAdminController@getSubjectStaffsList');

    // Fees
    Route::post('/school_banks_list', 'App\Http\Controllers\ApiAdminController@getSchoolBanksList');

    Route::post('/waiver_category_list', 'App\Http\Controllers\ApiAdminController@getWaiverCategoryList');

    Route::post('/concession_category_list', 'App\Http\Controllers\ApiAdminController@getConcessionCategoryList');

    Route::post('/fee_cancel_reasons_list', 'App\Http\Controllers\ApiAdminController@getFeeCancelReasonsList');

    Route::post('/fee_payment_modes_list', 'App\Http\Controllers\ApiAdminController@getFeePaymentModesList');

    Route::post('/fee_terms_list', 'App\Http\Controllers\ApiAdminController@getFeeTermsList');

    Route::post('/fee_receipt_heads_list', 'App\Http\Controllers\ApiAdminController@getFeeReceiptHeadsList');

    Route::post('/fee_accounts_list', 'App\Http\Controllers\ApiAdminController@getFeeAccountsList');

    Route::post('/fee_category_list', 'App\Http\Controllers\ApiAdminController@getFeeCategoryList');

    Route::post('/fee_items_list', 'App\Http\Controllers\ApiAdminController@getFeeItemsList');

    Route::post('/fee_structure_list', 'App\Http\Controllers\ApiAdminController@getFeeStructuresList'); 

    // Attendance

    Route::post('/oa_student_attendance', 'App\Http\Controllers\ApiAdminController@getOAStudentAttendance');

    Route::post('/student_daily_attendance', 'App\Http\Controllers\ApiAdminController@getStudentDailyAttendance');

    Route::post('/save_student_daily_attendace','App\Http\Controllers\ApiAdminController@saveStudentDailyAttendance');

    Route::post('/student_leave_reports', 'App\Http\Controllers\ApiAdminController@getStudentLeaveReports');

    Route::post('/student_attendance_report', 'App\Http\Controllers\ApiAdminController@getStudentattendanceReports');

    Route::post('/students_leave_list', 'App\Http\Controllers\ApiAdminController@getStudentsLeaveList');

    // Staff Attendance

    Route::post('/staff_dailyattendance', 'App\Http\Controllers\ApiAdminController@getStaffDailyAttendance'); 

    Route::post('/staff_leavelist', 'App\Http\Controllers\ApiAdminController@getStaffLeaveList'); 

    Route::post('/staff_attendancerep', 'App\Http\Controllers\ApiAdminController@getStaffAttendanceReport'); 

    // User Roles 
    
    Route::post('/userroles_list',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getUserRoles']);
    
    Route::post('/save/userroles','App\Http\Controllers\ApiAdminController@postUserRoles'); 

    //Role Admin Users 
    
    Route::post('/roleusers_list',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getRoleUsers']);
    
    Route::post('/save/roleusers','App\Http\Controllers\ApiAdminController@postRoleUsers'); 

    Route::post('/save/roleusers_image','App\Http\Controllers\ApiAdminController@postRoleUsersProfileImage'); 

    //User Role Class Mapping
    
    Route::post('/role_class_mapping_list',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getRoleClassMappingList']);

    Route::post('/save/role_class_mapping','App\Http\Controllers\ApiAdminController@postRoleClassMapping');

    //  Role Module Mapping
    
    Route::post('/role_module_mapping',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getRoleModuleMappingList']);

    Route::post('/save/role_module_mapping','App\Http\Controllers\ApiAdminController@postRoleModuleMapping');

    //  Staff Module Mapping
    
    Route::post('/staff_module_mapping',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getStaffModuleMappingList']);

    Route::post('/save/staff_module_mapping','App\Http\Controllers\ApiAdminController@postStaffModuleMapping');

    // Gallery 

    Route::post('/gallery_list',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getGallery']);

    Route::post('/save/gallery_list','App\Http\Controllers\ApiAdminController@postGallery'); 

    // Examinations

    Route::post('/examinations',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getExaminations']);

    Route::post('/examination_settings',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getExaminationSettings']);

    Route::post('/exam_terms',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getExamTerms']);

    Route::post('/exam_results',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getExamResults']);

    Route::post('save/mark_entry',[ 'uses'=>'App\Http\Controllers\ApiAdminController@saveMarkEntry']);

    Route::post('/fetch_examinations',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getFetchExaminations']);

    // Fees

    Route::post('/fee_collection_report',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getFeeCollectionReport']);

    Route::post('/fee_pending_report',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getFeePendingReport']);

    Route::post('/fee_waiver_report',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getFeeWaiverReport']);

    Route::post('/fee_concession_report',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getFeeConcessionReport']);

    Route::post('/fee_overall_report',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getFeeOverallReport']);

    Route::post('/fees_receipts_cancelled_report',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getFeeReceiptsCancelledReport']);

    Route::post('/fees_receipts_report',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getFeeReceiptsReport']);

    Route::post('/fees_summary_report',[ 'uses'=>'App\Http\Controllers\ApiAdminController@getFeeSummaryReport']);
});