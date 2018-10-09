<?php

$titlePage="Receipt";

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
                        <input type="hidden" name="document_number" value=<?= $_GET['r']; ?>>
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
                        <input type="hidden" name="document_data" value=<?= $receiptData[0]->ddata; ?>>
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
                    <input type="hidden" name="do-form" value=<?= $_GET['do']; ?>>
                    <div class="form-group">
                        <label>Diserahkan oleh</label>
                        <input type="text" name="delivered_by" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Diterima oleh</label>
                        <input type="text" name="received_by" class="form-control" required>
                    </div>
                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>

                    <div class="nav-right">
                        <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </div>
                </form>
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

        <!-- REMOVE RECEIPT ITEM -->
        <div class="app-form modal" id="modal-remove-receipt-item">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/receipt/remove" method="post">
                        <input type="hidden" name="receipt-item" value="">
                        <button type="submit" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Hapus data</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
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
        <div class="main-data" data-number=<?= $_GET['r']; ?> data-document=11>
            <a href=<?= getSearchPage(); ?>><button type="button" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-menu-left"></span> Kembali</button></a>
            <div class="row">
                <div class="col-md-8">
                    <?php foreach($receiptData as $data): ?>
                        <div>
                            <h3><span style="background-color:#F6D155;">Receipt Number: <?= $data->receipt_number; ?></span></h3>
                            <h4>Receipt Date: <?= $data->receipt_date; ?></h4>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h3>From: <?= makeFirstLetterUpper($data->supplier); ?></h3>
                                <h4><?= makeFirstLetterUpper($data->saddress); ?></h4>
                                <h4>Telp: <?= $data->sphone; ?></h4>
                            </div>
                            <div class="col-md-6 text-left">
                                <h3>Ship to: <?= makeFirstLetterUpper($data->buyer); ?></h3>
                                <h4><?= makeFirstLetterUpper($data->baddress); ?></h4>
                                <h4>Telp: <?= $data->bphone; ?></h4>
                            </div>     
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div>
                        <h3>Item list</h3>
                        <?php if(count($receiptItems)==0): ?>
                            <p style="color:red">Belum terdapat data item do.</p>
                        <?php else: $printBtn=true; ?>
                            <table class="table table-striped">
                                <thead>
                                    <th>Produk</th>
                                    <th>Quantity</th>
                                    <th>Price unit</th>
                                    <th>Discount(%)</th>
                                    <th>Price total</th>
                                    <th class="text-center">Action</th>
                                </thead>
                                <tbody>
                                    <?php foreach($receiptItems as $item): $priceTotal=0; $price=(100-$item->discount)*$item->price*0.01; $priceTotal+=$price;  ?>
                                        <tr id=<?= $item->id; ?>>
                                            <td data-item="product" data-item-val=<?= $item->pid; ?>><?= $item->product; ?></td>
                                            <td data-item="quantity"><?= $item->quantity; ?></td>
                                            <td data-item="price"><?= $item->price; ?></td>
                                            <td data-item="discount"><?= $item->discount; ?></td>
                                            <td data-item="total"><?= $priceTotal; ?></td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="#" class="btn-modal btn-action" data-id="update-receipt-item"><span class="glyphicon glyphicon-pencil"></span> Update</a></li>
                                                        <li><a href="#" class="btn-modal btn-action" data-id="remove-receipt-item"><span class="glyphicon glyphicon-remove"></span> Remove</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>

                    <?php foreach($receiptData as $data): ?>
                        <div style="margin-bottom:20px;">
                            <p>Keterangan: <?= ($data->remark==null||empty($data->remark))?"-":makeFirstLetterUpper($data->remark); ?></p>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if($printBtn): ?>
                        <a target="_blank" href="/print/receipt?r=<?= $_GET['r']; ?>"><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-print"></span> Cetak</button></a>
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

        <div class="app-form modal" id="modal-update-receipt-item">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Memperbarui data</h3>
                </div>
                <form action="/stock/in" method="POST">
                    <input type="hidden" name="id" value=<?= $item->id; ?>>
                    <?php /* rcp in: 1, rcp out:2,  */ ?>
                        <input type="hidden" name="receipt_type" value=<?= $receiptData[0]->receipt_type; ?> >
                    <div class="form-group">
                        <label>Product</label>
                        <select name="product" class="form-control" required>
                            <option value=''>PRODUCT</option>
                            <?php foreach($receiptItems as $item): ?> 
                                <option value= <?= $item->pid; ?> ><?= ucfirst($item->product); ?></option>             
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="detail-respond row" style="margin-bottom:5px;">
                        <div class="col-md-4"></div>
                        <div class="col-md-8"></div>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" class="form-control" name="quantity" min=1 step=1 required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" name="price" min=1 step=1 required>
                    </div>
                    <div class="form-group">
                        <label>Discount</label>
                        <input type="number" class="form-control" name="discount" min=1 step=1 required>
                    </div>
                    <button type="button" class="btn btn-danger btn-close">Tutup</button>
                    <button type="submit" class="btn btn-primary" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                
                </form>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

    </div>
