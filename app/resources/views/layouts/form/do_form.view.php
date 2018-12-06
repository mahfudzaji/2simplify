<?php

$titlePage=returnMessage()['doForm']['title'];

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>

        <header id="main-header">
            <h1><?= $titlePage; ?></h1>
            <p>Halaman ini menangani data terkait <?= $titlePage; ?></p>
            <button class="btn btn-sm btn-header btn-modal" id="create-do-form"><span class="glyphicon glyphicon-pencil"></span> Tambahkan data</button>
        </header>

        <div class="sub-header"> 
            <form action="/form/do" method="GET" style="display:inherit">    
                <input type="hidden" name="search" value="true">
                <div class="search" id="buyer-based">
                    <div class="form-group">
                        <select name="buyer" class="form-control">
                            <option value=''>Pembeli</option>
                            <?php foreach($partners as $partner): ?>
                                <option value=<?= $partner->id ?>><?= ucfirst($partner->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="supplier-based">
                    <div class="form-group">
                        <select name="supplier" class="form-control">
                            <option value=''>Penjual</option>
                            <?php foreach($partners as $partner): ?>
                                <option value=<?= $partner->id ?>><?= ucfirst($partner->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="product-based">
                    <div class="form-group">
                        <select name="product" class="form-control">
                            <option value=''>Produk</option>
                            <?php foreach($products as $product): ?>
                                <option title="<?= $product->name; ?>" value=<?= $product->id ?>><?= makeItShort($product->name, 50); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="date-based" style="position:relative">
                    <button type="button" class="btn btn-default" id="btn-date-based">TANGGAL DO</button>
                    <div class="form-group" style="position: absolute;left: 50%;margin-top: 5px;transform: translateX(-50%);z-index: 5;display: none;width: 400px;">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="do_date_start" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="do_date_end" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button> 
                </div>     
            </form>
        </div>

        <div class="info">
            <label><span class="glyphicon glyphicon-floppy-saved"></span> Jumlah data: <?= $sumOfAllData; ?></label>
        </div>

        <div class="main-data" style="background:var(--theme-color-grey)">
            <?php if(count($doData)<1): ?>
                <div class="text-center">Belum terdapat data tersimpan</div>
            <?php else: ?>
                <div class="container-fluid grid-view">
                    <?php foreach($doData as $data): ?>
                        <?php $signInOut = $parameterData['company']==$data->bid?'form-in':'form-out'; ?>
                        <a href="/form/do/detail?do=<?= $data->id ?>">
                        <div class="cover-grid <?= $signInOut; ?>" style="overflow-y:auto;">
                            <ul>
                                <li><?= ucfirst($data->do_number); ?></li>
                                <li>S: <?= ucfirst($data->supplier); ?></li>
                                <li>B: </span> <?= ucfirst($data->buyer); ?></li>
                                <li><span class="glyphicon glyphicon-calendar"></span> <?= ucfirst($data->do_date); ?></li>
                                <?php $product = explode('<br>', $data->product); ?>
                                <?php if(count($product)>3): ?>
                                    <li><?= $product[0]; ?></li>
                                    <li><?= $product[1]; ?></li>
                                    <li>...</li>
                                    <li><?= $product[count($product)-1]; ?></li>
                                <?php else: ?>
                                    <li><?= $data->product; ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div>
                    <a href="<?= (strpos($_SERVER['REQUEST_URI'], '?')==false)?rtrim($_SERVER['REQUEST_URI'],'/').'?download=true':rtrim($_SERVER['REQUEST_URI'],'/').'&download=true'; ?>" target="_blank"><button type="button" class="btn btn-md btn-primary"><span class="glyphicon glyphicon-download-alt"></span> Download</button></a>
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

    </div>

    <div class="app-form modal" id="modal-create-do-form">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Tambahkan <?= $titlePage; ?></h3>
                </div>

                <form action="/form/do/create" method="POST" class="form-modal">
                    <div class="modal-wizard show">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <!-- <button class="btn btn-md btn-success btn-modal-toggle" id="po-in-form">PO IN</button> -->
                                <input type="radio" name="do_type" value="1" checked><strong>DO IN</strong> 
                            </div>
                            <div class="col-md-6 text-center">
                                <!-- <button class="btn btn-md btn-default btn-modal-toggle" id="po-out-form">PO OUT</button> -->
                                <input type="radio" name="do_type" value="0" required><strong>DO OUT</strong>
                            </div>
                        </div>
                        <div class="form-group">
                            <label id="company-label">Dari Perusahaan</label>
                            <select name="company" class="form-control" required>
                                <option value="0">Perusahaan</option>
                                <?php foreach($partners as $data): ?>
                                    <option value=<?= $data->id; ?>><?= $data->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nomor PO</label>
                            <select name="po_quo" class="form-control" required>
                            </select>
                        </div>
                        <div class='form-group'>
                            <label>DO Number</label>
                            <input type='text' name='do_number' class='form-control' required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal DO</label>
                            <input type="date" name="do_date" class="form-control" required>
                        </div>
                        <div class="data-respond">
                        </div>
                       <!--  <br>
                        <button type="button" class="btn btn-danger btn-close" >Tutup</button>
                        <button type="submit" class="btn btn-md btn-primary" style="float:right;"><span class="glyphicon glyphicon-send"></span> Kirim</button> -->
                    </div>
                    <div class="modal-wizard">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Diserahkan oleh</label>
                                <input type="text" name="delivered_by" class="form-control" placeholder="Diserahkan oleh" required>
                            </div>
                            <div class="col-md-6">
                                <label>Diterima oleh</label>
                                <input type="text" name="received_by" class="form-control" placeholder="Diterima oleh" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <!-- <input type="text" name="remark" class="form-control" placeholder="Keterangan tambahan"> -->
                            <div id="remark"></div>
                        </div>
                        <div class="form-group">
                            <label>Disetujui oleh</label>
                            <select name="approved_by" class="form-control" required>
                                <option value=''>-</option>
                                <?php foreach($approvalPerson as $person): ?>
                                    <option value=<?= $person->user_id ?>><?= ucfirst($person->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                <!--
                    <div class="modal modal-add-stock-detail">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3></h3>
                            </div>
                            <div class="stock-item row">
                                <input type="hidden" name='product[]' value='' required>
                                <div class="form-group col-md-6">
                                    <label>Tanggal diterima</label>
                                    <input type="date" class="form-control" name="received_at[]" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Serial number</label>
                                    <input type="text" class="form-control" name="serial_number[]" placeholder="Serial number" required>
                                </div>
                            </div>

                            <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

                        </div>
                    </div>
                -->   

                    <button type="button" class="btn btn-danger btn-back" style="display:none;"><span class="glyphicon glyphicon-chevron-left"></span> Kembali</button> 
                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>
                    <span class="wizard-step"></span>                             
                    <button type="button" name="submit" class="btn btn-primary btn-next" style="float:right;">Lanjut <span class="glyphicon glyphicon-chevron-right"></span></button>

                </form>

                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            
            </div>
        </div>

</main>


<script type="text/javascript">
    
    $(document).ready(function(){

        $("#remark").trumbowyg();

        $("#modal-create-do-form").on("change", "input[name~='do_type']", function(){
            var doType = $(this).val();

            $(this).closest("form").find("select").find("option").attr("selected", false);
            $(this).closest("form").find("select").find("option[value~='0']").attr("selected", true);
            $(this).closest("form").find(".data-respond").empty();
            $(this).closest("form").find("select[name~='po_quo']").find("option[value!='0']").remove();
            //$(this).closest("form").find("input[type='text']").val("");

            var parameters={};
            $.get("/parameter", {doType:doType}, function(data, status){
                var responds = JSON.parse(data);

                for(var i=0; i<responds.length; i++){
                    parameters[responds[i].parameter]=responds[i].value;
                }

                $("#modal-create-do-form").find("select[name~='company']").find("option[value~="+parameters.company+"]").hide();
            })

            //doType:1-->do in, 2-->do out
            if(doType==1){
                $(this).closest("form").find("#company-label").text("Dari perusahaan");
                //$(this).closest("form").find("select[name~='approved_by']").closest(".form-group").remove();
                $(this).closest("form").find("select[name~='approved_by']").closest(".form-group").find("label").text("Diketahui oleh");
                //give new input field
                $(this).closest("form").find("select[name~='po_quo']").closest(".form-group").after("<div class='form-group'><label>DO Number</label><input type='text' name='do_number' class='form-control' required></div>");
            }else{
                $(this).closest("form").find("#company-label").text("Untuk perusahaan");
                $(this).closest("form").find("input[name~='do_number']").closest(".form-group").remove();
                //give new input field
                //$(this).closest("form").find("input[name~='remark']").closest(".form-group").after("<div class='form-group'><label>Disetujui oleh</label><select name='approved_by' class='form-control' required></select></div>");
                $(this).closest("form").find("select[name~='approved_by']").closest(".form-group").find("label").text("Disetujui oleh");
            }
        });

        $("#modal-create-do-form").on("change", "select[name~='company']", function(){
            $("#modal-create-do-form").find(".modal-content").find(".data-respond").empty()
            var company = $(this).val();
            var doType = $(this).closest("form").find("input[name~='do_type']:checked").val();
            
            if(company!=0 && company!=null && company.length>0){
                $.get("/form/po/get-number", {company:company, do_type:doType}, function(data, status){
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

        $("#modal-create-do-form").on("change", "select[name~='po_quo']", function(){
            var poNumber = $(this).find("option:selected").attr("data-po");

            $("#modal-create-do-form").find(".modal-add-stock-detail:not(:first)").remove();
            $("#modal-create-do-form").find(".modal-add-stock-detail").find(".stock-item:not(:first)").remove();

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
                            var lastStockDetail = $("#modal-create-do-form").find(".modal-add-stock-detail:last");
                            
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
                        
                    $("#modal-create-do-form").find(".modal-content").find(".data-respond").empty().append(poProduct);

                });
            }else{
                $("#modal-create-do-form").find(".modal-content").find(".data-respond").empty().append("Belum terdapat data");
            }
        });

        $("main").on("click", ".add-stock-detail", function(){
            //doType 0 => DO out
            //doType 1 => DO in 

            var doType = $(this).closest("form").find("input[name~='do_type']:checked").val();
            var serialNumberList = "<option value=''>PILIH SN</option>";
            
            var stockDetail = $(this).attr("id");
            var stockItem = $(this).closest("tr").attr("data-item");

            if(doType==0){
                //get available serial number related to the stock item
                $.get("/stock/get-serial-number", {item:stockItem}, function(data , status){
                    var respond = JSON.parse(data);

                    for(var i=0; i<respond.length; i++){
                        serialNumberList += "<option value="+respond[i].serial_number+">"+respond[i].serial_number+"</option>";
                    }
                    console.log(serialNumberList);
                    //change input type text to the select element that have list of serial number
                    $("main").find("#modal-"+stockDetail).find("input[name~='serial_number[]'], select[name~='serial_number[]']").replaceWith("<select name='serial_number[]' class='form-control' required>"+serialNumberList+"</select>");

                });
            }else{

                $("main").find("#modal-"+stockDetail).find("input[name~='serial_number[]'], select[name~='serial_number[]']").replaceWith("<input type='text' name='serial_number[]' class='form-control' required>");

            }
        });
    })
</script>

<?php

require base.'base/footer.view.php'

?>
