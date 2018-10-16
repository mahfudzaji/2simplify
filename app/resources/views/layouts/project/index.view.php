<?php

$titlePage=returnMessage()['project']['title'];

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>

        <header id="main-header">
            <h1><?= $titlePage; ?></h1>
            <p>Halaman ini menangani data terkait <?= $titlePage; ?></p>
            <button class="btn btn-sm btn-header btn-modal" id="create-project"><span class="glyphicon glyphicon-pencil"></span> Tambahkan project</button>
        </header>

        <div class="sub-header"> 
            <form action="/project" method="GET" style="display:inherit">    
                <input type="hidden" name="search" value="true">
                <div class="search" id="product-based">
                    <div class="form-group">
                        <select name="partner" class="form-control">
                            <option value=''>Customer</option>
                            <?php foreach($companies as $company): ?>
                                <option title="<?= $company->name; ?>" value=<?= $company->id ?>><?= makeItShort($company->name, 50); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="product-based">
                    <div class="form-group">
                        <select name="pic" class="form-control">
                            <option value=''>PIC</option>
                            <?php foreach($users as $user): ?>
                                <option title="<?= $user->name; ?>" value=<?= $user->id ?>><?= makeItShort($user->name, 50); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="date-based" style="position:relative">
                    <button type="button" class="btn btn-default" id="btn-date-based">TANGGAL PROJECT</button>
                    <div class="form-group" style="position: absolute;left: 50%;margin-top: 5px;transform: translateX(-50%);z-index: 5;display: none;width: 400px;">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="search" id="po-based">
                    <div class="form-group">
                        <select name="po" class="form-control">
                            <option value=''>Ada PO?</option>
                            <option value=1>Ada</option>
                            <option value=0>Tidak</option>
                        </select>
                    </div>
                </div>  -->
                <div class="search">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button> 
                </div>     
            </form>
        </div>

        <div class="info">
            <label><span class="glyphicon glyphicon-floppy-saved"></span> Jumlah data: <?= $sumOfAllData; ?></label>
        </div>

        <div class="main-data">
            <?php if(count($projectData)<1): ?>
                <div class="text-center">Belum terdapat data tersimpan</div>
            <?php else: ?>
                <div class="container-fluid">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Customer</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>PIC</th>
                                    <th>Status</th>
                                    <th>Created by</th>
                                    <th>Updated by</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($projectData as $data): ?>
                                    <tr>
                                        <td><a href="/project/detail?pr=<?= $data->id ?>"><strong><?= ucwords($data->name); ?></strong></a></td>
                                        <td><?= $data->customer; ?></td>
                                        <td><?= $data->start_date; ?></td>
                                        <td><?= $data->end_date ?></td>
                                        <td><?= $data->pic; ?></td>
                                        <td><?= $data->project_status; ?></td>
                                        <td><?= $data->created_by; ?></td>
                                        <td><?= $data->created_by; ?></td>
                                    <tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- START PAGINATION -->
            <?php 
                if($pages>1){
                    echo pagination($pages);
                }
            ?>
            <!-- END OF PAGINATION -->

        </div>

        <div class="app-form modal" id="modal-create-project">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Menambahkan data <?= $titlePage; ?></h3>
                </div>
                <form action="/project/create" method="POST">
                    <div class="form-group">
                        <label>Customer</label>
                        <select name="company" class="form-control">
                            <option value=''>Customer</option>
                            <?php foreach($companies as $company): ?>
                                <option title="<?= $company->name; ?>" value=<?= $company->id ?>><?= makeItShort($company->name, 50); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>  
                    <div class="form-group">
                        <label>PO in</label>
                        <select name="po_quo" class="form-control" required>
                            <option value=''>PO</option>
                        </select>
                    </div>  
                    <div class="data-respond"></div>
                    <div class="form-group">
                        <label>Project</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama project" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" placeholder="Deskripsi"></textarea>
                    </div>  
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Dimulai</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Berakhir</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label>PIC</label>
                        <select name="pic" class="form-control" required>
                            <option value=''>PIC</option>
                            <?php foreach($users as $pic): ?> 
                                <option value= <?= $pic->id; ?> ><?= ucfirst($pic->name); ?></option>             
                            <?php endforeach; ?>
                        </select>
                    </div>   
                    <button type="button" class="btn btn-danger btn-close">Tutup</button>
                    <button type="submit" class="btn btn-primary" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                </form>

                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

    </div> 

</main>

