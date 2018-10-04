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

        foreach($this->placeholderProjectForm as $k => $v){
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

        $uploadFiles=$builder->getSpecificData('upload_files', ['*'], ['public'=>1], '', 'Document');
        $products=$builder->getAllData('products', 'Product');

        /* $projectDetailData = $builder->custom("SELECT a.id, a.name, a.description, 
        DATE_FORMAT(a.start_date, '%d %M %Y') as start_date, DATE_FORMAT(a.end_date, '%d %M %Y') as end_date,
        b.name as pic, 
        case project_status when 1 then 'Belum dimulai' when 2 then 'Sedang dikerjakan' when 3 then 'Selesai' end as project_status,
        a.project_status as psid,
        c.name as created_by,
        d.name as updated_by,
        g.name as buyer
        FROM projects as a
        INNER JOIN users as b on a.pic=b.id
        INNER JOIN users as c on a.created_by=c.id
        INNER JOIN users as d on a.updated_by=d.id
        LEFT JOIN po_project as e on a.id=e.project
        LEFT JOIN form_po as f on e.po=f.id
        LEFT JOIN companies as g on f.buyer=g.id
        WHERE a.id=$id", 'Project'); */

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

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode(["projectDetailData"=>$projectDetailData]);
            exit();
        }else{
            view('/project/detail', compact('projectDetailData', 'uploadFiles', 'products'));
        }
    }

    public function projectNewRequest(){

    }
}

?>