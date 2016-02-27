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
                <fieldset>
                    <legend>Content</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="product_name">Nama Produk</label>
                            <input type="text" id="product_name" class="input-block-level" name="product_name" value="<?=(isset($post['product_name'])) ? $post['product_name'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="product_code">Kode Produk</label>
                            <input type="text" id="product_code" class="input-block-level" name="product_code" value="<?=(isset($post['product_code'])) ? $post['product_code'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label class="option" for="id_category">Nama Kategori</label>
                            <select class="perpage" name="id_category" id="id_category">
                                <?php
                                    foreach($categories as $category) {
                                        if (isset($post['id_category']) && $post['id_category'] == $category['id_category']) {
                                            echo '<option value="'.$category['id_category'].'" selected="selected">'.$category['category'].'</option>';
                                        } else {
                                            echo '<option value="'.$category['id_category'].'">'.$category['category'].'</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="product_unit">Satuan</label>
                            <input type="text" id="product_unit" class="input-block-level" name="product_unit" value="<?=(isset($post['product_unit'])) ? $post['product_unit'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="product_note">Catatan</label>
                            <textarea id="product_note" class="input-block-level" name="product_note"><?=(isset($post['product_note'])) ? $post['product_note'] : ''?></textarea>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="buy_price">Harga Beli</label>
                            <input type="text" id="buy_price" class="input-block-level" name="buy_price" value="<?=(isset($post['buy_price'])) ? $post['buy_price'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="sell_price">Harga Jual (ke divisi)</label>
                            <input type="text" id="sell_price" class="input-block-level" name="sell_price" value="<?=(isset($post['sell_price'])) ? $post['sell_price'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="product_minimum">Minimum Produk (notifikasi jika produk telah mencapai batas yang telah ditentukan)</label>
                            <input type="text" id="product_minimum" class="input-block-level" name="product_minimum" value="<?=(isset($post['product_minimum'])) ? $post['product_minimum'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="product_stock">Stok Produk</label>
                            <input type="text" id="product_stock" class="input-block-level" name="product_stock" value="<?=(isset($post['product_stock'])) ? $post['product_stock'] : ''?>"/>
                        </div>
                    </div>
                </fieldset>
                <?php if (isset($post['id_product'])) : ?>
                <fieldset class="listProduct">
                    <legend>List Supplier</legend>
                    <div class="genProduct">
                        <table class="table table-striped table-bordered table-hover" id="tableProduct">
                            <thead>
                                <tr>
                                    <th class="center" style='width:1px;'>No</th>
                                    <th class="center" id="supplier">Supplier <span></span></th>
                                    <th class="center" id="supplier_pic">PIC <span></span></th>
                                    <th class="center" id="supplier_address">Alamat <span></span></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($suppliers)) : $i=0; ?>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <tr id="row<?=$i?>">
                                        <td><?=($i+1)?></td>
                                        <td class="text-left"><?=$supplier['supplier']?></td>
                                        <td class="text-left"><?=$supplier['supplier_pic']?></td>
                                        <td class="text-left"><?=$supplier['supplier_address']?></td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
                <?php endif; ?>
            </div>
            <div class="span4">
                <!-- start media -->
                <fieldset>
                    <legend>Media</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="primary_image">Primary Image</label>
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                    <?php if (isset($post['primary_image']) && $post['primary_image'] != '' && file_exists(IMG_UPLOAD_DIR.'product/'.$post['primary_image'])) { ?>
                                        <img src="<?=IMG_UPLOAD_DIR_REL.'product/'.$post['primary_image']?>" />
                                    <?php } ?>
                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">
                                    
                                </div>
                                <div>
                                    <span class="btn btn-file">
                                        <span class="fileupload-new">Select image</span>
                                        <span class="fileupload-exists">Change</span>
                                        <input name="primary_image" id="primary_image" type="file" />
                                    </span>
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid" style="margin-top:30px;">
                        <div class="span12">
                            <label for="thumbnail_image">Thumbnail Image</label>
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                    <?php if (isset($post['thumbnail_image']) && $post['thumbnail_image'] != '' && file_exists(IMG_UPLOAD_DIR.'product/'.$post['thumbnail_image'])) { ?>
                                        <img src="<?=IMG_UPLOAD_DIR_REL.'product/'.$post['thumbnail_image']?>" />
                                    <?php } ?>
                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">
                                    
                                </div>
                                <div>
                                    <span class="btn btn-file">
                                        <span class="fileupload-new">Select image</span>
                                        <span class="fileupload-exists">Change</span>
                                        <input name="thumbnail_image" id="thumbnail_image" type="file" />
                                    </span>
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <hr/>
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
