<section class="well animated fadeInUp">
    <div class="row-fluid">
        <div class="span12">
            <div style='float: left;color: #00a429'>
                <ul class="breadcrumb">
                </ul>
            </div>
            <div style="clear:both;"></div>
            <?php if (isset($message)) { ?>
                <div style='float: left;color: #00a429'><?= $message ?></div>
            <?php } ?>
            <?php if (isset($tmp_msg)) { ?>
                <div style='float: left;color: #00a429'><?= $tmp_msg ?></div>
            <?php } ?>
            <?php if (isset($success_msg)) { ?>
                <div style='float: left;color: #00a429'><?= $success_msg ?></div>
            <?php } ?>
            <div class='text-right'>
                <a class="btn btn-primary" href="<?= $add_url ?>"><i class="icon-plus-sign"></i> Tambah Grup</a>
            </div>
        </div>
    </div>  
    <hr>
    <div id='list_data'>
        <button type="button" class="btn reload" title='Reload Data'><i class="icon-refresh"></i></button>
        <select class='perpage' style='margin-bottom:0;width:125px;'>
            <optgroup label='Show per page'>
                <option value='5'>5</option>
                <option value='10'>10</option>
                <option value='50'>50</option>
                <option value='100'>100</option>
            </optgroup>
        </select>
        <?php
            if (isset($error_msg)) {
                echo $error_msg;
            }
        ?>
        <br/><br/><br/>
        <!-- start listing data -->
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="center" style='width:1px;'>No</th>
                    <th title="Sort" id="auth_user_group">Nama Grup <span></span></th>
                    <th class="center">Auth</th>
                    <th class="center">Action</th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text" placeholder="Search" class="cari" id="search_group" style="width:300px;"></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <!-- end of listing data -->
        <hr/>
        <div class="row-fluid">
            <div class="span12 text-right">
                <a class="btn btn-primary" href="<?= $add_url ?>"><i class="icon-plus-sign"></i> Tambah Grup</a>
            </div>
        </div>
    </div>
</section>
<style>
    .ui-icon-carat-1-s,.ui-icon-carat-1-n{float: right;}
</style>
<script>
    the_grid('list_data', '<?= $list_data ?>', 10);
</script>