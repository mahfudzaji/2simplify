<?php

$titlePage="Partner";
define('base', 'app/resources/views/layouts/');
require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1><?= strtoupper($category[0]->name); ?></h1>
            <p><?= ucfirst($category[0]->description); ?></p>
            <button class="btn btn-sm btn-header" id="btn-add-asset"><span class="glyphicon glyphicon-pencil"></span> Tambahkan Aset</button>
        </header>

        <div class="main-data">
            <div class="grid-view">
                <?php if(count($products)>0): ?>
                    <?php foreach($products as $product): ?>
                        <a href="/p-a/category?c=<?= $cat->id ?>" >
                        <div class="cover-grid">
                            <ul>
                                <li><?= $product->name; ?></li>
                                <li><?= $product->active==0?'Aktifkan':'Non-aktifkan'; ?></li>
                            </ul>
                        </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>

                    <p>Belum ada produk untuk kategori ini</p>

                <?php endif; ?>
            </div>
        </div>

        <div class="app-form modal" id="create-asset-form">   
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Tambahkan Aset</h3>
                </div>

                <form action="p-a/asset/create" method="POST">
                    <div class="form-group">
                        <label>Produk</label>
                        <select name="product" class="form-control">
                            <option value=''>PRODUK</option>
                            <?php foreach($productCat as $cat): ?> 
                                <option value= <?= $cat->id; ?> ><?= ucfirst($cat->name); ?></option>             
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Serial number</label>
                        <input type="text" name="serial_number" class="form-control" placeholder="Serial number">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary"><span class="glyphicon glyphicon-send"></span> Kirim</button>
                                                                                     
                    <button class="btn btn-danger btn-close" style="float:right;">Tutup</button>
                </form>
            </div>  
        </div>

    </div>
</main>
<?php

require base.'base/footer.view.php';

?>