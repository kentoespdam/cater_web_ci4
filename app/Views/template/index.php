<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cater Web</title>
    <!--meta charset="utf-8"-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CATER | PDAM</title>
    <link rel="shortcut icon" href="<?= base_url('assets') ?>/images/pdam2.png">
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
    <link rel="stylesheet" href="<?= base_url('assets') ?>/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
    folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/css/skins/_all-skins.min.css">
    <!-- EasyUi -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/easyui/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/easyui/themes/icon.css">
    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets') ?>/toast/toastify.min.css">

    <style>
        .easyui-toolbar {
            width: 100%;
            padding: .5em;
            display: flex;
            gap: .5em;
            flex-flow: wrap;
            align-items: flex-end;
        }
    </style>

    <!-- <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script> -->
    <script src="<?= base_url('assets') ?>/easyui/jquery.min.js" defer></script>
    <script type="text/javascript" src="<?= base_url('assets') ?>/easyui/jquery.easyui.min.js" defer></script>
    <script type="text/javascript" src="<?= base_url('assets') ?>/easyui/plugins/datagrid-detailview.js" defer></script>
    <script type="text/javascript" src="<?= base_url('assets') ?>/easyui/plugins/datagrid-export.js" defer></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('assets') ?>/js/app.min.js" defer></script>
    <!-- Toastify -->
    <script type="text/javascript" src="<?= base_url('assets') ?>/toast/toastify.min.js" defer></script>
    <script src="<?= base_url('assets') ?>/js/global.js" defer></script>
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?= $this->include('template/topbar') ?>
        <?= $this->include('template/navigation')
        ?>

        <div class="content-wrapper">
            <section class="content" style="min-height: 92vh;">
                <?= $this->renderSection('content') ?>
            </section>
        </div>
    </div>
</body>

</html>