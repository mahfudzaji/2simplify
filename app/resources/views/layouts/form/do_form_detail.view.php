<?php

$titlePage="Delivery order";

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

$printBtn = false;
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
                        <input type="hidden" name="document_number" value=<?= $_GET['do']; ?>>
                        <input type="hidden" name="document_type" value=5>
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

        <!-- ATTACHMENT -->
        <div class="app-form modal" id="modal-create-attachment">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Lampiran</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/attachment" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="document_data" value=<?= $doData[0]->ddata; ?>>
                        <div class="form-group">
                            <label>Lampiran</label>
                            <textarea class="form-control" name="description" placeholder="Tuliskan deskripsi lampiran..." required></textarea>
                        </div>
                        <input type="file" name="attachment"><br>
                        <button type="submit" class="btn btn-primary pull-right">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                    <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

                </div>
                <button class="btn btn-danger btn-close clear" >Tutup</button>
            </div>
        </div>

        <!-- UPDATE DO FORM -->
        <div class="app-form modal" id="modal-update-do-form">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui Data <?= $titlePage; ?></h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui data <?= $titlePage; ?>.</p>
                    <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan</p>
                </div>
                <form action="/form/do/update" method="post">
                    <input type="hidden" name="do_form" value=<?= $_GET['do']; ?>>
                    
                    <div class="form-group">
                        <label>DO number</label>
                        <input type="text" class="form-control" name="do_number" placeholder="DO number" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal DO</label>
                        <input type="date" name="do_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Diserahkan oleh</label>
                        <input type="text" name="delivered_by" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Diterima oleh</label>
                        <input type="text" name="received_by" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan / Term & condition</label>
                        <!-- <textarea name="remark" class="form-control" placeholder="Keterangan tambahan"></textarea> -->
                        <div id="remark"></div>
                    </div>

                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>

                    <div class="nav-right">
                        <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </div>

                </form>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>   

        <!-- REMOVE DO FORM -->
        <div class="app-form modal" id="modal-remove-do">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/do/remove" method="post">
                        <input type="hidden" name="do" value=<?= $_GET['do']; ?>>
                        <button type="submit" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Hapus data</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- APPROVAL PO FORM -->
        <div class="app-form modal" id="modal-approve-do-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/do/approve" method="post">
                        <input type="hidden" name="do-form" value=<?= $_GET['do']; ?>>
                        <input type="hidden" name="approval" value="1">
                        <button type="submit" class="btn btn-success btn-sm form-control"><span class="glyphicon glyphicon-ok"></span> Setuju</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
            </div>
        </div>

        <!-- REJECT PO FORM -->
        <div class="app-form modal" id="modal-reject-do-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/do/approve" method="post">
                        <input type="hidden" name="do-form" value=<?= $_GET['do']; ?>>
                        <input type="hidden" name="approval" value="2">
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

        <div class="app-form modal" id="modal-add-stock-item">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Menambahkan Item DO</h3>
                </div>
                <form action="/stock/in" method="POST">
                    <input type="hidden" name="document" value=6>
                    <input type="hidden" name="spec_doc" value=<?= $_GET['do']; ?>>
                    <?php /* do in: 1, do out:2,  */ ?>                              
                    <input type="hidden" name="do_type" value=<?= $doData[0]->do_type; ?>>
                    <div class="form-group">
                        <label>Product</label>
                        <select name="product" class="form-control" required>
                            <option value=''>PRODUCT</option>
                            <?php foreach($doItems as $item): ?> 
                                <option value= <?= $item->product; ?> data-qty=<?= $item->quantity ?>><?= ucfirst($item->name); ?></option>             
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="detail-respond row" style="margin-bottom:5px;">
                        <div class="col-md-4"></div>
                        <div class="col-md-8"></div>
                    </div>
                    <div class="form-group">
                        <label>Diterima pada</label>
                        <input type="date" class="form-control" name="received_at" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" class="form-control" name="quantity" min=1 step=1 required>
                    </div>

                    <button type="button" class="btn btn-danger btn-close">Tutup</button>
                    <button type="submit" class="btn btn-primary" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                
                </form>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- MAIN -->
        <div class="main-data" data-number=<?= $_GET['do']; ?> data-document=6>
            <a href=<?= getSearchPage(); ?>><button type="button" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-menu-left"></span> Kembali</button></a>
            <div class="row">
                <div class="col-md-8">
                    <?php foreach($doData as $data2): ?>
                        <div>
                            <h3>DO Number: <span style="background-color:#F6D155;" data-item="do_number"><?= $data2->do_number; ?></span></h3>
                            <h4>DO Date: <span data-item="do_date" data-item-val="<?= $data2->dd; ?>"><?= $data2->do_date; ?></span></h4>
                            <h4>PO Number: <a href="/form/po/detail?po=<?= $data2->poid ?>" target=_blank><?= $data2->po_number; ?></a></h3>
                        </div>
                        <div class="col-md-6" style="padding-left:0">
                            <h3>From: <?= makeFirstLetterUpper($data2->supplier); ?></h3>
                            <h4><?= makeFirstLetterUpper($data2->saddress); ?></h4>
                            <h4>Telp/Fax: <?= $data2->sphone; ?> / <?= $data2->sfax; ?></h4>
                            <h4>PIC: <?= makeFirstLetterUpper($data2->pic_supplier); ?></h4>
                        </div>
                        <div class="col-md-6 text-left" style="padding-right:0">
                            <h3>Ship to: <?= makeFirstLetterUpper($data2->buyer); ?></h3>
                            <h4><?= makeFirstLetterUpper($data2->baddress); ?></h4>
                            <h4>Telp/Fax: <?= $data2->bphone; ?> / <?= $data2->bfax; ?></h4>
                            <h4>PIC: <?= makeFirstLetterUpper($data2->pic_buyer); ?></h4>
                        </div>     
                    <?php endforeach; ?>
    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Delivered by</th>
                                <th>Received by</th>
                                <th>Created by</th>
                                <th>Updated by</th>
                                <th>Approved by</th>
                                <th>Approved</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        
                        <?php foreach($doData as $data1): ?>
                            <tr data-item=<?= $data1->id; ?>>
                                <td data-item="delivered_by"><?= makeFirstLetterUpper($data1->delivered_by); ?></td>
                                <td data-item="received_by"><?= makeFirstLetterUpper($data1->received_by); ?></td>
                                <td data-item="created_by" data-item-val=<?= $data1->cbid; ?>><?= makeFirstLetterUpper($data1->created_by); ?></td>
                                <td data-item="updated_by" data-item-val=<?= $data1->ubid; ?>><?= makeFirstLetterUpper($data1->updated_by); ?></td>
                                <td data-item="approved_by" data-item-val=<?= ($data1->approved_by!=null&&!empty($data1->approved_by))?$data1->abid:""; ?>><?= ($data1->approved_by!=null&&!empty($data1->approved_by))?makeFirstLetterUpper($data1->approved_by):"-"; ?></td>
                                <td data-item="approved"><?= ($data1->approved!=0&&!empty($data1->approved))?"Yes":"Not yet"; ?></td>
                                <?php if($data1->approved==0&&empty($data1->approved)): ?>
                                    <?php if($doData[0]->cbid==substr($_SESSION['sim-id'], 3, -3)): ?>
                                        <!-- Single button by Bootstrap -->
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="#" class="btn-modal btn-action" data-id="update-do-form"><span class="glyphicon glyphicon-pencil"></span> Update</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <!-- <td class="text-center"><button type="button" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-pencil"></span> Update</button></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-remove"></span> Remove</button></td>  -->   
                                    <?php elseif($doData[0]->abid==substr($_SESSION['sim-id'], 3, -3)): ?>
                                        <!-- Single button by Bootstrap -->
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="#" class="btn-modal btn-action" data-id="approve-do-form"><span class="glyphicon glyphicon-ok"></span> Setuju</a></li>
                                                    <li><a href="#" class="btn-modal btn-action" data-id="reject-do-form"><span class="glyphicon glyphicon-remove"></span> Ditolak</a></li>
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

                    <?php foreach($doData as $data2): ?>
                        <div style="margin-bottom:20px;">
                            <p>Keterangan: <div data-item='remark'><?= ($data2->remark==null||empty($data2->remark))?"-":makeFirstLetterUpper($data2->remark); ?></div></p>
                        </div>
                    <?php endforeach; ?>

                    <div>
                        <h3>Deskripsi unit</h3>
                        <?php if(count($receivedItems)==0): ?>
                            <p style="color:red">Belum terdapat data item do.</p>
                        <?php else: $printBtn=true; ?>
                            <table class="table table-striped">
                                <thead>
                                    <th>Produk</th>
                                    <th>Quantity</th>
                                    <th><?= $doData[0]->do_type==1?"Diterima":"Dikirim"; ?></th>
                                </thead>
                                <tbody>
                                    <?php foreach($receivedItems as $item): ?>
                                        <tr>
                                            <td><?= $item->product; ?></td>
                                            <td><?= $item->qty; ?></td>
                                            <td><?= $doData[0]->do_type==1?$item->received_at:$item->send_at; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                        <button class="btn btn-md btn-primary btn-modal" id="add-stock-item">Tambahkan item</button>
                    </div>

                    <?php if($printBtn): ?>
                        <div style="margin-top:10px;">
                            <a target="_blank" href="/print/do?do=<?= $_GET['do']; ?>"><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-print"></span> Cetak</button></a>
                            <button class="btn btn-sm btn-danger btn-modal" id="remove-do"><span class="glyphicon glyphicon-edit"></span> Remove</button>
                        </div>
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

    $('#remark').trumbowyg();

    /* UPDATE DO ITEM */
    $(".btn-action").on("click", function(){
        var dataId= $(this).attr("data-id");

        var poItem = $(this).parent().closest("tr").attr("data-item");

        if(dataId=='update-do-form'){        
            var receivedBy = $(this).parent().closest("tr").find("[data-item~='received_by']").html();
            var deliveredBy = $(this).parent().closest("tr").find("[data-item~='delivered_by']").html();
            var doDate = $("[data-item~='do_date']").attr("data-item-val");
            var remark = $("[data-item~='remark']").html();
            var doNumber = $("[data-item~='do_number']").html();

            console.log(doDate);

            $("#modal-update-do-form").find("input[name~='received_by']").val(receivedBy);
            $("#modal-update-do-form").find("input[name~='delivered_by']").val(deliveredBy); 

            $("#modal-update-do-form").find("input[name~='do_date']").val(doDate);
            $("#modal-update-do-form").find("#remark").trumbowyg('html', remark);
            $("#modal-update-do-form").find("#remark").trumbowyg('html');
            $("#modal-update-do-form").find("input[name~='do_number']").val(doNumber);

        }
    });

    /* SHOW ATTACHMENT */
    $("select[name~='attachment']").on("change", function(){
        var attachment = $(this).val();
        var responds = '';
        $.get("/dropdown-attachment", {upload_file: attachment}, function(data, status){
            //console.log(data);
            responds = JSON.parse(data);
            var image = "/public/upload/"+responds[0].upload_file;
            var description =responds[0].description;
            //console.log(responds);
            $("#modal-create-attachment").find(".image-appear").empty();
            $("#modal-create-attachment").find(".image-appear").append("<img src="+image+" alt='Attachment' class='img-responsive'><p class='text-center'>"+description+"</p>");
        });
    });

    //show the detail of the product
    $("#modal-add-stock-item").on("change", "select[name~='product']", function(){
        var product = $(this).val();

        $.get('/stock/getProductDetail', {product:product}, function(data, status){

            var productDetail = JSON.parse(data)[0];

            var detail ="<ul>";
            detail+="<li>Nama : "+productDetail.name+"</li>";
            detail+="<li>Part number : "+productDetail.part_number+"</li>";
            detail+="<li>Deskripsi : "+productDetail.description+"</li>";
            detail+="<li>Link : "+productDetail.link+"</li>";
            detail+="</ul>";

            $("select[name~='product']").closest("form").find(".detail-respond").find(".col-md-4").empty().append("<img src='/public/upload/"+productDetail.upload_file+"' class='img-responsive'>");
            $("select[name~='product']").closest("form").find(".detail-respond").find(".col-md-8").empty().append(detail);

        });

        $.get('/stock/check-stock-available', {product:product, status:1}, function(data, status){
            var responds = JSON.parse(data);
            var stockIn=0;
            
            if(responds.length>0){
                stockIn = Number(responds[0].quantity);
            }

            $("select[name~='product']").closest("form").find(".detail-respond").find(".col-md-8").find("li").last().append("<li><strong>Stock : "+stockIn+"</strong></li>");

        });

        var quantity = $(this).find("option:selected").attr("data-qty");
        
        $(this).closest("form").find("input[name~='quantity']").val(0).attr("max", quantity);
        
        //if DO in then type the serial number
        //if DO out then select the serial number

        //do in: 1, do out:2,
        var doType = $(this).closest("form").find("input[name~='do_type']").val();
        
        if(doType == 2){

            $(this).closest("form").find("input[name~='received_at']").parent().find("label").html("Dikirim pada");

            $(this).closest("form").find("input[name~='received_at']").attr("name", "send_at");
            
        }

    });

    $("#modal-add-stock-item").on("change", "input[name~='quantity']", function(){
        var quantity = $(this).val();
        var snColumn = $(this).closest("form").find("[name~='serial_number[]']");
        snColumn.not(":first").remove();
        var firstSNColumn = snColumn.first();

        for(var i=1; i<quantity; i++){
            $(this).closest("form").find("[name~='serial_number[]']:last").after(firstSNColumn.clone());
        }
    });

});
</script>
<?php

require base.'base/footer.view.php'

?>