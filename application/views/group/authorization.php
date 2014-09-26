
<section class="well animated fadeInUp">
    <h3><i class="icon-plus-sign text-success"></i> Otorisasi Grup</h3>
    <hr/>
    <div style="clear:both;"></div>
    <?php if (isset($message)) { ?>
        <div style='color: #00a429'><?= $message ?></div>
    <?php } ?>
    <?php if (isset($tmp_msg)) { ?>
        <div style='color: #00a429'><?= $tmp_msg ?></div>
    <?php } ?>
    <?php if (isset($success_msg)) { ?>
        <div style='color: #00a429'><?= $success_msg ?></div>
    <?php } ?>
    <form action="<?= $form_action ?>" method="post" class="well">
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend>Auth</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label class="checkbox">
                                <input type="checkbox" value="1" id="select-all"/> Pilih Semua
                            </label>
                        </div>
                    </div>
                    <hr/>
                    <div class="row-fluid">
                        <div class="span12">
                            <?=$auth_menu_group?>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="span4">
            </div>
        </div>
        <hr/>
        <div class="row-fluid">
            <div class="span6">
                &nbsp;
            </div>
            <div class="span6 text-right">
                <button class="btn btn-primary" type="submit"><i class="icon-save"></i> Simpan</button>
                <a class="btn btn-warning" href="<?=$cancel_url?>"><i class="icon-ban-circle"></i> Batal</a>
            </div>
        </div>
    </form>
</section>

<script type="text/javascript">
    $(document).ready(function() {
        $("#select-all").change(function() {
            var checkboxes = $(this).closest('form').find(':checkbox');
            if($(this).is(':checked')) {
                checkboxes.attr('checked', 'checked');
            } else {
                checkboxes.removeAttr('checked');
            }
        });
        if ($('.checkauth:checked').length == $('.checkauth').length) {
            $("#select-all").attr('checked', 'checked');
        }
        $(".checkauth").change(function() {
            if ($('.checkauth:checked').length == $('.checkauth').length) {
                $("#select-all").attr('checked', 'checked');
            } else {
                $("#select-all").removeAttr('checked');
            }
        });
        
    });
</script>
