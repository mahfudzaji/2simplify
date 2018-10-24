<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\App;

class PAController{

    private $role, $userId;

    private $placeholderProduct=[
        
        'name'=>'required', 
        'description'=>'required',
        'part_number'=>'required',
        'link'=>'',
        'category'=>'required'

    ];

    private $placeholderCategory=[
        'name'=>'required', 
        'description'=>'required'
    ];
    
    private $placeholderVendor=[
        'name'=>'required',
        'description'=>'',
        'link'=>''
    ];

    private $placeholderAsset=[
        'product' => 'required',
        'serial_number' => 'required',
        'service_point' => 'required',
        'asset_condition' => 'required',
        'created_by' => 'required',
        'created_at' => 'required',
        'updated_by' => 'required',
        'updated_at' => 'required',
        'own_status' => 'required',
        'status' => 'required',
        'notes' => ''
    ];

    public function __construct(){
        $user=Auth::user();

        $this->userId=Auth::user()[0]->id;

        $this->role = App::get('role');
        
        $this->role -> getRole($this->userId);
        
        if(!$this->role->can("view-product")){
            redirectWithMessage([["anda tidak memiliki hak untuk melihat daftar product", 0]] ,'/');
        }
    }

    public function index(){
        /*
        tampilannnya
        add category/add product
        thumbnail category-->bisa view/diupdate/aktif/non-aktifkan category
        ketika di klik akan diarahkan ke halaman yang berisi daftar product yang termasuk dalam category yang sama
        */

        $builder=App::get("builder");

        //$vendors = $builder->getAllData("vendors", "Product");
        
        $products=$builder->getAllData('products', 'Product');
        
        //show page based user's role
        $roleOfUser = $this->role->getRole($this->userId); 

        //Searching for specific category
        //category: buyer, supplier, po_date, product
        
        $whereClause='';
        
        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            //$search['vendor']=filterUserInput($_GET['vendor']);
            $search['category']=filterUserInput($_GET['category']);
            $search['a.id']=filterUserInput($_GET['product']);

            $operator='&&';

            foreach($search as $k => $v){
                if(!empty($search[$k])){
                    $whereClause.=$k."=".$v.$operator;
                }
            }

            $whereClause=trim($whereClause, '&&');
            //dd($whereClause);
        }

        if($whereClause==''){
            $whereClause=1;
        }

        //End of searching

        $productCat=$builder->getAllData("product_categories", "Product");
        