<script type="text/javascript">
    
    $(document).ready(function(){
        
        //Show the list of po from the buyer (PO IN)
        $("#modal-create-project").on("change", "select[name~='company']", function(){
            $("#modal-create-project").find(".modal-content").find(".data-respond").empty()
            var company = $(this).val();
            
            if(company!=0 && company!=null && company.length>0){
                $.get("/form/po/get-number", {company:company, do_type:0}, function(data, status){
                    var poNumber="<option value='0'>PO number</option>";
                    var responds=JSON.parse(data);

                    if(responds.length>0){
                        for(var i=0; i<responds.length; i++){
                            poNumber+="<option value="+responds[i].po_quo+" data-po="+responds[i].id+">"+responds[i].po_number+"</option>";
                        }   
                    }    
                    $("select[name~='po_quo']").empty().append(poNumber);           
                });
            }  
        });
        
        //Show information about the selected PO
        $("#modal-create-project").on("change", "select[name~='po_quo']", function(){
            var poNumber = $(this).find("option:selected").attr("data-po");

            if(poNumber!=0 && poNumber!=null && poNumber.length>0){
                $.get("/form/po/detail", {po:poNumber}, function(data, status){

                    var quotationData="Belum terdapat data";
                    var responds=JSON.parse(data);

                    //console.log(responds);

                    var poData = responds.poData[0];
                    var poNum = poData.po_number;
                    var poDate = poData.po_date;
                    var supplier = poData.supplier;
                    var buyer = poData.buyer;
                    var picB = poData.pic_buyer;
                    var picS = poData.pic_supplier;
                    var addressS = poData.saddress;
                    var addressB = poData.baddress;

                    var poProduct = "<h3>Supplier: "+supplier+"</h3><h3>Buyer: "+buyer+"</h3><h4>"+addressB+"</h4>";
                    poProduct+="<h4><span style='background-color:#95DEE3;'><a href='/form/po/detail?po="+poData.po+"' target='blank'>"+poNum+"</a></span></h4><h4>Quo date:"+poDate+"</h4>";

                    poProduct += "<table class='table table-striped'><thead><tr><th>Part Number</th><th>Product</th><th>Qty</th></tr></thead><tbody>";
                    
                    var poDetail = responds.poDetailData;

                    //console.log(poDetail);

                    //clone class .modal-add-stock-detail as many as poDetail.length
                    var firstStockDetail = $("#modal-create-do-form").find(".modal-add-stock-detail:first");
                    var firstStockItem = firstStockDetail.find(".stock-item:first");

                    for(var i=0; i<poDetail.length; i++){

                        poProduct += "<tr data-item="+poDetail[i].pid+"><td>"+poDetail[i].part_number+"</td>";
                        poProduct += "<td>"+poDetail[i].product+"</td>";
                        poProduct += "<td>"+poDetail[i].quantity+"</td></tr>";
                        //poProduct += "<td><button type='button' class='btn btn-link add-stock-detail btn-modal' id='stock-product-"+poDetail[i].pid+"'>Detail</button></td></tr>";

                        var clone = firstStockDetail.clone();
                        var product = poDetail[i].pid;

                        //if this is first data, no need to clone class .modal-add-stock-detail
                        if(i==0){
                            firstStockDetail.attr("id", "modal-stock-product-"+poDetail[i].pid);
                            firstStockDetail.find(".stock-item").find("input[name~='product[]']").val(poDetail[i].pid);
                            firstStockDetail.find(".modal-header").find("h3").text(poDetail[i].product);

                            for(var j=1; j<poDetail[i].quantity; j++){
                                //locate the cloned data after the last element class .stock-item
                                firstStockDetail.find(".stock-item:last").after(firstStockItem.clone());
                            }
                        }else{
                            var lastStockDetail = $("#modal-create-project").find(".modal-add-stock-detail:last");
                            
                            var currentStockDetail = clone.attr("id", "modal-stock-product-"+poDetail[i].pid); 

                            //remove all stock-item except the first
                            clone.find(".stock-item:not(:first)").remove();

                            //locate it at the last
                            lastStockDetail.after(currentStockDetail);

                            var currentStockItem = lastStockDetail.find(".stock-item:first");

                            currentStockDetail.find(".modal-header").find("h3").text(poDetail[i].product);

                            for(var j=1; j<poDetail[i].quantity; j++){
                                
                                currentStockDetail.find(".stock-item:last").after(currentStockItem.clone());
                                currentStockDetail.find(".stock-item:last").find("input, select").attr("required", true);
                            }

                            currentStockDetail.find(".stock-item").find("input[name~='product[]']").val(poDetail[i].pid);
                            
                        } 
                        
                    }

                    poProduct += "</tbody></table>";
                        
                    $("#modal-create-project").find(".modal-content").find(".data-respond").empty().append(poProduct);

                });
            }else{
                $("#modal-create-project").find(".modal-content").find(".data-respond").empty().append("Belum terdapat data");
            }
        });

    });

</script>

<?php

require base.'base/footer.view.php'

?>
