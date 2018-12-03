<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\App;

class FormController{

    private $role, $roleOfUser;

    private $placeholderReceiveForm=array(
        //receive form
        [
        "receive_date"=>"required",  
        "submitted"=>"required",
        "received"=>"required",
        "requisite"=>"required",
        "service_point"=>"required",      
        "remark"=>''
        ],
        //stock
        [
        "product"=>"required",
        "serial_number"=>"required",
        "stock_condition"=>"required",
        "service_point"=>"required"
        ]
    );

    private $placeholderArForm=array(
        "customer" => "required",
        "project_name" => "",
        "activity_date" => "required",
        "activity" => "required",
        "next_activity" => "",
        "target_completed" => "",
        "remark" => ""
    );

    private $placeholderNotesForm=array(
        "document_number"=>"required",
        "document_type"=>"required",
        "notes"=>"required"
    );

    private $placeholderAttachmentForm=array(
        "document_data" => 'required',
        "description" => ''
    );

    private $placeholderVacationForm=array(
        "requisite" => 'required',
        "verified_by" => 'required',
        "approved_by" => 'required',
        "remark" => ''
    );

    private $placeholderReimburseForm=array(
        //input to db: reimburse_detail
        [
        "requisite" => 'required',
        "receipt_date" => 'required',
        "cost" => 'required',
        "remark" => ''
        ],
        //input to db: form_reimburse
        [
        "verified_by" => 'required',
        "approved_by" => 'required'
        ]
    );

    private $placeholderPoForm=array(
        //insert to db: form_po
        [
            "supplier" => 'required',
            "pic_supplier" => 'required',
            "buyer" => 'required',
            "pic_buyer" => 'required',
            "doc_date" => 'required',
            "currency" => 'required',
            "ppn" => 'required',
            "acknowledged_by" => 'required',
            "approved_by" => 'required',
            "remark" => ''
        ],
        //insert to db: po_product
        [
            "product" => 'required',
            "quantity" => 'required',
            "price_unit" => 'required',
            "item_discount" => ''
        ]
    );

    private $placeholderDoForm=array(
        "po_quo" => 'required',
        "do_date" => 'required',
        "delivered_by" => 'required',
        "received_by" => '',
        "remark" => '',
        "approved_by" => 'required'
    );
    
    private $placeholderReceiptForm=array(
        //insert to db: form_receipt
        [
            "supplier" => 'required',
            "buyer" => 'required',
            "receipt_date" => 'required',
            "currency" => 'required',
            "ppn" => 'required',
            "remark" => ''
        ],
        //insert to db: receipt_stock
        [
            "product" => 'required',
            "quantity" => 'required',
            "price" => 'required',
            "discount" => ''
        ]
    );

    public function __construct(){
        $user=Auth::user();

        $userId=$user[0]->id;

        $this->role = App::get('role');
        
        $this->roleOfUser = $this->role -> getRole($userId);
        
    }

    public function index(){

        view("form/index");
    
    }

    public function showActivityHistory(){
        
        if(!$this->role->can("view-activity-history")){
            redirectWithMessage(["Anda tidak memiliki hak untuk melihat activity history", 0],'/form/tanda-terima');
        }
        
        $documentDataId=$_GET['document_data'];
    }

//=====================================================================================================//

    /* ACTIVITY REPORT */
    public function arIndex(){

        if(!$this->role->can("view-activity-report")){
            redirectWithMessage([[ returnMessage()['activityReport']['accessRight']['view'] , 0]],'/home');
        }

        $builder = App::get('builder');

        $customers=$builder->getAllData('companies', 'Partner');

        /*         
        $activityReport = $builder->custom("SELECT a.id, b.name as customer, 
        a.activity, a.next_activity, 
        case a.active when 1 then 'Problem solved' else 'Not solved yet' end as status, 
        date_format(a.activity_date, '%d %M %Y') as activity_date, 
        date_format(a.created_at, '%d %M %Y') as created_at, 
        date_format(a.updated_at, '%d %M %Y') as updated_at 
        FROM `form_ar` as a 
        INNER JOIN companies as b on a.customer=b.id 
        ORDER BY a.activity_date", "Document"); 
        */

        //=====================//

        //Searching for specific category
        $whereClause='';
        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            $search['customer']=filterUserInput($_GET['customer']);

            $searchByDateStart=filterUserInput($_GET['activity_report_start']);
            $searchByDateEnd=filterUserInput($_GET['activity_report_end']);

            $operator='&&';

            foreach($search as $k => $v){
                if(!empty($search[$k])){
                    $whereClause.=$k."=".$v.$operator;
                }
            }

            if(!empty($searchByDateStart) && !empty($searchByDateEnd)){
                $whereClause.=" activity_date between '$searchByDateStart' and '$searchByDateEnd'";
            }elseif(!empty($searchByDateStart)){
                $whereClause.=" activity_date='$searchByDateStart'";
            }elseif(!empty($searchByDateEnd)){
                $whereClause.=" activity_date='$searchByDateEnd'";
            }
            //dd($whereClause);
            $whereClause=trim($whereClause, '&&');

        }

        if($whereClause==''){
            $whereClause=1;
        }

