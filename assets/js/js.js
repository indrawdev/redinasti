var lastSel;
var tinggi 		= 300;
var imgPath 	= base_url + 'assets/images/icon/';
var idGridnya 	= 'gridData';
var saveBtn 	= '<i class="icon-save"></i> Save';
var updateBtn 	= '<i class="icon-save"></i> Update';
var loadingBtn 	= '<i class="icon-spin icon-spinner"></i> Loading...';
function pickdates(){};



function gridParam(grid_id)
{
	jQuery("#"+grid_id).jqGrid('gridResize');
	jQuery("#"+grid_id).jqGrid('navGrid','#p'+grid_id,{add:false,edit:false,del:false,search:false});	
	jQuery("#"+grid_id).jqGrid('filterToolbar');
}

//biar bisa navigasi grid menggunakan up n down key
function navGrid(idGrid){
	idGrid = (idGrid) ? idGrid : 'gridData';
	idGrid = '#' + idGrid;
	jQuery(idGrid).jqGrid('bindKeys', {
		'onEnter':function( id ) {
			jQuery(idGrid).jqGrid('saveRow',id,
				function(ret){ //success function
					var result	= ret.responseText; // response ie still suck
					var sukses	= result.substring(0,1);
					var pesan	= result.substring(1);
					alert(pesan);
					if(sukses == '1'){
						//jInfo(pesan);
						if(pesan == 'Input Success') {jQuery(idGrid).jqGrid('setGridParam',{page: 1}).trigger('reloadGrid');}
						$(idGrid).focus();
						return true;
					}
					//else{
						//jAlert(pesan);
						//$(idGrid).focus();
					//}
				}
			);
		}
	});
}

function dbl_click(id,gridId){
	gridId = (gridId==true) ? 'gridData' : gridId;
	gridId = '#' + gridId;
	jQuery(gridId).jqGrid('editRow',id,false,pickdates);
	$('select.editable,input:text.editable').attr('class','frm_grid');	
}
function select_row(id,gridId){
	gridId = (gridId==true) ? 'gridData' : gridId;
	gridId = '#' + gridId;
	if(id && id!==lastSel){
		jQuery(gridId).jqGrid('restoreRow',lastSel);
		jQuery(gridId).jqGrid('editRow',id,false,pickdates);
		if(lastSel==0) {$(gridId).jqGrid('delRowData',0);}
		lastSel=id;
		$('select.editable,input:text.editable').attr('class','frm_grid');
	}
}

function gridKomplit(gridId){
	gridId 	= (gridId) ? gridId : 'gridData';
	gridId 	= '#' + gridId;
	var ids	= jQuery(gridId).jqGrid('getDataIDs');
	var id	= ids[0];
	if(id != 0){lastSel = id}
	jQuery(gridId).setSelection(ids[0]);
	$(gridId).focus();
}


/**
	*@author Agung agung_doanks@yahoo.co.id
	*@description popup window versi jquery
	*@param {string} id element
	*@param {int} width : width of popup 
	*@param {int} height : height of popup 
 */
function jPopup(idForm,width,height){
	width  = (width==null) ? 250 : width;
	height = (height==null) ? 300 : height;
	$( "#"+idForm ).dialog({
		autoOpen	: false,
		height	: height,
		width		: width,
		modal		: true,
		closeOnEscape : false,
		show		:"fade",
		hide		:"fade"
	});
	$(  "#"+idForm ).dialog( "open" );
}

function toggle_frm(label,frm){
	$('#'+frm).toggle(500);
	//$('.grid').toggle(500);
	if(label.html() == '[+]'){
		label.html('[-]');
	}
	else{
		label.html('[+]');
	}
	restore_form();
}
function restore_form(idform,idmessage,idbutton){
	if(idform == null) 		idform = '#form1';
	if(idmessage == null) 	idmessage = '#message';
	if(idbutton == null) 	idbutton = '#save';
	clear_form_elements(idform);
	//$(idmessage).removeClass('error');
	$(idmessage).hide();
	$(idbutton).html(saveBtn);
	$(idform+'_new').hide();
	//$(idform).validationEngine('hide');
	//bootstrap validation
	$(idform).find('.control-group').each(function() {
			$(this).removeClass('error');
	})
	$('.field-validation-error').html('');
	
}
function clear_form_elements(ele) {
	var kelas;
    $(ele).find(':input').each(function() {
		kelas = $(this).attr('class');
		if(kelas){
			if(kelas.substr(0,8) != 'no_clear'){ //class no_clear di skip. note : class no clear harus disebutkan pertama
				//if(this.name.substr(0,4) != 'grup'){//versi lama, masih di pake di auth grup
				  switch(this.type) {
						case 'password':
						case 'select-multiple':
						case 'select-one':
						case 'text':
						case 'file':
						case 'hidden':
						case 'textarea':
							$(this).val('');
							break;
						case 'checkbox':
						case 'radio':
							this.checked = false;
					}
				//}
			}
		}
	});
}

