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

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], 'register', function(){
    return redirect('/');
});

Auth::routes();

//Route::get('/', 'HomeController@index')->name('home');
Route::get('/dashboard', 'HomeController@index')->name('home');

//Reports
Route::get('report/expenses', 'ReportController@expense');

//Expense
Route::get('user-deposit/payeename', 'DepositController@payeename');
Route::get('user-deposit/payeebalance', 'DepositController@payeeBalance');

//Deposits
Route::post('/deposits/return/{id}', 'DepositController@return');
Route::resource('/deposits', 'DepositController');

//Payment Vendor
Route::get('payment-vendors/ajax','PaymentVendorController@id_ajax');
Route::post('/payment-vendors/return/{id}', 'PaymentVendorController@return');
Route::resource('/payment-vendors', 'PaymentVendorController');

//Payment Others
Route::resource('payment/others', 'PaymentOtherController');
Route::post('cheques/payment', 'PaymentOtherController@payment');
Route::post('cheques/change-status', 'PaymentOtherController@changeStatus');

//Payment NEFT
Route::resource('payment/nefts', 'PaymentNeftController');
Route::post('cashs/payment', 'PaymentNeftController@payment');
Route::get('cashs/change-status/{id}', 'PaymentNeftController@changeStatus');

//Payment History
Route::resource('payment/history', 'PaymentHistoryController');

//User Transaction
Route::resource('user-transactions', 'UserTransactionController');

//Expense
Route::get('expenses/partyname', 'ExpenseController@partyname');
Route::get('expenses/partygstin', 'ExpenseController@partyGSTIN');
Route::get('expenses/paid/{id}', 'ExpenseController@changetopaid');
Route::get('expenses/cancel/{id}', 'ExpenseController@cancel');
Route::resource('/expenses', 'ExpenseController');

//Sub Expense Category
Route::get('/expense-categories/ajax','ExpenseCategoryController@id_ajax');
Route::get('/subexpenses/ajax','SubExpenseController@id_ajax');



Route::group(['middleware' => 'CheckAdmin'], function() {
	
	//Reports
	Route::get('/report/deposits', 'ReportController@deposit');
	//Route::get('/report/expenses', 'ReportController@expense');
	Route::get('/report/assets', 'ReportController@asset');
	//Route::get('/report/ledgers', 'ReportController@ledger');
	//Route::get('/report/assets/expiry', 'ReportController@expiry');
	//Route::get('/report/overall', 'ReportController@overall');
	// Route::get('/report/datatable', 'ReportController@datatable_ajax');

	//Asset
	Route::get('assets/old/ajax_voucher_no', 'AssetController@ajax_voucher_no');
	Route::get('assets/new/ajax_voucher_no', 'AssetNewController@ajax_voucher_no');
	Route::resource('/assets/new', 'AssetNewController');
	Route::resource('/assets/old', 'AssetController');

	//Users
	Route::get('users/activate-user/{id}','UserController@activateUser');
	Route::get('/users/ajax','UserController@id_ajax');
	Route::resource('/users', 'UserController');
	
	//Workshop
	Route::get('/workshops/ajax','WorkshopController@id_ajax');

	//Bank
	Route::get('banks/ajax','BankController@id_ajax');
	Route::resource('banks','BankController');

	//Vendor
	Route::resource('vendors','VendorController');

	//Sub Sub Expense Category
	//Route::get('/subsubexpenses/ajax','SubSubExpenseController@id_ajax');
	
	//Sub Asset Category
	Route::get('/subassets/ajax','SubAssetController@id_ajax');

	Route::group(['middleware' => 'CheckSuperUser'], function() {

		//company
		Route::resource('/companies', 'CompanyController');
		
		//Workshop
		Route::resource('/locations', 'WorkshopController');

		//Location - Ajax
		//Route::get('/locations/ajax','LocationController@id_ajax');
		//Route::resource('/locations', 'LocationController');

		//Department
		Route::resource('/descriptions', 'DescriptionController');

		//Tax
		Route::resource('/taxes', 'TaxController');

		//Designation
		Route::resource('/designations', 'DesignationController');

		//Employee(user) Type
		Route::resource('/employee-types', 'EmployeeTypeController');

		//Expense Category
		Route::resource('expense-categories', 'ExpenseCategoryController');

		//Sub Expense Category
		Route::resource('sub-expense-categories', 'SubExpenseController');

		//Sub Sub Expense Category - Ajax
		//Route::resource('expense-categories/expense-category', 'SubSubExpenseController');
		
		//Purchase Category
		Route::resource('/purchase-categories', 'PurchaseCategoryController');
		
		//Asset Category
		Route::resource('/asset-categories', 'AssetCategoryController');

		//Sub Asset Category
		Route::resource('/subassets', 'SubAssetController');
		
		//Sub Purchase Category
		//Route::get('/subpurchase/ajax','SubPurchaseController@id_ajax');
		//Route::resource('/subpurchase', 'SubPurchaseController');

		//Employees
		//Route::get('/employees/ajax','EmployeeController@id_ajax');
		//Route::resource('/employees', 'EmployeeController');
		
	});

});


