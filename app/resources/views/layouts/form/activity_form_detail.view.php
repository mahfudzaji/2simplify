<?php

$titlePage=returnMessage()['activityReport']['title'];

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';
?>

<main>
    <div class="container-fluid">
        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1>Detail <?= $titlePage; ?></h1>
            <p>Halaman ini menangani data terkait <?= $titlePage; ?></p>
            <button class="btn btn-sm btn-header btn-modal-ajax" id="show-notes"><span class="glyphicon glyphicon-star"></span> Catatan</button>
            <button class="btn btn-sm btn-header btn-modal-ajax" id="create-attachment"><span class="glyphicon glyphicon-paperclip"></span> Lampiran</button>
        </header>
        
        <!-- SHOW NOTES -->
        <div class="app-form modal" id="modal-show-notes">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Catatan</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/notes/create" method="POST">
                        <input type="hidden" name="document_number" value=<?= $_GET['ar']; ?>>
                        <input type="hidden" name="document_type" value=2>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea class="form-control" name="notes" placeholder="Tuliskan catatan anda..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                    <div style="clear:both">
                        <label>Daftar catatan</label>
                        
                        <div class="modal-list">

                        </div>
                    </div>
                </div>
                <br> 
                <button class="btn btn-danger btn-close" >Tutup</button>
            </div>
        </div>

        <!-- UPDATE RECEIVE FORM -->
        <div class="app-form modal" id="modal-update-ar-form">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui Data <?= $titlePage; ?></h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/activity-report/update" method="POST">
                        <input type="hidden" name="ar" value=<?= $_GET['ar']; ?>>
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
                        <div class='form-group'>
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
                        </div>
                        <button type="button" class="btn btn-danger btn-close" >Tutup</button>                          
                        <button type="button" name="submit" class="btn btn-primary btn-next pull-right">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ATTACHMENT -->
        <div class="app-form modal" id="modal-create-attachment">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Lampiran</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/attachment" method="post">
                        <input type="hidden" name="document_data" value=<?= $arData[0]->ddata; ?>>
                        <div class="form-group">
                            <label>Lampiran</label>
                            <textarea class="form-control" name="description" placeholder="Tuliskan deskripsi lampiran..." required></textarea>
                        </div>
                        <div class="form-group">
                            <select name="attachment" class="form-control select-ajax" required>
                                <option value=''>PILIH LAMPIRAN</option>
                                <?php foreach($uploadFiles as $uploadFile): ?>
                                    <option value=<?= $uploadFile->id; ?>><?= $uploadFile->title; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="image-appear"></div>
                        <button type="submit" class="btn btn-primary pull-right">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                    <div style="clear:both">
                        <label>Daftar lampiran</label>
                        
                        <div class="modal-list"></div>
                    </div>
                </div>
                <br> 
                <button class="btn btn-danger btn-close" >Tutup</button>
            </div>
        </div>

        <!-- IMAGE SCROLL -->
        <div class="modal image-scroll-modal scroll-modal-horizontal">          
            <span class="btn-close glyphicon glyphicon-remove"></span>
            <span class="modal-nav modal-nav-right glyphicon glyphicon-chevron-right"></span>
            <span class="modal-nav modal-nav-left glyphicon glyphicon-chevron-left"></span>
            <img class="modal-image image-responsive" src="">
            <p class="description"></p>            
        </div>

        <!-- MAIN -->
        <div class="main-data" data-number=<?= $_GET['ar']; ?> data-document=2>
            <div class="row">
                <div class="col-md-8 col-md-offset-2 table-responsive">
                    <?php foreach($arData as $data): ?>
                        <div class="row" style="margin:10px 0; padding:5px; border:1px solid var(--theme-color3); border-radius:4px;background:var(--theme-color5);font-size:1.5em;color:var(--theme-color4);">
                            <div class="col-md-4">
                                <label>Nomor: <?= strtoupper($data->docCode)."-".strtoupper($data->code)."-".$data->id; ?></label>
                            </div>
                            <div class="col-md-4 col-md-offset-4 text-right">
                                <label data-item="activity_date" data-item-val=<?= $data->acd; ?>><?= ucfirst($data->activity_date); ?></label>
                            </div>
                        </div>
                        <div>
                            <label>Customer</label>
                            <blockquote data-item="customer" data-item-val=<?= $data->idcustomer; ?>><?= ucfirst($data->customer)?></blockquote>
                        </div>
                        <div>
                            <label>Project</label>
                            <blockquote data-item="project_name" data-item=<?= $data->project_name; ?>><?= ucfirst($data->project_name)?></blockquote>
                        </div>
                        <div>
                            <label>Aktifitas</label>
                            <blockquote data-item="activity"><?= ucfirst($data->activity); ?></blockquote>
                        </div>
                        <div>
                            <label>Rencana aktivitas</label>
                            <blockquote data-item="next_activity"><?= ucfirst($data->next_activity); ?></blockquote>
                        </div>
                        <div>
                            <label>Target selesai</label>
                            <blockquote data-item="target_completed" data-item-val=<?= $data->tcd; ?>><?= ucfirst($data->target_completed); ?></blockquote>
                        </div>
                        <div>
                            <label>Keterangan tambahan</label>
                            <blockquote data-item="remark"><?= ucfirst($data->remark); ?></blockquote>
                        </div>
                        <div>
                            <label>Status</label>
                            <blockquote><?= ucfirst($data->status); ?></blockquote>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Dibuat oleh</label>
                                <blockquote><?= ucfirst($data->created_by); ?></blockquote>
                            </div>
                            <div class="col-md-3">
                                <label>Dibuat pada</label>
                                <blockquote><?= ucfirst($data->created_at); ?></blockquote>
                            </div>
                            <div class="col-md-3">
                                <label>Diperbaharui oleh</label>
                                <blockquote><?= ucfirst($data->updated_by); ?></blockquote>
                            </div>
                            <div class="col-md-3">
                                <label>Diperbaharui pada</label>
                                <blockquote><?= ucfirst($data->updated_at); ?></blockquote>
                            </div>
                        </div>
                    <?php endforeach; ?>
                
                    <button type="button" class="btn btn-primary btn-sm btn-modal" id="update-ar-form"><span class="glyphicon glyphicon-pencil"></span> Update data</button>   
                    <a target="_blank" href="/print/activity-report?ar=<?= $_GET['ar']; ?>"><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-print"></span> Cetak</button></a>
                    <form action="/form/activity-report/close" method="POST" class="pull-right">
                        <input type="hidden" name="ar" value=<?= $_GET['ar']; ?>>
                        <button type="submit" id="close-activity" class="btn btn-success btn-sm pull-right confirm">Already solved</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</main>
