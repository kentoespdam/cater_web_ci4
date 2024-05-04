<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= base_url('assets') ?>/images/pdam2.png" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?= $session['nama'] ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="treeview">
                <a href="<?= base_url() ?>">
                    <i class="fa fa-dashboard"></i> <span>MASTER DATA</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if ($session['kdStatus'] == 'admin'): ?>
                        <li><a href="<?= base_url('master/user') ?>"><i class="fa fa-circle-o"></i> User</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>CATER PDAM</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="hasilbaca/verif"><i class="fa fa-circle-o"></i> VERIF BACA METER</a></li>
                    <li><a href="v_hasil_baca.php"><i class="fa fa-circle-o"></i> HASIL BACA METER</a></li>
                    <li><a href="v_cek_foto.php"><i class="fa fa-circle-o"></i> CEK FOTO</a></li>
                    <li><a href="v_baca-meter.php"><i class="fa fa-circle-o"></i> BACA METER MANDIRI</a></li>
                    <?php if ($session['kdStatus'] == 'admin'): ?>
                        <li><a href="v_baca-meter.php"><i class="fa fa-circle-o"></i> Baca Meter Mandiri</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span>LAPORAN</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="v_lap_hasil_baca.php"><i class="fa fa-circle-o"></i>HASIL BACA METER</a></li>
                    <li><a href="v_lap_hasil_baca_target.php"><i class="fa fa-circle-o"></i>REKAP TARGET CATER</a></li>
                    <li><a href="v_lap_rekap.php"><i class="fa fa-circle-o"></i>REKAP PER KONDISI</a></li>
                    <li><a href="v_lap_hasil_baca_0.php"><i class="fa fa-circle-o"></i>PEMAKAIAN (0)</a></li>
                    <li><a href="v_lap_rekap_pakai_0.php"><i class="fa fa-circle-o"></i>REKAP PEMAKAIAN (0)</a></li>
                </ul>
            </li>

            <li class="treeview">
                <a href="<?= base_url() ?>auth/logout">
                    <i class="fa fa-dashboard"></i> <span>LOG OUT</span>
                </a>
            </li>

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>