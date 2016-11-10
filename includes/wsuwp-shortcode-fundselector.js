jQuery(document).ready(function($) {

	$("#fundSearch").autocomplete(
        {
			source: function( request, response ) {
	
				$.getJSON( wpData.ajax_url, 
					{
						term : request.term,
						action : "wsuwp_plugin_idonate_ajax_fund_search"
					}, 
					function( data, status, xhr ) {			
						response( data );
					}
				);
			},
			minLength: 3,
            select: function( event, ui ) {
                wsuwp.utils.addListItem($("#selectedFunds"), ui.item.value, ui.item.designation_id);
                $("#fundSearch").val("");
                event.preventDefault();
            }
        }
    );

});