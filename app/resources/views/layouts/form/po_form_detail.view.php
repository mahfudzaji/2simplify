<?php

$titlePage="Purchase Order";

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
            <?php if($poData[0]->quo==null || empty($poData[0]->quo)): ?>
                <button class="btn btn-sm btn-header btn-modal" id="create-new-item"><span class="glyphicon glyphicon-plus"></span> Item baru</button>
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
                        <input type="hidden" name="document_data" value=<?= $poData[0]->ddata; ?>>
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

        <!-- UPDATE PO FORM -->
        <div class="app-form modal" id="modal-update-po">
            <div class="modal-content" style="width:50%;">
                <div class="modal-header">
                    <h3>Update Data <?= $titlePage; ?></h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui data <?= $titlePage; ?>.</p>
                    <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan</p>
                </div>
                <form action="/form/po/update" method="post">
                    <input type="hidden" name="po" value=<?= $_GET['po']; ?>>

                    <div class="form-group">
                        <label>PO number</label>
                        <input type="text" class="form-control" name="po_number" placeholder="PO number" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal PO</label>
                        <input type="date" name="doc_date" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>PIC buyer</label>
                        <input type="text" class="form-control" name="pic_buyer" placeholder="PIC pihak pengaju quotation" required>
                    </div>

                    <div class="form-group">
                        <label>PIC supplier</label>
                        <input type="text" class="form-control" name="pic_supplier" placeholder="PIC pihak pemberi quotation" required>
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

        <!-- REMOVE PO FORM -->
        <div class="app-form modal" id="modal-remove-po">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/po/remove" method="post">
                        <input type="hidden" name="po" value=<?= $_GET['po']; ?>>
                        <button type="submit" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Hapus data</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- NOT USED -->
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
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
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
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>
        <!-- NOT USED -->

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
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- UPDATE PO ITEM -->
        <div class="app-form modal" id="modal-update-po-item">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui Data <?= $titlePage; ?></h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui data <?= $titlePage; ?>.</p>
                    <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan</p>
                </div>
                <form action="/form/po/update-item" method="post">
                    <input type="hidden" name="po-item" value="">
                    <input type="hidden" name="po-form" value="<?= $_GET['po']; ?>">
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
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- REMOVE PO ITEM -->
        <div class="app-form modal" id="modal-remove-po-item">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/po/remove-item" method="post">
                        <input type="hidden" name="po-item" value="">
                        <button type="submit" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Hapus data</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- PRINT PO -->
        <div class="app-form modal" id="modal-print-po">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Cetak Data <?= $titlePage; ?></h3>
                </div>

                <div class="description">
                    <p><span style="color:red;">*</span>Catatan: <br> Keterangan tambahan yang akan ditambahkan pada form PO</p>
                </div>
                <form action="/print/po" method="GET">
                    <input type="hidden" name="po" value="<?= $_GET['po']; ?>">
                    <div id="apr"></div>

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
        <div class="main-data" data-number=<?= $_GET['po']; ?> data-document=5>
            <a href=<?= getSearchPage(); ?>><button type="button" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-menu-left"></span> Kembali</button></a>
            <div class="row">
                <div class="col-md-8">
                    <?php foreach($poData as $data2): ?>
                        <div class="col-md-6" style="padding-left:0">
                            <h3>Supplier: <?= makeFirstLetterUpper($data2->supplier); ?></h3>
                            <h4><?= makeFirstLetterUpper($data2->saddress); ?></h4>
                            <h4>Telp/Fax: <?= $data2->sphone; ?> / <?= $data2->sfax; ?></h4>
                            <h4>PIC: <span data-item="pic_supplier"><?= makeFirstLetterUpper($data2->pic_supplier); ?></span></h4>
                        </div>
                        <div class="col-md-6 text-left" style="padding-right:0">
                            <h3><span style="background-color:#EDCDC2;" data-item="po_number"><?= $data2->po_number; ?></span></h3>

                            <?php
                                if($data2->quo_number==null || empty($data2->quo_number)){
                                    $quoNumber = "-";
                                }else{
                                    if($data2->revision_number==null || empty($data2->revision_number)){
                                        $quoNumber = "<a href='/form/quo/detail?quo=$data2->id' target='_blank'>".$data2->quo_number."</a>";    
                                    }else{
                                        $quoNumber = "<a href='/form/quo/detail?quo=$data2->id&revision=$data2->revision_number' target='_blank'>".$data2->quo_number."/REV".$data2->revision_number."</a>";
                                    }
                                }
                            ?>

                            <h4>Quo: <?= $quoNumber; ?></h4>
                            <h4>PO DATE: <span data-item="po_date" data-item-val="<?= $data2->dd; ?>"><?= $data2->po_date; ?></span></h4>
                            <h4>PIC: <span data-item="pic_buyer"><?= makeFirstLetterUpper($data2->pic_buyer); ?></span></h4>
                            <h4>Telp/Fax: <?= $data2->bphone; ?> / <?= $data2->bfax; ?></h4>
                            <h4>Currency: <span data-item="currency" data-item-val=<?= $data2->cid; ?>><?= $data2->currency; ?></span></h4>
                        </div>
                        
                    <?php endforeach; ?>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Part Number</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price unit</th>
                                <th>Discount(%)</th>
                                <th>Price total</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        
                        <?php foreach($poDetailData as $data1): $item=(100-$data1->item_discount)*$data1->total*0.01; $priceTotal+=$item; ?>
                            <tr data-item=<?= $data1->id; ?>>
                                <td data-item="part_number" data-item-val=<?= $data1->part_number; ?>><?= $data1->part_number; ?></td>
                                <td data-item="product" data-item-val=<?= $data1->pid; ?>><?= $data1->product; ?></td>
                                <td data-item="quantity"><?= $data1->quantity; ?></td>
                                <td data-item="price_unit" data-item-val=<?= $data1->price_unit; ?> class="text-right"><?= formatRupiah($data1->price_unit); ?></td>
                                <td data-item="item_discount"><?= $data1->item_discount; ?></td>
                                <td data-item="total" data-item-val=<?= $item; ?> class="text-right"><?= formatRupiah($item); ?></td>
                                <td data-item="status"><?= $data1->status; ?></td>
                                <?php if($data1->status==0): ?>
                                    <?php if($poData[0]->quo==null || $poData[0]->quo=="" || empty($poData[0])): ?>
                                    <?php if($poData[0]->cbid==substr($_SESSION['sim-id'], 3, -3)): ?>
                                        <!-- Single button by Bootstrap -->
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="#" class="btn-modal btn-action" data-id="update-po-item"><span class="glyphicon glyphicon-pencil"></span> Update</a></li>
                                                    <li><a href="#" class="btn-modal btn-action" data-id="remove-po-item"><span class="glyphicon glyphicon-remove"></span> Remove</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <!-- <td class="text-center"><button type="button" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-pencil"></span> Update</button></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-remove"></span> Remove</button></td>  -->   
                                    <?php elseif($poData[0]->abid==substr($_SESSION['sim-id'], 3, -3)): ?>
                                        <!-- Single button by Bootstrap -->
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="#" class="btn-modal btn-action" data-id="approve-po-item"><span class="glyphicon glyphicon-ok"></span> Setuju</a></li>
                                                    <li><a href="#" class="btn-modal btn-action" data-id="reject-po-item"><span class="glyphicon glyphicon-remove"></span> Ditolak</a></li>
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
                                <?php else: ?>
                                    <td class="text-center">---</td>
                                <?php endif; ?>

                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <?php foreach($poData as $data2): ?>
                        <div style="margin-bottom:20px;">
                            <h4>Total: <?= formatRupiah($priceTotal); ?></h4>
                            <p>PPN: <span data-item="ppn" data-item-val=<?= $data2->ppn; ?> ><?= $data2->ppn; ?></span> %</p>
                            <p>Keterangan: <div data-item='remark'><?= ($data2->remark==null||empty($data2->remark))?"-":makeFirstLetterUpper($data2->remark); ?></div></p>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="label label-default">Acknowledged by: <?= $data2->acknowledged_by; ?></small>
                                    <small><?= $data2->acknowledged_at!=null?$data2->acknowledged_at."<span class='glyphicon glyphicon-ok'></span>":""; ?></small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small class="label label-default">Approval by: <?= $data2->approved_by; ?></small>
                                    <small><?= $data2->approved_at!=null?$data2->approved_at."<span class='glyphicon glyphicon-ok'></span>":""; ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <button type="button" class="btn btn-primary btn-sm btn-modal" id="print-po"><span class="glyphicon glyphicon-print"></span> Cetak</button>
                    <!-- <a target="_blank" href="/print/po?po=<?= $_GET['po']; ?>"><button type="button" class="btn btn-primary btn-modal" id="print-po"><span class="glyphicon glyphicon-print"></span> Cetak</button></a> -->
                    <button class="btn btn-sm btn-primary btn-modal" id="update-po"><span class="glyphicon glyphicon-edit"></span> Update PO</button>
                    <?php if($poData[0]->do==null || empty($poData[0]->do) || $poData[0]->do==''): ?>
                        <button class="btn btn-sm btn-danger btn-modal" id="remove-po"><span class="glyphicon glyphicon-edit"></span> Remove</button>
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
    $('#apr').trumbowyg();

    /* UPDATE PO ITEM */
    $(".btn-action").on("click", function(){
        var dataId= $(this).attr("data-id");

        var poItem = $(this).parent().closest("tr").attr("data-item");

        if(dataId=='update-po-item'){        
            var product = $(this).parent().closest("tr").find("[data-item~='product']").attr("data-item-val");
            var quantity = $(this).parent().closest("tr").find("[data-item~='quantity']").html();
            var priceUnit = $(this).parent().closest("tr").find("[data-item~='price_unit']").attr("data-item-val");
            var discountItem = $(this).parent().closest("tr").find("[data-item~='item_discount']").html();

            $("#modal-update-po-item").find("input[name~='po-item']").val(poItem);
            $("#modal-update-po-item").find("select[name~='product']").find("option").attr("selected", false);
            $("#modal-update-po-item").find("select[name~='product']").find("option[value~='"+product+"']").attr("selected", true);
            $("#modal-update-po-item").find("input[name~='quantity']").val(quantity); 
            $("#modal-update-po-item").find("input[name~='price_unit']").val(priceUnit);
            $("#modal-update-po-item").find("input[name~='item_discount']").val(discountItem);
        }else{
            $("#modal-remove-po-item, #modal-approve-po-item, #modal-reject-po-item").find("input[name~='po-item']").val(poItem);
        }
        
    });

    /* UPDATE PO FORM */
    $("#update-po").on("click", function(){
        var buyer = $("[data-item~='pic_buyer']").html();
        var poDate = $("[data-item~='po_date']").attr("data-item-val");
        var supplier = $("[data-item~='pic_supplier']").html();
        var currency = $("[data-item~='currency']").attr("data-item-val");
        var remark = $("[data-item~='remark']").html();
        var poNumber = $("[data-item~='po_number']").html();
        var ppn = $("[data-item~='ppn']").attr("data-item-val");

        $("#modal-update-po").find("input[name~='pic_buyer']").val(buyer);
        $("#modal-update-po").find("input[name~='pic_supplier']").val(supplier);
        $("#modal-update-po").find("input[name~='doc_date']").val(poDate);
        $("#modal-update-po").find("#remark").trumbowyg('html', remark);
		$("#modal-update-po").find("#remark").trumbowyg('html');
        //$("#modal-update-quo").find("select[name~='currency']").find("option").attr("selected", false);
        $("#modal-update-po").find("select[name~='currency']").find("option[value~='"+currency+"']").attr("selected", true);
        $("#modal-update-po").find("input[name~='po_number']").val(poNumber);
        $("#modal-update-po").find("input[name~='ppn']").val(ppn);
    })

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