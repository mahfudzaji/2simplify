<?php

$titlePage="Cuti";

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';
?>

<main>
    <div class="container-fluid">
        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1>Detail Cuti</h1>
            <p>Halaman ini menangani data detail terkait cuti</p>
            <button class="btn btn-sm btn-header btn-modal btn-modal-ajax" id="show-notes"><span class="glyphicon glyphicon-star"></span> Catatan</button>
            <button class="btn btn-sm btn-header btn-modal" id="create-attachment"><span class="glyphicon glyphicon-paperclip"></span> Lampiran</button>
        </header>

        <!-- SHOW NOTES -->
        <div class="app-form modal" id="modal-show-notes">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Catatan</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/notes/create" method="POST">
                        <input type="hidden" name="document_number" value=<?= $_GET['v']; ?>>
                        <input type="hidden" name="document_type" value=4>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea class="form-control" name="notes" placeholder="Tuliskan catatan anda..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                </div>
                <button class="btn btn-danger btn-close" style="clear:both;">Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- ATTACHMENT -->
        <div class="app-form modal" id="modal-create-attachment">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Lampiran</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/attachment" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="document_data" value=<?= $vacationData[0]->ddata; ?>>
                        <div class="form-group">
                            <label>Lampiran</label>
                            <textarea class="form-control" name="description" placeholder="Tuliskan deskripsi lampiran..." required></textarea>
                        </div>
                        <div class="form-group">
                            <input type="file" name="attachment" required>
                        </div>
                        <button type="submit" class="btn btn-primary pull-right">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                </div>
                <button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- UPDATE VACATION FORM -->
        <div class="app-form modal" id="modal-update-vacation-form">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui Data Cuti</h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui data cuti.</p>
                    <dl>
                    <dt>Catatan</dt>
                        <?php foreach($vacationList as $list): ?>
                            <dd>Cuti tahunan: <?= $list->yearly_v; ?> hari</dd>
                            <dd>Cuti melahirkan: <?= $list->birth_v; ?> hari</dd>
                            <dd>Cuti menikah: <?= $list->merried_v; ?> hari</dd>
                            <dd>Cuti menikahkan: <?= $list->merried_off_v; ?> hari</dd>
                            <dd>Cuti khitanan: <?= $list->circumcision_v; ?> hari</dd>
                            <dd>Cuti baptis: <?= $list->baptism_v; ?> hari</dd>
                            <dd>Cuti menemani istri melahirkan/istri mengalami keguguran: <?= $list->accompany_wife; ?> hari</dd>
                            <dd>Cuti karena suami/istri, orangtua/mertua, anak/menantu meninggal dunia: <?= $list->close_family_passed_away; ?> hari</dd>
                            <dd>Cuti karena anggota keluarga dalam satu rumah meninggal dunia: <?= $list->family_passed_away; ?> hari</dd>
                            <dd>Cuti bersama: <?= $list->joint_holiday; ?> hari</dd>
                            <dd>Keterangan tambahan: <?= $list->remark; ?></dd>
                        <?php endforeach; ?>
                    </dl>
                </div>

                <form action="/form/cuti/update" method="post">
                    <input type="hidden" name="v" value=<?= $_GET['v']; ?> >
                    <div class="form-group">
                        <label>Keperluan</label>
                        <select name="requisite" class="form-control" required>
                            <option value=''>PILIH KEPERLUAN</option>
                            <?php foreach($requisites as $requisite): ?>
                                <option value=<?= $requisite->id ?>><?= $requisite->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="finish_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Jumlah hari</label>
                        <input type="number" name="day_used" min=1 step=1 class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="remark" class="form-control" placeholder="Tuliskan keterangan terkait cuti"></textarea>
                    </div>
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
                    <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>                  
                    <button type="submit" name="submit" class="btn btn-primary btn-next" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                </form>
            </div>
        </div>

        <!-- APPROVAL VACATION FORM -->
        <div class="app-form modal" id="modal-approve-vacation-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <a href="/form/cuti/approve?v=<?= $_GET['v']; ?>&a=1"><button type="button" class="btn btn-success btn-sm btn-modal form-control" id="approve-vacation-form"><span class="glyphicon glyphicon-ok"></span> Setuju</button></a>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
            </div>
        </div>

        <!-- REJECT VACATION FORM -->
        <div class="app-form modal" id="modal-reject-vacation-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <a href="/form/cuti/approve?v=<?= $_GET['v']; ?>&a=0"><button type="button" class="btn btn-danger btn-sm btn-modal form-control" id="approve-vacation-form"><span class="glyphicon glyphicon-ok"></span> Ditolak</button></a>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
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
        <div class="main-data" data-number=<?= $_GET['v']; ?> data-document=4>
            <div class="row">
                <div class="col-md-4 table-responsive">
                    <table class="table table-striped">
                        <?php foreach($vacationData as $data): ?>
                            <tr>
                                <th>Nomor</th>
                                <td><?= "SNC-".strtoupper($data->docCode)."-".$data->code."-".$data->id; ?></td>
                            </tr>
                            <tr>
                                <th>Diajukan oleh</th>
                                <td><?= ucfirst($data->submitter); ?></td>
                            </tr>
                            <tr>
                                <th>Department</th>
                                <td><?= ucfirst($data->department); ?></td>
                            </tr>
                            <tr>
                                <th>Diajukan pada</th>
                                <td><?= $data->created_at; ?></td>
                            </tr>
                            <tr>
                                <th>Keperluan</th>
                                <td data-item="requisite" data-item-val=<?= $data->rid; ?> ><?= ucfirst($data->requisite); ?></td>
                            </tr>
                            <tr>
                                <th>Jumlah hari</th>
                                <td data-item="day_used" data-item-val=<?= $data->day_used; ?> ><?= ucfirst($data->day_used); ?> hari</td>
                            </tr>
                            <tr>
                                <th>Tanggal cuti</th>
                                <td>
                                    <table class="table table-striped">
                                        <tbody>
                                            <?php foreach($vacationDate as $date): ?>
                                            <tr><td data-item="vacation_date" data-item-val=<?= $date->vd; ?> style="padding-left:0;"><?= ucfirst($date->vacation_date); ?></td></tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td data-item="remark"><?= ucfirst($data->remark); ?></td>
                            </tr>
                            <tr>
                                <th>Diverifikasi oleh</th>
                                <td data-item="verified_by" data-item-val=<?= $data->vbid; ?> ><?= ucfirst($data->verified_by); ?></td>
                            </tr>
                            <tr>
                                <th>Terverifikasi</th>
                                <td><?= ucfirst($data->verified); ?></td>
                            </tr>
                            <tr>
                                <th>Disetujui oleh</th>
                                <td data-item="approved_by" data-item-val=<?= $data->abid; ?> ><?= ucfirst($data->approved_by); ?></td>
                            </tr>
                            <tr>
                                <th>Telah disetujui</th>
                                <td><?= ucfirst($data->approved); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <?php if($vacationData[0]->aid==0): ?>
                        <?php if($vacationData[0]->name==substr($_SESSION['sim-id'], 3, -3)): ?>
                            <button type="button" class="btn btn-primary btn-sm btn-modal" id="update-vacation-form"><span class="glyphicon glyphicon-pencil"></span> Update data</button>   
                        <?php elseif($vacationData[0]->abid==substr($_SESSION['sim-id'], 3, -3)): ?>
                            <button type="button" class="btn btn-success btn-sm btn-modal" id="approve-vacation-form"><span class="glyphicon glyphicon-ok"></span> Setuju</button>
                            <button type="button" class="btn btn-danger btn-sm btn-modal" id="reject-vacation-form"><span class="glyphicon glyphicon-remove"></span> Ditolak</button>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a target="_blank" href="/print/vacation?v=<?= $_GET['v']; ?>"><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-print"></span> Cetak</button></a>
                    
                </div>
                <!-- SHOW NOTES -->
                <div class="col-md-4 vertical-overflow-space">         
                    <h3>Daftar catatan</h3>
                    <div class="modal-list">
                        <?php if(count($notes)>0): ?>
                            <?php foreach($notes as $note): ?>
                                <div class='note'>
                                    <p><strong><?= $note->created_by; ?></strong><span class='pull-right'><?= $note->created_at; ?></span></p>
                                    <blockquote class='clearfix'><?= $note->notes; ?></blockquote>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Belum terdapat data</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- SHOW ATTACHMENT -->
                <div class="col-md-4 vertical-overflow-space">         
                    <h3>Daftar lampiran</h3>
                    <div id="modal-show-attachment">
                        <div class="modal-list" style="justify-content:flex-start;">
                            <?php if(count($attachments)>0): ?>
                                <?php foreach($attachments as $attachment): ?>
                                    <div class='note attachment active' style="margin-top:0;">
                                        <p><strong><?= $attachment->title; ?></strong>
                                        <span class="pull-right"><?= $attachment->created_at; ?></span></p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <img src=/public/upload/<?= $attachment->upload_file; ?> class='img-responsive img-scroll-item clearfix' style="width:100%; max-width:100%;">
                                            </div>
                                            <div class="col-md-6">
                                                <p class='img-scroll-item-desc'><?= $attachment->description; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Belum terdapat data</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
