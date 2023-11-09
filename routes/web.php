<?php

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


// Разработчик, администратор, врач
Route::group(['middleware' => ['auth', 'fullAccess_plusdoctor']], function () {
	
	Route::post('/patientcard/delete_patient', 'MainController@deletePatient');
	
    Route::get('/guides', 'GuidesController@showGuidesForm');
	Route::post('/guides/addanimaltype', 'GuidesController@addNewAnimaltype');
	Route::post('/guides/addanimalbreed', 'GuidesController@addNewAnimalbreed');
	Route::post('/guides/addanimalcolor', 'GuidesController@addNewAnimalcolor');
	
	Route::post('/guides/newtemplate', 'GuidesController@newTemplate');	
	Route::get('/guides/gettemplateslist', 'GuidesController@getTemplatesList');
	Route::post('/guides/deletetemplate', 'GuidesController@deleteTemplate');
	
	Route::post('/guides/adddiagnosis', 'GuidesController@addDiagnosis');
	Route::post('/guides/changediagnosis', 'GuidesController@changeDiagnosis');
	
	Route::post('/guides/addvacine', 'GuidesController@addVacine');
	Route::post('/guides/changevacine', 'GuidesController@changeVacine');
		
	Route::post('/guides/addproductcategory', 'GuidesController@addProductCategory');
	Route::get('/guides/getproductcategories', 'GuidesController@getProductCategories');
	Route::post('/guides/deleteproductcategory', 'GuidesController@deleteProductCategory');
	Route::post('/guides/addproduct', 'GuidesController@addProduct');
	Route::post('/guides/changeproduct', 'GuidesController@changeProduct');
	Route::get('/guides/getproducts', 'GuidesController@getProducts');
	Route::post('/guides/deleteproduct', 'GuidesController@deleteProduct');
	
	Route::post('/guides/addservicecategory', 'GuidesController@addServiceCategory');
	Route::get('/guides/getservicecategories', 'GuidesController@getServiceCategories');
	Route::post('/guides/deleteservicecategory', 'GuidesController@deleteServiceCategory');	
	Route::post('/guides/addservice', 'GuidesController@addService');
	Route::post('/guides/changeservice', 'GuidesController@changeService');
	Route::get('/guides/getservices', 'GuidesController@getServices');
	Route::post('/guides/deleteservice', 'GuidesController@deleteService');
	
	Route::post('/guides/newanalisystemplate', 'GuidesController@newAnalisysTemplate');	
	Route::get('/guides/getanalisystemplateslist', 'GuidesController@getAnalisysTemplatesList');		
	Route::post('/guides/deleteanalisystemplate', 'GuidesController@deleteAnalisysTemplate');
			
	Route::post('/patientcard/delete_visit', 'PatientCard@deleteVisit');
	Route::post('/patientcard/delete_research', 'PatientCard@deleteResearch');
	Route::post('/patientcard/delete_analysis', 'PatientCard@deleteAnalysis');
	Route::post('/patientcard/delete_vacine', 'PatientCard@deleteVacine');
	Route::post('/patientcard/delete_bill', 'PatientCard@deleteBill');
	
	Route::get('/patientcard/get_pays_list', 'PatientCard@getPaysList');

	Route::get('/patientcard/get_salary_data', 'PatientCard@getSalaryData');
	
});


// Разработчик, администратор
Route::group(['middleware' => ['auth', 'fullAccess']], function () {
				
	Route::get('/staff', 'StaffController@showStaffPage');
	Route::get('/register', 'StaffController@register');
	Route::get('/staff/showusers', 'StaffController@showUsers');
	Route::post('/staff/delete_user', 'StaffController@deleteUser');
	Route::post('/staff/change_user', 'StaffController@changeUser');
	Route::post('/staff/change_login_password', 'StaffController@changeLoginPassword');
	Route::post('/staff/deny_access', 'StaffController@denyAccess');
	
});


// Только разработчик
Route::group(['middleware' => ['auth', 'superadmin']], function () {
	Route::get('/guides/importanimaltypes', 'GuidesController@importAnimaltypes');
	Route::get('/guides/importanimalbreeds', 'GuidesController@importAnimalbreeds');
	Route::get('/guides/importanimalcolors', 'GuidesController@importAnimalcolors');
	Route::get('/guides/importpatientsandclients', 'GuidesController@importPatientsandClients');
	Route::post('/guides/setexpdate', 'GuidesController@setExpDate');
	Route::get('/guides/getexpdate', 'GuidesController@getExpDate');
	Route::get('/guides/importdiagnosis', 'GuidesController@importDiagnosis');
	Route::get('/guides/importvisits', 'GuidesController@importVisits');
	Route::get('/guides/importproducts', 'GuidesController@importProducts');
	Route::get('/guides/importservices', 'GuidesController@importServices');
	Route::get('/guides/importtemplates', 'GuidesController@importTemplates');
	Route::get('/guides/importanalisystemplates', 'GuidesController@importAnalisysTemplates');
	Route::get('/guides/importanalisys', 'GuidesController@importAnalisys');

});


