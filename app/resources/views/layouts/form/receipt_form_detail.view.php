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
                        <input type="hidden" name="document_type" value=11>
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
                        <input type="hidden" name="document_data" value=<?= $receiptData[0]->ddata; ?>>
                        <div class="form-group">
                            <label>Lampiran</label>
                            <textarea class="form-control" name="description" placeholder="Tuliskan deskripsi lampiran..." required></textarea>
                        </div>
                        <input type="file" name="attachment" required><br>
                        <button type="submit" class="btn btn-primary pull-right">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                </div>
                <button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

            </div>
        </div>

        <!-- UPDATE FORM -->
        <div class="app-form modal" id="modal-update-receipt-form">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui Data <?= $titlePage; ?></h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui data <?= $titlePage; ?>.</p>
                </div>
                <form action="/form/receipt/update" method="post">
                    <input type="hidden" name="receipt_form" value=<?= $_GET['r']; ?>>
                    <div class="form-group">
                        <label>Tanggal receipt</label>
                        <input type="date" name="receipt_date" class="form-control" required>
                    </div>
                    <div class='form-group'>
                        <label>Receipt Number</label>
                        <input type='text' name='receipt_number' class='form-control' required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Supplier</label>
                                <select name="supplier" class="form-control">
                                    <option value=''>SUPPLIER</option>
                                    <?php foreach($partners as $partner): ?>
                                        <option value="<?= $partner->id ?>"><?= ucfirst($partner->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
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
                    </div>
                    <div class="form-group">
                        <label>Mata uang</label>
                        <select name="currency" class="form-control">
                            <option value=''>MATA UANG</option>
                            <option value='1'>Rupiah</option>
                            <option value='2'>Dollar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>PPN (%)</label>
                        <input type="number" min=0 max=100 name="ppn" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
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

        <!-- REMOVE RECEIPT FORM -->
        <div class="app-form modal" id="modal-remove-receipt-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/receipt/remove" method="post">
                        <input type="hidden" name="receipt_form" value=<?= $_GET['r']; ?>>
                        <button type="submit" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Hapus data</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- UPDATE RECEIPT ITEM -->
        <div class="app-form modal" id="modal-update-receipt-item">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Memperbarui data</h3>
                </div>
                <form action="/form/receipt/update-item" method="POST">
                    <input type="hidden" name="receipt_item" value=''>
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
                        <input type="number" class="form-control" name="discount" min=0 step=1 required>
                    </div>
                    <button type="button" class="btn btn-danger btn-close">Tutup</button>
                    <button type="submit" class="btn btn-primary" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                
                </form>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- REMOVE RECEIPT ITEM -->
        <div class="app-form modal" id="modal-remove-receipt-item">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/receipt/remove-item" method="post">
                        <input type="hidden" name="receipt_item" value="">
                        <button type="submit" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Hapus item</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- ADD NEW ITEM -->
        <div class="app-form modal" id="modal-add-stock-item">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Menambahkan Receipt Item</h3>
                </div>
                <form action="/form/receipt/new-item" method="POST">
                    <?php /* rcp in: 1, rcp out:2,  */ ?>
                    <input type="hidden" name="receipt_type" value=<?= $receiptData[0]->receipt_type; ?> >
                    <input type="hidden" name="receipt_form" value=<?= $_GET['r']; ?>>
                    <div class="modal-wizard show">
                        <div class="description">
                            <p>Pilih produk sesuai barang yang diserahterimakan. Apabila barang tidak terdaftar maka daftarkan terlebih dahulu atau pilih
                            produk 'lain-lain'.</p>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" class="form-control" name="receive_send_date" required>
                        </div>
                        <div class="row inline-input">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Produk</label>
                                    <select name="product[]" class="form-control" required>
                                        <option value=''>PRODUK</option>
                                        <?php foreach($products as $product): ?>
                                            <option title="Available: <?= $product->quantity; ?>" value=<?= $product->id ?> data-qty=<?= $product->quantity; ?>><?= makeItShort(ucfirst($product->name), 50); ?></option>
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
                                    <input type="number" min=0 name="price[]" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Disc (%)</label>
                                    <input type="number" min=0 name="discount[]" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-default btn-add-input-form">Tambah</button>
                    </div>
                    <br>
                    <button type="button" class="btn btn-danger btn-close">Tutup</button>
                    <button type="submit" class="btn btn-primary" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                
                </form>
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
                            <h3><span style="background-color:#F6D155;">Receipt Number: <span data-item='receipt_number'><?= $data->receipt_number; ?></span></span></h3>
                            <h4>Receipt Date: <span data-item='receipt_date' data-item-val="<?= $data->rd; ?>"><?= $data->receipt_date; ?></span></h4>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h3 data-item="supplier" data-item-val=<?= $data->sid; ?>>From: <?= makeFirstLetterUpper($data->supplier); ?></h3>
                                <h4><?= makeFirstLetterUpper($data->saddress); ?></h4>
                                <h4>Telp: <?= $data->sphone; ?></h4>
                            </div>
                            <div class="col-md-6 text-left">
                                <h3 data-item="buyer" data-item-val=<?= $data->bid; ?>>Ship to: <?= makeFirstLetterUpper($data->buyer); ?></h3>
                                <h4><?= makeFirstLetterUpper($data->baddress); ?></h4>
                                <h4>Telp: <?= $data->bphone; ?></h4>
                            </div>     
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <div>
                        <h3>Item list</h3>
                        <?php if(count($receiptItems)==0): ?>
                            <p style="color:red">Belum terdapat data receipt item.</p>
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
                                    <?php foreach($receiptItems as $item): $priceTotal=0; $price=(100-$item->discount)*$item->price*0.01; $priceTotal=$price*$item->quantity;  ?>
                                        <tr id=<?= $item->id; ?>>
                                            <td data-item="product" data-item-val=<?= $item->pid; ?>><?= $item->product; ?></td>
                                            <td data-item="quantity"><?= $item->quantity; ?></td>
                                            <td data-item="price" data-item-val=<?= $item->price; ?> class="text-right"><?= formatRupiah($item->price); ?></td>
                                            <td data-item="discount"><?= $item->discount; ?></td>
                                            <td data-item="total" data-item-val=<?= $item->price; ?> class="text-right"><?= formatRupiah($priceTotal); ?></td>
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
                        <button class="btn btn-md btn-primary btn-modal" id="add-stock-item">Tambahkan item</button>
                    </div>

                    <?php foreach($receiptData as $data): ?>
                        <div style="margin-bottom:20px;">
                            <p>Mata uang: <span data-item="currency" data-item-val="<?= $data->cid; ?>"><?= $data->currency; ?></span></p>
                            <p>PPN: <span data-item="ppn"><?= $data->ppn; ?></span> %</p>
                            <p>Keterangan: <span data-item="remark"><?= ($data->remark==null||empty($data->remark))?"-":makeFirstLetterUpper($data->remark); ?></span></p>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if($printBtn): ?>
                        <a target="_blank" href="/print/receipt?r=<?= $_GET['r']; ?>"><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-print"></span> Cetak</button></a>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-primary btn-modal" id="update-receipt-form"><span class="glyphicon glyphicon-edit"></span> Update</button>
                    <button class="btn btn-sm btn-danger btn-modal" id="remove-receipt-form"><span class="glyphicon glyphicon-edit"></span> Remove</button>

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
                                        <?php if($attachment->file_type==1): ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <img src="/public/upload/<?= $attachment->upload_file; ?>" class='img-responsive img-scroll-item clearfix' style="width:100%; max-width:100%;">
                                                </div>
                                                <div class="col-md-6">
                                                    <p class='img-scroll-item-desc'><?= $attachment->description; ?></p>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p><?= $attachment->description; ?></p>
                                            <p><a href="/public/upload/<?= $attachment->upload_file; ?>" target="_blank">File</a></p>
                                        <?php endif; ?>
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
    $("#modal-add-stock-item").on("change", "select[name~='product[]']", function(){
        var product = $(this).val();

        var receiptType = $(this).closest("form").find("input[name~='receipt_type']").val();

        if(receiptType==2){
            var quantity = $(this).find("option:selected").attr("data-qty");
        
            $(this).closest(".inline-input").find("input[name~='quantity[]']").val(0).attr("max", quantity);
        } 

    });

    /* UPDATE ITEM */
    $(".btn-action").on("click", function(){
        var dataId= $(this).attr("data-id");

        var receiptItem = $(this).parent().closest("tr").attr("id");

        if(dataId=='update-receipt-item'){        
            
            var product = $(this).parent().closest("tr").find("[data-item~='product']").attr("data-item-val");
            var quantity = $(this).parent().closest("tr").find("[data-item~='quantity']").html();
            var price = $(this).parent().closest("tr").find("[data-item~='price']").attr("data-item-val");
            var discount = $(this).parent().closest("tr").find("[data-item~='discount']").html();

            $("#modal-update-receipt-item").find("input[name~='receipt_item']").val(receiptItem);
            //$("#modal-update-receipt-item").find("select[name~='product']").find("option").attr("selected", false);
            $("#modal-update-receipt-item").find("select[name~='product']").find("option[value~='"+product+"']").attr("selected", true);
            $("#modal-update-receipt-item").find("input[name~='quantity']").val(quantity); 
            $("#modal-update-receipt-item").find("input[name~='price']").val(price);
            $("#modal-update-receipt-item").find("input[name~='discount']").val(discount);
            
        }else{
            $("#modal-remove-receipt-item, #modal-approve-receipt-item, #modal-reject-receipt-item, #modal-revision-receipt-item").find("input[name~='receipt_item']").val(receiptItem);
        } 
    });

    /* UPDATE FORM */
    $("#update-receipt-form").on("click", function(){
        var data=$(".main-data");

        var placeholderForm=["receipt_number", "receipt_date", "ppn"];
        var dataItem='';

        for(var i=0;i<placeholderForm.length;i++){
            dataItem=data.find("[data-item~='"+placeholderForm[i]+"']");
            if(dataItem.attr('data-item-val')!=null){
                var value = dataItem.attr('data-item-val');
            }else{
                var value = dataItem.html();
            }

            $("#modal-update-receipt-form").find("form").find("[name~='"+placeholderForm[i]+"']").val(value);
        
        }

        var remark = data.find("[data-item~='remark']").html();
        $("#modal-update-receipt-form").find("#remark").trumbowyg('html', remark);
		$("#modal-update-receipt-form").find("#remark").trumbowyg('html');

        var placeholderForm2=["supplier", "buyer", "currency"];
        for(var i=0;i<placeholderForm2.length;i++){
            dataItem=data.find("[data-item~='"+placeholderForm2[i]+"']");
            if(dataItem.attr('data-item-val')!=null){
                var value = dataItem.attr('data-item-val');
            }
           // $("#modal-update-po-form").find("select[name~='"+placeholderForm2[i]+"']").find("option").attr("selected", false);
            $("#modal-update-receipt-form").find("form").find("select[name~='"+placeholderForm2[i]+"']").find("option[value~='"+value+"']").attr("selected", true);
        
        }
    });

});
</script>
<?php

require base.'base/footer.view.php'

?>