<?php

$titlePage=returnMessage()['receiptForm']['title'];

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>

        <header id="main-header">
            <h1><?= $titlePage; ?></h1>
            <p>Halaman ini menangani data terkait <?= $titlePage; ?></p>
            <button class="btn btn-sm btn-header btn-modal" id="create-receipt-form"><span class="glyphicon glyphicon-pencil"></span> Tambahkan data</button>
        </header>

        <div class="sub-header"> 
            <form action="/form/receipt" method="GET" style="display:inherit">    
                <input type="hidden" name="search" value="true">
                <div class="search" id="buyer-based">
                    <div class="form-group">
                        <select name="buyer" class="form-control">
                            <option value=''>Pembeli</option>
                            <?php foreach($partners as $partner): ?>
                                <option value="<?= $partner->id ?>"><?= makeItShort(ucfirst($partner->name),50); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="supplier-based">
                    <div class="form-group">
                        <select name="supplier" class="form-control">
                            <option value=''>Penjual</option>
                            <?php foreach($partners as $partner): ?>
                                <option value="<?= $partner->id ?>"><?= makeItShort(ucfirst($partner->name),50); ?></option>
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
                    <button type="button" class="btn btn-default" id="btn-date-based">TANGGAL RECEIPT</button>
                    <div class="form-group" style="position: absolute;left: 50%;margin-top: 5px;transform: translateX(-50%);z-index: 5;display: none;width: 400px;">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="receipt_date_start" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="receipt_date_end" class="form-control">
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
            <?php if(count($receiptData)<1): ?>
                <div class="text-center">Belum terdapat data tersimpan</div>
            <?php else: ?>
                <div class="container-fluid grid-view">
                    <?php foreach($receiptData as $data): ?>
                        <?php $signInOut = $parameterData['company']==$data->bid?'form-in':'form-out'; ?>
                        <a href="/form/receipt/detail?r=<?= $data->id ?>">
                        <div class="cover-grid <?= $signInOut; ?>" style="overflow-y:auto;">
                            <ul>
                                <li><span class="glyphicon glyphicon-calendar"></span> <?= ucfirst($data->receipt_date); ?></li>
                                <li>NO: <?= $data->receipt_number==''?'-':ucfirst($data->receipt_number); ?></li>
                                <li>S: <?= ucfirst($data->supplier); ?></li>
                                <li>B: </span> <?= ucfirst($data->buyer); ?></li>  
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

    <div class="app-form modal" id="modal-create-receipt-form">         
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tambahkan <?= $titlePage; ?></h3>
            </div>
            
            <form action="/form/receipt/create" method="POST">
                <div class="modal-wizard show">
                    <div class="description">
                        <p>Form ini digunakan untuk menambahkan receipt in dan out.</p>
                        <dl>
                        <dt>Catatan</dt>
                            <dd>Receipt In: Membeli item dari supplier</dd>
                            <dd>Receipt Out: Menjual item kepada buyer</dd>
                        </dl>
                    </div>
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <!-- <button class="btn btn-md btn-success btn-modal-toggle" id="po-in-form">PO IN</button> -->
                            <input type="radio" name="receipt_type" value="1" required checked><strong>RECEIPT IN</strong> 
                        </div>
                        <div class="col-md-6 text-center">
                            <!-- <button class="btn btn-md btn-default btn-modal-toggle" id="po-out-form">PO OUT</button> -->
                            <input type="radio" name="receipt_type" value="2"><strong>RECEIPT OUT</strong>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tanggal receipt</label>
                        <input type="date" name="receipt_date" class="form-control" required>
                    </div>
                    <div class='form-group'>
                        <label>Receipt Number</label>
                        <input type='text' name='receipt_number' class='form-control' required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Supplier</label>
                                <select name="supplier" class="form-control">
                                    <option value=''>SUPPLIER</option>
                                    <?php foreach($partners as $partner): ?>
                                        <option value="<?= $partner->id ?>"><?= ucfirst($partner->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
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
                                        <option title="Available: <?= $product->quantity; ?>" value=<?= $product->id ?> data-qty=<?= $product->quantity; ?>><?= makeItShort(ucfirst($product->name), 50); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number" min=0 name="quantity[]" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Harga satuan</label>
                                <input type="number" min=0 name="price[]" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Disc (%)</label>
                                <input type="number" min=0 name="discount[]" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-default btn-add-input-form">Tambah</button>
                    
                    
                    <div class="form-group">
                        <label>PPN (%)</label>
                        <input type="number" min=0 max=100 name="ppn" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
                        <!-- <textarea name="remark" class="form-control" placeholder="Keterangan tambahan"></textarea> -->
                        <div id="remark"></div>
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
		
		$('#remark').trumbowyg();

        $("#create-receipt-form").on("click", function(){
            var receipt_type = 1;
            var parameters={};
            $.get("/parameter", {poType:receipt_type}, function(data, status){
                var responds = JSON.parse(data);
                var form=$("input[name~='receipt_type']").closest("form");

                for(var i=0; i<responds.length; i++){
                    parameters[responds[i].parameter]=responds[i].value;
                }

                //set value of select element to default parameter
                form.find("select[name~='buyer']").find("option").attr("selected", false);
                form.find("select[name~='buyer']").find("option[value~='"+parameters.company+"']").attr("selected", true);
                form.find("select[name~='buyer']").val(parameters.company);
                form.find("select[name~='supplier']").find("option[value='"+parameters.company+"']").hide();

                //show only specific value and hide the others
                form.find("select[name~='buyer']").find("option[value!='"+parameters.company+"']").hide();
            });
        });

        //make all select to default state
        $("#modal-create-receipt-form").on("change", "input[name~='receipt_type']", function(){
            var receiptType = $(this).val();

            $(this).closest("form").find("select").find("option").attr("selected", false);
            $(this).closest("form").find("select").find("option[value~='0']").attr("selected", true);
            $(this).closest("form").find(".data-respond").empty();

        });
        
        $("#modal-create-receipt-form").on("change", "input[name~='receipt_type']", function(){
            var receiptType = $(this).val();
            var parameters={};

            $.get("/parameter", {poType:receiptType}, function(data, status){
                var responds = JSON.parse(data);
                var form=$("input[name~='receipt_type']").closest("form");

                for(var i=0; i<responds.length; i++){
                    parameters[responds[i].parameter]=responds[i].value;
                }

                //console.log(parameters.company);
                //receiptType:1-->receipt in, 0-->receipt out
                if(receiptType==1){
                    //give new input field
                    $("input[name~='receipt_date']").closest(".form-group").after("<div class='form-group'><label>Receipt Number</label><input type='text' name='receipt_number' class='form-control' required></div>");
                    
                    //set value of select element to default parameter
                    form.find("select[name~='buyer']").find("option[value~='"+parameters.company+"']").attr("selected", true);
                    form.find("select[name~='buyer']").val(parameters.company);

                    //show only specific value and hide the others
                    form.find("select[name~='buyer']").find("option[value!='"+parameters.company+"']").hide();

                    form.find("select[name~='supplier']").find("option").show();
                    form.find("select[name~='supplier']").find("option[value='"+parameters.company+"']").hide();
                    //set nothing selected in the buyer select element
                    form.find("select[name~='supplier']").find("option").attr('selected', false);
                    
                }else{
                    $("input[name~='receipt_number']").closest(".form-group").remove();
                    
                    form.find("select[name~='supplier']").find("option[value~='"+parameters.company+"']").attr("selected", true);
                    form.find("select[name~='supplier']").val(parameters.company);
                    
                    form.find("select[name~='supplier']").find("option[value!='"+parameters.company+"']").hide();

                    form.find("select[name~='buyer']").find("option").show();
                    form.find("select[name~='buyer']").find("option[value='"+parameters.company+"']").hide();
                    form.find("select[name~='buyer']").find("option").attr('selected', false);
                    
                }

            });
            //console.log(typeof(parameters));
        });
        
        $("#modal-create-receipt-form").on("change", "select[name~='product[]']", function(){
            var product = $(this).val();

            var receiptType = $(this).closest("form").find("input[name~='receipt_type']:checked").val();

            if(receiptType==2){
                var quantity = $(this).find("option:selected").attr("data-qty");
            
                $(this).closest(".inline-input").find("input[name~='quantity[]']").val(0).attr("max", quantity);
            } 

        });
            
    })
</script>