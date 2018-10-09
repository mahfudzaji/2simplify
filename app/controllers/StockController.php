<?php 

namespace App\Controllers;

use App\Core\Auth;
use App\Core\App;

class StockController{

    private $role;
    private $placeholderStockForm = array(
        "service_point" => "required",
        "ownership" => "required",
        "product" => "required",
        "received_at" => "required",
        "serial_number" => "required",
        "stock_condition" => "required",
        "notes" => ""
    );

    private $placeholderStock = array(
        [
            "product" => "required",
            "serial_number" => "required",
        ],
        [
            "do_or_receipt" => "required",
            "doc" => "required",
        ]
    );

    public function __construct(){
        $user=Auth::user();

        $userId=$user[0]->id;

        $this->role = App::get('role');
        
        $this->role -> getRole($userId);
        
    }

    public function index(){
        if(!$this->role->can("view-stock")){
            redirectWithMessage([[ returnMessage()['stock']['accessRight']['view'] , 0]], getLastVisitedPage());
        }
        
        $builder = App::get('builder');

        $servicePoints=$builder->getAllData('service_points', 'Internal');

        $category = $builder->getAllData('product_categories', 'Product');

        $products=$builder->getAllData('products', 'Product');

        $vendors = $builder->getAllData("vendors", "Product");

        //Searching for specific category
        //category: status, ownership, delivered_date, product, is it receive form or do?
        
        $whereClause='';
        
        if(isset($_GET['search']) && $_GET['search']==true){

            $search=array();

            $search['d.id']=filterUserInput($_GET['category']);
    
            $operator='&&';

            foreach($search as $k => $v){
                if($search[$k]!=""){
                    $whereClause.=$k."=".$v.$operator;
                }
            }

            $whereClause=trim($whereClause, '&&');
    
        }

        if($whereClause==''){
            $whereClause=1;
        }


        //Based on product category
        $stocksData = $builder->custom("SELECT d.id as cid, d.name as category, d.description,
        IFNULL((select count(*) from stocks inner join products on stocks.product=products.id where products.category=d.id and status=1),0) as stock_in, 
        IFNULL((select sum(quantity) as quantity from project_item inner join products on project_item.product=products.id where products.category=d.id and status=1),0) as qty_pro_in,
        IFNULL((select sum(quantity) as quantity from receipt_stock inner join products on receipt_stock.product=products.id where products.category=d.id and status=1),0) as qty_receipt_in,
        IFNULL((select count(*) from stocks inner join products on stocks.product=products.id where products.category=d.id and status=2),0) as stock_out, 
        IFNULL((select sum(quantity) as quantity from project_item inner join products on project_item.product=products.id where products.category=d.id and status=2),0) as qty_pro_out,
        IFNULL((select sum(quantity) as quantity from receipt_stock inner join products on receipt_stock.product=products.id where products.category=d.id and status=2),0) as qty_receipt_out
        FROM `stocks` as a 
        INNER JOIN products as b on a.product=b.id 
        INNER JOIN product_categories as d on b.category=d.id
        WHERE $whereClause
        GROUP BY b.category
        ORDER BY d.name asc", 'Stock');

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

        $pages=ceil(count($stocksData)/maxDataInAPage());
    
        //End of pagination

        //======================//

        $sumOfAllData=count($stocksData);
        
        $stocksData=array_slice($stocksData,$limitStart,maxDataInAPage());

        view('stock/index', compact('stocksData', 'partners', 'approvalPerson', 'products', 'vendors', 'pages', 'servicePoints', 'sumOfAllData', 'category'));
    
    }

    public function getProduct(){
        if(!$this->role->can("view-stock")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['stock']['accessRight']['view'] , 0]], getLastVisitedPage());
            }
        }

        $vendor = filterUserInput($_GET['vendor']);

        $builder = App::get('builder');

        $getProduct = $builder->getSpecificData('products', ['*'], ['id'=>$vendor], '', 'Product');

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode($getProduct);
        }
    }

    public function getProductDetail(){
        if(!$this->role->can("view-stock")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['stock']['accessRight']['view'] , 0]], getLastVisitedPage());
            }
        }

        $product = filterUserInput($_GET['product']);

        //echo $product;

        $builder = App::get('builder');

        $getProductDetail = $builder->custom("SELECT ifnull(a.part_number, '-') as part_number, c.upload_file, a.name, a.description, a.link 
        FROM products as a 
        INNER JOIN upload_files as c on a.picture=c.id
        WHERE a.id = $product", "Product");

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode($getProductDetail);
        }
    }

    public function getStockList(){
        if(!$this->role->can("view-stock")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['stock']['accessRight']['view'] , 0]], getLastVisitedPage());
            }
        }
        
        $builder = App::get('builder');

        $product = filterUserInput($_GET['product']);

        $stocksData = $builder->custom("SELECT a.id, a.serial_number, b.name as product, 
        e.name as service_point, 
        case a.stock_condition when 0 then 'Rusak' when 1 then 'Baik' end as stock_condition,
        date_format(a.received_at, '%d %M %Y') as received_at,
        date_format(a.send_at, '%d %M %Y') as send_at,
        case a.status when 2 then 'out' when 1 then 'in' end as status,
        a.status as ids,
        c.name as created_by, d.name as updated_by,
        f.do_or_receipt_in,
        f.doc_in,
        a.service_point as idsp,
        a.received_at as ra,
        a.send_at as sa
        FROM `stocks` as a 
        INNER JOIN products as b on a.product=b.id 
        INNER JOIN stock_relation as f on a.stock_relation=f.id
        INNER JOIN users as c on a.created_by=c.id 
        INNER JOIN users as d on a.updated_by=d.id
        INNER JOIN service_points as e on a.service_point=e.id
        WHERE a.product = $product
        ORDER BY a.id desc", "Document");

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode($stocksData);
        }

    }

    public function stockAdd(){
        if(!$this->role->can("create-stock")){
            redirectWithMessage([[ returnMessage()['stock']['accessRight']['create'] , 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];
        $passingRequirement = true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderStockForm as $k => $v){
            if(checkRequirement($v, $k, $_POST[$k])){
                $data[$k]=filterUserInput($_POST[$k]);
            }else{
                $passingRequirement=false;
            }
        }

        //if not the passing requirements
        if(!$passingRequirement){
            //array_shift($_SESSION['sim-messages'], [returnMessage()['formNotPassingRequirements'], 0]);
            redirectWithMessage( $_SESSION['sim-messages'] ,getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }

        $data['created_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        $serialNumber = $data['serial_number'];

        $builder = App::get('builder');
        
        $flag = true;
        
        for($i=0; $i<count($serialNumber); $i++){
            $data['serial_number']=$serialNumber[$i];
            
            $insertToStock = $builder->insert("stocks", $data);
            
            if(!$insertToStock){
                $flag = false;
            }
        }

        if(!$flag){
            recordLog('Stock', returnMessage()['stock']['createFail'] );
            redirectWithMessage([[ returnMessage()['databaseOperationFailed'], 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('Stock', returnMessage()['stock']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['stock']['createSuccess'] ,1]],getLastVisitedPage());
    }

    public function stockReduce(){

    }

    public function stockProcessAction(){

    }

    public function checkStock(){
        $builder = App::get('builder');

        $product = filterUserInput($_GET['product']);
        $status = filterUserInput($_GET['status']);

        $stockAvailable = $builder->custom("SELECT a.product as pid, 
        (select count(*) from stocks where product=a.product and status=1) as stock_in, 
        (select sum(quantity) as quantity from project_item where status=1 and product=a.id) as qty_pro_in,
        (select sum(quantity) as quantity from receipt_stock where status=1 and product=a.id) as qty_receipt_in
        FROM `stocks` as a 
        INNER JOIN products as b on a.product=b.id 
        WHERE a.product=$product and a.status=$status
        GROUP BY a.product", 'Stock');

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode($stockAvailable);
        }else{
            return $stockAvailable;
        }
    }

    public function stockUpdate(){
        if(!$this->role->can("update-stock")){
            redirectWithMessage([[ returnMessage()['stock']['accessRight']['update'] , 0]], getLastVisitedPage());
        }
        
        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

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

        
    }

    public function stockCreateFromForm(){
        //updating stock data base on form; there are 2 forms that handle related to stock : receive form and delivery order 
        if(!$this->role->can("update-stock")){
            redirectWithMessage([[ returnMessage()['stock']['accessRight']['update'] , 0]], getLastVisitedPage());
        }

        //dd($_POST);

        //checking form requirement
        $passingRequirement = true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderStockCreateFromForm as $k => $v){
            if(!checkRequirement($v, $k, $_POST[$k])){
                $passingRequirement=false;
            }
        }

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage( $_SESSION['sim-messages'] ,getLastVisitedPage());
            //redirect(getLastVisitedPage());
        }
        
        $docType = filterUserInput($_POST['do_or_receipt']);
        $docId = filterUserInput($_POST['doc']);
        $updatedBy = substr($_SESSION['sim-id'], 3, -3);
        $doType = filterUserInput($_POST['do_type']);
        $product = filterUserInput($_POST['product']);
        $serialNumber = filterUserInput($_POST['serial_number']);

        //do in:1, do out:2
        if($docType==1){
            $status = 1;
        }elseif($docType==2){
            $status = 0;
        }

        
        $builder = App::get('builder');
        $flag = true;

        for($i=0; $i<count($serialNumber); $i++){
            $updateStock = $builder->update("stocks", ['do_or_receipt' => $docType, 'doc' => $docId, 'updated_by' => $updatedBy, 'status' => $status], ['product' => $product, 'serial_number' => $serialNumber[$i]], "&&", "Asset");            
            
            if(!$updateStock){
                $flag = false;
            }
        }
        
        if(!$flag){
            recordLog('Stock', returnMessage()['stock']['updateFail'] );
            redirectWithMessage([[ returnMessage()['databaseOperationFailed'], 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('Stock', returnMessage()['stock']['updateSuccess'] );
        }

        $builder->save();

        //array_push($_SESSION['sim-messages'], [ returnMessage()['stock']['updateSuccess'] ,1]);

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['stock']['updateSuccess'] ,1]], getLastVisitedPage());
    }

    public function stockSerialNumber(){

        if(!$this->role->can("view-stock")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['stock']['accessRight']['view'] , 0]], getLastVisitedPage());
            }
        }

        $product = filterUserInput($_GET['product']);

        $builder = App::get('builder');

        $getSerialNumber = $builder->getSpecificData("stocks", ['id','serial_number'], ['product'=>$product, 'stock_condition'=>1, 'status'=>1], '&&', 'Asset');

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode($getSerialNumber);
        }

    }

    //Tested
    public function stockIn(){
        if(!$this->role->can("create-stock")){
            redirectWithMessage([[ returnMessage()['stock']['accessRight']['create'] , 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];
        $passingRequirement = true;
        $_SESSION['sim-messages']=[];

        $this->placeholderStock[0]['received_at']= "required";

        for($i=0; $i<count($this->placeholderStock); $i++){
            foreach($this->placeholderStock[$i] as $k => $v){
                if(checkRequirement($v, $k, $_POST[$k])){
                    $data[$i][$k]=filterUserInput($_POST[$k]);
                }else{
                    $passingRequirement=false;
                }
            }
        }

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage( $_SESSION['sim-messages'] ,getLastVisitedPage());
        }

        $data[0]['created_by'] = substr($_SESSION['sim-id'], 3, -3);
        $data[0]['updated_by'] = substr($_SESSION['sim-id'], 3, -3);

        $serialNumber = $data[0]['serial_number'];

        $builder = App::get('builder');

        $flag = true;
        
        for($i=0; $i<count($serialNumber); $i++){
            $data[0]['serial_number'] = $serialNumber[$i];
            
            //check whether serial number and product already registered in db or not
            $getSpecificStock = $builder->getSpecificData("stocks", ['*'], ['product' => $data[0]['product'], 'serial_number' => $serialNumber[$i]], "&&", "Document");
            if(count($getSpecificStock)>0){
                redirectWithMessage([[ "Serial number telah terdapat pada daftar stock", 0]],getLastVisitedPage());
                exit();
            }

            //insert to stock relation
            $insertToStockRelation = $builder->insert("stock_relation", ['do_or_receipt_in' => $data[1]['do_or_receipt'], 'doc_in' => $data[1]['doc']]);
            
            $data[0]['stock_relation'] = $builder->getPdo()->lastInsertId();

            $insertToStock = $builder->insert("stocks", $data[0]);
            
            if(!$insertToStock){
                $flag = false;
            }
        }

        if(!$flag){
            recordLog('Stock', returnMessage()['stock']['createFail'] );
            redirectWithMessage([[ returnMessage()['databaseOperationFailed'], 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('Stock', returnMessage()['stock']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['stock']['createSuccess'] ,1]],getLastVisitedPage());
    }

    public function stockOut(){
        if(!$this->role->can("update-stock")){
            redirectWithMessage([[ returnMessage()['stock']['accessRight']['update'] , 0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];
        $passingRequirement = true;
        $_SESSION['sim-messages']=[];

        $this->placeholderStock[0]['send_at']= "required";

        for($i=0; $i<count($this->placeholderStock); $i++){
            foreach($this->placeholderStock[$i] as $k => $v){
                if(checkRequirement($v, $k, $_POST[$k])){
                    $data[$i][$k]=filterUserInput($_POST[$k]);
                }else{
                    $passingRequirement=false;
                }
            }
        }
 
        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage( $_SESSION['sim-messages'] ,getLastVisitedPage());
        }

        $data[0]['updated_by'] = substr($_SESSION['sim-id'], 3, -3);
        //Status=> 1:in, 2:out
        $data[0]['status'] = 2;

        $serialNumber = $data[0]['serial_number'];

        $builder = App::get('builder');

        $flag = true;
        
        for($i=0; $i<count($serialNumber); $i++){
            //$data['serial_number'] = $serialNumber[$i];
            
            //select stock that have specific sn and product name
            $getSpecificStock = $builder->getSpecificData("stocks", ['stock_relation'], ['product' => $data[0]['product'], 'serial_number' => $serialNumber[$i]], "&&", "Document");
            
            //get the stock relation
            $stockRelation = $getSpecificStock[0]->stock_relation;
            
            //update data stock_relation that have specific id
            $updateStockRelation = $builder->update("stock_relation", ['do_or_receipt_out' => $data[1]['do_or_receipt'], 'doc_out' => $data[1]['doc']], ['id'=>$stockRelation], '', 'Document');
            if(!$updateStockRelation){
                recordLog('Stock', returnMessage()['stock']['createFail'] );
                redirectWithMessage([[ returnMessage()['databaseOperationFailed'], 0]],getLastVisitedPage());
                exit();
            }else{
                recordLog('Stock', returnMessage()['stock']['createSuccess'] );
            }

            //update stock data
            $updateStock = $builder->update("stocks", $data[0], ['product' => $data[0]['product'], 'serial_number' => $serialNumber[$i]], '&&', 'Document');
            
            if(!$updateStock){
                $flag = false;
            }
        }

        if(!$flag){
            recordLog('Stock', returnMessage()['stock']['createFail'] );
            redirectWithMessage([[ returnMessage()['databaseOperationFailed'], 0]],getLastVisitedPage());
            exit();
        }else{
            recordLog('Stock', returnMessage()['stock']['createSuccess'] );
        }

        $builder->save();

        //redirect to form page with message
        redirectWithMessage([[ returnMessage()['stock']['createSuccess'] ,1]],getLastVisitedPage());

    }

}

?>