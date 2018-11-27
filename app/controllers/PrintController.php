<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\App;

class PrintController{

    private $role;
    private $placeholderPrintForm=[
        'document'=>'required',
        'document_number'=>'required'
    ];

    public function __construct(){
        $user=Auth::user();

        $userId=Auth::user()[0]->id;

        $this->role = App::get('role');
        
        $this->role -> getRole($userId);
        
    }

    public function receiveForm(){
       
        if(!$this->role->can('print-receive-form')){
            redirectWithMessage([[ returnMessage()['receiveForm']['accessRight']['print'] ,0]], getLastVisitedPage());
        }

        //checking form requirement
        $data=[];

        //check the requirement
        //if passing the requirement, put the data into $data array
        //otherwise redirect back to the page

        $passingRequirement=true;
        $_SESSION['sim-messages']=[];

        foreach($this->placeholderPrintForm as $k => $v){
            if(checkRequirement($v, $k, $_GET[$k])){
                $data[$k]=filterUserInput($_GET[$k]);
            }else{
                $passingRequirement=false;
            }  
        }
        $data['created_by'] = substr($_SESSION['sim-id'], 3, -3);

        //if not the passing requirements
        if(!$passingRequirement){
            redirectWithMessage([['Maaf, terjadi kesalahan. Mohon coba lagi.', 0]], getLastVisitedPage());
        }

        $builder=App::get('builder');

        $receiveData=$builder->custom("SELECT a.id, ifnull(a.remark,'-') as remark, 
        date_format(a.created_at,'%d %M %Y %H:%i') as created_at,
        date_format(a.updated_at,'%d %M %Y %H:%i') as updated_at,
        date_format(a.receive_date,'%d %M %Y %H:%i') as receive_date,
        c.name as requisite, 
        d.name as submitted, 
        e.name as received, 
        case a.status when 0 then 'open' else 'closed' end as status, 
        j.name as created_by,
        k.name as updated_by
        FROM `form_receive` as a 
        inner join requisite as c on a.requisite=c.id 
        inner join companies as d on a.submitted=d.id 
        inner join companies as e on a.received=e.id  
        inner join document_data as f on f.document_number=a.id 
        inner join stocks as g on f.asset=g.id 
        inner join service_points as h on g.service_point=h.id 
        inner join products as i on g.product=i.id
        inner join users as j on a.created_by=j.id
        inner join users as k on a.updated_by=k.id
        where f.document=$data[document] and f.document_number=$data[document_number] group by f.document_number order by a.receive_date",'Document');
        
        $assets=$builder->custom("SELECT c.name, a.serial_number, case stock_condition when 0 then 'rusak' else 'baik' end as asset_condition FROM `stocks` as a 
        inner join document_data as b on a.id=b.asset inner join products as c on a.product=c.id WHERE b.document=$data[document] and b.document_number=$data[document_number]", 'Asset');

        if(count($receiveData)<1){
            redirectWithMessage([['Data tidak tersedia atau asset terdaftar pada tanda terima tidak ada/telah dihapus',0]], getLastVisitedPage());
        }

        //print that data
        printData('receive_form', compact('receiveData','assets'));
    }

    public function arForm(){
        if(!$this->role->can('print-activity-report')){
            redirectWithMessage([[ returnMessage()['activityReport']['accessRight']['print'] , 0]],getLastVisitedPage());
        }
        
        $id = filterUserInput($_GET['ar']);

        $builder = App::get('builder');

        $arData = $builder->custom("SELECT a.customer as idcustomer, 
        a.activity_date as acd, 
        a.id, 
        b.name as customer, 
        b.code, 
        date_format(a.activity_date, '%d %M %Y') as activity_date,
        case a.project_name when '' then '-' else a.project_name end as project_name,
        a.remark,   
        a.activity, 
        case a.next_activity when '' then '-' else a.next_activity end as next_activity,  
        case a.active when 1 then 'already solved' else 'Not solved yet' end as status, 
        c.name as created_by, 
        d.name as updated_by, 
        date_format(a.created_at, '%d %M %Y') as created_at, 
        date_format(a.updated_at, '%d %M %Y') as updated_at, 
        e.id as ddata
        FROM `form_ar` as a 
        inner join companies as b on a.customer=b.id 
        inner join users as c on a.created_by=c.id 
        inner join users as d on a.updated_by=d.id
        inner join document_data as e on a.id=e.document_number 
        where a.id=$id and e.document=2", "Document");

        //print that data
        printData('activity_report', compact('arData'));
    }

    public function vacationForm(){
        if(!$this->role->can("print-vacation-form")){
            redirectWithMessage([[ returnMessage()['vacationForm']['accessRight']['print'] , 0]],getLastVisitedPage());
        }

        $id = filterUserInput($_GET['v']);

        $builder = App::get('builder');

        $vacationData = $builder->custom("SELECT f.id as ddata, a.id, a.submitter as smt, b.name as submitter, b.code,
        a.day_used, 
        c.name as requisite,
        a.requisite as rid, 
        case a.approved when 0 then 'Not appoved yet' when 1 then 'Approved' else 'Rejected' end as approved, 
        case a.verified when 0 then 'Not verified yet' when 1 then 'verified' else 'Rejected' end as verified, 
        d.name as verified_by, 
        a.verified_by as vbid,
        e.name as approved_by,
        a.approved_by as abid,
        case a.remark when '' then '-' else a.remark end as remark,
        g.name as department,
        a.day_used,
        date_format(a.created_at, '%d %M %Y') as created_at,
        h.upload_file as submitterSign,
        i.upload_file as verifySign,
        j.upload_file as approveSign,
        case a.verified_at when NULL then '-' else date_format(a.verified_at, '%d %M %Y, %H:%i') end as verified_at,
        case a.approved_at when NULL then '-' else date_format(a.approved_at, '%d %M %Y, %H:%i') end as approved_at
        FROM form_vacation as a 
        inner join users as b on a.submitter=b.id 
        inner join requisite as c on a.requisite=c.id 
        inner join users as d on a.verified_by=d.id 
        inner join users as e on a.approved_by=e.id
        inner join document_data as f on f.document_number=$id
        inner join departments as g on b.department=g.id
        left join upload_files as h on b.signature=h.id
        left join upload_files as i on d.signature=i.id
        left join upload_files as j on e.signature=j.id
        where f.document=4 and a.id=$id", 'Document');

        //dd($vacationData);

        $year = explode(' ', $vacationData[0]->created_at)[2];
        $submitter = $vacationData[0]->smt;

        $vacationDate = $builder->custom("SELECT date_format(a.vacation_date, '%d %M %Y') as vacation_date, a.vacation_date as vd
        FROM vacation_date as a
        inner join form_vacation as b on a.document_number=b.id
        where a.document_number=$id", 'Document');

        $countVacationInThisYear = $builder->getSpecificData("form_vacation", ['sum(day_used) as total'], ['submitter' => $submitter, 'approved' => 1], 'and', 'Document');

        $vacationPerYear = $builder->getSpecificData("vacation_per_year", ['*'], ['year' => $year], '', 'Document');

        //dd($countVacationInThisYear);
        //print that data
        printData('vacation_form', compact('vacationData', 'vacationDate', 'vacationPerYear', 'countVacationInThisYear'));
    }
    
    public function reimburseForm(){
        if(!$this->role->can("print-reimburse-form")){
            redirectWithMessage([[ returnMessage()['reimburseForm']['accessRight']['print'] , 0]],getLastVisitedPage());
        }

        $id = filterUserInput($_GET['r']);
        
        $builder = App::get('builder');

        $reimburseData = $builder->custom("SELECT a.id, a.submitter as name, b.name as submitter, b.code,  
        date_format(a.verified_at, '%d %M %Y') as verified_at, 
        date_format(a.approved_at, '%d %M %Y') as approved_at, 
        date_format(a.created_at, '%d %M %Y') as created_at, 
        date_format(a.updated_at, '%d %M %Y') as updated_at, 
        case a.paid when 0 then 'Belum ditebus' when 1 then 'Telah ditebus. Mohon konfirmasi' when 2 then 'Pembayaran telah dikonfirmasi' end as paid, 
        a.approved_by as abid,
        c.name as approved_by, 
        d.name as verified_by,
        f.code as docCode,
        g.name as department,
        h.upload_file as submitterSign,
        i.upload_file as verifySign,
        j.upload_file as approveSign
        FROM `form_reimburse` as a 
        inner join users as b on a.submitter=b.id 
        inner join users as c on a.approved_by=c.id 
        inner join users as d on a.verified_by=d.id
        inner join document_data as e on e.document_number=a.id
        inner join documents as f on f.id=e.document
        inner join departments as g on b.department=g.id
        left join upload_files as h on b.signature=h.id
        left join upload_files as i on c.signature=i.id
        left join upload_files as j on d.signature=j.id
        where e.document=3 and a.id= $id", "Document");

        //dd($reimburseData);

        $reimburseDetailData = $builder->custom("SELECT date_format(a.receipt_date, '%d %M %Y') as receipt_date, 
        b.name as requisite, 
        a.cost, 
        a.remark, 
        case a.approved when 0 then 'Not approved yet' when 1 then 'Approved' when 2 then 'Reject' else 'need revision' end as approved, 
        a.approved as aid
        FROM `reimburse_detail` as a 
        inner join requisite as b on a.requisite=b.id 
        WHERE a.document_number=$id", "Document");

        printData('reimburse_form', compact('reimburseData', 'reimburseDetailData'));
    }

    public function quotationForm(){
        if(!$this->role->can("print-quo")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['quoForm']['accessRight']['print'] , 0]],getLastVisitedPage());
            }
        }

        $id= filterUserInput($_GET['quo']);

        $builder = App::get('builder');

        $uploadFiles=$builder->getSpecificData('upload_files', ['*'], ['public'=>1], '', 'Document');

        $products=$builder->getAllData('products', 'Product');

        $ownCompany = $builder->custom("SELECT b.upload_file as logo, a.name, a.address, a.province, a.phone, a.fax, a.email FROM companies as a 
        INNER JOIN upload_files as b on a.logo=b.id WHERE a.relationship=1", "Document");

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
        if(isset($_GET['revision']) && !empty($_GET['revision'])){

            $revisionNumber= filterUserInput($_GET['revision']);

            $quoData = $builder->custom("SELECT k.quo_number as quo_number, DATE_FORMAT(j.doc_date, '%d %M %Y') as quo_date,
            f.name as buyer, f.address as baddress, f.phone as bphone, IFNULL(f.fax, '-')as bfax,
            a.pic_buyer, 
            g.name as supplier, g.address as saddress, g.phone as sphone, IFNULL(g.fax, '-') as sfax,
            a.pic_supplier, 
            i.name as currency, a.ppn, 
            b.name as created_by, DATE_FORMAT(j.created_at, '%d %M %Y') as created_at, 
            c.name as updated_by, DATE_FORMAT(j.updated_at, '%d %M %Y') as updated_at, 
            d.name as acknowledged_by, DATE_FORMAT(a.acknowledged_at, '%d %M %Y') as  acknowledged_at, 
            e.name as approved_by, DATE_FORMAT(a.approved_at, '%d %M %Y') as approved_at,  
            h.id as ddata,
            j.created_by as cbid,
            a.approved_by as abid,
            a.remark,
            l.upload_file as approverSign
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
            inner join upload_files as l on e.signature=l.id
            WHERE h.document=9 and k.id=$id and j.revision_number=$revisionNumber", 'Document');

            $quoDetailData = $builder->custom("SELECT a.id, IFNULL(c.part_number, '-') as part_number, c.name as product, 
            a.product as pid,
            a.quantity, 
            a.price_unit,
            a.item_discount,
            a.quantity*a.price_unit as total,
            a.status  
            FROM `quo_product` as a 
            inner join form_quo as d on a.quo=d.id
            inner join quo_revision as e on d.id=e.form_quo
            inner join quo_revision as f on a.revision=f.id
            inner join form_po as b on b.id=d.quo 
            inner join products as c on a.product=c.id 
            WHERE d.id=$id and f.revision_number=$revisionNumber
            GROUP BY a.id
            ORDER BY a.id", 'Document');

            //dd($quoDetailData);
        }else{
            $quoData = $builder->custom("SELECT k.quo_number as quo_number, DATE_FORMAT(a.doc_date, '%d %M %Y') as quo_date,
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
            a.created_by as cbid,
            a.approved_by as abid,
            a.remark,
            l.upload_file as approverSign
            FROM `form_po` as a 
            INNER JOIN form_quo as k on a.id=k.quo
            INNER JOIN users as b on a.created_by=b.id 
            INNER JOIN users as c on a.updated_by=c.id 
            INNER JOIN users as d on a.acknowledged_by=d.id
            INNER JOIN users as e on a.approved_by=e.id 
            INNER JOIN companies as f on a.buyer=f.id
            INNER JOIN companies as g on a.supplier=g.id
            INNER JOIN document_data as h on h.document_number=k.id
            INNER JOIN currency as i on a.currency=i.id
            LEFT JOIN upload_files as l on e.signature=l.id
            WHERE h.document=9 and k.id=$id", 'Document');


            $quoDetailData = $builder->custom("SELECT a.id, IFNULL(c.part_number, '-') as part_number, c.name as product, 
            a.product as pid,
            a.quantity, 
            a.price_unit,
            a.item_discount,
            a.quantity*a.price_unit as total,
            a.status 
            FROM `quo_product` as a 
            inner join form_quo as d on a.quo=d.id
            inner join form_po as b on b.id=d.quo 
            inner join products as c on a.product=c.id 
            WHERE d.id=$id and a.revision is null
            ORDER BY a.id", 'Document');
        }

        /* End of Quo revision */
        
        if(count($quoData)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], '/form/quo');
        }

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode(["quoData"=>$quoData, "quoDetailData"=>$quoDetailData]);
            exit();
        }else{
            printData('quotation_form',compact('quoData', 'quoDetailData', 'uploadFiles', 'attachments', 'products', 'countDataQuoRevision', 'ownCompany'));
        }   
    }

    public function poForm(){
        if(!$this->role->can("print-po")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['poForm']['accessRight']['print'] , 0]],getLastVisitedPage());
            }
        }

        $id= filterUserInput($_GET['po']);
        
        $builder = App::get('builder');

        $uploadFiles=$builder->getAllData('upload_files', 'Document');

        $products=$builder->getAllData('products', 'Product');

        $ownCompany = $builder->custom("SELECT b.upload_file as logo, a.name, a.address, a.province, a.phone, a.fax, a.email FROM companies as a 
        INNER JOIN upload_files as b on a.logo=b.id WHERE a.relationship=1", "Document");

        $poData = $builder->custom("SELECT j.po_number as po_number, DATE_FORMAT(a.doc_date, '%d %M %Y') as po_date,
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
        l.upload_file as creatorSign,
        m.upload_file as acknowledgeSign,
        n.upload_file as approverSign
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
        left join upload_files as l on b.signature=l.id
        left join upload_files as m on d.signature=m.id
        left join upload_files as n on e.signature=n.id
        WHERE h.document=5 and a.id=$id", 'Document');

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

        if(count($poData)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], getLastVisitedPage());
        }
        
        //dd($poDetailData);
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode(["poData"=>$poData, "poDetailData"=>$poDetailData]);
            exit();
        }else{
            printData('po_form-rev',compact('poData', 'poDetailData', 'uploadFiles', 'products', 'ownCompany'));
        }   
    }

