<section class="well animated fadeInUp">
    <h3><i class="icon-plus-sign text-success"></i> <?=$page_title?></h3>
    <hr/>
    <div class="display_message">
        <?php 
            if (isset($message)) { 
                echo $message;
            }
        ?>
    </div>
    <form action="<?= $form_action ?>" method="post" enctype="multipart/form-data"  class="well">
        <div class="row-fluid">
            <div class="span5">
                <fieldset>
                    <legend>Kode Produksi</legend>
                    <?php if (is_superadmin()) : ?>
                    <div class="row-fluid" style="margin-bottom: 20px;">
                        <div class="span12">
                            <label for="id_division">Divisi</label>
                            <!--<input type="hidden" id="id_division" name="id_division" class="bigdrop" value=""/>-->
                            <select name="id_division" id="id_division">
                                <option value=""></option>
                                <?php foreach ($divisions as $division) : ?>
                                    <?php if (isset($post['id_division']) && $post['id_division'] == $division['id_division']) : ?>
                                    <option value="<?=$division['id_division']?>" selected="selected"><?=$division['division']?></option>
                                    <?php else : ?>
                                    <option value="<?=$division['id_division']?>"><?=$division['division']?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="row-fluid" style="margin-bottom: 20px;">
                        <div class="span12">
                            <label for="id_production_category">Kategori</label>
                            <select name="id_production_category" id="id_production_category">
                                <option value=""></option>
                                <?php foreach ($categories as $category) : ?>
                                    <?php if (isset($post['id_production_category']) && $post['id_production_category'] == $category['id_production_category']) : ?>
                                    <option value="<?=$category['id_production_category']?>" selected="selected"><?=$category['production_category']?></option>
                                    <?php else : ?>
                                    <option value="<?=$category['id_production_category']?>"><?=$category['production_category']?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select> &nbsp;&nbsp;
                            <button class="btn btn-primary" id="addCategory" type="button">(+)</button>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="production_name">Nama Produksi/Barang</label>
                            <input type="text" id="production_name" name="production_name" value="<?=(isset($post['production_name'])) ? $post['production_name'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid" style="margin-bottom:20px;">
                        <div class="span12">
                            <label for="production_type">Tipe Produksi</label>
                            <input type="radio" name="production_type" id="jadi" value="1" <?=(isset($post['production_type']) && $post['production_type'] == 1) ? 'checked' : ''?>/> Jadi &nbsp;
                            <input type="radio" name="production_type" id="set_jadi" value="2" <?=(isset($post['production_type']) && $post['production_type'] == 2) ? 'checked' : ''?>/> 1/2 Jadi &nbsp;
                            <input type="radio" name="production_type" id="mentah" value="3" <?=(isset($post['production_type']) && $post['production_type'] == 3) ? 'checked' : ''?>/> Mentah &nbsp;
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="size">Ukuran</label>
                            <input type="text" id="size" name="size" value="<?=(isset($post['size'])) ? $post['size'] : ''?>"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="production_note">Catatan</label>
                            <textarea id="production_note" rows="4" class="input-block-level" name="production_note" style="resize: none"><?=(isset($post['production_note'])) ? $post['production_note'] : ''?></textarea>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="span7">
                <fieldset class="listProduct" style="margin-top:0;">
                    <legend>Produk</legend>
                    <div class="genProduct">
                        <table class="table table-striped table-bordered table-hover" id="tableProduct">
                            <thead>
                                <tr>
                                    <th class="center" id="product_name">Produk <span></span></th>
                                    <th class="center" id="qty">QTY <span></span></th>
                                    <th class="center" id="product_price" style="width: 145px;">Harga Satuan <span></span></th>
                                    <th class="center" style="width:40px;" >Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($post['post_product']) && count($post['post_product'])>0) : $i=0; ?>
                                <?php foreach ($post['post_product'] as $prod) : ?>
                                    <tr id="row<?=$i?>">
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
                                        <td class="qty"><input type="text" name="post_product[<?=$i?>][purchase_qty]" id="post_product_qty_<?=$i?>" value="<?=(isset($prod['purchase_qty'])) ? $prod['purchase_qty'] : '' ?>" class="input-text"/></td>
                                        <td class="price">Rp. <input type="text" name="post_product[<?=$i?>][purchase_price]" id="post_product_price_<?=$i?>" value="<?=(isset($prod['purchase_price'])) ? $prod['purchase_price'] : '' ?>" class="input-text" readonly="readonly"/></td>
                                        <td><button type="button" class="btn btn-danger delProduct" id="delProduct-<?=$i?>" onclick="delProduct('<?=$i?>')">(-)</button></td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            <?php endif; ?>
                            <tr class="footer tableFooter">
                                <td colspan="5"><button type="button" class="btn btn-success pull-right" id="addProduct">(+)</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Kode Produksi</legend>
                    <div class="genProduction">
                        <table class="table table-striped table-bordered table-hover" id="tableProduction">
                            <thead>
                                <tr>
                                    <th class="center" id="production_name">Kode Produksi <span></span></th>
                                    <th class="center" id="production_price" style="width: 145px;">Harga <span></span></th>
                                    <th class="center" style="width:40px;" >Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($post['post_production']) && count($post['post_production'])>0) : $i=0; ?>
                                <?php foreach ($post['post_production'] as $prodt) : ?>
                                    <tr id="row<?=$i?>">
                                        <td class="productionOption">
                                            <select name="post_production[<?=$i?>][id_production]" id="post_production_id_<?=$i?>" class="productionSelect" data-id="<?=$i?>">
                                            <?php foreach ($productions as $production) : ?>
                                                <?php if (isset($prodt['id_production']) && $prodt['id_production'] == $production['id_production']) : ?>
                                                    <option value="<?=$production['id_production']?>" selected="selected"><?=$production['product_code'].' - '.$production['product_name']?></option>
                                                <?php else : ?>
                                                    <option value="<?=$production['id_production']?>"><?=$production['product_code'].' - '.$production['product_name']?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="price">Rp. <input type="text" name="post_production[<?=$i?>][production_price]" id="post_production_price_<?=$i?>" value="<?=(isset($prodt['production_price'])) ? $prod['production_price'] : '' ?>" class="input-text" readonly="readonly"/></td>
                                        <td><button type="button" class="btn btn-danger delProduction" id="delProduction-<?=$i?>" onclick="delProduction('<?=$i?>')">(-)</button></td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            <?php endif; ?>
                            <tr class="footer tableFooter">
                                <td colspan="5"><button type="button" class="btn btn-success pull-right" id="addProduction">(+)</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Biaya Lain-lain</legend>
                    <div class="genOtherCost">
                        <table class="table table-striped table-bordered table-hover" id="tableOtherCost">
                            <thead>
                                <tr>
                                    <th class="center" id="cost_note">Keterangan <span></span></th>
                                    <th class="center" id="cost" style="width: 145px;">Pengeluaran <span></span></th>
                                    <th class="center" style="width:40px;" >Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($post['post_cost']) && count($post['post_cost'])>0) : $i=0; ?>
                                <?php foreach ($post['post_cost'] as $cost) : ?>
                                    <tr id="row-cost<?=$i?>">
                                        <td class="cost_desc">
                                            <textarea id="post_cost_note_<?=$i?>" rows="2" class="input-block-level" name="post_cost[<?=$i?>][note]" style="resize: none"><?=(isset($cost['note'])) ? $cost['note'] : '' ?></textarea>
                                        </td>
                                        <td class="cost_price">Rp. <input type="text" name="post_cost[<?=$i?>][cost]" id="post_cost_cost_<?=$i?>" value="<?=(isset($cost['cost'])) ? $cost['cost'] : '' ?>" class="input-text othercost-input"/></td>
                                        <td><button type="button" class="btn btn-danger delOtherCost" id="delOtherCost-<?=$i?>" onclick="delOtherCost('<?=$i?>')">(-)</button></td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            <?php endif; ?>
                            <tr class="footer tableFooter">
                                <td colspan="5"><button type="button" class="btn btn-success pull-right" id="addOtherCost">(+)</button></td>
                            </tr>
                            </tbody>
                        </table>
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
    var row = '<?=$product_count?>';
    var row_production = '<?=$production_count?>';
    var row_cost = '<?=$cost_count?>';
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
        $("#id_production_category").select2({
            placeholder: "Pilih Kategori"
        });
        $("#id_division").select2({
            placeholder: "Pilih Divisi"
        }).on('change', function(e) {
            var division_id = e.val;
            $("table#tableProduct tbody tr").not('.tableFooter').html('');
            $("table#tableProduction tbody tr").not('.tableFooter').html('');
        });
        // add product
        $("#addProduct").click(function() {
            <?php if (is_superadmin()) : ?>
                var division_id = $("#id_division").val();
            <?php else : ?>
                var division_id = '<?=getSessionAdmin('admin_id_division')?>';
            <?php endif; ?>
            if (division_id) {
                $.ajax({
                    url:'<?=$getproduct_url?>',
                    type:'post',
                    dataType:'json',
                    data:{division_id:division_id},
                    success: function(data) {
                        if (data['error']) {
                            $(".display_message").html(data['error']);
                        }
                        if (data['return']) {
                            addProduct(row);
                            $("#post_product_id_"+row).append(data['return']);
                            row++;
                        }
                    }
                });
            } else {
                alert('mohon pilih divisi terlebih dahulu.');
            }
        });
        // add production
        $("#addProduction").click(function() {
            <?php if (is_superadmin()) : ?>
                var division_id = $("#id_division").val();
            <?php else : ?>
                var division_id = '<?=getSessionAdmin('admin_id_division')?>';
            <?php endif; ?>
            if (division_id) {
                $.ajax({
                    url:'<?=$getproduction_url?>',
                    type:'post',
                    dataType:'json',
                    data:{division_id:division_id},
                    success: function(data) {
                        if (data['error']) {
                            $(".display_message").html(data['error']);
                        }
                        if (data['return']) {
                            addProduction(row_production);
                            $("#post_production_id_"+row_production).append(data['return']);
                            row_production++;
                        }
                    }
                });
            } else {
                alert('mohon pilih divisi terlebih dahulu.');
            }
        });
        // add cost
        $("#addOtherCost").click(function() {
            html = '<tr id="row-cost'+ row_cost +'">';
            html += '<td class="cost_desc">';
            html += '<textarea id="post_cost_note_'+ row_cost +'" rows="2" class="input-block-level" name="post_cost['+ row_cost +'][note]" style="resize: none"></textarea>';
            html += '</td>';
            html += '<td class="cost_price">Rp. <input type="text" name="post_cost['+ row_cost +'][cost]" id="post_cost_cost_'+ row_cost +'" value="" class="input-text othercost-input"/></td>';
            html += '<td><button type="button" class="btn btn-danger delOtherCost" id="delOtherCost-'+ row_cost +'" onclick="delOtherCost(\''+row_cost+'\')">(-)</button></td>';
            html += '</tr>';
            $('#tableOtherCost .tableFooter').before(html);
            row_cost++;
        });
        $(".prodSelect").each(function() {
            var this_id = $(this).attr('data-id');
            $(this).select2({
                placeholder: "Pilih Produk"
            }).on('change', function(e) {
                var product_id = e.val;
                if (product_id) {
                    $.ajax({
                        url:'<?=$productinfo_url?>',
                        type:'post',
                        dataType:'json',
                        data:'product_id='+product_id+'&division_id='+division_id,
                        success: function(data) {
                            if (data['error']) {
                                $('.display_message').html(data['error']);
                            }
                            if (data['value']['purchase_price']) {
                                $("#post_product_price_"+this_id).val(data['value']['purchase_price']);
                            }
                        }
                    });
                }
            });
        });
        $(".productionSelect").each(function() {
            var this_id = $(this).attr('data-id');
            $(this).select2({
                placeholder: "Pilih Kode Produksi"
            }).on('change', function(e) {
                var production_id = e.val;
                if (production_id) {
                    $.ajax({
                        url:'<?=$productioninfo_url?>',
                        type:'post',
                        dataType:'json',
                        data:'production_id='+production_id+'&division_id='+division_id,
                        success: function(data) {
                            if (data['error']) {
                                $('.display_message').html(data['error']);
                            }
                            if (data['value']['price']) {
                                $("#post_production_price_"+this_id).val(data['value']['price']);
                            }
                        }
                    });
                }
            });
        });
    });
    
    function addProduct(row) {
        <?php if (is_superadmin()) : ?>
            var division_id = $("#id_division").val();
        <?php else : ?>
            var division_id = '<?=getSessionAdmin('admin_id_division')?>';
        <?php endif; ?>
        html = '<tr id="row'+ row +'">';
        html += '<td class="productOption">';
        html += '<select name="post_product['+ row +'][id_product]" id="post_product_id_'+ row +'">';
        html += '<option value=""></option>';
        html += '</select>';
        html += '</td>';
        html += '<td class="qty"><input type="text" name="post_product['+ row +'][purchase_qty]" id="post_product_qty_'+ row +'" value="" class="input-text"/></td>';
        html += '<td class="price">Rp. <input type="text" name="post_product['+ row +'][purchase_price]" id="post_product_price_'+ row +'" value="" class="input-text" readonly="readonly"/></td>';
        html += '<td><button type="button" class="btn btn-danger delProduct" id="delProduct-'+ row +'" onclick="delProduct(\''+row+'\')">(-)</button></td>';
        html += '</tr>';
        $('#tableProduct .tableFooter').before(html);
        $("#post_product_id_"+ row).select2({
            placeholder: "Pilih Produk"
        }).on('change', function(e) {
            var product_id = e.val;
            if (product_id) {
                $.ajax({
                    url:'<?=$productinfo_url?>',
                    type:'post',
                    dataType:'json',
                    data:'product_id='+product_id+'&division_id='+division_id,
                    success: function(data) {
                        if (data['error']) {
                            $('.display_message').html(data['error']);
                        }
                        if (data['value']['sell_price']) {
                            $('#post_product_price_'+(row)).val(data['value']['purchase_price']);
                        }
                    }
                });
            }
        });
        $("#post_product_id_"+ row).select2("enable");
        //row++;
    }
    
    function addProduction(row) {
        <?php if (is_superadmin()) : ?>
            var division_id = $("#id_division").val();
        <?php else : ?>
            var division_id = '<?=getSessionAdmin('admin_id_division')?>';
        <?php endif; ?>
        html = '<tr id="row-production'+ row +'">';
        html += '<td class="productionOption">';
        html += '<select name="post_production['+ row +'][id_production]" id="post_production_id_'+ row +'">';
        html += '<option value=""></option>';
        html += '</select>';
        html += '</td>';
        html += '<td class="price">Rp. <input type="text" name="post_production['+ row +'][production_price]" id="post_production_price_'+ row +'" value="" class="input-text" readonly="readonly"/></td>';
        html += '<td><button type="button" class="btn btn-danger delProduction" id="delProduction-'+ row +'" onclick="delProduction(\''+row+'\')">(-)</button></td>';
        html += '</tr>';
        $('#tableProduction .tableFooter').before(html);
        $("#post_production_id_"+ row).select2({
            placeholder: "Pilih Kode Produksi"
        }).on('change', function(e) {
            var production_id = e.val;
            if (production_id) {
                $.ajax({
                    url:'<?=$productioninfo_url?>',
                    type:'post',
                    dataType:'json',
                    data:'production_id='+production_id+'&division_id='+division_id,
                    success: function(data) {
                        if (data['error']) {
                            $('.display_message').html(data['error']);
                        }
                        if (data['value']['price']) {
                            $('#post_production_price_'+(row)).val(data['value']['price']);
                        }
                    }
                });
            }
        });
        $("#post_production_id_"+ row).select2("enable");
        //row++;
    }
    
    function delProduct(id) {
        $("#row"+id).remove();
        row--;
    }
    
    function delProduction(id) {
        $("#row-production"+id).remove();
    }
    
    function delOtherCost(row) {
        $("#row-cost"+row).remove();
    }
    
</script>
