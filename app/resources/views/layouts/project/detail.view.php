<?php

$titlePage="Project";

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

$priceTotal=0;

?>

<main>
    <div class="container-fluid">
        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1>Detail <?= $titlePage; ?></h1>
            <p>Halaman ini menangani data detail terkait <?= $titlePage; ?></p>
            <?php if($projectDetailData[0]->psid!=3): ?>
                <button class="btn btn-sm btn-header btn-modal" id="create-new-item"><span class="glyphicon glyphicon-plus"></span> Request item</button>
            <?php endif; ?>
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
                        <input type="hidden" name="document_number" value=<?= $_GET['po']; ?>>
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
                        <input type="hidden" name="document_data" value=<?= $poData[0]->ddata; ?>>
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

        <!-- UPDATE PO FORM -->
        <div class="app-form modal" id="modal-update-po-form">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui Data <?= $titlePage; ?></h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui data <?= $titlePage; ?>.</p>
                    <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan</p>
                </div>
                <form action="/form/po/update" method="post">
                    <input type="hidden" name="po-item" value="">
                    <div class="form-group">
                        <label>Product</label>
                        <select name="product" class="form-control" required>
                            <option value=''>PRODUK</option>
                            <?php foreach($products as $product): ?>
                                <option value=<?= $product->id ?> title=" <?= $product->description ?> " ><?= ucfirst($product->name).'|'.strtoupper($product->part_number); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" min=0 class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Price unit</label>
                        <input type="number" name="price_unit" min=0 class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Diskon (%)</label>
                        <input type="number" min=0 name="item_discount" class="form-control" required>
                    </div>

                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>

                    <div class="nav-right">
                        <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- REMOVE PO FORM -->
        <div class="app-form modal" id="modal-remove-po-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/po/remove" method="post">
                        <input type="hidden" name="po-item" value="">
                        <input type="hidden" name="po" value=<?= $_GET['po']; ?>>
                        <button type="submit" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Hapus data</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
            </div>
        </div>

        <!-- APPROVAL PO FORM -->
        <div class="app-form modal" id="modal-approve-po-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/po/approve" method="post">
                        <input type="hidden" name="po-item" value="">
                        <input type="hidden" name="a" value="1">
                        <button type="submit" class="btn btn-success btn-sm form-control"><span class="glyphicon glyphicon-ok"></span> Setuju</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
            </div>
        </div>

        <!-- REJECT PO FORM -->
        <div class="app-form modal" id="modal-reject-po-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/po/approve" method="post">
                        <input type="hidden" name="po-item" value="">
                        <input type="hidden" name="a" value="0">
                        <button type="submit" name="reject" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Ditolak</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
            </div>
        </div>

        <!-- CREATE NEW ITEM -->
        <div class="app-form modal" id="modal-create-new-item">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Menambah item <?= $titlePage; ?></h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk menambahkan item <?= $titlePage; ?>.</p>
                    <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan</p>
                </div>
                <form action="/form/po/new-item" method="post">
                    <input type="hidden" name="po" value=<?= $_GET['po']; ?>>
                    <div class="form-group">
                        <label>Product</label>
                        <select name="product" class="form-control" required>
                            <option value=''>PRODUK</option>
                            <?php foreach($products as $product): ?>
                                <option value=<?= $product->id ?> title=<?= $product->description ?> ><?= ucfirst($product->name).'|'.strtoupper($product->part_number); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" min=0 class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Price unit</label>
                        <input type="number" name="price_unit" min=0 class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Diskon (%)</label>
                        <input type="number" min=0 name="item_discount" class="form-control" required>
                    </div>
                            
                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>

                    <div class="nav-right">
                        <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </div>
                </form>
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
        <div class="main-data">
            <a href=<?= getSearchPage(); ?>><button type="button" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-menu-left"></span> Kembali</button></a>
            <div class="row">
                <div class="col-md-4 table-responsive">
                    <table class="table table-hover">
                        <?php foreach($projectDetailData as $data): ?>
                            <tr>
                                <th>PO</th>
                                <td><?= ucfirst($data->po_number); ?></td>
                            </tr>
                            <tr>
                                <th>Customer</th>
                                <td><?= ucfirst($data->customer); ?></td>
                            </tr>
                            <tr>
                                <th>Project</th>
                                <td data-item="name"><?= ucfirst($data->name); ?></td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td data-item="description"><?= ucfirst($data->description); ?></td>
                            </tr>
                            <tr>
                                <th>Start date</th>
                                <td data-item="start_date" data-item-val="<?= $data->sd; ?>"><?= ucfirst($data->start_date); ?></td>
                            </tr>
                            <tr>
                                <th>End date</th>
                                <td data-item="end_date" data-item-val="<?= $data->ed; ?>"><?= ucfirst($data->end_date); ?></td>
                            </tr>
                            <tr>
                                <th>PIC</th>
                                <td data-item="pic" data-item-val="<?= $data->picid; ?>"><?= ucfirst($data->pic); ?></td>
                            </tr>
                            <tr>
                                <th>Created by</th>
                                <td><?= ucfirst($data->created_by); ?></td>
                            </tr>
                            <tr>
                                <th>Created at</th>
                                <td><?= ucfirst($data->created_at); ?></td>
                            </tr>
                            <tr>
                                <th>Updated by</th>
                                <td><?= ucfirst($data->updated_by); ?></td>
                            </tr>
                            <tr>
                                <th>Updated at</th>
                                <td><?= ucfirst($data->updated_at); ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><?= ucfirst($data->updated_at); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <button type="button" class="btn btn-primary btn-sm btn-modal" id="update-project"><span class="glyphicon glyphicon-pencil"></span> Update data</button>   
                    <a target="_blank" href="/print/project-form?pr=<?= $_GET['pr']; ?>"><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-print"></span> Cetak</button></a>
                    
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

    /* UPDATE PO ITEM */
    $(".btn-action").on("click", function(){
        var dataId= $(this).attr("data-id");

        var poItem = $(this).parent().closest("tr").attr("data-item");

        if(dataId=='update-po-form'){        
            var product = $(this).parent().closest("tr").find("[data-item~='product']").attr("data-item-val");
            var quantity = $(this).parent().closest("tr").find("[data-item~='quantity']").html();
            var priceUnit = $(this).parent().closest("tr").find("[data-item~='price_unit']").html();
            var discountItem = $(this).parent().closest("tr").find("[data-item~='item_discount']").html();

            $("#modal-update-po-form").find("input[name~='po-item']").val(poItem);
            $("#modal-update-po-form").find("select[name~='product']").find("option").attr("selected", false);
            $("#modal-update-po-form").find("select[name~='product']").find("option[value~='"+product+"']").attr("selected", true);
            $("#modal-update-po-form").find("input[name~='quantity']").val(quantity); 
            $("#modal-update-po-form").find("input[name~='price_unit']").val(priceUnit);
            $("#modal-update-po-form").find("input[name~='item_discount']").val(discountItem);
        }else{
            $("#modal-remove-po-form, #modal-approve-po-form, #modal-reject-po-form").find("input[name~='po-item']").val(poItem);
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

});
</script>
<?php

require base.'base/footer.view.php'

?>