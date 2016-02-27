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
                            <label for="divisioncategory">Nama Kategori</label>
                            <input type="text" id="divisioncategory" class="input-block-level" name="category" value="<?=(isset($post['category'])) ? $post['category'] : ''?>"/>
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
