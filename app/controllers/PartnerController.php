<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\App;

class PartnerController{

    private $role;
    private $placeholder=[
            'name'=>'required', 
            'code'=>'required',
            'bussiness_entity'=>'required', 
            'province'=>'required', 
            'address'=>'required',
            'phone'=>'', 
            'email'=>'email', 
            'relationship'=>'required', 
            'remark'=>''
        ];

    public function __construct(){
        $user=Auth::user();

        $userId=Auth::user()[0]->id;

        $this->role = App::get('role');
        
        $this->role -> getRole($userId);
        
        if(!$this->role->can("view-partner")){
            redirectWithMessage([["anda tidak memiliki hak untuk melihat daftar partner", 0]],'/');
        }
    }

    public function index(){
 
        $builder=App::get('builder');

        $products=$builder->getAllData('products', 'Product');
        $partners=$builder->getAllData('companies', 'Partner');
        $provinces=$builder->getAllData('provinces', 'Partner');

        $whereClause='';
        
        //Searching for specific category
        //category: companies, products
        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            $search['a.id']=filterUserInput($_GET['name']);
            $search['d.id']=filterUserInput($_GET['product']);
    
            $operator='&&';

            foreach($search as $k => $v){
                if(!empty($search[$k])){
                    $whereClause.=$k."=".$v.$operator;
                }
            }

            $whereClause=trim($whereClause, '&&');
    
        }

        //End of searching

        if($whereClause==''){
            $whereClause=1;
        }

        //dd($whereClause);

