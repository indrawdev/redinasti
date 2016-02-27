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
    <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data"  class="well">
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend></legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="purchase_invoice">No Faktur</label>
                            <input type="text" id="purchase_invoice" name="purchase_invoice" value="<?=(isset($post['purchase_invoice'])) ? $post['purchase_invoice'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid" style="margin-bottom: 20px;">
                        <div class="span12">
                            <label for="id_supplier">Supplier</label>
                            <!--<input type="hidden" id="id_supplier" name="id_supplier" class="bigdrop" value=""/>-->
                            <select name="id_supplier" id="id_supplier">
                                <option value=""></option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <?php if (isset($post['id_supplier']) && $post['id_supplier'] == $supplier['id_supplier']) : ?>
                                    <option value="<?=$supplier['id_supplier']?>" selected="selected"><?=$supplier['supplier']?></option>
                                    <?php else : ?>
                                    <option value="<?=$supplier['id_supplier']?>"><?=$supplier['supplier']?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="driver">Supir</label>
                            <input type="text" id="driver" name="driver" value="<?=(isset($post['driver'])) ? $post['driver'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="licence_plate">No. Plat</label>
                            <input type="text" id="licence_plate" name="licence_plate" value="<?=(isset($post['licence_plate'])) ? $post['licence_plate'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="purchase_note">Catatan</label>
                            <textarea id="purchase_note" rows="4" class="input-block-level" name="purchase_note" style="resize: none"><?=(isset($post['purchase_note'])) ? $post['purchase_note'] : ''?></textarea>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="listProduct">
                    <legend>Produk</legend>
                    <div class="genProduct">
                        <table class="table table-striped table-bordered table-hover" id="tableProduct">
                            <thead>
                                <tr>
                                    <th class="center" style='width:1px;'>No</th>
                                    <th class="center" id="product_name">Produk <span></span></th>
                                    <th class="center" id="qty">QTY <span></span></th>
                                    <th class="center" id="product_price">Harga Satuan <span></span></th>
                                    <th class="center" style="width:40px;" >Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($post['post_product']) && count($post['post_product'])>0) : $i=0; ?>
                                <?php foreach ($post['post_product'] as $prod) : ?>
                                    <tr id="row<?=$i?>">
                                        <td><?=($i+1)?></td>
                                        <td class="productOption">
                                            <select name="post_product[<?=$i?>][id_product]" id="post_product_id_<?=$i?>" class="prodSelect" data-id="<?=$i?>">
                                            <?php foreach ($products as $product) : ?>
                                                <?php if (isset($prod['id_product']) && $prod['id_product'] == $product['id_product']) : ?>
                                                    <option value="<?=$product['id_product']?>" selected="selected"><?=$product['product_name']?></option>
                                                <?php else : ?>
                                                    <option value="<?=$product['id_product']?>"><?=$product['product_name']?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="qty"><input type="number" min="1" max="999" name="post_product[<?=$i?>][purchase_qty]" id="post_product_qty_<?=$i?>" value="<?=(isset($prod['purchase_qty'])) ? $prod['purchase_qty'] : '' ?>" class="input-text number_only"/></td>
                                        <td class="price">Rp. <input type="text" name="post_product[<?=$i?>][purchase_price]" id="post_product_price_<?=$i?>" value="<?=(isset($prod['purchase_price'])) ? $prod['purchase_price'] : '' ?>" class="input-text number_only"/></td>
                                        <td><button type="button" class="btn btn-danger delProduct" id="delProduct-<?=$i?>" onclick="delProduct('<?=$i?>')">(-)</button></td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            <?php endif; ?>
                            <tr class="footer" id="tableFooter">
                                <td colspan="5"><button type="button" class="btn btn-success pull-right" id="addProduct">(+)</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
            </div>
            <div class="span4">
                <fieldset>
                    <legend></legend>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="shipDate">Tanggal Kirim</label>
                            <div id="shipDate" class="input-prepend">
                                <span class="add-on curp">
                                    <i data-time-icon="icon-time" data-date-icon="icon-calendar">
                                    </i>
                                </span>
                                <input data-format="yyyy-mm-dd" type="text" class="span11 pop-datepicker" name="shipping_date" value="<?=(isset($post['shipping_date'])) ? $post['shipping_date'] : date('Y-m-d')?>" placeholder="yyyy-mm-dd"/>
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
<script type="text/javascript">
    var row = <?=$product_count?>;
    $(function() {
        $("#addProduct").click(function() {
            addProduct();
        });
        $("#id_supplier").select2({
            placeholder: "Pilih Supplier"
        });
        $(".prodSelect").each(function() {
            var this_id = $(this).attr('data-id');
            $(this).select2({
                placeholder: "Pilih Produk"
            }).on('change', function(e) {
                var product_id = e.val;
                if (product_id) {
                    $.ajax({
                        url:'<?=$product_info_url?>',
                        type:'post',
                        dataType:'json',
                        data:'product_id='+product_id,
                        success: function(data) {
                            if (data['error']) {
                                $('.display_message').html(data['error']).focus();
                            }
                            if (data['value']['sell_price']) {
                                $("#post_product_price_"+this_id).val(data['value']['sell_price']);
                            }
                            if (data['value']['product_note']) {
                                $('#post_product_id_'+(this_id)).after(nl2br('<br/>'+data['value']['product_note']));
                            }
                        }
                    });
                }
            });
        });   
    });
    
    function addProduct() {
        html = '<tr id="row'+ row +'">';
        html += '<td>'+ (row+1) +'</td>';
        html += '<td class="productOption">';
        html += '<select name="post_product['+ row +'][id_product]" id="post_product_id_'+ row +'" data-id="'+ row +'">';
        html += '<option value=""></option>';
        <?php foreach ($products as $product) : ?>
            html += '<option value="<?=$product['id_product']?>"><?=$product['product_name']?></option>';
        <?php endforeach; ?>
        html += '</select>';
        html += '</td>';
        html += '<td class="qty"><input type="number" min="1" max="999" name="post_product['+ row +'][purchase_qty]" id="post_product_qty_'+ row +'" value="" class="input-text number_only"/></td>';
        html += '<td class="price">Rp. <input type="text" name="post_product['+ row +'][purchase_price]" id="post_product_price_'+ row +'" value="" class="input-text number_only"/></td>';
        html += '<td><button type="button" class="btn btn-danger delProduct" id="delProduct-'+ row +'" onclick="delProduct(\''+row+'\')">(-)</button></td>';
        html += '</tr>';
        $('#tableProduct #tableFooter').before(html);
        $("#post_product_id_"+ row).select2({
            placeholder: "Pilih Produk"
        }).on('change', function(e) {
            var product_id = e.val,
                seltor = $(e.target).attr('data-id');
            if (product_id) {
                $.ajax({
                    url:'<?=$product_info_url?>',
                    type:'post',
                    dataType:'json',
                    data:'product_id='+product_id,
                    success: function(data) {
                        if (data['error']) {
                            $('.display_message').html(data['error']).focus();
                        }
                        if (data['value']['sell_price']) {
                            $('#post_product_price_'+(seltor)).val(data['value']['sell_price']);
                        }
                        if (data['value']['product_note']) {
                            $('#post_product_id_'+(seltor)).after(nl2br('<br/>'+data['value']['product_note']));
                        }
                    }
                });
            }
        });
        $("#post_product_id_"+ row).select2("enable");
        number_only('.number_only');
        row++;
    }
    
    function delProduct(id) {
        $("#row"+id).remove();
        row--;
    }
    
</script>
