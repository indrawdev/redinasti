	$(function () {
		$('#form1').validate();
		$('#form_product').validate();
		$('#save').click(function(){
			var val = $(this).html();
			if(val == loadingBtn){return}
			if($("#form1").validate().form() == true) {
				$.ajax({
					url         : current_ctrl+'add_ref',
					type        : "POST",
					data        : $('#form1').serialize(),
					beforeSend  : function(){$('#save').html(loadingBtn);},
					success     : function(msg){
									if(msg.substring(0,1) == '0'){
										$('p#message > span.message').html(msg.substring(1));
										$('#message').show();
										//$('#message').addClass('error');
										$('#save').html(val);
									}
									else{
										var id_select = $('#select_type').val();
										var class_select = $('#'+id_select).attr('class');
										if(class_select=='span11 prod_varian'){
											$('.prod_varian').append(msg);
										}
										else{
											$('#'+id_select).append(msg);
										}
										var lastValue = $('#'+id_select+' option:last-child').val();
										$('#'+id_select).val(lastValue);
										$('#addNew').modal('hide');
										restore_form();
									}
					},
					error		: function (xhr, ajaxOptions, thrownError) {
										alert('Error!');
										//alert(xhr.status);
										//alert(thrownError);
					}
				});
			}
		})

		$('#save_product').click(function(){
			var val = $(this).html();
			if(val == loadingBtn){return}
			var sku 			= $('#sku').val();
			var product_name 	= $('#product_name').val();
			var product_handle 	= $('#product_handle').val();
			if(product_name == '' || product_handle == ''){
				open_tab(1);
			}
			if(sku==''){
				open_tab(4);
			}
			
			if($("#form_product").validate().form() == true) {
				var img = $('#image').val().split('.');
				img = img[img.length-1].toLowerCase();
				if(img != 'jpg' && img!='jpeg' && img!='png' && img!='gif' && img != ''){
					$('p#message_product > span.message_product').html('Invalid image type');
					$('#message_product').show();
					return false;
				}
				else{
					$('p#message_product > span.message_product').html('');
					$('#message_product').hide();
				}
				
				$.ajax({
					url         : current_ctrl+'proses/cek',
					type        : "POST",
					data        : $('#form_product').serialize(),
					beforeSend  : function(){$('#save_product').html(loadingBtn);},
					error		: function () {alert('Error!');},
					success     : function(msg){
									if(msg.substring(0,1) == '0'){
										$('p#message_product > span.message_product').html(msg.substring(1));
										$('#message_product').show();
										$('#save_product').html(val);
										location.hash = 'top';
									}
									else{
										$('#save_product').html(val);
										if(msg=='ok'){
											$('#form_product').submit();
										}
									}
					}
				});
			}
			//else{
			//	location.hash = 'top';
			//}
		})

		$('#save_cust').click(function(){
			var val = $(this).html();
			if(val == loadingBtn){return}
			//if($("#form_customer").validate().form() == true) {
				$.ajax({
					url         : current_ctrl+'proses/'+xid,
					type        : "POST",
					data        : $('#form_customer').serialize(),
					beforeSend  : function(){$('#save_cust').html(loadingBtn);},
					error		: function () {alert('Error!');$('#save_cust').html(val);},
					success     : function(msg){
									if(msg.substring(0,1) == '0'){
										$('p#message_product > span.message_product').html(msg.substring(1));
										$('#message_product').show();
										$('#save_cust').html(val);
										location.hash = 'top';
									}
									else{
										window.location.href=current_ctrl;
									}
					}
				});
			//}
		})

	});	

	$("#tags").autocomplete({source: current_ctrl+"ac_tags"});

	$('#cancel_product,#cancel_cust').click(function(){
		window.location.href=current_ctrl;
	})
	
	
	$('.count_rp').keyup(function(){
		var suply_price				= parseFloat($('#suply_price').val());
		var markup					= parseFloat($('#markup').val());
		var hasil					= (markup > 0.0) ? (suply_price + ((markup/100) * suply_price)): suply_price;
		var pajak					= 'a'+$('#ref_tax_id').val();
		var tax_val 				= parseFloat(tax[pajak]);
		var pajaknya				= hasil / 100 * tax_val;
		var retail_price_aft_tax 	= pajaknya + hasil;
		$('#retail_price').val(hasil);
		$('#retail_price_tax').html(pajaknya);
		$('#retail_price_aft_tax').val(retail_price_aft_tax);
		
	})
	$('#retail_price').keyup(function(){
		var suply_price				= parseFloat($('#suply_price').val());
		var retail_price			= parseFloat($('#retail_price').val());
		var selisih					= retail_price-suply_price;
		var hasil					= (suply_price > 0.0) ? + ((selisih/suply_price) * 100): suply_price;
		var pajak					= 'a'+$('#ref_tax_id').val();
		var tax_val 				= parseFloat(tax[pajak]);
		var pajaknya				= retail_price / 100 * tax_val;
		var retail_price_aft_tax 	= pajaknya + retail_price;
		$('#markup').val(hasil);
		$('#retail_price_tax').html(pajaknya);
		$('#retail_price_aft_tax').val(retail_price_aft_tax);
	})
	$('#ref_tax_id').change(function(){
		pajak();
	})
	function pajak(){
		var pajak					= 'a'+$('#ref_tax_id').val();
		var retail_price			= parseFloat($('#retail_price').val());
		var tax_val 				= parseFloat(tax[pajak]);
		if(retail_price){
			var pajaknya				= retail_price / 100 * tax_val;
			$('#retail_price_tax').html(pajaknya);
			$('#retail_price_aft_tax').val(pajaknya + retail_price);
		}
	}
	pajak();
	$('#retail_price_aft_tax').keyup(function(){
		var retail_price_aft_tax 	= parseFloat($(this).val());
		var pajak					= 'a'+$('#ref_tax_id').val();
		var tax_val 				= parseFloat(tax[pajak]);
		var suply_price				= parseFloat($('#suply_price').val());
		var retail_price			= (tax_val > 0.0) ? ((retail_price_aft_tax * tax_val) / (tax_val+1)) : retail_price_aft_tax;
		$('#retail_price').val(retail_price);
		var selisih					= retail_price-suply_price;
		var hasil					= (suply_price > 0.0) ? + ((selisih/suply_price) * 100): suply_price;
		$('#markup').val(hasil);
	})
	

	$('#add_varian').click(function(){
		var varian2 = $('#varian2').attr('class');
		var varian3 = $('#varian3').attr('class');
		if(varian2 == 'span11 new_varian'){
			$('#varian2').removeClass('new_varian');
			return
		}
		if(varian3 == 'span11 new_varian'){
			$('#varian3').removeClass('new_varian');
			$('#max_varian').removeClass('new_varian');
			$('#add_varian').addClass('new_varian');
		}
	})

	$('.hide_varian').click(function(){
		var id = $(this).attr('data-var');
		$('#varian'+id).addClass('new_varian');
		$('#varian'+id).find('select').val('');
		$('#varian'+id).find('input').val('');
		$('#max_varian').addClass('new_varian');
		$('#add_varian').removeClass('new_varian');
	})
	function varian(){
		var cek = $('#is_varian').attr('checked');
		if(cek){
			$('#varian1,#add_varian,#varian_header').removeClass('new_varian');
		}
		else{
			$('#varian1,#add_varian,#varian2,#varian3,#varian_header,#max_varian').addClass('new_varian');
			$('#varian1,#varian2,#varian3').find('select').val('');
			$('#varian1,#varian2,#varian3').find('input').val('');
			
		}
		var varian3 = $('#varian3').attr('class');
		if(varian3 == 'span11'){
			$('#varian3').removeClass('new_varian');
			$('#max_varian').removeClass('new_varian');
			$('#add_varian').addClass('new_varian');
		}
	}
	$('#is_varian').click(function(){
		varian();
	})


	$('.cancel').click(function(){
		var id = $('#select_type').val();
		$('#'+id).val('');
		$('#addNew').modal('hide');
	})
	$('select').change(function(){
		var val = $(this).val();
		if(val == 'addNew'){
			var id = $(this).attr('id');
			var judul;
			$('#desc').hide();
			$('#variant').hide();
			if(id =='ref_product_brand_id'){
				judul = 'Brand';
				$('#desc').show();
			}
			else if(id=='ref_product_type_id'){
				judul = 'Type';
			}
			else if(id.substring(0,21)=='ref_product_varian_id'){
				judul = 'Varian';
				$('#variant').show();
				$('#default_value_label').html('value');
			}
			else if(id=='ref_supplier_id'){
				judul = 'Supplier';
				$('#desc').show();
				$('#variant').show();
				$('#default_value_label').html('markup');
			}
			$('#addNew').modal('show');
			$('.popup_title').html(judul);
			$('#select_type').val(id);
		}
	})
	
	$('#add_tags').click(function(){
		var val = $('#tags').val();
		var cek = 0;
		var n = 0;
		if(val != ''){
			$('#tags_product').find('code').each(function(){
				if(val == $(this).html()){
					cek = 1;
				}
				n = parseInt($(this).attr('data-tags')) +1;
			})
			if(cek==0){
				$('#tags_product').append("<span id='tags"+n+"'> \
											<input type='hidden' value='"+val+"' name='tags[]'> \
											<code data-tags='"+n+"'>"+val+"</code> \
											<i class='icon-remove-sign tangan'  data-tags='"+n+"'></i> \
											</span>");
				$('#tags').val('');
				$('i').click(function(){
					var tag = $(this).attr('data-tags');
					$('#tags'+tag).remove();
				})
			}
			else{
				$('#tags').focus();
			}
		}
		else{
			$('#tags').focus();
		}
	})
	$('.ui-accordion-header').click(function(){
		var area = $(this).attr('data-area');
		$('.ui-accordion div').find('div.ui-accordion-content').each(function(){
			if($(this).attr('data-area')== area){
				$(this).toggle();
			}
		})
		var cek = $(this).find('i').attr('class');
		$(this).find('i').removeClass('icon-caret-right icon-caret-down');
		if(cek == 'icon-caret-down'){
			$(this).find('i').addClass('icon-caret-right');
		}
		else{
			$(this).find('i').addClass('icon-caret-down');
		}
	})

	varian();
	$('input.numeric').numeric();
	$('#product_handle').alphanumeric({allow:".-_"});
    //proses variant
	$(function () {
		$('#save_variant').click(function(){
			var val = $(this).html();
			if(val == loadingBtn){return}
			if($('#sku').val()==''){
				open_tab(3);
			}
			if($("#form_product_varian2").validate().form() == true) {
				$.ajax({
					url         : current_ctrl+'proses_varian/'+id_prod+'/'+id_varian_prod,
					type        : "POST",
					data        : $('#form_product_varian2').serialize(),
					beforeSend  : function(){$('#save_variant').html(loadingBtn);},
					error		: function (err) {
									alert('Server Error!');
									$('#save_variant').html(val);
					},
					success     : function(msg){
									if(msg.substring(0,1) == '0'){
										$('p#message_product > span.message_product').html(msg.substring(1));
										$('#message_product').show();
										$('#save_variant').html(val);
									}
									else{
										window.location.href=current_ctrl+"view/"+id_prod;
									}
					}
				});
			}
		})

	});	
    
function open_tab(id){
	$('.ui-accordion div').find('div.ui-accordion-content').each(function(){
				if($(this).attr('data-area')== id){
					$(this).show();
				}
				
				$('.ui-accordion-header').each(function(){
					if($(this).attr('data-area') == id){
						$(this).find('i').removeClass('icon-caret-right').addClass('icon-caret-down');
					}
				})
				//$("#form_product").validate().form();
			})
}

$('#product_name').keyup(function(){
	var val = $(this).val().replace(/[^a-zA-Z0-9._-]/g, '');
	//val.replace(/[^0-9.-]/g, ''));
	$('#product_handle').val(val);

})