        $partnerData = $builder->custom("SELECT a.id, a.name, 
        IFNULL(GROUP_CONCAT(d.name separator '<br>'), '-') as product,
        f.bussiness_entity,
        a.address, a.phone, a.email,
        g.province,
        h.relationship,
        case a.active when 0 then 'Activate' when 1 then 'Deactivate' end as active
        FROM `companies` as a 
        left join form_po as b on a.id=b.buyer
        left join po_quo as e on b.id=e.po 
        left join po_product as c on c.doc=e.id 
        left join products as d on d.id=c.product
        inner join bussiness_entities as f on a.bussiness_entity=f.id
        inner join provinces as g on a.province=g.id
        inner join relationships as h on a.relationship=h.id
        where $whereClause 
        group by a.id","Document");

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

        $pages=ceil(count($partnerData)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($partnerData);
        
        $partnerData=array_slice($partnerData,$limitStart,maxDataInAPage());

        view('partner/index', compact('partnerData', 'partners', 'products', 'sumOfAllData', 'pages', 'provinces'));
    }

    public function create(){

        //checking access right
        if(!$this->role->can("create-partner")){
            redirectWithMessage([["anda tidak memiliki hak untuk melihat daftar partner", 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];
        
        foreach($this->placeholder as $k => $v){
            
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=$_POST[$k];
            }else{
                $passingRequirement=false;
            }  

        }

        //if not the passing requirements
        if(!$passingRequirement){
            //redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
            redirect(getLastVisitedPage());
        }
        
        //post the data to database
        $builder=App::get('builder');

        $data['created_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        //here is processing upload file then get the result
        if(isset($_FILES["logo"]) && !empty($_FILES["logo"]) && $_FILES["logo"]!='' && $_FILES["logo"]['size']!=0){
            
            $processingUpload = new UploadController();

            //Only accept img
            $uploadResult = $processingUpload->processingUpload($_FILES["logo"], 1);

            if($uploadResult){
                $lastUploadedId=$processingUpload->getLastUploadedId();

                $data['logo']=$lastUploadedId;
            }else{
                //$_SESSION['sim-messages']=[['Maaf, gagal upload logo', 0]];
                redirectWithMessage($_SESSION['sim-messages'], getLastVisitedPage());
            }
            unset($processingUpload);
  
        }

        $insertPartner= $builder->insert('companies', $data);

        if(!$insertPartner){
            recordLog('Partner', "Pendaftaran partner gagal");
            redirectWithMessage([["Pendaftaran partner gagal", 0]], getLastVisitedPage());
        }

        recordLog('Partner', "Pendaftaran partner berhasil");

        $builder->save();

        //redirect to partner page with message
        redirectWithMessage([["Pendaftaran partner berhasil",1]], getLastVisitedPage());
    
    }

    public function update(){

        //checking access right
        if(!$this->role->can("update-partner")){
            redirectWithMessage([["anda tidak memiliki hak untuk memperbaharui data partner", 0]],'partner');
        }

        //checking form requirement

        $id = filterUserInput($_POST['p']);

        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];
        
        foreach($this->placeholder as $k => $v){
            
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  

        }

        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }


        //post the data to database
        $builder=App::get('builder');

        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        //here is processing upload file then get the result
        if(isset($_FILES["logo"]) && !empty($_FILES["logo"]) && $_FILES["logo"]!='' && $_FILES["logo"]['size']!=0){
            
            $processingUpload = new UploadController();

            //Only accept img
            $uploadResult = $processingUpload->processingUpload($_FILES["logo"], 1);

            if($uploadResult){
                $lastUploadedId=$processingUpload->getLastUploadedId();

                $data['logo']=$lastUploadedId;
            }else{
                //$_SESSION['sim-messages']=[['Maaf, gagal upload logo', 0]];
                redirectWithMessage($_SESSION['sim-messages'], getLastVisitedPage());
            }
            unset($processingUpload);
  
        }

        $updatePartner= $builder->update('companies', $data, ['id' => $id], '', 'Partner');

        if(!$updatePartner){
            recordLog('Partner', "Pembaharuan partner gagal");
            redirectWithMessage([["Maaf, Pembaharuan partner gagal", 0]], getLastVisitedPage());
        }

        recordLog('Partner', "Pembaharuan partner berhasil");

        $builder->save();

        //redirect to partner page with message
        redirectWithMessage([["Pembaharuan partner berhasil",1]], getLastVisitedPage());
     
    }

    public function detail(){
        //get the specific partner data where have the following id

        $builder=App::get('builder');

        $id= filterUserInput($_GET['p']);

        $sql="SELECT a.id, 
        a.name, a.address, a.phone, a.email, a.remark, 
        a.code,
        date_format(a.created_at,'%d %M %Y %H:%i') as created_at, 
        date_format(a.updated_at, '%d %M %Y %H:%i') as updated_at, 
        b.bussiness_entity,
        b.id as beid, 
        c.province,
        c.id as pid, 
        d.relationship,
        d.id as rid,
        e.name as created_by, 
        f.name as updated_by,  
        CASE a.active when 0 then 'Aktifkan' else 'Non-aktifkan' end AS status
        FROM companies as a 
        INNER JOIN bussiness_entities as b on a.bussiness_entity=b.id 
        INNER JOIN provinces as c on a.province=c.id 
        INNER JOIN relationships as d on a.relationship=d.id 
        INNER JOIN users as e on a.created_by=e.id 
        INNER JOIN users as f on a.updated_by=f.id 
        WHERE a.id=$id
        LIMIT 10";

        $partner=$builder->custom($sql, 'Partner');

        $entities= $builder->getAllData('bussiness_entities','Partner');

        $provinces= $builder->getAllData('provinces','Partner');

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
        WHERE a.submitted=$id OR a.received=$id
        ORDER BY a.receive_date DESC LIMIT 10",'Document');

        $quoData = $builder->custom("SELECT g.id, g.quo_number, 
        date_format(a.doc_date, '%d %M %Y') as doc_date, 
        c.name as supplier,
        d.name as buyer,
        GROUP_CONCAT(f.name ORDER by f.id asc SEPARATOR '<br>') as product
        FROM `form_po` as a 
        inner join form_quo as g on g.quo=a.id
        inner join companies as c on a.supplier=c.id
        inner join companies as d on a.buyer=d.id
        inner join quo_product as e on g.id=e.quo
        inner join products as f on e.product=f.id
        where po_or_quo=0 && revision is null && buyer=$id || supplier=$id 
        group by a.id
        order by a.id 
        DESC LIMIT 10 ","Document");

        $poData = $builder->custom("SELECT a.id, g.po_number,
        date_format(a.doc_date, '%d %M %Y') as doc_date, 
        c.name as supplier,
        d.name as buyer,
        GROUP_CONCAT(f.name ORDER by f.id asc SEPARATOR '<br>') as product
        FROM `form_po` as a 
        inner join po_quo as g on g.po=a.id
        inner join companies as c on a.supplier=c.id
        inner join companies as d on a.buyer=d.id
        inner join po_product as e on g.id=e.doc
        inner join products as f on e.product=f.id
        LEFT JOIN form_quo as h on g.quo=h.id 
        WHERE po_or_quo=1 && buyer=$id || supplier=$id 
        GROUP BY a.id
        UNION 
        SELECT d.id,
        a.po_number,
        date_format(d.doc_date, '%d %M %Y') as doc_date, 
        e.name as supplier,
        f.name as buyer, 
        GROUP_CONCAT(DISTINCT(g.name) ORDER by g.id asc SEPARATOR '<br>') as product 
        FROM po_quo as a 
        INNER JOIN form_quo as b on a.quo=b.id 
        INNER JOIN form_po as d on a.po=d.id 
        INNER JOIN quo_product as c on a.quo=c.quo
        INNER JOIN companies as e on d.supplier=e.id 
        INNER JOIN companies as f on d.buyer=f.id 
        INNER JOIN products as g on c.product=g.id
        WHERE po_or_quo=1 && buyer=$id || supplier=$id 
        GROUP BY d.id
        ORDER BY id 
        DESC
        LIMIT 10", "Document");

        $doData = $builder->custom("SELECT a.id, d.po, d.quo, d.po_number, 
        DATE_FORMAT(a.do_date, '%d %M %Y') as do_date, 
        a.do_number, 
        a.delivered_by, a.received_by, 
        b.name as created_by, 
        c.name as updated_by,
        f.name as supplier,
        g.name as buyer,
        GROUP_CONCAT(DISTINCT(i.name) ORDER by i.id asc SEPARATOR '<br>') as product
        FROM form_do as a 
        INNER JOIN users as b on a.created_by=b.id
        INNER JOIN users as c on a.updated_by=c.id
        INNER JOIN po_quo as d on a.po_quo=d.id
        INNER JOIN form_po as e on d.po=e.id
        INNER JOIN companies as f on e.supplier=f.id
        INNER JOIN companies as g on e.buyer=g.id
        INNER JOIN po_product as h on d.id=h.doc
        INNER JOIN products as i on h.product=i.id 
        WHERE buyer=$id || supplier=$id 
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
        GROUP_CONCAT(DISTINCT(i.name) ORDER by i.id asc SEPARATOR '<br>') as product
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
        WHERE buyer=$id || supplier=$id 
        GROUP BY a.id
        ORDER BY id 
        DESC
        LIMIT 10", "Document"); 
        
        view('partner/detail', compact('partner','entities','provinces', 'receiveData', 'quoData', 'poData', 'doData'));
    }

    public function toggleStatus(){
        
        //checking access right
        if(!$this->role->can("activate-partner") && !$this->role->can("deactive-partner")){
            echo "anda tidak memiliki hak untuk mengubah status partner";
        }

        /*
        database operation to change the status of the partner
        if status==1 then make it 0 (deactivate)
        if status==0 then make it 1 (active)
        */

        $builder=App::get('builder');
        
        $result=$builder->getSpecificData('companies', ['*'] , ['id'=>$_POST['p']], '', 'Partner');

        $status=$result[0]->active;
        
        $toUpdate=[
            'active'=>!$status,
        ];

        $builder->update('companies', $toUpdate, ['id'=>$_POST['p']], '', 'Partner');

        $builder->save();

        echo "status partner berhasil diubah";
    }
}