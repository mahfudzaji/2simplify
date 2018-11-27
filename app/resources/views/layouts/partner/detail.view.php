<?php

    $titlePage="Partner";
    define('base', 'app/resources/views/layouts/');
    require base.'base/header.view.php';

?>
<style>
    /* SRF */
    .partner-detail-menu{
        background:orange;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
    }

    .partner-detail-menu div a{
        color: white;
        box-sizing: border-box;
        display: block;
        padding: 20px;
    }

    .partner-detail-menu div:hover, .partner-detail-menu div.active{
        background:rgba(233, 75, 60, 0.5);
    }
</style>
<main>
    <div class="container-fluid">
        
        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1><?= strtoupper($partner[0]->name); ?></h1>
            <p><?php echo ucwords($partner[0]->province);echo "-"; echo ucfirst($partner[0]->remark); ?></p>
            <!-- <button class="btn btn-sm btn-header" onclick="toggleStatus()"><span class="glyphicon glyphicon-ok"></span> <?= ucfirst($partner[0]->status); ?></button> -->
        </header>

        <!-- <div class="sub-bar">
            <div class="sub-bar-right filter" data-filter-by="customer" data-filter-by-val=<?= $partner[0]->id; ?>>
                <button class="btn btn-sm btn-default btn-filter btn-ajax" id="filter-by-po">PO</button>
                <button class="btn btn-sm btn-default btn-filter btn-ajax" id="filter-by-do">DO</button>
                <button class="btn btn-sm btn-default btn-filter btn-ajax" id="filter-by-bast">BAST</button>
                <button class="btn btn-sm btn-default btn-filter btn-ajax" id="filter-by-rf">Tanda Terima</button>
                <button class="btn btn-sm btn-default btn-filter btn-ajax" id="filter-by-product">Produk</button>
            </div>
        </div> -->

        <div class="main-data">
            <div class="row">
                <div class="col-md-6 table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <td>Hubungan</td>
                                <td data-item="relationship" data-val=<?= $partner[0]->rid; ?>><?= ucfirst($partner[0]->relationship); ?></td>
                            </tr>
                            <tr>
                                <td>Badan usaha</td>
                                <td data-item="bussiness_entity" data-val=<?= $partner[0]->beid; ?>><?= ucfirst($partner[0]->bussiness_entity); ?></td>
                            </tr>
                            <tr>
                                <td>Nama</td>
                                <td data-item="name" ><?= ucfirst($partner[0]->name); ?></td>
                            </tr>
                            <tr>
                                <td>Kode pendokumentasian</td>
                                <td data-item="code" ><?= ucfirst($partner[0]->code); ?></td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td data-item="address"><?= ucfirst($partner[0]->address); ?></td>
                            </tr>
                            <tr>
                                <td>Propinsi</td>
                                <td data-item="province" data-val=<?= $partner[0]->pid; ?>><?= ucfirst($partner[0]->province); ?></td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td data-item="phone"><?= ucfirst($partner[0]->phone); ?></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td data-item="email"><?= ucfirst($partner[0]->email); ?></td>
                            </tr>
                            <tr>
                                <td>Keterangan</td>
                                <td data-item="remark"><?= ($partner[0]->remark==null || empty($partner[0]->remark))?"-":$partner[0]->remark; ?></td>
                            </tr>
                            <tr>
                                <td>Didaftarkan pada</td>
                                <td><?= ucfirst($partner[0]->created_at); ?></td>
                            </tr>
                            <tr>
                                <td>Didaftarkan oleh</td>
                                <td><?= ucfirst($partner[0]->created_by); ?></td>
                            </tr>
                            <tr>
                                <td>Diubah pada</td>
                                <td><?= ucfirst($partner[0]->updated_at); ?></td>
                            </tr>
                            <tr>
                                <td>Diubah oleh</td>
                                <td><?= ucfirst($partner[0]->updated_by); ?></td>
                            </tr>
                            
                        </tbody>
                    </table>
                    <button class="btn btn-primary btn-sm btn-modal" id="update-partner-form"><span class="glyphicon glyphicon-edit"></span> Update data</button>
                </div>
                <div class="col-md-6 data-filter">
                    <div class="partner-detail-menu">
                        <div data-menu="receive" class="menu active"><a href="#" class="partner-detail-menu-item">Tanda terima</a></div>
                        <div data-menu="quo" class="menu"><a href="#" class="partner-detail-menu-item">Quo</a></div>
                        <div data-menu="po" class="menu"><a href="#" class="partner-detail-menu-item">PO</a></div>
                        <div data-menu="do" class="menu"><a href="#" class="partner-detail-menu-item">DO</a></div>

                    </div>
                    <div class="partner-detail" id="partner-receive">
                        <h3>Daftar 10 tanda terima terakhir</h3>
                        <?php if(count($receiveData)<1): ?>
                            <p class="text-center">Belum terdapat data</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal diterima</th>
                                            <th>Diserahkan</th>
                                            <th>Diterima</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($receiveData as $data): ?>
                                            
                                            <tr>
                                                <td><a href="/form/tanda-terima/detail?r=<?= $data->id ?>"><?= $data->receive_date; ?></a></td>
                                                <td><?= ucfirst($data->submitted); ?></td>
                                                <td><?= ucfirst($data->received); ?></td>
                                            </tr>
                                            
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="partner-detail" id="partner-quo" style="display:none;">
                        <h3>Daftar 10 QUO terakhir</h3>
                        <?php if(count($quoData)<1): ?>
                            <p class="text-center">Belum terdapat data</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Number</th>
                                            <th>Penjual</th>
                                            <th>Pembeli</th>
                                            <th>Tanggal</th>
                                            <th>Produk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($quoData as $data): ?>
                                            <tr>
                                                <td><a href="/form/quo/detail?quo=<?= $data->id ?>"><?= ucfirst($data->quo_number); ?></a></td>
                                                <td><?= ucfirst($data->supplier); ?></td>
                                                <td><?= ucfirst($data->buyer); ?></td>
                                                <td><?= ucfirst($data->doc_date); ?></td>
                                                <?php $product = explode('<br>', $data->product); ?>
                                                <?php if(count($product)>3): ?>
                                                    <td><?= $product[0].', '.$product[1].', '.$product[count($product)-1]; ?></td>
                                                <?php else: ?>
                                                    <td><?= $data->product; ?></td>
                                                <?php endif; ?>
                                            </tr>                     
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="partner-detail" id="partner-po" style="display:none;">
                        <h3>Daftar 10 PO terakhir</h3>
                        <?php if(count($poData)<1): ?>
                            <p class="text-center">Belum terdapat data</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Number</th>
                                            <th>Penjual</th>
                                            <th>Pembeli</th>
                                            <th>Tanggal</th>
                                            <th>Produk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($poData as $data): ?>
                                            <tr>
                                                <td><a href="/form/po/detail?po=<?= $data->id ?>"><?= ucfirst($data->po_number); ?></a></td>
                                                <td><?= ucfirst($data->supplier); ?></td>
                                                <td><?= ucfirst($data->buyer); ?></td>
                                                <td><?= ucfirst($data->doc_date); ?></td>
                                                <?php $product = explode('<br>', $data->product); ?>
                                                <?php if(count($product)>3): ?>
                                                    <td><?= $product[0].', '.$product[1].', '.$product[count($product)-1]; ?></td>
                                                <?php else: ?>
                                                    <td><?= $data->product; ?></td>
                                                <?php endif; ?>
                                            </tr>                     
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="partner-detail" id="partner-do" style="display:none;">
                        <h3>Daftar 10 DO terakhir</h3>
                        <?php if(count($doData)<1): ?>
                            <p class="text-center">Belum terdapat data</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Number</th>
                                            <th>Penjual</th>
                                            <th>Pembeli</th>
                                            <th>Tanggal</th>
                                            <th>Produk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($doData as $data): ?>
                                            <tr>
                                                <td><a href="/form/do/detail?do=<?= $data->id ?>"><?= ucfirst($data->do_number); ?></a></td>
                                                <td><?= ucfirst($data->supplier); ?></td>
                                                <td><?= ucfirst($data->buyer); ?></td>
                                                <td><?= ucfirst($data->do_date); ?></td>
                                                <?php $product = explode('<br>', $data->product); ?>
                                                <?php if(count($product)>3): ?>
                                                    <td><?= $product[0].', '.$product[1].', '.$product[count($product)-1]; ?></td>
                                                <?php else: ?>
                                                    <td><?= $data->product; ?></td>
                                                <?php endif; ?>
                                            </tr>                     
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-form modal" id="modal-update-partner-form">    
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui data Partner</h3>
                </div>

                <form action="/partner/update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="p" value=<?= $partner[0]->id; ?> >
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
                        <select name="bussiness_entity" class="form-control" required>
                            <option value=''>BADAN USAHA</option>
                            <?php foreach($entities as $entity): ?>
                                <option value=<?= $entity->id; ?> ><?= $entity->bussiness_entity ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Propinsi</label>
                        <select name="province" class="form-control" required>
                        <option value=''>PROPINSI</option>
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
                        <input type="radio" data-rel="1" name="relationship" value=1>Own <br>
                        <input type="radio" data-rel="2" name="relationship" value=2 checked>Partner <br>
                        <input type="radio" data-rel="3" name="relationship" value=3>Customer
                    </div>
                    <div class="form-group">
                        <label>Keterangan tambahan</label>
                        <textarea name="remark" class="form-control" placeholder="Keterangan tambahan"></textarea>
                    </div>
                    <input type="file" name="logo"><br>
                    <button type="submit" name="submit" class="btn btn-primary" style="float:right;"><span class="glyphicon glyphicon-send"></span> Kirim</button>
                                                                                     
                    <button type="button" class="btn btn-danger btn-close">Tutup</button>
                </form>
                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>
            </div>  
        </div>

    </div>