function auth_system(insert,update,del){
	var kelas;
	var invis 	= 'display:none';
	 $('body').find('.insert,.update,.delete,.insert_update').each(function() {
		kelas = $(this).attr('class');
		if(kelas == 'insert_update'){
			if(this.value.toLowerCase() == 'update'){
				if(update != 1){
					$(this).attr('style',invis);
				}
			}
			else if(insert != 1){
				$(this).attr('style',invis);
			}
		}
		else if(kelas == 'insert' && insert != 1){
			$(this).attr('style',invis);
		}
		else if(kelas == 'update' && update != 1){
			$(this).attr('style',invis);
		}
		else if(kelas == 'delete' && del != 1){
			$(this).attr('style',invis);
		}
	});
}
function ajax_filter_select(id,url){
	var select = $('#'+id);
	$.ajax({
		url				:	url,
		beforeSend		: function(){select.html("<option value=''>Loading...</option>")},
		success			:	function(ret){select.html(ret)}
	});
}
function delete_selected_grid(idGrid,url){
	restore_form();
	var s;
	var gridID = "#"+idGrid;
	s = jQuery(gridID).jqGrid('getGridParam','selarrrow');
	if(s == ''){
		jAlert('Select Row!');
		return false;
	}
	if(confirm('Delete ?')){
		del_row(url,s,idGrid);
	}
	//jConfirm('Delete ?',"del_row('"+url+"','"+s+"','"+idGrid+"')",true);
}
function del_row(url,id,gridID){
	gridID = (gridID=='') ? '#gridData' : "#"+gridID;
	jQuery(gridID).jqGrid('setGridParam',{}).trigger('reloadGrid');
	restore_form();
	$.ajax({
			type			: 'POST',
			url         : url,
			data			: 'iddel='+ id,
			success     : function(msg){
								//jInfo(msg);
								alert(msg);
								if(msg=='Delete Success'){
								jQuery(gridID).jqGrid('setGridParam',{}).trigger('reloadGrid');
								}
							}
	});
}
function delete_row(idDel,url,idGrid){
	idGridnya = idGrid;
	//jConfirm('Delete ?',"del_row('"+url+"',"+idDel+",'"+idGrid+"')",true);
	if(confirm('Delete ?')){
		del_row(url,idDel,idGridnya);
	}
}

function datepicker_period(id_start,id_end){//datepicker period
	$(function() {
			var dates = $( "#"+id_start+", #"+id_end ).datepicker({
				dateFormat: 'dd-mm-yy', 
				defaultDate: "+1w",
				numberOfMonths: 1,
				beforeShow: function(input) {$(input).unbind('blur')},
				onClose: function(dateText, inst) {$(this).validationEngine();this.focus();this.blur()},
				onSelect: function( selectedDate ) {
							var option = this.id == "start_date" ? "minDate" : "maxDate",
								instance = $( this ).data( "datepicker" ),
								date = $.datepicker.parseDate(
									instance.settings.dateFormat ||
									$.datepicker._defaults.dateFormat,
									selectedDate, instance.settings
								);
							dates.not( this ).datepicker( "option", option, date );
				}
			});
	})
}

