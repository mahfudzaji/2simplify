<?php

/*
$links=
    [
        ''=>'controllers/index.php',
        'hello/world'=>'controllers/hello.php',
        'login'=>'controllers/login.php',
        'welcome'=>'controllers/welcome.php',
    ];

1. split/explode parameter, delimiter by @
2. parameter[0] = file yang dituju
3. parameter[1] = method yang dituju

$router->post('login', 'controllers/AuthController@login');
$router->get('logout', 'controllers/AuthController@logout');
$router->post('register', 'controllers/AuthController@register');

$router->get('','controllers/index.php');
$router->get('hello/world','controllers/hello.php');
$router->get('welcome','controllers/welcome.php');
$router->get('logout','controllers/logout.php');

$router->post('login','controllers/login.php');
$router->post('register','controllers/register.php');
*/

//user
$router->post('login', 'UserController@login');
$router->post('register', 'UserController@register');
$router->get('logout', 'UserController@logout');
$router->post('toggleUserStatus', 'UserController@toggleUserStatus');
$router->post('confirmation','UserController@userConfirmation');

//reset
$router->post('forget', 'UserController@forgetPassword');
$router->post('reset', 'UserController@resetPassword');
$router->post('registerFirstUser', 'UserController@registerFirstUser');

//home
$router->get('home','HomeController@index');

//page
$router->get('', 'PageController@index');
$router->get('login','PageController@login');
//$router->get('register','PageController@register');

//confirmation email and make new password
$router->get('confirmation', 'PageController@userConfirmation');
//resetting your password
$router->get('reset', 'PageController@reset');
//if forget the password
$router->get('forget', 'PageController@forget');

//partner
$router->get('partner', 'PartnerController@index' );
$router->post('partner/create', 'PartnerController@create');
$router->get('partner/detail', 'PartnerController@detail');
$router->post('partner/status', 'PartnerController@toggleStatus');
$router->post('partner/update', 'PartnerController@update');

//FORM

////notes
$router->get('form/notes', 'FormController@showDocumentNotes');
$router->post('form/notes/create','FormController@documentNotesCreate');

////attachment
$router->get('attachment', 'FormController@showDocumentAttachment');
$router->get('dropdown-attachment', 'FormController@showDropDownAttachment');
$router->post('attachment', 'FormController@createDocumentAttachment');

////receive form
$router->get('form', 'FormController@index');
$router->get('form/tanda-terima', 'FormController@receiveFormIndex');
$router->get('form/tanda-terima/detail', 'FormController@receiveFormDetail');
$router->post('form/tanda-terima/create', 'FormController@receiveFormCreate');
$router->post('form/tanda-terima/update', 'FormController@receiveFormUpdate');

////activity report
$router->get('form/activity-report', 'FormController@arIndex');
$router->get('form/activity-report/detail', 'FormController@arDetail');
$router->post('form/activity-report/create', 'FormController@arCreate');
$router->post('form/activity-report/update', 'FormController@arUpdate');
$router->post('form/activity-report/close', 'FormController@arClose');

////cuti
$router->get('form/cuti', 'FormController@cutiFormIndex');
$router->get('form/cuti/detail', 'FormController@cutiFormDetail');
$router->get('form/cuti/approve', 'FormController@cutiFormApproval');
$router->post('form/cuti/create', 'FormController@cutiFormCreate');
$router->post('form/cuti/update', 'FormController@cutiFormUpdate');

////reimburse
$router->get('form/reimburse', 'FormController@reimburseFormIndex');
$router->get('form/reimburse/detail', 'FormController@reimburseFormDetail');
$router->post('form/reimburse/approve', 'FormController@reimburseFormApproval');
$router->post('form/reimburse/create', 'FormController@reimburseFormCreate');
$router->post('form/reimburse/update', 'FormController@reimburseFormUpdate');
$router->post('form/reimburse/remove', 'FormController@reimburseFormItemRemove');

////receipt
$router->get('form/receipt', 'FormController@receiptFormIndex');
$router->get('form/receipt/detail', 'FormController@receiptFormDetail');
$router->post('form/receipt/create', 'FormController@receiptFormCreate');
$router->post('form/receipt/update', 'FormController@receiptFormUpdate');
$router->post('form/receipt/update-item', 'FormController@receiptFormItemUpdate');
$router->post('form/receipt/remove-item', 'FormController@receiptItemRemove');
$router->post('form/receipt/remove', 'FormController@receiptFormRemove');
$router->post('form/receipt/new-item', 'FormController@receiptFormCreateNewItem');
$router->post('form/receipt/approve', 'FormController@receiptFormApproval');
$router->post('form/receipt/create-from-quo', 'FormController@receiptFormCreateFromQuo');
$router->get('form/receipt/get-number', 'FormController@receiptFormNumber');


////quo
$router->get('form/quo', 'FormController@quoFormIndex');
$router->get('form/quo/detail', 'FormController@quoFormDetail');
$router->post('form/quo/create', 'FormController@quoFormCreate');
$router->post('form/quo/update', 'FormController@quoFormUpdate');
$router->post('form/quo/remove', 'FormController@quoFormRemove');
$router->post('form/quo/update-item', 'FormController@quoFormItemUpdate');
$router->post('form/quo/remove-item', 'FormController@quoFormItemRemove');
$router->post('form/quo/new-item', 'FormController@quoFormCreateNewItem');
$router->post('form/quo/approve', 'FormController@quoFormApproval');
$router->post('form/quo/create-revision', 'FormController@quoFormCreateRevision');
$router->get('form/quo/get-number', 'FormController@quoFormNumber');

