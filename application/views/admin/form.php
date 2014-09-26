<link href="<?= CSS_URL ?>bootstrap-fileupload.min.css" rel="stylesheet"/>
<script language="JavaScript" src="<?= JS_URL ?>bootstrap-fileupload.min.js"></script>
<script language="JavaScript" src="<?= JS_URL ?>jquery.alphanumeric.pack.js"></script>
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
    <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data"  class="well">
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend>Data</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="username">Username</label>
                            <input type="text" id="username" class="input-block-level" name="username" value="<?=(isset($post['username'])) ? $post['username'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label class="option" for="id_auth_group">Grup</label>
                            <select class="perpage" name="id_auth_group" id="id_auth_group">
                                <?php
                                    foreach($groups as $group) {
                                        if (isset($post['id_auth_group']) && $group['id_auth_group'] == $post['id_auth_group']) {
                                            echo '<option value="'.$group['id_auth_group'].'" selected="selected">'.$group['auth_group'].'</option>';
                                        } else {
                                            echo '<option value="'.$group['id_auth_group'].'">'.$group['auth_group'].'</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label class="option" for="id_division">Divisi</label>
                            <select class="perpage" name="id_division" id="id_division">
                                <?php
                                    foreach($divisions as $division) {
                                        if (isset($post['id_division']) && $division['id_division'] == $post['id_division']) {
                                            echo '<option value="'.$division['id_division'].'" selected="selected">'.$division['division'].'</option>';
                                        } else {
                                            echo '<option value="'.$division['id_division'].'">'.$division['division'].'</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="password">Password <?=$empty_msg?></label>
                            <input type="password" id="password" class="input-block-level" name="password" value=""/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="conf_password">Konfirmasi Password</label>
                            <input type="password" id="conf_password" class="input-block-level" name="conf_password" value=""/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="name">Nama</label>
                            <input type="text" id="name" class="input-block-level" name="name" value="<?=(isset($post['name'])) ? $post['name'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="email">Email</label>
                            <input type="text" id="email" class="input-block-level" name="email" value="<?=(isset($post['email'])) ? $post['email'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="alamat">Alamat</label>
                            <textarea rows="4" name="alamat" class="input-block-level" id="alamat"><?=(isset($post['alamat'])) ? $post['alamat'] : ''?></textarea>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="phone">Telepon</label>
                            <input type="text" id="phone" class="input-block-level" name="phone" value="<?=(isset($post['phone'])) ? $post['phone'] : ''?>"/>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="span4">
                <fieldset>
                    <legend>Status / Opsi</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label class="checkbox">
                                <?php if (isset($post['status']) && !empty($post['status'])) { ?>
                                <input type="checkbox" name="status" value="1" checked/> Aktif
                                <?php } else { ?>
                                <input type="checkbox" name="status" value="1"/> Aktif
                                <?php } ?>
                            </label>
                        </div>
                    </div>
                </fieldset>
                <hr/>
                <?php if (is_superadmin()) : ?>
                <fieldset>
                    <legend>Super Administrator</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label class="checkbox">
                                <?php if (isset($post['is_superadmin']) && !empty($post['is_superadmin'])) { ?>
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
                <fieldset>
                    <legend>Media</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="image">Foto</label>
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                    <?php if (isset($post['image']) && $post['image'] != '' && file_exists(IMG_UPLOAD_DIR.'admin/'.$post['image'])) { ?>
                                        <img src="<?=IMG_UPLOAD_DIR_REL.'admin/'.$post['image']?>" />
                                    <?php } ?>
                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">
                                    
                                </div>
                                <div>
                                    <span class="btn btn-file">
                                        <span class="fileupload-new">Pilih Foto</span>
                                        <span class="fileupload-exists">Ubah</span>
                                        <input name="image" id="image" type="file" />
                                    </span>
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Hapus</a>
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
                <button class="btn btn-primary" type="submit"><i class="icon-save"></i> Simpan</button>
                <a class="btn btn-warning" href="<?=$cancel_url?>"><i class="icon-ban-circle"></i> Batal</a>
            </div>
        </div>
    </form>
</section>