$(function() {$( "input:submit,input:button,input:reset" ).button();});//button
$(function() {//datepi3r
	//harus diisi
	$('.datepicker_req').datepicker({
		dateFormat: 'dd-mm-yy', 
		changeMonth: true,
		changeYear: true,
		beforeShow: function(input) {$(input).unbind('blur')},
		onClose: function(dateText, inst) {$(this).validationEngine();this.focus();this.blur()}
	});
	//boleh kosong
	$('.datepicker').datepicker({
		dateFormat: 'dd-mm-yy', 
		changeMonth: true,
		changeYear: true
	});
});
(function( $ ) {//Auto complete select
	$.widget( "ui.combobox", {
		_create: function() {
			var self = this,
				select = this.element.hide(),
				selected = select.children( ":selected" ),
				value = selected.val() ? selected.text() : "";
			var input = this.input = $( "<input style='position:relative;'>" )
				.insertAfter( select )
				.val( value )
				.autocomplete({
					delay: 0,
					minLength: 0,
					source: function( request, response ) {
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
						response( select.children( "option" ).map(function() {
							var text = $( this ).text();
							if ( this.value && ( !request.term || matcher.test(text) ) )
								return {
									label: text.replace(
										new RegExp(
											"(?![^&;]+;)(?!<[^<>]*)(" +
											$.ui.autocomplete.escapeRegex(request.term) +
											")(?![^<>]*>)(?![^&;]+;)", "gi"
										), "<strong>$1</strong>" ),
									value: text,
									option: this
								};
						}) );
					},
					select: function( event, ui ) {
						ui.item.option.selected = true;
						self._trigger( "selected", event, {
							item: ui.item.option
						});
					},
					change: function( event, ui ) {
						if ( !ui.item ) {
							var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
								valid = false;
							select.children( "option" ).each(function() {
								if ( $( this ).text().match( matcher ) ) {
									this.selected = valid = true;
									return false;
								}
							});
							if ( !valid ) {
								// remove invalid value, as it didn't match anything
								$( this ).val( "" );
								select.val( "" );
								input.data( "autocomplete" ).term = "";
								return false;
							}
						}
					}
				})
				.addClass( "ui-widget ui-widget-content ui-corner-left" );

			input.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.label + "</a>" )
					.appendTo( ul );
			};

			this.button = $( "<button type='button'>&nbsp;</button>" )
				.attr( "tabIndex", -1 )
				.attr( "title", "Show All Items" )
				.insertAfter( input )
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				})
				.removeClass( "ui-corner-all" )
				.addClass( "ui-corner-right ui-button-icon" )
				.click(function() {
					// close if already visible
					if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
						input.autocomplete( "close" );
						return;
					}

					// work around a bug (likely same cause as #5265)
					$( this ).blur();

					// pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
					input.focus();
				});
				this.element.show();
		},

		destroy: function() {
			this.input.remove();
			this.button.remove();
			this.element.show();
			$.Widget.prototype.destroy.call( this );
		}
	});
})( jQuery );


