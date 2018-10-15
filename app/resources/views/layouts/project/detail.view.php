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
                <button class="btn btn-sm btn-header btn-modal" id="create-new-request"><span class="glyphicon glyphicon-plus"></span> Request item</button>
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
                        <input type="hidden" name="document_number" value=<?= $_GET['pr']; ?>>
                        <input type="hidden" name="document_type" value=10>
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
                    <form action="/attachment" method="post">
                        <input type="hidden" name="document_data" value=<?= $projectDetailData[0]->ddata; ?>>
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

        <!-- UPDATE REQUEST ITEM -->
        <div class="app-form modal" id="modal-update-item">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui Data <?= $titlePage; ?></h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui data <?= $titlePage; ?>.</p>
                    <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan</p>
                </div>
                <form action="/project/update-item" method="post">
                    <input type="hidden" name="item_request" value="">
                    <div class="form-group">
                        <label>Request item</label>
                        <select name="product" class="form-control" required>
                            <option value=''>PRODUK</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" min=0 class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Returned</label>
                        <input type="number" name="returned" min=0 class="form-control" required>
                    </div>

                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>

                    <div class="nav-right">
                        <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </div>
                </form>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- REMOVE REQUEST ITEM -->
        <div class="app-form modal" id="modal-remove-item">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/project/remove-item" method="post">
                        <input type="hidden" name="request_item" value="">
                        <div class="form-group">
                            <label>Request item</label>
                            <select name="product" class="form-control" required>
                                <option value=''>PRODUK</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Hapus data</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
            </div>
            <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
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

        <!-- REQUEST NEW ITEM -->
        <div class="app-form modal" id="modal-create-new-request">
            <div class="modal-content modal-wizard show">
                <div class="modal-header">
                    <h3>Request item</h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan oleh user untuk request item yang diperlukan pada project ini.</p>
                    <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan</p>
                </div>
                <form action="/project/new-request" method="post">
                    <input type="hidden" name="project" value=<?= $_GET['pr']; ?>>
                    <div class="form-group">
                        <label>Request date</label>
                        <input type="date" name="request_date" min=0 class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Requested by</label>
                        <select name="requested_by" class="form-control" required>
                            <option value=''>Requested by</option>
                            <?php foreach($users as $user): ?>
                                <option value=<?= $user->id ?>><?= ucfirst($user->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row inline-input">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Produk</label>
                                <select name="product[]" class="form-control" required>
                                    <option value=''>PRODUK</option>
                                    <?php foreach($products as $product): ?>
                                        <option title="<?= $product->name; ?>" value=<?= $product->id ?>><?= (strlen($product->name)>50)?substr(ucfirst($product->name),0, 50)."...":ucfirst($product->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number" min=0 name="quantity[]" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-default btn-add-input-form">Tambah</button>
                    <div class="form-group">
                        <label>Remark</label>
                        <textarea name="remark" class="form-control"></textarea>
                    </div>
                            
                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>

                    <div class="nav-right">
                        <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </div>
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
        <div class="main-data" data-document=10 data-number=<?= $_GET['pr']; ?>>
            <a href=<?= getSearchPage(); ?>><button type="button" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-menu-left"></span> Kembali</button></a>
            <div class="row">
                <div class="col-md-3 table-responsive">
                    <table class="table table-hover">
                        <?php foreach($projectDetailData as $data): ?>
                            <tr>
                                <th>PO</th>
                                <td><a href='/form/po/detail?po=<?= $data->po; ?>' target=_blank><?= ucfirst($data->po_number); ?></a></td>
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
                                <td>
                                    <?php if($data->psid!=3): ?>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?= ucfirst($data->project_status); ?> <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="btn-modal btn-action" data-id="update-item"><span class="glyphicon glyphicon-pencil"></span> Sedang Dikerjakan</a></li>
                                            <li><a href="#" class="btn-modal btn-action" data-id="remove-item"><span class="glyphicon glyphicon-remove"></span> Selesai</a></li>
                                        </ul>
                                    </div>
                                    <?php else: ?>    
                                        Selesai
                                    <?php endif; ?>          
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <button type="button" class="btn btn-primary btn-sm btn-modal" id="update-project"><span class="glyphicon glyphicon-pencil"></span> Update data</button>   
                    <a target="_blank" href="/print/project-form?pr=<?= $_GET['pr']; ?>"><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-print"></span> Cetak</button></a>
                    
                </div>
                <div class="col-md-9">
                    <h3>Daftar Project Item Request</h3>
                    <?php if(count($projectItemRequested)==0): ?>
                        <p>Belum terdapat data Request item</p>  
                    <?php else: $printBtn=true; ?>
                        <table class="table table-striped">
                            <thead>
                                <th>Tanggal</th>
                                <th>Nomor</th>
                                <th>Oleh</th>
                                <th>Part number</th>
                                <th>Produk</th>
                                <th>Quantity</th>
                                <th>Returned</th>
                                <th>Keterangan</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                <?php foreach($projectItemRequested as $item): ?>
                                    <tr>
                                        <td><?= $item->request_date; ?></td>
                                        <td><?= $item->request_number; ?></td>
                                        <td><?= ucwords($item->requested_by); ?></td>
                                        <td><?= $item->part_number; ?></td>
                                        <td><?= $item->product; ?></td>
                                        <td><?= $item->quantity_out; ?></td>
                                        <td><?= $item->quantity_in==''?'0':$item->quantity_in; ?></td>
                                        <td><?= makeFirstLetterUpper($item->remark); ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="#" class="btn-modal btn-action" data-id="update-item"><span class="glyphicon glyphicon-pencil"></span> Update</a></li>
                                                    <li><a href="#" class="btn-modal btn-action" data-id="remove-item"><span class="glyphicon glyphicon-remove"></span> Remove</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
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