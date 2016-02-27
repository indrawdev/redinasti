<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8">
        <title><?=$site_setting['app_title']?></title>
        <meta name="viewport" content="user-scalable=0,width=1024,initial-scale=1,maximum-scale=1"/>
        <meta name="HandheldFriendly" content="true"/>
        <meta name="author" content="Ivan Lubis">
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="copyright" content="Ivan Lubis" />
        <meta name="creator" content="Ivan Lubis" />
        <meta http-equiv="Reply-to" content="" />
        <meta http-equiv="X-UA-Compatible" content="IE=7, IE=9">

        <!-- Le styles -->
        <link href="<?= CSS_URL ?>bootstrap.css" rel="stylesheet"/>
        <link href="<?= CSS_URL ?>bootstrap-responsive.css" rel="stylesheet"/>
        <link href="<?= CSS_URL ?>docs.css" rel="stylesheet"/>
        <link href="<?= CSS_URL ?>bootstrap-select.css" rel="stylesheet"/>
        <link href="<?= CSS_URL ?>bootstrap-datetimepicker.min.css" rel="stylesheet"/>
        <link href="<?= CSS_URL ?>bootstrap-modal.css" rel="stylesheet"/>
        <link href="<?= CSS_URL ?>todc-bootstrap.css" rel="stylesheet"/>
        <link href="<?= CSS_URL ?>font-awesome.css" rel="stylesheet"/>
        <link href="<?= CSS_URL ?>style.css" rel="stylesheet"/>

        <!--[if IE 7]>
              <link href="<?= CSS_URL ?>style_ie.css" rel="stylesheet">
          <link href="<?= CSS_URL ?>font-awesome-ie7.min.css" rel="stylesheet">
        <![endif]-->

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
               <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Le modernizr -->
        <script type="text/javascript" src="<?= JS_URL ?>modernizr.js"></script>

    </head>

    <body class="login">
        <article>
            <div class="container">
                <div class="row">
                    <div class="span12">
                        <div class="animated fadeInDown login-logo">
                            <img src="<?= IMG_URL ?>webcontrol-logo.png">
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="span6 offset3">
                        <?php
                            if (isset($error_msg)) {
                                echo $error_msg;
                            }
                            if (isset($tmp_msg)) {
                                echo $tmp_msg;
                            }
                            if (isset($message)) {
                                echo $message;
                            }
                        ?>
                        <form action="login" method="post">
                            <div class="control-group user animated fadeInDown">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="icon-user text-info"></i></span>
                                    <input id="prependedInput" type="text" placeholder="Username" name="uid">
                                </div>
                            </div>
                            <div class="control-group pass animated fadeInDown">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="icon-lock text-info"></i></span>
                                    <input id="prependedInput" type="password" placeholder="Password" name="pas">
                                </div>
                            </div>
                            <div class="control-group text-left animated fadeInDown">
                                <div class="controls">
                                    <div class="row-fluid">
                                        <label class="checkbox span4">
                                          <!--<input type="checkbox"> Remember me-->
                                        </label>
                                        <button type="submit" class="btn btn-primary span4"><i class="icon-signin"></i> Login</button>
                                        <button type="button" class="btn btn-danger span4" formaction="#"><i class="icon-question-sign"></i> Forgot</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </article>

        <footer class="footer animated fadeInUp">
            <div class="container">
                <?=$site_setting['app_footer']?>
            </div>
        </footer>

        <script type="text/javascript" src="<?= JS_URL ?>jquery.js"></script>
        <script type="text/javascript" src="<?= JS_URL ?>bootstrap.js"></script>

    </body>
</html>