function ckeditor(id,lebar,tinggi){
	if(lebar == null){
		lebar = 805;
	}
	if(tinggi == null){
		tinggi = 300;
	}
	CKEDITOR.replace(id,
	{
		//skin : '',
		width:lebar ,
		height:tinggi,
		
		filebrowserBrowseUrl 		: base_url+'assets/js/ckfinder/ckfinder.html',
		filebrowserImageBrowseUrl 	: base_url+'assets/js/ckfinder/ckfinder.html?Type=Images',
		filebrowserFlashBrowseUrl 	: base_url+'assets/js/ckfinder/ckfinder.html?Type=Flash',
		filebrowserUploadUrl 		: base_url+'assets/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		filebrowserImageUploadUrl 	: base_url+'assets/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
		filebrowserFlashUploadUrl 	: base_url+'assets/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
		toolbar 							:
												[
													['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
													['Bold','Italic','Underline'],
													['Image'],
													['Cut','Copy','Paste'],
													['NumberedList','BulletedList'],
													['TextColor','BGColor'],
													['Table','HorizontalRule','Strike'],
													['Format','FontSize','Preview','Source']
													
													//,'/',
													//['Source','Preview','Templates','Cut','Copy','Paste'],
													//['Bold','Italic','Underline','Strike','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','NumberedList','BulletedList','Subscript','Superscript','-'],
													//['Link','Unlink','-','Image','Flash','Table','HorizontalRule','Smiley','SpecialChar'],['TextColor','BGColor','-','Font','FontSize']
												]
	});	
	
}
function InsertHtmlToCkEditor(v,oEditor){
	//var oEditor = CKEDITOR.instances.outcome;
	var value = v;

	// Check the active editing mode.
	if ( oEditor.mode == 'wysiwyg' )
	{
		oEditor.insertHtml( value );
	}
	else
		alert( 'You must be in WYSIWYG mode!' );
}
function submit(formID){
	if (formID==null){
		formID = 'form1';
	}
	$('#'+formID).submit();
	$('html').html('Loading...');
}


$(function() {
	$(".tooltips a").tooltip({
		bodyHandler: function() {
			return $($(this).attr("href")).html();
		},
		showURL: false
	});
});
function tooltips(){
	$(function() {
		$(".tooltips a").tooltip({
			bodyHandler: function() {
				return $($(this).attr("href")).html();
			},
			showURL: false
		});
	});
}
//new

/**
 *@author agung iskandar <agung_doanks@yahoo.co.id
 *@since version 1.0 12 nov 2011
 *@description  window.alert() jquery style
 *@param {string} text : word message alert
 *@param {string} type : type of icon. 'info' is alert info
 */
function jAlert(text,type){
	if(type=='info'){
		img = 'infoIcon.gif';
	}
	else{
		img ='alertIcon.jpg';
	}
    var imagesAlert = '<br><img src="'+imgPath+img+'"  alt="[images]" align="left">';
	$(function(){
		if(document.getElementById('dialog-modal')==null){
			$('body').append('<div id="dialog-modal">'+imagesAlert+text+'</div>');
		}
		else{
			$('#dialog-modal').html(imagesAlert+text);
		}
		$( "#dialog-modal" ).dialog({
			autoOpen	: false,
			height		: 170,
			width		: 375,
			modal		: true,
			show		:"fade",
			hide		:"fade",
			buttons		: {	Ok: function() { $( this ).dialog( "close");$('#'+idGridnya).focus(); }}
		});
		$( "#dialog-modal" ).dialog( "open" );
		$('.ui-button').focus();
		return false;
	});
}
/**
 *@author agung iskandar <agung_doanks@yahoo.co.id
 *@description similar like jAlert just diferent icon
 *@param {string} text : word message info
 */
function jInfo(text){
	jAlert(text,'info');
}
/**
 *@author Agung agung_doanks@yahoo.co.id
 *@function jConfirm
 *@version 1.1 
 *@description confirm versi jquery
 *@since 12/nov/2011 rev 29/dec/2012
 *@param {string} text      : question word
 *@param {string} act       : javascript function 
 *@param {boolean} isDelete : confirm delete style 
 */
function jConfirm(text,act,is_delete){	
	$(function() {
		var img = 'confirmIcon.jpg';
		if (is_delete == true){
			img = 'deleteIcon.jpg';
		}
        var iconConfirm = '<br><img src="'+imgPath+img+'"  alt="[images]" align="left">';
		  var isi = '<div id="dialog-modal-c"><div style="border-bottom:1px solid grey;min-height:80px;">'+iconConfirm+text+
								  '</div><div style="margin-top:8px;float:right"><input type="button" value="OK" id="close_confirm" onclick="'+act+'">\
								  <input type="button" id="cancel_confirm" value="Cancel" onclick="$(\'#dialog-modal-c\').dialog(\'close\')"></div></div>';
		if(document.getElementById('dialog-modal-c')==null){
			$('body').append(isi);
		}
		else{
			$('#dialog-modal-c').html(isi);
		}
		$('#close_confirm').click(function(){$('#dialog-modal-c').dialog('close');});
		$(function() {$( "input:submit,input:button,input:reset" ).button();});//button
		$( "#dialog-modal-c" ).dialog({
			autoOpen	: false,
			height	: 170,
			width		: 375,
			modal		: true,
			show		:"fade",
			hide		:"fade"
		});
		$( "#dialog-modal-c" ).dialog( "open" );
	});
	return false;
}
/**
 *@author agung iskandar <agung_doanks@yahoo.co.id
 *@description similar like jConfirm just diferent icon
 */
function jConfirmDelete(text,act){
    jConfirm(text,act,true);
}


//class cancel or class close utk clear form
$(function () {
	$('.cancel, .close').click(function(){
		restore_form();
	})
})


function ckSmall(id){
		CKEDITOR.replace( id, {
			width: 600,
			height:100,
		toolbar: [
					[ 'Bold', 'Italic','Underline' ],
					[ 'Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo' ],
					[ 'Link', 'Unlink', 'Anchor' ],
					[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-'/*, 'Blockquote' */]
					//,['Preview']
		]
	})
}
//force number
    (function ($) {
    $.fn.forceNumeric = function () {
    return this.each(function () {
     
    $(this).keyup(function() {
    if (!/^[0-9.-]+$/.test($(this).val())) {
    $(this).val($(this).val().replace(/[^0-9.-]/g, ''));
    }
    });
    });
    };
    })(jQuery);