</main>

<script>

function toggleStatus(){
    var p=location.href.split('?')[1].split('=')[1];
    var c=confirm("Anda yakin mengubah status partner?");
    if(c){
        $.post('/partner/status',{p:p},function(data, status){
            location.reload();
        }); 
    }
}

$(document).ready(function(){
    //MODAL
    $(".btn-modal").on("click", function(){
        //traversing... get the data and fill it in the form
        var data=$(".main-data").find("table").find("tbody");

        var placeholderPartner=['name', 'code','bussiness_entity', 'province', 'address', 'phone','email', 'remark'];
        var dataItem='';

        for(var i=0;i<placeholderPartner.length;i++){
            dataItem=data.find("td[data-item~='"+placeholderPartner[i]+"']");
            if(dataItem.attr('data-val')!=null){
                var value = dataItem.attr('data-val');
            }else{
                var value = dataItem.html();
            }
            $(".modal").find("form").find("[name~='"+placeholderPartner[i]+"']").val(value);
        }

        dataRel = data.find("td[data-item~='relationship']").attr("data-val");
        var rel= $(".modal").find("form").find("input[data-rel~="+dataRel+"]").attr('checked', true);

        $(".modal").css("display","block");
    });

    //srf detail menu item
    $(".partner-detail-menu-item").on("click", function(){
        
        $(".partner-detail").hide();

        $(this).closest(".partner-detail-menu").find(".menu").removeClass("active");

        var menu = $(this).closest("div").attr("data-menu");

        $(this).closest(".main-data").find("div[data-menu~='"+menu+"']").addClass("active");

        $(this).closest(".main-data").find("#partner-"+menu).show();
    });
});
</script>

<?php
    require base.'base/footer.view.php';
?>