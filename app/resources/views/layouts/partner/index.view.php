<?php

$titlePage="Partner";
define('base', 'app/resources/views/layouts/');
require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">
        
        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1>Partner</h1>
            <p>Halaman ini menangani info terkait partner dan customer</p>
            <button class="btn btn-sm btn-header btn-modal" id="btn-create-company"><span class="glyphicon glyphicon-pencil"></span> Tambahkan Partner</button>
        </header>

        <div class="sub-header"> 
            <form action="/partner" method="GET" style="display:inherit">    
                <input type="hidden" name="search" value="true">
                <div class="search" id="name-based">
                    <div class="form-group">
                        <select name="name" class="form-control">
                            <option value=''>Nama</option>
                            <?php foreach($partners as $partner): ?>
                                <option value=<?= $partner->id ?> title="<?= $partner->name; ?>"><?= makeItShort($partner->name, 50); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="product-based">
                    <div class="form-group">
                        <select name="product" class="form-control">
                            <option value=''>Produk</option>
                            <?php foreach($products as $product): ?>
                                <option value=<?= $product->id ?> title="<?= $product->name; ?>"><?= makeItShort($product->name, 50); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Cari</button> 
                </div>     
            </form>
        </div>

        <div class="info">
            <label><span class="glyphicon glyphicon-floppy-saved"></span> Jumlah data: <?= $sumOfAllData; ?></label>
        </div>

        <div class="app-form modal" id="modal-btn-create-company">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Tambahkan Partner</h3>
                </div>

                <form action="partner/create" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama" autofocus required>
                    </div>
                    <div class="form-group">
                        <label>Kode untuk dokumen</label>
                        <input type="text" name="code" class="form-control" placeholder="Kode untuk dokumen" required>
                    </div>
                    <div class="form-group">
                        <label>Badan usaha</label>
                        <select name="bussiness_entity" class="form-control">
                            <option value=''>BADAN USAHA</option>
                            <option value='1'>PT</option>
                            <option value='2'>CV</option>
                            <option value='3'>Individu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Propinsi</label>
                        <select name="province" class="form-control" required>
                        <option>PROPINSI</option>
                        <?php foreach($provinces as $province): ?>
                            <option value=<?= $province->id; ?> ><?= ucwords($province->province); ?> </option>
                        <?php endforeach; ?>               
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Alamat lengkap</label>
                        <input type="text" name="address" class="form-control" placeholder="Alamat" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor telepon</label>
                        <input type="text" name="phone" class="form-control" placeholder="Nomor telepon">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>  
                    <div class="form-group">
                        <label>Hubungan</label><br>
                        <input type="radio" data-rel="1" name="rel" value=1>Own <br>
                        <input type="radio" name="relationship" value=2 checked>Partner <br>
                        <input type="radio" name="relationship" value=3>Customer
                    </div>
                    <div class="form-group">
                        <label>Keterangan tambahan</label>
                        <textarea name="remark" class="form-control" placeholder="Keterangan tambahan"></textarea>
                    </div>
                    <input type="file" name="logo"><br>
                    <button type="submit" name="submit" class="btn btn-primary" style="float:right;"><span class="glyphicon glyphicon-send"></span> Kirim</button>
                                                                                     
                    <button class="btn btn-danger btn-close">Tutup</button>
                </form>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

            </div>
        </div>

        <div class="main-data">
            <?php if(count($partnerData)<1): ?>
                <div class="text-center">Belum terdapat data tersimpan</div>
            <?php else: ?>
                <div class="container-fluid">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Product</th>
                                    <th>Address</th>
                                    <th>Province</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Relation</th>
                                    <!-- <th>Active</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($partnerData as $data): ?>
                                    <?php 
                                        $product = explode('<br>', $data->product);
                                        $thisProduct = "";
                                        if(count($product)>3){
                                            $thisProduct.=$product[0].", ".$product[1].", etc";
                                        }else{
                                            $thisProduct = $data->product;
                                        }
                                    ?>
                                    <tr>
                                        <td><a href="/partner/detail?p=<?= $data->id ?>"><strong><?= ucwords($data->name).", ".$data->bussiness_entity; ?></strong></a></td>
                                        <td><?= $thisProduct; ?></td>
                                        <td><?= makeFirstLetterUpper($data->address); ?></td>
                                        <td><?= ucwords($data->province); ?></td>
                                        <td><?= $data->phone; ?></td>
                                        <td><?= $data->email; ?></td>
                                        <td><?= makeFirstLetterUpper($data->relationship); ?></td>
                                        <!-- <td><button type="button" name="toggle-class" class="btn btn-sm btn-default"><?= $data->active; ?></button></td> -->
                                    <tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif;?>
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

<script>
$("document").ready(function(){
    
    $("#btn-header").on("click",function(){
        $(this).parent().closest("main").find("#create-partner-form").css("display","block");
    });

    $(".btn-close").on("click",function(){
        $(this).parent().closest(".modal").css("display","none");
    })

});
</script>
<?php

require base.'base/footer.view.php';

?>

<script type="text/javascript">
    /*function detail(e){
        var thisPage=location.href;

        thisPage=thisPage.split("?")[0];

        var dataPartner=e.getAttribute('data-partner');
   
        location.assign(thisPage+'?partner='+dataPartner);
    }*/
</script>