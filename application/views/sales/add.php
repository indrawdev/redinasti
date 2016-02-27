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
                    <!--
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="sales_invoice">No Faktur</label>
                            <input type="text" id="purchase_invoice" name="sales_invoice" value="<?=(isset($post['sales_invoice'])) ? $post['sales_invoice'] : ''?>"/>
                        </div>
                    </div>
                    -->
                    <div class="row-fluid" style="margin-bottom: 20px;">
                        <div class="span12">
                            <label for="id_store">Toko</label>
                            <select name="id_store" id="id_store">
                                <option value=""></option>
                                <?php foreach ($stores as $store) : ?>
                                    <option value="<?=$store['id_store']?>" <?php (isset($post['id_store']) && $post['id_store'] == $store['id_store']) ? 'selected="selected"' : '' ?>><?=$store['store']?></option>
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
                            <label for="sales_note">Catatan</label>
                            <textarea id="sales_note" rows="4" class="input-block-level" name="sales_note" style="resize: none"><?=(isset($post['sales_note'])) ? $post['sales_note'] : ''?></textarea>
                        </div>
                    </div>
                    <!--
                    <div class="row-fluid">
                        <div class="span12">
                            <label for="other_cost">Potongan Lain-lain</label>
                            <input type="text" id="driver" name="other_cost" value="<?=(isset($post['other_cost'])) ? $post['other_cost'] : ''?>"/>
                        </div>
                    </div>
                    -->
                </fieldset>
                <fieldset class="listProduction">
                    <legend>Barang</legend>
                    <div class="genProduction">
                        <table class="table table-striped table-bordered table-hover" id="tableProduction">
                            <thead>
                                <tr>
                                    <th class="center" id="production_name">Nama Barang <span></span></th>
                                    <th class="center" id="price">Harga Jual <span></span></th>
                                    <th class="center" id="discount">Diskon (%) <span></span></th>
                                    <th class="center" id="total">Total <span></span></th>
                                    <th class="center" style="width:40px;" >Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($post['post_production']) && count($post['post_production'])>0) : $i=0; ?>
                                <?php foreach ($post['post_production'] as $prod) : ?>
                                    <tr id="row-production<?=$i?>">
                                        <td class="productionOption">
                                            <select name="post_production[<?=$i?>][id_production]" id="post_production_id_<?=$i?>" class="productionSelect" data-id="<?=$i?>">
                                                <option value=""></option>
                                                <?php foreach ($productions as $production) : ?>
                                                    <option value="<?=$production['id_production']?>" <?php echo (isset($prod['id_production']) && ($prod['id_production'] == $production['id_production'])) ? 'selected="selected"' : '';  ?>><?=$production['production_code'].' ('.$production['item_name'].')'?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="price"><input type="text" name="post_production[<?=$i?>][price]" id="post_production_price_<?=$i?>" value="<?=(isset($prod['price'])) ? $prod['price'] : '' ?>" class="input-text number_only" placeholder="Rp. "/></td>
                                        <td class="discount"><input type="number" min="0" max="100" name="post_production[<?=$i?>][discount]" id="post_production_discount_<?=$i?>" value="<?=(isset($prod['discount'])) ? $prod['discount'] : '' ?>" class="input-text number_only" placeholder="% "/></td>
                                        <td class="total"><input type="text" name="post_production[<?=$i?>][total]" id="post_production_total_<?=$i?>" value="<?=(isset($prod['price']) && isset($prod['discount'])) ? ($prod['price']-ceil(($prod['discount']/100)*$prod['price'])) : ''?>" class="input-text number_only" placeholder="Rp. " disabled="disabled"/></td>
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
    var row = '<?=$production_count;?>',
        store_id = $("#id_store").val();
    $(function() {
        $("#id_store").select2({
            placeholder: "Pilih Toko"
        }).on('change', function(e) {
            $("table#tableProduction tbody tr").not('.tableFooter').empty();
        });
        
        $("#addProduction").click(function() {
            addProduction();
        });
        $(".productionSelect").each(function() {
            var val_price = 0,
                val_discount = 0;
            var this_id = $(this).attr('data-id');
            var this_total = $("#post_production_total_"+this_id);
            $(this).select2({
                placeholder: "Pilih Kode Produksi"
            }).on('change', function(e) {
                var production_id = e.val;
                if (production_id) {
                    $.ajax({
                        url:'<?=$productioninfo_url?>',
                        type:'post',
                        dataType:'json',
                        data:'production_id='+production_id+'&store_id='+store_id,
                        success: function(data) {
                            if (data['error']) {
                                $('.display_message').html(data['error']).focus();
                            }
                            if (data['value']['sales_production'] && data['value']['sales_production']['sales_price']) {
                                $('#post_production_price_'+this_id).val(data['value']['sales_production']['sales_price']);
                                val_price = data['value']['sales_production']['sales_price'];
                            } else if (data['value']['production_sell_price']) {
                                $('#post_production_price_'+this_id).val(data['value']['production_sell_price']);
                                val_price = data['value']['production_sell_price'];
                            }
                            if (data['value']['discount_percentage'] && data['value']['sales_production']['discount_percentage']) {
                                $('#post_production_discount_'+this_id).val(data['value']['sales_production']['discount_percentage']);
                                val_discount = data['value']['sales_production']['discount_percentage'];
                            }
                        }
                    });
                }
            });
            $("#post_production_price_"+ this_id).on('focusout', function(e) {
                if (val_discount > 0) {
                    var calculate = calculate_discount($(this).val(),val_discount);
                    this_total.val(calculate);
                }
                val_price = $(this).val();
            });
            $("#post_production_discount_"+ this_id).on('focusout', function(e) {
                if (val_price > 0) {
                    var calculate = calculate_discount(val_price,$(this).val());
                    this_total.val(calculate);
                }
                val_discount = $(this).val();
            });
        }); 
    });
    function addProduction() {
        var store_id = $("#id_store").val();
        html = '<tr id="row-production'+ row +'">';
        html += '<td class="productionOption">';
        html += '<select name="post_production['+ row +'][id_production]" id="post_production_id_'+ row +'" data-id="'+row+'">';
        html += '<option value=""></option>';
        <?php foreach ($productions as $production) : ?>
            html += '<option value="<?=$production['id_production']?>"><?=$production['production_code'].' ('.$production['item_name'].')'?></option>';
        <?php endforeach; ?>
        html += '</select>';
        html += '</td>';
        html += '<td class="price"><input type="text" name="post_production['+ row +'][price]" id="post_production_price_'+ row +'" value="" class="input-text number_only" placeholder="Rp. "/></td>';
        html += '<td class="discount"><input type="number" min="0" max="100" name="post_production['+ row +'][discount]" id="post_production_discount_'+ row +'" value="" class="input-text number_only" placeholder="% "/></td>';
        html += '<td class="total"><input type="text" name="post_production['+ row +'][total]" id="post_production_total_'+ row +'" value="" class="input-text" placeholder="Rp. " disabled="disabled"/></td>';
        html += '<td><button type="button" class="btn btn-danger delProduction" id="delProduction-'+ row +'" onclick="delProduction(\''+row+'\')">(-)</button></td>';
        html += '</tr>';
        $('#tableProduction .tableFooter').before(html);
        var val_price = 0,
            val_discount = 0;
        var this_total = $("#post_production_total_"+row);
        $("#post_production_id_"+ row).select2({
            placeholder: "Pilih Barang"
        }).on('change', function(e) {
            var production_id = e.val,
                seltor = $(e.target).attr('data-id');
            if (production_id) {
                $.ajax({
                    url:'<?=$productioninfo_url?>',
                    type:'post',
                    dataType:'json',
                    data:'production_id='+production_id+'&store_id='+store_id,
                    success: function(data) {
                        if (data['error']) {
                            $('.display_message').html(data['error']).focus();
                        }
                        if (data['value']['sales_production'] && data['value']['sales_production']['sales_price']) {
                            $('#post_production_price_'+seltor).val(data['value']['sales_production']['sales_price']);
                            val_price = data['value']['sales_production']['sales_price'];
                        } else if (data['value']['production_sell_price']) {
                            $('#post_production_price_'+seltor).val(data['value']['production_sell_price']);
                            val_price = data['value']['production_sell_price'];
                        }
                        if (data['value']['discount_percentage'] && data['value']['sales_production']['discount_percentage']) {
                            $('#post_production_discount_'+seltor).val(data['value']['sales_production']['discount_percentage']);
                            val_discount = data['value']['sales_production']['discount_percentage'];
                        }
                    }
                });
            }
        });
        $("#post_production_price_"+ row).on('focusout', function(e) {
            if (val_discount > 0) {
                var calculate = calculate_discount($(this).val(),val_discount);
                this_total.val(calculate);
            }
            val_price = $(this).val();
        });
        $("#post_production_discount_"+ row).on('focusout', function(e) {
            if (val_price > 0) {
                var calculate = calculate_discount(val_price,$(this).val());
                this_total.val(calculate);
            }
            val_discount = $(this).val();
        });
        number_only('.number_only');
        $("#post_production_id_"+ row).select2("enable");
        row++;
    }
    
    function delProduction(id) {
        $("#row-production"+id).remove();
        //row--;
    }
    
    function calculate_discount(price,percentage) {
        price = (price) ? price : 0;
        percentage = (percentage) ? percentage : 0;
        var calc_price = Math.ceil((percentage/100) * price);
        var total = price - calc_price;
        
        return total;
    }
    
</script>
