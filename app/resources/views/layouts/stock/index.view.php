<?php

$titlePage=returnMessage()['stock']['title'];

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>

        <header id="main-header">
            <h1><?= $titlePage; ?></h1>
            <p>Halaman ini menangani data terkait <?= $titlePage; ?></p>
            <!-- <button class="btn btn-sm btn-header btn-modal" id="create-stock"><span class="glyphicon glyphicon-pencil"></span> Tambahkan stok</button> -->
        </header>

        <div class="sub-header"> 
            <form action="/stock" method="GET" style="display:inherit">    
                <input type="hidden" name="search" value="true">
                <div class="search" id="product-based">
                    <div class="form-group">
                        <select name="category" class="form-control">
                            <option value=''>Category</option>
                            <?php foreach($category as $cat): ?>cat
                                <option title="<?= $cat->name; ?>" value=<?= $cat->id ?>><?= makeItShort($cat->name, 50); ?></option>
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

        <div class="main-data" >
            <?php if(count($stocksData)<1): ?>
                <div class="text-center">Belum terdapat data tersimpan</div>
            <?php else: ?>
                <div class="container-fluid">
                    <?php foreach($stocksData as $data): ?>
                        <div class='content' style="width:55%; margin:5px auto;">
                            <div style="border-bottom:2px solid;cursor:pointer;background-color:#ffffe6;">
                                <div class="content-preview row" id="<?= $data->cid; ?>">
                                    <div class="col-md-4">
                                        <!-- <img src='/public/upload/<?= $data->pic; ?>' class="img-responsive"> -->
                                    </div>
                                    <div class="col-md-8">
                                        <h2><?= $data->category; ?></h2>
                                        <p><?= $data->description; ?></p>
                                        <p>IN: <?= $data->stock_in+$data->qty_pro_in+$data->qty_receipt_in; ?></p>
                                        <p>OUT: <?= $data->stock_out+$data->qty_pro_out+$data->qty_receipt_out; ?></p>
                                    </div>
                                </div>
                                <div class='table-responsive detail' style="background-color:#fff;">
                                </div>
                            </div> 
                        </div>

                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <!-- START PAGINATION -->
            <?php 
                if($pages>1){
                    echo pagination($pages);
                }
            ?>
            <!-- END OF PAGINATION -->

        </div>

    </div>

    <div class="app-form modal" id="modal-create-stock">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Menambahkan data <?= $titlePage; ?></h3>
                </div>
                <!--
                dropdown->nama vendor
                dropdown->nama product
                input text->serial number
                            ada tombol tambahkan serial number
                input date
                tombol submit  
                -->
                <form action="/stock/new-stock" method="POST">
                    <div class="form-group">
                        <label>Service point</label>
                        <select name="service_point" class="form-control" required>
                            <option value=''>SERVICE POINT</option>
                            <?php foreach($servicePoints as $sp): ?>
                                <option value=<?= $sp->id ?> ><?= ucfirst($sp->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Vendor</label>
                        <select name="vendor" class="form-control" required>
                            <option value=''>VENDOR</option>
                            <?php foreach($vendors as $vendor): ?> 
                                <option value= <?= $vendor->id; ?> ><?= ucfirst($vendor->name); ?></option>             
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Produk</label>
                        <select name="product" class="form-control" required>
                            <option value=''>PRODUCT</option>
                        </select>
                    </div>
                    <div class="detail-respond row" style="margin-bottom:5px;">
                        <div class="col-md-4"></div>
                        <div class="col-md-8"></div>
                    </div>
                    <div class="form-group">
                        <label>Tanggal diterima</label>
                        <input type="date" name="received_at" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Serial number</label>
                        <input type="text" name="serial_number[]" class="form-control" placeholder="Serial number" required>
                        <button type="button" class="btn btn-default" id="btn-add-sn" style="margin-top:5px">Tambah</label> 
                    </div>
                    <div class="form-group">
                        <label>Kepemilikan</label>
                        <select name="ownership" class="form-control" required>
                            <option value=''>Kepemilikan</option>
                            <option value='0'>Unit pinjaman</option>
                            <option value='1'>Milik sendiri</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="notes" class="form-control" placeholder="Keterangan"></textarea>
                    </div>     

                    <button type="button" class="btn btn-danger btn-close">Tutup</button>
                    <button type="submit" class="btn btn-primary" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                </form>

                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>

</main>

<script type="text/javascript">
    
    $(document).ready(function(){
        
        $("#btn-add-sn").on("click", function(){
            var clone = $(this).parent().find("input[name~='serial_number[]']:first").clone().val("");
            $(this).before(clone);
        });

        $("#modal-create-stock").on("change", "select[name~='vendor']", function(){
            var vendor = $(this).val();
            
            $.get('/stock/getProduct', {vendor:vendor}, function(data, status){
                var products = JSON.parse(data);
                var productOption = "<option value=''>PRODUCT</option>";
                
                for(var i=0; i<products.length; i++){
                    productOption+="<option value="+products[i].id+">"+products[i].name+"</option>";
                }
                //console.log(productOption);
                $("select[name~='vendor']").closest("form").find("select[name~='product']").empty().append(productOption);
            });
            
        });

        //show the detail of the product
        $("#modal-create-stock").on("change", "select[name~='product']", function(){
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
        });

        $(".content-preview").on("click", function(){
            let product = $(this).attr("id");
            
            $('.content').removeClass('active');
            $('.content').children('.detail').hide();
            $(this).closest('.content').addClass('active');

            $.get("/stock/get-stock-list", {product:product}, function(data, status){
                let responds = JSON.parse(data);
                
                let unitList = "<table class='table table-hover'><thead>";
				unitList += "<tr><th>Serial number</th><th>Stock condition</th><th>Location</th><th>Receive date</th><th>Send date</th><th>Status</th><th>Action</th></tr>";
                unitList += "</thead><tbody>";
                

                for(let i=0; i<responds.length; i++){
                    unitList += "<tr id="+responds[i].id+">";
                    unitList += "<td data-item='serial-number'>"+responds[i].serial_number+"</td>";
                    unitList += "<td>"+makeFirstLetterUpper(responds[i].stock_condition)+"</td>";
                    unitList += "<td data-item='service-point' data-item-val="+responds[i].idsp+">"+makeFirstLetterUpper(responds[i].service_point)+"</td>";
                    unitList += "<td data-item='receive-date' data-item-val="+responds[i].ra+">"+responds[i].received_at+"</td>";
                    unitList += "<td data-item='send-date' data-item-val="+responds[i].sa+">"+responds[i].send_at+"</td>";
                    unitList += "<td>"+makeFirstLetterUpper(responds[i].status)+"</td>";
                    unitList += "<td><button type='button' class='btn btn-sm btn-primary btn-modal' data-id='update-stock'>More</button></td>";
                    unitList += "</tr>";
                }

                unitList += "</tbody></table>";

                /* $('.active>.detail').empty();
                $('.active>.detail').append(unitList); */
                $('.content.active').find('.detail').empty();
                $('.content.active').find('.detail').append(unitList);

                console.log(unitList);

            });

            $(this).closest('.content').children('.detail').show();

        })

    });

</script>

<?php

require base.'base/footer.view.php'

?>