// Все авторизированные
Route::group(['middleware' => 'auth'], function () {
	
	Route::get('/', 'MainController@mainPage');

	Route::get('/searchpatients', 'MainController@searchPatients');

	Route::get('/addanimal', 'MainController@showAddDogForm');
	Route::post('/addanimal/client/add', 'MainController@saveClient');
	Route::get('/addanimal/client/search', 'MainController@clientsForSelect');
	Route::get('/addanimal/client/searchone', 'MainController@searchOneClient');

	Route::get('/patientcard', 'MainController@openPatientCard');
	Route::get('/patientcard/searchpatient', 'MainController@searchOnePatient'); 
	Route::post('/patientcard/change_patient_data', 'MainController@changePatientData');
	
	Route::post('/patientcard/change_client_data', 'MainController@changeClientData');
	Route::post('/patientcard/new_client', 'MainController@newClient');
	Route::post('/patientcard/change_client', 'MainController@changeClient');
	
	Route::get('/guides/animaltype_search', 'GuidesController@searchAnimalTypes');
	Route::get('/guides/animalbreed_search', 'GuidesController@searchAnimalBreeds');
	Route::get('/guides/animalcolor_search', 'GuidesController@searchAnimalColors');
	Route::get('/guides/diagnosis_search', 'GuidesController@searchDiagnosis');

	Route::get('/staff/getworkerdata', 'StaffController@getWorkerData');
	Route::get('/staff/search', 'StaffController@searchStaff');
	
	Route::post('/patientcard/upload_photo', 'PatientCard@uploadPhoto');
	Route::get('/patientcard/get_photo_for_visit', 'PatientCard@getPhotoforVisit');
	Route::get('/patientcard/get_photo_for_patient', 'PatientCard@getPhotoforPatient');
	Route::post('/patientcard/delete_photo', 'PatientCard@deletePhoto');
	
	Route::get('/patientcard/get_weights', 'PatientCard@getWeights');
	
	Route::post('/patientcard/new_visit', 'PatientCard@newVisit');
	Route::get('/patientcard/get_visits_list', 'PatientCard@getVisitsList');	
	Route::get('/patientcard/get_one_visit', 'PatientCard@getOneVisit');
	Route::get('/patientcard/get_visit_data', 'PatientCard@getVisitData');

	Route::get('/patientcard/visits_for_select', 'PatientCard@visitsForSelect');
	
	Route::post('/patientcard/new_research', 'PatientCard@newResearch');	
	Route::get('/patientcard/get_researches_list', 'PatientCard@getResearchesList');
	Route::get('/patientcard/get_one_research', 'PatientCard@getOneResearch');
	Route::get('/patientcard/get_research_data', 'PatientCard@getResearchData');
	
	Route::post('/patientcard/new_analysis', 'PatientCard@newAnalysis');
	Route::get('/patientcard/get_analysis_list', 'PatientCard@getAnalysisList');
	Route::get('/patientcard/get_one_analysis', 'PatientCard@getOneAnalysis');
	Route::get('/patientcard/get_analysis_data', 'PatientCard@getAnalysisData');
	
	Route::get('/guides/vacine_search', 'GuidesController@searchVacine');
	Route::post('/patientcard/new_vacine', 'PatientCard@newVacine');
	Route::get('/patientcard/get_vacines_list', 'PatientCard@getVacinesList');
	Route::get('/patientcard/get_vacine_data', 'PatientCard@getVacineData');
	
	Route::get('/guides/product_search', 'GuidesController@searchProduct');
	Route::get('/guides/one_product_data', 'GuidesController@oneProductData');
	
	Route::get('/guides/service_search', 'GuidesController@searchService');
	Route::get('/guides/one_service_data', 'GuidesController@oneServiceData');
	
	Route::get('/patientcard/patientsforbills', 'PatientCard@patientsForBills');
	Route::post('/patientcard/new_bill', 'PatientCard@newBill');
	Route::get('/patientcard/get_bills_list', 'PatientCard@getBillsList');
	Route::get('/patientcard/get_one_bill', 'PatientCard@getOneBill');
	Route::get('/patientcard/get_bill_data', 'PatientCard@getBillData');
	Route::post('/patientcard/pay_all_bills', 'PatientCard@payAllBills');
	
	

	Route::get('/guides/gettemplates', 'GuidesController@getTemplates');
	Route::get('/guides/gettemplatedata', 'GuidesController@getTemplateData');
	
	Route::get('/guides/getanalisystemplates', 'GuidesController@getAnalisysTemplates');
	Route::get('/guides/getanalisystemplatedata', 'GuidesController@getAnalisysTemplateData');
		
	Route::get('/bills', 'MainController@showBillsPage');
	
});


Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

//Route::get('/home', 'HomeController@index')->name('home');



