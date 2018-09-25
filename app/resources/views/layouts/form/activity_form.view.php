<?php

$titlePage="Activity report";

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1>Laporan aktivitas</h1>
            <p>Halaman ini menangani data terkait laporan aktivitas</p>
            <button class="btn btn-sm btn-header btn-modal" id="create-ar-form"><span class="glyphicon glyphicon-pencil"></span> Tambahkan data</button>
        </header>

        <div class="sub-header"> 
            <form action="/form/activity-report" method="GET" style="display:inherit">    
                <input type="hidden" name="search" value="true">
                <div class="search" id="customer-based">
                    <div class="form-group">
                        <select name="customer" class="form-control">
                            <option value=''>Customer</option>
                            <?php foreach($customers as $customer): ?>
                                <option value=<?= $customer->id ?>><?= $customer->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="date-based" style="position:relative">
                    <button type="button" class="btn btn-default" id="btn-date-based">TANGGAL AKTIVITAS</button>
                    <div class="form-group" style="position: absolute;left: 50%;margin-top: 5px;transform: translateX(-50%);z-index: 5;display: none;width: 400px;">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="activity_report_start" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="activity_report_end" class="form-control">
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="search">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Cari</button> 
                </div>     
            </form>
        </div>

        <div class="container-fluid info">
            <label><span class="glyphicon glyphicon-floppy-saved"></span> Jumlah data: <?= $sumOfAllData; ?></label>
        </div>

        <div class="main-data" style="background:var(--theme-color-grey)">
            <?php echo count($activityReport)<1?'<p class="text-center">Belum terdapat data tersimpan</p>':''; ?>
            <div class="container-fluid grid-view">  
                <?php foreach($activityReport as $data): ?>
                    <a href="/form/activity-report/detail?ar=<?= $data->id ?>">
                    <div class="cover-grid" style="overflow-y:auto;">
                        <ul>
                            <li><span class="glyphicon glyphicon-calendar"></span> <?= $data->activity_date; ?></li>
                            <li><strong>C:</strong> <?= ucfirst($data->customer); ?></li>
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

        <div class="app-form modal" id="modal-create-ar-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Tambahkan Tanda terima</h3>
                </div>
                <div class="description">
                    <p>Form ini digunakan untuk menambahkan data tanda terima. Form ini digunakan untuk menambahkan data tanda terima.</p>
                    <dl>
                    <dt>Catatan</dt>
                        <dd>Pinjam: pihak asal(<em>dari</em>) meminjamkan barang kepada yang sebagai tujuan(<em>untuk</em>)</dd>
                        <dd>Serah terima: pihak asal(<em>dari</em>) melakukan serah terima barang ke pihak tujuan(<em>untuk</em>) 
                    </dl>
                </div>
                <form action="/form/activity-report/create" method="POST">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="activity_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Customer</label>
                        <select name="customer" class="form-control" required>
                            <option>Pilih customer</option>
                            <?php foreach($customers as $customer): ?>
                                    <option value=<?= $customer->id ?> data-rel=<?= $customer->relationship; ?> ><?= ucfirst($customer->name); ?></option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama project</label>
                        <input type="text" name="project_name" class="form-control" placeholder="Nama project">
                    </div>
                    <div class="form-group">
                        <label>Aktivitas</label>
                        <textarea name="activity" class="form-control" placeholder="Tuliskan secara lengkap aktivitas yang anda lakukan. Termasuk apabila terdapat catatan untuk pengingat" required></textarea>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" id="checkNextActivity"> Ada rencana aktifitas
                    </div>
                    <div id="nextActivityForm" style="display:none">
                        <!--<div class='form-group'>
                            <label>Rencana aktivitas</label>
                            <textarea name='next_activity' class='form-control' placeholder='Tuliskan rencana aktivitas yang akan dilakukan.'></textarea>
                        </div>
                        <div class='form-group'>
                            <label>Tanggal target</label>
                            <input type='date' name='target_completed' class='form-control'>
                        </div>
                        <div class='form-group'>
                            <label>Keterangan</label>
                            <textarea name='remark' class='form-control' placeholder='Tuliskan keterangan terkait rencana aktivitas yang akan dilakukan(apabila perlu/terdapat suatu hal yang perlu dilakukan berikutnya).'></textarea>
                        </div>-->
                    </div>
                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>                    
                    <button type="submit" name="submit" class="btn btn-primary btn-next" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                </form>

                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

            </div>
        </div>
    
    </div>
</main>

<script type="text/javascript">
    $(document).ready(function(){

        $("#checkNextActivity").on("change", function(event){
            var check = $(this).is(":checked");
            if(check){
                $("#nextActivityForm").append("<div class='form-group'><label>Rencana aktivitas</label><textarea name='next_activity' class='form-control' placeholder='Tuliskan rencana aktivitas yang akan dilakukan.'></textarea></div><div class='form-group'><label>Tanggal target</label><input type='date' name='target_completed' class='form-control'></div><div class='form-group'><label>Keterangan</label><textarea name='remark' class='form-control' placeholder='Tuliskan keterangan terkait rencana aktivitas yang akan dilakukan(apabila perlu/terdapat suatu hal yang perlu dilakukan berikutnya).'></textarea></div>");
                $("#nextActivityForm").show();
            }else{
                $("#nextActivityForm").empty();
                $("#nextActivityForm").hide();
            }
        });

        
    })
    
</script>

<?php

require base.'base/footer.view.php';

?>