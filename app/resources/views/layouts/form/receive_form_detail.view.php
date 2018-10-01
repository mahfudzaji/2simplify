<?php

$titlePage="Tanda terima";

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';
?>

<main>
    <div class="container-fluid">
        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1>Detail Tanda terima</h1>
            <p>Halaman ini menangani data terkait tanda terima</p>
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
                        <input type="hidden" name="document_number" value=<?= $_GET['r']; ?>>
                        <input type="hidden" name="document_type" value=1>
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
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- UPDATE RECEIVE FORM -->
        <div class="app-form modal" id="modal-update-receive-form">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui Data Tanda terima</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/tanda-terima/update" method="POST">
                        <input type="hidden" name="document_number" value=<?= $_GET['r']; ?>>
                        <div class="description">
                            <p>Form ini digunakan untuk memperbaharui data tanda terima.</p>
                            <dl>
                            <dt>Catatan</dt>
                                <dd>Pinjam: pihak asal(<em>dari</em>) meminjamkan barang kepada yang sebagai tujuan(<em>untuk</em>)</dd>
                                <dd>Serah terima: pihak asal(<em>dari</em>) melakukan serah terima barang ke pihak tujuan(<em>untuk</em>) 
                            </dl>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="datetime-local" name="receive_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Service point</label>
                            <select name="service_point" class="form-control">
                                <option value=''>SERVICE POINT</option>
                                <?php foreach($servicePoints as $sp): ?>
                                    <option value=<?= $sp->id ?> data-rel=<?= $sp->name; ?> ><?= ucfirst($sp->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Diserahkan</label>
                            <select name="submitted" class="form-control">
                                <option value=''>DARI</option>
                                <?php foreach($partners as $partner): ?>
                                    <option value=<?= $partner->id ?> data-rel=<?= $partner->relationship; ?> ><?= ucfirst($partner->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Diterima</label>
                            <select name="received" class="form-control">
                                <option value=''>UNTUK</option>
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
                        <div class="form-group">
                            <label>Keterangan tambahan</label>
                            <textarea name="remark" class="form-control" placeholder="Keterangan tambahan"></textarea>
                        </div>
                        

                        <button type="button" class="btn btn-danger btn-close" >Tutup</button>                          
                        <button type="button" name="submit" class="btn btn-primary btn-next pull-right">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                    <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
                </div>
            </div>
        </div>

        <!-- UPDATE STOCK ITEM -->
        <div class="app-form modal" id="modal-update-stock-item">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui data tanda terima</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/p-a/asset/update" method="POST">
                        <input type="hidden" name="id" value=''>
                        <div class="form-group">
                            <label>Serial number</label>
                            <input type="text" name="serial_number" class="form-control" placeholder="serial number" required>
                        </div>
                        <div class="form-group">
                            <label>Lokasi</label>
                            <select name="service_point" class="form-control" required>
                                <option value=''>SERVICE POINT</option>
                                <?php foreach($servicePoints as $sp): ?>
                                    <option value=<?= $sp->id ?> ><?= ucfirst($sp->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kondisi</label>
                            <select name="asset_condition" class="form-control" required>
                                <option value=''>Kondisi</option>
                                <option value='1'>Baik</option>
                                <option value='0'>Rusak</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-danger btn-close" >Tutup</button>
                        <button type="submit" class="btn btn-primary pull-right">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                    <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
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
                        <input type="hidden" name="document_data" value=<?= $receiveData[0]->ddata; ?>>
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
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
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
        <div class="main-data" data-number=<?= $_GET['r']; ?> data-document=1>
            <div class="row">
                <div class="col-md-4 table-responsive">
                    <table class="table table-hover">
                        <?php foreach($receiveData as $data): ?>
                            <tr>
                                <th>Nomor</th>
                                <td><?= $data->code."-TT-".$data->id; ?></td>
                            </tr>
                            <tr>
                                <th>Diserahkan</th>
                                <td data-item="submitted" data-item-val="<?= $data->ids; ?>"><?= ucfirst($data->submitted); ?></td>
                            </tr>
                            <tr>
                                <th>Diterima</th>
                                <td data-item="received" data-item-val="<?= $data->idr; ?>"><?= ucfirst($data->received); ?></td>
                            </tr>
                            <tr>
                                <th>Service point</th>
                                <td data-item="service_point" data-item-val="<?= $data->idsp; ?>"><?= ucfirst($data->service_point); ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal diterima</th>
                                <td data-item="receive_date" data-item-val="<?= $data->rd; ?>"><?= ucfirst($data->receive_date); ?></td>
                            </tr>
                            <tr>
                                <th>Keperluan</th>
                                <td data-item="requisite" data-item-val="<?= $data->idreq; ?>"><?= ucfirst($data->requisite); ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><?= ucfirst($data->status); ?></td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td data-item="remark"><?= ucfirst($data->remark); ?></td>
                            </tr>
                            <tr>
                                <th>Dibuat oleh</th>
                                <td><?= ucfirst($data->created_by); ?></td>
                            </tr>
                            <tr>
                                <th>Dibuat pada</th>
                                <td><?= ucfirst($data->created_at); ?></td>
                            </tr>
                            <tr>
                                <th>Diperbaharui oleh</th>
                                <td><?= ucfirst($data->updated_by); ?></td>
                            </tr>
                            <tr>
                                <th>Diperbaharui pada</th>
                                <td><?= ucfirst($data->updated_at); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <button type="button" class="btn btn-primary btn-sm btn-modal" id="update-receive-form"><span class="glyphicon glyphicon-pencil"></span> Update data</button>   
                    <a target="_blank" href="/print/receive-form?document_number=<?= $_GET['r']; ?>&document=1"><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-print"></span> Cetak</button></a>
                    
                </div>
                <div class="col-md-8">
                    <?php foreach($detailNumber as $detail): ?>
                        <div class="big-list" id=<?= $detail->id; ?>>
                            <div class="row big-list-main">
                                <div class="col-md-3">
                                    <img src=/public/upload/<?= $detail->upload_file; ?> alt=<?= $detail->title; ?> >
                                </div>
                                <div class="col-md-6">
                                    <p><strong><?= $detail->name; ?></strong></p>
                                    <p>Deskripsi: <?= $detail->description; ?> </p>
                                </div>
                                <div class="col-md-3">
                                    <p class="text-right">Jumlah: <?= $detail->jumlah; ?></p>
                                </div>
                            </div>
                            <div class="table-responsive big-list-child">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Serial number</th>
                                            <th>Lokasi</th>
                                            <th>Kondisi</th>
                                            <th>Status</th>
                                            <th colspan=2 class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</main>
<script>

$(document).ready(function(){

    /* SHOW ASSET ITEM */
    $(".big-list").on("click", ".big-list-main", function(event){
        var product=$(this).parent().attr("id");
        var documentNumber=$(this).closest(".main-data").attr("data-number");

        //var id=event.currentTarget.id;
        
        var bigListChild=$(".big-list#"+product).find(".big-list-child");

        var countData=bigListChild.find("tbody").find("tr").length;

        if(countData){
            bigListChild.slideToggle();
        }else{    
            $.get("/product/asset/detail", {product:product, document_number:documentNumber}, function(data, status){
                
                var assets=JSON.parse(data);
                bigListChild.find("tbody").empty();
                
                for(var i=0; i<assets.length; i++){
                    var toAppend="<tr data-item="+assets[i].id+"><td data-item='serial_number'>"+assets[i].serial_number+"</td><td data-item='service_point' data-item-val="+assets[i].id_sp+">"+assets[i].service_point+"</td><td data-item='asset_condition' data-item-val="+assets[i].id_ac+">"+assets[i].asset_condition+"</td><td data-item='status'>"+assets[i].status+"</td><td class='text-center'><span class='span-action span-action-update glyphicon glyphicon-pencil' style='color:#034f84;'></span></td><td class='text-center'><form action='/p-a/asset/remove' method='post'><input type='hidden' name='asset' value="+assets[i].id+"><button type='submit' class='btn btn-link btn-remove confirm' id='deletion' style='padding:0;color:#ff0000'><span class='span-action span-action-remove glyphicon glyphicon-remove'></span></button></form></td></tr>";
                    bigListChild.find("tbody").append(toAppend);
                }

                bigListChild.slideToggle();        
            });
        }
        
    });

    /* UPDATE ASSET ITEM */
    $(".big-list").on("click", "span.span-action-update", function(){
        var assetItem=$(this).closest("tr").attr("data-item");
        var tr=$(this).closest("tr");
        
        var serialNumber=tr.find("td[data-item~='serial_number']").html();
        var servicePoint=tr.find("td[data-item~='service_point']").attr("data-item-val");
        var assetCondition=tr.find("td[data-item~='asset_condition']").attr("data-item-val");
        var notes=tr.find("td[data-item~='notes']").html();

        $("#modal-update-stock-item").find("input[name~='id']").val(assetItem);

        $("#modal-update-stock-item").find("input[name~='serial_number']").val(serialNumber);

        $("#modal-update-stock-item").find("select[name~='service_point']").find("option[value~='"+servicePoint+"']").attr("selected", true);

        $("#modal-update-stock-item").find("select[name~='asset_condition']").find("option[value~='"+assetCondition+"']").attr("selected", true);
        
        $("#modal-update-stock-item").find("textarea[name~='notes']").val(notes);

        $("#modal-update-stock-item").show();

    });

    /* UPDATE RECEIVE FORM */
    $("#update-receive-form").on("click", function(){
        var table=$(this).parent().find("table");

        var receiveDate = table.find("td[data-item~='receive_date']").attr("data-item-val");
        var servicePoint = table.find("td[data-item~='service_point']").attr("data-item-val");
        var requisite = table.find("td[data-item~='requisite']").attr("data-item-val");
        var submitted = table.find("td[data-item~='submitted']").attr("data-item-val");
        var received = table.find("td[data-item~='received']").attr("data-item-val");
        var remark = table.find("td[data-item~='remark']").html();
   
        $("#modal-update-receive-form").find("input[name~='receive_date']").val(receiveDate);
        $("#modal-update-receive-form").find("select[name~='service_point']").find("option[value~='"+servicePoint+"']").attr("selected", true);
        $("#modal-update-receive-form").find("select[name~='submitted']").find("option[value~='"+submitted+"']").attr("selected", true);
        $("#modal-update-receive-form").find("select[name~='received']").find("option[value~='"+received+"']").attr("selected", true);
        $("#modal-update-receive-form").find("select[name~='requisite']").find("option[value~='"+requisite+"']").attr("selected", true);
        $("#modal-update-receive-form").find("textarea[name~='remark']").val(remark);
    });

    $(".big-list").on("click", ".btn-confirm-no", function(event){
        event.currentTarget.parentElement.style.display='none';
    });

    $("main").on("click", ".confirm", function(event){
        
        var c='';
        var type=$(this).attr("id");

        if(type=="deletion"){
            c=confirm("Yakin? Semua data terkait asset ini akan dihapus");
        }

        if(c){
            return true;
        }
        return false; 

    });

});
</script>
<?php

require base.'base/footer.view.php'

?>