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
                <fieldset>
                    <legend>Content</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="store">Nama Toko</label>
                            <input type="text" id="store" class="input-block-level" name="store" value="<?=(isset($post['store'])) ? $post['store'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="store_pic">PIC Toko</label>
                            <input type="text" id="store_pic" class="input-block-level" name="store_pic" value="<?=(isset($post['store_pic'])) ? $post['store_pic'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="store_address">Alamat Toko</label>
                            <textarea id="store_address" rows="4" class="input-block-level" name="store_address" style="resize: none"><?=(isset($post['store_address'])) ? $post['store_address'] : ''?></textarea>
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
