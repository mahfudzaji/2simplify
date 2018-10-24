<?php

$titlePage="Product";
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
            <button class="btn btn-sm btn-header btn-modal" id="create-product"><span class="glyphicon glyphicon-pencil"></span> Tambahkan Item</button>
        </header>

        <div class="sub-header"> 
            <form action="/product" method="GET" style="display:inherit">    
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
                    <form action="product/create-product" method="POST" enctype="multipart/form-data">
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
                            <label>Kode/Part number</label>
                            <input type="text" name="part_number" class="form-control" placeholder="Part number" autofocus required>
                        </div>
                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" placeholder="Deskripsi" required></textarea>
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

                    <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

                </div>
            </div>  
        </div>

        <!-- UPDATE PRODUCT FORM -->
        <div class="app-form modal" id="modal-update-product">   
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Memperbaharui Produk</h3>
                </div>
                <div class="modal-main-content">
                    <div class="description">
                        <p>Form ini digunakan untuk memperbaharui data produk.</p>
                    </div>
                    <form action="product/update-product" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="pid" value"">
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
                            <label>Kode/Part number</label>
                            <input type="text" name="part_number" class="form-control" placeholder="Part number" autofocus required>
                        </div>
                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" placeholder="Deskripsi" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Link</label>
                            <input type="text" name="link" class="form-control" placeholder="Link eksternal">
                        </div>
                        <div class="form-group">
                            <label>Gambar</label>
                            <input type="file" name="picture">
                        </div>         

                        <button type="button" class="btn btn-danger btn-close">Tutup</button>
                        <button type="submit" name="submit" class="btn btn-primary" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </form>

                    <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

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

        <!-- UPDATE CATEGORY -->
        <div class="app-form modal" id="modal-update-category">   
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Tambahkan Kategori</h3>
                </div>

                <form action="/product/update-category" method="POST">
                    <input type="hidden" name="cid" value="">
                    <div class="form-group">
                        <label>Nama kategori</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama" autofocus required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" placeholder="Deskripsi" required></textarea>
                    </div>                                                                             
                    <button class="btn btn-danger btn-close">Tutup</button>
                    <button type="submit" name="submit" class="btn btn-primary" style="float:right;"><span class="glyphicon glyphicon-send"></span> Kirim</button>
                
                    <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

                </form>
            </div>  
        </div>

        <!-- CATEGORY FORM -->
        <?php if(array_key_exists('superadmin' , $roleOfUser)): ?>
            <div class="app-form modal" id="modal-create-category">   
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Tambahkan Kategori</h3>
                    </div>

                    <form action="/product/create-category" method="POST">
                        <div class="form-group">
                            <label>Nama kategori</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama" autofocus required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" placeholder="Deskripsi" required></textarea>
                        </div>                                                                             
                        <button class="btn btn-danger btn-close">Tutup</button>
                        <button type="submit" name="submit" class="btn btn-primary" style="float:right;"><span class="glyphicon glyphicon-send"></span> Kirim</button>
                    
                        <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

                    </form>
                </div>  
            </div>
        <?php endif; ?>

        <div class="main-data">
            <div class="container-fluid grid-view">
                <?php echo count($paData)<1?'<div class="text-center">Belum terdapat data tersimpan</div>':''; ?>
                <?php foreach($paData as $data): ?>
                <div data-item="product" id="<?= $data->catid; ?>">
                    <div class="cover-grid category fade-toggle-trigger" >
                        <span class="glyphicon glyphicon-edit btn-modal" data-id="update-category" style="right: 0;position: absolute; z-index:50"></span>
                        <ul>
                            <li data-item="category"><?= strtoupper($data->category); ?></li>
                            <li data-item="description"><?= $data->description; ?></li> 
                            <li><?= count($data->products); ?> Product</li> 
                        </ul>
                        <span class="glyphicon glyphicon-chevron-down arrow-down"></span>
                    </div>
                    
                    <?php $prods=$data->products;?>

                    <div class="fade-toggle" style="max-width:200px; margin:auto">
                        <?php for($j=0; $j<count($prods); $j++): ?> 
                            <div class="product">
                                <ul>
                                    <li class="btn-modal" id="<?= $prods[$j]['id']; ?>" data-pn="<?= $prods[$j]['part_number']; ?>" data-desc="<?= $prods[$j]['desc']; ?>" data-id="update-product"><span><?= $prods[$j]['prod']; ?></span><a href="<?= $prods[$j]['link']; ?>" class="text-right" target="_blank" style="float:right; display:inline-block"><span class="glyphicon glyphicon-new-window"></span></a></li>  
                                </ul>
                            </div>
                        <?php endfor; ?>
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

        $("li[data-id~='update-product']").on("click", function(){
            let pid = $(this).attr('id')
            let desc = $(this).attr('data-desc');
            let pn = $(this).attr('data-pn');
            let product =$(this).find("span").html();
            let link =$(this).find("a").attr("href");
            let category = $(this).closest("[data-item~='product']").attr("id");

            //console.log(desc+","+pn+","+product+","+link+","+category);
            
            $("#modal-update-product").find("select[name~='category']").find("option[value~='"+category+"']").attr("selected", true);
            $("#modal-update-product").find("input[name~='part_number']").val(pn);
            $("#modal-update-product").find("input[name~='name']").val(product);
            $("#modal-update-product").find("textarea[name~='description']").val(desc);
            $("#modal-update-product").find("input[name~='link']").val(link);
            $("#modal-update-product").find("input[name~='pid']").val(pid);

        });
        
        $("[data-id~='update-category']").on("click", function(){

            let cid = $(this).closest("[data-item~='product']").attr("id");
            let category = $(this).parent().find("[data-item~='category']").html();
            let desc = $(this).parent().find("[data-item~='description']").html();
            
            $("#modal-update-category").find("input[name~='cid']").val(cid);
            $("#modal-update-category").find("input[name~='name']").val(category);
            $("#modal-update-category").find("textarea[name~='description']").val(desc);

        });

    })
</script>

<?php

require base.'base/footer.view.php';

?>