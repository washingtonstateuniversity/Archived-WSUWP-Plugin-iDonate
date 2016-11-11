/// <reference path="wsuwp-shortcode-fundselector-utils.js" />

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
                wsuwpUtils.addListItem($("#selectedFunds"), ui.item.name, ui.item.designationId);
				$("#fundSearch").val("");
                event.preventDefault();
            }
        }
    );

});