        $activityReport=$builder->custom("SELECT a.id, 
            b.name as customer, 
            a.activity, 
            a.next_activity, 
            case a.active when 1 then 'Problem solved' else 'Not solved yet' end as status, 
            date_format(a.activity_date, '%d %M %Y') as activity_date, 
            date_format(a.created_at, '%d %M %Y') as created_at, 
            date_format(a.updated_at, '%d %M %Y') as updated_at,
            c.name as created_by,
            a.pic
            FROM `form_ar` as a 
            inner join companies as b on a.customer=b.id
            inner join users as c on a.created_by=c.id 
            where $whereClause 
            order by a.activity_date desc",'Document');

        //download all the data
        if(isset($_GET['download']) && $_GET['download']==true){
            
            $dataColumn = ['activity_date', 'customer', 'activity', 'next_activity', 'status', 'created_by', 'pic'];

            $this->download(toDownload($activityReport, $dataColumn));

        }

        //Pagination
        //only show data for specified page
        if(isset($_GET['p'])){
            $p=$_GET['p'];

            if(!is_numeric($p)){
                redirectWithMessage([[ returnMessage()['pagination']['unknown'] ,0]],getLastVisitedPage());
            }
            
        }else{
            $p=1;
        }

        $limitStart=$p*maxDataInAPage()-maxDataInAPage();

        $pages=ceil(count($activityReport)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($activityReport);
        
        $activityReport=array_slice($activityReport,$limitStart,maxDataInAPage());

        //======================//

        view("form/activity_form", compact('activityReport', 'customers', 'pages', 'sumOfAllData'));
    }

    public function arCreate(){
        if(!$this->role->can("create-activity-report")){
            redirectWithMessage([[ returnMessage()['activityReport']['accessRight']['create'] , 0]],getLastVisitedPage());
        }

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderArForm as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        $data['created_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }

        $builder = App::get("builder");
        $insertToArForm = $builder->insert('form_ar', $data);

        $data['document_number']=$builder->getPdo()->lastInsertId();
        //id of activity report document
        $data['document']=2;

        $insertToDocumentData=$builder->insert('document_data',['document_number'=>$data['document_number'], 'document'=>$data['document']]);

        if(!$insertToArForm && !$insertToDocumentData){
            recordLog('Activity report', returnMessage()['activityReport']['createFail'] );
            redirect(getLastVisitedPage());
            exit();
        }else{
            recordLog('Activity report', returnMessage()['activityReport']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['activityReport']['createSuccess'] ,1]],getLastVisitedPage());

    }

    public function arDetail(){
        if(!$this->role->can("view-activity-report")){
            redirectWithMessage([["Anda tidak memiliki hak untuk melihat daftar laporan aktivitas", 0]],'/home');
        }

        $id = filterUserInput($_GET['ar']);

        $builder = App::get('builder');

        $arData = $builder->custom("SELECT a.customer as idcustomer, 
        a.activity_date as acd,
        a.target_completed as tcd, 
        a.id, 
        b.name as customer, 
        b.code, 
        date_format(a.activity_date, '%d %M %Y') as activity_date, 
        a.activity, 
        ifnull(case a.project_name when '' then '-' else a.project_name end, '-') as project_name,
        ifnull(case a.next_activity when '' then '-' else a.next_activity end, '-') as next_activity,
        ifnull(case a.remark when '' then '-' else a.remark end, '-') as remark,
        case a.active when 1 then 'already solved' else 'Not solved yet' end as status, 
        c.name as created_by, 
        d.name as updated_by,
        date_format(a.target_completed, '%d %M %Y') as target_completed, 
        date_format(a.created_at, '%d %M %Y') as created_at, 
        date_format(a.updated_at, '%d %M %Y') as updated_at, 
        e.id as ddata,
        f.code as docCode
        FROM `form_ar` as a 
        inner join companies as b on a.customer=b.id 
        inner join users as c on a.created_by=c.id 
        inner join users as d on a.updated_by=d.id
        inner join document_data as e on a.id=e.document_number 
        inner join documents as f on e.document=f.id
        where a.id=$id and e.document=2", "Document");

        $uploadFiles=$builder->getAllData('upload_files', 'Document');

        $customers=$builder->getAllData('companies', 'Partner');

        view('form/activity_form_detail', compact('arData', 'uploadFiles', 'customers'));
    }

    public function arUpdate(){
        if(!$this->role->can("update-activity-report")){
            redirectWithMessage([[ returnMessage()['activityReport']['accessRight']['update'] , 0]],getLastVisitedPage());
        }

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderArForm as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }

        $builder = App::get('builder');

        $idAr=filterUserInput($_POST['ar']);
        
        $where=[
            'id' => $idAr,
        ];

        $updateActivityReport = $builder->update('form_ar', $data, $where, '', 'Document');

        if(!$updateActivityReport ){
            recordLog(returnMessage()['activityReport']['title'], "Pembaharuan data ".returnMessage()['activityReport']['title']." $idAr gagal");
            redirect(getLastVisitedPage());
            exit();
        }else{
            recordLog(returnMessage()['activityReport']['title'], "Pembaharuan data ".returnMessage()['activityReport']['title']." $idAr berhasil");
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([["Pembaharuan data ".returnMessage()[activityReport][title]." $idAr berhasil",1]],getLastVisitedPage());
    }

    public function arClose(){
        if(!$this->role->can("update-activity-report")){
            redirectWithMessage([[ returnMessage()['activityReport']['accessRight']['update'] , 0]],getLastVisitedPage());
        }

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach(['ar' => 'required'] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }

        $builder = App::get('builder');

        $updateActivityReport = $builder->update('form_ar', ['active' => 1], ['id'=> $data['ar']], '', 'Document');

        if(!$updateActivityReport){
            recordLog(returnMessage()['activityReport']['title'], "Pembaharuan data ".returnMessage()['activityReport']['title']." $idAr gagal");
            redirect(getLastVisitedPage());
            exit();
        }else{
            recordLog(returnMessage()['activityReport']['title'], "Pembaharuan data ".returnMessage()['activityReport']['title']." $idAr berhasil");
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([["Pembaharuan data ".returnMessage()[activityReport][title]." $idAr berhasil",1]],getLastVisitedPage());
        
    }

//=====================================================================================================//

    /* TANDA TERIMA */
    public function receiveFormIndex(){

        if(!$this->role->can("view-receive-letter")){
            redirectWithMessage(["Anda tidak memiliki hak untuk melihat daftar tanda terima", 0],'/home');
        }

        $builder=App::get('builder');

        $servicePoints=$builder->getAllData('service_points', 'Internal');

        $partners=$builder->getAllData('companies', 'Partner');
        
        $requisites=$builder->getSpecificData('requisite',['*'],['form'=>1],'', 'Document');
        
        $products=$builder->getAllData('products', 'Product');
        
        /* $receiveData=$builder->custom("SELECT a.id, a.remark, date_format(a.created_at,'%d %M %Y') as created_at,date_format(a.updated_at,'%d %M %Y') as updated_at, date_format(a.receive_date,'%d %M %Y') as receive_date, c.name as requisite, d.name as submitted, e.name as received, case a.status when 0 then 'open' else 'closed' end as status 
        FROM `form_receive` as a inner join requisite as c on a.requisite=c.id inner join companies as d on a.submitted=d.id inner join companies as e on a.received=e.id order by a.receive_date desc",'Document');
        */

        $whereClause=1;

        //Searching for specific category
        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            $search['requisite']=filterUserInput($_GET['requisite']);
            $search['submitted']=filterUserInput($_GET['submitted']);
            $search['received']=filterUserInput($_GET['received']);

            $searchByDateStart=filterUserInput($_GET['receive_date_start']);
            $searchByDateEnd=filterUserInput($_GET['receive_date_end']);

            $whereClause='';
            $operator='&&';

            foreach($search as $k => $v){
                if(!empty($search[$k])){
                    $whereClause.=$k."=".$v.$operator;
                }
            }

            if(!empty($searchByDateStart) && !empty($searchByDateEnd)){
                $whereClause.=" receive_date between '$searchByDateStart' and '$searchByDateEnd'";
            }elseif(!empty($searchByDateStart)){
                $whereClause.=" receive_date='$searchByDateStart'";
            }elseif(!empty($searchByDateEnd)){
                $whereClause.=" receive_date='$searchByDateEnd'";
            }

            $whereClause=trim($whereClause, '&&');
            
            if($whereClause==''){
                $whereClause=1;
            }

        }
        
        $receiveData=$builder->custom("SELECT a.id, a.remark, 
        date_format(a.created_at,'%d %M %Y') as created_at,
        date_format(a.updated_at,'%d %M %Y') as updated_at, 
        date_format(a.receive_date,'%d %M %Y') as receive_date, 
        c.name as requisite, 
        d.name as submitted, 
        e.name as received, 
        case a.status when 0 then 'open' else 'closed' end as status 
        FROM `form_receive` as a 
        INNER JOIN requisite as c on a.requisite=c.id 
        INNER JOIN companies as d on a.submitted=d.id 
        INNER JOIN companies as e on a.received=e.id  
        WHERE $whereClause 
        ORDER BY a.receive_date DESC",'Document');

        //End for searching
        
        //=====================//

        //download all the data
        if(isset($_GET['download']) && $_GET['download']==true){
            
            $dataColumn = ['receive_date', 'submitted', 'received', 'requisite', 'remark'];

            $this->download(toDownload($receiveData, $dataColumn));

        }

        //Pagination
        //only show data for specified page
        if(isset($_GET['p'])){
            $p=$_GET['p'];

            if(!is_numeric($p)){
                redirectWithMessage([["Halaman yang anda tuju tidak diketahui",0]],getLastVisitedPage());
            }
            
        }else{
            $p=1;
        }

        $limitStart=$p*maxDataInAPage()-maxDataInAPage();

        $pages=ceil(count($receiveData)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($receiveData);
        
        $receiveData=array_slice($receiveData,$limitStart,maxDataInAPage());

        view("form/receive_form", compact('servicePoints', 'partners', 'requisites', 'products', 'receiveData', 'pages', 'sumOfAllData'));
    }

    public function receiveFormCreate(){

        if(!$this->role->can("create-receive-letter")){
            redirectWithMessage(["Anda tidak memiliki hak untuk membuat tanda terima", 0],'/form/tanda-terima');
        }

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        for($i=0;$i<count($this->placeholderReceiveForm);$i++){
            foreach($this->placeholderReceiveForm[$i] as $k => $v){
                if(checkRequirement($v, $k, $_POST[$k])){
                    $data[$i][$k]=$_POST[$k];
                }else{
                    $passingRequirement=false;
                }  
            }
            $data[$i]['created_by'] = substr($_SESSION['sim-id'], 3, -3);
            $data[$i]['updated_by'] = substr($_SESSION['sim-id'], 3, -3);
        }

        //dd($data);

        //if not the passing requirements
        if(!$passingRequirement){
            redirect('/form/tanda-terima');
            exit();
        }

        //apabila requisite : pinjam(1) maka own status dari asset merupakan kepemilikan pihak lain
        //selain itu own status dari asset merupakan kepemilikan sendiri dan status dari tanda terima sudah closed(1)
        if($data[0]['requisite']==1){
            $data[1]['ownership'] = 0;
        }else{
            $data[0]['status'] = 1;
        }

        $builder=App::get('builder');
        $insertToFormReceive=$builder->insert('form_receive', $data[0]);

        $data[2]['document_number']=$builder->getPdo()->lastInsertId();
        $data[2]['document']=1;
        

        //$products=array();
        $products=$data[1]['product'];
        $serialNumbers=$data[1]['serial_number'];
        $assetCondition=$data[1]['stock_condition'];

        //validate to check the number of data is same or not
        if((count($products) != count($serialNumbers)) && (count($products) != count($assetCondition))){
            //redirect to form page with message
            redirectWithMessage([["Pendaftaran data tanda terima gagal. Produk, serial number dan kondisi harus diisi dengan benar",1]],getLastVisitedPage());
        }


        for($i=0; $i < count($serialNumbers) ; $i++){
            $data[1]['serial_number']=$serialNumbers[$i];
            $data[1]['product']=$products[$i];
            $data[1]['stock_condition']=$assetCondition[$i];
            $data[1]['doc'] = $data[2]['document_number'];

            $insertToAsset=$builder->insert('stocks', $data[1]);

            $data[2]['asset']=$builder->getPdo()->lastInsertId();

            $insertToDocumentData=$builder->insert('document_data',$data[2]);
        }


        if(!$insertToFormReceive && !$insertToAsset && !$insertToDocumentData){
            recordLog('Tanda terima', "Pendaftaran data tanda terima gagal");
            redirect(getLastVisitedPage());
            exit();
        }else{
            recordLog('Tanda terima', "Pendaftaran data tanda terima berhasil");
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([["Pendaftaran data tanda terima berhasil",1]],getLastVisitedPage());

    }

    public function receiveFormDetail(){

        if(!$this->role->can("view-receive-letter")){
            redirectWithMessage(["Anda tidak memiliki hak untuk melihat daftar tanda terima", 0], getLastVisitedPage());
        }
        
        $id=filterUserInput($_GET['r']);
        //dd($this->role);
        $builder=App::get('builder');

        $receiveData=$builder->custom("SELECT a.id,f.id as ddata, ifnull(a.remark,'-') as remark, 
        date_format(a.created_at,'%d %M %Y %H:%i') as created_at,
        date_format(a.updated_at,'%d %M %Y %H:%i') as updated_at,
        date_format(a.receive_date,'%d %M %Y %H:%i') as receive_date,
        a.receive_date as rd,
        c.name as requisite,
        a.requisite as idreq, 
        d.name as submitted,
        a.submitted as ids, 
        e.name as received,
        a.received as idr, 
        case a.status when 0 then 'open' else 'closed' end as status, 
        j.name as created_by,
        k.name as updated_by,
        l.code,
        l.name as service_point,
        a.service_point as idsp
        FROM `form_receive` as a 
        INNER JOIN requisite as c on a.requisite=c.id 
        INNER JOIN companies as d on a.submitted=d.id 
        INNER JOIN companies as e on a.received=e.id  
        INNER JOIN document_data as f on f.document_number=a.id 
        INNER JOIN stocks as g on f.asset=g.id 
        INNER JOIN service_points as h on g.service_point=h.id 
        INNER JOIN products as i on g.product=i.id
        INNER JOIN users as j on a.created_by=j.id
        INNER JOIN users as k on a.updated_by=k.id
        INNER JOIN service_points as l on a.service_point=l.id
        WHERE f.document=1 and f.document_number=$id 
        GROUP BY f.document_number 
        ORDER BY a.receive_date",'Document');
        
        //Get big-list
        //this query's output is show the total number of items in the receive form
        $detailNumber=$builder->custom("SELECT count(*) as jumlah, d.id, a.document_number, a.asset, d.name, d.description, ifnull(e.upload_file,'#') as upload_file, ifnull(e.title,'product') as title
        FROM `document_data` as a 
        INNER JOIN form_receive as b on a.document_number=b.id 
        INNER JOIN stocks as c on a.asset=c.id 
        INNER JOIN products as d on c.product=d.id 
        LEFT JOIN upload_files as e on d.picture=e.id 
        WHERE a.document_number=$id and a.document=1 
        GROUP BY d.name ORDER BY d.name ASC","Document");
        
        $partners=$builder->getAllData('companies', 'Partner');
        
        $requisites=$builder->getSpecificData('requisite',['*'],['form'=>1],'', 'Document');

        $servicePoints=$builder->getAllData('service_points', 'Internal');

        $uploadFiles=$builder->getAllData('upload_files', 'Document');

        if(count($receiveData)<1){
            redirectWithMessage([['Data tidak tersedia atau asset terdaftar pada tanda terima tidak ada/telah dihapus',0]], getLastVisitedPage());
        }


        view('/form/receive_form_detail', compact('receiveData','detailNumber', 'partners', 'requisites', 'servicePoints','uploadFiles'));
    }

    public function receiveFormUpdate(){

        if(!$this->role->can("update-receive-letter")){
            redirectWithMessage(["Anda tidak memiliki hak untuk memperbaharui data tanda terima", 0],'/form/tanda-terima');
        }

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        $this->placeholderReceiveForm[0]['document_number']='required';

        foreach($this->placeholderReceiveForm[0] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=$_POST[$k];
            }else{
                $passingRequirement=false;
            }  
        }

        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);
        
        //if not the passing requirements
        if(!$passingRequirement){
            redirect('/form/tanda-terima');
            exit();
        }

        $receiveDate=filterUserInput($data['receive_date']);
        $submitted=filterUserInput($data['submitted']);
        $received=filterUserInput($data['received']);
        $requisite=filterUserInput($data['requisite']);
        $remark=filterUserInput($data['remark']);
        $documentNumber=filterUserInput($data['document_number']);

        $builder=App::get('builder');

        //$updateFormReceive=$builder->custom("update form_receive set receive_date=$receiveDate, requisite=$requisite, submitted=$submitted, received=$received, updated_by=$data[updated_by], remark=$remark where id=$data[document_number]", 'Document');

        $toUpdate=[
            'receive_date' => $receiveDate, 
            'requisite' => $requisite, 
            'submitted' => $submitted, 
            'received'=> $received, 
            'updated_by'=> $data['updated_by'], 
            'remark' => $remark
        ];

        $where=[
            'id' => $data[document_number]
        ];

        $updateFormReceive=$builder->update('form_receive', $toUpdate, $where, '', 'Document');

        if(!$updateFormReceive ){
            recordLog('Tanda terima', "Pembaharuan data tanda terima $documentNumber gagal");
            redirect(getLastVisitedPage());
            exit();
        }else{
            recordLog('Tanda terima', "Pendaftaran data tanda terima $documentNumber berhasil");
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([["Pendaftaran data tanda terima berhasil",1]],getLastVisitedPage());


    }

//=====================================================================================================//
    
    /* CUTI */
    public function cutiFormIndex(){

        if(!$this->role->can("view-vacation")){
            redirectWithMessage(["Anda tidak memiliki hak untuk melihat daftar data cuti", 0], '/home');
        }

        $builder = App::get('builder');

        //$vacationData = $builder->custom("SELECT a.id, b.name as submitter, a.day_used, c.name as requisite FROM form_vacation as a inner join users as b on a.submitter=b.id inner join requisite as c on a.requisite=c.id order by a.id desc", "Document");
        $requisites = $builder->getSpecificData('requisite',['*'],['form'=>4],'', 'Document');
        $submitters = $builder->getAllData('users','Document');
        $vacationList = $builder->getSpecificData('vacation_per_year',['*'],['year'=>date('Y')],'', 'Document');
        $approvalPerson = $builder->custom("SELECT a.user_id, b.name FROM role_user as a inner join users as b on a.user_id=b.id WHERE a.role_id=2", "Document");
        
        //Searching for specific category
        $whereClause = '';
        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            $search['requisite']=filterUserInput($_GET['requisite']);
            $search['submitter']=filterUserInput($_GET['submitter']);

            $searchByDateStart=filterUserInput($_GET['vacation_date_start']);
            $searchByDateEnd=filterUserInput($_GET['vacation_date_end']);
  
            $operator='&&';

            foreach($search as $k => $v){
                if(!empty($search[$k])){
                    $whereClause.=$k."=".$v.$operator;
                }
            }

            if(!empty($searchByDateStart) && !empty($searchByDateEnd)){
                $whereClause.=" d.vacation_date between '$searchByDateStart' and '$searchByDateEnd'";
            }elseif(!empty($searchByDateStart)){
                $whereClause.=" d.vacation_date='$searchByDateStart'";
            }elseif(!empty($searchByDateEnd)){
                $whereClause.=" d.vacation_date='$searchByDateEnd'";
            }
            //dd($whereClause);
            $whereClause=trim($whereClause, '&&');

            /*
            SELECT b.id, c.name as submitter, 
            b.day_used, 
            d.name as requisite 
            FROM vacation_date as a
            inner join form_vacation as b on a.document_number=b.id 
            inner join users as c on b.submitter=c.id
            inner join requisite as d on b.requisite=d.id 
            where a.vacation_date between '2017-08-20' and '2017-09-24'
            order by a.id desc
            */
    
        }else{
            $whereClause=1;
        }
    
        //End for searching
        
        $vacationData = $builder->custom("SELECT a.id, b.name as submitter, 
            a.day_used, 
            c.name as requisite,
            GROUP_CONCAT(date_format(d.vacation_date, '%d %M %Y') order by d.vacation_date ASC SEPARATOR '<br>') as vacation_date
            FROM form_vacation as a 
            INNER JOIN users as b on a.submitter=b.id 
            INNER JOIN requisite as c on a.requisite=c.id 
            INNER JOIN vacation_date as d on a.id=d.document_number
            WHERE $whereClause
            GROUP BY a.id ORDER BY a.id DESC", "Document");

        //download all the data
        if(isset($_GET['download']) && $_GET['download']==true){
            
            $dataColumn = ['submitter', 'requisite', 'day_used', 'vacation_date'];

            $this->download(toDownload($vacationData, $dataColumn));

        }
        
        //=====================//

        //Pagination
        //only show data for specified page
        if(isset($_GET['p'])){
            $p=$_GET['p'];

            if(!is_numeric($p)){
                redirectWithMessage([["Halaman yang anda tuju tidak diketahui",0]],getLastVisitedPage());
            }
            
        }else{
            $p=1;
        }

        $limitStart=$p*maxDataInAPage()-maxDataInAPage();

        $pages=ceil(count($vacationData)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($vacationData);
        
        $vacationData=array_slice($vacationData,$limitStart,maxDataInAPage());

        view("form/vacation_form", compact('vacationData', 'requisites', 'vacationList', 'submitters', 'approvalPerson', 'pages', 'sumOfAllData'));
    }

    public function cutiFormCreate(){
        if(!$this->role->can("create-vacation")){
            redirectWithMessage([["Anda tidak memiliki hak untuk membuat data cuti", 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderVacationForm as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        $data['submitter'] = substr($_SESSION['sim-id'], 3, -3);
        $data['created_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        //if not the passing requirements
        if(!$passingRequirement){
            //redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            redirect(getLastVisitedPage());
        }

        $vacationDate = $_POST['vacation_date'];

        $data['day_used'] = count($vacationDate);

        $builder = App::get('builder');

        $insertToVacationForm = $builder->insert("form_vacation", $data);

        $data['document_number']=$builder->getPdo()->lastInsertId();
        $data['document']=4;

        for($i=0; $i<count($vacationDate); $i++){
            $insertToVacationDate=$builder->insert("vacation_date", ['vacation_date' => filterUserInput($vacationDate[$i]), 'document_number'=>$data['document_number']]);

            if(!$insertToVacationDate){
                recordLog('Vacation form', returnMessage()['vacationForm']['createFail'] );
                redirectWithMessage(['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0],getLastVisitedPage());
                exit();
            }
        }

        //give notification
        $insertToNotification = $builder->insert('notifications', ['message'=> 'Terdapat pengajuan cuti yang memerlukan persetujuan anda', 'document' => $data['document'], 'document_number' => $data['document_number'], 'for_user' => $data['approved_by']]);
        
        if(!$insertToNotification){
            recordLog('Vacation form', returnMessage()['vacationForm']['createFail'] );
            redirect(getLastVisitedPage());
            exit();
        }
    
        $insertToDocumentData=$builder->insert('document_data',['document_number'=>$data['document_number'], 'document'=>$data['document']]);

        if(!$insertToVacationForm){
            recordLog('Vacation form', returnMessage()['vacationForm']['createFail'] );
            redirect(getLastVisitedPage());
            exit();
        }else{
            recordLog('Vacation form', returnMessage()['vacationForm']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['vacationForm']['createSuccess'] ,1]],getLastVisitedPage());
        
    }

    public function cutiFormDetail(){
        if(!$this->role->can("view-vacation")){
            redirectWithMessage([["Anda tidak memiliki hak untuk melihat data cuti", 0]], getLastVisitedPage());
        }
        
        $id = filterUserInput($_GET['v']);

        $builder = App::get('builder');

        $vacationData = $builder->custom("SELECT f.id as ddata, b.id as name, a.id, b.name as submitter, b.code,
        a.day_used, 
        date_format(a.created_at, '%d %M %Y') as created_at,
        c.name as requisite,
        a.requisite as rid, 
        case a.approved when 0 then 'not appoved yet' when 1 then 'approved' else 'rejected' end as approved, 
        case a.verified when 0 then 'not verified yet' when 1 then 'verified' else 'rejected' end as verified,
        a.approved as aid,
        a.verified as vid, 
        d.name as verified_by, 
        a.verified_by as vbid,
        e.name as approved_by,
        a.approved_by as abid,
        g.name as department,
        a.remark, 
        h.code as docCode
        FROM form_vacation as a 
        inner join users as b on a.submitter=b.id 
        inner join requisite as c on a.requisite=c.id 
        inner join users as d on a.verified_by=d.id 
        inner join users as e on a.approved_by=e.id
        inner join document_data as f on f.document_number=$id 
        inner join departments as g on b.department=g.id
        inner join documents as h on f.document=h.id
        where f.document=4 and a.id=$id", 'Document');

        /*
        $vacationData = $builder->custom("SELECT a.id, b.name as submitter, b.code,
        a.day_used, 
        date_format(a.created_at, '%d %M %Y') as created_at,
        c.name as requisite,
        d.name as department,
        GROUP_CONCAT(date_format(e.vacation_date, '%d %M %Y') order by e.vacation_date ASC SEPARATOR ', ') as vacation_date
        FROM form_vacation as a 
        inner join users as b on a.submitter=b.id 
        inner join requisite as c on a.requisite=c.id 
        inner join departments as d on b.department=d.id
        inner join vacation_date as e on a.id=e.document_number
        where a.approved_by=$this->userId and a.approved=0 group by a.id order by a.created_at ASC ", 'Document');
        */

        $vacationDate = $builder->custom("SELECT date_format(a.vacation_date, '%d %M %Y') as vacation_date, a.vacation_date as vd
        FROM vacation_date as a
        INNER JOIN form_vacation as b on a.document_number=b.id
        WHERE a.document_number=$id", 'Document');

        $notes=$builder->custom("SELECT a.notes, b.name as created_by, date_format(a.created_at, '%d %M %Y %H:%i') as created_at 
        FROM document_notes as a 
        INNER JOIN users as b on a.created_by=b.id 
        INNER JOIN document_data as c on a.document_data=c.id
        WHERE c.document_number=$id","Document");

        $uploadFiles=$builder->getAllData('upload_files', 'Document');

        $partners=$builder->getAllData('companies', 'Partner');

        $attachments=$builder->custom("SELECT b.id, c.upload_file,c.title, date_format(b.created_at, '%d %M %Y') as created_at, b.description
        FROM document_data as a 
        RIGHT JOIN document_attachments as b on a.id=b.document_data 
        INNER JOIN upload_files as c on b.attachment=c.id
        WHERE a.document=4 and a.document_number=$id","Document");

        $vacationList = $builder->getSpecificData('vacation_per_year',['*'],['year'=>date('Y')],'', 'Document');

        $approvalPerson = $builder->custom("SELECT a.user_id, b.name FROM role_user as a inner join users as b on a.user_id=b.id WHERE a.role_id=2", "Document");

        $requisites = $builder->getSpecificData('requisite',['*'],['form'=>4],'', 'Document');

        if(count($vacationData)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], getLastVisitedPage());
        }

        //dd($vacationData);
        view('/form/vacation_form_detail', compact('vacationData', 'notes', 'uploadFiles', 'partners', 'attachments', 'vacationList', 'approvalPerson', 'requisites', 'vacationDate'));

    }

    public function cutiFormUpdate(){
        if(!$this->role->can("update-vacation")){
            redirectWithMessage([[ returnMessage()['vacationForm']['accessRight']['update'] , 0]], getLastVisitedPage());
        }

        $id = filterUserInput($_POST['v']);

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderVacationForm as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }
        //dd($data);
        $builder = App::get('builder');

        $updateVacationForm = $builder->update('form_vacation', $data, ['id' => $id], '', 'Document');

        if(!$updateVacationForm){
            recordLog('Vacation form', returnMessage()['vacationForm']['updateFail'] );
            redirect(getLastVisitedPage());
            exit();
        }else{
            recordLog('Vacation form', returnMessage()['vacationForm']['updateSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['vacationForm']['updateSuccess'] ,1]],getLastVisitedPage());

    }

    public function cutiFormApproval(){
        //if the user dont have access right to approve vacation submission
        if(!$this->role->can("approval-vacation")){
            redirectWithMessage([[ returnMessage()['vacationForm']['accessRight']['approval'] , 0]], getLastVisitedPage());
        }

        //get the id of the vacation data
        $idVacationForm = filterUserInput($_GET['v']);
        $approval = filterUserInput($_GET['a']);

        //check whether this account match with the person that should be approve this vacation data
        $thisAccount = substr($_SESSION['sim-id'], 3, -3);

        $builder = App::get('builder');
        $vacationData = $builder->getSpecificData('form_vacation', ['*'], ['id'=>$idVacationForm], '', 'Document');
        $accountShouldBeApprove = $vacationData[0]->approved_by;

        if($accountShouldBeApprove != $thisAccount){
            redirectWithMessage([[ returnMessage()['vacationForm']['accessRight']['specificApproval'] , 0]], getLastVisitedPage());
        }

        if($approval==0){
            //reject
            $updateApproval=2;
        }elseif($approval=1){
            //approved
            $updateApproval=1;
        }else{
            redirectWithMessage([['Maaf, terjadi kesalahan untuk proses persetujuan. Mohon ulangi lagi', 0]], getLastVisitedPage());
        }

        //update this vacation data as approved
        $approveThisVacationData = $builder->update("form_vacation", ['approved'=>$updateApproval, 'updated_by'=>$thisAccount], ['id'=>$idVacationForm], '', 'Document');

        if(!$approveThisVacationData ){
            recordLog('Cuti', "Approval data cuti gagal");
            redirect(getLastVisitedPage());
            exit();
        }else{
            recordLog('Cuti', "Approval data cuti berhasil");
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['vacationForm']['updateSuccess'], 1]], getLastVisitedPage());

    }

//=====================================================================================================//

    /*  REIMBURSE */
    public function reimburseFormIndex(){

        if(!$this->role->can("view-reimburse")){
            redirectWithMessage([[ returnMessage()['reimburseForm']['accessRight']['view'] , 0]],'/home');
        }

        $builder = App::get('builder');
        $submitters = $builder->getAllData('users','Document');
        $requisites = $builder->getSpecificData('requisite',['*'],['form'=>3],'', 'Document');
        $approvalPerson = $builder->custom("SELECT a.user_id, b.name FROM role_user as a inner join users as b on a.user_id=b.id WHERE a.role_id=2", "Document");

        $whereClause='';

        //Searching for specific category
        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            $search['requisite']=filterUserInput($_GET['requisite']);
            $search['submitter']=filterUserInput($_GET['submitter']);

            $searchByDateStart=filterUserInput($_GET['created_at_start']);
            $searchByDateEnd=filterUserInput($_GET['created_at_end']);
  
            $operator='&&';

            foreach($search as $k => $v){
                if(!empty($search[$k])){
                    $whereClause.=$k."=".$v.$operator;
                }
            }

            if(!empty($searchByDateStart) && !empty($searchByDateEnd)){
                $whereClause.=" a.created_at between '$searchByDateStart' and '$searchByDateEnd'";
            }elseif(!empty($searchByDateStart)){
                $whereClause.=" a.created_at like '%$searchByDateStart%'";
            }elseif(!empty($searchByDateEnd)){
                $whereClause.=" a.created_at like '%$searchByDateEnd%'";
            }
            //dd($whereClause);
            $whereClause=trim($whereClause, '&&');
    
        }
        //End of searching

        if($whereClause==''){
            $whereClause=1;
        }

        $reimburseData = $builder->custom("SELECT a.id, 
        b.name as submitter, 
        date_format(a.created_at, '%d %M %Y') as created_at, 
        case a.send when 0 then 'Tersimpan' else 'Terkirim' end as send, 
        a.paid
        FROM `form_reimburse` as a 
        inner join users as b on a.submitter=b.id
        inner join reimburse_detail as c on a.id=c.document_number
        where $whereClause 
        group by a.id
        order by a.id ","Document");

        //download all the data
        if(isset($_GET['download']) && $_GET['download']==true){
            
            $dataColumn = ['created_at', 'submitter', 'send'];

            $this->download(toDownload($reimburseData, $dataColumn));

        }

        //Pagination
        //only show data for specified page
        if(isset($_GET['p'])){
            $p=$_GET['p'];

            if(!is_numeric($p)){
                redirectWithMessage([["Halaman yang anda tuju tidak diketahui",0]],getLastVisitedPage());
            }
            
        }else{
            $p=1;
        }

        $limitStart=$p*maxDataInAPage()-maxDataInAPage();

        $pages=ceil(count($reimburseData)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($reimburseData);
        
        $reimburseData=array_slice($reimburseData,$limitStart,maxDataInAPage());

        view("form/reimburse_form", compact('reimburseData', 'sumOfAllData', 'requisites', 'approvalPerson', 'pages', 'submitters'));
    }

    public function reimburseFormCreate(){
        if(!$this->role->can("create-reimburse")){
            redirectWithMessage([[ returnMessage()['reimburseForm']['accessRight']['create'] , 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        for($i=0; $i<count($this->placeholderReimburseForm); $i++){
            foreach($this->placeholderReimburseForm[$i] as $k => $v){
                if(checkRequirement($v, $k, $_POST[$k])){
                    $data[$i][$k]=filterUserInput($_POST[$k]);
                }else{
                    $passingRequirement=false;
                }  
            }
        }

        //if not the passing requirements
        if(!$passingRequirement){
            //redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            redirect(getLastVisitedPage());
        }

        if(isset($_POST['submit'])){
            $data[1]['send'] = 1; 
        }

        $data[1]['submitter'] = substr($_SESSION['sim-id'], 3, -3);
        $data[1]['created_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data[1]['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        $dataKeys= array_keys($data[0]);
   
        //check whether the value between keys is equal
        $value=0;
        $isSame=true;
        for($i=0;$i<count($dataKeys);$i++){
            $countValue = count($data[0][$dataKeys[$i]]);
            if($i==0){
                $value = $countValue;
            }
            if($countValue!=$value){
                $isSame=false;
            }
        }

        if(!$isSame){
            redirectWithMessage([["Mohon isi data dengan lengkap", 0]], getLastVisitedPage());
        }

        
        $builder = App::get('builder');

        //insert to db:form_reimburse
        $insertToFormReimburse = $builder->insert('form_reimburse', $data[1]);

        if(!$insertToFormReimburse){
            recordLog('Reimburse form', returnMessage()['reimburseForm']['createFail'] );
            redirectWithMessage(['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0],getLastVisitedPage());
            exit();
        }

        $idReimburseForm=$builder->getPdo()->lastInsertId();

        //grouping data based on reimburse list
        $newDataRecap=[];
        for($i=0; $i<$value; $i++){
            $newData=[];
            foreach($dataKeys as $key){
                $newData[$key]=$data[0][$key][$i];
            }
            $newData['document_number'] = $idReimburseForm;
            array_push($newDataRecap, $newData);
        }

        //dd($newDataRecap);
        $isSuccessInsertToReimburseDetail=true;
        for($i=0; $i<count($newDataRecap); $i++){
            $insertToReimburseDetail = $builder->insert('reimburse_detail', $newDataRecap[$i]);
            if(!$insertToReimburseDetail){
                $isSuccessInsertToReimburseDetail=false;
            }
        }

        $insertToDocumentData = $builder->insert("document_data", ['document'=>'3', 'document_number'=>$idReimburseForm]);
        if(!$insertToDocumentData){
            recordLog('Reimburse form', returnMessage()['reimburseForm']['createFail'] );
            redirectWithMessage(['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0],getLastVisitedPage());
            exit();
        }


        if(!$isSuccessInsertToReimburseDetail){
            recordLog('Reimburse form', returnMessage()['reimburseForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('Reimburse form', returnMessage()['reimburseForm']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['reimburseForm']['createSuccess'] ,1]],getLastVisitedPage());

    }

    public function reimburseFormUpdate(){
        if(!$this->role->can("update-reimburse")){
            redirectWithMessage([[ returnMessage()['reimburseForm']['accessRight']['update'] , 0]], getLastVisitedPage());
        }

        $id = filterUserInput($_POST['r-item']);
        
        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderReimburseForm[0] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }
        
        //dd($data);

        $builder = App::get('builder');

        $idOfReimburseItem = $builder->getSpecificData('reimburse_detail', ['document_number'], ['id'=>$id], '', 'Document');
        
        $updateReimburseForm = $builder->update("form_reimburse", ['updated_by'=>substr($_SESSION['sim-id'], 3, -3)], ['id'=>$idOfReimburseItem[0]->document_number], "", "Document");
        
        if(!$updateReimburseForm){
            recordLog('Reimburse form', returnMessage()['reimburseForm']['updateFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
        }
        
        $updateReimburseDetail = $builder->update("reimburse_detail", $data, ['id'=>$id], "", "Document");

        if(!$updateReimburseDetail){
            recordLog('Reimburse form', returnMessage()['reimburseForm']['updateFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
        }else{
            recordLog('Reimburse form', returnMessage()['reimburseForm']['updateSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['reimburseForm']['updateSuccess'] ,1]],getLastVisitedPage());

    }

    public function reimburseFormDetail(){
        if(!$this->role->can("view-reimburse")){
            redirectWithMessage([[ returnMessage()['reimburseForm']['accessRight']['view'] , 0]],'/home');
        }

        $id= filterUserInput($_GET['r']);

        $builder = App::get('builder');

        $reimburseData = $builder->custom("SELECT a.id, a.submitter as name, b.name as submitter, b.code,  
        date_format(a.verified_at, '%d %M %Y') as verified_at, 
        date_format(a.approved_at, '%d %M %Y') as approved_at, 
        date_format(a.created_at, '%d %M %Y %H:%i') as created_at, 
        date_format(a.updated_at, '%d %M %Y %H:%i') as updated_at, 
        a.paid as pid,
        case a.paid when 0 then 'Belum ditebus' when 1 then 'Telah ditebus. Mohon konfirmasi' when 2 then 'Pembayaran telah dikonfirmasi' end as paid, 
        a.approved_by as abid,
        c.name as approved_by, 
        d.name as verified_by,
        f.code as docCode,
        e.id as ddata
        FROM `form_reimburse` as a 
        inner join users as b on a.submitter=b.id 
        inner join users as c on a.approved_by=c.id 
        inner join users as d on a.verified_by=d.id
        inner join document_data as e on e.document_number=a.id
        inner join documents as f on f.id=e.document
        where e.document=3 and a.id= $id", "Document");

        //dd($reimburseData);

        $reimburseDetailData = $builder->custom("SELECT a.id, a.receipt_date as rdd, a.requisite as rid, date_format(a.receipt_date, '%d %M %Y') as receipt_date, 
        b.name as requisite, 
        a.cost, 
        a.remark, 
        a.approved as aid,
        case a.approved when 0 then 'Not approved yet' when 1 then 'Approved' when 2 then 'Reject' else 'need revision' end as approved 
        FROM `reimburse_detail` as a 
        inner join requisite as b on a.requisite=b.id 
        WHERE a.document_number=$id", "Document");

        /* $notes=$builder->custom("SELECT a.notes, b.name as created_by, date_format(a.created_at, '%d %M %Y %H:%i') as created_at 
        FROM document_notes as a 
        INNER JOIN users as b on a.created_by=b.id 
        INNER JOIN document_data as c on a.document_data=c.id
        WHERE c.document_number=$id","Document"); */

        $attachments=$builder->custom("SELECT b.id, 
        c.upload_file,
        c.title, 
        date_format(b.created_at, '%d %M %Y') as created_at, 
        b.description
        FROM document_data as a RIGHT JOIN document_attachments as b on a.id=b.document_data 
        INNER JOIN upload_files as c on b.attachment=c.id
        WHERE a.document=3 and a.document_number=$id","Document");

        $uploadFiles=$builder->getAllData('upload_files', 'Document');

        $requisites = $builder->getSpecificData('requisite',['*'],['form'=>3],'', 'Document');

        if(count($reimburseDetailData)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], '/form/reimburse');
        }

        //dd($reimburseData);

        view('/form/reimburse_form_detail', compact('reimburseDetailData', 'reimburseData', 'requisites', 'uploadFiles', 'attachments'));

    }

    public function reimburseFormApproval(){
        //if the user dont have access right to approve vacation submission
        if(!$this->role->can("approval-reimburse")){
            redirectWithMessage([[ returnMessage()['reimburseForm']['accessRight']['approval'] , 0]], getLastVisitedPage());
        }

        //dd($_POST);

        //get the id of the vacation data
        $idReimburseItem = filterUserInput($_POST['r-item']);
        $approval = filterUserInput($_POST['a']);

        $thisAccount = substr($_SESSION['sim-id'], 3, -3);

        $builder = App::get('builder');

        //get id of form_reimburse where related to this reimburse item
        $idOfReimburseItem = $builder->getSpecificData('reimburse_detail', ['document_number'], ['id'=>$idReimburseItem], '', 'Document');

        //get all the form_reimburse where have id $idOfReimburseItem
        $reimburseData = $builder->getSpecificData('form_reimburse', ['*'], ['id'=>$idOfReimburseItem[0]->document_number], '', 'Document');

        //get the value of approved_by column
        $accountShouldBeApprove = $reimburseData[0]->approved_by;
        
        //check whether this account match with the person that should be approve this vacation data        
        if($accountShouldBeApprove != $thisAccount){
            redirectWithMessage([[ returnMessage()['vacationForm']['accessRight']['specificApproval'] , 0]], getLastVisitedPage());
        }
       
        if($approval==0){
            //reject
            $updateApproval=2;
        }elseif($approval=1){
            //approved
            $updateApproval=1;
        }else{
            redirectWithMessage([['Maaf, terjadi kesalahan untuk proses persetujuan. Mohon ulangi lagi', 0]], getLastVisitedPage());
        }

        //update this reimburse data as approved
        
        $updateReimburseForm = $builder->update("form_reimburse", ['updated_by'=>substr($_SESSION['sim-id'], 3, -3)], ['id'=>$reimburseData[0]->id], "", "Document");

        $approveThisReimburseData = $builder->update("reimburse_detail", ['approved'=>$updateApproval], ['id'=>$idReimburseItem], '', 'Document');

        if(!$approveThisReimburseData ){
            recordLog('Reimburse', "Approval data Reimburse gagal");
            redirect(getLastVisitedPage());
            exit();
        }else{
            recordLog('Reimburse', "Approval data Reimburse berhasil");
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['reimburseForm']['updateSuccess'], 1]], getLastVisitedPage());
    }

    public function reimburseFormItemRemove(){
        //if the user dont have access right to approve vacation submission
        if(!$this->role->can("update-reimburse")){
            redirectWithMessage([[ returnMessage()['reimburseForm']['accessRight']['delete'] , 0]], getLastVisitedPage());
        }

        //id of the reimburse data item
        $id = filterUserInput($_POST['r-item']);
        
        //check whether the user that perform deletion action is same with the reimburse data's creator
        $thisAccount = substr($_SESSION['sim-id'], 3, -3);

        $builder = App::get('builder');
        
        $accountShouldBeAllow = $builder->custom("SELECT created_by FROM form_reimburse as a, reimburse_detail as b WHERE a.id=b.document_number and b.id=$id", "Document");
        
        if($accountShouldBeAllow[0]->created_by != $thisAccount){
            redirectWithMessage([[ returnMessage()['reimburseForm']['accessRight']['delete'] , 0]], getLastVisitedPage());
        }

        $removeReimburseDataItem = $builder->delete("reimburse_detail", ['id'=>$id], "", "Document");
        
        if(!$removeReimburseDataItem){
            recordLog('Reimburse', "Removal data reimburse item gagal");
            redirect(getLastVisitedPage());
            exit();
        }else{
            recordLog('Reimburse', "Removal data reimburse item berhasil");
        }

        $builder->save();
        
        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['reimburseForm']['deleteSuccess'], 1]], getLastVisitedPage());

    }

//=====================================================================================================//  

    /* RECEIPT */

    public function receiptFormIndex(){
        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

        $builder = App::get('builder');

        //$products = $builder->getAllData('products', 'Product');

        $partners = $builder->getAllData('companies', 'Partner');

        $products = $builder->custom("SELECT b.id, b.name, IFNULL(sum(quantity), 0) as quantity
        FROM `stocks` as a 
        RIGHT JOIN products as b on a.product=b.id 
        GROUP BY b.id", 'Product');

        //dd($products);

        $parameterData=[];
        $parameters = $builder->getAllData('default_parameter', 'Internal');
        for($i=0; $i<count($parameters); $i++){
            $parameterData[$parameters[$i]->parameter]=$parameters[$i]->value;
        }

        //Searching for specific category
        
        $whereClause='';
    
        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            $search['buyer']=filterUserInput($_GET['buyer']);
            $search['supplier']=filterUserInput($_GET['supplier']);
            $search['product']=filterUserInput($_GET['product']);

            $searchByDateStart=filterUserInput($_GET['receipt_date_start']);
            $searchByDateEnd=filterUserInput($_GET['receipt_date_end']);
    
            $operator='&&';

            foreach($search as $k => $v){
                if(!empty($search[$k])){
                    $whereClause.=$k."=".$v.$operator;
                }
            }

            if(!empty($searchByDateStart) && !empty($searchByDateEnd)){
                $whereClause.=" a.receipt_date between '$searchByDateStart' and '$searchByDateEnd'";
            }elseif(!empty($searchByDateStart)){
                $whereClause.=" a.receipt_date like '%$searchByDateStart%'";
            }elseif(!empty($searchByDateEnd)){
                $whereClause.=" a.receipt_date like '%$searchByDateEnd%'";
            }
            //dd($whereClause);
            $whereClause=trim($whereClause, '&&');
    
        }

        if($whereClause==''){
            $whereClause=1;
        }

        //End of searching

        $receiptData = $builder->custom("SELECT a.id, a.receipt_number,
        date_format(a.receipt_date, '%d %M %Y') as receipt_date, 
        a.supplier as sid,
        a.buyer as bid,
        d.name as supplier,
        e.name as buyer,
        GROUP_CONCAT(distinct(c.name) ORDER by c.id asc SEPARATOR '<br>') as product,
        GROUP_CONCAT(b.quantity ORDER by c.id asc SEPARATOR '<br>') as quantity,
        GROUP_CONCAT(b.price ORDER by c.id asc SEPARATOR '<br>') as price,
        GROUP_CONCAT(b.discount ORDER by c.id asc SEPARATOR '<br>') as discount,
        a.remark
        FROM `form_receipt` as a 
        INNER JOIN receipt_product as b on a.id=b.receipt
        INNER JOIN products as c on b.product=c.id
        INNER JOIN companies as d on a.supplier=d.id
        INNER JOIN companies as e on a.buyer=e.id
        WHERE $whereClause
        GROUP BY a.id
        ORDER BY a.id DESC","Document");

        //download all the data
        if(isset($_GET['download']) && $_GET['download']==true){
            
            $dataColumn = ['receipt_date', 'receipt_number', 'supplier', 'buyer', 'product',  'price', 'quantity','remark'];

            $this->download(toDownload($receiptData, $dataColumn));

        }

        //Pagination
        //only show data for specified page
        if(isset($_GET['p'])){
            $p=$_GET['p'];

            if(!is_numeric($p)){
                redirectWithMessage([["Halaman yang anda tuju tidak diketahui",0]],getLastVisitedPage());
            }
            
        }else{
            $p=1;
        }
        
        $limitStart=$p*maxDataInAPage()-maxDataInAPage();

        $pages=ceil(count($receiptData)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($receiptData);
        
        $receiptData=array_slice($receiptData,$limitStart,maxDataInAPage());

        setSearchPage();
        
        view('form/receipt_form', compact('receiptData', 'products', 'pages', 'sumOfAllData', 'partners', 'parameterData'));
        
    }

    public function receiptFormCreate(){
        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        for($i=0; $i<count($this->placeholderReceiptForm); $i++){
            foreach($this->placeholderReceiptForm[$i] as $k => $v){
                if(checkRequirement($v, $k, $_POST[$k])){
                    $data[$i][$k]=filterUserInput($_POST[$k]);
                }else{
                    $passingRequirement=false;
                }  
            }
        }

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        $data[0]['created_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data[0]['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        //Check whether data inserted is appropriate with the default parameters
        $receiptType = filterUserInput($_POST['receipt_type']);

        $builder = App::get('builder');
    
        $parameterData=[];
        $parameters = $builder->getAllData('default_parameter', 'Internal');
        for($i=0; $i<count($parameters); $i++){
            $parameterData[$parameters[$i]->parameter]=$parameters[$i]->value;
        }

        $companyData = $builder->getSpecificData("companies", ['*'], ["id"=>$data[0]['buyer']], '', 'Partner');
        $userData = $builder->getSpecificData("users", ["*"], ["id"=>$data[0]['created_by']], '', 'User');

        //if 'company' value in default parameter is checked in mode 'RECEIPT IN' 
        
        if($receiptType==1){
            $data[0]['receipt_number'] = filterUserInput($_POST['receipt_number']);
            //supplier must be the value of 'company' parameter
            if($data[0]['buyer']!=$parameterData['company']){
                redirectWithMessage([['Mohon isi data supplier dengan benar', 0 ]], getLastVisitedPage());
            }
        }else{
            //if 'company' value in default parameter is checked in mode 'RECEIPT OUT' 
            //buyer must be the value of 'company' parameter
            if($data[0]['supplier']!=$parameterData['company']){
                redirectWithMessage([['Mohon isi data buyer dengan benar', 0 ]], getLastVisitedPage());
            }

            // For numbering format purpose
            $thisYear = date('Y');
            $thisMonth = convertToRoman(date('m'));
            $countDataInThisYear = $builder->custom("select count(*) as total_data from form_receipt where date_format(receipt_date, '%Y') in ($thisYear) and supplier=$parameterData[company]", "Document");
            
            $numbering=$countDataInThisYear[0]->total_data;
            $numbering=  str_pad($numbering+1, 3, '0', STR_PAD_LEFT);
            $userCode = strtoupper($userData[0]->code);
            $companyCode = strtoupper($companyData[0]->code);
            $data[0]['receipt_number'] = $numbering."/RCP/".$companyCode."/SNC-".$userCode."/".$thisMonth."/".date('Y');
            // End of numbering format
        }

        //End of check

        //check whether the value between keys is equal
        $dataKeys= array_keys($data[1]);

        $value=0;
        $isSame=true;
        for($i=0;$i<count($dataKeys);$i++){
            $countValue = count($data[1][$dataKeys[$i]]);
            if($i==0){
                $value = $countValue;
            }
            if($countValue!=$value){
                $isSame=false;
            }
        }

        if(!$isSame){
            redirectWithMessage([["Mohon isi data product dengan lengkap", 0]], getLastVisitedPage());
        }

        //dd($data);

        //insert to db:form_receipt
        $insertToFormReceipt = $builder->insert('form_receipt', $data[0]);
        $idReceiptForm = $builder->getPdo()->lastInsertId();

        //dd($insertToFormReceipt);

        if(!$insertToFormReceipt){
            recordLog('Receipt form', returnMessage()['receiptForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.1', 0]],getLastVisitedPage());
            exit();
        }

        $newDataRecap=[];
        for($i=0; $i<$value; $i++){
            $newData=[];
            foreach($dataKeys as $key){
                $newData[$key]=$data[1][$key][$i];
            }
            $newData['receipt'] = $idReceiptForm;
            $newData['updated_by'] = substr($_SESSION['sim-id'], 3, -3);
            $newData['remark'] = filterUserInput($_POST['remark']);
            array_push($newDataRecap, $newData);
        }

        //dd($newData);

        $flag = true;
        for($i=0; $i<count($newDataRecap); $i++){

            //insert into receipt_product
            $insertToStockProduct = $builder->insert("receipt_product", $newDataRecap[$i]);

            $idReceiptStock = $builder->getPdo()->lastInsertId();

            $insertToStockRelation = $builder->insert("stock_relation", ['document' => 11, 'spec_doc' => $idReceiptStock]);

            $stockRelation = $builder->getPdo()->lastInsertId();

            $insertToStock = $builder->insert("stocks", ['received_at' => $data[0]['receipt_date'], 'product'=>$newDataRecap[$i]['product'], 'quantity'=>$newDataRecap[$i]['quantity'], 'stock_relation' => $stockRelation, 'status' => $receiptType]);

            if(!$insertToStockProduct || !$insertToStock || !$insertToStockRelation){
                $flag = false;
            }

        }

        if(!$flag){
            redirectWithMessage([['Maaf, Gagal membuat receipt form', 0]],getLastVisitedPage());
        }

        //insert into document_data
        $insertToDocumentData = $builder->insert("document_data", ['document'=>'11', 'document_number'=>$idReceiptForm]);
        if(!$insertToDocumentData){
            recordLog('Receipt form', returnMessage()['receiptForm']['createFail'] );
            redirectWithMessage(['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.2', 0],getLastVisitedPage());
            exit();
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['receiptForm']['createSuccess'] ,1]],getLastVisitedPage());
    }

    public function receiptFormDetail(){

        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

        $id = filterUserInput($_GET['r']);

        $builder = App::get('builder');

        $products = $builder->custom("SELECT b.id, b.name, IFNULL(sum(quantity), 0) as quantity
        FROM `stocks` as a 
        RIGHT JOIN products as b on a.product=b.id 
        GROUP BY b.id", 'Product');

        $partners = $builder->getAllData('companies', 'Partner');

        $parameterData=[];
        $parameters = $builder->getAllData('default_parameter', 'Internal');
        for($i=0; $i<count($parameters); $i++){
            $parameterData[$parameters[$i]->parameter]=$parameters[$i]->value;
        }

        $uploadFiles=$builder->getAllData('upload_files', 'Document');

        $attachments=$builder->custom("SELECT b.id, 
        c.upload_file,
        c.title, 
        c.file_type,
        date_format(b.created_at, '%d %M %Y') as created_at, 
        b.description
        FROM document_data as a RIGHT JOIN document_attachments as b on a.id=b.document_data 
        INNER JOIN upload_files as c on b.attachment=c.id
        WHERE a.document=11 and a.document_number=$id","Document");


        $receiptData = $builder->custom("SELECT a.id, a.receipt_number,
        date_format(a.receipt_date, '%d %M %Y') as receipt_date,
        a.receipt_date as rd, 
        a.supplier as sid,
        a.buyer as bid,
        d.name as supplier,
        d.address as saddress,
        d.phone as sphone,
        e.name as buyer,
        e.address as baddress,
        e.phone as bphone,
        GROUP_CONCAT(c.name ORDER by c.id asc SEPARATOR '<br>') as product,
        GROUP_CONCAT(b.quantity ORDER by c.id asc SEPARATOR '<br>') as quantity,
        GROUP_CONCAT(b.price ORDER by c.id asc SEPARATOR '<br>') as price,
        GROUP_CONCAT(b.discount ORDER by c.id asc SEPARATOR '<br>') as discount,
        a.remark,
        f.id as ddata,
        a.ppn,
        g.name as currency,
        a.currency as cid,
        case a.supplier when $parameterData[company] then '2' else '1' end  as receipt_type
        FROM `form_receipt` as a 
        INNER JOIN receipt_product as b on a.id=b.receipt
        INNER JOIN products as c on b.product=c.id
        INNER JOIN companies as d on a.supplier=d.id
        INNER JOIN companies as e on a.buyer=e.id
        INNER JOIN document_data as f on f.document_number=a.id
        INNER JOIN currency as g on a.currency=g.id
        WHERE a.id=$id and f.document=11
        GROUP BY a.id
        ORDER BY a.id DESC","Document");
        
        if(count($receiptData)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], '/form/receipt');
        }

        $receiptItems = $builder->custom("SELECT b.id, b.product as pid, c.name as product, b.quantity, b.price, b.discount
        FROM form_receipt as a 
        INNER JOIN receipt_product as b on b.receipt=a.id
        INNER JOIN products as c on b.product=c.id 
        WHERE a.id=$id", "Document");

        view('form/receipt_form_detail', compact('receiptData', 'receiptItems', 'receivedItems', 'attachments', 'uploadFiles', 'partners', 'products'));
    }

    public function receiptFormUpdate(){
        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

        $id = filterUserInput($_POST['receipt_form']);

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderReceiptForm[0] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        $data['receipt_number'] = filterUserInput($_POST['receipt_number']);
        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);
        

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        //dd($data);

        $builder = App::get('builder');

        $updateReceiptForm = $builder->update("form_receipt", $data, ['id' => $id], '', 'Document');

        if(!$updateReceiptForm){
            recordLog('Receipt form', returnMessage()['receiptForm']['updateFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
        }

        recordLog('Receipt form', returnMessage()['receiptForm']['updateSuccess'] );

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['receiptForm']['updateSuccess'] ,1]],getLastVisitedPage());
    }

    public function receiptFormItemUpdate(){
        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

        $item = filterUserInput($_POST['receipt_item']);

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderReceiptForm[1] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }
        

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        $builder = App::get('builder');

        $updateReceiptItem = $builder->update("receipt_product", $data, ['id' => $item], '', 'Document');

        $stockRelation = $builder->getSpecificData("stock_relation", ['*'], ['document' => 11, 'spec_doc' => $item], '&&', 'Stock')[0]->id;

        $updateStock = $builder->update("stocks", ['product' => $data['product'], 'quantity' => $data['quantity']], ['stock_relation' => $stockRelation], '', 'Document');

        if(!$updateStock || !$updateReceiptItem){
            recordLog('Receipt form', "Memperbaharui receipt item gagal" );
            redirectWithMessage([["Memperbaharui receipt item gagal", 0]],getLastVisitedPage());
        }

        recordLog('Receipt form', "Memperbaharui receipt item berhasil" );

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ "Memperbaharui receipt item berhasil",1]],getLastVisitedPage());

    }

    public function receiptFormRemove(){
        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

        $receipt = filterUserInput($_POST['receipt_form']);

        $builder = App::get('builder');

        $getStockRelation = $builder->custom("SELECT a.id FROM stock_relation as a 
        INNER JOIN receipt_product as b on a.spec_doc=b.id
        INNER JOIN form_receipt as c on b.receipt=c.id
        WHERE a.document=11 and b.receipt=$receipt", "Document");

        for($i=0; $i<count($getStockRelation); $i++){
            $sr = $getStockRelation[$i]->id;

            $deleteStockRelation = $builder->delete("stock_relation", ['id' => $sr], '', 'Stock');
            if(!$deleteStockRelation){
                redirectWithMessage([["Maaf, gagal menghapus data", 0]], getLastVisitedPage());
            }
        }

        /* $deleteStockRelation = $builder->custom("DELETE a FROM stock_relation as a 
        INNER JOIN receipt_product as b on a.spec_doc=b.id
        INNER JOIN form_receipt as c on b.receipt=c.id
        WHERE a.document=11 and b.receipt=$receipt", "Stock");

        if(!$deleteStockRelation){
            redirectWithMessage([["Maaf, gagal menghapus data", 0]], getLastVisitedPage());
        } */

        $deleteReceiptForm = $builder->delete("form_receipt", ['id' => $receipt], '', 'Document');

        if(!$deleteReceiptForm){
            redirectWithMessage([["Maaf, gagal menghapus data", 0]], getLastVisitedPage());
        }

        recordLog('Receipt form', returnMessage()['receiptForm']['deleteSuccess'] );

        $builder->save();

        redirectWithMessage([[returnMessage()['receiptForm']['deleteSuccess'], 1]], '/form/receipt');

    }
    
    public function receiptItemRemove(){
        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

        $item = filterUserInput($_POST['receipt_item']);

        $builder = App::get('builder');
        
        $deleteStock = $builder->delete("stock_relation", ['document' => 11, 'spec_doc' => $item], '&&', 'Stock');

        $deleteReceiptItem = $builder->delete("receipt_product", ['id' => $item], '', 'Document');


        if(!$deleteStock || !$deleteReceiptItem){
            redirectWithMessage([["Maaf, gagal menghapus data", 0]], getLastVisitedPage());
        }

        recordLog('Receipt item', returnMessage()['receiptForm']['deleteSuccess'] );

        $builder->save();

        redirectWithMessage([[returnMessage()['receiptForm']['deleteSuccess'], 1]], getLastVisitedPage());
    }

    public function receiptFormCreateNewItem(){

        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderReceiptForm[1] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        //check whether the value between keys is equal
        $dataKeys= array_keys($data);

        $value=0;
        $isSame=true;
        for($i=0;$i<count($dataKeys);$i++){
            $countValue = count($data[$dataKeys[$i]]);
            if($i==0){
                $value = $countValue;
            }
            if($countValue!=$value){
                $isSame=false;
            }
        }

        if(!$isSame){
            redirectWithMessage([["Mohon isi data product dengan lengkap", 0]], getLastVisitedPage());
        }

        //dd($data);

        $idReceiptForm = filterUserInput($_POST['receipt_form']);
        $receiptType = filterUserInput($_POST['receipt_type']);
        $receiveSendDate = filterUserInput($_POST['receive_send_date']);

        $newDataRecap=[];
        for($i=0; $i<$value; $i++){
            $newData=[];
            foreach($dataKeys as $key){
                $newData[$key]=$data[$key][$i];
            }
            $newData['receipt'] = $idReceiptForm;
            $newData['updated_by'] = substr($_SESSION['sim-id'], 3, -3);
            array_push($newDataRecap, $newData);
        }
        
        if($receiptType==1){
            $date = "received_at";
        }else{
            $date = "send_at";
        }

        $builder = App::get('builder');

        //dd($newDataRecap);

        $flag = true;
        for($i=0; $i<count($newDataRecap); $i++){

            //insert into receipt_product
            $insertToStockProduct = $builder->insert("receipt_product", $newDataRecap[$i]);
            

            $idReceiptStock = $builder->getPdo()->lastInsertId();

            $insertToStockRelation = $builder->insert("stock_relation", ['document' => 11, 'spec_doc' => $idReceiptStock]);

            $stockRelation = $builder->getPdo()->lastInsertId();
           
            $insertToStock = $builder->insert("stocks", [$date => $receiveSendDate, 'product'=>$newDataRecap[$i]['product'], 'quantity'=>$newDataRecap[$i]['quantity'], 'stock_relation' => $stockRelation, 'status' => $receiptType]);

            if(!$insertToStockProduct || !$insertToStock || !$insertToStockRelation){
                $flag = false;
            }

        }

        if(!$flag){
            recordLog('Receipt form', returnMessage()['receiptForm']['createFail'] );
            redirectWithMessage([['Maaf, Gagal menambahkan receipt item', 0]],getLastVisitedPage());
        }

        recordLog('Receipt form', returnMessage()['receiptForm']['createSuccess'] );
        
        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ "Menambahkan receipt item berhasil" ,1]],getLastVisitedPage());
    }

//=====================================================================================================//   

    /* QUOTATION */
    public function quoFormIndex(){
        if(!$this->role->can('view-data-quo')){
            redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['view'] , 0]], getLastVisitedPage());
        }

        $builder = App::get('builder');

        $products=$builder->getAllData('products', 'Product');

        $partners=$builder->getAllData('companies', 'Partner');

        $approvalPerson = $builder->custom("SELECT a.user_id, b.name FROM role_user as a inner join users as b on a.user_id=b.id WHERE a.role_id=2", "Document");

        $parameterData=[];
        $parameters = $builder->getAllData('default_parameter', 'Internal');
        for($i=0; $i<count($parameters); $i++){
            $parameterData[$parameters[$i]->parameter]=$parameters[$i]->value;
        }
        //Searching for specific category
        
        $whereClause='';
    
        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            $search['buyer']=filterUserInput($_GET['buyer']);
            $search['supplier']=filterUserInput($_GET['supplier']);
            $search['product']=filterUserInput($_GET['product']);

            $searchByDateStart=filterUserInput($_GET['po_date_start']);
            $searchByDateEnd=filterUserInput($_GET['po_date_end']);
    
            $operator='&&';

            foreach($search as $k => $v){
                if(!empty($search[$k])){
                    $whereClause.=$k."=".$v.$operator;
                }
            }

            if(!empty($searchByDateStart) && !empty($searchByDateEnd)){
                $whereClause.=" a.created_at between '$searchByDateStart' and '$searchByDateEnd'";
            }elseif(!empty($searchByDateStart)){
                $whereClause.=" a.created_at like '%$searchByDateStart%'";
            }elseif(!empty($searchByDateEnd)){
                $whereClause.=" a.created_at like '%$searchByDateEnd%'";
            }
            //dd($whereClause);
            $whereClause=trim($whereClause, '&&');
    
        }

        if($whereClause==''){
            $whereClause=1;
        }

        //End of searching

        $quoData = $builder->custom("SELECT g.id, g.quo_number, 
        date_format(a.doc_date, '%d %M %Y') as doc_date, 
        c.name as supplier,
        d.name as buyer,
        GROUP_CONCAT(f.name ORDER by f.id asc SEPARATOR '<br>') as product,
        e.quantity,
        a.supplier as sid,
        a.buyer as bid
        FROM `form_po` as a 
        inner join form_quo as g on g.quo=a.id
        inner join companies as c on a.supplier=c.id
        inner join companies as d on a.buyer=d.id
        left join quo_product as e on g.id=e.quo
        left join products as f on e.product=f.id
        where $whereClause && po_or_quo=0 && revision is null
        group by a.id
        order by a.id DESC","Document");

        //download all the data
        if(isset($_GET['download']) && $_GET['download']==true){
            
            $dataColumn = ['doc_date', 'quo_number', 'supplier', 'buyer', 'product', 'quantity'];

            $this->download(toDownload($quoData, $dataColumn));

        }
        
        //Pagination
        //only show data for specified page
        if(isset($_GET['p'])){
            $p=$_GET['p'];

            if(!is_numeric($p)){
                redirectWithMessage([["Halaman yang anda tuju tidak diketahui",0]],getLastVisitedPage());
            }
            
        }else{
            $p=1;
        }
        
        $limitStart=$p*maxDataInAPage()-maxDataInAPage();

        $pages=ceil(count($quoData)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($quoData);
        
        $quoData=array_slice($quoData,$limitStart,maxDataInAPage());
        
        view('form/quotation_form', compact('quoData', 'partners', 'approvalPerson', 'products', 'pages', 'sumOfAllData', 'parameterData'));
        
    }

    public function quoFormCreate(){
        /*
        QUO out: supplier must be company in default parameter. in this case its should be snc
        QUO in: buyer must be company in default parameter. in this case its should be snc
        */

        if(!$this->role->can('create-data-quo')){
            redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['create'] , 0]], getLastVisitedPage());
        }

        //insert into form_quo table in database
        $title = filterUserInput($_POST['title']);

        $builder = App::get('builder');

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        for($i=0; $i<count($this->placeholderPoForm); $i++){
            foreach($this->placeholderPoForm[$i] as $k => $v){
                if(checkRequirement($v, $k, $_POST[$k])){
                    $data[$i][$k]=filterUserInput($_POST[$k]);
                }else{
                    $passingRequirement=false;
                }  
            }
        }

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        $data[0]['created_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data[0]['updated_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data[0]['po_or_quo'] = 0;

        //Check whether data inserted is appropriate with the default parameters
        $quoType = filterUserInput($_POST['quo_type']);
       // dd($quoType);
        
        if(isEmpty($quoType) && !isset($quoType)){
            redirectWithMessage([['Mohon isi form dengan benar', 0]], getLastVisitedPage());
        }

        $parameterData=[];
        $parameters = $builder->getAllData('default_parameter', 'Internal');
        for($i=0; $i<count($parameters); $i++){
            $parameterData[$parameters[$i]->parameter]=$parameters[$i]->value;
        }

        $companyData = $builder->getSpecificData("companies", ['*'], ["id"=>$data[0]['buyer']], '', 'Partner');
        $userData = $builder->getSpecificData("users", ["*"], ["id"=>$data[0]['created_by']], '', 'User');

        //if 'company' value in default parameter is checked in mode 'QUO IN'         
        if($quoType==1){
            //supplier must be value of 'company' parameter
            if($data[0]['buyer']!=$parameterData['company']){
                redirectWithMessage([['Mohon isi data buyer dengan benar', 0 ]], getLastVisitedPage());
            }

            $quoNumber = filterUserInput($_POST['quo_number']);
        }else{
            //if 'company' value in default parameter is checked in mode 'QUO OUT' 
            //buyer must be value of 'company' parameter
            if($data[0]['supplier']!=$parameterData['company']){
                redirectWithMessage([['Mohon isi data supplier dengan benar', 0 ]], getLastVisitedPage());
            }

            // For numbering format purpose
            $thisYear = date('Y');
            $thisMonth = convertToRoman(date('m'));
            $countDataInThisYear = $builder->custom("select count(*) as total_data from form_po where date_format(doc_date, '%Y') in ($thisYear) and po_or_quo=0", "Document");
            
            $numbering=$countDataInThisYear[0]->total_data;
            $numbering=  str_pad($numbering+1, 3, '0', STR_PAD_LEFT);
            $userCode = strtoupper($userData[0]->code);
            $companyCode = strtoupper($companyData[0]->code);
            $quoNumber = $numbering."/QUO/".$companyCode."/SNC-".$userCode."/".$thisMonth."/".date('Y');
            // end of numbering format
        }

        //End of check


        $dataKeys= array_keys($data[1]);
        
        //check whether the value between keys is equal
        $value=0;
        $isSame=true;
        for($i=0;$i<count($dataKeys);$i++){
            $countValue = count($data[1][$dataKeys[$i]]);
            if($i==0){
                $value = $countValue;
            }
            if($countValue!=$value){
                $isSame=false;
            }
        }

        if(!$isSame){
            redirectWithMessage([["Mohon isi data dengan lengkap", 0]], getLastVisitedPage());
        }

        //insert to db:form_po
        $insertToFormPo = $builder->insert('form_po', $data[0]);
        
        if(!$insertToFormPo){
            recordLog('QUO form', returnMessage()['quoForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }

        $idFormPo = $builder->getPdo()->lastInsertId();

        //insert to db:form_quo
        $insertToFormQuo = $builder->insert("form_quo", ['title' => $title, 'quo'=>$idFormPo, 'quo_number'=>$quoNumber]);

        if(!$insertToFormQuo){
            recordLog('QUO form', returnMessage()['quoForm']['createFail'] );
            redirectWithMessage(['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0],getLastVisitedPage());
            exit();
        }
        
        $idFormQuo = $builder->getPdo()->lastInsertId();

        //grouping data
        $newDataRecap=[];
        for($i=0; $i<$value; $i++){
            $newData=[];
            foreach($dataKeys as $key){
                $newData[$key]=$data[1][$key][$i];
            }
            $newData['quo'] = $idFormQuo;
            array_push($newDataRecap, $newData);
        }

        //$newDataRecap is the data that will be inserted into table po_product
        //$data[0] is the data that will be inserted into table form_po
        
        $isSuccessInsertToQuoProduct=true;
        for($i=0; $i<count($newDataRecap); $i++){
            $insertToQuoProduct = $builder->insert('quo_product', $newDataRecap[$i]);
            if(!$insertToQuoProduct){
                $isSuccessInsertToQuoProduct=false;
            }
        }

        //dd($insertToPoProduct);

        $insertToDocumentData = $builder->insert("document_data", ['document'=>'9', 'document_number'=>$idFormQuo]);
        if(!$insertToDocumentData){
            recordLog('Quotation form', returnMessage()['quoForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }

        if(!$isSuccessInsertToQuoProduct){
            recordLog('Quotation form', returnMessage()['quoForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('Quotation form', returnMessage()['quoForm']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['quoForm']['createSuccess'] ,1]],getLastVisitedPage());
    }

    public function quoFormDetail(){
        if(!$this->role->can("view-data-quo")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['view'] , 0]], getLastVisitedPage());
            }
        }

        $id= filterUserInput($_GET['quo']);

        $builder = App::get('builder');

        $uploadFiles=$builder->getSpecificData('upload_files', ['*'], ['public'=>1], '', 'Document');

        $products=$builder->getAllData('products', 'Product');

        $attachments=$builder->custom("SELECT b.id, 
        c.upload_file,
        c.title, 
        date_format(b.created_at, '%d %M %Y') as created_at, 
        b.description
        FROM document_data as a RIGHT JOIN document_attachments as b on a.id=b.document_data 
        INNER JOIN upload_files as c on b.attachment=c.id
        WHERE a.document=9 and a.document_number=$id","Document");

        /* All about Quo revision */
        $quoRevision=[];
        $countDataQuoRevision = $builder->custom("select count(*) as total_data from quo_revision where form_quo=$id", "Document")[0]->total_data;
        //dd($countDataQuoRevision);
        if(isset($_GET['revision']) && !empty($_GET['revision']) && $_GET['revision'] != null && $_GET['revision'] != 'null'){

            $revisionNumber= filterUserInput($_GET['revision']);

            $quoData = $builder->custom("SELECT k.quo_number as quo_number, DATE_FORMAT(j.doc_date, '%d %M %Y') as quo_date, j.doc_date as dd,
            f.name as buyer, f.address as baddress, f.phone as bphone, IFNULL(f.fax, '-')as bfax,
            a.pic_buyer, 
            g.name as supplier, g.address as saddress, g.phone as sphone, IFNULL(g.fax, '-') as sfax,
            a.pic_supplier, 
            i.name as currency, a.currency as cid, a.ppn, 
            b.name as created_by, DATE_FORMAT(j.created_at, '%d %M %Y') as created_at, 
            c.name as updated_by, DATE_FORMAT(j.updated_at, '%d %M %Y') as updated_at, 
            d.name as acknowledged_by, DATE_FORMAT(a.acknowledged_at, '%d %M %Y') as  acknowledged_at, 
            e.name as approved_by, DATE_FORMAT(a.approved_at, '%d %M %Y') as approved_at,  
            h.id as ddata,
            j.created_by as cbid,
            a.approved_by as abid,
            a.remark,
            j.revision_number,
            l.id as po,
            a.ppn
            FROM `form_po` as a 
            inner join form_quo as k on a.id=k.quo
            inner join quo_revision as j on k.id=j.form_quo
            inner join users as b on j.created_by=b.id 
            inner join users as c on j.updated_by=c.id 
            inner join users as d on a.acknowledged_by=d.id
            inner join users as e on a.approved_by=e.id 
            inner join companies as f on a.buyer=f.id
            inner join companies as g on a.supplier=g.id
            inner join document_data as h on h.document_number=k.id
            inner join currency as i on a.currency=i.id
            left join po_quo as l on l.quo=k.id
            WHERE h.document=9 and k.id=$id and j.revision_number=$revisionNumber", 'Document');

            $quoDetailData = $builder->custom("SELECT a.id, IFNULL(c.part_number, '-') as part_number, c.name as product, 
            a.product as pid,
            a.quantity, 
            a.price_unit,
            a.item_discount,
            a.quantity*a.price_unit as total,
            a.status as sid,
            case a.status when 0 then 'Belum disetujui' when 1 then 'Disetujui' when 2 then 'Ditolak' when 3 then 'Perlu revisi' end as status  
            FROM `quo_product` as a 
            inner join form_quo as d on a.quo=d.id
            inner join quo_revision as e on d.id=e.form_quo
            inner join quo_revision as f on a.revision=f.id
            inner join form_po as b on b.id=d.quo 
            inner join products as c on a.product=c.id 
            WHERE d.id=$id and f.revision_number=$revisionNumber
            GROUP BY a.id
            ORDER BY a.id", 'Document');

        }else{
            $quoData = $builder->custom("SELECT k.quo_number as quo_number, DATE_FORMAT(a.doc_date, '%d %M %Y') as quo_date,
            a.doc_date as dd,
            f.name as buyer, f.address as baddress, f.phone as bphone, IFNULL(f.fax, '-')as bfax,
            a.pic_buyer, 
            g.name as supplier, g.address as saddress, g.phone as sphone, IFNULL(g.fax, '-') as sfax,
            a.pic_supplier, 
            i.name as currency, a.currency as cid, a.ppn, 
            b.name as created_by, DATE_FORMAT(a.created_at, '%d %M %Y') as created_at, 
            c.name as updated_by, DATE_FORMAT(a.updated_at, '%d %M %Y') as updated_at, 
            d.name as acknowledged_by, DATE_FORMAT(a.acknowledged_at, '%d %M %Y') as  acknowledged_at, 
            e.name as approved_by, DATE_FORMAT(a.approved_at, '%d %M %Y') as approved_at,  
            h.id as ddata,
            a.created_by as cbid,
            a.approved_by as abid,
            a.remark,
            l.id as po,
            a.ppn
            FROM `form_po` as a 
            inner join form_quo as k on a.id=k.quo
            inner join users as b on a.created_by=b.id 
            inner join users as c on a.updated_by=c.id 
            inner join users as d on a.acknowledged_by=d.id
            inner join users as e on a.approved_by=e.id 
            inner join companies as f on a.buyer=f.id
            inner join companies as g on a.supplier=g.id
            inner join document_data as h on h.document_number=k.id
            inner join currency as i on a.currency=i.id
            left join po_quo as l on l.quo=k.id
            WHERE h.document=9 and k.id=$id", 'Document');


            $quoDetailData = $builder->custom("SELECT a.id, IFNULL(c.part_number, '-') as part_number, c.name as product, 
            a.product as pid,
            a.quantity, 
            a.price_unit,
            a.item_discount,
            a.quantity*a.price_unit as total,
            a.status as sid,
            case a.status when 0 then 'Belum disetujui' when 1 then 'Disetujui' when 2 then 'Ditolak' when 3 then 'Perlu revisi' end as status  
            FROM `quo_product` as a 
            inner join form_quo as d on a.quo=d.id
            inner join form_po as b on b.id=d.quo 
            inner join products as c on a.product=c.id 
            WHERE d.id=$id and a.revision is null
            ORDER BY a.id", 'Document');

        }
        //dd($quoDetailData);
        /* End of Quo revision */

        if(count($quoData)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], '/form/quo');
        }

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode(["quoData"=>$quoData, "quoDetailData"=>$quoDetailData]);
            exit();
        }else{
            view('/form/quotation_form_detail', compact('quoData', 'quoDetailData', 'uploadFiles', 'attachments', 'products', 'countDataQuoRevision'));
        }
    }

    public function quoFormUpdate(){
        if(!$this->role->can("update-data-quo")){
            redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['update'] , 0]], getLastVisitedPage());
        }

        $builder = App::get('builder');

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        $quo = filterUserInput($_POST['quo']);

        if(isEmpty($quo)){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }

        $placeholderQuoFormUpdate = [
            'doc_date' => 'required',
            'pic_buyer' => 'required',
            'pic_supplier' => 'required',
            'currency' => 'required',
            'ppn' => 'required',
            'remark' => ''
        ];

        foreach($placeholderQuoFormUpdate as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }
        

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        //Update data
        $quoNumber = filterUserInput($_POST['quo_number']);
        $updateQuoNumber = $builder->update('form_quo', ['quo_number' => $quoNumber], ['id' => $quo], '', 'Document');

        //dd($idFormQuo);

        if(!$updateQuoNumber){
            recordLog('QUO form', returnMessage()['quoForm']['updateFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
        }else{
            recordLog('QUO form', returnMessage()['quoForm']['updateSuccess'] );
        }
        
        //Get id of the quo $quo
        $idFormQuo = $builder->getSpecificData("form_quo", ['quo'], ['id' => $quo], '', 'Document');

        $idFormQuo = $idFormQuo[0]->quo;

        //Update data
        $updateQuoForm = $builder->update('form_po', $data, ['id' => $idFormQuo], '', 'Document');

        //dd($idFormQuo);

        if(!$updateQuoForm){
            recordLog('QUO form', returnMessage()['quoForm']['updateFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
        }else{
            recordLog('QUO form', returnMessage()['quoForm']['updateSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['quoForm']['updateSuccess'] ,1]],getLastVisitedPage());

    }

    public function quoFormRemove(){
        if(!$this->role->can("update-data-quo")){
            redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['delete'] , 0]], getLastVisitedPage());
        }

        //remove quo form
        //first, search of id of po_form based on id of quo_form
        //then remove it 

        //id of quo form
        $quo = filterUserInput($_POST['quo']);

        $builder = App::get('builder');

        $getidQuo = $builder->getSpecificData("form_quo", ['quo'], ['id' => $quo], '', 'Document')[0]->quo;

        $deleteQuoForm = $builder->delete("form_po", ['id' => $getidQuo], '', 'Document');

        if(!$deleteQuoForm){
            redirectWithMessage([[ returnMessage()['quoForm']['deleteFail'] , 0]], getLastVisitedPage());
        }

        recordLog('QUO form', returnMessage()['quoForm']['deleteSuccess'] );

        $builder->save();

        redirectWithMessage([[returnMessage()['quoForm']['deleteSuccess'], 1]], '/form/quo');
    }

    public function quoFormItemUpdate(){
        if(!$this->role->can("update-data-quo")){
            redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['update'] , 0]], getLastVisitedPage());
        }

        $id = filterUserInput($_POST['quo-item']);
        
        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderPoForm[1] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        //Make status of the approval state to be "not yet approved" after updating the quo product data
        $data['status'] = 0;

        $builder = App::get('builder');

        $idOfQuoItem = $builder->getSpecificData('quo_product', ['quo'], ['id'=>$id], '', 'Document');

        $idFormQuo = $builder->getSpecificData('form_quo', ['quo'], ['id'=>$idOfQuoItem[0]->quo], '', 'Document');

        $idOfQuoForm = $idFormQuo[0]->quo;
        
        $updateQuoForm = $builder->update('form_po', ['updated_by'=>substr($_SESSION['sim-id'], 3, -3)], ['id'=>$idOfQuoForm], '', "Document");

        if(!$updateQuoForm){
            recordLog('QUO form', returnMessage()['quoForm']['updateFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
        }
        
        $updateQuoProduct = $builder->update("quo_product", $data, ['id'=>$id], "", "Document");

        if(!$updateQuoProduct){
            recordLog('QUO form', returnMessage()['quoForm']['updateFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
        }else{
            recordLog('QUO form', returnMessage()['quoForm']['updateSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['quoForm']['updateSuccess'] ,1]],getLastVisitedPage());

    }

    public function quoFormCreateRevision(){
        if(!$this->role->can('create-data-quo')){
            redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['create'] , 0]], getLastVisitedPage());
        }

        $builder = App::get('builder');

        $idFormQuo = filterUserInput($_POST['quo']);
        $revDate = filterUserInput($_POST['doc_date']);
        $createdBy = substr($_SESSION['sim-id'], 3, -3);
        $updatedBy = substr($_SESSION['sim-id'], 3, -3);
        
        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        
        foreach($this->placeholderPoForm[1] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        /*
        insert to quo_revision
        insert to quo_product, same quo but give revision value
        */
        $countDataRevision = $builder->custom("select count(*) as total_data from quo_revision where form_quo=$idFormQuo", "Document");

        //Insert to quo_revision table
        $insertToQuoRevision = $builder->insert('quo_revision', ['form_quo'=>$idFormQuo, 'revision_number'=>$countDataRevision[0]->total_data+1, 'doc_date' => $revDate, 'created_by' => $createdBy, 'updated_by' => $updatedBy]);

        $idQuoRevision = $builder->getPdo()->lastInsertId();

        if(!$insertToQuoRevision){
            recordLog('QUO form', returnMessage()['quoForm']['createFail'] );
            redirectWithMessage([[returnMessage()['quoForm']['createFail'], 0]],getLastVisitedPage());
        }

        $dataKeys= array_keys($this->placeholderPoForm[1]);
        
        //check whether the value between keys is equal
        $value=0;
        $isSame=true;
        for($i=0;$i<count($dataKeys);$i++){
            $countValue = count($data[$dataKeys[$i]]);
            if($i==0){
                $value = $countValue;
            }
            if($countValue!=$value){
                $isSame=false;
            }
        }

        if(!$isSame){
            redirectWithMessage([["Mohon isi data dengan lengkap", 0]], getLastVisitedPage());
        }

        //grouping data
        $newDataRecap=[];
        for($i=0; $i<$value; $i++){
            $newData=[];
            foreach($dataKeys as $key){
                $newData[$key]=$data[$key][$i];
            }
            $newData['quo'] = $idFormQuo;
            $newData['revision'] = $idQuoRevision;
            array_push($newDataRecap, $newData);
        }

        $isSuccessInsertToQuoProduct=true;
        for($i=0; $i<count($newDataRecap); $i++){
            $insertToQuoProduct = $builder->insert('quo_product', $newDataRecap[$i]);
            if(!$insertToQuoProduct){
                $isSuccessInsertToQuoProduct=false;
            }
        }

        if(!$isSuccessInsertToQuoProduct){
            recordLog('Quotation form', returnMessage()['quoForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('Quotation form', returnMessage()['quoForm']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['quoForm']['createSuccess'] ,1]],getLastVisitedPage());
    }

    public function quoFormItemRemove(){
        if(!$this->role->can("remove-data-quo")){
            redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['delete'] , 0]], getLastVisitedPage());
        }

        //id of the quo data item
        $id = filterUserInput($_POST['quo-item']);
        
        //check whether the user that perform deletion action is same with the quo data's creator
        $thisAccount = substr($_SESSION['sim-id'], 3, -3);

        $builder = App::get('builder');

        $idOfQuoItem = $builder->getSpecificData('quo_product', ['quo'], ['id'=>$id], '', 'Document');
        
        $idFormQuo = $builder->getSpecificData('form_quo', ['quo'], ['id'=>$idOfQuoItem[0]->quo], '', 'Document');

        $idOfQuoForm = $idFormQuo[0]->quo;
                
        $accountShouldBeAllow = $builder->custom("SELECT created_by FROM form_po WHERE id=$idOfQuoForm and po_or_quo=0", "Document");

        if($accountShouldBeAllow[0]->created_by != $thisAccount){
            redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['delete'] , 0]], getLastVisitedPage());
        }

        $removeQuoDataItem = $builder->delete("quo_product", ['id'=>$id], "", "Document");

        $updateQuoForm = $builder->update('form_po', ['updated_by' => substr($_SESSION['sim-id'], 3, -3)], ['id' => $idOfQuoForm], '', "Document");
        
        if(!$removeQuoDataItem){
            recordLog('QUO', "Removal data quo item gagal");
            redirect(getLastVisitedPage());
            exit();
        }else{
            recordLog('QUO', "Removal data quo item berhasil");
        }

        $builder->save();
        
        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['quoForm']['deleteSuccess'], 1]], getLastVisitedPage());

    }

    public function quoFormCreateNewItem(){
        if(!$this->role->can('create-data-quo')){
            redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['create'] , 0]], getLastVisitedPage());
        }

        $id = filterUserInput($_POST['quo']);

        if(isEmpty($id) && !isset($id)){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }

        $builder = App::get('builder');

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderPoForm[1] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        $data['quo']=$id;

        //if only the items is for quo revision
        if(isset($_POST['revision']) && !empty($_POST['revision'])){
            $data['revision']=filterUserInput($_POST['revision']);
        }

        $insertToQuoProduct = $builder->insert('quo_product', $data);

        if(!$insertToQuoProduct){
            recordLog('QUO form', returnMessage()['quoForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('QUO form', returnMessage()['quoForm']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['quoForm']['createSuccess'] ,1]], getLastVisitedPage());
        
    }

    public function quoFormApproval(){
        if(!$this->role->can('approval-data-quo')){
            redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['approval'] , 0]], getLastVisitedPage());
        }
        $idOfQuoItem = filterUserInput($_POST['quo-item']);
        $approval = filterUserInput($_POST['approval']);
        $idFormQuo = filterUserInput($_POST['quo']);
        
        if(isEmpty([$idOfQuoItem, $approval, $idFormQuo])){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'] , 0]], getLastVisitedPage());
        }

        $builder = App::get('builder');

        //If the quo item is approved, $approval will be 1
        //if the quo item is reject, $approval will be 2
        if($approval!='1' && $approval!='2' && $approval!='3'){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'] , 0]], getLastVisitedPage());
        }

        $updateQuoItem = $builder->update('quo_product', ['status'=>$approval], ['id'=>$idOfQuoItem, 'quo'=>$idFormQuo], '&&','Document');

        if(!$updateQuoItem){
            recordLog('QUO form', returnMessage()['quoForm']['updateFail'] );
            redirectWithMessage([[ returnMessage()['quoForm']['updateFail'] , 0]], getLastVisitedPage());
        }else{
            recordLog('QUO form', returnMessage()['quoForm']['updateSuccess'] );
        }

        $builder->save();
        
        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['quoForm']['updateSuccess'] ,1]],getLastVisitedPage());
    }

    public function quoFormNumber(){
        if(!$this->role->can("view-data-quo")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['view'] , 0]], getLastVisitedPage());
            }
        }

        $builder = App::get('builder');

        $company = filterUserInput($_GET['company']);
        $poType = filterUserInput($_GET['po_type']);

        //$poType=1 -> po in
        //$poType=0 -> po out
        if($poType==1){
            $whereClause="a.buyer=$company";
        }elseif($poType==0){
            $whereClause="a.supplier=$company";
        }

        /* $quoNumber = $builder->custom("SELECT b.id, b.quo_number 
        FROM `form_po` as a 
        INNER JOIN form_quo as b on a.id=b.quo 
        WHERE $whereClause and not exists (select b.quo_number from form_po as a inner join form_quo as b on a.id=b.quo inner join po_quo as c on c.quo=b.id)
        ORDER BY b.id" , "Document"); */

        //Get quo number that not yet processed to po 
        $quoNumber = $builder->custom("SELECT b.id, b.quo_number, c.revision_number  
        FROM `form_po` as a 
        INNER JOIN form_quo as b on a.id=b.quo
        LEFT JOIN quo_revision as c on b.id=c.form_quo 
        WHERE b.id not in (select b.id from form_po as a inner join form_quo as b on a.id=b.quo inner join po_quo as c on c.quo=b.id where $whereClause) 
        and $whereClause
        ORDER BY b.id", "Document");
        
        echo json_encode($quoNumber);

    }

//=====================================================================================================//   
    
    /* PURCHASE ORDER */
    public function poFormIndex(){
        if(!$this->role->can('view-data-po')){
            redirectWithMessage([[ returnMessage()['poForm']['accessRight']['view'] , 0]], getLastVisitedPage());
        }

        $builder = App::get('builder');

        $products=$builder->getAllData('products', 'Product');

        $partners=$builder->getAllData('companies', 'Partner');

        $approvalPerson = $builder->custom("SELECT a.user_id, b.name FROM role_user as a inner join users as b on a.user_id=b.id WHERE a.role_id=2", "Document");

        $parameterData=[];
        $parameters = $builder->getAllData('default_parameter', 'Internal');
        for($i=0; $i<count($parameters); $i++){
            $parameterData[$parameters[$i]->parameter]=$parameters[$i]->value;
        }

        //Searching for specific category
        //category: buyer, supplier, po_date, product, is there any quo
        
        $whereClause='';
    
        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            $search['buyer']=filterUserInput($_GET['buyer']);
            $search['supplier']=filterUserInput($_GET['supplier']);
            $search['product']=filterUserInput($_GET['product']);

            $searchByDateStart=filterUserInput($_GET['po_date_start']);
            $searchByDateEnd=filterUserInput($_GET['po_date_end']);
    
            $operator='&&';

            foreach($search as $k => $v){
                if(!empty($search[$k])){
                    $whereClause.=$k."=".$v.$operator;
                }
            }

            if(!empty($searchByDateStart) && !empty($searchByDateEnd)){
                $whereClause.=" a.created_at between '$searchByDateStart' and '$searchByDateEnd'";
            }elseif(!empty($searchByDateStart)){
                $whereClause.=" a.created_at like '%$searchByDateStart%'";
            }elseif(!empty($searchByDateEnd)){
                $whereClause.=" a.created_at like '%$searchByDateEnd%'";
            }
            //dd($whereClause);
            $whereClause=trim($whereClause, '&&');
    
        }

        if($whereClause==''){
            $whereClause=1;
        }

        //End of searching

        /* $quoData = $builder->custom("SELECT a.id, h.quo_number,
        date_format(a.doc_date, '%d %M %Y') as doc_date, 
        c.name as supplier,
        d.name as buyer,
        GROUP_CONCAT(f.name ORDER by f.id asc SEPARATOR '<br>') as product
        FROM `form_po` as a 
        inner join po_quo as g on g.quo=a.id
        inner join companies as c on a.supplier=c.id
        inner join companies as d on a.buyer=d.id
        inner join po_product as e on g.id=e.doc
        inner join products as f on e.product=f.id
        inner join form_quo as h on a.id=h.quo
        where $whereClause && po_or_quo=0
        group by a.id
        order by a.id","Document"); */


        $poData = $builder->custom("SELECT a.id, g.po_number, e.quantity,
        date_format(a.doc_date, '%d %M %Y') as doc_date, 
        c.name as supplier,
        d.name as buyer,
        GROUP_CONCAT(f.name ORDER by f.id asc SEPARATOR '<br>') as product,
        a.supplier as sid,
        a.buyer as bid
        FROM `form_po` as a 
        inner join po_quo as g on g.po=a.id
        inner join companies as c on a.supplier=c.id
        inner join companies as d on a.buyer=d.id
        inner join po_product as e on g.id=e.doc
        inner join products as f on e.product=f.id
        LEFT JOIN form_quo as h on g.quo=h.id 
        WHERE $whereClause && po_or_quo=1
        GROUP BY a.id
        ORDER BY a.id DESC","Document");


        if(isset($_GET['quo'])&&!empty($_GET['quo'])){

            $withQuo = filterUserInput($_GET['quo']);
            
            if($withQuo == 1){
                /* $poData = $builder->custom("SELECT d.id, b.quo_number, 
                a.po_number,
                date_format(d.doc_date, '%d %M %Y') as doc_date, 
                e.name as supplier,
                f.name as buyer, 
                GROUP_CONCAT(g.name ORDER by g.id asc SEPARATOR '<br>') as product 
                FROM po_quo as a 
                INNER JOIN form_quo as b on a.quo=b.id 
                INNER JOIN quo_product as c on b.id=c.quo 
                INNER JOIN form_po as d on a.po=d.id 
                INNER JOIN companies as e on d.supplier=e.id 
                INNER JOIN companies as f on d.buyer=f.id 
                INNER JOIN products as g on c.product=g.id
                WHERE $whereClause && po_or_quo=1
                GROUP BY d.id
                ORDER BY d.id DESC", "Document"); */
                
                $poData = $builder->custom("SELECT d.id, b.quo_number, 
                a.po_number,
                date_format(d.doc_date, '%d %M %Y') as doc_date, 
                e.name as supplier,
                f.name as buyer, 
                GROUP_CONCAT(DISTINCT(g.name) ORDER by g.id asc SEPARATOR '<br>') as product,
                c.quantity,
                d.supplier as sid,
                d.buyer as bid
                FROM po_quo as a 
                INNER JOIN form_quo as b on a.quo=b.id 
                INNER JOIN form_po as d on a.po=d.id 
                LEFT JOIN quo_product as c on a.quo=c.quo
                INNER JOIN companies as e on d.supplier=e.id 
                INNER JOIN companies as f on d.buyer=f.id 
                LEFT JOIN products as g on c.product=g.id
                WHERE $whereClause && po_or_quo=1
                GROUP BY d.id
                ORDER BY d.id DESC", "Document");
            }
        }

        //download all the data
        if(isset($_GET['download']) && $_GET['download']==true){
            
            $dataColumn = ['doc_date', 'po_number', 'quo_number', 'supplier', 'buyer', 'product', 'quantity'];

            $this->download(toDownload($poData, $dataColumn));

        }

        //Pagination
        //only show data for specified page
        if(isset($_GET['p'])){
            $p=$_GET['p'];

            if(!is_numeric($p)){
                redirectWithMessage([["Halaman yang anda tuju tidak diketahui",0]],getLastVisitedPage());
            }
            
        }else{
            $p=1;
        }
        
        $limitStart=$p*maxDataInAPage()-maxDataInAPage();

        $pages=ceil(count($poData)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($poData);
        
        $poData=array_slice($poData,$limitStart,maxDataInAPage());

        setSearchPage();
        
        view('form/po_form', compact('poData', 'partners', 'approvalPerson', 'products', 'pages', 'sumOfAllData', 'quoData', 'poWithQuoData', 'parameterData'));
        
    }

    public function poFormCreate(){
        if(!$this->role->can('create-data-po')){
            redirectWithMessage([[ returnMessage()['poForm']['accessRight']['create'] , 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        for($i=0; $i<count($this->placeholderPoForm); $i++){
            foreach($this->placeholderPoForm[$i] as $k => $v){
                if(checkRequirement($v, $k, $_POST[$k])){
                    $data[$i][$k]=filterUserInput($_POST[$k]);
                }else{
                    $passingRequirement=false;
                }  
            }
        }

        //if not the passing requirements
        if(!$passingRequirement){
            //redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            redirect(getLastVisitedPage());
        }

        $data[0]['created_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data[0]['updated_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data[0]['po_or_quo'] = 1;

        //Check whether data inserted is appropriate with the default parameters
        //$poType = filterUserInput($_POST['po_type']);
        $poType = filterUserInput($_POST['po_type']);

        /* if(isset($poType)){
            redirectWithMessage([['Mohon isi form dengan benar', 0 ]], getLastVisitedPage());
        } */

        $builder = App::get('builder');
    
        $parameterData=[];
        $parameters = $builder->getAllData('default_parameter', 'Internal');
        for($i=0; $i<count($parameters); $i++){
            $parameterData[$parameters[$i]->parameter]=$parameters[$i]->value;
        }

        $companyData = $builder->getSpecificData("companies", ['*'], ["id"=>$data[0]['buyer']], '', 'Partner');
        $userData = $builder->getSpecificData("users", ["*"], ["id"=>$data[0]['created_by']], '', 'User');

        //if 'company' value in default parameter is checked in mode 'PO IN' 
        
        if($poType==1){
            //supplier must be value of 'company' parameter
            if($data[0]['supplier']!=$parameterData['company']){
                redirectWithMessage([['Mohon isi data supplier dengan benar', 0 ]], getLastVisitedPage());
            }
            $poNumber = filterUserInput($_POST['po_number']);
        }else{
            //if 'company' value in default parameter is checked in mode 'PO OUT' 
            //buyer must be value of 'company' parameter
            if($data[0]['buyer']!=$parameterData['company']){
                redirectWithMessage([['Mohon isi data buyer dengan benar', 0 ]], getLastVisitedPage());
            }

            // For numbering format purpose
            $thisYear = date('Y');
            $thisMonth = convertToRoman(date('m'));
            $countDataInThisYear = $builder->custom("select count(*) as total_data from form_po where date_format(doc_date, '%Y') in ($thisYear) and po_or_quo=1", "Document");
            
            $numbering=$countDataInThisYear[0]->total_data;
            $numbering=  str_pad($numbering+1, 3, '0', STR_PAD_LEFT);
            $userCode = strtoupper($userData[0]->code);
            $companyCode = strtoupper($companyData[0]->code);
            $poNumber = $numbering."/PO/".$companyCode."/SNC-".$userCode."/".$thisMonth."/".date('Y');
            // End of numbering format
        }

        //End of check

        //check whether the value between keys is equal
        $dataKeys= array_keys($data[1]);

        $value=0;
        $isSame=true;
        for($i=0;$i<count($dataKeys);$i++){
            $countValue = count($data[1][$dataKeys[$i]]);
            if($i==0){
                $value = $countValue;
            }
            if($countValue!=$value){
                $isSame=false;
            }
        }

        if(!$isSame){
            redirectWithMessage([["Mohon isi data product dengan lengkap", 0]], getLastVisitedPage());
        }
        
        //dd($data);

        //insert to db:form_po
        $insertToFormPo = $builder->insert('form_po', $data[0]);

        //dd($insertToFormPo);

        if(!$insertToFormPo){
            recordLog('PO form', returnMessage()['poForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }

        $idPoForm = $builder->getPdo()->lastInsertId();

        //insert to db:po_quo
        $insertToQuoPo = $builder->insert("po_quo", ['po'=>$idPoForm, 'po_number'=>$poNumber]);

        $idQuoPo = $builder->getPdo()->lastInsertId();

        $newDataRecap=[];
        for($i=0; $i<$value; $i++){
            $newData=[];
            foreach($dataKeys as $key){
                $newData[$key]=$data[1][$key][$i];
            }
            $newData['doc'] = $idQuoPo;
            array_push($newDataRecap, $newData);
        }

        //$newDataRecap is the data that will be inserted into table po_product
        //$data[0] is the data that will be inserted into table form_po
        
        //dd($newDataRecap);
        //dd($data);

        $isSuccessInsertToPoProduct=true;
        for($i=0; $i<count($newDataRecap); $i++){
            $insertToPoProduct = $builder->insert('po_product', $newDataRecap[$i]);
            if(!$insertToPoProduct){
                $isSuccessInsertToPoProduct=false;
            }
        }

        $insertToDocumentData = $builder->insert("document_data", ['document'=>'5', 'document_number'=>$idPoForm]);
        if(!$insertToDocumentData){
            recordLog('PO form', returnMessage()['poForm']['createFail'] );
            redirectWithMessage(['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0],getLastVisitedPage());
            exit();
        }


        if(!$isSuccessInsertToPoProduct){
            recordLog('PO form', returnMessage()['poForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('PO form', returnMessage()['poForm']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['poForm']['createSuccess'] ,1]],getLastVisitedPage());
    }

    public function poFormDetail(){

        if(!$this->role->can("view-data-po")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['poForm']['accessRight']['view'] , 0]], getLastVisitedPage());
            }
        }

        $id = filterUserInput($_GET['po']);

        $builder = App::get('builder');

        $uploadFiles=$builder->getAllData('upload_files', 'Document');

        $products=$builder->getAllData('products', 'Product');

        $attachments=$builder->custom("SELECT b.id, 
        c.upload_file,
        c.title, 
        date_format(b.created_at, '%d %M %Y') as created_at, 
        b.description
        FROM document_data as a RIGHT JOIN document_attachments as b on a.id=b.document_data 
        INNER JOIN upload_files as c on b.attachment=c.id
        WHERE a.document=5 and a.document_number=$id","Document");

        $poData = $builder->custom("SELECT a.id as po, j.po_number as po_number, DATE_FORMAT(a.doc_date, '%d %M %Y') as po_date, a.doc_date as dd,
		f.name as buyer, f.address as baddress, f.phone as bphone, IFNULL(f.fax, '-')as bfax,
        a.pic_buyer, 
        g.name as supplier, g.address as saddress, g.phone as sphone, IFNULL(g.fax, '-') as sfax,
        a.pic_supplier, 
        i.name as currency, a.ppn, 
        b.name as created_by, DATE_FORMAT(a.created_at, '%d %M %Y') as created_at, 
        c.name as updated_by, DATE_FORMAT(a.updated_at, '%d %M %Y') as updated_at, 
        d.name as acknowledged_by, DATE_FORMAT(a.acknowledged_at, '%d %M %Y') as  acknowledged_at, 
        e.name as approved_by, DATE_FORMAT(a.approved_at, '%d %M %Y') as approved_at,  
        h.id as ddata,
        a.remark,
        a.created_by as cbid,
        a.approved_by as abid,
        k.quo_number,
        j.quo,
        l.revision_number,
        k.id,
        a.currency as cid,
        m.id as do
        FROM `form_po` as a 
        inner join users as b on a.created_by=b.id 
        inner join users as c on a.updated_by=c.id 
        inner join users as d on a.acknowledged_by=d.id
        inner join users as e on a.approved_by=e.id 
        inner join companies as f on a.buyer=f.id
        inner join companies as g on a.supplier=g.id
        inner join document_data as h on h.document_number=a.id
        inner join currency as i on a.currency=i.id
        inner join po_quo as j on a.id=j.po
        left join form_quo as k on j.quo=k.id
        left join quo_revision as l on j.quo_revision=l.id
        left join form_do as m on j.id=m.po_quo
        WHERE h.document=5 and a.id=$id", 'Document');

        /* $poDetailData = $builder->custom("SELECT a.id, IFNULL(c.part_number, '-') as part_number, c.name as product, 
        a.product as pid,
        a.quantity, 
        a.price_unit,
        a.item_discount,
        a.quantity*a.price_unit as total,
        case a.status when 0 then '-' when 1 then 'Disetujui' when 2 then 'Ditolak' when 3 then 'Perlu revisi' end as status  
        FROM `po_product` as a 
        inner join po_quo as d on d.id=a.doc
        inner join form_po as b on b.id=d.po 
        inner join products as c on a.product=c.id 
        WHERE b.id=$id
        ORDER BY a.id", 'Document'); */

        $poQuoData = $builder->getSpecificData("po_quo", ['*'], ['po'=>$id], '', 'Document');
        $revisionQuo = $poQuoData[0]->quo_revision;
        if($revisionQuo==null || $revisionQuo==''){
            $whereClause='&& a.revision is null';
        }else{
            $whereClause='&& a.revision='.$revisionQuo;
        }

        $poDetailData = $builder->custom("SELECT a.id, IFNULL(c.part_number, '-') as part_number, c.name as product, 
        a.product as pid,
        a.quantity, 
        a.price_unit,
        a.item_discount,
        a.quantity*a.price_unit as total,
        a.status as sid,
        case a.status when 0 then 'Belum disetujui' when 1 then 'Disetujui' when 2 then 'Ditolak' when 3 then 'Perlu revisi' end as status  
        FROM `po_product` as a 
        inner join po_quo as d on d.id=a.doc
        inner join form_po as b on b.id=d.po 
        inner join products as c on a.product=c.id 
        WHERE b.id=$id
        UNION
        SELECT a.id, IFNULL(c.part_number, '-') as part_number, c.name as product, 
        a.product as pid,
        a.quantity, 
        a.price_unit,
        a.item_discount,
        a.quantity*a.price_unit as total,
        a.status as sid,
        case a.status when 0 then 'Belum disetujui' when 1 then 'Disetujui' when 2 then 'Ditolak' when 3 then 'Perlu revisi' end as status  
        FROM `po_quo` as d
        inner join form_quo as e on d.quo=e.id
        inner join quo_product as a on a.quo=e.id
        inner join products as c on a.product=c.id
        WHERE d.po=$id $whereClause", 'Document');


        //dd($poDetailData);

        if(count($poData)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], getLastVisitedPage());
        }

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode(["poData"=>$poData, "poDetailData"=>$poDetailData]);
            exit();
        }else{
            view('/form/po_form_detail', compact('poData', 'poDetailData', 'uploadFiles', 'attachments', 'products'));
        }

    }

    public function poFormUpdate(){
        if(!$this->role->can("update-data-po")){
            redirectWithMessage([[ returnMessage()['poForm']['accessRight']['update'] , 0]], getLastVisitedPage());
        }

        $builder = App::get('builder');

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        $po = filterUserInput($_POST['po']);

        if(isEmpty($po)){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }

        $placeholderPOFormUpdate = [
            'doc_date' => 'required',
            'pic_buyer' => 'required',
            'pic_supplier' => 'required',
            'currency' => 'required',
            'ppn' => 'required',
            'remark' => ''
        ];

        foreach($placeholderPOFormUpdate as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }
        
        //if not the passing requirements
        if(!$passingRequirement){
            //redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            redirect($_SESSION['sim-messages'], getLastVisitedPage());
        }

        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);
        $poNumber = filterUserInput($_POST['po_number']);

        $builder = App::get('builder');

        $updatePoNumber = $builder->update('po_quo', ['po_number' => $poNumber], ['po'=>$po], '', 'Document');

        $updatePoForm = $builder->update("form_po", $data, ['id'=>$po], "", "Document");
 
        if(!$updatePoForm || !$updatePoNumber){
            recordLog('PO form', returnMessage()['poForm']['updateFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
        }
        
        recordLog('PO form', returnMessage()['poForm']['updateSuccess'] );

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['poForm']['updateSuccess'] ,1]],getLastVisitedPage());

    }

    public function poFormRemove(){
        if(!$this->role->can("update-data-po")){
            redirectWithMessage([[ returnMessage()['poForm']['accessRight']['delete'] , 0]], getLastVisitedPage());
        }

        //check whether po already have do
        //if so, when po removed then do also removed
        //remove po_quo will remove po_product also
        //then remove form_po

        $po = filterUserInput($_POST['po']);

        $builder = App::get('builder');

        $getidPoQuo = $builder->getSpecificData("po_quo", ['id'], ['po' => $po], '', 'Document')[0]->id;

        $getDoRelatedPo = $builder->getSpecificData("form_do", ['id'], ['po_quo' => $getidPoQuo], '', 'Document')[0]->id;

        $deletePOQuo = $builder->delete("po_quo", ['po' => $po], '', 'Document');

        $deletePOForm = $builder->delete("form_po", ['id' => $po], '', 'Document');

        $deleteStockRelated = $builder->delete("stock_relation", ['spec_doc' => $getDoRelatedPo, 'document' => 6], '&&', 'Document');

        if(!$deletePOQuo || !$deletePOForm || !$deleteStockRelated){
            redirectWithMessage([[ returnMessage()['poForm']['deleteFail'] , 0]], getLastVisitedPage());
        }

        recordLog('PO form', returnMessage()['poForm']['deleteSuccess'] );

        $builder->save();

        redirectWithMessage([[returnMessage()['poForm']['deleteSuccess'], 1]], '/form/po');

    }

    public function poItemUpdate(){
        if(!$this->role->can("update-data-po")){
            redirectWithMessage([[ returnMessage()['poForm']['accessRight']['update'] , 0]], getLastVisitedPage());
        }

        $id = filterUserInput($_POST['po-item']);
        
        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderPoForm[1] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        $builder = App::get('builder');

        $idOfPoItem = $builder->getSpecificData('po_product', ['doc'], ['id'=>$id], '', 'Document');

        $idOfPoItem = $builder->getSpecificData('po_quo', ['po'], ['id'=>$idOfPoItem[0]->doc], '', 'Document');

        //dd($idOfPoItem);
        
        $updatePoForm = $builder->update("form_po", ['updated_by'=>substr($_SESSION['sim-id'], 3, -3)], ['id'=>$idOfPoItem[0]->po], "", "Document");
        
        if(!$updatePoForm){
            recordLog('PO form', returnMessage()['poForm']['updateFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
        }
        
        $updatePoProduct = $builder->update("po_product", $data, ['id'=>$id], "", "Document");

        if(!$updatePoProduct){
            recordLog('PO form', returnMessage()['poForm']['updateFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
        }
        
        recordLog('PO form', returnMessage()['poForm']['updateSuccess'] );

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['poForm']['updateSuccess'] ,1]],getLastVisitedPage());
    }

    public function poItemRemove(){
        if(!$this->role->can("update-data-po")){
            redirectWithMessage([[ returnMessage()['poForm']['accessRight']['delete'] , 0]], getLastVisitedPage());
        }

        //id of the reimburse data item
        $id = filterUserInput($_POST['po-item']);
        $idOfPoForm = filterUserInput($_POST['po']);

        $builder = App::get('builder');

        //check whether po have quo or not
        //if have, delete the item from table quo_product
        //otherwise, delete the item from table po_product
        $haveQuo = true;
        $checkForQuo = $builder->getSpecificData("po_quo", ["quo"], ["po"=>$idOfPoForm], "", "Document");
        if($checkForQuo[0]->quo==null or empty($checkForQuo[0]->quo)){
            $haveQuo=false;
        }

        if($haveQuo){
            $removePoDataItem = $builder->delete("quo_product", ['id'=>$id], "", "Document");
        }else{
            $removePoDataItem = $builder->delete("po_product", ['id'=>$id], "", "Document");
        }
        
        if(!$removePoDataItem){
            recordLog('PO', "Removal data PO item gagal");
            redirectWithMessage([[ returnMessage()['poForm']['deleteFail'], 1]], getLastVisitedPage());
        }else{
            recordLog('PO', "Removal data PO item berhasil");
        }

        $builder->save();
        
        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['poForm']['deleteSuccess'], 1]], getLastVisitedPage());

    }

    public function poFormCreateNewItem(){
        if(!$this->role->can('create-data-po')){
            redirectWithMessage([[ returnMessage()['poForm']['accessRight']['create'] , 0]], getLastVisitedPage());
        }

        $id = filterUserInput($_POST['po']);

        $builder = App::get('builder');

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderPoForm[1] as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }
        
        $idOfPoQuo = $builder->getSpecificData("po_quo", ["*"], ["po"=>$id], "", "Document");

        $data['doc']=$idOfPoQuo[0]->id;

        $insertToPoProduct = $builder->insert('po_product', $data);

        if(!$insertToPoProduct){
            recordLog('PO form', returnMessage()['poForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('PO form', returnMessage()['poForm']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['poForm']['createSuccess'] ,1]],getLastVisitedPage());
        
    }

    public function poFormApproval(){
        redirect(getLastVisitedPage());
        exit();
    }

    public function poFormCreateFromQuo(){
        
        if(!$this->role->can('create-data-po')){
            redirectWithMessage([[ returnMessage()['poForm']['accessRight']['create'] , 0]], getLastVisitedPage());
        }
  
        $quo = filterUserInput($_POST['quotation']);
        $company = filterUserInput($_POST['company']);
        $poType = filterUserInput($_POST['po_type']);
        
        if(isEmpty([$quo,$company,$poType])){
            redirectWithMessage([["Mohon isi dengan benar", 0]], getLastVisitedPage());
        }

        $builder = App::get('builder');

        $quoData = $builder->custom("SELECT b.* FROM `form_quo` as a inner join form_po as b on a.quo=b.id WHERE a.id=$quo", "Document");

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;

        foreach($this->placeholderPoForm[0] as $k => $v){
            if(checkRequirement($v, $k, $quoData[0]->$k)){
                $data[$k]=filterUserInput($quoData[0]->$k);
            }else{
                $passingRequirement=false;
            }  
        }

        $data['created_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data['po_or_quo'] = 1;

        $companyData = $builder->getSpecificData("companies", ['*'], ["id"=>$company], '', 'Partner');
        $userData = $builder->getSpecificData("users", ["*"], ["id"=>$data['created_by']], '', 'User');

        if($poType==1){
            $poNumber = filterUserInput($_POST['po_number']);
        }else{
            // For numbering format purpose
            $thisYear = date('Y');
            $thisMonth = convertToRoman(date('m'));
            $countDataInThisYear = $builder->custom("select count(*) as total_data from form_po where date_format(doc_date, '%Y') in ($thisYear) and po_or_quo=1", "Document");
            
            $numbering=$countDataInThisYear[0]->total_data;
            $numbering=  str_pad($numbering+1, 3, '0', STR_PAD_LEFT);
            $userCode = strtoupper($userData[0]->code);
            $companyCode = strtoupper($companyData[0]->code);
            $poNumber = $numbering."/PO/".$companyCode."/SNC-".$userCode."/".$thisMonth."/".date('Y');
            // End of numbering format
        }

        //insert to db:form_po as po
        
        $insertToFormPo = $builder->insert('form_po', $data);

        if(!$insertToFormPo){
            recordLog('PO form', returnMessage()['poForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }

        $idPoForm = $builder->getPdo()->lastInsertId();

        //insert to db:po_quo

        $insertToQuoPo = $builder->insert("po_quo", ['po'=>$idPoForm, 'po_number'=>$poNumber, 'quo'=>$quo]);
        
        if(!$insertToQuoPo){
            recordLog('PO form', returnMessage()['poForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('PO form', returnMessage()['poForm']['createSuccess'] );
        }

        $insertToDocumentData = $builder->insert("document_data", ['document'=>'5', 'document_number'=>$idPoForm]);
        if(!$insertToDocumentData){
            recordLog('PO form', returnMessage()['poForm']['createFail'] );
            redirectWithMessage(['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0],getLastVisitedPage());
            exit();
        }

        $builder->save();
        
        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['poForm']['createSuccess'] ,1]],getLastVisitedPage());

    }

    //Get the PO number while creating DO
    public function poFormNumber(){
        if(!$this->role->can("view-data-po")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['poForm']['accessRight']['view'] , 0]], getLastVisitedPage());
            }
        }

        $builder = App::get('builder');

        $company = filterUserInput($_GET['company']);

        //$doType=1 -> DO in
        //$doType=0 -> DO out
        $doType = filterUserInput($_GET['do_type']);

        if($doType==1){
            $whereClause="a.supplier=$company";
        }elseif($doType==0){
            $whereClause="a.buyer=$company";
        }

        if(isset($_GET['do_type'])&&!empty($_GET['do_type'])&&$_GET['do_type']!=''){     
            if($doType==1){
                $whereClause="b.id not in (select po_quo from form_do) and a.supplier=$company";
            }elseif($doType==0){
                $whereClause="b.id not in (select po_quo from form_do) and a.buyer=$company";
            }
        }

        //Only get PO number that not yet processed to DO
        $poNumber = $builder->custom("SELECT a.id, b.id as po_quo, b.po_number 
        FROM `form_po` as a 
        INNER JOIN po_quo as b on a.id=b.po 
        WHERE $whereClause
        ORDER BY b.id" , "Document");
        
        echo json_encode($poNumber);

    }

//=====================================================================================================//

    /* DELIVERY ORDER */
    public function doFormIndex(){
        if(!$this->role->can('view-data-do')){
            redirectWithMessage([[ returnMessage()['doForm']['accessRight']['view'] , 0]], getLastVisitedPage());
        }
        
        $builder = App::get('builder');
        
        $products=$builder->getAllData('products', 'Product');

        $partners=$builder->getAllData('companies', 'Partner');

        $approvalPerson = $builder->custom("SELECT a.user_id, b.name FROM role_user as a inner join users as b on a.user_id=b.id WHERE a.role_id=2", "Document");

        $parameterData=[];
        $parameters = $builder->getAllData('default_parameter', 'Internal');
        for($i=0; $i<count($parameters); $i++){
            $parameterData[$parameters[$i]->parameter]=$parameters[$i]->value;
        }

        //Searching for specific category
        //category: buyer, supplier, po_date, product, is there any quo
        
        $whereClause='';

        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            $search['buyer']=filterUserInput($_GET['buyer']);
            $search['supplier']=filterUserInput($_GET['supplier']);
            $search['product']=filterUserInput($_GET['product']);

            $searchByDateStart=filterUserInput($_GET['do_date_start']);
            $searchByDateEnd=filterUserInput($_GET['do_date_end']);
    
            $operator='&&';

            foreach($search as $k => $v){
                if(!empty($search[$k])){
                    $whereClause.=$k."=".$v.$operator;
                }
            }

            if(!empty($searchByDateStart) && !empty($searchByDateEnd)){
                $whereClause.=" a.created_at between '$searchByDateStart' and '$searchByDateEnd'";
            }elseif(!empty($searchByDateStart)){
                $whereClause.=" a.created_at like '%$searchByDateStart%'";
            }elseif(!empty($searchByDateEnd)){
                $whereClause.=" a.created_at like '%$searchByDateEnd%'";
            }
            //dd($whereClause);
            $whereClause=trim($whereClause, '&&');
    
        }

        if($whereClause==''){
            $whereClause=1;
        }

        //End of searching

        $doData = $builder->custom("SELECT a.id, d.po, d.quo, d.po_number, 
        DATE_FORMAT(a.do_date, '%d %M %Y') as do_date, 
        a.do_number, 
        a.delivered_by, a.received_by, 
        b.name as created_by, 
        c.name as updated_by,
        f.name as supplier,
        g.name as buyer,
        e.supplier as sid,
        e.buyer as bid,
        GROUP_CONCAT(DISTINCT(i.name) ORDER by i.id asc SEPARATOR '<br>') as product,
        h.quantity
        FROM form_do as a 
        INNER JOIN users as b on a.created_by=b.id
        INNER JOIN users as c on a.updated_by=c.id
        INNER JOIN po_quo as d on a.po_quo=d.id
        INNER JOIN form_po as e on d.po=e.id
        INNER JOIN companies as f on e.supplier=f.id
        INNER JOIN companies as g on e.buyer=g.id
        INNER JOIN po_product as h on d.id=h.doc
        INNER JOIN products as i on h.product=i.id 
        WHERE $whereClause
        GROUP BY a.id
        UNION
        SELECT a.id, d.po, d.quo, d.po_number, 
        DATE_FORMAT(a.do_date, '%d %M %Y') as do_date, 
        a.do_number, 
        a.delivered_by, a.received_by, 
        b.name as created_by, 
        c.name as updated_by,
        f.name as supplier,
        g.name as buyer,
        j.supplier as sid,
        j.buyer as bid,
        GROUP_CONCAT(DISTINCT(i.name) ORDER by i.id asc SEPARATOR '<br>') as product,
        h.quantity
        FROM form_do as a 
        INNER JOIN users as b on a.created_by=b.id
        INNER JOIN users as c on a.updated_by=c.id
        INNER JOIN po_quo as d on a.po_quo=d.id
        INNER JOIN form_quo as e on d.quo=e.id
        INNER JOIN form_po as j on e.quo=j.id 
        INNER JOIN companies as f on j.supplier=f.id
        INNER JOIN companies as g on j.buyer=g.id
        INNER JOIN quo_product as h on e.id=h.quo
        INNER JOIN products as i on h.product=i.id 
        WHERE $whereClause
        GROUP BY a.id
        ORDER BY id 
        DESC", "Document");

        //download all the data
        if(isset($_GET['download']) && $_GET['download']==true){
            
            $dataColumn = ['do_date', 'do_number', 'po_number', 'supplier', 'buyer', 'product', 'quantity'];

            $this->download(toDownload($doData, $dataColumn));

        }

        //Pagination
        //only show data for specified page
        if(isset($_GET['p'])){
            $p=$_GET['p'];

            if(!is_numeric($p)){
                redirectWithMessage([["Halaman yang anda tuju tidak diketahui",0]],getLastVisitedPage());
            }
            
        }else{
            $p=1;
        }
        
        $limitStart=$p*maxDataInAPage()-maxDataInAPage();

        $pages=ceil(count($doData)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($doData);
        
        $doData=array_slice($doData,$limitStart,maxDataInAPage());

        setSearchPage();
        
        view('form/do_form', compact('doData', 'partners', 'approvalPerson', 'products', 'pages', 'sumOfAllData', 'parameterData'));
        
    }

    public function doFormCreate(){
        if(!$this->role->can('create-data-do')){
            redirectWithMessage([[ returnMessage()['doForm']['accessRight']['create'] , 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderDoForm as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }


        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        
        $builder = App::get('builder');

        $data['created_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        $companyData = $builder->custom("SELECT d.code as code 
        FROM form_do as a
        INNER JOIN po_quo as b on a.po_quo=b.id
        INNER JOIN form_po as c on b.po=c.id
        INNER JOIN companies as d on c.buyer=d.id 
        WHERE a.po_quo=$data[po_quo]", "Document");

        $userData = $builder->getSpecificData("users", ["*"], ["id"=>$data['created_by']], '', 'User');

        //do_type=1 -->do in --> adding the item into stock 
        //do_type=0 -->do out --> reduce the item of the stock
        if($_POST['do_type']==1){
        
            if(!isset($_POST['do_number']) || empty($_POST['do_number'])){
                redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            }
            
            $data['do_number'] = filterUserInput($_POST['do_number']);
        
        }elseif($_POST['do_type']==0){

            // For numbering format purpose
            $thisYear = date('Y');
            $thisMonth = convertToRoman(date('m'));
            $countDataInThisYear = $builder->custom("select count(*) as total_data from form_do where date_format(do_date, '%Y') in ($thisYear)", "Document");
            
            $numbering=$countDataInThisYear[0]->total_data;
            $numbering=  str_pad($numbering+1, 3, '0', STR_PAD_LEFT);
            $userCode = strtoupper($userData[0]->code);
            $companyCode = strtoupper($companyData[0]->code);
            $data['do_number'] = $numbering."/DO/".$companyCode."/SNC-".$userCode."/".$thisMonth."/".date('Y');
            // End of numbering format

        }else{

            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        
        }

        $insertToFormDo = $builder->insert("form_do", $data);

        $idFormDo = $builder->getPdo()->lastInsertId();

        if(!$insertToFormDo){
            recordLog('DO form', returnMessage()['doForm']['createFail'] );
            redirectWithMessage([[ returnMessage()['databaseOperationFailed'], 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('DO form', returnMessage()['doForm']['createSuccess'] );
        }

        $insertToDocumentData = $builder->insert("document_data", ['document'=>'6', 'document_number'=>$idFormDo]);
        if(!$insertToDocumentData){
            recordLog('DO form', returnMessage()['doForm']['createFail'] );
            redirectWithMessage([[ returnMessage()['databaseOperationFailed'], 0]],getLastVisitedPage());
            exit();
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['doForm']['createSuccess'] ,1]],getLastVisitedPage());
    }

    public function doFormDetail(){
        if(!$this->role->can('view-data-do')){
            redirectWithMessage([[ returnMessage()['doForm']['accessRight']['view'] , 0]], getLastVisitedPage());
        }

        $id = filterUserInput($_GET['do']);

        $builder = App::get('builder');

        $vendors = $builder->getAllData("vendors", "Product");

        $parameterData=[];
        $parameters = $builder->getAllData('default_parameter', 'Internal');
        for($i=0; $i<count($parameters); $i++){
            $parameterData[$parameters[$i]->parameter]=$parameters[$i]->value;
        }

        $uploadFiles=$builder->getAllData('upload_files', 'Document');

        $attachments=$builder->custom("SELECT b.id, 
        c.upload_file,
        c.title, 
        date_format(b.created_at, '%d %M %Y') as created_at, 
        b.description
        FROM document_data as a RIGHT JOIN document_attachments as b on a.id=b.document_data 
        INNER JOIN upload_files as c on b.attachment=c.id
        WHERE a.document=6 and a.document_number=$id","Document");

        $doData = $builder->custom("SELECT a.id, d.id as poid, c.id as ddata, b.po, b.quo, b.po_number, 
        date_format(a.do_date, '%d %M %Y') as do_date, a.do_date as dd, a.do_number, a.remark, 
        a.delivered_by, a.received_by, a.approved,
        g.name as created_by, h.name as updated_by, i.name as approved_by, a.approved_at,
        e.name as supplier, e.address as saddress, e.phone as sphone, e.fax as sfax, d.pic_supplier, 
        f.name as buyer, f.address as baddress, f.phone as bphone, f.fax as bfax, d.pic_buyer,
        a.created_by as cbid,
        a.updated_by as ubid,
        a.approved_by as abid,
        case d.supplier when $parameterData[company] then '2' else '1' end  as do_type
        FROM `form_do` as a 
        INNER JOIN po_quo as b on b.id=a.po_quo 
        INNER JOIN document_data as c on a.id=c.document_number
        INNER JOIN form_po as d on b.po=d.id
        INNER JOIN companies as e on d.supplier=e.id
        INNER JOIN companies as f on d.buyer=f.id
        INNER JOIN users as g on a.created_by=g.id
        INNER JOIN users as h on a.updated_by=h.id
        LEFT JOIN users as i on a.approved_by=i.id
        WHERE a.id=$id and c.document=6
        GROUP BY a.id", "Document");


        //1: DO IN, 2: DO OUT
        $doType = $doData[0]->do_type;
        if($doType==2){
            //statusStock: 1=>in, 0=>out
            $whereClause = "a.status=2";
        }else{
            $whereClause = 1;
        }

        $receivedItems = $builder->custom("SELECT b.name as product, a.quantity as qty, 
        DATE_FORMAT(a.received_at, '%d %M %Y') as received_at,DATE_FORMAT(a.send_at, '%d %M %Y') as send_at
        FROM `stocks` as a 
        INNER JOIN products as b on a.product=b.id 
        INNER JOIN stock_relation as c on a.stock_relation=c.id
        WHERE c.document=6 and c.spec_doc=$id && $whereClause 
        GROUP BY a.product","Document");

        //Get the PO product for processed to DO item
        $doItems = $builder->custom("SELECT h.product, i.name, h.quantity
        FROM form_do as a 
        INNER JOIN po_quo as d on a.po_quo=d.id
        INNER JOIN form_po as e on d.po=e.id
        INNER JOIN po_product as h on d.id=h.doc
        INNER JOIN products as i on h.product=i.id 
        WHERE a.id=$id
        GROUP BY i.id
        UNION
        SELECT h.product, i.name, h.quantity
        FROM form_do as a 
        INNER JOIN po_quo as d on a.po_quo=d.id
        INNER JOIN form_quo as e on d.quo=e.id
        INNER JOIN form_po as j on e.quo=j.id 
        INNER JOIN quo_product as h on e.id=h.quo
        INNER JOIN products as i on h.product=i.id 
        WHERE a.id=$id
        GROUP BY i.id", "Document");

        if(count($doData)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], '/form/quo');
        }


        view('form/do_form_detail', compact('doData', 'attachments', 'uploadFiles', 'doItems', 'vendors', 'receivedItems'));
    }

    public function doFormApproval(){
        if(!$this->role->can('approval-data-do')){
            redirectWithMessage([[ returnMessage()['doForm']['accessRight']['approval'] , 0]], getLastVisitedPage());
        }

        $idFormDo = filterUserInput($_POST['do-form']);
        $approval = filterUserInput($_POST['approval']);
        $approvalAt = null;

        //If the do form is approved, $approval will be 1
        //if the do form is reject, $approval will be 0
        if($approval!=1 && $approval!=0){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'] , 0]], getLastVisitedPage());
        }

        if($approval==1){
            $approvalAt = date("Y-m-d h:i:s", time());
        }

        $updatedBy = substr($_SESSION['sim-id'], 3, -3);

        $builder = App::get('builder');

        $updateApproval = $builder->update("form_do", ['approved' => $approval, 'approved_at' => $approvalAt, 'updated_by' => $updatedBy], ["id"=>$idFormDo], "", "Document");

        if(!$updateApproval){
            recordLog('DO form', returnMessage()['doForm']['updateFail'] );
            redirectWithMessage([[ returnMessage()['doForm']['updateFail'] , 0]], getLastVisitedPage());
        }else{
            recordLog('DO form', returnMessage()['doForm']['updateSuccess'] );
        }

        $builder->save();
        
        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['doForm']['updateSuccess'] ,1]],getLastVisitedPage());

    }

    public function doFormUpdate(){
        if(!$this->role->can('update-data-do')){
            redirectWithMessage([[ returnMessage()['doForm']['accessRight']['update'] , 0]], getLastVisitedPage());
        }

        $id = filterUserInput($_POST['do_form']);

        $placeholderDOFormUpdate = [
            'do_date' => 'required',
            'do_number' => 'required',
            'received_by' => 'required',
            'delivered_by' => 'required',
            'remark' => ''
        ];
        
        $passingRequirement=true;

        $data = [];

        foreach($placeholderDOFormUpdate as $placeholder => $requirement){
            if(checkRequirement($requirement, $placeholder, $_POST[$placeholder])){
                $data[$placeholder] = filterUserInput($_POST[$placeholder]);
            }else{
                $passingRequirement=false;
            }
        }

        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }

        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);
        
        //update data 

        $builder = App::get('builder');

        $updateDoForm = $builder->update('form_do', $data, ['id' => $id], '', 'Document');

        if(!$updateDoForm){
            recordLog('DO form', returnMessage()['doForm']['updateFail'] );
            redirectWithMessage([[ returnMessage()['databaseOperationFailed'], 0]],getLastVisitedPage());
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['doForm']['updateSuccess'] ,1]],getLastVisitedPage());

    }

    public function doFormRemove(){
        if(!$this->role->can('update-data-do')){
            redirectWithMessage([[ returnMessage()['doForm']['accessRight']['update'] , 0]], getLastVisitedPage());
        }

        //remove stock_relation will remove stock also
        //then remove form_do

        $do = filterUserInput($_POST['do']);

        $builder = App::get('builder');

        $deleteStockRelated = $builder->delete("stock_relation", ['spec_doc' => $do, 'document' => 6], '&&', 'Document');

        $deletePOForm = $builder->delete("form_do", ['id' => $do], '', 'Document');

        if(!$deletePOForm || !$deleteStockRelated){
            redirectWithMessage([[ returnMessage()['doForm']['deleteFail'] , 0]], getLastVisitedPage());
        }

        recordLog('DO form', returnMessage()['doForm']['deleteSuccess'] );

        $builder->save();

        redirectWithMessage([[returnMessage()['doForm']['deleteSuccess'], 1]], '/form/do');

    }

//=====================================================================================================//

    /* ATTACHMENT */
    public function showDocumentAttachment(){
        if(!$this->role->can("view-attachment")){
            //https://paulund.co.uk/use-php-to-detect-an-ajax-request//
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage(["Anda tidak memiliki hak untuk melihat lampiran", 0], getLastVisitedPage());
            }
        }

        $documentNumber=filterUserInput($_GET['document_number']);
        $documentType=filterUserInput($_GET['document_type']);
        
        $builder=App::get('builder');

        $attachment=$builder->custom("SELECT b.id, c.upload_file, IFNULL(c.title, '') as title, c.created_at, b.description
        FROM document_data as a 
        RIGHT JOIN document_attachments as b on a.id=b.document_data 
        INNER JOIN upload_files as c on b.attachment=c.id
        WHERE a.document=$documentType and a.document_number=$documentNumber","Document");

        echo json_encode($attachment);
    }

    public function createDocumentAttachment(){
        if(!$this->role->can("create-attachment")){
            redirectWithMessage([["Anda tidak memiliki hak untuk menambahkan lampiran", 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderAttachmentForm as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }
        $data['created_by'] = substr($_SESSION['sim-id'], 3, -3);

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([['Mohon isi form sesuai requirement', 0]], getLastVisitedPage());
        }
        
        $builder = App::get('builder');

        if(isset($_FILES["attachment"]) && !empty($_FILES["attachment"]) && $_FILES["attachment"]!='' && $_FILES["attachment"]['size']!=0){
           
            $processingUpload = new UploadController();

            $uploadResult = $processingUpload->processingUpload($_FILES["attachment"]);

            if($uploadResult){
                $lastUploadedId=$processingUpload->getLastUploadedId();

                $data['attachment']=$lastUploadedId;
            }else{
                //$_SESSION['sim-messages']=[['Maaf, gagal upload signature', 0]];
                redirectWithMessage([["Maaf, gagal upload signature", 0]],'/home');
            }
            unset($processingUpload);

        }

        $insertAttachment = $builder->insert('document_attachments', $data);

        if(!$insertAttachment){
            recordLog('Document attachment', "Pendaftaran lampiran dokumen gagal");
            redirectWithMessage([["Pendaftaran lampiran dokumen gagal",0]],getLastVisitedPage());
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([["Pendaftaran lampiran dokumen berhasil",1]],getLastVisitedPage());

    }

    public function showDropDownAttachment(){
        if(!$this->role->can("view-attachment")){
            //https://paulund.co.uk/use-php-to-detect-an-ajax-request//
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage(["Anda tidak memiliki hak untuk melihat lampiran", 0], getLastVisitedPage());
            }
        }

        $builder=App::get('builder');

        $uploadFile = filterUserInput($_GET['upload_file']);

        $attachment= $builder->getSpecificData('upload_files', ['*'], ['id'=>$uploadFile], '', 'Document');

        echo json_encode($attachment);

    }
    /* END OF ATTACHMENT */

//=====================================================================================================//

    /* NOTES */

    public function documentNotesCreate(){
    
        if(!$this->role->can("create-notes")){
            redirectWithMessage([["Anda tidak memiliki hak untuk membuat notes", 0]], getLastVisitedPage());
        }

        //checking form requirement

        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];
        
        foreach($this->placeholderNotesForm as $k => $v){
            
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=$_POST[$k];
            }else{
                $passingRequirement=false;
            }  

        }

        if(!$passingRequirement){
            redirect(getLastVisitedPage());
            exit();
        }

        $documentType=filterUserInput($data['document_type']);
        $documentNumber=filterUserInput($data['document_number']);
        $notes=filterUserInput($data['notes']);

        //dd($data);

        $builder=App::get('builder');

        $idOfDocumentData = $builder->getSpecificData("document_data", ["id"], ["document"=>$documentType, "document_number"=>$documentNumber], "&&", "Document");

        $parameters=[
            'document_data'=>$idOfDocumentData[0]->id,
            'notes'=>$notes,
            'created_by'=>substr(substr($_SESSION['sim-id'],3), 0, -3),
        ];

        //dd($parameters);

        $insertNotes=$builder->insert('document_notes', $parameters);

        if(!$insertNotes){

            redirectWithMessage([['Memberikan notes gagal', 0]] , getLastVisitedPage());

        }

        recordLog('Insert document notes', 'Mendaftarkan notes $notes berhasil');

        $builder->save();

        redirectWithMessage([['Memberikan notes berhasil', 1]] , getLastVisitedPage());

    }

    public function showDocumentNotes(){

        if(!$this->role->can("view-notes")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage(["Anda tidak memiliki hak untuk melihat notes", 0], getLastVisitedPage());
            }
        }

        $documentNumber=filterUserInput($_GET['document_number']);
        $documentType=filterUserInput($_GET['document_type']);
        
        $builder=App::get('builder');

        $notes=$builder->custom("SELECT a.notes, b.name as created_by, date_format(a.created_at, '%d %M %Y %H:%i') as created_at 
        FROM document_notes as a 
        INNER JOIN users as b on a.created_by=b.id 
        INNER JOIN document_data as c on a.document_data=c.id
        WHERE c.document_number=$documentNumber and c.document=$documentType","Document");

        echo json_encode($notes);

    }

//=====================================================================================================//

    /* DOWNLOAD */
    public function download($formData){
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=FORM_DATA.xls");
        
        $column = array_keys($formData[0]);
        
        echo "<table><thead><tr>";

        for($i=0; $i<count($column); $i++){
            echo "<th>".str_replace("_", " ", makeFirstLetterUpper($column[$i]))."</th>";
        }

        echo "</tr></thead><tbody>";

        for($i=0; $i<count($formData); $i++){
            echo "<tr>";
            foreach($formData[$i] as $key => $value){
                echo "<td>".makeFirstLetterUpper($value)."</td>";
            }
            echo "</tr>";
        }

        echo "</tbody></table>";

        exit();
    }
    
}

?>