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
    return view('welcome');
});
Route::get('/', 'App\Http\Controllers\AdminController@index');

Route::get('/admin', 'App\Http\Controllers\AdminController@index');

Route::get('/terms', 'App\Http\Controllers\AdminController@termsconditions');

Route::get('/aboutus', 'App\Http\Controllers\AdminController@aboutus');

Route::get('/policy', 'App\Http\Controllers\AdminController@policy');

Route::get('/{slugname}/page/terms', 'App\Http\Controllers\AdminController@termsconditions');

Route::get('/{slugname}/page/aboutus', 'App\Http\Controllers\AdminController@aboutus');

Route::get('/{slugname}/page/policy', 'App\Http\Controllers\AdminController@policy');

Route::get('/{slugname}/page/privacypolicy', 'App\Http\Controllers\AdminController@policy');

Route::get('/delete_account','App\Http\Controllers\AdminController@deleteAccountUrl');

Route::group(['prefix' => '/admin'], function () {

    Route::get('/', 'App\Http\Controllers\AdminController@index');

    Route::get('/login', 'App\Http\Controllers\AdminController@index');

    Route::post('/login', 'App\Http\Controllers\AdminController@postLogin');

    Route::get('/forgotpwd','App\Http\Controllers\AdminController@viewForgotPwd');

    Route::post('/forgotpwd','App\Http\Controllers\AdminController@putForgotPwd');

    Route::get('/resetpwd','App\Http\Controllers\AdminController@viewResetPwd');

    Route::post('/resetpwd','App\Http\Controllers\AdminController@putResetPwd');

    

    Route::group(['middleware' => 'preventBackHistory'],function(){

        Route::get('/homechk', 'App\Http\Controllers\AdminController@homePageChk');

        Route::get('/profile', 'App\Http\Controllers\AdminController@viewSchoolProfile');

        Route::get('/notifychk', 'App\Http\Controllers\NotifyController@sendPushNotification');

        Route::get('/home', 'App\Http\Controllers\AdminController@homePage');

        Route::post('/load/homeworkstatus', 'App\Http\Controllers\AdminController@loadHomeworkStatus');

        Route::post('/load/attendancestatus', 'App\Http\Controllers\AdminController@loadAttendanceStatus');

        Route::post('/load/modulestatus', 'App\Http\Controllers\AdminController@loadModuleStatus');

        Route::post('/load/moduleupdatedstatus', 'App\Http\Controllers\AdminController@loadModuleUpdatedStatus');

        Route::get('/logout', 'App\Http\Controllers\AdminController@logout'); 

        Route::post('/checkSetAdminCountry', 'App\Http\Controllers\AdminController@checkSetAdminCountry'); 
        
        Route::get('/changepwd','App\Http\Controllers\AdminController@changePassword');

        Route::post('/change_password','App\Http\Controllers\AdminController@updatePassword');

        Route::get('/users/profile','App\Http\Controllers\AdminController@viewProfile'); 

        Route::post('/fetch-notifications','App\Http\Controllers\AdminController@fetchNotifications');

        Route::get('/notifications','App\Http\Controllers\AdminController@viewNotifications');

        // Admin Settings

        Route::get('/generalsettings', 'App\Http\Controllers\AdminController@adminsettings');

        Route::post('/save/adminsettings', 'App\Http\Controllers\AdminController@saveAdminSettings');

        // Settings

        Route::get('/settings', 'App\Http\Controllers\AdminController@settings');

        Route::post('/save/settings', 'App\Http\Controllers\AdminController@saveSettings');

        Route::get('/termscond', 'App\Http\Controllers\AdminController@termscond');

        Route::post('/save/termscond', 'App\Http\Controllers\AdminController@saveTermsCond');

        Route::get('/about', 'App\Http\Controllers\AdminController@about');

        Route::post('/save/about', 'App\Http\Controllers\AdminController@saveAbout');

        Route::get('/privacypolicy', 'App\Http\Controllers\AdminController@privacypolicy');

        Route::post('/save/privacypolicy', 'App\Http\Controllers\AdminController@savePrivacypolicy');

        // FAQ

        Route::get('/faq', 'App\Http\Controllers\AdminController@viewFAQ');

        Route::get('/faq/datatables', ['as' => 'faqs.data', 'uses' => 'App\Http\Controllers\AdminController@getFAQ']);

        Route::post('/save/faq', 'App\Http\Controllers\AdminController@postFAQ');

        Route::post('/edit/faq', 'App\Http\Controllers\AdminController@editFAQ');

        // SMS Templates

        Route::get('/smstemplates', 'App\Http\Controllers\AdminController@viewSMSTemplates');

        Route::get('/smstemplates/datatables', ['as' => 'smstemplates.data', 'uses' => 'App\Http\Controllers\AdminController@getSMSTemplates']);

        Route::post('/save/smstemplates', 'App\Http\Controllers\AdminController@postSMSTemplates');

        Route::post('/edit/smstemplates', 'App\Http\Controllers\AdminController@editSMSTemplates');

        Route::post('/delete/smstemplates', 'App\Http\Controllers\AdminController@deleteSMSTemplates');

        //Countries

        Route::get('/countries', 'App\Http\Controllers\AdminController@viewCountries');

        Route::get('/countries/datatables', ['as' => 'countries.data', 'uses' => 'App\Http\Controllers\AdminController@getCountries']);

        Route::post('/save/countries', 'App\Http\Controllers\AdminController@postCountries');

        Route::post('/edit/countries', 'App\Http\Controllers\AdminController@editCountries');

        //States

        Route::get('/states', 'App\Http\Controllers\AdminController@viewStates');

        Route::get('/states/datatables', ['as' => 'states.data', 'uses' => 'App\Http\Controllers\AdminController@getStates']);

        Route::post('/save/states', 'App\Http\Controllers\AdminController@postStates');

        Route::post('/edit/states', 'App\Http\Controllers\AdminController@editStates');

        //Districts

        Route::get('/districts', 'App\Http\Controllers\AdminController@viewDistricts');

        Route::get('/districts/datatables', ['as' => 'districts.data', 'uses' => 'App\Http\Controllers\AdminController@getDistricts']);

        Route::post('/save/districts', 'App\Http\Controllers\AdminController@postDistricts');

        Route::post('/edit/districts', 'App\Http\Controllers\AdminController@editDistricts');

        Route::post('/getstate/districts', 'App\Http\Controllers\AdminController@getStateDistricts');

        //Schools
        Route::get('/schools', 'App\Http\Controllers\InstituteController@viewSchools');

        Route::get('/schools/datatables', ['as' => 'schools.data', 'uses' => 'App\Http\Controllers\InstituteController@getSchools']);

        Route::post('/save/schools', 'App\Http\Controllers\InstituteController@postSchools');

        Route::post('/edit/schools', 'App\Http\Controllers\InstituteController@editSchools');

        Route::post('/delete/schools', 'App\Http\Controllers\InstituteController@deleteSchools');

        Route::post('/loginschool', 'App\Http\Controllers\AdminController@loginSchools');

        //Master Classes
        Route::get('/mclasses', 'App\Http\Controllers\AdminController@viewMasterClasses');

        Route::post('/save/mclasses', 'App\Http\Controllers\AdminController@postMasterClasses');

        Route::get('/mclasses/datatables', ['as' => 'mclasses.data', 'uses' => 'App\Http\Controllers\AdminController@getMasterClasses']);

        Route::post('/edit/mclasses', 'App\Http\Controllers\AdminController@editMasterClasses');

        // Departments
        Route::get('/departments', 'App\Http\Controllers\AdminController@viewDepartments');

        Route::post('/save/departments', 'App\Http\Controllers\AdminController@postDepartments');

        Route::get('/departments/datatables', ['as' => 'departments.data','uses' => 'App\Http\Controllers\AdminController@getDepartments']);

        Route::post('/edit/departments', 'App\Http\Controllers\AdminController@editDepartments');

        // Blood Groups

        Route::get('/bloodgroups', 'App\Http\Controllers\AdminController@viewBloodgroups');

        Route::get('/bloodgroups/datatables', ['as' => 'faqs.data', 'uses' => 'App\Http\Controllers\AdminController@getBloodgroups']);

        Route::post('/save/bloodgroups', 'App\Http\Controllers\AdminController@postBloodgroups');

        Route::post('/edit/bloodgroups', 'App\Http\Controllers\AdminController@editBloodgroups');

        Route::post('/delete/bloodgroups', 'App\Http\Controllers\AdminController@deleteBloodgroups');

        //Student
        Route::get('/student', 'App\Http\Controllers\AdminController@viewStudent');

        Route::get('/student/datatables', ['as' => 'student.data', 'uses' => 'App\Http\Controllers\AdminController@getStudents']);

        Route::post('/save/student', 'App\Http\Controllers\AdminController@postStudent');

        Route::post('/edit/student', 'App\Http\Controllers\AdminController@editStudent');

        Route::get('/view_student', 'App\Http\Controllers\AdminController@viewStudentProfile');

        Route::post('/delete/students', 'App\Http\Controllers\AdminController@deleteStudent');

        Route::post('/move/student', 'App\Http\Controllers\AdminController@moveStudent');

        Route::post('/savemove/student', 'App\Http\Controllers\AdminController@postMoveStudent');

        Route::get('/student_excel',['as'=>'student_excel.data','uses'=>'App\Http\Controllers\AdminController@getExcelStudents']);

        Route::post('/edit/student_additional', 'App\Http\Controllers\AdminController@editStudentAdditional');

        Route::post('/save/student_details', 'App\Http\Controllers\AdminController@postStudentAdditional');

        Route::post('/edit/student_siblingstaffs', 'App\Http\Controllers\AdminController@editStudentSiblingstaffs');

        Route::post('/save/scholarsiblingstaff', 'App\Http\Controllers\AdminController@postStudentSiblingstaffs');

        Route::post('/save/scholarreward', 'App\Http\Controllers\AdminController@postStudentScholarReward');

        Route::get('/filter_examresults', 'App\Http\Controllers\AdminController@filterExamResults');

        // Import student
        Route::get('/import_students', 'App\Http\Controllers\AdminController@viewImportStudents');   

        Route::post('/import/scholarslist', ['uses' => 'App\Http\Controllers\ImportExportController@importScholarslistExcel']); 
        // Pre Admission Students

        Route::get('/pre_student', 'App\Http\Controllers\AdminController@viewPreStudent');

        Route::get('/pre_student/datatables', ['as' => 'pre_studentlist.data', 'uses' => 'App\Http\Controllers\AdminController@getPreStudents']);

        Route::post('/save/pre_student', 'App\Http\Controllers\AdminController@postPreStudent');

        Route::post('/edit/pre_student', 'App\Http\Controllers\AdminController@editPreStudent');

        Route::post('/delete/pre_students', 'App\Http\Controllers\AdminController@deletePreStudent');

        Route::post('/savemove/pre_student', 'App\Http\Controllers\AdminController@postMovePreStudent');



        // Import Teachers
        Route::get('/import_staffs', 'App\Http\Controllers\AdminController@viewImportTeachers');   

        Route::post('/import/staffslist', ['uses' => 'App\Http\Controllers\ImportExportController@importStaffslistExcel']);

        // Alumni Scholars
        Route::get('/alumnis', 'App\Http\Controllers\AdminController@viewAlumni');

        Route::get('/alumnis/datatables', ['as' => 'student.data', 'uses' => 'App\Http\Controllers\AdminController@getAlumni']);

        Route::post('/revert/alumnistudent', 'App\Http\Controllers\AdminController@revertStudent');

        // Categories
        Route::get('/categories', 'App\Http\Controllers\AdminController@viewCategories');

        Route::post('/save/categories', 'App\Http\Controllers\AdminController@postCategories');

        Route::get('/categories/datatables', ['as' => 'categories.data', 'uses' => 'App\Http\Controllers\AdminController@getCategories']);

        Route::post('/edit/categories', 'App\Http\Controllers\AdminController@editCategories');

        Route::post('/delete/categories', 'App\Http\Controllers\AdminController@deleteCategories');

        // Background Themes
        Route::get('/bthemes', 'App\Http\Controllers\AdminController@viewBackgroundThemes');

        Route::post('/save/bthemes', 'App\Http\Controllers\AdminController@postBackgroundThemes');

        Route::get('/bthemes/datatables', ['as' => 'bthemes.data', 'uses' => 'App\Http\Controllers\AdminController@getBackgroundThemes']);

        Route::post('/edit/bthemes', 'App\Http\Controllers\AdminController@editBackgroundThemes');

        Route::post('/delete/bthemes', 'App\Http\Controllers\AdminController@deleteBackgroundThemes');

        // Group
        Route::get('/group', 'App\Http\Controllers\AdminController@viewGroup');

        Route::post('/save/group', 'App\Http\Controllers\AdminController@postGroup');

        Route::get('/group/datatables', ['as' => 'group.data', 'uses' => 'App\Http\Controllers\AdminController@getGroup']);

        Route::post('/edit/group', 'App\Http\Controllers\AdminController@editGroup');

        Route::post('/view/group', 'App\Http\Controllers\AdminController@viewGroupMembers');  

        // Survey
        Route::get('/survey', 'App\Http\Controllers\AdminController@viewSurvey');

        Route::get('/addsurvey', 'App\Http\Controllers\AdminController@viewAddSurvey');

        Route::post('/save/survey', 'App\Http\Controllers\AdminController@postSurvey');

        Route::get('/survey/datatables', ['as' => 'survey.data', 'uses' => 'App\Http\Controllers\AdminController@getSurvey']);

        Route::get('/editsurvey', 'App\Http\Controllers\AdminController@editSurvey');

        Route::post('/delete/survey', 'App\Http\Controllers\AdminController@deleteSurvey'); 

        Route::post('/update/surveypost', 'App\Http\Controllers\AdminController@updateSurveyPost');

        Route::post('/update/survey', 'App\Http\Controllers\AdminController@updateStaffSurvey');

        // Reward / Remarks

        Route::get('/remarks', 'App\Http\Controllers\AdminController@viewRemarks');

        Route::get('/remarks/datatables', ['as' => 'smscredits.data', 'uses' => 'App\Http\Controllers\AdminController@getRemarks']);

        Route::post('/delete/remarks', 'App\Http\Controllers\AdminController@deleteRemarks');  

        Route::get('/remarks_excel',['as'=>'remarks_excel.data','uses'=>'App\Http\Controllers\AdminController@getRemarksExcel']);  

        Route::get('/rewards', 'App\Http\Controllers\AdminController@viewRewards');

        Route::get('/rewards/datatables', ['as' => 'smscredits.data', 'uses' => 'App\Http\Controllers\AdminController@getRewards']);

        Route::get('/rewards_excel',['as'=>'rewards_excel.data','uses'=>'App\Http\Controllers\AdminController@getRewardsExcel']);  

        Route::post('/delete/rewards', 'App\Http\Controllers\AdminController@deleteRewards');  

         

        // SMS Credits
        Route::get('/smscredits', 'App\Http\Controllers\AdminController@viewSMSCredits');

        Route::post('/save/smscredits', 'App\Http\Controllers\AdminController@postSMSCredits');

        Route::get('/smscredits/datatables', ['as' => 'smscredits.data', 'uses' => 'App\Http\Controllers\AdminController@getSMSCredits']);

        Route::post('/delete/smscredits', 'App\Http\Controllers\AdminController@deleteSMSCredits');

        // SMS Credits Consolidated
        Route::get('/smsschoolcredits', 'App\Http\Controllers\AdminController@viewSMSSchoolCredits'); 

        Route::get('/smsschoolcredits/datatables', ['as' => 'smsschoolcredits.data', 'uses' => 'App\Http\Controllers\AdminController@getSMSSchoolCredits']); 

        // Communication posts Staffs

        Route::get('/posts_staff', 'App\Http\Controllers\AdminController@viewPostsStaffs');

        Route::post('/delete/posts_staff', 'App\Http\Controllers\AdminController@deletePostsStaffs');

        Route::post('/update/posts_staff', 'App\Http\Controllers\AdminController@updatePostsStaffs');

        Route::get('/posts_staff_status/datatables', ['as' => 'posts_staff_status.data', 'uses' => 'App\Http\Controllers\AdminController@getPostStatus']); 

        Route::get('/posts_staff_status_excel',['as'=>'posts_staff_status_excel.data','uses'=>'App\Http\Controllers\AdminController@getPostStatusExcel']);

        Route::get('/addpoststaff', 'App\Http\Controllers\AdminController@addPostsStaff');

        Route::post('/post_load_content_staffs', 'App\Http\Controllers\AdminController@postLoadModalContentStaffs'); 

        Route::post('/post_staff_message', 'App\Http\Controllers\AdminController@postCommunicationStaff');

        // Communication posts

        Route::get('/posts', 'App\Http\Controllers\AdminController@viewPosts');

        Route::post('/filter_things', 'App\Http\Controllers\AdminController@filterThings'); 

        Route::post('/post_load_contents', 'App\Http\Controllers\AdminController@postLoadModalContents'); 

        Route::get('/addposts', 'App\Http\Controllers\AdminController@addPosts');

        Route::get('/editposts', 'App\Http\Controllers\AdminController@editPosts');

        Route::post('/delete/posts', 'App\Http\Controllers\AdminController@deletePosts');

        Route::post('/update/posts', 'App\Http\Controllers\AdminController@updatePosts');

        Route::get('/poststatus', 'App\Http\Controllers\AdminController@viewPostStatus');

        Route::get('/poststatus/datatables', ['as' => 'poststatus.data', 'uses' => 'App\Http\Controllers\AdminController@getPostStatus']); 

        Route::get('/poststatus_excel',['as'=>'poststatus_excel.data','uses'=>'App\Http\Controllers\AdminController@getPostStatusExcel']);

        Route::get('/communication', 'App\Http\Controllers\AdminController@viewCommunications');
        Route::post('/post_new_message', 'App\Http\Controllers\AdminController@postCommunication');
        Route::post('/post_update_message', 'App\Http\Controllers\AdminController@postCommunicationUpdate');

        //communication sms

        Route::get('/postsms', 'App\Http\Controllers\AdminController@viewPostSms');

        Route::get('/addpostsms', 'App\Http\Controllers\AdminController@addPostSms');

        Route::get('/editpostsms', 'App\Http\Controllers\AdminController@editPostSms');
        
        Route::post('/post_new_sms_scholar', 'App\Http\Controllers\AdminController@postCommunicationSmsScholar');

        Route::post('/post_update_sms_scholar', 'App\Http\Controllers\AdminController@postCommunicationUpdateSmsScholar');
         
        Route::post('/delete/postsms', 'App\Http\Controllers\AdminController@deletePostSms'); 

        Route::post('/update/postsms', 'App\Http\Controllers\AdminController@updatePostSms'); 

        Route::get('/postsmsstatus', 'App\Http\Controllers\AdminController@viewPostSmsStatus');

        Route::get('/postsmsstatus/datatables', ['as' => 'postsmsstatus.data', 'uses' => 'App\Http\Controllers\AdminController@getPostSmsStatus']);

        Route::get('/smsstatus_excel',['as'=>'smsstatus_excel.data','uses'=>'App\Http\Controllers\AdminController@getPostSmsStatusExcel']);

        //Communication Homeworks

        Route::get('/posthomeworks', 'App\Http\Controllers\AdminController@viewPostHomeworks'); 

        Route::post('/update/posthomeworks', 'App\Http\Controllers\AdminController@updatePostHomeworks');

        Route::post('/delete/posthomeworks', 'App\Http\Controllers\AdminController@deletePostHomeworks');  

        Route::get('/posthomeworkstatus/datatables', ['as' => 'posthomeworkstatus.data', 'uses' => 'App\Http\Controllers\AdminController@getPostHomeworkStatus']); 

        Route::get('/posthomeworksstatus_excel',['as'=>'posthomeworksstatus_excel.data','uses'=>'App\Http\Controllers\AdminController@getPostHomeworkStatusExcel']);

        //fetch state

        Route::post('/fetch-states', 'App\Http\Controllers\AdminController@fetchStates');

        Route::post('/fetch-term-classes', 'App\Http\Controllers\AdminController@fetchTermClasses');

        Route::post('/fetch-to-class', 'App\Http\Controllers\AdminController@fetchClasses');

        Route::post('/fetch-student-class', 'App\Http\Controllers\AdminController@fetchStudentClass');

        Route::post('/fetch-districts', 'App\Http\Controllers\AdminController@fetchDistricts');

        Route::post('/fetch-questions', 'App\Http\Controllers\AdminController@fetchQuestions');

        Route::post('/fetch-questions-type', 'App\Http\Controllers\AdminController@fetchQuestionType');

        //Teachers
        Route::get('/staffs', 'App\Http\Controllers\AdminController@viewTeachers');

        Route::get('/staffs/datatables', ['as' => 'staffs.data', 'uses' => 'App\Http\Controllers\AdminController@getTeachers']);

        Route::post('/save/staffs', 'App\Http\Controllers\AdminController@postTeachers');

        Route::post('/edit/staffs', 'App\Http\Controllers\AdminController@editTeachers');

        Route::get('/view_staff', 'App\Http\Controllers\AdminController@viewStaffProfile');

        /*//Class Teachers
        Route::get('/class_teachers', 'App\Http\Controllers\AdminController@viewClassTeachers');

        Route::get('/class_teachers/datatables', ['as' => 'class_teachers.data', 'uses' => 'App\Http\Controllers\AdminController@getClassTeachers']); */

        //Class Teacher Mapping

        Route::get('/ctutors', 'App\Http\Controllers\AdminController@viewClassTeacherMapping');

        Route::post('/save/ctutors_mapping', 'App\Http\Controllers\AdminController@postClassTeacherMapping');

        Route::post('/save/ctutors', 'App\Http\Controllers\AdminController@postClassTeachers');

        Route::post('/edit/ctutors', 'App\Http\Controllers\AdminController@editClassTeachers');

        //Subject Mapping to Teachers
        Route::get('/subject_mapping', 'App\Http\Controllers\AdminController@viewMappingSubject');

        Route::get('/add/subjectmapping','App\Http\Controllers\AdminController@addSubjectMapping');

        Route::post('/clone/subjectmapping','App\Http\Controllers\AdminController@cloneMappedSubject');

        Route::post('/load/mappedsubjects','App\Http\Controllers\AdminController@loadMappedSubjects');

        Route::post('/delete/mappedsubjects','App\Http\Controllers\AdminController@deleteMappedSubject');
        
        Route::get('/subject_mapping/datatables', ['as' => 'subject_mapping.data', 'uses' => 'App\Http\Controllers\AdminController@getMappingSubject']);

        Route::post('/save/subject_mapping', 'App\Http\Controllers\AdminController@postMappingSubject');

        Route::get('/edit/subject_mapping/{teacher_id}', 'App\Http\Controllers\AdminController@editMappingSubject');

        //Slot
        Route::get('/slot', 'App\Http\Controllers\AdminController@viewSLot');

        Route::get('/slot/datatables', ['as' => 'slot.data', 'uses' => 'App\Http\Controllers\AdminController@getSlot']);

        Route::post('/save/slot', 'App\Http\Controllers\AdminController@postSlot');

        Route::post('/edit/slot', 'App\Http\Controllers\AdminController@editSlot');

        //Subjects
        Route::get('/subjects', 'App\Http\Controllers\AdminController@viewSubjects');

        Route::post('/save/subjects', 'App\Http\Controllers\AdminController@postSubjects');

        Route::get('/subjects/datatables', ['as' => 'subject.data', 'uses' => 'App\Http\Controllers\AdminController@getSubjects']);

        Route::post('/edit/subjects', 'App\Http\Controllers\AdminController@editSubjects');

        //Home Work
        Route::get('/homework', 'App\Http\Controllers\AdminController@viewHomework');

        Route::get('/homework/datatables', ['as' => 'homework.data', 'uses' => 'App\Http\Controllers\AdminController@getHomework']);

        Route::post('/save/homework', 'App\Http\Controllers\AdminController@postHomework');

        Route::post('/edit/homework', 'App\Http\Controllers\AdminController@editHomework');

        Route::post('/update/homework', 'App\Http\Controllers\AdminController@updateHomework');

        Route::post('/edit/homeworkgrp', 'App\Http\Controllers\AdminController@editHomeworkGroup');

        Route::post('/save/homeworkgrp', 'App\Http\Controllers\AdminController@postHomeworkGroup');

        Route::get('/testlist/views/{test_id}','App\Http\Controllers\AdminController@viewHomeworkTestList');

        //Chapters

        Route::get('/chapters', 'App\Http\Controllers\AdminController@viewChapters');

        Route::get('/chapters/datatables', ['as' => 'chapters.data', 'uses' => 'App\Http\Controllers\AdminController@getChapters']);

        Route::post('/save/chapters', 'App\Http\Controllers\AdminController@postChapters');

        Route::post('/edit/chapters', 'App\Http\Controllers\AdminController@editChapters');

        // Chapter Topics

        Route::get('/chaptertopics', 'App\Http\Controllers\AdminController@viewChaptersTopics');

        Route::get('/chaptertopics/datatables', ['as' => 'chaptertopics.data', 'uses' => 'App\Http\Controllers\AdminController@getChapterTopics']);

        Route::post('/save/chaptertopics', 'App\Http\Controllers\AdminController@postChapterTopics');

        Route::post('/edit/chaptertopics', 'App\Http\Controllers\AdminController@editChapterTopics');

        //Topics

        Route::get('/topics', 'App\Http\Controllers\AdminController@viewSingleChapter');

        Route::get('/topics/datatables', ['as' => 'topics.data', 'uses' => 'App\Http\Controllers\AdminController@getTopics']);

        Route::post('/save/topics', 'App\Http\Controllers\AdminController@postTopics');

        Route::post('/edit/topics', 'App\Http\Controllers\AdminController@editTopics');

        Route::post('/fetch-chapters', 'App\Http\Controllers\AdminController@fetchChapters');

        Route::post('/fetch-class-subject', 'App\Http\Controllers\AdminController@fetchClasseSubject');

        Route::post('/fetch-chapterstopics', 'App\Http\Controllers\AdminController@fetchChaptersTopics');

        // Grades
        Route::get('/grades', 'App\Http\Controllers\AdminController@viewGrades');
        Route::get('/grades/datatables', ['as' => 'grades.data', 'uses' => 'App\Http\Controllers\AdminController@getGrades']);
        Route::post('/save/grades', 'App\Http\Controllers\AdminController@postGrades');
        Route::post('/edit/grades', 'App\Http\Controllers\AdminController@editGrades');

        // School Operations
        // Classes
        Route::get('/classes', 'App\Http\Controllers\AdminController@viewClasses');
        Route::get('/schoolclasses/datatables', ['as' => 'schoolclasses.data', 'uses' => 'App\Http\Controllers\AdminController@getSchoolClasses']);
        Route::get('/classupdate', 'App\Http\Controllers\AdminController@updateClasses');
        Route::post('/save/updateclasses', 'App\Http\Controllers\AdminController@postUpdateClasses');

        Route::get('/classes/datatables', ['as' => 'classes.data', 'uses' => 'App\Http\Controllers\AdminController@getClasses']);
        Route::post('/save/classes', 'App\Http\Controllers\AdminController@postClasses');
        Route::post('/edit/classes', 'App\Http\Controllers\AdminController@editClasses');

        // Sections
        Route::get('/sections', 'App\Http\Controllers\AdminController@viewSections');
        Route::get('/sections/datatables', ['as' => 'sections.data', 'uses' => 'App\Http\Controllers\AdminController@getSections']);
        Route::post('/save/sections', 'App\Http\Controllers\AdminController@postSections');
        Route::post('/edit/sections', 'App\Http\Controllers\AdminController@editSections');

        // Section Subject Mappings
        Route::get('/section_subjects', 'App\Http\Controllers\AdminController@viewSectionSubjects');
        Route::get('/section_subjects/datatables', ['as' => 'section_subjects.data', 'uses' => 'App\Http\Controllers\AdminController@getSectionSubjects']);
        Route::post('/save/section_subjects', 'App\Http\Controllers\AdminController@postSectionSubjects');
        Route::post('/edit/section_subjects', 'App\Http\Controllers\AdminController@editSectionSubjects');

        // Days
        Route::get('/days', 'App\Http\Controllers\AdminController@viewDays');
        Route::get('/days/datatables', ['as' => 'days.data', 'uses' => 'App\Http\Controllers\AdminController@getDays']);

        // Circulars
        Route::get('/circulars', 'App\Http\Controllers\AdminController@viewCirculars');
        Route::get('/circulars/datatables', ['as' => 'admincirculars.data', 'uses' => 'App\Http\Controllers\AdminController@getCirculars']);
        Route::post('/save/circulars', 'App\Http\Controllers\AdminController@postCirculars');
        Route::post('/edit/circulars', 'App\Http\Controllers\AdminController@editCirculars');

        //Period
        Route::get('/period_timing', 'App\Http\Controllers\AdminController@viewPeriodTiming');
        Route::get('/add/periods', 'App\Http\Controllers\AdminController@addPeriods');
        Route::get('/periods/datatables', ['as' => 'periods.data', 'uses' => 'App\Http\Controllers\AdminController@getPeriods']);
        Route::post('/save/period_timing', 'App\Http\Controllers\AdminController@postPeriodTiming');
        Route::get('/edit/period_timing', 'App\Http\Controllers\AdminController@getPeriodTiming');

        //Time Table

        Route::post('/fetch-section', 'App\Http\Controllers\AdminController@fetchSection');

        Route::post('/fetch-student', 'App\Http\Controllers\AdminController@fetchStudent');


        Route::post('/fetch-tests', 'App\Http\Controllers\AdminController@fetchTest');

        Route::post('/fetch-exams', 'App\Http\Controllers\AdminController@fetchExams');

        Route::post('/fetch-terms', 'App\Http\Controllers\AdminController@fetchTerms');

        Route::post('/fetch-subject', 'App\Http\Controllers\AdminController@fetchSubject');

        Route::post('/fetch-exam-subjects', 'App\Http\Controllers\AdminController@fetchExamSubject');

        Route::post('/fetch-subjectsection', 'App\Http\Controllers\AdminController@fetchSubjectSection');

        Route::get('/timetable', 'App\Http\Controllers\AdminController@viewTimetable');

        Route::post('/load/timetable', 'App\Http\Controllers\AdminController@loadTimetable');

        Route::post('/save/timetable', 'App\Http\Controllers\AdminController@saveTimetable');

        //Students leave
        Route::get('/studentsleavelist', 'App\Http\Controllers\AdminController@viewStudentLeave');

        Route::get('/edit_leave', 'App\Http\Controllers\AdminController@editStudentLeave');

        Route::post('/edit/studentleave', 'App\Http\Controllers\AdminController@updateStudentLeave');

        Route::get('/studentsleavelist/datatables', ['as' => 'studentsleavelist.data', 'uses' => 'App\Http\Controllers\AdminController@getStudentLeave']);

        Route::get('/admin_studentleave_excel',['as'=>'admin_studentleave_excel.data','uses'=>'App\Http\Controllers\AdminController@getExcelStudentLeave']);
        

        //Teacher leave
        Route::get('/staff_leavelist', 'App\Http\Controllers\AdminController@viewTeacherLeave');

        Route::get('/staff_leavelist/datatables', ['as' => 'staff_leavelist.data', 'uses' => 'App\Http\Controllers\AdminController@getTeacherLeave']);

        Route::get('/edit_teacherleave/{id}', 'App\Http\Controllers\AdminController@editTeacherLeave');

        Route::post('/edit/teacherleave', 'App\Http\Controllers\AdminController@updateTeacherLeave');

        Route::get('/admin_teacherleavelist_excel',['as'=>'admin_teacherleavelist_excel.data','uses'=>'App\Http\Controllers\AdminController@getExcelTeacherLeave']);

        // Events
        Route::get('/events', 'App\Http\Controllers\AdminController@viewEvents');
        Route::get('/events/datatables', ['as' => 'adminevents.data', 'uses' => 'App\Http\Controllers\AdminController@getEvents']);
        Route::post('/save/events', 'App\Http\Controllers\AdminController@postEvents');
        Route::post('/edit/events', 'App\Http\Controllers\AdminController@editEvents');
        Route::post('/delimage','App\Http\Controllers\AdminController@deleteEventAttachment');
        Route::post('/delcircularimage','App\Http\Controllers\AdminController@deleteEventCircular');
        Route::post('/load/gallery','App\Http\Controllers\AdminController@loadGallery');

        // Gallery
        Route::get('/gallery', 'App\Http\Controllers\AdminController@viewGallery');
        Route::get('/gallery/datatables', ['as' => 'adminevents.data', 'uses' => 'App\Http\Controllers\AdminController@getGallery']);
        Route::post('/save/gallery', 'App\Http\Controllers\AdminController@postGallery');
        Route::post('/edit/gallery', 'App\Http\Controllers\AdminController@editGallery');
        Route::post('/delgalleryimage','App\Http\Controllers\AdminController@deleteGalleryAttachment');

        // Holidays
        Route::get('/holidays', 'App\Http\Controllers\AdminController@viewHolidays');
        Route::get('/holidays/datatables', ['as' => 'holidays.data', 'uses' => 'App\Http\Controllers\AdminController@getHolidays']);
        Route::post('/save/holidays', 'App\Http\Controllers\AdminController@postHolidays');
        Route::post('/edit/holidays', 'App\Http\Controllers\AdminController@editHolidays');
        Route::post('/delete/holidays', 'App\Http\Controllers\AdminController@deleteHolidays');

        Route::get('/changeholidays', 'App\Http\Controllers\AdminController@viewChangeHolidays');
        Route::post('/load/holidays', 'App\Http\Controllers\AdminController@loadChangeHolidays');
        Route::post('/save/changeholidays', 'App\Http\Controllers\AdminController@saveChangeHolidays');



        // Mark Attendance 

        Route::get('/mark_attendance', 'App\Http\Controllers\AdminController@viewMarkAttendance');
        Route::post('/load/scholar_daily_attendance', 'App\Http\Controllers\AdminController@loadScholarDailyAttendancePage');
        Route::get('/load/scholar_marked_attendance', 'App\Http\Controllers\AdminController@getScholarMarkedAttendance'); 
        Route::post('/addabsentstudent', 'App\Http\Controllers\AdminController@addScholarMarkedAbsent'); 

        //Attendance Approval Overall

        Route::get('/oa_student_attendance_approval', 'App\Http\Controllers\AdminController@viewOAStudentAttendanceApproval');
        Route::get('/oa_student_attendance_approval/datatables', ['as' => 'loadStudentattendanceoaapproval.data', 'uses' => 'App\Http\Controllers\AdminController@loadOAStudentAttendanceApproval']);
        Route::post('/approve/oaAttendance', 'App\Http\Controllers\AdminController@oaAttendanceApproval');
        //Attendance 

        Route::get('/oa_student_attendance', 'App\Http\Controllers\AdminController@viewOAStudentAttendance');
        Route::get('/oa_student_attendance/datatables', ['as' => 'loadStudentattendanceoa.data', 'uses' => 'App\Http\Controllers\AdminController@loadOAStudentAttendance']);

        Route::get('/student_attendance', 'App\Http\Controllers\AdminController@viewStudentAttendance');
        Route::post('/load/studentattendance', 'App\Http\Controllers\AdminController@loadStudentAttendance');
        Route::get('/attendencereport/excel', ['as' => 'attendence.excel', 'uses' => 'App\Http\Controllers\AdminController@getStudentAttendenceExcel']);
        Route::post('/update/studentattendance','App\Http\Controllers\AdminController@updateStudentAttendance');
        Route::get('/entryattendance/{id}/{year}/{class_id}/{section_id}', 'App\Http\Controllers\AdminController@viewEditStudentsAttendance');
        Route::get('/student_dailyattendance', 'App\Http\Controllers\AdminController@viewStudentDailyAttendance');
        Route::post('/update/studentdailyattendance','App\Http\Controllers\AdminController@updateStudentDilayAttendance');
        Route::post('/load/studentdailyattendance', 'App\Http\Controllers\AdminController@loadStudentDailyAttendance');
        Route::post('/fetch_attendance','App\Http\Controllers\AdminController@fetchAttendance');
        Route::post('/save/dailyattendace','App\Http\Controllers\AdminController@postDailyAttendance');
     
        Route::get('/send_attendance_notification', 'App\Http\Controllers\AdminController@send_attendance_notification');

        Route::get('/student_daily_attendance', 'App\Http\Controllers\AdminController@viewStudentDailyAttendancePage');
        Route::post('/load/student_daily_attendance', 'App\Http\Controllers\AdminController@loadStudentDailyAttendancePage');
        Route::post('/save/daily_attendace','App\Http\Controllers\AdminController@postDailyAttendancePage');

        Route::get('/teacher_attendance', 'App\Http\Controllers\AdminController@viewTeacherAttendance');
        Route::post('/load/teacherattendance', 'App\Http\Controllers\AdminController@loadTeacherAttendance');
        Route::post('/update/teacherattendance','App\Http\Controllers\AdminController@updateTeacherAttendance');
        Route::get('/tattendencereport/excel', ['as' => 'teacherattendence.excel', 'uses' => 'App\Http\Controllers\AdminController@getTeacherAttendenceExcel']);
        Route::post('/fetch_teachers','App\Http\Controllers\AdminController@fetchTeachers');
        Route::get('/teacher_entryattendance/{id}/{year}', 'App\Http\Controllers\AdminController@viewEditTeachersAttendance');
        Route::post('/save/teacher_dailyattendace','App\Http\Controllers\AdminController@postTeachersDailyAttendance');
        


        
        Route::get('/staff_dailyattendance', 'App\Http\Controllers\AdminController@viewTeacherDailyAttendance');
        Route::post('/load/dailyteacherattendance', 'App\Http\Controllers\AdminController@loadTeacherDailyAttendance');
        Route::post('/update/teacherattendance','App\Http\Controllers\AdminController@updateTeacherDailyAttendance');
        Route::get('/tattendencereport/excel', ['as' => 'teacherattendence.excel', 'uses' => 'App\Http\Controllers\AdminController@getTeacherAttendenceExcel']);
        Route::get('/teacher_entryattendance/{id}/{year}', 'App\Http\Controllers\AdminController@viewEditTeachersAttendance');

        //Academics
        Route::get('/student_academics', 'App\Http\Controllers\AdminController@viewStudentAcademics');
        Route::get('/student_academics/datatables', ['as' => 'student_academics.data', 'uses' => 'App\Http\Controllers\AdminController@getStudentAcademics']);
        Route::post('/save/studentacademics', 'App\Http\Controllers\AdminController@postStudentAcademics');
        Route::post('/edit/studentacademics', 'App\Http\Controllers\AdminController@editStudentAcademics');

        
        //Promotions
        Route::get('/student_promotions', 'App\Http\Controllers\AdminController@viewStudentPromotions');
        Route::get('/promotion', ['as' => 'promotion.data', 'uses' => 'App\Http\Controllers\AdminController@loadStudentPromotions']);
        Route::post('/update/promotions', 'App\Http\Controllers\AdminController@updateStudentPromotions');

        // Exams
        Route::get('/exams', 'App\Http\Controllers\AdminController@viewExams');
        Route::post('/load/exams', 'App\Http\Controllers\AdminController@loadExams');
        Route::post('/viewload/exams', 'App\Http\Controllers\AdminController@viewloadExams');
        Route::get('/add/exams', 'App\Http\Controllers\AdminController@addExams');
        /*Route::get('/edit/exams/{monthyear}/{exam_name}', 'App\Http\Controllers\AdminController@editExams');
        Route::get('/view/exams/{monthyear}/{exam_name}', 'App\Http\Controllers\AdminController@previewExams');*/
        Route::get('/exams/datatables', ['as' => 'exams.data', 'uses' => 'App\Http\Controllers\AdminController@getExams']);
        Route::post('/save/exams', 'App\Http\Controllers\AdminController@postExams');
        Route::get('/edit/exams', 'App\Http\Controllers\AdminController@editExams');
        Route::get('/view/exams', 'App\Http\Controllers\AdminController@previewExams');

        // Route::post('/edit/exams', 'App\Http\Controllers\AdminController@editExams');

        // Examinations
        Route::get('/examinations', 'App\Http\Controllers\AdminController@viewExaminations');
        Route::get('/examinations/datatables', ['as' => 'examinations.data', 'uses' => 'App\Http\Controllers\AdminController@getExaminations']);
        Route::post('/save/examinations', 'App\Http\Controllers\AdminController@postExaminations');
        Route::post('/edit/examinations', 'App\Http\Controllers\AdminController@editExaminations');

        Route::get('/examination_settings', 'App\Http\Controllers\AdminController@viewExaminationSettings');
        Route::get('/examination_settings/datatables', ['as' => 'examination_settings.data', 'uses' => 'App\Http\Controllers\AdminController@getExaminationSettings']);

        Route::get('/examsettings', 'App\Http\Controllers\AdminController@settingsExaminations');
        Route::post('/load/examination', 'App\Http\Controllers\AdminController@loadExamination');
        Route::post('/save/examsettings', 'App\Http\Controllers\AdminController@postExamSettings');
        Route::get('/edit/examsettings', 'App\Http\Controllers\AdminController@editExamSettings');
        Route::get('/download/examhallticket', 'App\Http\Controllers\AdminController@downloadExamHallticket');
        Route::get('/generate/examhallticket', 'App\Http\Controllers\AdminController@generateExamHallticket');
        Route::get('/examgeneratescholars/datatables', ['as' => 'examgeneratescholars.data', 'uses' => 'App\Http\Controllers\AdminController@getExamGenerateScholars']);

        // Terms
        Route::get('/terms', 'App\Http\Controllers\AdminController@viewTerms');
        Route::get('/terms/datatables', ['as' => 'terms.data', 'uses' => 'App\Http\Controllers\AdminController@getTerms']);
        Route::post('/save/terms', 'App\Http\Controllers\AdminController@postTerms');
        Route::post('/edit/terms', 'App\Http\Controllers\AdminController@editTerms');

        // Marks Entry
        Route::get('/marks_entry', 'App\Http\Controllers\AdminController@viewMarksEntry');
        Route::post('/load/marks_entry', 'App\Http\Controllers\AdminController@loadMarksEntry');
        Route::post('/update/marks_entry', 'App\Http\Controllers\AdminController@updateMarksEntry');
        Route::post('/update/all_marks_entry', 'App\Http\Controllers\AdminController@updateAllMarksEntry');

        // as per client marks entry
        Route::get('/exam_marksentry', 'App\Http\Controllers\AdminController@viewExamMarksEntry');
        Route::post('/load/exam_marksentry', 'App\Http\Controllers\AdminController@loadExamMarksEntry');

        // Exam results
        Route::get('/exam_results', 'App\Http\Controllers\AdminController@viewExamResults');
        Route::post('/load/exam_results', 'App\Http\Controllers\AdminController@loadExamResults');
        Route::get('/load/exam_results_pdf', 'App\Http\Controllers\AdminController@loadExamResultsPdf');

        // Question Banks
        Route::get('/questionbank', 'App\Http\Controllers\AdminController@viewQuestionbank');
        Route::get('/questionbank/datatables', ['as' => 'questionbank.data', 'uses' => 'App\Http\Controllers\AdminController@getQuestionbank']);
        Route::get('/add/questionbank', 'App\Http\Controllers\AdminController@addQuestionbank');
        Route::post('/save/questionbank', 'App\Http\Controllers\AdminController@postQuestionbank');
        Route::post('/clone/questiontype', 'App\Http\Controllers\AdminController@cloneQuestiontype');
        Route::get('/view/questionbank', 'App\Http\Controllers\AdminController@previewQuestionbank');
        Route::get('/edit/questionbank', 'App\Http\Controllers\AdminController@editQuestionbank');
        Route::post('/check-chapterqb', 'App\Http\Controllers\AdminController@checkChapterQb');
        
        Route::post('/delete/questionbank', 'App\Http\Controllers\AdminController@deleteQuestionBank');
        Route::post('/delete/individualquestion', 'App\Http\Controllers\AdminController@deleteIndividualQuestionBank');
        //Route::post('/export/questionbank', 'App\Http\Controllers\AdminController@exportQuestionbank');

        Route::post('/export/questionbank/', ['as' => 'qb_excel.data', 'uses' => 'App\Http\Controllers\ImportExportController@getQuestionbankExcel']);
        
        Route::post('/import/questionbank', ['uses' => 'App\Http\Controllers\ImportExportController@importQuestionbankExcel']);

        // Test List
        Route::get('/testlist', 'App\Http\Controllers\AdminController@viewTestlist');
        Route::get('/testlist/datatables', ['as' => 'admin_testlist.data', 'uses' => 'App\Http\Controllers\AdminController@getTestlist']);
        Route::get('/qbfrtest/datatables', ['as' => 'qbfrtest.data', 'uses' => 'App\Http\Controllers\AdminController@getQuestionbankForTest']);
        Route::get('/add/testlist', 'App\Http\Controllers\AdminController@addTest');
        Route::post('/view/qbfrtest', 'App\Http\Controllers\AdminController@viewQbforTest');
        Route::post('/save/qbtest', 'App\Http\Controllers\AdminController@saveQbTest');
        Route::get('/view/testlist', 'App\Http\Controllers\AdminController@previewTest');
        Route::get('/edittestlist', 'App\Http\Controllers\AdminController@editTest');
        Route::post('/update/updatetest', 'App\Http\Controllers\AdminController@EditTestList');
        Route::get('/auto/testlist', 'App\Http\Controllers\AdminController@addAutoTest');
        Route::post('/view/qbfrautotest', 'App\Http\Controllers\AdminController@viewQbforAutoTest');
        Route::post('/save/qbautotest', 'App\Http\Controllers\AdminController@saveQbAutoTest');

        // test attempted

        Route::get('/view/testattempted/datatables', ['as' => 'admin_testattempted.data', 'uses' => 'App\Http\Controllers\AdminController@getTestAttempted']);
        Route::get('/view/testattempted/{id}', 'App\Http\Controllers\AdminController@viewTestAttempted');

        Route::get('/testlistpapers', 'App\Http\Controllers\AdminController@viewTestlistPapers');
        Route::get('/testlistpapers/datatables', ['as' => 'admin_testlistpapers.data', 'uses' => 'App\Http\Controllers\AdminController@getTestlistPapers']);

        Route::get('/auto/testlistpapers', 'App\Http\Controllers\AdminController@addAutoTestPapers');
        Route::post('/view/qbfrautotestpapers', 'App\Http\Controllers\AdminController@viewQbforAutoTestPapers');
        Route::post('/save/qbautotestpapers', 'App\Http\Controllers\AdminController@saveQbAutoTestPapers');
        Route::get('/view/testlistpapers', 'App\Http\Controllers\AdminController@previewTestPapers');

        Route::get('/studentstestlist', 'App\Http\Controllers\AdminController@viewStudentsTestlist');
        Route::get('/studentstestlist/datatables', ['as' => 'studentstestlist.data', 'uses' => 'App\Http\Controllers\AdminController@getStudentsTestlist']);
        Route::get('/studenttestlist_excel', ['as' => 'studenttestlist_excel.data', 'uses' => 'App\Http\Controllers\AdminController@getExcelStudentsTestlist']);
        Route::get('/view/studentstestlist', 'App\Http\Controllers\AdminController@previewStudentsTest');
        Route::get('/edit/editstudentstestlist', 'App\Http\Controllers\AdminController@editStudentsTest');
        Route::post('/save/studentstestmarks', 'App\Http\Controllers\AdminController@saveStudentsMark');

        
        Route::get('/studentattendancerep','App\Http\Controllers\AdminController@viewStudentAttenReport');
        Route::post('/load/studentattendancerep', 'App\Http\Controllers\AdminController@loadStudentAttendanceRep');
        Route::post('/update/studentattendancerep','App\Http\Controllers\AdminController@updateStudentAttendanceRep');

        Route::get('/staff_attendancerep','App\Http\Controllers\AdminController@viewTeacherAttendanceRep');
        Route::post('/load/teacherattendancerep', 'App\Http\Controllers\AdminController@loadTeacherAttendanceRep');
        Route::post('/update/teacherattendancerep','App\Http\Controllers\AdminController@updateTeacherAttendanceRep');

        // Students Strength
        Route::get('/studentstrength', 'App\Http\Controllers\AdminController@viewStudentStrength');
        Route::get('/studentstrength/datatables', ['as' => 'studentstrength.data', 'uses' => 'App\Http\Controllers\AdminController@getStudentStrength']);

        // Students Attendance report
        Route::get('/studentspresence', 'App\Http\Controllers\AdminController@viewStudentsPresence');
        Route::get('/studentspresence/datatables', ['as' => 'studentspresence.data', 'uses' => 'App\Http\Controllers\AdminController@getStudentsPresence']);
        Route::post('/load/studentspresence', 'App\Http\Controllers\AdminController@loadStudentPresence');

        //Today students absent report
        Route::get('/studentleavereports','App\Http\Controllers\AdminController@viewStudentAbsentReport');
        Route::get('/studentleavereports/datatables', ['as' => 'studentleavereports.data', 'uses' => 'App\Http\Controllers\AdminController@getStudentAbsentReport']);

        Route::get('/admin_studentleavereports_excel',['as'=>'admin_studentleavereports_excel.data','uses'=>'App\Http\Controllers\AdminController@getExcelTodayStudentLeaveReports']);


        //Test attempts list
        Route::get('/studenttestattempts','App\Http\Controllers\AdminController@viewStudentTestAttempts');
        Route::get('/studenttestattempts/datatables', ['as' => 'studenttestattempts.data', 'uses' => 'App\Http\Controllers\AdminController@getStudentsTestAttempts']);

        //View Test Result Attempts
        Route::get('/view/studentstestattempts','App\Http\Controllers\AdminController@viewTestAttemptResult');

        //Master- Fees Module -receipt head
        Route::get('/receipt_head', 'App\Http\Controllers\AdminController@receiptHeadMaster');

        Route::get('/receipt_head_data/datatables', ['as' => 'receipt_head_data.data', 'uses' => 'App\Http\Controllers\AdminController@getReceiptHead']);

        Route::post('/save/receipt_head', 'App\Http\Controllers\AdminController@postReceiptHead');
        Route::post('/edit/receipt_head', 'App\Http\Controllers\AdminController@editReceiptHead');
        Route::post('/delete/receipt_head', 'App\Http\Controllers\AdminController@deleteReceiptHead');

        //Master- Fees Module -Fee category
        Route::get('/fee_category', 'App\Http\Controllers\AdminController@feeCategoryMaster');

        Route::get('/fee_category_data/datatables', ['as' => 'fee_category_data.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeCategory']);

        Route::post('/save/fee_category', 'App\Http\Controllers\AdminController@postFeeCategory');
        Route::post('/edit/fee_category', 'App\Http\Controllers\AdminController@editFeeCategory');
        Route::post('/delete/fee_category', 'App\Http\Controllers\AdminController@deleteFeeCategory');

        //Master- Fees Module -Payment Mode
        Route::get('/payment_mode', 'App\Http\Controllers\AdminController@PaymentModeMaster');

        Route::get('/payment_mode_data/datatables', ['as' => 'payment_mode_data.data', 'uses' => 'App\Http\Controllers\AdminController@getPaymentMode']);

        Route::post('/save/payment_mode', 'App\Http\Controllers\AdminController@postPaymentMode');
        Route::post('/edit/payment_mode', 'App\Http\Controllers\AdminController@editPaymentMode');
        Route::post('/delete/payment_mode', 'App\Http\Controllers\AdminController@deletePaymentMode');

        //Master- Fees Module -Fee Cancel Reason
        Route::get('/fee_cancel_reason', 'App\Http\Controllers\AdminController@FeeCancelReasonMaster');

        Route::get('/fee_cancel_reason_data/datatables', ['as' => 'fee_cancel_reason_data.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeCancelReason']);

        Route::post('/save/fee_cancel_reason', 'App\Http\Controllers\AdminController@postFeeCancelReason');
        Route::post('/edit/fee_cancel_reason', 'App\Http\Controllers\AdminController@editFeeCancelReason');
        Route::post('/delete/fee_cancel_reason', 'App\Http\Controllers\AdminController@deleteFeeCancelReason');


        //Master- Fees Module -Concession Category
        Route::get('/concession_category', 'App\Http\Controllers\AdminController@ConcessionCategoryMaster');

        Route::get('/concession_category_data/datatables', ['as' => 'concession_category_data.data', 'uses' => 'App\Http\Controllers\AdminController@getConcessionCategory']);

        Route::post('/save/concession_category', 'App\Http\Controllers\AdminController@postConcessionCategory');
        Route::post('/edit/concession_category', 'App\Http\Controllers\AdminController@editConcessionCategory');
        Route::post('/delete/concession_category', 'App\Http\Controllers\AdminController@deleteConcessionCategory');

        //Master- Fees Module -waiver Category
        Route::get('/wavier_category', 'App\Http\Controllers\AdminController@WavierCategoryMaster');
        Route::post('/save/wavier_category', 'App\Http\Controllers\AdminController@postWavierCategory');
        Route::get('/wavier_category_data/datatables', ['as' => 'wavier_category_data.data', 'uses' => 'App\Http\Controllers\AdminController@getWavierCategory']);
        Route::post('/edit/wavier_category', 'App\Http\Controllers\AdminController@editWavierCategory');
        Route::post('/delete/wavier_category', 'App\Http\Controllers\AdminController@deleteWavierCategory');

        //Master- Fees Module -Bank Master
        Route::get('/bank_master', 'App\Http\Controllers\AdminController@BankListMaster');

        Route::get('/bank_master_data/datatables', ['as' => 'bank_master_data.data', 'uses' => 'App\Http\Controllers\AdminController@getBankList']);

        Route::post('/save/bank_master', 'App\Http\Controllers\AdminController@postBankList');
        Route::post('/edit/bank_master', 'App\Http\Controllers\AdminController@editBankList');
        Route::post('/delete/bank_master', 'App\Http\Controllers\AdminController@deleteBankList');

        //Master- Fees Module -Fee Items
        Route::get('/fee_items', 'App\Http\Controllers\AdminController@FeeItemsMaster');

        Route::get('/fee_items_data/datatables', ['as' => 'fee_items_data.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeItems']);

        Route::post('/save/fee_items', 'App\Http\Controllers\AdminController@postFeeItems');
        Route::post('/edit/fee_items', 'App\Http\Controllers\AdminController@editFeeItems');
        Route::post('/delete/fee_items', 'App\Http\Controllers\AdminController@deleteFeeItems');

         //Master- Fees Module -Fee Terms
         Route::get('/fee_terms', 'App\Http\Controllers\AdminController@FeeTermsMaster');

         Route::get('/fee_terms_data/datatables', ['as' => 'fee_terms_data.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeTerms']);
 
         Route::post('/save/fee_terms', 'App\Http\Controllers\AdminController@postFeeTerms');
         Route::post('/edit/fee_terms', 'App\Http\Controllers\AdminController@editFeeTerms');
         Route::post('/delete/fee_terms', 'App\Http\Controllers\AdminController@deleteFeeTerms');
         
          //Master- Fees Module -Account

         Route::get('/fees_account', 'App\Http\Controllers\AdminController@accountmaster');
         Route::get('/account_data/datatables', ['as' => 'account_data.data', 'uses' => 'App\Http\Controllers\AdminController@acclist']);
         Route::post('/save/fees_account', 'App\Http\Controllers\AdminController@addaccount');
         Route::post('/edit/fees_account', 'App\Http\Controllers\AdminController@editaccount');
         Route::post('/delete/fees_account', 'App\Http\Controllers\AdminController@deleteFeeAccount');

         //Fee collections module

        Route::get('/fee_structure/list', 'App\Http\Controllers\AdminController@feeStructureListPage');

        Route::get('/fee_structure/add', 'App\Http\Controllers\AdminController@feeStructureAddPage');

        Route::get('/filter_fee_item','App\Http\Controllers\AdminController@filterFeeCategoryItem');
        
        Route::post('/post_fee_structure', 'App\Http\Controllers\AdminController@postNewFeeStructure');

        Route::get('/fee_structure_list/datatables', ['as' => 'fee_structure_list.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeStructureLists']);

        Route::post('/edit/get_fee_structure_data', 'App\Http\Controllers\AdminController@editFeeStructureList');
        
        Route::post('/edit/fee_structure_item', 'App\Http\Controllers\AdminController@postEditFeeStructureList');

        Route::post('/delete/fee_structure_data', 'App\Http\Controllers\AdminController@deleteFeeStructureList');

        Route::get('/fee_collection', 'App\Http\Controllers\AdminController@feeCollectionPage');

        Route::get('/search_student','App\Http\Controllers\AdminController@searchStudentNames');

        Route::get('/filter_collections','App\Http\Controllers\AdminController@filterFeeCollections'); 

        Route::post('/post_pay_fee', 'App\Http\Controllers\AdminController@postPayFees');

        Route::post('/save/feeconcession', 'App\Http\Controllers\AdminController@postPayFeesConcession');

        Route::post('/save/feewaiver', 'App\Http\Controllers\AdminController@postPayFeesWaiver');

        Route::get('/feesummary', 'App\Http\Controllers\AdminController@feeSummaryPage');

        Route::get('/feesummary/datatables', ['as' => 'feesummary.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeSummaryLists']);

        Route::get('/feesummarydeleted/datatables', ['as' => 'feesummary.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeSummaryDeletedLists']);

        Route::get('/feeconcessions', 'App\Http\Controllers\AdminController@feeConcessionsPage');

        Route::get('/feeconcessions/datatables', ['as' => 'feeconcessions.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeConcessionsLists']);

        Route::post('/load/feeconcessions', 'App\Http\Controllers\AdminController@getLoadFeesConcessions');

        Route::post('/save/fee_concessions', 'App\Http\Controllers\AdminController@postSaveFeesConcessions');

        Route::post('/load/additional_feesitems', 'App\Http\Controllers\AdminController@getLoadAdditionalFeesitems');

        Route::post('/save/addfees', 'App\Http\Controllers\AdminController@postSaveFeesAdditions');

        Route::post('/delete/addfees', 'App\Http\Controllers\AdminController@postDeleteFeesAdditions');

        Route::post('/delete/add_on_fees', 'App\Http\Controllers\AdminController@postDeleteAddonFees');

        Route::post('/delete/mandatoryfees', 'App\Http\Controllers\AdminController@postDeleteFeesMandatory');

        Route::post('/delete/feeconcession', 'App\Http\Controllers\AdminController@postDeleteFeesConcession');

        Route::post('/delete/feewaiver', 'App\Http\Controllers\AdminController@postDeleteFeesWaiver');

        Route::post('/delete/feeconwaiverrecord', 'App\Http\Controllers\AdminController@postDeleteFeesConWaiverRecord');

        Route::get('/fee_summary', 'App\Http\Controllers\AdminController@repfeeSummaryPage');

        Route::get('/scholar_feesummary/datatables', ['as' => 'fee_summary.data', 'uses' => 'App\Http\Controllers\AdminController@getrepFeeSummaryLists']);

        Route::get('/scholar_fee_summary_excel',['as'=>'scholar_fee_summary_excel.data','uses'=>'App\Http\Controllers\AdminController@getScholarFeeFummaryExcel']);

        Route::get('/scholar_fee_summary_deleted_excel',['as'=>'scholar_fee_summary_deleted_excel.data','uses'=>'App\Http\Controllers\AdminController@getScholarFeeSummaryDeletedExcel']);

        //Fees Receipts

        Route::post('/load/openfeereceipts', 'App\Http\Controllers\AdminController@loadFeeReceiptData');

        Route::get('/feereceipts', 'App\Http\Controllers\AdminController@feeReceiptsPage');

        Route::get('/feereceipts/datatables', ['as' => 'feereceipts.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeReceiptLists']);

        Route::get('/generateReceiptPDF', 'App\Http\Controllers\AdminController@generateReceiptPdf'); 

        Route::post('/fee_collection/cancel_fee_receipt', 'App\Http\Controllers\AdminController@CancelFeeReceiptData');
        Route::post('/fee_collection/fee_cancel', 'App\Http\Controllers\AdminController@postCancelFeeReceipt');
        Route::get('/cancelfeereceipts/datatables', ['as' => 'cancelfeereceipts.data', 'uses' => 'App\Http\Controllers\AdminController@getCancelFeeReceiptLists']);

        // Fees Reports

        Route::get('/fee_update', 'App\Http\Controllers\AdminController@updatefeeSummaryPage'); 
        Route::get('/fee_update/datatables', ['as' => 'fee_update.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeUpdates']);
        Route::post('/edit/fee_update', 'App\Http\Controllers\AdminController@editFeeUpdates');

        // Collection Report 
        Route::get('/fee_report/collection', 'App\Http\Controllers\AdminController@viewCollectionFeesReport');

        Route::get('/fee_report/collection/datatables', ['as' => 'fee_report_collection.data', 'uses' => 'App\Http\Controllers\AdminController@getCollectionFeesReport']);

        Route::get('/fee_report/collection_excel',['as'=>'fee_report_collection_excel.data','uses'=>'App\Http\Controllers\AdminController@getCollectionFeesReportExcel']);

        // Consolidated Fee Report
        Route::get('/fees_report', 'App\Http\Controllers\AdminController@viewFeesReport');

        Route::get('/fees_report/datatables', ['as' => 'fees_report.data', 'uses' => 'App\Http\Controllers\AdminController@getFeesReport']);

        Route::get('/fees_report_excel',['as'=>'fees_report_excel.data','uses'=>'App\Http\Controllers\AdminController@getFeesReportExcel']);

        // Summary Fee Report
        Route::get('/fees_summary_report', 'App\Http\Controllers\AdminController@viewFeeSummaryReport');

        Route::get('/fees_summary_report/datatables', ['as' => 'fees_summary_report.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeSummaryReport']);

        Route::get('/fees_summary_report_excel',['as'=>'fees_report_excel.data','uses'=>'App\Http\Controllers\AdminController@getFeesSummaryReportExcel']);

        // Receipts Fee Report
        Route::get('/fees_receipts_report', 'App\Http\Controllers\AdminController@viewFeeReceiptsReport');

        Route::get('/fees_receipts_report/datatables', ['as' => 'fees_receipts_report.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeReceiptsReport']);

        Route::get('/fees_receipts_report_excel',['as'=>'fees_receipts_report_excel.data','uses'=>'App\Http\Controllers\AdminController@getFeesReceiptsReportExcel']);

        // Receipts Cancelled Fee Report
        Route::get('/fees_receipts_cancelled_report', 'App\Http\Controllers\AdminController@viewFeeReceiptsCancelledReport');

        Route::get('/fees_receipts_cancelled_report/datatables', ['as' => 'fees_receipts_cancelled_report.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeReceiptsCancelledReport']);

        Route::get('/fees_receipts_cancelled_report_excel',['as'=>'fees_report_excel.data','uses'=>'App\Http\Controllers\AdminController@getFeesCancelledReportExcel']);

        // Overall Fee Report
        Route::get('/fees_overall_report', 'App\Http\Controllers\AdminController@viewFeeOverallReport');
        Route::get('/fees_overall_report/datatables', ['as' => 'fees_overall_report.data', 'uses' => 'App\Http\Controllers\AdminController@loadFeeOverallReport']);
        
        // Concession Report 
        Route::get('/conwai_fee_report/collection', 'App\Http\Controllers\AdminController@viewConWaiFeesReport');

        Route::get('/conwai_fee_report/collection/datatables', ['as' => 'conwai_fee_report_collection.data', 'uses' => 'App\Http\Controllers\AdminController@getConWaiFeesReport']);

        Route::get('/conwai_fee_report/collection_excel',['as'=>'conwai_fee_report_collection_excel.data','uses'=>'App\Http\Controllers\AdminController@getConWaiFeesReportExcel']);

        // Waiver Report 
        Route::get('/waiver_fee_report/collection', 'App\Http\Controllers\AdminController@viewWaiverFeesReport');

        Route::get('/waiver_fee_report/collection/datatables', ['as' => 'waiver_fee_report_collection.data', 'uses' => 'App\Http\Controllers\AdminController@getWaiverFeesReport']);

        Route::get('/waiver_fee_report/collection_excel',['as'=>'waiver_fee_report_collection_excel.data','uses'=>'App\Http\Controllers\AdminController@getWaiverFeesReportExcel']);

        // Pending Fee Report 
        Route::get('/pending_fee_report/collection', 'App\Http\Controllers\AdminController@viewPendingFeesReport');

        Route::get('/pending_fee_report/collection/datatables', ['as' => 'pending_fee_report_collection.data', 'uses' => 'App\Http\Controllers\AdminController@getPendingFeesReport']);

        Route::get('/pending_fee_report/collection_excel',['as'=>'conwai_fee_report_collection_excel.data','uses'=>'App\Http\Controllers\AdminController@getPendingFeesReportExcel']);



        // Contacts List 

        Route::get('/contacts_list', 'App\Http\Controllers\AdminController@viewContactsList');

        Route::post('/save/contacts_list', 'App\Http\Controllers\AdminController@postContactsList');

        Route::get('/contacts_list/datatables', ['as' => 'contacts_list.data', 'uses' => 'App\Http\Controllers\AdminController@getContactsList']);

        Route::post('/edit/contacts_list', 'App\Http\Controllers\AdminController@editContactsList');

        // Contacts For
        Route::get('/contacts_for', 'App\Http\Controllers\AdminController@viewContactsFor');

        Route::post('/save/contacts_for', 'App\Http\Controllers\AdminController@postContactsFor');

        Route::get('/contacts_for/datatables', ['as' => 'contacts_for.data', 'uses' => 'App\Http\Controllers\AdminController@getContactsFor']);

        Route::post('/edit/contacts_for', 'App\Http\Controllers\AdminController@editContactsFor');

        //fee waiver
        
        Route::post('fee_collection/post_fee_waiver', 'App\Http\Controllers\AdminController@postFeeWaiver');

        Route::get('/feewaiversummary/datatables', ['as' => 'feewaiversummary.data', 'uses' => 'App\Http\Controllers\AdminController@getFeeWaiverLists']); 






        /* Role Management */  

        // Modules
    
        Route::get('/modules','App\Http\Controllers\AdminRoleController@viewModules');

        Route::get('/modules/datatables',['as'=>'modules.data','uses'=>'App\Http\Controllers\AdminRoleController@getModules']); 

        Route::post('/save/module','App\Http\Controllers\AdminRoleController@postModule');    

        Route::post('/edit/module','App\Http\Controllers\AdminRoleController@editModule');    

        // User Roles
    
        Route::get('/userroles','App\Http\Controllers\AdminRoleController@viewUserRoles');
        
        Route::get('/userroles/datatables',['as'=>'userroles.data','uses'=>'App\Http\Controllers\AdminRoleController@getUserRoles']);
        
        Route::post('/save/userroles','App\Http\Controllers\AdminRoleController@postUserRoles');
        
        Route::post('/edit/userroles','App\Http\Controllers\AdminRoleController@editUserRoles');

        //Role Admin Users
    
        Route::get('/roleusers','App\Http\Controllers\AdminRoleController@viewRoleUsers');
        
        Route::get('/roleusers/datatables',['as'=>'roleusers.data','uses'=>'App\Http\Controllers\AdminRoleController@getRoleUsers']);
        
        Route::post('/save/roleusers','App\Http\Controllers\AdminRoleController@postRoleUsers');
        
        Route::post('/edit/roleusers','App\Http\Controllers\AdminRoleController@editRoleUsers');

        // Role Module Mapping
    
        Route::get('/role_module_mapping','App\Http\Controllers\AdminRoleController@viewRoleModuleMapping');

        Route::get('/user_roles/datatables',['as'=>'user_roles.data','uses'=>'App\Http\Controllers\AdminRoleController@getUserRolesMapping']);

        Route::get('/role_module_mapping/datatables',['as'=>'role_mapping.data','uses'=>'App\Http\Controllers\AdminRoleController@getRoleModuleMapping']);

        Route::post('/save/role_access','App\Http\Controllers\AdminRoleController@postRoleModuleMapping');

        Route::get('/role_module_mapping/update_role_access','App\Http\Controllers\AdminRoleController@ViewRoleAccess');
           
        Route::get('get_modules','App\Http\Controllers\AdminRoleController@getModule');

        Route::get('/staff_module_mapping','App\Http\Controllers\AdminRoleController@ViewTeacherRoleAccess');

        // Role Class Mapping
    
        Route::get('/role_class_mapping','App\Http\Controllers\AdminRoleController@viewRoleClassMapping');

        Route::get('/role_class_mapping/datatables',['as'=>'role_class_mapping.data','uses'=>'App\Http\Controllers\AdminRoleController@getRoleClassMapping']);

        Route::post('/save/role_class_mapping','App\Http\Controllers\AdminRoleController@postRoleClassMapping');

        Route::post('/edit/role_class_mapping','App\Http\Controllers\AdminRoleController@editRoleClassMapping');
    });
    
});



