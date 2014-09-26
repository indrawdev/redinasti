<section class="well animated fadeInUp">
    <div style='float: left;color: #00a429'>
        <?php
        if (isset($message)) {
            echo $message;
        }
        ?>
        <ul class="breadcrumb">
            <?php
            if ($breadcrumbs) {
                foreach($breadcrumbs as $breadcrumb) {
                    echo '<li><a href="'.$breadcrumb['href'].'" class="'.$breadcrumb['class'].'">'.$breadcrumb['menu'].'</a></li>';
                }
            }
            ?>
        </ul>
    </div>
    <div style="clear:both;"></div>
    <h5 style="margin-left:30px;">Hi, <?= ucwords($auth_sess['admin_name']); ?></h5>
    
    <div style=" height:50px"></div>
</section>