////po
$router->get('form/po', 'FormController@poFormIndex');
$router->get('form/po/detail', 'FormController@poFormDetail');
$router->post('form/po/create', 'FormController@poFormCreate');
$router->post('form/po/update', 'FormController@poFormUpdate');
$router->post('form/po/remove', 'FormController@poFormRemove');
$router->post('form/po/new-item', 'FormController@poFormCreateNewItem');
$router->post('form/po/update-item', 'FormController@poItemUpdate');
$router->post('form/po/remove-item', 'FormController@poItemRemove');
$router->post('form/po/approve', 'FormController@poFormApproval');
$router->post('form/po/create-from-quo', 'FormController@poFormCreateFromQuo');
$router->get('form/po/get-number', 'FormController@poFormNumber');

////do
$router->get('form/do', 'FormController@doFormIndex');
$router->post('form/do/create', 'FormController@doFormCreate');
$router->get('form/do/detail', 'FormController@doFormDetail');
$router->post('form/do/approve', 'FormController@doFormApproval');
$router->post('form/do/update', 'FormController@doFormUpdate');
$router->post('form/do/remove', 'FormController@doFormRemove');

//PRODUCTS AND STOCKS
$router->get('product', 'PAController@index');
$router->get('product/category', 'PAController@category');
/* $router->get('product/vendor', 'PAController@vendor'); */
$router->get('product/asset/detail', 'PAController@detailAsset');
$router->post('product/create-product', 'PAController@createProduct');
$router->post('product/update-product', 'PAController@updateProduct');
$router->post('product/create-category', 'PAController@createCategory');
$router->post('product/update-category', 'PAController@updateCategory');
/*$router->post('product/vendor/create', 'PAController@createVendor');
$router->post('product/asset/create', 'PAController@createAsset');
$router->post('product/asset/remove', 'PAController@removeAsset');*/
$router->post('product/asset/update', 'PAController@updateAsset'); 

////STOCKS
$router->get('stock', 'StockController@index');
$router->get('stock/history', 'StockController@stockHistory');
$router->get('stock/getProduct', 'StockController@getProduct');
$router->get('stock/getProductDetail', 'StockController@getProductDetail');
$router->post('stock/new-stock', 'StockController@stockAdd');
$router->post('stock/stock-create-from-form', 'StockController@stockCreateFromForm');
$router->get('stock/get-serial-number', 'StockController@stockSerialNumber');
$router->get('stock/get-stock-list', 'StockController@getStockList');
$router->get('stock/check-stock-available', 'StockController@checkStock');
$router->get('stock/check-stock-category', 'StockController@checkStockByCategory');
$router->get('stock/detail', 'StockController@stockDetail');


//tested
$router->post('stock/in', 'StockController@stockIn');
$router->post('stock/update', 'StockController@stockUpdate');

//SETTINGS
$router->get('settings', 'SettingController@index');
$router->post('settings/profile/update', 'SettingController@profileUpdate');
$router->post('settings/user/update', 'SettingController@userUpdate');

//PROJECT
$router->get('project', 'ProjectController@index');
$router->post('project/create', 'ProjectController@projectCreate');
$router->get('project/detail', 'ProjectController@projectDetail');
$router->post('project/update', 'ProjectController@projectUpdate');
$router->post('project/remove', 'ProjectController@projectRemove');
$router->post('project/new-request', 'ProjectController@projectNewRequest');
$router->post('project/update-item', 'ProjectController@projectUpdateItem');
$router->post('project/remove-item', 'ProjectController@projectRemoveItem');
$router->post('project/update-status', 'ProjectController@projectUpdateStatus');
$router->get('project/get-project-item', 'ProjectController@projectItem');


////ENGINEERING
$router->get('engineering', '');
/*
projects
ticketing system
visiting customer/troubleshooting (result: activity report)
daily activity & planning activity
*/

//NOTIFICATION
$router->get('notification', 'NotificationController@index');

//UPLOAD
$router->get('upload', 'UploadController@index');
$router->post('upload', 'UploadController@upload');
$router->post('remove-upload', 'UploadController@remove');

//ACTIVITY HISTORY
$router->get('activity-history', 'FormController@showActivityHistory');

//404
$router->get('404', 'PageController@pageNotFound');

//PRINT
$router->get('print/receive-form', 'PrintController@receiveForm');
$router->get('print/activity-report', 'PrintController@arForm');
$router->get('print/vacation', 'PrintController@vacationForm');
$router->get('print/reimburse', 'PrintController@reimburseForm');
$router->get('print/quotation', 'PrintController@quotationForm');
$router->get('print/po', 'PrintController@poForm');
$router->get('print/do', 'PrintController@doForm');
$router->get('print/receipt', 'PrintController@receiptForm');

//SERBA-SERBI
$router->get('parameter', 'PageController@parameterShow');
$router->post('activity/create', 'HomeController@activityCreate');
$router->post('event/create', 'HomeController@eventCreate');
$router->post('event/update', 'HomeController@eventUpdate');

//TESTING
$router->get('test', 'PageController@testing');
$router->get('save-ciphertext', 'PageController@saveCipherText');
$router->get('show-ciphertext', 'PageController@showCipherText');
?>