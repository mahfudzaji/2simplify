<?php
require 'only-header.view.php';
?>

<body>
    <nav>
        <div class="container-fluid">
            
            <ul>
                <li><a href="/home">Home</a></li>
                
                <li><a href="#">Form<span class="caret"></span></a>
                    <ul class="dropdown">
                        <li><a href='/form/tanda-terima'>Tanda terima</a></li>
                        <!-- <li><a href='/form/activity-report'>Activity report</a></li>
                        <li><a href='/form/reimburse'>Form reimburse</a></li>
                        <li><a href='/form/cuti'>Form cuti</a></li> -->
                        <li><a href='/form/quo'>QUO</a></li>
                        <li><a href='/form/po'>PO</a></li>
                        <li><a href='/form/do'>DO</a></li>
                    </ul>
                </li>
                <li><a href="/partner">Partner</a></li>
                <li><a href='#'>Produk & stok<span class="caret"></span></a>
                    <ul class="dropdown">
                        <li><a href='/p-a'>Produk</a></li>
                        <li><a href='/stock'>Stok</a></li>
                    </ul>
                </li>
                <!-- <li><a href='/upload'>Unggah data</a></li> -->
                <!-- <li><a href='/engineering'>Engineer</a></li> -->
                
                <?php if(isset($_SESSION['sim-isLogin'])): ?>

                    <ul class="nav-right">
                        <!-- <li><a href="/notification"><span class="badge">2</span> Notif</a></li> -->
                        
                        <li><a href='#'><span class="glyphicon glyphicon-user"></span></a>
                            <ul class="dropdown" style="left:auto;right:0;">
                                <li><a href='/settings?c=profile'>Settings</a></li>
                                <li><a href="/logout">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                    
                <?php endif; ?>
            </ul>
            
        </div>
    </nav>