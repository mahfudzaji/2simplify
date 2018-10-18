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
            <form action="/stock/history" method="GET" style="display:inherit">    
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
                <div class="search" id="date-based" style="position:relative">
                    <button type="button" class="btn btn-default" id="btn-date-based">TANGGAL</button>
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
                <div class="search">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button> 
                </div>     
            </form>
        </div>

        <div class="info">
            <label><span class="glyphicon glyphicon-floppy-saved"></span> Jumlah data: <?= $sumOfAllData; ?></label>
        </div>

        <div class="main-data" >
            <?php if(count($stockData)<1): ?>
                <div class="text-center">Belum terdapat data tersimpan</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Category</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Bukti</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($stockData as $data): ?>
                                <tr>
                                    <td><?= $data->receive_or_send_date; ?></td>
                                    <td><?= ucfirst($data->category); ?></td>
                                    <td><?= ucfirst($data->product); ?></td>
                                    <td><?= $data->quantity; ?></td>
                                    <td><a href="<?= $data->link; ?>" target="_blank"><?= $data->form_number; ?></a></td>
                                    <td><?= ucfirst($data->status); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div>
                    <a href="<?= (strpos($_SERVER['REQUEST_URI'], '?')==false)?rtrim($_SERVER['REQUEST_URI'],'/').'?download=true':rtrim($_SERVER['REQUEST_URI'],'/').'&download=true'; ?>" target="_blank"><button type="button" class="btn btn-md btn-primary"><span class="glyphicon glyphicon-download-alt"></span> Download</button></a>
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

        <div class="app-form modal" id="modal-stock-detail">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Detail <?= $titlePage; ?></h3>
                </div>
                <div class="description">
                    <p></p>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>  
                                <th>Tanggal</th>
                                <th>Nomor</th>
                                <th>Quantity</th>
                                <th>Status</th>
                            <tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-danger btn-close">Tutup</button>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>
        </div>
    </div>

</main>

<script type="text/javascript">
    
    $(document).ready(function(){

        $(".content-preview").on("click", function(){
            let category = $(this).attr("id");
            
            $('.content').removeClass('active');
            $('.content').children('.detail').hide();
            $(this).closest('.content').addClass('active');

            $.get("/stock/check-stock-category", {category:category}, function(data, status){
                let responds = JSON.parse(data);

                console.log(responds);
                
                let stockList = "<table class='table table-hover'><thead>";
				stockList += "<tr><th>Product</th><th>Stock in</th><th>Stock out</th><th>Total</th><th>Action</th></tr>";
                stockList += "</thead><tbody>";
                

                for(let i=0; i<responds.length; i++){
                    
                    let stockIn = Number(responds[i].stock_in)-Number(responds[i].stock_out)
                    let stockOut = Number(responds[i].stock_out)
                    let total = stockIn+stockOut

                    console.log(stockIn-stockOut)

                    stockList += "<tr id="+responds[i].pid+">";
                    stockList += "<td data-item='product'>"+responds[i].product+"</td>";
                    stockList += "<td data-item='stock-in' data-item-val="+stockIn+">"+stockIn+"</td>";
                    stockList += "<td data-item='stock-out' data-item-val="+stockOut+">"+stockOut+"</td>";
                    stockList += "<td data-item='total' data-item-val="+responds[i].ra+">"+total+"</td>";
                    stockList += "<td><button type='button' class='btn btn-link btn-action btn-modal' data-id='stock-detail'>More</button></td>";
                    //stockList += "<td><button type='button' class='btn btn-sm btn-primary btn-modal' data-id='update-stock'>More</button></td>";
                    stockList += "</tr>";
                }

                stockList += "</tbody></table>";

                $('.content.active').find('.detail').empty();
                $('.content.active').find('.detail').append(stockList); 

                //console.log(unitList);

            });

            $(this).closest('.content').children('.detail').show();

        })
        
        $(".content").on("click", ".btn-action", function(){
            let product = $(this).closest("tr").attr("id");
            let productName = $(this).closest("tr").find("td[data-item~='product']").html();
            $('#modal-stock-detail').find(".description").find("p").html(productName);

            $.get("/stock/detail", {product:product}, function(data, status){
                let responds = JSON.parse(data);
                console.log(responds);
                let stockList = '';
                for(let i=0; i<responds.length; i++){

                    stockList += "<tr>";
                    stockList += "<td>"+responds[i].created_at+"</td>";
                    stockList += "<td><a href='"+responds[i].link+"' target='_blank'>"+responds[i].form_number+"</a></td>";
                    stockList += "<td>"+responds[i].quantity+"</td>";
                    stockList += "<td>"+responds[i].status+"</td>";

                    stockList += "</tr>";
                }

                $('#modal-stock-detail').find('tbody').empty();
                $('#modal-stock-detail').find('tbody').append(stockList); 

            });
        });
    });

</script>

<?php

require base.'base/footer.view.php'

?>
