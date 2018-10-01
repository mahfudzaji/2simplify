<?php

$titlePage=returnMessage()['project']['title'];

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>

        <header id="main-header">
            <h1><?= $titlePage; ?></h1>
            <p>Halaman ini menangani data terkait <?= $titlePage; ?></p>
            <button class="btn btn-sm btn-header btn-modal" id="create-project"><span class="glyphicon glyphicon-pencil"></span> Tambahkan project</button>
        </header>

        <div class="sub-header"> 
            <form action="/stock" method="GET" style="display:inherit">    
                <input type="hidden" name="search" value="true">
                <div class="search" id="product-based">
                    <div class="form-group">
                        <select name="product" class="form-control">
                            <option value=''>PIC</option>
                            <?php foreach($users as $user): ?>
                                <option title="<?= $user->name; ?>" value=<?= $user->id ?>><?= (strlen($user->name)>50)?substr(ucfirst($user->name),0, 50)."...":ucfirst($user->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="search" id="date-based" style="position:relative">
                    <button type="button" class="btn btn-default" id="btn-date-based">TANGGAL PROJECT</button>
                    <div class="form-group" style="position: absolute;left: 50%;margin-top: 5px;transform: translateX(-50%);z-index: 5;display: none;width: 400px;">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="date_start" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="date_end" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search" id="po-based">
                    <div class="form-group">
                        <select name="po" class="form-control">
                            <option value=''>Ada PO?</option>
                            <option value=1>Ada</option>
                            <option value=0>Tidak</option>
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

        <div class="main-data">
            <?php if(count($projectData)<1): ?>
                <div class="text-center">Belum terdapat data tersimpan</div>
            <?php else: ?>
                <div class="container-fluid">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>PIC</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($projectData as $data): ?>
                                    <tr>
                                        <td><a href="/project/detail?p=<?= $data->id ?>"><strong><?= ucwords($data->name); ?></strong></a></td>
                                        <td><?= $data->start_date; ?></td>
                                        <td><?= $data->end_date ?></td>
                                        <td><?= $data->pic; ?></td>
                                        <td><?= $data->status; ?></td>
                                    <tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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

    <div class="app-form modal" id="modal-create-project">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Menambahkan data <?= $titlePage; ?></h3>
                </div>
                <form action="/project/create" method="POST">
                    <div class="form-group">
                        <label>Project</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama project" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" placeholder="Deskripsi"></textarea>
                    </div>    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Dimulai</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Berakhir</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label>PIC</label>
                        <select name="pic" class="form-control" required>
                            <option value=''>PIC</option>
                            <?php foreach($users as $pic): ?> 
                                <option value= <?= $pic->id; ?> ><?= ucfirst($pic->name); ?></option>             
                            <?php endforeach; ?>
                        </select>
                    </div>   
                    <button type="button" class="btn btn-danger btn-close">Tutup</button>
                    <button type="submit" class="btn btn-primary" style="float:right;">Kirim <span class="glyphicon glyphicon-send"></span></button>
                </form>

                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
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
				unitList += "<tr><th>Serial number</th><th>Stock condition</th><th>Location</th><th>Receive date</th><th>Send date</th><th>Status</th><th>Update</th></tr>";
                unitList += "</thead><tbody>";
                

                for(let i=0; i<responds.length; i++){
                    unitList += "<tr id="+responds[i].id+">";
                    unitList += "<td data-item='serial-number'>"+responds[i].serial_number+"</td>";
                    unitList += "<td>"+makeFirstLetterUpper(responds[i].stock_condition)+"</td>";
                    unitList += "<td data-item='service-point' data-item-val="+responds[i].idsp+">"+makeFirstLetterUpper(responds[i].service_point)+"</td>";
                    unitList += "<td data-item='receive-date' data-item-val="+responds[i].ra+">"+responds[i].received_at+"</td>";
                    unitList += "<td data-item='send-date' data-item-val="+responds[i].sa+">"+responds[i].send_at+"</td>";
                    unitList += "<td>"+makeFirstLetterUpper(responds[i].status)+"</td>";
                    unitList += "<td><button type='button' class='btn btn-sm btn-primary btn-modal' data-id='update-stock'>Update</button></td>";
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
