<script language="JavaScript" src="<?= JS_URL ?>jquery.alphanumeric.pack.js"></script>
<section class="well animated fadeInUp">
    <h3><i class="icon-plus-sign text-success"></i> Add New Localization</h3>
    <hr/>
    <?php 
        if (isset($message)) { 
            echo $message;
        }
    ?>
    <form action="<?= $form_action ?>" method="post" class="well">
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend>Content</legend>
                    <div class="row-fluid">
                        <div class="span6">
                            <label for="locale">Locale</label>
                            <input type="text" id="locale" class="input-block-level" name="locale" value="<?=$post['locale']?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label for="iso_1">ISO 2</label>
                            <input type="text" id="iso_1" class="input-block-level" name="iso_1" value="<?=$post['iso_1']?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label for="iso_2">ISO 3</label>
                            <input type="text" id="iso_2" class="input-block-level" name="iso_2" value="<?=$post['iso_2']?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label for="locale_path">Path</label>
                            <input type="text" id="iso_2" class="input-block-level" name="locale_path" value="<?=$post['locale_path']?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="checkbox" style="margin-top: 8px;">
                                <?php if (!empty($post['locale_status'])) { ?>
                                <input type="checkbox" name="locale_status" value="1" checked/> Default
                                <?php } else { ?>
                                <input type="checkbox" name="locale_status" value="1"/> Default
                                <?php } ?>
                            </label>
                        </div>
                    </div>
                    <br/>
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
                <button class="btn btn-primary" type="submit"><i class="icon-save"></i> Save</button>
                <a class="btn btn-warning" href="<?=site_url('localization')?>"><i class="icon-ban-circle"></i> Cancel</a>
            </div>
        </div>
    </form>
</section>
