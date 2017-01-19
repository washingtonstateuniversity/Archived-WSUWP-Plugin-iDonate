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
								"name": wsuwpUtils.htmlDecode(fund.title.rendered),
								"value": wsuwpUtils.htmlDecode(fund.title.rendered)
							};
						});
						
						response( fundList );
					}
				);
			},
			minLength: 3,
            select: function( event, ui ) {
				$("#inpDesignationId").text(ui.item.designationId);
				$("#inpFundName").text(ui.item.name);
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
				$list.empty();
				$list.append('<option disabled selected value> SELECT A CATEGORY </option>');
				
				var $fundList = $('#funds');				
				$fundList.empty();
				$fundList.append('<option disabled selected value> SELECT A FUND </option>');
				$fundList.prop("disabled", true);

				$.each(json, function(key, value) {   
					$list
					.append($('<option>', { value : value["id"], "data-category" : value["taxonomy"] })
					.text( wsuwpUtils.htmlDecode( value["name"]) )); 
				});

			})
		}

		event.preventDefault();
	});

	
	// Subcategory Selected Change event
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
				$list.empty();
				$list.append('<option disabled selected value> SELECT A FUND </option>');
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

	// Fund Selected Change event
	$(".fund-selection")
	.change( function ( ) {
		var designationId = $(this).val();
		var fundName = $(this).find(":selected").text();
		$("#inpDesignationId").text(designationId);
		$("#inpFundName").text(fundName);
		showAmountZone();
	});

	// Add Fund Button click  event
	$(".amount-selection").not(".other")
	.click( function ( event ) {
		console.log("amoutnselectionclick");
		handleAmountSelectionClick( event );	
		addFundAction();
	});

	$("#addFundButton")
	.click( function ( event ) {	
		console.log("addfundbutton click");	
		jQuery("#inpAmount").val( jQuery("#otherAmount").val() );
		addFundAction();
	});

	// Remove Fund Button Click Event
	// (Using body to defer binding until element has been created)
	$('body').on('click', '#selectedFunds li span.close a', function (event) {
		event.preventDefault();
		
		$(this).parent().parent().remove();

		// If the Fund list is empty, disable the Continue Button
		if($("#selectedFunds").find("li").length === 0)
		{
			wsuwpUtils.disableButton($("#continueButton"));
			hideContinueButton();
		}
		
	});

	// Amount Selection Buttons Initialization and Click Event
//	$(".amount-selection").button()
//	.click( handleAmountSelectionClick );

	// Other Amount text field Change Event
	$("#otherAmount").on('input propertychange paste', function () {
		var inputAmount = $(this).val();
		if(wsuwpUtils.validateAmount(inputAmount))
		{
			$("#inpAmount").val(inputAmount);
			jQuery("#errorOtherAmount").text("");
		}
		else
		{
			jQuery("#errorOtherAmount").text("Amount should be between $3 and $100,000.");
		}		
	});

	$(".amount-selection.other").on('click',function(){
		$(".amount-selection.selected").removeClass("selected");
		$(this).addClass("selected");
		showOther();
	});

	// Continue Button Initialization and Click Event
	$("#continueButton").button()
	.click( continueAction );

	$("#backButton").on('click',function(){
		showForm();
	});

	loadPriorities($("#priorities"), "idonate_priorities", "idonate_priorities");
	loadPriorities($("#unit-priorities"), wpData.unit_taxonomy, wpData.unit_category);
});

function loadPriorities($list, category, subcategory)
{
	if($list.find("option").length <= 1 && category && subcategory) {
		// GET /wp-json/idonate_fundselector/v1/funds/category/subcategory (e.g. GET /wp-json/idonate_fundselector/v1/funds/idonate_priorities/idonate_priorities)
		var restQueryUrl = wpData.plugin_url_base + 'funds/' + category + '/' + subcategory;
		
		jQuery.getJSON( restQueryUrl )
		.done(function( json ) { 
			$list.empty();
			$list.append('<option disabled selected value> SELECT A FUND </option>');
			jQuery.each(json, function(key, value) {   
				$list
				.append(jQuery('<option>', { value : value["designationId"] })
				.text( wsuwpUtils.htmlDecode(value["fund_name"]) ) ); 
			});

			if(json.length > 0)
			{
				$list.prop("disabled", false);
			}
		})
	}
}

