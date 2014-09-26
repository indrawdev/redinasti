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
                            <label for="supplier">Nama Supplier</label>
                            <input type="text" id="supplier" class="input-block-level" name="supplier" value="<?=(isset($post['supplier'])) ? $post['supplier'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="supplier_pic">PIC Supplier</label>
                            <input type="text" id="supplier_pic" class="input-block-level" name="supplier_pic" value="<?=(isset($post['supplier_pic'])) ? $post['supplier_pic'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="supplier_address">Alamat Supplier</label>
                            <textarea id="supplier_address" rows="4" class="input-block-level" name="supplier_address" style="resize: none"><?=(isset($post['supplier_address'])) ? $post['supplier_address'] : ''?></textarea>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="span4">
                <fieldset>
                    <legend>Produk</legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="products">Produk yang dijual</label>
                            <select multiple name="products[]" id="products">
                                <?php foreach ($products as $product) : ?>
                                    <?php 
                                    if (isset($post['products']) && count($post['products'])>0) {
                                        if (array_search($product['id_product'], $post['products']) !== FALSE) {
                                            echo '<option value="'.$product['id_product'].'" selected="selected">'.$product['product_name'].'</option>';
                                        } else {
                                            echo '<option value="'.$product['id_product'].'">'.$product['product_name'].'bbbb</option>';
                                        }
                                    } else {
                                        echo '<option value="'.$product['id_product'].'">'.$product['product_name'].'aaaa</option>';
                                    }
                                    ?>
                                <?php endforeach; ?>
                            </select>
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
<script type="text/javascript">
    $(function() {
        $("#products").select2({
            placeholder: "Pilih Produk"
        });
    });
</script>
