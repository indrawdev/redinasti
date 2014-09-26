<?php
if (!$auth_sess) {
    redirect(base_url('login'));
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8"/>
        <title>CMS - <?= $site_setting['app_title'] ?></title>
        <meta name="viewport" content="user-scalable=0,width=1024,initial-scale=1,maximum-scale=1"/>
        <meta name="HandheldFriendly" content="true"/>
        <meta name="author" content="Ivan Lubis"/>
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="copyright" content="&copy; Ivan Lubis" />
        <meta name="creator" content="Ivan Lubis" />
        <meta http-equiv="Reply-to" content="" />
        <meta http-equiv="X-UA-Compatible" content="IE=7, IE=9"/>

        <!-- Le styles -->
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>print/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>print/font-awesome/css/font-awesome.min.css"/>

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
                <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
                <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>custom-theme/jquery.ui.1.10.0.ie.css"/>
        <![endif]-->
        <link href="<?= CSS_URL ?>print.css" rel="stylesheet"/>
        <script>
            var base_url = '<?= base_url() ?>';
            var current_ctrl = '<?= current_controller() ?>';
            var current_url = '<?= current_url() ?>';
            var assets_url = '<?= ASSETS_URL ?>';
        </script>
        <script type="text/javascript" src="<?= CSS_URL ?>print/js/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="<?= CSS_URL ?>print/bootstrap/js/bootstrap.min.js"></script>
    </head>
    <body style='padding-top:0'>
        <div class="container" id="content-container">
            <!--<div class="page-header">
                <h1><?=$page_title?></h1>
            </div>-->
            <div class="row">
                <div class="container">
                    <?= $content ?>
                </div>
            </div>
        </div>

    </body>

</html>