</main>
<script>

$(document).ready(function(){

    /* UPDATE DO ITEM */
    /* $(".btn-action").on("click", function(){
        var dataId= $(this).attr("data-id");

        var poItem = $(this).parent().closest("tr").attr("data-item");

        if(dataId=='update-receipt-form'){        
            var receivedBy = $(this).parent().closest("tr").find("[data-item~='received_by']").html();
            var deliveredBy = $(this).parent().closest("tr").find("[data-item~='delivered_by']").html();

            $("#modal-update-receipt-form").find("input[name~='received_by']").val(receivedBy);
            $("#modal-update-receipt-form").find("input[name~='delivered_by']").val(deliveredBy); 

        }
    }); */

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
            detail+="<li>Vendor : "+productDetail.vendor+"</li>";
            detail+="<li>Part number : "+productDetail.part_number+"</li>";
            detail+="<li>Deskripsi : "+productDetail.description+"</li>";
            detail+="<li>Link : "+productDetail.link+"</li>";
            detail+="</ul>";

            $("select[name~='product']").closest("form").find(".detail-respond").find(".col-md-4").empty().append("<img src='/public/upload/"+productDetail.upload_file+"' class='img-responsive'>");
            $("select[name~='product']").closest("form").find(".detail-respond").find(".col-md-8").empty().append(detail);

        });

        var quantity = $(this).find("option:selected").attr("data-qty");
        
        $(this).closest("form").find("input[name~='quantity']").val(0).attr("max", quantity);
        
        //if DO in then type the serial number
        //if DO out then select the serial number

        //do in: 1, do out:2,
        var doType = $(this).closest("form").find("input[name~='do_type']").val();
        
        if(doType == 1){

            $(this).closest("form").find("[name~='serial_number[]']").replaceWith("<input type='text' name='serial_number[]' class='form-control' required>");
        
        }else if(doType == 2){

            $(this).closest("form").attr("action", "/stock/out");

            $(this).closest("form").find("input[name~='received_at']").parent().find("label").html("Dikirim pada");

            $(this).closest("form").find("input[name~='received_at']").attr("name", "send_at");

            //change to select input
            $(this).closest("form").find("[name~='serial_number[]']").replaceWith("<select name='serial_number[]' class='form-control' required>");

            $.get('/stock/get-serial-number', {product:product}, function(data, status){
                var serialNumber = JSON.parse(data);
                var snOption = "<option value=''>Serial Number</option>";

                for(var i=0; i<serialNumber.length; i++){
                    snOption+="<option value="+serialNumber[i].serial_number+">"+serialNumber[i].serial_number+"</option>";
                }

                $("#modal-add-stock-item").find("select[name~='serial_number[]']").empty().append(snOption);
            });
        }

    });

    /* UPDATE QUO ITEM */
    $(".btn-action").on("click", function(){
        var dataId= $(this).attr("data-id");

        var receiptItem = $(this).parent().closest("tr").attr("id");

        if(dataId=='update-receipt-item'){        
            
            var product = $(this).parent().closest("tr").find("[data-item~='product']").attr("data-item-val");
            var quantity = $(this).parent().closest("tr").find("[data-item~='quantity']").html();
            var price = $(this).parent().closest("tr").find("[data-item~='price']").html();
            var discount = $(this).parent().closest("tr").find("[data-item~='discount']").html();

            $("#modal-update-receipt-item").find("input[name~='receipt-item']").val(receiptItem);
            //$("#modal-update-receipt-item").find("select[name~='product']").find("option").attr("selected", false);
            $("#modal-update-receipt-item").find("select[name~='product']").find("option[value~='"+product+"']").attr("selected", true);
            $("#modal-update-receipt-item").find("input[name~='quantity']").val(quantity); 
            $("#modal-update-receipt-item").find("input[name~='price']").val(price);
            $("#modal-update-receipt-item").find("input[name~='discount']").val(discount);
            
        }else{
            $("#modal-remove-receipt-item, #modal-approve-receipt-item, #modal-reject-receipt-item, #modal-revision-receipt-item").find("input[name~='receipt-item']").val(receiptItem);
        } 
    });

});
</script>
<?php

require base.'base/footer.view.php'

?>