        $paData=$builder->custom("SELECT DISTINCT(b.id) as catid, 
        b.name as category, 
        b.description, 
        GROUP_CONCAT(a.id SEPARATOR '<br>') as pid, 
        GROUP_CONCAT(a.name SEPARATOR '<br>') as products, 
        GROUP_CONCAT(a.description SEPARATOR '<br>') as pdesc,
        GROUP_CONCAT(a.part_number SEPARATOR '<br>') as part_number,  
        GROUP_CONCAT(case when a.link IS NULL then '-' else a.link end SEPARATOR '<br>') as plink
        FROM products as a 
        INNER JOIN product_categories as b on a.category=b.id 
        WHERE $whereClause 
        GROUP BY b.id", "Document");


        $pd=[];
        for($i=0; $i<count($paData); $i++){
            $pid = explode('<br>', $paData[$i]->pid);
            $p = explode('<br>', $paData[$i]->products);
            $pl = explode('<br>', $paData[$i]->plink);
            $pdesc = explode('<br>', $paData[$i]->pdesc);
            $pn = explode('<br>', $paData[$i]->part_number);

            for($j=0;$j<count($p);$j++){
                array_push($pd, ['id' => $pid[$j],'prod'=>$p[$j], 'link'=>$pl[$j], 'desc'=>$pdesc[$j], 'part_number'=>$pn[$j]]);
            }

            $paData[$i]->products=$pd;
            unset($paData[$i]->plink);
            unset($paData[$i]->pdesc);
            unset($paData[$i]->part_number);
            $pd=[];
        }
        //dd($paData);
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

        $pages=ceil(count($paData)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($paData);
        
        $paData=array_slice($paData,$limitStart,maxDataInAPage());   
        
        view('product/index',compact('productCat', 'sumOfAllData', 'pages', 'roleOfUser', 'vendors', 'products', 'paData'));

    }

    public function category(){
    
        if(empty($_GET['c']) || !isset($_GET['c'])){
            redirectWithMessage([['Kategori tidak diketahui',0]], '/p-a');
        }

        $productCat=filterUserInput($_GET['c']);

        $builder=App::get('builder');
        $parameter=[
            'name',
            'active'
        ];

        $category=$builder->getSpecificData('product_categories',['name','description'], ['id'=>$productCat], '', 'Product');

        if(count($category)>0){
            $products=$builder->getSpecificData('products',$parameter, ['category'=>$productCat], '', 'Product');
            view('product/product',compact('products','category'));
        }else{
            redirectWithMessage([['Maaf, kategori produk yang anda maksud tidak ada',0]], '/p-a'); 
        }      

    }

    public function createProduct(){

        //checking access right
        if(!$this->role->can("create-product")){
            redirectWithMessage([["anda tidak memiliki hak membuat data product", 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];


        foreach($this->placeholderProduct as $k => $v){ 
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }
        

        $data['created_by']=substr($_SESSION['sim-id'], 3, -3);
        $data['updated_by']=substr($_SESSION['sim-id'], 3, -3);

        
        //here is processing upload file then get the result
        if(isset($_FILES["picture"]) && !empty($_FILES["picture"]) && $_FILES["picture"]!='' && $_FILES["picture"]['size']!=0){
            
            $processingUpload = new UploadController();

            //Only accept img
            $uploadResult = $processingUpload->processingUpload($_FILES["picture"], 1);

            if($uploadResult){
                $lastUploadedId=$processingUpload->getLastUploadedId();

                $data['picture']=$lastUploadedId;
            }else{
                //$_SESSION['sim-messages']=[['Maaf, gagal upload picture', 0]];
                redirectWithMessage($_SESSION['sim-messages'], getLastVisitedPage());
            }
            unset($processingUpload);
  
        }

        if(!$passingRequirement){
            redirectWithMessage([[ returnMessage()['formNotPassingRequirements'], 0]],getLastVisitedPage());
        }

        $builder=App::get('builder');

        $insertToProduct = $builder->insert('products', $data);

        if(!$insertToProduct){
            recordLog('Insert product', 'Mendaftarkan product gagal');
            redirectWithMessage([['Maaf, mendaftarkan produk gagal', 0]] , getLastVisitedPage());

        }else{
            recordLog('Insert product', 'Mendaftarkan product berhasil');
        }

        $builder->save();

        redirectWithMessage([['Mendaftarkan produk berhasil', 1]] , getLastVisitedPage());

    }

    public function createCategory(){
        
        //checking access right
        if(!$this->role->can("create-product")){
            redirectWithMessage([["anda tidak memiliki membuat data kategori", 0]],getLastVisitedPage());
        }

        //checking form requirement

        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];
        
        foreach($this->placeholderCategory as $k => $v){
            
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  

        }

        if(!$passingRequirement){
            redirect('/product');
            exit();
        }

        
        $data['created_by']=substr(substr($_SESSION['sim-id'],3), 0, -3);
        $data['updated_by']=substr(substr($_SESSION['sim-id'],3), 0, -3);

        $builder=App::get('builder');

        $insertCategory=$builder->insert('product_categories', $data);

        if($insertCategory){

            recordLog('Insert Category', 'Mendaftarkan Category berhasil');

            $builder->save();

            redirectWithMessage([['Mendaftarkan Category berhasil', 1]] , getLastVisitedPage());

        }else{

            redirectWithMessage([['Maaf, mendaftarkan Category gagal', 1]] , getLastVisitedPage());

        }

    }

    public function createVendor(){
        if(!$this->role->can("create-product")){
            redirectWithMessage([["Anda tidak memiliki hak membuat data product", 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderVendor as $k => $v){ 
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        $data['created_by']=substr(substr($_SESSION['sim-id'],3), 0, -3);
        $data['updated_by']=$data['created_by'];

        if(!$passingRequirement){
            redirect('/p-a');
            exit();
        }

        $builder = App::get('builder');

        $insertToVendors = $builder->insert('vendors', $data);

        if(!$insertToVendors){
            redirectWithMessage([['Maaf, anda gagal mendaftarkan vendor. Hubungi administrator', 0]], getLastVisitedPage());
        }else{
            recordLog('Insert product', 'Mendaftarkan product berhasil');
        }

        $builder->save();

        redirectWithMessage([['Mendaftarkan vendor berhasil', 1]], getLastVisitedPage());

    }

    public function detailAsset(){

        //checking access right
        if(!$this->role->can("view-asset")){
            redirectWithMessage([["anda tidak memiliki melihat data asset", 0]],'p-a');
        }

        if(isset($_GET['product'])){
            if(isEmpty($_GET['product'])){
                return "Data tidak ada";
            }

            $product=filterUserInput($_GET['product']);
            $document_number=filterUserInput($_GET['document_number']);

            $builder=App::get('builder');

            /*$documentData=$builder->custom("SELECT b.id, b.serial_number,b.asset_condition as id_ac, case b.asset_condition when 0 then 'rusak' else 'baik' end as asset_condition, case b.own_status when 0 then 'pihak luar' else 'milik sendiri' end as own_status, case b.status when 0 then 'out' else 'in' end as status,b.service_point as id_sp, c.name as service_point 
            FROM `document_data` as a 
            inner join assets as b on a.asset=b.id 
            inner join service_points as c on b.service_point=c.id 
            where a.document_number=$assets", "Asset");*/

            $asset=$builder->custom("SELECT b.id, b.serial_number,b.stock_condition as id_ac, case b.stock_condition when 0 then 'rusak' else 'baik' end as asset_condition,  case b.status when 0 then 'out' else 'in' end as status,b.service_point as id_sp, c.name as service_point 
            FROM `document_data` as a 
            inner join stocks as b on a.asset=b.id 
            inner join service_points as c on b.service_point=c.id 
            inner join products as d on b.product=d.id where d.id=$product and a.document_number=$document_number", "Asset");

            echo  json_encode($asset);
            
        }
    }

    public function updateAsset(){

        //checking access right
        if(!$this->role->can("update-asset")){
            redirectWithMessage([["anda tidak memiliki hak untuk memperbaharui data asset", 0]],'p-a');
        }

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        $isIdExist=array_key_exists('id', $_POST);

        if(!$isIdExist){
            $passingRequirement=false;
        }

        foreach($_POST as $k => $v){

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
        
        //if not passing the requirements
        if(!$passingRequirement){
            redirectWithMessage([['Mohon untuk mengisi kolom secara lengkap',0]], getLastVisitedPage());
        }

        $builder= App::get('builder');

        $updateAsset = $builder->update('Assets', $data, ['id'=>$data['id']], '', 'Asset');

        if(!$updateAsset ){
            recordLog('Asset', "Pembaharuan data asset $data[id] gagal");
            redirectWithMessage([['Pembaharuan data asset gagal, mohon hubungi administrator',0]], getLastVisitedPage());

        }else{
            recordLog('Asset', "Pembaharuan data asset $data[id] berhasil");
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([["Pendaftaran data asset berhasil",1]],getLastVisitedPage());
    }

    public function removeAsset(){
        
        if(!$this->role->can("remove-asset")){
            redirectWithMessage([["anda tidak memiliki hak untuk menghapus asset item", 0]] ,getLastVisitedPage());
        }
        
        $asset=filterUserInput($_POST['asset']);

        $builder=App::get('builder');

        $removeAsset=$builder->delete('Stocks', ['id' => $asset], '', 'Document');

        if($removeAsset){
            recordLog('Stock', 'Menghapus stock item berhasil');
            $builder->save();
            redirectWithMessage([['Menghapus stock item berhasil', 1]] ,getLastVisitedPage());
        }else{
            redirect(getLastVisitedPage());
        }

    }

    public function vendor(){
    
        $productCat=filterUserInput($_GET['category']);
        
        $builder=App::get('builder');

        $vendor=$builder->custom("", "Document");

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode($vendor);
            exit();
        }
    }

    public function updateCategory(){
        //checking access right
        if(!$this->role->can("update-asset")){
            redirectWithMessage([["Anda tidak memiliki hak untuk memperbaharui data product", 0]],'/product');
        }

        //category id
        $id = filterUserInput($_POST['cid']);
        $data['updated_by']=substr($_SESSION['sim-id'], 3, -3);

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];


        foreach($this->placeholderCategory as $k => $v){ 
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }

        if(!$passingRequirement){
            redirect(getLastVisitedPage());
            exit();
        }

        $builder = App::get('builder');

        //dd($id);

        $updateToProduct = $builder->update("product_categories", $data, ['id' => $id], "", "Product");

        if(!$updateToProduct){
            recordLog('Insert product', 'Memperbaharui category gagal');
            redirectWithMessage([['Maaf, Memperbaharui category gagal', 0]] , getLastVisitedPage());

        }

        recordLog('Insert product', 'Memperbaharui category berhasil');

        $builder->save();

        redirectWithMessage([['Memperbaharui category berhasil', 1]] , getLastVisitedPage());
    }

    public function updateProduct(){
        //checking access right
        if(!$this->role->can("update-asset")){
            redirectWithMessage([["Anda tidak memiliki hak untuk memperbaharui data product", 0]],'/product');
        }

        //checking form requirement
        $data=[];
        
        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];


        foreach($this->placeholderProduct as $k => $v){ 
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }  
        }
        
        $id = filterUserInput($_POST['pid']);
        $data['updated_by']=substr($_SESSION['sim-id'], 3, -3);

        

        //here is processing upload file then get the result
        if(isset($_FILES["picture"])){
            $processingUpload = new UploadController();

            $uploadResult = $processingUpload->processingUpload($_FILES["picture"]);

            $lastUploadedId=$processingUpload->getLastUploadedId();

            $data['picture']=$lastUploadedId;
        }

        if(!$passingRequirement){
            redirect(getLastVisitedPage());
            exit();
        }

        $builder = App::get('builder');

        //dd($id);

        $updateToProduct = $builder->update("products", $data, ['id' => $id], "", "Product");

        if(!$updateToProduct){
            recordLog('Insert product', 'Memperbaharui product gagal');
            redirectWithMessage([['Maaf, Memperbaharui produk gagal', 0]] , getLastVisitedPage());

        }else{
            recordLog('Insert product', 'Memperbaharui product berhasil');
        }

        $builder->save();

        redirectWithMessage([['Memperbaharui produk berhasil', 1]] , getLastVisitedPage());
    }

}