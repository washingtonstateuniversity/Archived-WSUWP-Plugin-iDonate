/// <reference path="wsuwp-shortcode-fundselector-utils.js" />

jQuery(document).ready(function($) {

	// Fund Search Autocomplete
	$("#fundSearch").autocomplete(
        {
			source: function( request, response ) {
	
				$.getJSON( wpData.request_url_base + 'idonate_fund', 
					{
						search : request.term
					}, 
					function( data, status, xhr ) {			
						// Map the fund data to use the expected label name ("value") from fund name
						var fundList = $.map(data, function(fund) {
							return {
								"designationId": fund.designationId,
								"name": fund.title.rendered,
								"value": fund.title.rendered
							};
						});
						
						response( fundList );
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

	// Major Category Click Events
	$("#majorcategory a")
	.click( function( event ) {
		$("#majorcategory a").removeClass("active");  
		$(".categoryTab").addClass('hidden');

		var tabName = $(this).attr("data-tab");
		$("#" + tabName).removeClass('hidden');
		
		var categoryName = $(this).attr("data-category");

		if(categoryName) {
			var restUrl = wpData.request_url_base + encodeURIComponent(categoryName);
			
			$.getJSON( restUrl )
			.done(function( json ) {

				var $list = $('#subcategories'); 
				$list.html('<option disabled selected value> -- Select a Category -- </option>');
				$('#funds').html('<option disabled selected value> -- Select a Fund -- </option>');
				$.each(json, function(key, value) {   
					$list
					.append($('<option>', { value : value["id"], "data-category" : value["taxonomy"] })
					.text( wsuwpUtils.htmlDecode(value["name"]) )); 
				});

			})
		}

		event.preventDefault();
	});

	// Subcategory Click event
	$("#subcategories")
	.change( function( event ) {
		var category = $(this).find(":selected").attr("data-category");
		var subcategoryId = $(this).find(":selected").attr("value");

		if(category && subcategoryId) {
			// GET /wp-json/wp/v2/idonate_fund?<taxonomy_slug>=<category_id> (e.g. GET /wp-json/wp/v2/idonate_fund?idonate_priorities=35)
			var restQueryUrl = wpData.request_url_base + 'idonate_fund?' + category + "=" + subcategoryId;
			
			$.getJSON( restQueryUrl )
			.done(function( json ) {

				var $list = $('#funds'); 
				$list.html('<option disabled selected value> -- Select a Fund -- </option>');
				$.each(json, function(key, value) {   
					$list
					.append($('<option>', { value : value["designationId"] })
					.text( wsuwpUtils.htmlDecode(value["title"].rendered) ) ); 
				});

				if(json.length > 0)
				{
					$list.prop("disabled", false);
				}

			})
		}

		event.preventDefault();
	});
});