Route::group(['prefix' => '/teachersold'], function () {

    Route::get('/', 'App\Http\Controllers\TeacherController@index');

    Route::get('/login', 'App\Http\Controllers\TeacherController@index');

    Route::post('/login', 'App\Http\Controllers\TeacherController@postLogin');

    Route::get('/home', 'App\Http\Controllers\TeacherController@homePage');

    Route::get('/profile', 'App\Http\Controllers\TeacherController@viewprofile');

    Route::post('/save/profile', 'App\Http\Controllers\TeacherController@updateProfile');

    Route::get('/logout', 'App\Http\Controllers\TeacherController@logout');

    //Student
    Route::post('/fetch-terms', 'App\Http\Controllers\TeacherController@fetchClassTerms');

    Route::post('/fetch-class-terms', 'App\Http\Controllers\TeacherController@fetchMappedClassTerms');

    Route::post('/fetch-section', 'App\Http\Controllers\TeacherController@fetchSection');
    
    Route::post('/fetch-section-all', 'App\Http\Controllers\TeacherController@fetchSectionAll');

    Route::post('/fetch-student', 'App\Http\Controllers\TeacherController@fetchStudent');

    Route::post('/fetch-class-exam', 'App\Http\Controllers\TeacherController@fetchExams');

    
    Route::post('/fetch-tests', 'App\Http\Controllers\TeacherController@fetchTest');

    Route::post('/fetch-sub-section', 'App\Http\Controllers\TeacherController@fetchSubjectSection');

    Route::post('/fetch-sub-subject', 'App\Http\Controllers\TeacherController@fetchSubjectMapped');

    Route::post('/fetch-class-subject', 'App\Http\Controllers\TeacherController@fetchClassSubjectMapped');

    Route::post('/fetch-class-chapter', 'App\Http\Controllers\TeacherController@fetchClassChapter');

    Route::get('/student', 'App\Http\Controllers\TeacherController@viewStudent');

    Route::post('/fetch-to-class', 'App\Http\Controllers\TeacherController@fetchClasses');
    
    Route::post('/fetch-questions', 'App\Http\Controllers\TeacherController@fetchQuestions');

    Route::post('/fetch-questions-type', 'App\Http\Controllers\TeacherController@fetchQuestionType');

    Route::get('/student/datatables', ['as' => 'studentlist.data', 'uses' => 'App\Http\Controllers\TeacherController@getStudents']);

    Route::post('/save/student', 'App\Http\Controllers\TeacherController@postStudent');

    Route::post('/edit/student', 'App\Http\Controllers\TeacherController@editStudent');

    //Home Work
    Route::get('/homework', 'App\Http\Controllers\TeacherController@viewHomework');

    Route::get('/homework/datatables', ['as' => 'homeworklist.data', 'uses' => 'App\Http\Controllers\TeacherController@getHomework']);

    Route::post('/save/homework', 'App\Http\Controllers\TeacherController@postHomework');

    Route::post('/edit/homework', 'App\Http\Controllers\TeacherController@editHomework'); 

    Route::post('/fetch-student-name', 'App\Http\Controllers\TeacherController@fetchStudents');

    Route::get('/testlist/views/{test_id}','App\Http\Controllers\TeacherController@viewHomeworkTestList');


    //Time table
    Route::post('/fetch-teacher-section', 'App\Http\Controllers\TeacherController@fetchSection');

    Route::get('/timetable', 'App\Http\Controllers\TeacherController@viewTimetable');

    Route::post('/load/timetable', 'App\Http\Controllers\TeacherController@loadTimetable');

    Route::post('/save/timetable', 'App\Http\Controllers\TeacherController@saveTimetable');

    
    //Promotions
    Route::get('/student_promotions', 'App\Http\Controllers\TeacherController@viewStudentPromotions');
    Route::get('/promotion', ['as' => 'promotion.data', 'uses' => 'App\Http\Controllers\TeacherController@loadStudentPromotions']);
    Route::post('/update/promotions', 'App\Http\Controllers\TeacherController@updateStudentPromotions');

    // Circulars
    Route::get('/circulars', 'App\Http\Controllers\TeacherController@viewCirculars');
    Route::get('/circulars/datatables', ['as' => 'circulars.data', 'uses' => 'App\Http\Controllers\TeacherController@getCirculars']);
    Route::post('/save/circulars', 'App\Http\Controllers\TeacherController@postCirculars');
    Route::post('/edit/circulars', 'App\Http\Controllers\TeacherController@editCirculars');

    //Students leave
    Route::get('/studentsleave', 'App\Http\Controllers\TeacherController@viewStudentLeave');

    Route::get('/edit_leave', 'App\Http\Controllers\TeacherController@editStudentLeave'); 

    Route::post('/edit/studentleave', 'App\Http\Controllers\TeacherController@updateStudentLeave');

    Route::get('/studentsleave/datatables', ['as' => 'studentsleave.data', 'uses' => 'App\Http\Controllers\TeacherController@getStudentLeave']);

    Route::get('/studentleave_excel',['as'=>'studentleave_excel.data','uses'=>'App\Http\Controllers\TeacherController@getExcelStudentLeave']);
    
    //Teacher leave
    Route::get('/tleave', 'App\Http\Controllers\TeacherController@viewTeacherLeave');

    Route::post('/edit/teacherleave', 'App\Http\Controllers\TeacherController@editTeacherLeave');

    Route::post('/save/teacherleave', 'App\Http\Controllers\TeacherController@postTeacherLeave');

    Route::get('/tleave/datatables', ['as' => 'teacherleave.data', 'uses' => 'App\Http\Controllers\TeacherController@getTeacherLeave']);

    Route::get('/tleavelist_excel',['as'=>'teacherleavelist_excel.data','uses'=>'App\Http\Controllers\TeacherController@getExcelTeacherLeave']);

    //    Route::get('/studentsleave/datatables', ['as' => 'studentsleave.data', 'uses' => 'App\Http\Controllers\TeacherController@getStudentLeave']);

    // Marks Entry
    Route::get('/marks_entry', 'App\Http\Controllers\TeacherController@viewMarksEntry');
    Route::post('/load/marks_entry', 'App\Http\Controllers\TeacherController@loadMarksEntry');
    Route::post('/update/marks_entry', 'App\Http\Controllers\TeacherController@updateMarksEntry');
    Route::post('/update/all_marks_entry', 'App\Http\Controllers\TeacherController@updateAllMarksEntry');

    //Attendance
    Route::get('/student_attendance', 'App\Http\Controllers\TeacherController@viewStudentAttendance');
    Route::post('/load/studentattendance', 'App\Http\Controllers\TeacherController@loadStudentAttendance');
    Route::get('/attendencereport/excel', ['as' => 'studattendence.excel', 'uses' => 'App\Http\Controllers\TeacherController@getStudentAttendenceExcel']);
    Route::post('/update/studentattendance','App\Http\Controllers\TeacherController@updateStudentAttendance');
    Route::get('/entryattendance/{id}/{year}/{class_id}/{section_id}', 'App\Http\Controllers\TeacherController@viewEditStudentsAttendance');

    // Daily Attendance

    Route::get('/student_dailyattendance', 'App\Http\Controllers\TeacherController@viewStudentDailyAttendance');
    Route::post('/update/studentdailyattendance','App\Http\Controllers\TeacherController@updateStudentDilayAttendance');
    Route::post('/load/studentdailyattendance', 'App\Http\Controllers\TeacherController@loadStudentDailyAttendance');
    Route::post('/fetch_attendance','App\Http\Controllers\TeacherController@fetchAttendance');
    Route::post('/save/dailyattendace','App\Http\Controllers\TeacherController@postDailyAttendance');

    //Academics
    Route::get('/student_academics', 'App\Http\Controllers\TeacherController@viewStudentAcademics');
    Route::get('/student_academics/datatables', ['as' => 'tstudent_academics.data', 'uses' => 'App\Http\Controllers\TeacherController@getStudentAcademics']);
    Route::post('/save/studentacademics', 'App\Http\Controllers\TeacherController@postStudentAcademics');
    Route::post('/edit/studentacademics', 'App\Http\Controllers\TeacherController@editStudentAcademics');

    // Events
    Route::get('/events', 'App\Http\Controllers\TeacherController@viewEvents');
    Route::post('/save/events', 'App\Http\Controllers\TeacherController@postEvents');
    Route::get('/events/datatables', ['as' => 'events.data', 'uses' => 'App\Http\Controllers\TeacherController@getEvents']);
    Route::post('/load/gallery','App\Http\Controllers\TeacherController@loadGallery');
    

    //student test

    

     // Test List
     Route::get('/testlist', 'App\Http\Controllers\TeacherController@viewTestlist');
     Route::get('/testlist/datatables', ['as' => 'testlist.data', 'uses' => 'App\Http\Controllers\TeacherController@getTestlist']);
     Route::get('/qbfrtest/datatables', ['as' => 'teacher_qbfrtest.data', 'uses' => 'App\Http\Controllers\TeacherController@getQuestionbankForTest']);
     Route::get('/add/testlist', 'App\Http\Controllers\TeacherController@addTest');
     Route::post('/view/qbfrtest', 'App\Http\Controllers\TeacherController@viewQbforTest');
     Route::post('/save/qbtest', 'App\Http\Controllers\TeacherController@saveQbTest');
     Route::get('/view/testlist', 'App\Http\Controllers\TeacherController@previewTest');
     Route::get('/edittestlist', 'App\Http\Controllers\TeacherController@editTest');
     Route::post('/update/updatetest', 'App\Http\Controllers\TeacherController@EditTestList');
     Route::get('/auto/testlist', 'App\Http\Controllers\TeacherController@addAutoTest');
     Route::post('/view/qbfrautotest', 'App\Http\Controllers\TeacherController@viewQbforAutoTest');
     Route::post('/save/qbautotest', 'App\Http\Controllers\TeacherController@saveQbAutoTest');

    Route::get('/testlistpapers', 'App\Http\Controllers\TeacherController@viewTestlistPapers');
    Route::get('/testlistpapers/datatables', ['as' => 'teacher_testlistpapers.data', 'uses' => 'App\Http\Controllers\TeacherController@getTestlistPapers']); 
    Route::get('/auto/testlistpapers', 'App\Http\Controllers\TeacherController@addAutoTestPapers');
    Route::post('/view/qbfrautotestpapers', 'App\Http\Controllers\TeacherController@viewQbforAutoTestPapers');
    Route::post('/save/qbautotestpapers', 'App\Http\Controllers\TeacherController@saveQbAutoTestPapers');
    Route::get('/view/testlistpapers', 'App\Http\Controllers\TeacherController@previewTestPapers');
 
     Route::get('/tstudentstestlist', 'App\Http\Controllers\TeacherController@viewStudentsTestlist');
    Route::get('/tstudentstestlist/datatables', ['as' => 'teacher_studentstestlist.data', 'uses' => 'App\Http\Controllers\TeacherController@getStudentsTestlist']);
    Route::get('/tstudenttestlist_excel', ['as' => 'teacher_studenttestlist_excel.data', 'uses' => 'App\Http\Controllers\TeacherController@getExcelStudentsTestlist']);
    Route::get('/view/tstudentstestlist/{id}', 'App\Http\Controllers\TeacherController@previewStudentsTest');
    Route::get('/edit/edit_tstudentstestlist/{id}', 'App\Http\Controllers\TeacherController@editStudentsTest');
    Route::post('/save/studentstestmarks', 'App\Http\Controllers\TeacherController@saveStudentsMark');


     // Question Banks
     Route::get('/questionbank', 'App\Http\Controllers\TeacherController@viewQuestionbank');
     Route::get('/questionbank/datatables', ['as' => 'teacher_questionbank.data', 'uses' => 'App\Http\Controllers\TeacherController@getQuestionbank']);
     Route::get('/add/questionbank', 'App\Http\Controllers\TeacherController@addQuestionbank');
     Route::post('/save/questionbank', 'App\Http\Controllers\TeacherController@postQuestionbank');
     Route::post('/clone/questiontype', 'App\Http\Controllers\TeacherController@cloneQuestiontype');
     Route::get('/view/questionbank', 'App\Http\Controllers\TeacherController@previewQuestionbank');
     Route::get('/edit/questionbank', 'App\Http\Controllers\TeacherController@editQuestionbank');
     Route::post('/delete/questionbank', 'App\Http\Controllers\TeacherController@deleteQuestionBank');
     Route::post('/delete/individualquestion', 'App\Http\Controllers\TeacherController@deleteIndividualQuestionBank');

     //Route::post('/export/questionbank', 'AdminController@exportQuestionbank');
 
     Route::post('/export/questionbank', ['as' => 'qb_excel.data', 'uses' => 'App\Http\Controllers\ImportExportController@getQuestionbankExcel']);
     Route::post('/import/questionbank', ['uses' => 'App\Http\Controllers\ImportExportController@importQuestionbankExcel']);


     Route::get('/studentattendancerep','App\Http\Controllers\TeacherController@viewStudentAttenReport');
     Route::post('/load/studentattendancerep', 'App\Http\Controllers\TeacherController@loadStudentAttendanceRep');
     Route::post('/update/studentattendancerep','App\Http\Controllers\TeacherController@updateStudentAttendanceRep');

     // Categories
    Route::get('/categories', 'App\Http\Controllers\TeacherController@viewCategories');

    Route::post('/save/categories', 'App\Http\Controllers\TeacherController@postCategories');

    Route::get('/categories/datatables', ['as' => 'categories.data', 'uses' => 'App\Http\Controllers\TeacherController@getCategories']);

    Route::post('/edit/categories', 'App\Http\Controllers\TeacherController@editCategories');

    // Background Themes
    Route::get('/bthemes', 'App\Http\Controllers\TeacherController@viewBackgroundThemes');

    Route::post('/save/bthemes', 'App\Http\Controllers\TeacherController@postBackgroundThemes');

    Route::get('/bthemes/datatables', ['as' => 'bthemes.data', 'uses' => 'App\Http\Controllers\TeacherController@getBackgroundThemes']);

    Route::post('/edit/bthemes', 'App\Http\Controllers\TeacherController@editBackgroundThemes');

    // Group
    Route::get('/group', 'App\Http\Controllers\TeacherController@viewGroup');

    Route::post('/save/group', 'App\Http\Controllers\TeacherController@postGroup');

    Route::get('/group/datatables', ['as' => 'group.data', 'uses' => 'App\Http\Controllers\TeacherController@getGroup']);

    Route::post('/edit/group', 'App\Http\Controllers\TeacherController@editGroup');

     // Communication posts

    Route::get('/posts', 'App\Http\Controllers\TeacherController@viewPosts');

    Route::post('/filter_things', 'App\Http\Controllers\TeacherController@filterThings'); 

    Route::post('/post_load_contents', 'App\Http\Controllers\TeacherController@postLoadModalContents'); 

    Route::get('/addposts', 'App\Http\Controllers\TeacherController@addPosts');

    Route::get('/editposts', 'App\Http\Controllers\TeacherController@editPosts');

    Route::post('/delete/posts', 'App\Http\Controllers\TeacherController@deletePosts');

    Route::get('/poststatus', 'App\Http\Controllers\TeacherController@viewPostStatus');

    Route::get('/poststatus/datatables', ['as' => 'poststatus.data', 'uses' => 'App\Http\Controllers\TeacherController@getPostStatus']);

    Route::get('/poststatus_excel',['as'=>'poststatus_excel.data','uses'=>'App\Http\Controllers\AdminController@getPostStatusExcel']);

    Route::get('/communication', 'App\Http\Controllers\TeacherController@viewCommunications');
    Route::post('/post_new_message', 'App\Http\Controllers\TeacherController@postCommunication');
    Route::post('/post_update_message', 'App\Http\Controllers\TeacherController@postCommunication');

    //communication sms

    Route::get('/postsms', 'App\Http\Controllers\TeacherController@viewPostSms');

    Route::get('/addpostsms', 'App\Http\Controllers\TeacherController@addPostSms');

    Route::get('/editpostsms', 'App\Http\Controllers\TeacherController@editPostSms');
    
    Route::post('/post_new_sms_scholar', 'App\Http\Controllers\TeacherController@postCommunicationSmsScholar');
     
    Route::post('/delete/postsms', 'App\Http\Controllers\TeacherController@deletePostSms'); 

    Route::get('/postsmsstatus', 'App\Http\Controllers\TeacherController@viewPostSmsStatus');

    Route::get('/postsmsstatus/datatables', ['as' => 'postsmsstatus.data', 'uses' => 'App\Http\Controllers\TeacherController@getPostSmsStatus']);
});

Route::group(['prefix' => '/*'], function () {
    Route::get('/*', 'App\Http\Controllers\AdminController@page404');
});

Route::group(['prefix' => '{slugname}/*'], function () {
    Route::get('/*', 'App\Http\Controllers\AdminController@page404');
});