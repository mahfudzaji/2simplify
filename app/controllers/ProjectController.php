<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;

class ProjectController{

    private $role,$userId,$roleOfUser;

    private $placeholderProjectForm = array(
        "name" => "required",
        "description" => "",
        "start_date" => "required",
        "end_date" => "required",
        "pic" => "required"
    );

    private $placeholderRequestItemProjectForm = array(
        [
            "project" => "required",
            "requested_by" => "required",
            "request_date" => "required",
            "remark" => ""
        ],
        [
            "product" => "required",
            "quantity" => "required"
        ]
    );

    
    public function __CONSTRUCT(){
        $user=Auth::user();
        $this->userId=Auth::user()[0]->id;
        $this->role = App::get('role'); 
        $this->roleOfUser = $this->role->getRole($this->userId);

    }

    public function index(){

        $builder = App::get('builder');

        $users = $builder->getAllData('users', 'User');
        $companies = $builder->getAllData('companies', 'Partner');


        //Searching for specific category

        $whereClause='';

        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            $search['requisite']=filterUserInput($_GET['requisite']);
            $search['submitter']=filterUserInput($_GET['submitter']);

            $searchByDateStart=filterUserInput($_GET['start_date']);
            $searchByDateEnd=filterUserInput($_GET['end_date']);
  
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


        $projectData = $builder->custom("SELECT a.id, a.name, a.description, 
        DATE_FORMAT(a.start_date, '%d %M %Y') as start_date, DATE_FORMAT(a.end_date, '%d %M %Y') as end_date,
        b.name as pic, 
        case project_status when 1 then 'Belum dimulai' when 2 then 'Sedang dikerjakan' when 3 then 'Selesai' end as project_status,
        c.name as created_by,
        d.name as updated_by,
        f.name as customer,
        g.id as ddata
        FROM projects as a
        INNER JOIN users as b on a.pic=b.id
        INNER JOIN users as c on a.created_by=c.id
        INNER JOIN users as d on a.updated_by=d.id
        INNER JOIN form_po as e on a.po=e.id
        INNER JOIN companies as f on e.buyer=f.id
        INNER JOIN document_data as g on a.id=g.document_number
        WHERE $whereClause and g.document=10", "Project");

        //download all the data
        if(isset($_GET['download']) && $_GET['download']==true){
            
            $dataColumn = ['name', 'start_date', 'end_date', 'pic', 'project_status'];

            $this->download(toDownload($projectData, $dataColumn));

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

        $pages=ceil(count($projectData)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($projectData);
        
        $projectData=array_slice($projectData,$limitStart,maxDataInAPage());

        setSearchPage();

        view('/project/index', compact('projectData', 'sumOfAllData', 'pages', 'users', 'companies'));
    }

    public function projectCreate(){
        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

        $builder = App::get('builder');

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        
        foreach($this->placeholderRequestItemProjectForm as $k => $v){
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

        $insertToProjects = $builder->insert("projects", $data);

        if(!$insertToProjects){
            recordLog('Project', returnMessage()['project']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator.', 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('Project', returnMessage()['project']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['project']['createSuccess'] ,1]],getLastVisitedPage());

    }

    public function projectDetail(){
        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

        $builder = App::get('builder');

        $id = filterUserInput($_GET['pr']);

        $uploadFiles = $builder->getSpecificData('upload_files', ['*'], ['public'=>1], '', 'Document');
        $products = $builder->getAllData('products', 'Product');
        $users = $builder->getAllData('users', 'User');

        $projectDetailData = $builder->custom("SELECT a.id, a.name, a.description, 
        DATE_FORMAT(a.start_date, '%d %M %Y') as start_date, DATE_FORMAT(a.end_date, '%d %M %Y') as end_date,
        a.start_date as sd, a.end_date as ed,
        b.name as pic, 
        a.pic as picid,
        case project_status when 1 then 'Belum dimulai' when 2 then 'Sedang dikerjakan' when 3 then 'Selesai' end as project_status,
        a.project_status as psid,
        c.name as created_by,
        d.name as updated_by,
        f.name as customer,
        g.po_number as po_number,
        h.id as ddata,
        DATE_FORMAT(a.created_at, '%d %M %Y') as created_at, 
        DATE_FORMAT(a.updated_at, '%d %M %Y') as updated_at
        FROM projects as a
        INNER JOIN users as b on a.pic=b.id
        INNER JOIN users as c on a.created_by=c.id
        INNER JOIN users as d on a.updated_by=d.id
        INNER JOIN form_po as e on a.po=e.id
        INNER JOIN companies as f on e.buyer=f.id
        INNER JOIN po_quo as g on e.id=g.po
        INNER JOIN document_data as h on a.id=h.document_number
        WHERE a.id=$id and h.document=10", "Project");

        if(count($projectDetailData)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], getLastVisitedPage());
        }

        /* $receivedItems = $builder->custom("SELECT b.name as product, a.quantity,
        DATE_FORMAT(a.send_at, '%d %M %Y') as requested_at, DATE_FORMAT(a.received_at, '%d %M %Y') as returned_at, 
        a.remark, case a.status when 0 then 'out' when 1 then 'in' end as item_status, a.status as sid,
        FROM receipt_stock as a 
        INNER JOIN products as b on a.product=b.id 
        INNER JOIN stock_relation as c on a.stock_relation=c.id
        INNER JOIN users as d on 
        WHERE c.do_or_receipt_in=2 and c.doc_in=$id or c.do_or_receipt_out=2 and c.doc_out=$id
        GROUP BY a.product","Document"); */

        $projectItemRequested = $builder->custom("SELECT DATE_FORMAT(a.request_date, '%d %M %Y') as request_date,
        a.request_number, a.remark,  
        GROUP_CONCAT(d.name SEPARATOR '<br>') as product,
        GROUP_CONCAT(case when d.part_number IS NULL then '-' else d.part_number end SEPARATOR '<br>') as part_number,
        e.name as requested_by,
        GROUP_CONCAT(case c.status when 1 then 'IN' else 'OUT' end SEPARATOR '<br>') as status,
        
        (SELECT GROUP_CONCAT(c.quantity SEPARATOR '<br>') as qty FROM project_item_request as a 
        INNER JOIN project_stock_relation as b on a.id=b.item_request
        INNER JOIN receipt_stock as c on b.stock=c.id
        INNER JOIN products as d on c.product=d.id
        INNER JOIN users as e on a.requested_by=e.id
        WHERE a.project=$id and c.status=1 GROUP BY a.id) as quantity_in,
        
        (SELECT GROUP_CONCAT(c.quantity SEPARATOR '<br>') as qty FROM project_item_request as a 
        INNER JOIN project_stock_relation as b on a.id=b.item_request
        INNER JOIN receipt_stock as c on b.stock=c.id
        INNER JOIN products as d on c.product=d.id
        INNER JOIN users as e on a.requested_by=e.id
        WHERE a.project=$id and c.status=2 GROUP BY a.id)  as quantity_out

		FROM project_item_request as a
        INNER JOIN project_stock_relation as b on a.id=b.item_request
        INNER JOIN receipt_stock as c on b.stock=c.id
        INNER JOIN products as d on c.product=d.id
        INNER JOIN users as e on a.requested_by=e.id
        WHERE a.project=$id
        GROUP BY a.id", "Project");

        //dd($projectItemRequested);

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode(["projectDetailData"=>$projectDetailData]);
            exit();
        }else{
            view('/project/detail', compact('projectDetailData', 'uploadFiles', 'products', 'users', 'projectItemRequested'));
        }
    }

    public function projectNewRequest(){
        if(!array_key_exists('superadmin', $this->roleOfUser)){
            redirectWithMessage([["Anda tidak memiliki hak untuk memasuki menu ini", 0]], getLastVisitedPage());
        }

        $builder = App::get('builder');

        $parameterData=[];
        $parameters = $builder->getAllData('default_parameter', 'Internal');
        for($i=0; $i<count($parameters); $i++){
            $parameterData[$parameters[$i]->parameter]=$parameters[$i]->value;
        }

        $companyData = $builder->getSpecificData("companies", ['*'], ["id"=>$parameterData['company']], '', 'Partner');

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        for($i=0; $i<count($this->placeholderRequestItemProjectForm);$i++){
            foreach($this->placeholderRequestItemProjectForm[$i] as $k => $v){
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
        }

        // For numbering format purpose
        $thisYear = date('Y');
        $thisMonth = convertToRoman(date('m'));
        $countDataInThisYear = $builder->custom("select count(*) as total_data from project_item_request where date_format(request_date, '%Y') in ($thisYear)", "Document");
        
        $numbering=$countDataInThisYear[0]->total_data;
        $numbering=  str_pad($numbering+1, 3, '0', STR_PAD_LEFT);
        $companyCode = strtoupper($companyData[0]->code);
        $data[0]['request_number'] = "BPB/".$numbering."-".$companyCode."/".$thisMonth."-".date('Y');
        // End of numbering format

        $insertToProjectItemRequest = $builder->insert("project_item_request", $data[0]);

        if(!$insertToProjectItemRequest){
            recordLog('Project', returnMessage()['project']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator1.', 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('Project', returnMessage()['project']['createSuccess'] );
        }

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

        $newDataRecap=[];
        for($i=0; $i<$value; $i++){
            $newData=[];
            foreach($dataKeys as $key){
                $newData[$key]=$data[1][$key][$i];
            }
            $newData['created_by'] = substr($_SESSION['sim-id'], 3, -3);
            $newData['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

            //insert into receipt_stock with status 'out' because request item is always issuing stock
            $newData['status'] = 2;
            array_push($newDataRecap, $newData);
        }

        $isSuccessInsertToStock=true;
        for($i=0; $i<count($newDataRecap); $i++){
            //insert into receipt_stock with status 'out' because request item is always issuing stock
            $insertToStock = $builder->insert("receipt_stock", $newDataRecap[$i]);
            
            if(!$insertToStock){
                $isSuccessInsertToStock=false;
            }
        }

        if(!$isSuccessInsertToStock){
            recordLog('Request project item', returnMessage()['receiptForm']['createFail'] );
            redirectWithMessage([['Maaf, terjadi kesalahan, mohon ulangi lagi atau hubungi administrator2.', 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('Request project item', returnMessage()['receiptForm']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['project']['createSuccess'] ,1]],getLastVisitedPage());

    }
}

?>