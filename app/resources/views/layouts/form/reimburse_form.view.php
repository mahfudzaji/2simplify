<?php

$titlePage="Reimburse";

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>

        <header id="main-header">
            <h1>Reimburse</h1>
            <p>Halaman ini menangani data terkait reimburse</p>
            <button class="btn btn-sm btn-header btn-modal" id="create-reimburse-form"><span class="glyphicon glyphicon-pencil"></span> Tambahkan data</button>
        </header>

        <div class="sub-header"> 
            <form action="/form/reimburse" method="GET" style="display:inherit">    
                <input type="hidden" name="search" value="true">
                <div class="search" id="submitter-based">
                    <div class="form-group">
                        <select name="submitter" class="form-control">
                            <option value=''>Karyawan</option>
                            <?php foreach($submitters as $person): ?>
                                <option value=<?= $person->id ?>><?= ucfirst($person->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="requisite-based">
                    <div class="form-group">
                        <select name="requisite" class="form-control">
                            <option value=''>Keperluan</option>
                            <?php foreach($requisites as $req): ?>
                                <option value=<?= $req->id ?>><?= ucfirst($req->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="date-based" style="position:relative">
                    <button type="button" class="btn btn-default" id="btn-date-based">TANGGAL PENGAJUAN</button>
                    <div class="form-group" style="position: absolute;left: 50%;margin-top: 5px;transform: translateX(-50%);z-index: 5;display: none;width: 400px;">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="created_at_start" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="created_at_end" class="form-control">
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
            <?php echo count($reimburseData)<1?'<div class="text-center">Belum terdapat data tersimpan</div>':''; ?>
            <div class="container-fluid grid-view">
                <?php foreach($reimburseData as $data): ?>
                    <a href="/form/reimburse/detail?r=<?= $data->id ?>">
                    <div class="cover-grid" style="overflow-y:auto;">
                        <ul>
                            <li><span class="glyphicon glyphicon-user"></span> <?= $data->submitter; ?></li>
                            <li><span class="glyphicon glyphicon-calendar"></span> <?= ucfirst($data->created_at); ?></li>
                            <li><span class="glyphicon glyphicon-send"></span> <?= ucfirst($data->send); ?></li>
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

    </div>

    <div class="app-form modal modal-wizard" id="modal-create-reimburse-form">         
        <div class="modal-content" style="width:80%;">
            <div class="modal-header">
                <h3>Tambahkan Data Reimburse</h3>
            </div>

            <div class="description">
                <p>Form ini digunakan untuk menambahkan data reimburse.</p>
                <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan<br>
                Setelah form reimburse di kirim, anda tidak dapat menambahkan item reimburse lagi, untuk menambahkan item tersebut anda harus membuat form reimburse baru</p>
            </div>
            
            <div class="row">
                <div class="col-md-3"><label>Tanggal</label></div>
                <div class="col-md-3"><label>Keperluan</label></div>
                <div class="col-md-3"><label>Biaya</label></div>
                <div class="col-md-3"><label>Keterangan</label></div>
            </div>

            <form action="/form/reimburse/create" method="post">
                <div class="row inline-input">
                    <div class="col-md-3">
                        <div class="form-group vacation-date">
                            <input type="date" name="receipt_date[]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="requisite[]" class="form-control" required>
                            <option value=''>PILIH KEPERLUAN</option>
                            <?php foreach($requisites as $requisite): ?>
                                <option value=<?= $requisite->id ?>><?= $requisite->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="cost[]" min=0 class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="remark[]" class="form-control" placeholder="Tuliskan keterangan terkait cuti">
                    </div>
                </div>
                
                <p><button type="button" class="btn btn-default btn-add-input-form"><span class="glyphicon glyphicon-plus"></span> Tambah</button></p>

                <div class="form-group">
                    <label>Persetujuan</label>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Diverifikasi oleh</label>
                            <select name="verified_by" class="form-control" required>
                                <option value="">Pilih</option>
                                <?php foreach($approvalPerson as $person): ?>
                                    <option value=<?= $person->user_id; ?>><?= $person->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Disetujui oleh</label>
                            <select name="approved_by" class="form-control" required>
                                <option value="">Pilih</option>
                                <?php foreach($approvalPerson as $person): ?>
                                    <option value=<?= $person->user_id; ?>><?= $person->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-close" >Tutup</button>
                <div class="nav-right">
                    <button type="submit" name="save" class="btn btn-default btn-next">Simpan <span class="glyphicon glyphicon-floppy-disk"></span></button>   
                    <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                </div>
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
        
        /* var daysUsed= $("form").find(".vacation-date").length;

        $(".btn-add-input-form").on("click", function(){
            daysUsed=daysUsed+1;
            $("#count-days").html(daysUsed);
        });
        
        $("form").on("click",".btn-float", function(){
            console.log(daysUsed);
            daysUsed=daysUsed-1;
            $("#count-days").html(daysUsed);
            $(this).parent().closest(".vacation-date").remove();
        }); */
        
        $("select[name~='requisite']").on("change", function(){
            var req=$(this).val();
            $(".vacation-date").empty();
            $(".vacation-date:not(:first)").remove();
            
            /* 5= CUTI MELAHIRKAN */
            if(req=="5"){
                $(".btn-add-input-form").hide();
                $(".vacation-date").html("<label>Tanggal</label><div class='row'><div class='col-md-6'><input type='date' name='vacation_date[]' class='form-control' required></div><div class='col-md-6'><input type='date' name='vacation_date[]' class='form-control' required></div></div>")
            }else{
                $(".btn-add-input-form").show();
                $(".vacation-date").html("<label>Tanggal</label><input type='date' name='vacation_date[]' class='form-control' required>");
            }
        })
    })
</script>