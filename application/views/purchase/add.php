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
                            <label for="id_division">Divisi</label>
                            <!--<input type="hidden" id="id_supplier" name="id_supplier" class="bigdrop" value=""/>-->
                            <select name="id_division" id="id_division">
                                <option value=""></option>
                                <?php foreach ($divisions as $division) : ?>
                                    <option value="<?=$division['id_division']?>" <?= (isset($post['id_division']) && ($post['id_division'] == $division['id_division'])) ? 'selected="selected"' : '' ?>><?=$division['division']?></option>
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
            <div class="span12">
                <fieldset class="listProduct">
                    <legend>Barang Produksi</legend>
                    <div class="genProduction">
                        <table class="table table-striped table-bordered table-hover" id="tableProduction">
                            <thead>
                                <tr>
                                    <th class="center" id="product_code">Kode Produksi <span></span></th>
                                    <th class="center" id="product_name">Nama Barang <span></span></th>
                                    <th class="center" id="qty">QTY <span></span></th>
                                    <th class="center" id="price">Harga Jual <span></span></th>
                                    <!--
                                    <th class="center" id="hpp">HPP <span></span></th>
                                    <th class="center" id="discount">Diskon <span></span></th>
                                    -->
                                    <th class="center" style="width:40px;" >Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($post['post_production']) && count($post['post_production'])>0) : $i=0; ?>
                                <?php foreach ($post['post_production'] as $prod) : ?>
                                    <tr id="row<?=$i?>">
                                        <td class="code"><input type="text" name="post_production[<?=$i?>][code]" id="post_production_code_<?=$i?>" value="<?=(isset($prod['code'])) ? $prod['code'] : '' ?>" class="input-text"/></td>
                                        <td class="name productionOption">
                                            <select name="post_production[<?=$i?>][id]" id="post_production_id_<?=$i?>" class="productionSelect" data-id="<?=$i?>">
                                            <?php foreach ($items as $item) : ?>
                                                    <option value="<?=$item['id_item']?>" <?=(isset($prod['id']) && $prod['id'] == $item['id_item']) ? 'selected="selected"' : '' ?>><?=$item['item_category'].' ('.$item['item_name'].')'?></option>
                                            <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="qty"><input type="number" min="1" max="1" name="post_production[<?=$i?>][qty]" id="post_production_qty_<?=$i?>" value="<?=(isset($prod['qty'])) ? $prod['qty'] : '' ?>" class="input-text number_only" readonly="readonly"/></td>
                                        <td class="price"><input type="text" name="post_production[<?=$i?>][price]" id="post_production_price_<?=$i?>" value="<?=(isset($prod['price'])) ? $prod['price'] : '' ?>" class="input-text number_only" placeholder="Rp. "/></td>
                                        <input type="hidden" name="post_production[<?=$i?>][hpp]" id="post_production_hpp_<?=$i?>" value="<?=(isset($prod['hpp'])) ? $prod['hpp'] : '' ?>" class="input-text number_only" placeholder="Rp. "/>
                                        <input type="hidden" name="post_production[<?=$i?>][discount]" id="post_production_hpp_<?=$i?>" value="<?=(isset($prod['discount'])) ? $prod['discount'] : '' ?>" class="input-text number_only" placeholder="Rp. "/>
                                        <!--
                                        <td class="hpp"></td>
                                        <td class="discount"><input type="text" name="post_production[<?=$i?>][discount]" id="post_production_hpp_<?=$i?>" value="<?=(isset($prod['discount'])) ? $prod['discount'] : '' ?>" class="input-text number_only" placeholder="Rp. "/></td>
                                        -->
                                        <td><button type="button" class="btn btn-danger delProduct" id="delProduct-<?=$i?>" onclick="delProduct('<?=$i?>')">(-)</button></td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            <?php endif; ?>
                            <tr class="footer tableFooter" id="tableFooter">
                                <td colspan="7"><button type="button" class="btn btn-success pull-right" id="addProduction">(+)</button></td>
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
    var row = <?=$production_count?>;
    var division_id = $("#id_division").val();
    $(function() {
        $("#addProduction").click(function() {
            <?php if (is_superadmin()) : ?>
                var division_id = $("#id_division").val();
            <?php else : ?>
                var division_id = '<?=getSessionAdmin('admin_id_division')?>';
            <?php endif; ?>
            if (division_id) {
                $.ajax({
                    url:'<?=$getitem_url?>',
                    type:'post',
                    dataType:'json',
                    data:{division_id:division_id},
                    success: function(data) {
                        if (data['error']) {
                            $(".display_message").html(data['error']).focus();
                        }
                        if (data['return']) {
                            addProduction(row);
                            $("#post_production_id_"+row).append(data['return']);
                            row++;
                        }
                    }
                });
            } else {
                alert('mohon pilih divisi terlebih dahulu.');
            }
        });
        $("#id_division").select2({
            placeholder: "Pilih Divisi"
        }).on('change', function(e) {
            var division_id = e.val;
            $("table#tableProduction tbody tr").not('.tableFooter').empty();
        }); 
        $(".productionSelect").each(function() {
            var this_id = $(this).attr('data-id');
            $(this).select2({
                placeholder: "Pilih Kode Barang"
            }).on('change', function(e) {
                var item_id = e.val;
                if (item_id) {
                    $.ajax({
                        url:'<?=$iteminfo_url?>',
                        type:'post',
                        dataType:'json',
                        data:'item_id='+item_id+'&division_id='+division_id,
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
    
    function addProduction(row_) {
        <?php if (is_superadmin()) : ?>
            var division_id = $("#id_division").val();
        <?php else : ?>
            var division_id = '<?=getSessionAdmin('admin_id_division')?>';
        <?php endif; ?>
        html = '<tr id="row'+ row_ +'">';
        html += '<td class="code"><input type="text" name="post_production['+ row_ +'][code]" id="post_production_code_'+ row_ +'" value="" class="input-text"/></td>';
        html += '<td class="name productionOption">';
        html += '<select name="post_production['+ row_ +'][id]" id="post_production_id_'+ row_ +'" data-id="'+row_+'">';
        html += '<option value=""></option>';
        html += '</select>';
        html += '</td>';
        html += '<td class="qty"><input type="number" min="1" max="1" name="post_production['+ row_ +'][qty]" id="post_production_qty_'+ row_ +'" value="1" class="input-text number_only" readonly="readonly"/></td>';
        html += '<td class="price"><input type="text" name="post_production['+ row_ +'][price]" id="post_production_price_'+ row_ +'" value="" class="input-text number_only" placeholder="Rp. "/></td>';
        html += '<input type="hidden" name="post_production['+ row_ +'][hpp]" id="post_production_hpp_'+ row_ +'" value="" class="input-text number_only" placeholder="Rp. "/>';
        html += '<input type="hidden" name="post_production['+ row_ +'][discount]" id="post_production_discount_'+ row_ +'" value="" class="input-text number_only" placeholder="Rp. "/>';
        /*
        html += '<td class="hpp"><input type="text" name="post_production['+ row_ +'][hpp]" id="post_production_hpp_'+ row_ +'" value="" class="input-text number_only" placeholder="Rp. "/></td>';
        html += '<td class="discount"><input type="text" name="post_production['+ row_ +'][discount]" id="post_production_discount_'+ row_ +'" value="" class="input-text number_only" placeholder="Rp. "/></td>';
        */
        html += '<td><button type="button" class="btn btn-danger delProduction" id="delProduction-'+ row_ +'" onclick="delProduction(\''+row_+'\');">(-)</button></td>';
        html += '</tr>';
        $('#tableProduction #tableFooter').before(html);
        $("#post_production_id_"+ row_).select2({
            placeholder: "Pilih Nama Barang"
        }).on('change', function(e) {
            var item_id = e.val,
                seltor = $(e.target).attr('data-id');
            if (item_id) {
                $.ajax({
                    url:'<?=$iteminfo_url?>',
                    type:'post',
                    dataType:'json',
                    data:'item_id='+item_id+'&division_id='+division_id,
                    success: function(data) {
                        if (data['error']) {
                            $('.display_message').html(data['error']).focus();
                        }
                        if (data['value']['hpp']) {
                            $('#post_production_hpp_'+(seltor)).val(data['value']['hpp']);
                        }
                        if (data['value']['price']) {
                            $('#post_production_price_'+(seltor)).val(data['value']['price']);
                        }
                    }
                });
            }
        });
        $("#post_production_id_"+ row_).select2("enable");
        number_only('.number_only');
    }
    
    function delProduction(id) {
        $("#row"+id).remove();
        //row--;
    }
    
</script>
