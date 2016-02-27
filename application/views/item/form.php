<section class="well animated fadeInUp">
    <h3><i class="icon-plus-sign text-success"></i> <?=$page_title?></h3>
    <hr/>
    <div class="display_message" tabindex="1">
        <?php 
            if (isset($message)) { 
                echo $message;
            }
        ?>
    </div>
    <form action="<?= $form_action ?>" method="post" class="well">
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend>Kode Produksi</legend>
                    <?php if (is_superadmin()) : ?>
                    <div class="row-fluid" style="margin-bottom: 20px;">
                        <div class="span12">
                            <label for="id_division">Divisi</label>
                            <select name="id_division" id="id_division">
                                <option value=""></option>
                                <?php foreach ($divisions as $division) : ?>
                                    <option value="<?=$division['id_division']?>" <?=(isset($post['id_division']) && $post['id_division'] == $division['id_division']) ? 'selected="selected"' : ''?>><?=$division['division']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="row-fluid" style="margin-bottom: 20px;">
                        <div class="span12">
                            <label for="id_item_category">Nama Barang</label>
                            <select name="id_item_category" id="id_item_category">
                                <option value=""></option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?=$category['id_item_category']?>" <?=(isset($post['id_item_category']) && $post['id_item_category'] == $category['id_item_category']) ? 'selected="selected"' : '' ?>><?=$category['item_category']?></option>
                                <?php endforeach; ?>
                            </select> &nbsp;&nbsp;
                            <button class="btn btn-primary" id="addCategory" type="button">(+)</button>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="size">Ukuran</label>
                            <input type="text" id="size" name="size" value="<?=(isset($post['size'])) ? $post['size'] : ''?>" class="input-block-level"/>
                        </div>
                    </div>
                    <div class="row-fluid" style="margin-bottom:20px;">
                        <div class="span12">
                            <label for="item_type">Tipe Barang</label>
                            <input type="radio" name="item_type" id="jadi" value="1" <?=(isset($post['item_type']) && $post['item_type'] == 1) ? 'checked' : ''?>/> Jadi &nbsp;
                            <input type="radio" name="item_type" id="set_jadi" value="2" <?=(isset($post['item_type']) && $post['item_type'] == 2) ? 'checked' : ''?>/> 1/2 Jadi &nbsp;
                            <input type="radio" name="item_type" id="mentah" value="3" <?=(isset($post['item_type']) && $post['item_type'] == 3) ? 'checked' : ''?>/> Mentah &nbsp;
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="item_stock">Stok Barang</label>
                            <input type="number" max="999" min="1" id="item_stock" name="item_stock" value="<?=(isset($post['item_stock'])) ? $post['item_stock'] : ''?>" class="input-block-level number_only"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="item_hpp_price">HPP</label>
                            <input type="text" id="item_hpp_price" name="item_hpp_price" value="<?=(isset($post['item_hpp_price'])) ? $post['item_hpp_price'] : ''?>" class="input-block-level number_only"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="item_sell_price">Harga Jual</label>
                            <input type="text" id="item_sell_price" name="item_sell_price" value="<?=(isset($post['item_sell_price'])) ? $post['item_sell_price'] : ''?>" class="input-block-level number_only"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="item_note">Catatan</label>
                            <textarea id="item_note" rows="4" class="input-block-level" name="item_note" style="resize: none"><?=(isset($post['item_note'])) ? $post['item_note'] : ''?></textarea>
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
    var row = '0';
    var division_id = $("#id_division").val();
    $(function() {
        $('#addCategory').click(function() {
            $.ajax({
                url:'<?=$category_url?>',
                type:'get',
                dataType:'json',
                success: function(data) {
                    if (data['html']) {
                        $(".module-modal").html(data['html']);
                        $('.module-modal').modal();
                    }
                }
            });
        });
        $("#id_item_category").select2({
            placeholder: "Pilih Kategori"
        });
        $("#id_division").select2({
            placeholder: "Pilih Divisi"
        });
    });
    
</script>
