<link href="<?= CSS_URL ?>bootstrap-fileupload.min.css" rel="stylesheet"/>
<script language="JavaScript" src="<?= JS_URL ?>bootstrap-fileupload.min.js"></script>
<script language="JavaScript" src="<?= JS_URL ?>jquery.alphanumeric.pack.js"></script>
<section class="well animated fadeInUp">
    <h3><i class="icon-plus-sign text-success"></i> Profile</h3>
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
    <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data"  class="well">
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend>Data</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="username">Username</label>
                            <span><b><?=$post['username']?></b></span><br/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="name">Name</label>
                            <input type="text" id="name" class="input-block-level" name="name" value="<?=$post['name']?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="email">Email</label>
                            <input type="text" id="email" class="input-block-level" name="email" value="<?=$post['email']?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="alamat">Address</label>
                            <textarea rows="4" name="alamat" class="input-block-level" id="alamat"><?=$post['alamat']?></textarea>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" class="input-block-level" name="phone" value="<?=$post['phone']?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <a class="btn btn-primary" id="change_pass" href="#passModal" role="button" data-toggle="modal">Change Password</a>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="span4">
                <fieldset>
                    <legend>Media</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="image">Image</label>
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                    <?php if ($post['image'] != '' && file_exists(IMG_UPLOAD_DIR.'user/'.$post['image'])) { ?>
                                        <img src="<?=IMG_UPLOAD_DIR_REL.'user/'.$post['image']?>" />
                                    <?php } else { ?>
                                        <img src=""/>
                                    <?php } ?>
                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">
                                    
                                </div>
                                <div>
                                    <span class="btn btn-file">
                                        <span class="fileupload-new">Select image</span>
                                        <span class="fileupload-exists">Change</span>
                                        <input name="image" id="image" type="file" />
                                    </span>
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <hr/>
        <div class="row-fluid">
            <div class="span6">
                &nbsp;
            </div>
            <div class="span6 text-right">
                <button class="btn btn-primary" type="submit"><i class="icon-save"></i> Save</button>
                <a class="btn btn-warning" href="<?=site_url('home')?>"><i class="icon-ban-circle"></i> Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- Modal -->
<div id="passModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="passModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Change Password</h3>
    </div>
    <div class="modal-body">
        <form action="<?= $changepass_form ?>" method="post" class="well" id="change_pass_form">
            <fieldset>
                <legend>Password</legend>
                <div id="print-msg" class="error"></div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="old_password">Old Password</label>
                        <input type="password" id="old_password" class="input-block-level" name="old_password" value=""/>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" class="input-block-level" name="new_password" value=""/>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="conf_password">Confirm Password</label>
                        <input type="password" id="conf_password" class="input-block-level" name="conf_password" value=""/>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="save_password">Save changes</button>
        <button class="btn btn-warning" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>
<script type="text/javascript">
    $("#save_password").click(function() {
        $("#print-msg").html('');
        var old_password = $("#old_password").val();
        var new_password = $("#new_password").val();
        var conf_password = $("#conf_password").val();
        if (old_password != '') {
            if (new_password != '' && (conf_password == new_password)) {
                $.ajax({
                    url:'<?=$changepass_form?>',
                    type:'post',
                    dataType:'json',
                    data:$('#change_pass_form').serialize(),
                    beforeSend:function() {
                        $('button').attr('disabled','disabled');
                    },
                    complete:function() {
                        $('button').removeAttr('disabled');
                    },
                    success:function(data) {
                        if (data['error']) {
                            $("#print-msg").html(data['error']);
                        }
                        if (data['success']) {
                            if (data['redirect']) {
                                window.location = data['redirect'];
                            }
                        }
                        if (data['location']) {
                            window.location = data['location'];
                        }
                    }
                });
            } else {
                $("#print-msg").html('Please input Your New Password or Confirmation is not correct.<br/>');
            }
        } else {
            $("#print-msg").html('Please input Your old password.<br/>');
        }
    });
</script>
