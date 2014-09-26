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
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>bootstrap-responsive.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>docs.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>bootstrap-select.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>bootstrap-datetimepicker.min.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>bootstrap-modal.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>custom-theme/jquery-ui-1.10.2.custom.css" />
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>todc-bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>font-awesome.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>style.css"/>
        <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>datepicker.css"/>
        <link href="<?= CSS_URL ?>select2.css" rel="stylesheet"/>
        <link href="<?= CSS_URL ?>select2-bootstrap.css" rel="stylesheet"/>

        <!--[if IE 7]>
          <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>style_ie.css">
          <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>font-awesome-ie7.min.css">
        <![endif]-->

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
                <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
                <link rel="stylesheet" type="text/css" href="<?= CSS_URL ?>custom-theme/jquery.ui.1.10.0.ie.css"/>
        <![endif]-->
        <link href="<?= CSS_URL ?>custom.css" rel="stylesheet"/>

        <!-- Le modernizr -->
        <script type="text/javascript" src="<?= JS_URL ?>modernizr.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>jquery.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>jquery-ui.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>jquery.easing.1.3.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>jquery.validate.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>jquery.validate.unobtrusive.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>jquery.validate.bootstrap.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>jquery.cookie.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="<?= LIBS_URL ?>ckeditor/ckeditor.js"></script>
        <script>
            var base_url = '<?= base_url() ?>';
            var current_ctrl = '<?= current_controller() ?>';
            var current_url = '<?= current_url() ?>';
            var assets_url = '<?= ASSETS_URL ?>';
        </script>
        <script type="text/javascript" src="<?= JS_URL ?>js.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>mygrid.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>select2.js"></script>
    </head>
    <body style='padding-top:0'>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">

                <div class="container">
                    <button data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar" type="button">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="<?= site_url('/') ?>" class="brand"><img src="<?= base_url() ?>assets/img/webcontrol-logo-small.png"></a>
                    <div class="nav-collapse collapse">
                        <ul class="nav pull-right">
                            <li><a href="<?=site_url('profile')?>">Welcome, <?= ucwords($auth_sess['admin_name']) ?><i class="icon-user"></i></a></li>
                            <li class="divider-vertical"></li>
                            <li><a href="<?= site_url('logout') ?>" class="logout" title="Logout" data-placement="bottom" data-toggle="tooltip"><i class="icon-signout"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div style="height:32px"></div>
        <header id="overview" class="jumbotron subhead">
            <div class="container">
                <?php if ($site_info['site_logo'] != '' && file_exists(IMG_UPLOAD_DIR.'site/'.$site_info['site_logo'])) : ?>
                <div class="row">
                    <div class="span3"><img src="<?= IMG_UPLOAD_DIR_REL.'site/'.$site_info['site_logo'] ?>" class="main-logo" alt="Site Logo"/></div>
                </div>
                <?php endif; ?>
            </div>
        </header>
        <div class="navbar navbar-googlenav">
            <div class="navbar-inner">
                <div class="container">
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".sub-nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <div class="nav-collapse collapse sub-nav-collapse">
                        <ul class="nav">
                            <?=$main_nav?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container" id="content-container">
            <div class="row">
                <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <?=$site_setting['app_footer']?>
            </div>
        </footer>
        
        
        <div class="static-modal modal hide fade popUp-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        </div>
        <div class="module-modal modal hide fade popUp-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        </div>
        <script type="text/javascript" src="<?= JS_URL ?>bootstrap.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>bootstrap-select.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>bootstrap-modalmanager.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>bootstrap-modal.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>plugin.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>custom.js"></script>
        <script type="text/javascript">
            // datepicker
            $('.pop-datepicker').datepicker({
                format: "yyyy-mm-dd",
                keyboardNavigation: false,
                autoclose: true,
                todayHighlight: true
            });
            
            function deletechecked()
            {
                var answer = confirm("Are you sure to delete this data ?")
                if (answer) {
                    document.messages.submit();
                }

                return false;
            }
            
            $(".static-modal").on('hidden',function() {
                $('.static-modal').html('');
            });
            $(".module-modal").on('hidden',function() {
                $('.module-modal').html('');
            });
            $('.report-daterange').datepicker({
                format: "yyyy-mm-dd",
                autoclose: true
            });
        </script>

    </body>

</html>


