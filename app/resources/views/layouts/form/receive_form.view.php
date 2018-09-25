<?php

$titlePage="Tanda terima";

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';
?>

<style>
    .inline-input button.btn-float {
        transform: translate(120%, 75%);
    }
</style>

<main>
    <div class="container-fluid">
        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1>Tanda terima</h1>
            <p>Halaman ini menangani data terkait tanda terima</p>
            <button class="btn btn-sm btn-header btn-modal" id="create-receive-form"><span class="glyphicon glyphicon-pencil"></span> Tambahkan data</button>
        </header>

        <div class="sub-header"> 
            <form action="/form/tanda-terima" method="GET" style="display:inherit">    
                <input type="hidden" name="search" value="true">
                <div class="search" id="requisite-based">
                    <div class="form-group">
                        <select name="requisite" class="form-control">
                            <option value=''>KEPERLUAN</option>
                            <option value='1'>Pinjam</option>
                            <option value='2'>Serah terima</option>
                        </select>
                    </div>
                </div>
                <div class="search" id="submitted-based">
                    <div class="form-group">
                        <select name="submitted" class="form-control">
                            <option value=''>DISERAHKAN</option>
                            <?php foreach($partners as $partner): ?>
                                <option value=<?= $partner->id ?>><?= $partner->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="received-based">
                    <div class="form-group">
                        <select name="received" class="form-control">
                            <option value=''>DITERIMA</option>
                            <?php foreach($partners as $partner): ?>
                                <option value=<?= $partner->id ?>><?= $partner->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="date-based" style="position:relative">
                    <button type="button" class="btn btn-default" id="btn-date-based">TANGGAL TERIMA</button>
                    <div class="form-group" style="position: absolute;left: 50%;margin-top: 5px;transform: translateX(-50%);z-index: 5;display: none;width: 400px;">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="receive_date_start" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="receive_date_end" class="form-control">
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="search">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Cari</button> 
                </div>     
            </form>
        </div>

        <div class="info">
            <label><span class="glyphicon glyphicon-floppy-saved"></span> Jumlah data: <?= $sumOfAllData; ?></label>
        </div>

        <div class="main-data" style="background:var(--theme-color-grey)">
            <?php echo count($receiveData)<1?'<div class="text-center">Belum terdapat data tersimpan</div>':''; ?>
            <div class="container-fluid grid-view">
                <?php foreach($receiveData as $data): ?>
                    <a href="/form/tanda-terima/detail?r=<?= $data->id ?>">
                    <div class="cover-grid" style="overflow-y:auto;">
                        <ul>
                            <li><span class="glyphicon glyphicon-calendar"></span> <?= $data->receive_date; ?></li>
                            <li><strong>S:</strong> <?= ucfirst($data->submitted); ?></li>
                            <li><strong>T:</strong> <?= ucfirst($data->received); ?></li>
                        </ul>
                    </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div>
                <a href="<?= (strpos($_SERVER['REQUEST_URI'], '?')==false)?rtrim($_SERVER['REQUEST_URI'],'/').'?download=true':rtrim($_SERVER['REQUEST_URI'],'/').'&download=true'; ?>" target="_blank"><button type="button" class="btn btn-md btn-primary"><span class="glyphicon glyphicon-download-alt"></span> Download</button></a>
            </div>

            <!-- START PAGINATION -->
            <?php 
                if($pages>1){
                    echo pagination($pages);
                }
            ?>
            <!-- END OF PAGINATION -->
            
        </div>

        <div class="app-form modal" id="modal-create-receive-form">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Tambahkan Tanda terima</h3>
                </div>

                <form action="/form/tanda-terima/create" method="POST">
                    <div class="modal-wizard show">
                        <div class="description">
                            <p>Form ini digunakan untuk menambahkan data tanda terima.</p>
                            <dl>
                            <dt>Catatan</dt>
                                <dd>Pinjam: pihak asal(<em>dari</em>) meminjamkan barang kepada yang sebagai tujuan(<em>untuk</em>)</dd>
                                <dd>Serah terima: pihak asal(<em>dari</em>) melakukan serah terima barang ke pihak tujuan(<em>untuk</em>) 
                            </dl>
                        </div>
                        <div class="form-group">
                            <label>Service point</label>
                            <select name="service_point" class="form-control" required>
                                <option value=''>SERVICE POINT</option>
                                <?php foreach($servicePoints as $sp): ?>
                                    <option value=<?= $sp->id ?> ><?= ucfirst($sp->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="datetime-local" name="receive_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Diserahkan</label>
                            <select name="submitted" class="form-control">
                                <option value=''>Diserahkan</option>
                                <?php foreach($partners as $partner): ?>
                                    <option value=<?= $partner->id ?> data-rel=<?= $partner->relationship; ?> ><?= ucfirst($partner->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Diterima</label>
                            <select name="received" class="form-control">
                                <option value=''>Diterima</option>
                                <?php foreach($partners as $partner): ?>
                                    <option value=<?= $partner->id ?> ><?= ucfirst($partner->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Keperluan</label>
                            <select name="requisite" class="form-control">
                                <option value=''>KEPERLUAN</option>
                                <?php foreach($requisites as $requisite): ?>
                                    <option value=<?= $requisite->id ?> ><?= ucfirst($requisite->name); ?></option>

                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-wizard">
                        <div class="description">
                            <p>Pilih produk sesuai barang yang diserahterimakan. Apabila barang tidak terdaftar maka daftarkan terlebih dahulu atau pilih
                            produk 'lain-lain'. <br>Kemudian tuliskan serial number, apabila tidak terdapat keterangan serial number maka beri tanda '-' dan kemudian tuliskan keterangan tambahan yang diperlukan.</p>
                        </div>

                        <div class="row inline-input">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Produk</label>
                                    <select name="product[]" class="form-control">
                                        <option value=''>PRODUK</option>
                                        <?php foreach($products as $product): ?>
                                            <option value=<?= $product->id ?> title=<?= $product->description ?> ><?= ucfirst($product->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Serial number</label>
                                    <div>
                                        <input type="text" name="serial_number[]" class="form-control" placeholder="Serial number" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kondisi</label>
                                    <select name="stock_condition[]" class="form-control" required>
                                        <option value=''>Kondisi</option>
                                        <option value='1'>Baik</option>
                                        <option value='0'>Rusak</option>
                                    </select>
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

                    <button type="button" class="btn btn-danger btn-back" style="display:none;"><span class="glyphicon glyphicon-chevron-left"></span> Kembali</button> 
                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>
                    <span class="wizard-step"></span>                             
                    <button type="button" name="submit" class="btn btn-primary btn-next" style="float:right;">Lanjut <span class="glyphicon glyphicon-chevron-right"></span></button>

                </form>

                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

            </div>
        </div>

    </div>
</main>

<script>
$("document").ready(function(){
    
    $("form").on("change","select[name~='submitted']",function(){
        var submitted=$(this).val();
        var rel=$(this).find("option[value~="+submitted+"]").attr("data-rel");

        //if the owner checked
        if(rel==1){
            $("form").find("[name~='serial_number']").replaceWith("<select name='serial_number' class='form-control'><option value=''>SERIAL NUMBER</option></select>");
        }else{
            $("form").find("[name~='serial_number']").replaceWith("<input type='text' name='serial_number' class='form-control' placeholder='Serial number' required>");

        }
    });

});
</script>

<?php

require base.'base/footer.view.php'

?>