<?php

$titlePage="Quotation Form";

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
            <button class="btn btn-sm btn-header btn-modal" id="create-new-item"><span class="glyphicon glyphicon-plus"></span> Item baru</button>
            <button class="btn btn-sm btn-header btn-modal btn-modal-ajax" id="show-notes"><span class="glyphicon glyphicon-star"></span> Catatan</button>
            <button class="btn btn-sm btn-header btn-modal" id="create-attachment"><span class="glyphicon glyphicon-paperclip"></span> Lampiran</button>
            <button class="btn btn-sm btn-header btn-modal" id="show-revision"><span class="glyphicon glyphicon-edit"></span> Revisi</button>

        </header>

        <!-- SHOW NOTES -->
        <div class="app-form modal" id="modal-show-notes">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Catatan</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/notes/create" method="POST">
                        <input type="hidden" name="document_number" value=<?= $_GET['quo']; ?>>
                        <input type="hidden" name="document_type" value=9>
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
                        <input type="hidden" name="document_data" value=<?= $quoData[0]->ddata; ?>>
                        <div class="form-group">
                            <label>Lampiran</label>
                            <textarea class="form-control" name="description" placeholder="Tuliskan deskripsi lampiran..." required></textarea>
                        </div>
                        <div class="form-group">
                            <!-- <select name="attachment" class="form-control select-ajax" required>
                                <option value=''>PILIH LAMPIRAN</option>
                                <?php foreach($uploadFiles as $uploadFile): ?>
                                    <option value=<?= $uploadFile->id; ?>><?= $uploadFile->title; ?></option>
                                <?php endforeach; ?>
                            </select> -->
                            <input type="file" name="attachment" required>
                        </div>
                        <div class="image-appear"></div>
                        <br>
                        <button type="submit" class="btn btn-primary pull-right">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                </div>
                <button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- UPDATE QUO FORM -->
        <div class="app-form modal" id="modal-update-quo">
            <div class="modal-content" style="width:50%;">
                <div class="modal-header">
                    <h3>Update Data <?= $titlePage; ?></h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui data <?= $titlePage; ?>.</p>
                    <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan</p>
                </div>
                <form action="/form/quo/update" method="post">
                    <input type="hidden" name="quo" value=<?= $_GET['quo']; ?>>

                    <div class="form-group">
                        <label>Quotation number</label>
                        <input type="text" class="form-control" name="quo_number" placeholder="Quotation number" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal quo</label>
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

        <!-- REMOVE QUO FORM -->
        <div class="app-form modal" id="modal-remove-quo">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/quo/remove" method="post">
                        <input type="hidden" name="quo" value=<?= $_GET['quo']; ?>>
                        <button type="submit" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Hapus data</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- UPDATE QUO ITEM -->
        <div class="app-form modal" id="modal-update-quo-item">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui Data <?= $titlePage; ?></h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui data <?= $titlePage; ?>.</p>
                    <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan</p>
                </div>
                <form action="/form/quo/update-item" method="post">
                    <input type="hidden" name="quo-item" value="">
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
                        <label>Discount (%)</label>
                        <input type="number" name="item_discount" min=0 class="form-control" required>
                    </div>

                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>

                    <div class="nav-right">
                        <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </div>
                </form>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- REMOVE QUO ITEM -->
        <div class="app-form modal" id="modal-remove-quo-item">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/quo/remove-item" method="post">
                        <input type="hidden" name="quo-item" value="">
                        <button type="submit" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Hapus data</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- APPROVAL QUO FORM -->
        <div class="app-form modal" id="modal-approve-quo-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/quo/approve" method="post">
                        <input type="hidden" name="quo-item" value="">
                        <input type="hidden" name="approval" value="1">
                        <input type="hidden" name="quo" value=<?= $_GET['quo']; ?>>
                        <button type="submit" class="btn btn-success btn-sm form-control"><span class="glyphicon glyphicon-ok"></span> Setuju</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- REJECT QUO FORM -->
        <div class="app-form modal" id="modal-reject-quo-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/quo/approve" method="post">
                        <input type="hidden" name="quo-item" value="">
                        <input type="hidden" name="approval" value="2">
                        <input type="hidden" name="quo" value=<?= $_GET['quo']; ?>>
                        <button type="submit" name="reject" class="btn btn-danger btn-sm form-control"><span class="glyphicon glyphicon-remove"></span> Ditolak</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- REVISION QUO FORM -->
        <div class="app-form modal" id="modal-revision-quo-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Konfirmasi</h3>
                </div>
                <div class="modal-main-content">
                    <form action="/form/quo/approve" method="post">
                        <input type="hidden" name="quo-item" value="">
                        <input type="hidden" name="approval" value="3">
                        <input type="hidden" name="quo" value=<?= $_GET['quo']; ?>>
                        <button type="submit" name="revision" class="btn btn-warning btn-sm form-control"><span class="glyphicon glyphicon-edit"></span> Perlu Revisi</button>
                    </form>
                </div>
                <br><button class="btn btn-danger btn-close clear" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
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
                <form action="/form/quo/new-item" method="post">
                    <input type="hidden" name="quo" value=<?= $_GET['quo']; ?>>
                    <?= (isset($_GET['revision']) && !empty($_GET['revision']))?"<input type='hidden' name='revision' value=".$_GET['revision'].">":"" ?>
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
                        <label>Discount (%)</label>
                        <input type="number" name="item_discount" min=0 class="form-control" required>
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

        <!-- QUOTATION REVISION -->
        <div class="app-form modal" id="modal-create-revision">
            <div class="modal-content" style="width:60%;">
                <div class="modal-header">
                    <h3>Revisi Data <?= $titlePage; ?></h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk merevisi data <?= $titlePage; ?>.</p>
                    <p><span style="color:red;">*</span>Catatan: <br> Setelah mengirim form, kemudian upload bukti dan beri notes jika diperlukan</p>
                </div>
                <form action="/form/quo/create-revision" method="post">
                    <input type="hidden" name="quo" value=<?= $_GET['quo']; ?>>
                    <div class="form-group">
                        <label>Tanggal revisi</label>
                        <input type="date" name="doc_date" class="form-control" required>
                    </div>
                    <div class="row">
                        <input type="hidden" name="quo-item[]" value="">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Product</label>
                                <select name="product[]" class="form-control" required>
                                    <option value=''>PRODUK</option>
                                    <?php foreach($products as $product): ?>
                                        <option value=<?= $product->id ?> title="<?= $product->description ?>" ><?= ucfirst($product->name).'|'.strtoupper($product->part_number); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" name="quantity[]" min=0 class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Price unit</label>
                                <input type="number" name="price_unit[]" min=0 class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Discount (%)</label>
                                <input type="number" name="item_discount[]" min=0 class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>

                    <div class="nav-right">
                        <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </div>
                </form>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- SHOW REVISION -->
        <div class="app-form modal" id="modal-show-revision">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Daftar Revisi</h3>
                </div>
                <div class="modal-main-content">
                    <div id="revision">
                        <?php if($countDataQuoRevision>0): ?>
                            <?php for($i=1; $i<=$countDataQuoRevision; $i++): ?>
                                <a href="/form/quo/detail?quo=<?= $_GET['quo']; ?>&revision=<?= $i; ?>" target="_blank"><?= $quoData[0]->quo_number."/Rev".$i; ?></a><br>
                            <?php endfor; ?>
                        <?php else: ?>
                            <p>Belum terdapat revisi </p>
                        <?php endif; ?>
                    </div>
                </div>
                <br> 
                <button class="btn btn-danger btn-close" >Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

        <!-- MAIN -->
        <div class="main-data" data-number=<?= $_GET['quo']; ?> data-document=9>
            <div class="row">
                <div class="col-md-8">
                    <?php foreach($quoData as $data2): ?>
                        <div class="col-md-6" style="padding-left:0">
                            <h3>Buyer: <?= makeFirstLetterUpper($data2->buyer); ?></h3>
                            <h4><?= $data2->baddress; ?></h4>
                            <h4>Telp/Fax: <?= $data2->bphone; ?> / <?= $data2->bfax; ?></h4>
                            <h4>PIC: <span data-item="pic_buyer"><?= $data2->pic_buyer; ?></span></h4>
                        </div>
                        <div class="col-md-6 text-left" style="padding-right:0">
                            <h3><span style="background-color:#95DEE3;" data-item="quo_number"><?= (isset($_GET['revision']) && !empty($_GET['revision']))?$data2->quo_number."/Rev".$_GET['revision']:$data2->quo_number ?></span></h3>
                            <h4>QUO DATE: <span data-item="quo_date" data-item-val="<?= $data2->dd; ?>"><?= $data2->quo_date; ?></span></h4>
                            <h4>PIC: <span data-item="pic_supplier"><?= $data2->pic_supplier; ?></span></h4>
                            <h4>Telp/Fax: <?= $data2->sphone; ?> / <?= $data2->sfax; ?></h4>
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
                        
                        <?php foreach($quoDetailData as $data1): $item=(100-$data1->item_discount)*$data1->total*0.01; $priceTotal+=$item; ?>
                            <tr class="quo-item" data-item=<?= $data1->id; ?>>
                                <td data-item="part_number" data-item-val=<?= $data1->part_number; ?>><?= $data1->part_number; ?></td>
                                <td data-item="product" data-item-val=<?= $data1->pid; ?>><?= $data1->product; ?></td>
                                <td data-item="quantity"><?= $data1->quantity; ?></td>
                                <td data-item="price_unit" data-item-val=<?= $data1->price_unit; ?> class="text-right"><?= formatRupiah($data1->price_unit); ?></td>
                                <td data-item="item_discount"><?= $data1->item_discount; ?></td>
                                <td data-item="total" data-item-val=<?= $item; ?> class="text-right"><?= formatRupiah($item); ?></td>
                                <td data-item="status"><?= $data1->status; ?></td>
                                
                                <!-- Creator -->
                                <?php if($quoData[0]->cbid==substr($_SESSION['sim-id'], 3, -3)): ?>
                                    <?php if($data1->sid==0 || $data1->sid==3): ?>
                                    <!-- Single button by Bootstrap -->
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" class="btn-modal btn-action" data-id="update-quo-item"><span class="glyphicon glyphicon-pencil"></span> Update</a></li>
                                                <li><a href="#" class="btn-modal btn-action" data-id="remove-quo-item"><span class="glyphicon glyphicon-remove"></span> Remove</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <?php else: ?>
                                        <td class="text-center">---</td>
                                    <?php endif; ?> 

                                
                                <!-- Approval -->
                                <?php elseif($quoData[0]->abid==substr($_SESSION['sim-id'], 3, -3)): ?>
                                    <!-- Single button by Bootstrap -->
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" class="btn-modal btn-action" data-id="approve-quo-form"><span class="glyphicon glyphicon-ok"></span> Setuju</a></li>
                                                <li><a href="#" class="btn-modal btn-action" data-id="revision-quo-form"><span class="glyphicon glyphicon-edit"></span> Perlu revisi</a></li>
                                                <li><a href="#" class="btn-modal btn-action" data-id="reject-quo-form"><span class="glyphicon glyphicon-remove"></span> Ditolak</a></li>
                                            </ul>
                                        </div>
                                    </td>

                                <?php else: ?>
                                    <td class="text-center">---</td>
                                <?php endif; ?>

                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <?php foreach($quoData as $data2): ?>
                        <div style="margin-bottom:20px;">
                            <h4>Total: <?= formatRupiah($priceTotal); ?></h4>
                            <p>PPN: <span data-item="ppn" data-item-val=<?= $data2->ppn; ?> ><?= $data2->ppn; ?></span> %</p>
                            <p><strong>Keterangan:</strong> <div data-item="remark"><?= ($data2->remark==null||empty($data2->remark))?"-":makeFirstLetterUpper($data2->remark); ?></div></p>
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
                            <hr>
                            <p>Last updated :  <?= $data2->updated_at; ?></p>
                            <p>Updated by : <?= $data2->updated_by; ?></p>
                        </div>

                    <?php endforeach; ?>
                    <a target="_blank" href=/print/quotation?quo=<?php echo (isset($_GET['revision']) && !empty($_GET['revision']))?$_GET['quo']."&revision=".$_GET['revision']:$_GET['quo'] ?>><button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-print"></span> Cetak</button></a>
                    <button class="btn btn-sm btn-primary btn-modal" id="update-quo"><span class="glyphicon glyphicon-edit"></span> Update quo</button>
                    <button class="btn btn-sm btn-primary btn-modal" id="create-revision"><span class="glyphicon glyphicon-edit"></span> Buat revisi</button>
                    <?php if($quoData[0]->po==null || empty($quoData[0]->po) || $quoData[0]->po==''): ?>
                        <button class="btn btn-sm btn-danger btn-modal" id="remove-quo"><span class="glyphicon glyphicon-edit"></span> Remove</button>
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

    /* UPDATE QUO ITEM */
    $(".btn-action").on("click", function(){
        var dataId= $(this).attr("data-id");

        var quoItem = $(this).parent().closest("tr").attr("data-item");
        var product='';
        var quantity='';
        var priceUnit='';
        var itemDiscount='';

        if(dataId=='update-quo-item'){     
             
            product = $(this).parent().closest("tr").find("[data-item~='product']").attr("data-item-val");
            quantity = $(this).parent().closest("tr").find("[data-item~='quantity']").html();
            priceUnit = $(this).parent().closest("tr").find("[data-item~='price_unit']").attr("data-item-val");
            itemDiscount = $(this).parent().closest("tr").find("[data-item~='item_discount']").html();

            $("#modal-update-quo-item").find("input[name~='quo-item']").val(quoItem);
            $("#modal-update-quo-item").find("select[name~='product']").find("option").attr("selected", false);
            $("#modal-update-quo-item").find("select[name~='product']").find("option[value~='"+product+"']").attr("selected", true);
            $("#modal-update-quo-item").find("input[name~='quantity']").val(quantity); 
            $("#modal-update-quo-item").find("input[name~='price_unit']").val(priceUnit);
            $("#modal-update-quo-item").find("input[name~='item_discount']").val(itemDiscount);
 
            
        }else{
            $("#modal-remove-quo-item, #modal-approve-quo-item, #modal-reject-quo-item, #modal-revision-quo-item").find("input[name~='quo-item']").val(quoItem);
        }
        
    });

    /* UPDATE QUO FORM */
    $("#update-quo").on("click", function(){
        var buyer = $("[data-item~='pic_buyer']").html();
        var quoDate = $("[data-item~='quo_date']").attr("data-item-val");
        var supplier = $("[data-item~='pic_supplier']").html();
        var currency = $("[data-item~='currency']").attr("data-item-val");
        var ppn = $("[data-item~='ppn']").attr("data-item-val");
        var remark = $("[data-item~='remark']").html();
        var quoNumber = $("[data-item~='quo_number']").html();

        $("#modal-update-quo").find("input[name~='pic_buyer']").val(buyer);
        $("#modal-update-quo").find("input[name~='pic_supplier']").val(supplier);
        $("#modal-update-quo").find("input[name~='doc_date']").val(quoDate);
        $("#modal-update-quo").find("#remark").trumbowyg('html', remark);
		$("#modal-update-quo").find("#remark").trumbowyg('html');
        //$("#modal-update-quo").find("select[name~='currency']").find("option").attr("selected", false);
        $("#modal-update-quo").find("select[name~='currency']").find("option[value~='"+currency+"']").attr("selected", true);
        $("#modal-update-quo").find("input[name~='quo_number']").val(quoNumber);
        $("#modal-update-quo").find("input[name~='ppn']").val(ppn);

    });

    $("#create-revision").on("click", function(){
        $("#modal-create-revision").find("form").find(".row:not(:first)").remove();
        var total = $(this).closest(".main-data").find(".quo-item").length;
        var quoItem = "";
        for(var i=1; i<=total; i++){
            var thisQuoItem = $(".quo-item:nth-of-type("+i+")");
            
            var quoItem = thisQuoItem.attr("data-item");
            var product = thisQuoItem.find("[data-item~='product']").attr("data-item-val");
            var quantity = thisQuoItem.find("[data-item~='quantity']").html();
            var priceUnit = thisQuoItem.find("[data-item~='price_unit']").attr("data-item-val");
            var itemDiscount = thisQuoItem.find("[data-item~='item_discount']").html();

            if(i==1){
                $("#modal-create-revision").find("input[name~='quo-item[]']").val(quoItem);
                $("#modal-create-revision").find("select[name~='product[]']").find("option").attr("selected", false);
                $("#modal-create-revision").find("select[name~='product[]']").find("option[value~='"+product+"']").attr("selected", true);
                $("#modal-create-revision").find("input[name~='quantity[]']").val(quantity); 
                $("#modal-create-revision").find("input[name~='price_unit[]']").val(priceUnit);
                $("#modal-create-revision").find("input[name~='item_discount[]']").val(itemDiscount); 
            }else{
                var x=$("#modal-create-revision").find(".row:first").clone();
                x.find("input[name~='quo-item[]']").val(quoItem);
                x.find("select[name~='product[]']").find("option").attr("selected", false);
                x.find("select[name~='product[]']").find("option[value~='"+product+"']").attr("selected", true);
                x.find("input[name~='quantity[]']").val(quantity); 
                x.find("input[name~='price_unit[]']").val(priceUnit);
                x.find("input[name~='item_discount[]']").val(itemDiscount); 
                $("#modal-create-revision").find("form").find(".row:last").after(x);
            }
            
        }
    });
});
</script>
<?php

require base.'base/footer.view.php'

?>