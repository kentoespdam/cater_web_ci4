<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cater Web</title>
    <!--meta charset="utf-8"-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CATER | PDAM</title>
    <link rel="shortcut icon" href="<?= base_url('assets') ?>/dist/easyui/images/pdam.png">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/font-awesome/css/all.min.css">
    <!-- Ionicons -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"> -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/ionicons/css/ionicons.min.css">

    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/dist/css/skins/_all-skins.min.css">
    <!-- iCheck -->
    <!-- <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/iCheck/flat/blue.css">
    -->
    <!-- Morris chart -->
    <!-- <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/morris/morris.css">
    -->
    <!-- jvectormap -->
    <!-- <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    -->
    <!-- Date Picker -->
    <!-- <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/datepicker/datepicker3.css">
    -->
    <!-- Daterange picker -->
    <!--link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <!-- <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    -->

    <!-- <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/datatables/dataTables.bootstrap.css">
    -->

    <!-- EasyUi -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/easyui/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/easyui/themes/icon.css">

    <style>
        .datagrid-row>td>.datagrid-cell {
            font-size: small;
            font-weight: 500;
            line-height: normal;
        }
    </style>

    <script src="<?= base_url('assets') ?>/easyui/jquery.min.js" defer></script>
    <!--script type="text/javascript" src="<?= base_url('assets') ?>/dist/easyui/js/jquery-1.12.4.min.js"></script-->
    <script type="text/javascript" src="<?= base_url('assets') ?>/easyui/jquery.easyui.min.js" defer></script>
    <script type="text/javascript" src="<?= base_url('assets') ?>/easyui/plugins/datagrid-detailview.js" defer></script>

    <!-- <script src="<?= base_url('assets') ?>/plugins/datatables/jquery.dataTables.js"
    defer></script> -->
    <!-- datepicker -->
    <!-- <script src="<?= base_url('assets') ?>/plugins/datepicker/bootstrap-datepicker.js" defer></script> -->
    <!-- <script src="<?= base_url('assets') ?>/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" defer></script> -->
    <!-- Slimscroll -->
    <!-- <script src="<?= base_url('assets') ?>/plugins/slimScroll/jquery.slimscroll.min.js" defer></script> -->
    <!-- FastClick -->
    <!-- <script src="<?= base_url('assets') ?>/plugins/fastclick/fastclick.js"
    defer></script> -->
    <!-- AdminLTE App -->
    <script src="<?= base_url('assets') ?>/dist/js/app.min.js" defer></script>
    <script>
        const baseUri = window.location.origin;
    </script>
</head>

<!-- <body class="hold-trnsition skin-blue sidebar-mini"> -->

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper" style="height: 100vh;">
        <?= $this->include('template/topbar') ?>
        <?= $this->include('template/navigation')
        ?>

        <div class="content-wrapper">
            <!-- <div class="content-header">
                <?= $this->renderSection('page_title', true); ?>
            </div> -->

            <section class="content" style="height: 100%;">
                <?= $this->renderSection('content') ?>
            </section>
        </div>
    </div>
</body>

</html>