<script>
$(document).ready(function(){

    /* UPDATE VACATION */
    var requisite = $(".main-data").find("[data-item~='requisite']").attr("data-item-val");
    var dayUsed = $(".main-data").find("[data-item~='day_used']").attr("data-item-val");
    var startDate = $(".main-data").find("[data-item~='start_date']").attr("data-item-val");
    var finishDate = $(".main-data").find("[data-item~='finish_date']").attr("data-item-val");
    var verifiedBy = $(".main-data").find("[data-item~='verified_by']").attr("data-item-val");
    var approvedBy = $(".main-data").find("[data-item~='approved_by']").attr("data-item-val");
    var remark = $(".main-data").find("[data-item~='remark']").html();

    $("#modal-update-vacation-form").find("select[name~='requisite']").find("option[value~='"+requisite+"']").attr("selected", true);
    $("#modal-update-vacation-form").find("input[name~='day_used']").val(dayUsed);
    $("#modal-update-vacation-form").find("input[name~='start_date']").val(startDate);
    $("#modal-update-vacation-form").find("input[name~='finish_date']").val(finishDate);
    $("#modal-update-vacation-form").find("select[name~='verified_by']").find("option[value~='"+verifiedBy+"']").attr("selected", true);  
    $("#modal-update-vacation-form").find("select[name~='approved_by']").find("option[value~='"+approvedBy+"']").attr("selected", true); 
    $("#modal-update-vacation-form").find("textarea[name~='remark']").val(remark);
    
});
</script>
<?php

require base.'base/footer.view.php'

?>