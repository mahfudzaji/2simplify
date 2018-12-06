<?php

$titlePage=returnMessage()['poForm']['title'];

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>

        <header id="main-header">
            <h1><?= $titlePage; ?></h1>
            <p>Halaman ini menangani data terkait <?= $titlePage; ?></p>
            <button class="btn btn-sm btn-header btn-modal" id="create-po-form"><span class="glyphicon glyphicon-pencil"></span> Tambahkan data</button>
        </header>

        <div class="sub-header"> 
            <form action="/form/po" method="GET" style="display:inherit">    
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
                                <option title="<?= $product->name; ?>" value=<?= $product->id ?>><?= (strlen($product->name)>50)?substr(ucfirst($product->name),0, 50)."...":ucfirst($product->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="date-based" style="position:relative">
                    <button type="button" class="btn btn-default" id="btn-date-based">TANGGAL PO</button>
                    <div class="form-group" style="position: absolute;left: 50%;margin-top: 5px;transform: translateX(-50%);z-index: 5;display: none;width: 400px;">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="po_date_start" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="po_date_end" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search" id="quo-based">
                    <div class="form-group">
                        <select name="quo" class="form-control">
                            <option value=''>Ada Quo?</option>
                            <option value=1>Ada</option>
                            <option value=0>Tidak</option>
                        </select>
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
            <?php if(count($poData)<1): ?>
                <div class="text-center">Belum terdapat data tersimpan</div>
            <?php else: ?>
                <div class="container-fluid grid-view">
                    <?php foreach($poData as $data): ?>
                        <?php $signInOut = $parameterData['company']==$data->bid?'form-out':'form-in'; ?>
                        <a href="/form/po/detail?po=<?= $data->id ?>">
                        <div class="cover-grid <?= $signInOut; ?>" style="overflow-y:auto;">
                            <ul>
                                <li><?= ucfirst($data->po_number); ?></li>
                                <li>S: <?= ucfirst($data->supplier); ?></li>
                                <li>B: </span> <?= ucfirst($data->buyer); ?></li>
                                <li><span class="glyphicon glyphicon-calendar"></span> <?= ucfirst($data->doc_date); ?></li>
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

    <div class="app-form modal" id="modal-create-po-form">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Tambahkan <?= $titlePage; ?></h3>
                </div>
                
                <div>
                    <h3 class="text-center">Ada quotation?</h3>
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <button class="btn btn-lg btn-success btn-modal-toggle" id="with-quo-form">ADA</button>
                        </div>
                        <div class="col-md-6 text-center">
                            <button class="btn btn-lg btn-default btn-modal-toggle" id="no-quo-form">TIDAK</button>
                        </div>
                    </div>
                </div>
                <hr>

                <form action="/form/po/create-from-quo" method="POST" class="form-modal" id="modal-toggle-with-quo-form" style="display:none;">
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <!-- <button class="btn btn-md btn-success btn-modal-toggle" id="po-in-form">PO IN</button> -->
                            <input type="radio" name="po_type" value="1"><strong>PO IN</strong> 
                        </div>
                        <div class="col-md-6 text-center">
                            <!-- <button class="btn btn-md btn-default btn-modal-toggle" id="po-out-form">PO OUT</button> -->
                            <input type="radio" name="po_type" value="0" required><strong>PO OUT</strong>
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
                        <label>Nomor Quotation</label>
                        <select name="quotation" class="form-control" required>
                        </select>
                    </div>
                    <input type="hidden" name="quo_revision">
                    <div class="data-respond">
                    </div>
                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>
                    <button type="submit" class="btn btn-md btn-primary" style="float:right;"><span class="glyphicon glyphicon-send"></span> Proses ke PO</button>
                </form>

                <form action="/form/po/create" method="POST" class="form-modal" id="modal-toggle-no-quo-form" style="display:none;">
                    <div class="modal-wizard show">
                        <div class="description">
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <!-- <button class="btn btn-md btn-success btn-modal-toggle" id="po-in-form">PO IN</button> -->
                                <input type="radio" name="po_type" value="1" checked><strong>PO IN</strong> 
                            </div>
                            <div class="col-md-6 text-center">
                                <!-- <button class="btn btn-md btn-default btn-modal-toggle" id="po-out-form">PO OUT</button> -->
                                <input type="radio" name="po_type" value="0"><strong>PO OUT</strong>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Tanggal PO</label>
                            <input type="date" name="doc_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>PO Number</label>
                            <input type='text' name='po_number' class='form-control' required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <select name="supplier" class="form-control">
                                        <option value=''>SUPPLIER</option>
                                        <?php foreach($partners as $partner): ?>
                                            <option value=<?= $partner->id ?> data-rel=<?= $partner->relationship; ?> ><?= ucfirst($partner->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>PIC</label>
                                    <input type="text" name="pic_supplier" class="form-control" placeholder="PIC" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Buyer</label>
                                    <select name="buyer" class="form-control">
                                        <option value=''>BUYER</option>
                                        <?php foreach($partners as $partner): ?>
                                            <option value=<?= $partner->id ?> ><?= ucfirst($partner->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>PIC</label>
                                    <input type="text" name="pic_buyer" class="form-control" placeholder="PIC" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Mata uang</label>
                            <select name="currency" class="form-control">
                                <option value=''>MATA UANG</option>
                                <option value='1'>Rupiah</option>
                                <option value='2'>Dollar</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-wizard">
                        <div class="description">
                            <p>Pilih produk sesuai barang yang diserahterimakan. Apabila barang tidak terdaftar maka daftarkan terlebih dahulu atau pilih
                            produk 'lain-lain'. <br>Kemudian tuliskan serial number, apabila tidak terdapat keterangan serial number maka beri tanda '-' dan kemudian tuliskan keterangan tambahan yang diperlukan.</p>
                        </div>

                        <div class="row inline-input">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Produk</label>
                                    <select name="product[]" class="form-control" required>
                                        <option value=''>PRODUK</option>
                                        <?php foreach($products as $product): ?>
                                            <option title="<?= $product->name; ?>" value=<?= $product->id ?>><?= (strlen($product->name)>50)?substr(ucfirst($product->name),0, 50)."...":ucfirst($product->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <input type="number" min=0 name="quantity[]" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga satuan</label>
                                    <input type="number" min=0 name="price_unit[]" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Diskon (%)</label>
                                    <input type="number" min=0 name="item_discount[]" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <!--<span><button class="btn btn-danger btn-float"><span class="glyphicon glyphicon-trash"></span></button></span>-->
                        <button type="button" class="btn btn-default btn-add-input-form">Tambah</button>
                        
                        <div class="form-group">
                            <label>Keterangan tambahan</label>
                            <textarea name="remark" class="form-control" placeholder="Keterangan tambahan"></textarea>
                        </div>
                    </div>
                    <div class="modal-wizard">
                        <div class="description">
                            <p>Form ini digunakan untuk menambahkan data tanda terima. Form ini digunakan untuk menambahkan data tanda terima.</p>
                            <dl>
                            <dt>Catatan</dt>
                                <dd>Pinjam: pihak asal(<em>dari</em>) meminjamkan barang kepada yang sebagai tujuan(<em>untuk</em>)</dd>
                                <dd>Serah terima: pihak asal(<em>dari</em>) melakukan serah terima barang ke pihak tujuan(<em>untuk</em>) 
                            </dl>
                        </div>
                        <div class="form-group">
                            <label>PPN</label>
                            <input type="number" min=0 name="ppn" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Diketahui oleh</label>
                            <select name="acknowledged_by" class="form-control" required>
                                <option value=''>-</option>
                                <?php foreach($approvalPerson as $person): ?>
                                    <option value=<?= $person->user_id ?>><?= ucfirst($person->name); ?></option>
                                <?php endforeach; ?>
                            </select>
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

                    <button type="button" class="btn btn-danger btn-back" style="display:none;"><span class="glyphicon glyphicon-chevron-left"></span> Kembali</button> 
                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>
                    <span class="wizard-step"></span>                             
                    <button type="button" name="submit" class="btn btn-primary btn-next" style="float:right;">Lanjut <span class="glyphicon glyphicon-chevron-right"></span></button>

                </form>

                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            
            </div>
        </div>

</main>



<?php

require base.'base/footer.view.php'

?>

<script type="text/javascript">
    

    $(document).ready(function(){

        $("#create-po-form").on("click", function(){
            var poType = 1;
            var parameters={};
            $.get("/parameter", {poType:poType}, function(data, status){
                var responds = JSON.parse(data);
                var form=$("input[name~='po_type']").closest("form");

                for(var i=0; i<responds.length; i++){
                    parameters[responds[i].parameter]=responds[i].value;
                }

                //set value of select element to default parameter
                form.find("select[name~='supplier']").find("option").attr("selected", false);
                form.find("select[name~='supplier']").find("option[value~='"+parameters.company+"']").attr("selected", true);
                form.find("select[name~='supplier']").val(parameters.company);

                //show only specific value and hide the others
                form.find("select[name~='supplier']").find("option[value!='"+parameters.company+"']").hide();
            });
        });

        $("#modal-toggle-with-quo-form").on("change", "select[name~='company']", function(){
            $("#modal-create-po-form").find(".modal-content").find(".data-respond").empty()
            var company = $(this).val();
            var poType = $(this).closest("form").find("input[name~='po_type']:checked").val();

            if(company!=0 && company!=null && company.length>0){
                $.get("/form/quo/get-number", {company:company, po_type:poType}, function(data, status){
                    var quotationNumber="<option value='0'>Quo. number</option>";
                    var responds=JSON.parse(data);

                    if(responds.length>0){
                        for(var i=0; i<responds.length; i++){
                            var revision = '';
                            if(responds[i].revision_number != null && responds[i].revision_number != ''){
                                revision = '/rev'+responds[i].revision_number;

                            }
                            quotationNumber+="<option value="+responds[i].id+" data-revision="+responds[i].revision_number+">"+responds[i].quo_number+revision+"</option>";
                        }   
                    }    
                    $("select[name~='quotation']").empty().append(quotationNumber);           
                });
            }  
        });

        $("#modal-toggle-with-quo-form").on("change", "input[name~='po_type']", function(){
            var poType = $(this).val();

            $(this).closest("form").find("select").find("option").attr("selected", false);
            $(this).closest("form").find("select").find("option[value~='0']").attr("selected", true);
            $(this).closest("form").find(".data-respond").empty();
            //$(this).closest("form").find("input[type='text']").val("");

            var parameters={};
            $.get("/parameter", {poType:poType}, function(data, status){
                var responds = JSON.parse(data);

                for(var i=0; i<responds.length; i++){
                    parameters[responds[i].parameter]=responds[i].value;
                }

                $("#modal-toggle-with-quo-form").find("select[name~='company']").find("option[value~="+parameters.company+"]").hide();
            })

            //poType:1-->po in, 2-->po out
            if(poType==1){
                $(this).closest("form").find("#company-label").text("Dari perusahaan");
                //give new input field
                $(this).closest("form").find("select[name~='quotation']").closest(".form-group").after("<div class='form-group'><label>PO Number</label><input type='text' name='po_number' class='form-control' required></div>");
            }else{
                $(this).closest("form").find("#company-label").text("Untuk perusahaan");
                $(this).closest("form").find("input[name~='po_number']").closest(".form-group").remove();
            }
        });

        $("#modal-toggle-with-quo-form").on("change", "select[name~='quotation']", function(){
            var quoNumber = $(this).val();
            var revision = $(this).find("option:selected").attr("data-revision");

            $(this).closest("form").find("input[name~='quo_revision']").val(revision);

            if(quoNumber!=0 && quoNumber!=null && quoNumber.length>0){
                $.get("/form/quo/detail", {quo:quoNumber, revision:revision}, function(data, status){
                    var quotationData="Belum terdapat data";
                    var responds=JSON.parse(data);

                    console.log(responds);

                    var quoData = responds.quoData[0];
                    var quoNum = quoData.quo_number;
                    var quoDate = quoData.quo_date;
                    var supplier = quoData.supplier;
                    var buyer = quoData.buyer;
                    var picB = quoData.pic_buyer;
                    var picS = quoData.pic_supplier;
                    var addressS = quoData.saddress;
                    var addressB = quoData.baddress;

                    if('revision_number' in quoData){
                        quoNum+='/Rev'+quoData.revision_number;
                    }

                    var quoProduct = "<h3>Supplier: "+supplier+"</h3><h3>Buyer: "+buyer+"</h3><h4>"+addressB+"</h4>";
                    quoProduct+="<h4><span style='background-color:#95DEE3;'>"+quoNum+"</span></h4><h4>Quo date:"+quoDate+"</h4>";

                    quoProduct += "<table class='table table-striped'><thead><tr><th>Part Number</th><th>Product</th><th>Quantity</th><th>Price unit</th><th>Disc(%)</th><th>Price total</th><th>Status</th></tr></thead><tbody>";
                    
                    var quoDetail = responds.quoDetailData;

                    var priceTotal = 0;
                    for(var i=0; i<quoDetail.length; i++){
                        var item=(100-quoDetail[i].item_discount)*quoDetail[i].total*0.01; 
                        priceTotal+=item;
                        quoProduct += "<tr><td>"+quoDetail[i].part_number+"</td>";
                        quoProduct += "<td>"+quoDetail[i].product+"</td>";
                        quoProduct += "<td>"+quoDetail[i].quantity+"</td>";
                        quoProduct += "<td>"+quoDetail[i].price_unit+"</td>";
                        quoProduct += "<td>"+quoDetail[i].item_discount+"</td>";
                        quoProduct += "<td>"+item+"</td>";
                        quoProduct += "<td>"+quoDetail[i].status+"</td></tr>";
                    }

                    quoProduct += "</tbody></table>";
                        
                    $("#modal-create-po-form").find(".modal-content").find(".data-respond").empty().append(quoProduct);

                });
            }else{
                $("#modal-create-po-form").find(".modal-content").find(".data-respond").empty().append("Belum terdapat data");
            }
        });
        
        $("#modal-toggle-no-quo-form").on("change", "input[name~='po_type']", function(){
            var poType = $(this).val();
            var parameters={};

            $.get("/parameter", {poType:poType}, function(data, status){
                var responds = JSON.parse(data);
                var form=$("input[name~='po_type']").closest("form");

                for(var i=0; i<responds.length; i++){
                    parameters[responds[i].parameter]=responds[i].value;
                }

                //console.log(parameters.company);
                //poType:1-->po in, 2-->po out
                if(poType==1){
                    //give new input field
                    $("input[name~='doc_date']").closest(".form-group").after("<div class='form-group'><label>PO Number</label><input type='text' name='po_number' class='form-control' required></div>");
                    
                    //set value of select element to default parameter
                    form.find("select[name~='supplier']").find("option[value~='"+parameters.company+"']").attr("selected", true);
                    form.find("select[name~='supplier']").val(parameters.company);

                    //show only specific value and hide the others
                    form.find("select[name~='supplier']").find("option[value!='"+parameters.company+"']").hide();
                    form.find("select[name~='buyer']").find("option").show();
                    
                    //set nothing selected in the buyer select element
                    form.find("select[name~='buyer']").find("option").attr('selected', false);
                }else{
                    $("input[name~='po_number']").closest(".form-group").remove();
                    
                    form.find("select[name~='buyer']").find("option[value~='"+parameters.company+"']").attr("selected", true);
                    form.find("select[name~='buyer']").val(parameters.company);
                    
                    form.find("select[name~='buyer']").find("option[value!='"+parameters.company+"']").hide();
                    form.find("select[name~='supplier']").find("option").show();
                    
                    form.find("select[name~='supplier']").find("option").attr('selected', false);
                }

            });
            //console.log(typeof(parameters));
        });
    })
</script>