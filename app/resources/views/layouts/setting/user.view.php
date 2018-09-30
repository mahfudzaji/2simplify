<?php

$titlePage="Dashboard";

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';

?>

<style>
.user-group{
    margin-bottom:7px;
    padding:7px;
}

.user-group::before { 
  content: "»";
  color: blue;
}

.user-group:hover{
    color:var(--theme-color4);
    background-color:var(--theme-color1);
    font-weight:bold;
    cursor:pointer;
}
</style>

<main>
    <div class="container-fluid">

        <div class="row">
            
            <div class="col-md-2">
                <?= require 'header.php'; ?>
            </div>

            <div class="col-md-10">
                <?php require "app/resources/views/errors/errors.view.php"; ?>
                
                <h1><?= makeFirstLetterUpper($_GET['c']); ?></h1>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h2>Daftar user</h2>
                        
                        <?php foreach($users as $user): ?>
                            
                            <div class="user-group" >
                                <?php
                                    //name of the user. wrap this as a link to user's profile page
                                    echo "<span data-item='name'>".ucwords($user->name)."</span> (<span data-item='email'>".$user->email."</span>)";

                                    //activate or deactivate user account
                                    $link="<button type='button' class='btn btn-link'><a href='#' data-email=$user->email onclick='toggleStatusOfUser(this)' style='display:inline;'>";
                                        if($user->ida==1){
                                            $link.="deactivate";
                                        }else{
                                            $link.="activate";
                                        }
                                    $link.="</button></a>";

                                    echo $link."<button type='button' class='btn btn-link btn-modal' data-id='update-user'>Update</button><br>";
                                    echo "<span data-item='department' data-item-val='$user->idd'>".ucfirst($user->department)."</span> - <span data-item='role' data-item-val=$user->idr >".ucfirst($user->user_role)."</span>";

                                ?>
                            </div>

                        <?php endforeach; ?>

                    </div>
                    <div class="col-md-6">
                        <h2>Mendaftarkan user</h2>
                        <form action='/register' method='POST' enctype="multipart/form-data">
                            <input type="hidden" name="private" value=1>
                            <div class="form-group">
                                <label>Nama</label>
                                <input type='text' name='username' placeholder='Name' class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type='email' name='email' placeholder='Email' class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Departemen</label>
                                <select name="department" class="form-control">
                                    <option value="">Department</option>
                                    <?php foreach($departments as $department): ?>
                                        <option value=<?= $department->id; ?> ><?= $department->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Jabatan</label>
                                <select name="role" class="form-control">
                                    <option>PILIH JABATAN UNTUK USER</option>
                                    <option value=2>Supervisor/Manager</option>
                                    <option value=3>Staff</option>
                                    <option value=4>Viewer</option>
                                </select>
                            </div>

                            <!--FOTO -->
                            <div class="form-group">
                                <label>Photo</label>
                                <input type="file" name="photo" >
                            </div>

                            <!--TANDA TANGAN -->
                            <div class="form-group">
                                <label>Tanda tangan</label>
                                <input type="file" name="signature" >
                            </div>

                            <button type='submit' class="btn btn-primary"><span class="glyphicon glyphicon-send"></span> Kirim</button>
                        
                        </form>
                    </div>
                </div>
                
                                
            </div>

        </div>
        
        <div class="app-form modal" id="modal-update-user">         
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Perbaharui user</h3>
                </div>

                <div class="description">
                    <p>Form ini digunakan untuk memperbaharui profile.</p>
                </div>
                <form action="/settings/user/update" method="post">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" placeholder='Name' required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" placeholder='Email' required>
                    </div>

                    <div class="form-group">
                        <label>Departemen</label>
                        <select name="department" class="form-control">
                            <option value="">Department</option>
                            <?php foreach($departments as $department): ?>
                                <option value=<?= $department->id; ?> ><?= ucfirst($department->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jabatan</label>
                        <select name="role" class="form-control">
                            <option>PILIH JABATAN UNTUK USER</option>
                            <option value=2>Supervisor/Manager</option>
                            <option value=3>Staff</option>
                            <option value=4>Viewer</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-danger btn-close" >Tutup</button>

                    <div class="nav-right">
                        <button type="submit" name="submit" class="btn btn-primary btn-next">Kirim <span class="glyphicon glyphicon-send"></span></button>
                    </div>
                </form>

                <button type="button" class="btn btn-danger btn-close btn-close-top"><span class="glyphicon glyphicon-remove"></span> </button>

            </div>
        </div>

    </div>
</main>

<script>
    function toggleStatusOfUser(e){
        var email=e.getAttribute('data-email');

        var c = confirm("Anda yakin untuk mengubah status user ini?");

        if(c){
            $.post('toggleUserStatus', {email:email}, function(data, status){
                //alert(data);
                location.reload();
            });
        } 
    }

    $(document).ready(function(){
        $(".btn-modal").on("click", function(){
 
            var department = $(this).parent().find("[data-item~='department']").attr("data-item-val");
            var role = $(this).parent().find("[data-item~='role']").attr("data-item-val");
            var name = $(this).parent().find("[data-item~='name']").html();
            var email = $(this).parent().find("[data-item~='email']").html();

            $("#modal-update-user").find("select[name~='role']").find("option").attr("selected", false);
            $("#modal-update-user").find("select[name~='role']").find("option[value~='"+role+"']").attr("selected", true);
            $("#modal-update-user").find("select[name~='department']").find("option").attr("selected", false);
            $("#modal-update-user").find("select[name~='department']").find("option[value~='"+department+"']").attr("selected", true);
            $("#modal-update-user").find("input[name~='name']").val(name); 
            $("#modal-update-user").find("input[name~='email']").val(email);
            
        });
    });
</script>

<?php

require base.'base/footer.view.php'

?>
