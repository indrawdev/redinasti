<link href="<?= CSS_URL ?>bootstrap-fileupload.min.css" rel="stylesheet"/>
<script language="JavaScript" src="<?= JS_URL ?>bootstrap-fileupload.min.js"></script>
<script language="JavaScript" src="<?= JS_URL ?>jquery.alphanumeric.pack.js"></script>
<section class="well animated fadeInUp">
    <h3><i class="icon-plus-sign text-success"></i> <?=$page_title?></h3>
    <hr/>
    <?php 
        if (isset($message)) { 
            echo $message;
        }
    ?>
    <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data"  class="well">
        <div class="row-fluid">
            <div class="span8">
                <div class="tabbable">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tinfo" data-toggle="tab">Info</a></li>
                        <li><a href="#tsetting" data-toggle="tab">Setting</a></li>
                    </ul>
                    
                    <div class="tab-content">
                        <!-- info form tab -->
                        <div class="tab-pane active" id="tinfo">
                            <fieldset>
                                <legend>Info</legend>
                                <div class="row-fluid">
                                    <div class="span12">
                                        <label for="site_name">Site Name</label>
                                        <input type="text" id="site_name" class="input-block-level" name="site_name" value="<?=$post['site_name']?>"/>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span12">
                                        <label for="site_url">Site URL</label>
                                        <input type="text" id="site_url" class="input-block-level" name="site_url" value="<?=$post['site_url']?>"/>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span12">
                                        <label for="site_path">Site Path</label>
                                        <input type="text" id="site_path" class="input-block-level" name="site_path" value="<?=$post['site_path']?>"/>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span12">
                                        <label for="site_address">Site Address</label>
                                        <textarea id="site_address" class="input-block-level" name="site_address" rows="3"><?=$post['site_name']?></textarea>
                                    </div>
                                </div>
                            </fieldset>
                        </div><!-- info form tab -->
                        
                        <!-- setting form tab -->
                        <div class="tab-pane" id="tsetting">
                            <fieldset>
                                <legend>Setting</legend>
                                <?php 
                                foreach ($settings as $setting => $val) {
                                    echo '<div class="row-fluid">'
                                        .'<div class="span12">'
                                        .'<label for="'.$setting.'">'.ucwords(str_replace('_',' ',$setting)).'</label>'
                                        .'<textarea id="'.$setting.'" class="input-block-level" name="setting['.$setting.']" rows="1">'.$val.'</textarea>'
                                        .'</div>'
                                        .'</div>';
                                }
                                ?>
                            </fieldset>
                        </div><!-- technology form tab -->
                    </div>
                </div>
            </div>
            <div class="span4">
                <!-- start publishing option -->
                <fieldset>
                    <legend>Publishing Options</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label class="checkbox" style="margin-top: 8px;">
                                <?php if (!empty($post['is_default'])) { ?>
                                <input type="checkbox" name="is_default" value="1" checked/> Default
                                <?php } else { ?>
                                <input type="checkbox" name="is_default" value="1"/> Default
                                <?php } ?>
                            </label>
                        </div>
                    </div>
                </fieldset>
                <hr/>
                <!-- end of publishing option -->
                <!-- start media -->
                <fieldset>
                    <legend>Media</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="primary_image">Primary Image</label>
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                    <?php if ($post['site_logo'] != '' && file_exists(IMG_UPLOAD_DIR.'site/'.$post['site_logo'])) { ?>
                                        <img src="<?=IMG_UPLOAD_DIR_REL.'site/'.$post['site_logo']?>" />
                                    <?php } else { ?>
                                    <?php } ?>
                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">
                                    
                                </div>
                                <div>
                                    <span class="btn btn-file">
                                        <span class="fileupload-new">Select image</span>
                                        <span class="fileupload-exists">Change</span>
                                        <input name="site_logo" id="site_logo" type="file" />
                                    </span>
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <hr/>
                <!-- end of media -->
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
