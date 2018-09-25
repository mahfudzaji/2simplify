<?php

$titlePage="Reimburse";

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

$costTotal=0;

?>

<main>
    <div class="container-fluid">
        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1>Detail <?= $titlePage; ?></h1>
            <p>Halaman ini menangani data detail terkait <?= $titlePage; ?></p>
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
                        <input type="hidden" name="document_number" value=<?= $_GET['r']; ?>>
                        <input type="hidden" name="document_type" value=3>
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

        <!-- ATTACHMENT -->
        <div class="app-form modal" id="modal-create-attachment">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Lampiran</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/attachment" method="post">
                        <input type="hidden" name="document_data" value=<?= $reimburseData[0]->ddata; ?>>
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
                    <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

                </div>
                <button class="btn btn-danger btn-close clear" >Tutup</button>
            </div>
        </div>

        <!-- UPDATE REIMBURSE FORM -->
        <div class="app-form modal" id="modal-update-reimburse-form">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui Data Reimburse</h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui data reimburse.</p>
                    <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan</p>
                </div>
                <form action="/form/reimburse/update" method="post">
                    <input type="hidden" name="r-item" value="">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="receipt_date" class="form-control" required>
                    </div>
                    
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
                        <label>Biaya</label>
                        <input type="number" name="cost" min=0 class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="remark" class="form-control" placeholder="Tuliskan keterangan terkait cuti"></textarea>
                    </div>
                    

                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>

                    <div class="nav-right">
                        <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- REMOVE REIMBURSE FORM -->
        <div class="app-form modal" id="modal-remove-reimburse-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/reimburse/remove" method="post">
                        <input type="hidden" name="r-item" value="">
                        <button type="submit" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Hapus data</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
            </div>
        </div>

        <!-- APPROVAL REIMBURSE FORM -->
        <div class="app-form modal" id="modal-approve-reimburse-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/reimburse/approve" method="post">
                        <input type="hidden" name="r-item" value="">
                        <input type="hidden" name="a" value="1">
                        <button type="submit" class="btn btn-success btn-sm form-control"><span class="glyphicon glyphicon-ok"></span> Setuju</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
            </div>
        </div>

        <!-- REJECT REIMBURSE FORM -->
        <div class="app-form modal" id="modal-reject-reimburse-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/reimburse/approve" method="post">
                        <input type="hidden" name="r-item" value="">
                        <input type="hidden" name="a" value="0">
                        <button type="submit" name="reject" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Ditolak</button>
                    </form>
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
        <div class="main-data" data-number=<?= $_GET['r']; ?> data-document=3>
            <div class="row">
                <div class="col-md-8">
                    <?php foreach($reimburseData as $data2): ?>
                        <div class="col-md-6" style="padding-left:0">
                            <h3><?= $data2->submitter; ?></h3>
                            <h4>No: <?= "SNC-".strtoupper($data2->docCode."-".$data2->code)."-".$data2->id;?></h4>
                            <h4><?= $data2->paid; ?></h4>
                        </div>
                        <div class="col-md-6 text-right" style="padding-right:0">
                            <small>Created: <?= $data2->created_at; ?></small><br>
                            <small>Last update: <?= $data2->updated_at; ?></small>
                        </div>
                        
                    <?php endforeach; ?>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keperluan</th>
                                <th>Biaya</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        
                        <?php foreach($reimburseDetailData as $data1): $costTotal+=$data1->cost; ?>
                            <tr data-item=<?= $data1->id; ?>>
                                <td data-item="receipt_date" data-item-val=<?= $data1->rdd; ?>><?= $data1->receipt_date; ?></td>
                                <td data-item="requisite" data-item-val=<?= $data1->rid; ?>><?= $data1->requisite; ?></td>
                                <td data-item="cost"><?= $data1->cost; ?></td>
                                <td data-item="remark"><?= $data1->remark; ?></td>
                                <td><?= $data1->approved; ?></td>
                                
                                <?php if($data1->aid==0): ?>
                                    <?php if($reimburseData[0]->name==substr($_SESSION['sim-id'], 3, -3)): ?>
                                        <!-- Single button by Bootstrap -->
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="#" class="btn-modal btn-action" data-id="update-reimburse-form"><span class="glyphicon glyphicon-pencil"></span> Update</a></li>
                                                    <li><a href="#" class="btn-modal btn-action" data-id="remove-reimburse-form"><span class="glyphicon glyphicon-remove"></span> Remove</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <!-- <td class="text-center"><button type="button" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-pencil"></span> Update</button></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-remove"></span> Remove</button></td>  -->   
                                    <?php elseif($reimburseData[0]->abid==substr($_SESSION['sim-id'], 3, -3)): ?>
                                        <!-- Single button by Bootstrap -->
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="#" class="btn-modal btn-action" data-id="approve-reimburse-form"><span class="glyphicon glyphicon-ok"></span> Setuju</a></li>
                                                    <li><a href="#" class="btn-modal btn-action" data-id="reject-reimburse-form"><span class="glyphicon glyphicon-remove"></span> Ditolak</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <!-- td class="text-center"><button type="button" class="btn btn-success btn-sm btn-modal" id="approve-vacation-form"><span class="glyphicon glyphicon-ok"></span> Setuju</button></td>
                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-modal" id="reject-vacation-form"><span class="glyphicon glyphicon-remove"></span> Ditolak</button></td> -->
                                    <?php else: ?>
                                        <td class="text-center">---</td>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <td class="text-center">---</td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <?php foreach($reimburseData as $data2): ?>
                        <div style="margin-bottom:20px;">
                            <h4>Total: <?= $costTotal; ?></h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="label label-default">Verified: <?= $data2->verified_by; ?></small>
                                    <small><?= $data2->verified_at!=null?$data2->verified_at."<span class='glyphicon glyphicon-ok'></span>":""; ?></small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small class="label label-default">Approved: <?= $data2->approved_by; ?></small>
                                    <small><?= $data2->approved_at!=null?$data2->approved_at."<span class='glyphicon glyphicon-ok'></span>":""; ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <a target="_blank" href="/print/reimburse?r=<?= $_GET['r']; ?>"><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-print"></span> Cetak</button></a>
                    
                    <?php
                        if($reimburseData[0]->name==substr($_SESSION['sim-id'], 3, -3)):
                            //pid=paid id 
                            if($data2->pid==0){
                                $attr="disabled";
                                $remark="Konfirmasi";
                            }elseif($data2->pid==1){
                                $attr="";
                                $remark="Konfirmasi";
                            }else{
                                $attr="disabled";
                                $remark="Terkonfirmasi";
                            } 
                    ?>

                    <a target="_blank" href="/print/vacation?v=<?= $_GET['v']; ?>"><button type="button" class="btn btn-success btn-sm" <?= $attr; ?>><span class="glyphicon glyphicon-ok"></span> <?= $remark; ?></button></a>

                    <?php endif; ?>
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

    /* UPDATE REIMBURSE */
    $(".btn-action").on("click", function(){
        var dataId= $(this).attr("data-id");

        var reimburseItem = $(this).parent().closest("tr").attr("data-item");

        if(dataId=='update-reimburse-form'){        
            var receiptDate = $(this).parent().closest("tr").find("[data-item~='receipt_date']").attr("data-item-val");
            var requisite = $(this).parent().closest("tr").find("[data-item~='requisite']").attr("data-item-val");
            var cost = $(this).parent().closest("tr").find("[data-item~='cost']").html();
            var remark = $(this).parent().closest("tr").find("[data-item~='remark']").html();

            $("#modal-update-reimburse-form").find("select[name~='requisite']").find("option[value~='"+requisite+"']").attr("selected", true);
            $("#modal-update-reimburse-form").find("input[name~='cost']").val(cost);
            $("#modal-update-reimburse-form").find("input[name~='receipt_date']").val(receiptDate); 
            $("#modal-update-reimburse-form").find("textarea[name~='remark']").val(remark);
            $("#modal-update-reimburse-form").find("input[name~='r-item']").val(reimburseItem);
        }else{
            $("#modal-remove-reimburse-form, #modal-approve-reimburse-form, #modal-reject-reimburse-form").find("input[name~='r-item']").val(reimburseItem);
        }
        
    });

});
</script>
<?php

require base.'base/footer.view.php'

?>