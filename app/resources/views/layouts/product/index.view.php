<?php

$titlePage="Product dan Stok";
define('base', 'app/resources/views/layouts/');
require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1><?= $titlePage; ?></h1>
            <p>Halaman ini menangani info terkait kategori <?= $titlePage; ?> </p>
            <?php if(array_key_exists('superadmin' , $roleOfUser)): ?>
                <button class="btn btn-sm btn-header btn-modal" id="create-category"><span class="glyphicon glyphicon-pencil"></span> Tambahkan Kategori</button>
            <?php endif; ?>
            <button class="btn btn-sm btn-header btn-modal" id="create-vendor"><span class="glyphicon glyphicon-pencil"></span> Tambahkan Vendor</button>
            <button class="btn btn-sm btn-header btn-modal" id="create-product"><span class="glyphicon glyphicon-pencil"></span> Tambahkan Produk</button>
        </header>

        <div class="sub-header"> 
            <form action="/p-a" method="GET" style="display:inherit">    
                <input type="hidden" name="search" value="true">
                <div class="search" id="category-based">
                    <div class="form-group">
                        <select name="category" class="form-control">
                            <option value=''>KATEGORI</option>
                            <?php foreach($productCat as $cat): ?> 
                                <option value= <?= $cat->id; ?> title='<?= $cat->name; ?>' ><?= makeItShort(ucfirst($cat->name), 50); ?></option>             
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="vendor-based">
                    <div class="form-group">
                        <select name="vendor" class="form-control">
                            <option value=''>VENDOR</option>
                            <?php foreach($vendors as $vendor): ?> 
                                <option value= <?= $vendor->id; ?> title='<?= ucfirst($vendor->name); ?>' ><?= makeItShort(ucfirst($vendor->name), 50); ?></option>             
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="product-based">
                    <div class="form-group">
                        <select name="product" class="form-control">
                            <option value=''>PRODUK</option>
                            <?php foreach($products as $product): ?>
                                <option value=<?= $product->id ?> title='<?= ucfirst($product->name); ?>'><?= makeItShort(ucfirst($product->name), 50); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button> 
                </div>     
            </form>
        </div>

        <div class="info">
            <label><span class="glyphicon glyphicon-floppy-saved"></span> Jumlah data: <?= $sumOfAllData; ?></label>
        </div>

        <!-- PRODUCT FORM -->
        <div class="app-form modal" id="modal-create-product">   
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Tambahkan Produk</h3>
                </div>
                <div class="modal-main-content">
                    <div class="description">
                        <p>Form ini digunakan untuk menambahkan data produk.</p>
                    </div>
                    <form action="p-a/product/create" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="category" class="form-control" required>
                                <option value=''>KATEGORI</option>
                                <?php foreach($productCat as $cat): ?> 
                                    <option value= <?= $cat->id; ?> ><?= ucfirst($cat->name); ?></option>             
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Vendor</label>
                            <select name="vendor" class="form-control">
                                <option value=''>VENDOR</option>
                                <?php foreach($vendors as $vendor): ?> 
                                    <option value= <?= $vendor->id; ?> ><?= ucfirst($vendor->name); ?></option>             
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Part number</label>
                            <input type="text" name="part_number" class="form-control" placeholder="Part number" autofocus required>
                        </div>
                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" placeholder="Deskripsi"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Link</label>
                            <input type="text" name="link" class="form-control" placeholder="Link eksternal">
                        </div>
                        <div class="form-group">
                            <label>Gambar</label>
                            <input type="file" name="picture">
                        </div>         

                        <button class="btn btn-danger btn-close">Tutup</button>
                        <button type="submit" name="submit" class="btn btn-primary" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                </div>
            </div>  
        </div>

        <!-- VENDOR FORM -->
        <div class="app-form modal" id="modal-create-vendor">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Tambahkan Vendor</h3>
                </div>
                <div class="modal-main-content">
                    <div class="description">
                        <p>Form ini digunakan untuk menambahkan vendor.</p>
                    </div>
                    <form action="/p-a/vendor/create" method="POST">
                        <div class="form-group">
                            <label>Nama Vendor</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama" autofocus required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" placeholder="Deskripsi"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Link</label>
                            <input type="text" name="link" class="form-control" placeholder="Link eksternal">
                        </div>
                        <button class="btn btn-danger btn-close">Tutup</button>
                        <button type="submit" name="submit" class="btn btn-primary" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>
                </div>
            </div>  
        </div>

        <!-- CATEGORY FORM -->
        <?php if(array_key_exists('superadmin' , $roleOfUser)): ?>
            <div class="app-form modal" id="modal-create-category">   
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Tambahkan Kategori</h3>
                    </div>

                    <form action="p-a/category/create" method="POST">
                        <div class="form-group">
                            <label>Nama kategori</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama" autofocus required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" placeholder="Deskripsi"></textarea>
                        </div>                                                                             
                        <button class="btn btn-danger btn-close">Tutup</button>
                        <button type="submit" name="submit" class="btn btn-primary" style="float:right;"><span class="glyphicon glyphicon-send"></span> Kirim</button>
                    </form>
                </div>  
            </div>
        <?php endif; ?>

        <div class="main-data">
            <div class="container-fluid grid-view">
                <?php echo count($paData)<1?'<div class="text-center">Belum terdapat data tersimpan</div>':''; ?>
                <?php foreach($paData as $data): ?>
                <div>
                    <div class="cover-grid category fade-toggle-trigger">
                        <ul>
                            <li><strong><?= strtoupper($data->category); ?></strong></li>
                            <li><?= $data->description; ?></li>  
                        </ul>
                        <span class="glyphicon glyphicon-chevron-down arrow-down"></span>
                    </div>
                    
                    <?php $vendors=$data->vendors;?>

                    <div class="fade-toggle" style="max-width:200px; margin:auto">
                        <?php foreach($vendors as $vendor => $val): ?>
                            <div class="vendor">
                                <header><?= strtoupper($vendor); ?></header>
                                <div class="product">
                                    <ul>
                                    <?php for($j=0; $j<count($vendors[$vendor]); $j++): ?>  
                                        <li><?= $vendors[$vendor][$j][0]; ?><a href=<?= $vendors[$vendor][$j][1]; ?> class="text-right" target="_blank" style="float:right; display:inline-block"><span class="glyphicon glyphicon-new-window"></span></a></li> 
                                        <!-- <li><a href="//www.pertamina.com" target="_blank"><?= $vendors[$vendor][$j][0]; ?></a></li>  -->   
                                    <?php endfor; ?>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- START PAGINATION -->
            <?php 
                if($pages>1){
                    echo pagination($pages);
                }
            ?>
            <!-- END OF PAGINATION -->
            
        </div>

        
    </div>
</main>

<script type="text/javascript">
    $(document).ready(function(){
        $(".fade-toggle").hide();

        $(".fade-toggle-trigger").on("click", function(){
            $(this).next().fadeToggle();
        });
    })
</script>

<?php

require base.'base/footer.view.php';

?>