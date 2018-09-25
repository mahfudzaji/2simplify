<?php

$titlePage="Upload";
define('base', 'app/resources/views/layouts/');
require base.'base/header.view.php';

?>

<main>
    <div class="container-fluid">

        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1>Upload Data</h1>
            <p>Halaman ini menangani info terkait upload data </p>
            <button class="btn btn-sm btn-header btn-modal" id="upload"><span class="glyphicon glyphicon-pencil"></span> Upload</button>
        </header>

        <div class="main-data">
            <div class="grid-view">
                
            </div>
        </div>

        <!-- UPLOAD FORM -->
        <div class="app-form modal" id="modal-upload">   
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Upload data</h3>
                </div>
                <div class="modal-main-content">
                    <div class="description">
                        <p>Form ini digunakan untuk memperbaharui data tanda terima.</p>
                        <dl>
                        <dt>Catatan</dt>
                            <dd>Pinjam: pihak asal(<em>dari</em>) meminjamkan barang kepada yang sebagai tujuan(<em>untuk</em>)</dd>
                            <dd>Serah terima: pihak asal(<em>dari</em>) melakukan serah terima barang ke pihak tujuan(<em>untuk</em>) 
                        </dl>
                    </div>
                    <form action="/upload" method="post" enctype="multipart/form-data">
                        <!-- <input type="hidden" name="document" value=1>
                        <input type="hidden" name="document_data" value=1> -->
                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" name="title" class="form-control" placeholder="Judul lampiran" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" cols="30" rows="5" placeholder="Deskripsi lampiran"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Upload data</label>
                            <input type="file" name="upload_file">
                        </div>
                        <div class="form-group">
                            <label>Public (Semua akun dapat melihat file ini)</label>
                            <input type="checkbox" name="public" value=1>
                        </div>
                        <button class="btn btn-danger btn-close" >Tutup</button>
                        <button type="submit" class="btn btn-primary" style="float:right;">Upload <span class="glyphicon glyphicon-upload"></span></button>
                    </form>
                </div>
            </div>  
        </div>

    </div>
</main>

<script type="text/javascript">
    $("document").ready(function(){
        
        $(".btn-modal").on("click",function(){
            var modal=$(this).attr('id');
            $(this).parent().closest("main").find("#modal-"+modal).css("display","block");
        });

        $(".btn-close").on("click",function(){
            $(this).parent().closest(".modal").css("display","none");
        })

    });
</script>

<?php

require base.'base/footer.view.php';

?>