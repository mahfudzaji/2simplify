<?php

$titlePage="Notifition";

define("base", $_SERVER['DOCUMENT_ROOT']."/app/resources/views/layouts/");

require base.'base/header.view.php';
?>

<main>
    <div class="container-fluid">
        <?php require "app/resources/views/errors/errors.view.php"; ?>
        
        <header id="main-header">
            <h1>Notifikasi</h1>
        </header>

        <div class="main-data">
            <div class="sub-notif" id="vacation">
                <h3>Permohonan cuti</h3>
                <!--
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Diajukan pada</th>
                                <th>Diajukan oleh</th>
                                <th>Departemen</th>
                                <th>Keperluan</th>
                                <th>Jumlah hari</th>
                                <th>Tanggal</th>
                                <th colspan=2 class="text-center">Respon</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($vacationData as $data): ?>
                                <tr>
                                    <td><?= $data->created_at; ?></td>
                                    <td><?= ucwords($data->submitter); ?></td>
                                    <td><?= ucwords($data->department); ?></td>
                                    <td><?= ucwords($data->requisite); ?></td>
                                    <td><?= $data->day_used; ?> Hari</td>
                                    <td><?= $data->vacation_date; ?></td>
                                    <td class="text-center"><a href='#'>REJECT</a></td>
                                    <td class="text-center"><a href='#'>APPROVE</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                -->
                <div style="display:flex">
                <?php foreach($notificationData as $data): ?>
                    <div>
                    <a href="/form/cuti/detail?v=<?= $data->document_number; ?>">
                        <p><?= $data->message; ?></p>
                        <p><span><?= $data->created_at; ?></span></p>
                    </a>
                    </div>   
                <?php endforeach; ?>
                </div>
            </div>
            <div class="sub-notif" id="reimburse">
            <p>hallo bro ini aku mahfudz aji. cita citaku adalah menjadi problem solver dan innovator dan visiku kedepan adalah ingin menjadi orang yang bermanfaat bagi lingkungan sekitar</p>
            </div>
        </div>
    </div>
</main>

<?php

require base.'base/footer.view.php'

?>