function addFundAction()
{
	console.log("addfundaction");
	var designationId = jQuery("#inpDesignationId").text();
	var fundName = jQuery("#inpFundName").text();

	if(designationId && fundName)
	{
		wsuwpUtils.addListItem(jQuery("#selectedFunds"), fundName, designationId, jQuery("#inpAmount").val());
		wsuwpUtils.enableButton(jQuery("#continueButton"));
		showContinueButton();
		resetForm();
	}
}

function resetForm()
{
	jQuery("#fundSearch").val("");
	jQuery('.fund-selection').prop('selectedIndex', 0);
	setTimeout(function(){ jQuery('.amountwrapper .selected').removeClass('selected'); }, 1300);
	hideAmountZone();
	hideother();
}

function handleAmountSelectionClick(event)
{
	var $this = jQuery(event.target);
    if($this.attr("data-amount"))
	{
		jQuery("#inpAmount").val($this.attr("data-amount"));

		jQuery(".amount-selection").removeClass("selected");
		$this.addClass("selected");
	}
}

function continueAction()
{
	var designations = wsuwpUtils.getDesignationList(jQuery("#selectedFunds"));

	if(designations.length > 0)
	{
		hideForm();
		// Turn the list of designations into a JSON string
		var designationsString = JSON.stringify(designations);

		// Add the designation as an attribute
		jQuery("#iDonateEmbed").attr("data-designations", designationsString);

		// Initialize the iDonate embed
		initializeEmbeds();

		$loadingMessage = jQuery("#embedLoadingMessage");
		$loadingMessage.show();
		
		wsuwpUtils.iDonateEmbedLoad(jQuery("#loadingCheck"))
		.then(function loaded() {
			jQuery("#iDonateEmbed iframe").show();
			$loadingMessage.hide();
			//$loadingMessage.text("Finished loading");
		});
	}
	else
		jQuery("#iDonateEmbed iframe").hide();
}

function showForm()
{
	showAnything( jQuery("#firstform") ); 
	hideAnything( jQuery("#secondform") );
}

function hideForm()
{
	hideAnything( jQuery("#firstform") );
	showAnything( jQuery("#secondform") );
}

function showAmountZone()
{
	showAnything( jQuery(".amountwrapper.wrapper") ); 
}

function hideAmountZone()
{
	hideAnything( jQuery(".amountwrapper.wrapper") );
}

function showContinueButton()
{
	showAnything( jQuery(".continuebutton") ); 
}

function hideContinueButton()
{
	hideAnything( jQuery(".continuebutton") );
}

function showOther()
{
	showAnything( jQuery(".otherprice") ); 
}

function hideother()
{
	hideAnything( jQuery(".otherprice") );
}

function showAnything(element)
{
	jQuery(element).animate({"opacity":1},{duration:300}).show().delay(1000);
}

function hideAnything(element)
{
	jQuery(element).animate({"opacity":0}, {duration:1000}); 
	setTimeout(function(){ jQuery(element).hide(); }, 1100);
}


jQuery(document).ready(function(){
	jQuery("#majorcategory a").on("click",function(){
		jQuery(".form-group.search").addClass("hidden");
		jQuery("#majorcategory a.active").removeClass("active");
		jQuery(this).addClass("active");
	});
	jQuery(".search").on("click", function(){
		jQuery(".form-group.search").removeClass("hidden");
	});
	jQuery("button.amount-selection:last").addClass("lastbutton");
});