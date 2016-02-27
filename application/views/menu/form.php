
<section class="well animated fadeInUp">
    <h3><i class="icon-plus-sign text-success"></i> <?=$page_title?></h3>
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
                    <legend>Content</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="menu">Menu</label>
                            <input type="text" id="menu" class="input-block-level" name="menu" value="<?=$post['menu']?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label class="option" for="parent_auth_menu">Parent</label>
                            <select class="perpage" name="parent_auth_menu" id="parent_auth_menu">
                                <option value="0">ROOT</option>
                                <?=$parent_option?>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="file">File</label>
                            <input type="text" id="menu" class="input-block-level" name="file" value="<?=$post['file']?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="position">Urutan</label>
                            <input type="text" id="position" class="" name="position" value="<?=$post['position']?>"/>
                        </div>
                    </div>
                    <br/>
                </fieldset>
            </div>
            <div class="span4">
                <?php if (is_superadmin()) : ?>
                <fieldset>
                    <legend>Super Administrator</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label class="checkbox">
                                <?php if (!empty($post['is_superadmin'])) { ?>
                                <input type="checkbox" name="is_superadmin" value="1" checked/> Ya
                                <?php } else { ?>
                                <input type="checkbox" name="is_superadmin" value="1"/> Ya
                                <?php } ?>
                            </label>
                        </div>
                    </div>
                </fieldset>
                <hr/>
                <?php endif; ?>
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
