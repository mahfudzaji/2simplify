<?php

$titlePage="Beranda";

require 'base/header.view.php';

/*
-list and register user
-todays activities & list
-announcements & events
*/


?>
<main>
    <div class="container-fluid">

        <!-- messages -->

        <?php include "app/resources/views/errors/errors.view.php" ?>
        
        <div class='welcome text-center'>
            <h2>Hello, Selamat datang di <span class="label label-success">Simplify</span>, <?= ucfirst($_SESSION['sim-name']); ?></h2>
            <h4 style="margin-top:20px;">Aplikasi yang didesain untuk memudahkan operasional perkantoran</h4>
        </div>
        
        <div class="row" style="margin-top:15px;">
        <?php if(isset($roleOfUser)): ?>

            <!-- ||||||||||||||||||||||||| User as superadmin |||||||||||||||||||||||| -->
            <?php if(array_key_exists('superadmin', $roleOfUser)): ?>
                <div class="col-md-4">
                    <?php if(isset($users)): ?>

                        <div class="list users">
                    
                            <h3>Daftar pengguna </h3>
                    
                            <?php foreach($users as $user): ?>
                            
                                <li class="user">
                                    
                                    <?php
                                        //name of the user. wrap this as a link to user's profile page
                                        echo ucwords($user->name)." - ";

                                        //activate or deactivate user account
                                        $link="<a href='#' data-email=$user->email onclick='toggleStatusOfUser(this)' style='display:inline;'>";
                                            if($user->active==1){
                                                $link.="deactivate";
                                            }else{
                                                $link.="activate";
                                            }
                                        $link.="</a>";

                                        echo $link;

                                    ?>

                                </li>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>
                        
                    <h3>Mendaftarkan pengguna</h3>
                    
                    <form action='/register' method='POST' enctype="multipart/form-data">
                        <input type="hidden" name="private" value=1>
                        <div class="form-group">
                            <label>Nama</label>
                            <input type='text' name='username' placeholder='Nama' class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Alamat email</label>
                            <input type='email' name='email' placeholder='Alamat email' class="form-control">
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
            
            <!-- ||||||||||||||||||||||||| User as supervisor |||||||||||||||||||||||| -->
            <?php elseif(array_key_exists('supervisor', $roleOfUser)): 
                
                $createActivity = true;
                $listSelfActivity = true;
                $listAllActivity = true;

                $createAnnouncement = true;
                $listAnnouncement = true;

                $createEvent = true;
                $listEvent = true;
                
            ?>                         

            <!-- ||||||||||||||||||||||||| User as staff |||||||||||||||||||||||| -->
            <?php elseif(array_key_exists('staff', $roleOfUser)): 
            
                $createActivity = true;
                $listSelfActivity = true;
                $listAllActivity = false;

                $createAnnouncement = false;
                $listAnnouncement = true;
                
                $createEvent = false;
                $listEvent = true;
            
            ?>

            <!-- ||||||||||||||||||||||||| User as viewer |||||||||||||||||||||||| -->
            <?php elseif(array_key_exists('viewer', $roleOfUser)): 
                
                $createActivity = false;
                $listSelfActivity = false;
                $listAllActivity = false;

                $createAnnouncement = false;
                $listAnnouncement = true;
                
                $createEvent = false;
                $listEvent = true;    
                
            ?>

            <?php endif; ?>

            <div class="col-md-4 col-md-6">
                <h3>Aktifitas</h3>
                <form action="/activity/create" method="POST">
                    <div class="form-group">
                        <textarea name="activity" placeholder="Aktifitas hari ini" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-md btn-primary"><span class="glyphicon glyphicon-send"></span> Kirim</button>
                </form>
                <div class="data-list">
                    <h4>Daftar aktifitas anda (3 hari)</h4>
                    <p>Aktifitas anda dapat dipantau oleh atasan</p>
                    
                    <?php if(count($activities)>0): ?>
                        <div class="container-fluid">
                            <?php foreach($activities as $activity): ?>
                                <div>
                                    <?= makeFirstLetterUpper($activity->created_at); $act=explode("<br>", $activity->activity); ?>
                                    <ul>
                                        <?php for($i=0; $i<count($act); $i++): ?>
                                            <li><?= $act[$i]; ?></li>
                                        <?php endfor; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Belum terdapat aktifitas</p>
                    <?php endif; ?>
                    
                </div>
            </div>
            <div class="col-md-4 col-md-6">
                <h3>Event</h3>
                <form action="/event/create" method="POST">
                    <div class="form-group">
                        <textarea name="event" placeholder="Event" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="date" name="event_date" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-md btn-primary"><span class="glyphicon glyphicon-send"></span> Kirim</button>
                </form>
                <div class="data-list">
                    <h4>Daftar event</h4>
                    <ul class="container-fluid">
                        <?php foreach($events as $event): ?>
                            <li><?= makeFirstLetterUpper($event->event); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        <?php endif; ?>

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

});
</script>


<?php

require 'base/footer.view.php'

?>