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
				showAmountZone();
            }
        }
    ).autocomplete( "widget" ).addClass( "fundselector" );

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
		handleAmountSelectionClick( event );	
		addFundAction();
	});

	$("#addFundButton")
	.click( function ( event ) {	
		var amount = jQuery("#otherAmount").val();

		if(wsuwpUtils.validateAmount(amount))
		{	
			jQuery("#inpAmount").val( amount );
			addFundAction();
		}
	});

	// Close Add Fund Indicator
	$(".help-text span.close a").button()
	.click( function(event) {
		event.preventDefault();

		hideAnything(".help-text");

	} );

	// Remove Fund Button Click Event
	// (Using body to defer binding until element has been created)
	$('body').on('click', '#selectedFunds li span.close a', function (event) {
		event.preventDefault();
		
		$parent = $(this).parent().parent().parent();

		if($parent.hasClass("fund-scholarship")) $("#genScholarship").prop("checked", false);

		$parent.fadeOut(1000, function(){ 
            $(this).remove();
            wsuwpUtils.updateTotalAmountText($("#advFeeCheck").prop('checked'));
        });

        // If the Fund list is empty, disable the Continue Button
        if($("#selectedFunds").find("li").length === 1)
        {
            wsuwpUtils.disableButton($("#continueButton"));
            hideContinueButton();
            hideAnything(jQuery(".disclaimer"));
        }
	});

	// Other Amount text field Change Event
	$("#otherAmount").on('input propertychange paste', function () {
		var inputAmount = $(this).val();
		if(wsuwpUtils.validateAmount(inputAmount))
		{
			$("#inpAmount").val(inputAmount);
			wsuwpUtils.enableButton($("#addFundButton"));
			jQuery("#errorOtherAmount").text("");
		}
		else
		{
			wsuwpUtils.disableButton($("#addFundButton"));
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

	$("#addFundButton").button();

    hideContinueButton();
    
	$("#backButton").on('click',function(){
		showForm();
	});


	$("#genScholarship").change(function() {
		// this will contain a reference to the checkbox
		if (this.checked) {
			// the checkbox is now checked
			$("#inpAmount").val($(this).attr("data-amount"));
			$("#inpDesignationId").text($(this).attr("data-designation_id"));
			$("#inpFundName").text($(this).attr("data-fund_name"));
			addFundAction(true);
		} else {
			// the checkbox is now no longer checked
			$("#selectedFunds > li.fund-scholarship").remove();

			wsuwpUtils.updateTotalAmountText($("#advFeeCheck").prop('checked'));

			// If the Fund list is empty, disable the Continue Button
			if ($("#selectedFunds").find("li").length === 0) {
				wsuwpUtils.disableButton($("#continueButton"));
				hideContinueButton();
			}
		}
	});

	$("#advFeeCheck").change(function() {
		// this will contain a reference to the checkbox
		wsuwpUtils.updateTotalAmountText(this.checked);
	});

	// if a unit is specified
	if(wpData.unit_taxonomy && wpData.unit_category)
	{
		loadPriorities($("#unit-priorities"), wpData.unit_taxonomy, wpData.unit_category)
		.done(function() {
			loadFundFromDesignationID($("#unit-priorities"), wpData.unit_designation);
		});
		loadPriorities($("#priorities"), "idonate_priorities", "idonate_priorities");
	}
	else
	{
		loadPriorities($("#priorities"), "idonate_priorities", "idonate_priorities")
		.done(function() {
			loadFundFromDesignationID($("#priorities"), wpData.unit_designation);
		});
	}
});

function loadFundFromDesignationID($list, designationId){
	
	if(designationId)
	{
		var $des = wsuwpUtils.findElementbyDesignation($list, designationId);

		// Check if the designation already exists in the priorities list and select item
		if($des)
		{
			// Select and show amount buttons
			wsuwpUtils.selectFundInDropdown($des, designationId);
		}
		else // Otherwise add it to the priorities list and select it
		{
			// Look up the Fund Name by Designation ID
			// GET /wp-json/idonate_fundselector/v1/fund/designationId (e.g. GET /wp-json/idonate_fundselector/v1/fund/12dc5acc-07ea-4ed3-9c92-4b9ebe7c951c)
			var restQueryUrl = wpData.plugin_url_base + 'fund/' + designationId;
			
			jQuery.getJSON( restQueryUrl )
			.then(function( json ) { 			
				if(json.length > 0)
				{
					// Add to priorities
					var $fund = jQuery('<option>', { value : json[0]["designation_id"] })
								.text( wsuwpUtils.htmlDecode(json[0]["fund_name"]) );
					$list.append($fund);

					// Select and show amount buttons
					wsuwpUtils.selectFundInDropdown($fund, designationId); 
				}
			})
		}
	}
}

function loadPriorities($list, category, subcategory)
{
	if($list.find("option").length <= 1 && category && subcategory) {
		// GET /wp-json/idonate_fundselector/v1/funds/category/subcategory (e.g. GET /wp-json/idonate_fundselector/v1/funds/idonate_priorities/idonate_priorities)
		var restQueryUrl = wpData.plugin_url_base + 'funds/' + category + '/' + subcategory;
		
		return jQuery.getJSON( restQueryUrl )
		.then(function( json ) { 
			$list.empty();
			$list.append('<option disabled selected value> SELECT A FUND </option>');
			jQuery.each(json, function(key, value) {   
				$list
				.append(jQuery('<option>', { value : value["designation_id"] })
				.text( wsuwpUtils.htmlDecode(value["fund_name"]) ) ); 
			});

			if(json.length > 0)
			{
				$list.prop("disabled", false);
			}
		})
	}

	//return an already resolved promise (http://stackoverflow.com/a/33656679)
	return jQuery.when();
}

function addFundAction(scholarship)
{
	var designationId = jQuery("#inpDesignationId").text();
	var fundName = jQuery("#inpFundName").text();

	if(designationId && fundName)
	{
		wsuwpUtils.addListItem(jQuery("#selectedFunds"), fundName, designationId, jQuery("#inpAmount").val(), scholarship);
		wsuwpUtils.enableButton(jQuery("#continueButton"));
		
		wsuwpUtils.updateTotalAmountText(jQuery("#advFeeCheck").prop('checked'));
		showAnything(jQuery(".disclaimer"));
		showContinueButton();
		resetForm();
	}
}

function resetForm()
{
	jQuery("#fundSearch").val("");
	jQuery('.fund-selection').prop('selectedIndex', 0);
	jQuery('.category-selection').prop('selectedIndex', 0);
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

	if(designations && designations.length > 0)
	{
		hideForm();

		var $advFeeCheck = jQuery("#advFeeCheck");
		
		if($advFeeCheck.prop("checked")) {
			designations.push({id: $advFeeCheck.attr("data-designation_id"), amount: parseInt($advFeeCheck.attr("data-amount")) });
		}

		if(designations.length === 1)
		{
			var des = designations[0];
			
			// Add the designation as an attribute
			jQuery("#iDonateEmbed").attr("data-designation", des.id);
			
			// Get the designation name from the first span in the list item
			var desName = wsuwpUtils.htmlEncode(jQuery("#selectedFunds li span").first().text());
			var giftArrays = [[desName, des.amount]];

			jQuery("#iDonateEmbed").attr("data-gift_arrays", JSON.stringify(giftArrays));
			jQuery("#iDonateEmbed").attr("data-cash_default", des.amount);
		}
		else {
			// Turn the list of designations into a JSON string
			var designationsString = JSON.stringify(designations);
			
			// Add the designations as an attribute
			jQuery("#iDonateEmbed").attr("data-designations", designationsString);

			var sum = 0;

			for (var i = 0; i < designations.length; i++) {
				sum += parseInt(designations[i].amount);
			}		

			jQuery("#iDonateEmbed").attr("data-custom_gift_amount", sum);		
		}

		if(jQuery("#gpInWill").prop("checked")){
			jQuery("#iDonateEmbed").attr("data-custom_note_4", "WSU is in my will/estate plan!");
		}

		if(jQuery("#gpMoreInfo").prop("checked")){
			jQuery("#iDonateEmbed").attr("data-custom_note_5", "I would like more information about putting WSU in my will/estate plan");
		}

		// Initialize the iDonate embed
		initializeEmbeds();

		$loadingMessage = jQuery("#embedLoadingMessage");
		$loadingMessage.show();
		
		wsuwpUtils.iDonateEmbedLoad(jQuery("#loadingCheck"))
		.then(function loaded() {
			jQuery("#iDonateEmbed iframe").show();
			$loadingMessage.hide();
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
	showAnything( jQuery(".continuebutton, .disclaimer, .additional-info") ); 
}

function hideContinueButton()
{
	hideAnything( jQuery(".continuebutton, .disclaimer, .additional-info") );
}

function showOther()
{
	showAnything( jQuery(".otherprice") );
	showAnything(jQuery("#errorOtherAmount")); 
}

function hideother()
{
	hideAnything( jQuery(".otherprice") );
	hideAnything(jQuery("#errorOtherAmount"));
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

jQuery.extend(jQuery.ui.autocomplete.prototype.options, {
	open: function(event, ui) {
		jQuery(this).autocomplete("widget").css({
            "width": (jQuery(this).width() + "px")
        });
    }
});