<script>

$(document).ready(function(){

    $(".confirm").on("click", function(){

        var c='';
        var type=$(this).attr("id");

        if(type=='close-activity'){
            c=confirm("Aktifitas sudah selesai?");
        }

        if(c){
            return true;

        }else{
            return false;
        }

    });

    /* UPDATE ACTIVITY */
    var projectName=$(".main-data").find("[data-item~='project_name']").html();
    var activity = $(".main-data").find("[data-item~='activity']").html();
    var nextActivity = $(".main-data").find("[data-item~='next_activity']").html();
    var customer = $(".main-data").find("[data-item~='customer']").attr("data-item-val");
    var activityDate = $(".main-data").find("[data-item~='activity_date']").attr("data-item-val");
    var targetData = $(".main-data").find("[data-item~='target_completed']").attr("data-item-val");

    $("#modal-update-ar-form").find("input[name~='project_name']").val(projectName);
    $("#modal-update-ar-form").find("textarea[name~='activity']").val(activity);
    $("#modal-update-ar-form").find("textarea[name~='next_activity']").val(nextActivity);    
    $("#modal-update-ar-form").find("select[name~='customer']").find("option[value~='"+customer+"']").attr("selected", true);
    $("#modal-update-ar-form").find("input[name~='activity_date']").val(activityDate);
    $("#modal-update-ar-form").find("input[name~='target_completed']").val(targetData);

    /* END */

});
</script>
<?php

require base.'base/footer.view.php'

?>