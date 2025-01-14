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
                    <?php if ($session['kdStatus'] == 'admin') : ?>
                        <li><a href="/master/user"><i class="fa fa-circle-o"></i> User</a></li>
                        <li><a href="/master/transfer_kampung"><i class="fa fa-circle-o"></i> Transfer Kampung</a></li>
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
                    <!-- <li><a href="/hasilbaca/verif"><i class="fa fa-circle-o"></i> VERIF BACA METER</a></li> -->
                    <li><a href="/"><i class="fa fa-circle-o"></i> HASIL BACA METER</a></li>
                    <li><a href="/hasilbaca/cekfoto"><i class="fa fa-circle-o"></i> CEK FOTO</a></li>
                    <li><a href="/hasilbaca/selisih"><i class="fa fa-circle-o"></i> SELISIH FOTO</a></li>
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
                    <li><a href="/laporan/hasil_baca"><i class="fa fa-circle-o"></i>HASIL BACA METER</a></li>
                    <li><a href="/laporan/target"><i class="fa fa-circle-o"></i>REKAP TARGET CATER</a></li>
                    <li><a href="/laporan/kondisi"><i class="fa fa-circle-o"></i>REKAP PER KONDISI</a></li>
                    <li><a href="/laporan/hasil_baca_0"><i class="fa fa-circle-o"></i>PEMAKAIAN (0)</a></li>
                    <li><a href="/laporan/kondisi_pakai_0"><i class="fa fa-circle-o"></i>REKAP PEMAKAIAN (0)</a></li>
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