    public function doForm(){
        if(!$this->role->can("print-do")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['doForm']['accessRight']['print'] , 0]],getLastVisitedPage());
            }
        }

        $id = filterUserInput($_GET['do']);
        
        $builder = App::get('builder');

        $ownCompany = $builder->custom("SELECT b.upload_file as logo, a.name, a.address, a.province, a.phone, a.fax, a.email FROM companies as a 
        INNER JOIN upload_files as b on a.logo=b.id WHERE a.relationship=1", "Document");

        $doData = $builder->custom("SELECT a.id, d.id as poid, c.id as ddata, b.po, b.quo, b.po_number, DATE_FORMAT(d.doc_date, '%d %M %Y') as po_date, date_format(a.do_date, '%d %M %Y') as do_date, a.do_number, a.remark, 
        a.delivered_by, a.received_by, a.approved,
        g.name as created_by, h.name as updated_by, i.name as approved_by, a.approved_at,
        e.name as supplier, e.address as saddress, e.phone as sphone, e.fax as sfax, d.pic_supplier, 
        f.name as buyer, f.address as baddress, f.phone as bphone, f.fax as bfax, d.pic_buyer,
        a.created_by as cbid,
        a.updated_by as ubid,
        a.approved_by as abid
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

        /* $receivedItems = $builder->custom("SELECT b.part_number, b.name as product, count(*) as quantity, 
        GROUP_CONCAT(a.serial_number order by a.id asc SEPARATOR '<br>') as serial_number 
        FROM `stocks` as a 
        INNER JOIN products as b on a.product=b.id 
        INNER JOIN stock_relation as c on a.stock_relation=c.id
        WHERE c.do_or_receipt_in=1 and c.doc_in=$id or c.do_or_receipt_out=1 and c.doc_out=$id
        GROUP BY a.product","Document"); */
        $receivedItems = $builder->custom("SELECT b.part_number, b.name as product, a.quantity as quantity, 
        DATE_FORMAT(a.received_at, '%d %M %Y') as received_at
        FROM `stocks` as a 
        INNER JOIN products as b on a.product=b.id 
        INNER JOIN stock_relation as c on a.stock_relation=c.id
        WHERE c.document=6 and c.spec_doc=$id
        GROUP BY a.product","Document");


        if(count($doData)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], getLastVisitedPage());
        }

        //dd($doData);
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode(["doData"=>$doData, "receivedItems"=>$receivedItems]);
            exit();
        }else{
            printData('do_form',compact('doData', 'receivedItems', 'ownCompany'));
        }  
    }

    public function receiptForm(){
        if(!$this->role->can("print-do")){
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                echo '{"access":false}';
                exit();
            }else{
                redirectWithMessage([[ returnMessage()['receiptForm']['accessRight']['print'] , 0]],getLastVisitedPage());
            }
        }

        $id = filterUserInput($_GET['r']);
        
        $builder = App::get('builder');

        $ownCompany = $builder->custom("SELECT b.upload_file as logo, a.name, a.address, a.province, a.phone, a.fax, a.email FROM companies as a 
        LEFT JOIN upload_files as b on a.logo=b.id WHERE a.relationship=1", "Document");

        $receiptData = $builder->custom("SELECT a.id, a.receipt_number,
        date_format(a.receipt_date, '%d %M %Y') as receipt_date, 
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
        a.remark,
        f.id as ddata,
        g.name as currency,
        a.ppn
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

        $receiptItems = $builder->custom("SELECT c.part_number, b.id, b.product as pid, c.name as product, b.quantity, b.price, b.discount
        FROM form_receipt as a 
        INNER JOIN receipt_product as b on b.receipt=a.id
        INNER JOIN products as c on b.product=c.id 
        WHERE a.id=$id", "Document");

        //dd($receiptData);

        if(count($receiptData)<1){
            redirectWithMessage([['Data tidak tersedia atau telah dihapus',0]], getLastVisitedPage());
        }

        //dd($doData);
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            echo json_encode(["receiptData"=>$receiptData, "receivedItems"=>$receivedItems]);
            exit();
        }else{
            printData('receipt_form',compact('receiptData', 'receiptItems', 'ownCompany'));
        }  
    }
}

?>