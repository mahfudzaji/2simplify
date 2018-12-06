<?php

$titlePage=returnMessage()['quoForm']['title'];

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>

        <header id="main-header">
            <h1><?= $titlePage; ?></h1>
            <p>Halaman ini menangani data terkait <?= $titlePage; ?></p>
            <button class="btn btn-sm btn-header btn-modal" id="create-quo-form"><span class="glyphicon glyphicon-pencil"></span> Tambahkan data</button>
        </header>

        <div class="sub-header"> 
            <form action="/form/quo" method="GET" style="display:inherit">    
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
                    <button type="button" class="btn btn-default" id="btn-date-based">TANGGAL QUO</button>
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
                <div class="search">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button> 
                </div>     
            </form>
        </div>

        <div class="info">
            <label><span class="glyphicon glyphicon-floppy-saved"></span> Jumlah data: <?= $sumOfAllData; ?></label>
        </div>

        <div class="main-data" style="background:var(--theme-color-grey)">
            <?php if(count($quoData)<1): ?>
                <div class="text-center">Belum terdapat data tersimpan</div>
            <?php else: ?>
                <div class="container-fluid grid-view">
                    <?php foreach($quoData as $data): ?>
                        <?php $signInOut = $parameterData['company']==$data->bid?'form-in':'form-out'; ?>
                        <a href="/form/quo/detail?quo=<?= $data->id ?>">
                        <div class="cover-grid <?= $signInOut; ?>" style="overflow-y:auto;">
                            <ul>
                                <li><?= ucfirst($data->quo_number); ?></li>
                                <li>S: <?= ucfirst($data->supplier); ?></li>
                                <li>B: <?= ucfirst($data->buyer); ?></li>
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

    <div class="app-form modal" id="modal-create-quo-form">         
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tambahkan <?= $titlePage; ?></h3>
            </div>
            
            <form action="/form/quo/create" method="POST">
                <div class="modal-wizard show">
                    <div class="description">
                        <p>Form ini digunakan untuk menambahkan data Quotation in dan out.</p>
                        <dl>
                        <dt>Catatan</dt>
                            <dd>Quo In: Meminta harga penawaran dari pihak lain (Calon pembeli)</dd>
                            <dd>Quo Out: Memberi harga penawaran ke pihak lain (Calon penjual)</dd>
                        </dl>
                    </div>
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <!-- <button class="btn btn-md btn-success btn-modal-toggle" id="po-in-form">PO IN</button> -->
                            <input type="radio" name="quo_type" value="1" required checked><strong>QUO IN</strong> 
                        </div>
                        <div class="col-md-6 text-center">
                            <!-- <button class="btn btn-md btn-default btn-modal-toggle" id="po-out-form">PO OUT</button> -->
                            <input type="radio" name="quo_type" value="0"><strong>QUO OUT</strong>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label>Subject</label>
                        <input type='text' name='title' class='form-control' required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal QUO</label>
                        <input type="date" name="doc_date" class="form-control" required>
                    </div>
                    <div class='form-group'>
                        <label>QUO Number</label>
                        <input type='text' name='quo_number' class='form-control' required>
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
                        <div class="col-md-3">
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Disc (%)</label>
                                <input type="number" min=0 name="item_discount[]" class="form-control">
                            </div>
                        </div>
                        
                    </div>

                    <!--<span><button class="btn btn-danger btn-float"><span class="glyphicon glyphicon-trash"></span></button></span>-->
                    <button type="button" class="btn btn-default btn-add-input-form">Tambah</button>
                    
                    <div class="form-group">
                        <label>Keterangan / Term & condition</label>
                        <!-- <textarea name="remark" class="form-control" placeholder="Keterangan tambahan"></textarea> -->
                        <div id="remark"></div>
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

        $('#remark').trumbowyg();

        $("#create-quo-form").on("click", function(){
            var quoType = 1;
            var parameters={};
            $.get("/parameter", {quoType:quoType}, function(data, status){
                var responds = JSON.parse(data);
                var form=$("input[name~='quo_type']").closest("form");

                for(var i=0; i<responds.length; i++){
                    parameters[responds[i].parameter]=responds[i].value;
                }

                //set value of select element to default parameter
                form.find("select[name~='buyer']").find("option[value~='"+parameters.company+"']").attr("selected", true);
                form.find("select[name~='buyer']").val(parameters.company);

                //show only specific value and hide the others
                form.find("select[name~='buyer']").find("option[value!='"+parameters.company+"']").hide();
            });
        });

        $("form").on("change", "input[name~='quo_type']", function(){
            var quoType = $(this).val();
            var parameters={};

            $.get("/parameter", {quoType:quoType}, function(data, status){
                var responds = JSON.parse(data);
                var form=$("input[name~='quo_type']").closest("form");

                for(var i=0; i<responds.length; i++){
                    parameters[responds[i].parameter]=responds[i].value;
                }

                //console.log(parameters.company);
                //quoType:1-->quo in, 2-->quo out
                if(quoType==1){
                    //give new input field
                    $("input[name~='doc_date']").closest(".form-group").after("<div class='form-group'><label>QUO Number</label><input type='text' name='quo_number' class='form-control' required></div>");
                    
                    //set value of select element to default parameter
                    form.find("select[name~='buyer']").find("option[value~='"+parameters.company+"']").attr("selected", true);
                    form.find("select[name~='buyer']").val(parameters.company);

                    //show only specific value and hide the others
                    form.find("select[name~='buyer']").find("option[value!='"+parameters.company+"']").hide();
                    form.find("select[name~='supplier']").find("option").show();
                    
                    //set nothing selected in the supplier select element
                    form.find("select[name~='supplier']").find("option").attr('selected', false);
                }else{
                    $("input[name~='quo_number']").closest(".form-group").remove();
                    
                    form.find("select[name~='supplier']").find("option[value~='"+parameters.company+"']").attr("selected", true);
                    form.find("select[name~='supplier']").val(parameters.company);
                    
                    form.find("select[name~='supplier']").find("option[value!='"+parameters.company+"']").hide();
                    form.find("select[name~='buyer']").find("option").show();
                    
                    form.find("select[name~='buyer']").find("option").attr('selected', false);
                }

            });
            //console.log(typeof(parameters));
        });

    })
</script>