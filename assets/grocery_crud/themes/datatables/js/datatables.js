var default_per_page = typeof default_per_page !== 'undefined' ? default_per_page : 25;

function supports_html5_storage()
{	
	try {
		JSON.parse("{}");
		return 'localStorage' in window && window['localStorage'] !== null;
	} catch (e) {
		return false;
	}
}

var use_storage = supports_html5_storage();
	
$(document).ready(function() {
	var mColumns = [];
	
	$('table#groceryCrudTable thead tr th').each(function(index){
		if(!$(this).hasClass('actions'))
		{
			mColumns[index] = index;
		}
	});
	
	var aButtons = [];
	
	if(!unset_export)
	{
		aButtons.push(    {
	         "sExtends":    "xls",
	         "sButtonText": export_text,
	         "mColumns": mColumns
	     });
	}
	
	if(!unset_print)
	{
		aButtons.push({
	         "sExtends":    "print",
	         "sButtonText": print_text,
	         "mColumns": mColumns
	     });		
	}
	
	var oTable = $('#groceryCrudTable').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": use_storage,
        "fnStateSave": function (oSettings, oData) {
            localStorage.setItem( 'DataTables_' + unique_hash, JSON.stringify(oData) );
        },
    	"fnStateLoad": function (oSettings) {
            return JSON.parse( localStorage.getItem('DataTables_'+unique_hash) );
    	},		
		"iDisplayLength": default_per_page,
		"aaSorting": datatables_aaSorting,
		"oLanguage":{
		    "sProcessing":   list_loading,
		    "sLengthMenu":   show_entries_string,
		    "sZeroRecords":  list_no_items,
		    "sInfo":         displaying_paging_string,
		    "sInfoEmpty":   list_zero_entries,
		    "sInfoFiltered": filtered_from_string,
		    "sSearch":       search_string+":",
		    "oPaginate": {
		        "sFirst":    paging_first,
		        "sPrevious": paging_previous,
		        "sNext":     paging_next,
		        "sLast":     paging_last
		    }		
		},
		"sDom": 'T<"clear"><"H"lfr>t<"F"ip>',
	    "oTableTools": {
	    	"aButtons": aButtons,
	        "sSwfPath": base_url+"assets/grocery_crud/themes/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
	    }
	});

	$("#groceryCrudTable tfoot input").keyup( function () {
		oTable.fnFilter( this.value, $("#groceryCrudTable tfoot input").index(this) );
		
		if(use_storage)
		{
			var search_values_array = [];
			
			$("#groceryCrudTable tfoot tr th").each(function(index,value){
				search_values_array[index] = $(this).children(':first').val();
			});
		
			localStorage.setItem( 'datatables_search_'+ unique_hash ,'["' + search_values_array.join('","') + '"]');
		}
	} );
	
	var search_values = localStorage.getItem('datatables_search_'+ unique_hash);
	
	if( search_values !== null)
	{
		$.each($.parseJSON(search_values),function(num,val){
			if(val !== '')
			{
				$("#groceryCrudTable tfoot tr th:eq("+num+")").children(':first').val(val);
			}
		});
	}
	
	$('.clear-filtering').click(function(){
		localStorage.removeItem( 'DataTables_' + unique_hash);
		localStorage.removeItem( 'datatables_search_'+ unique_hash);
		window.location.reload();
	});
	
	$('a[role=button]').live("mouseover mouseout", function(event) {
		  if ( event.type == "mouseover" ) {
			  $(this).addClass('ui-state-hover');
		  } else {
			  $(this).removeClass('ui-state-hover');
		  }
	});	
	
	$('th.actions').unbind('click');
	$('th.actions>div').html($('th.actions>div').text());
	
} ); 

function delete_row(delete_url , row_id)
{	
	if(confirm(message_alert_delete))
	{
		$.ajax({
			url: delete_url,
			dataType: 'json',
			success: function(data)
			{					
				if(data.success)
				{
					$('#ajax_refresh_and_loading').trigger('click');
					$('#report-success').html( data.success_message ).slideUp('fast').slideDown('slow');						
					$('#report-error').html('').slideUp('fast');
					$('tr#row-'+row_id).addClass('row_selected');
					var anSelected = fnGetSelected( oTable );
					oTable.fnDeleteRow( anSelected[0] );					
				}
				else
				{
					$('#report-error').html( data.error_message ).slideUp('fast').slideDown('slow');						
					$('#report-success').html('').slideUp('fast');						
					
				}
			}
		});
	}
	
	return false;
}

function fnGetSelected( oTableLocal )
{
	var aReturn = new Array();
	var aTrs = oTableLocal.fnGetNodes();
	
	for ( var i=0 ; i<aTrs.length ; i++ )
	{
		if ( $(aTrs[i]).hasClass('row_selected') )
		{
			aReturn.push( aTrs[i] );
		}
	}
	return aReturn;
}