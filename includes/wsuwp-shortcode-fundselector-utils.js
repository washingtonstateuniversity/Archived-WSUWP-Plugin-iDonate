// Does our namespace exist
window.wsuwpUtils = window.wsuwpUtils || {};

(function () {

    window.wsuwpUtils = {

		addListItem: function ( $list, name, designationId ) {
			var html = '<div class="list-group-item col-sm-9" data-designation_id="' + designationId + '">' + name + '</div>'; 
			html += '<div class="list-group-item col-sm-2"><span class="label label-success">$25</span></div>';
			html += '<div class="list-group-item col-sm-1"><a href="#" role="button" class="list-group-item"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span><span class="sr-only">Remove Fund button</span></a></div>';
			$list.append(html);
		},

		getDesignationList: function ($listElement)
		{
			var designationIds = [];
			
			$listElement.find("li").each(function (index, element){
				// Element should look like '[{"id":"someId", "amount":99},{"id":"someId", "amount":99}]'
				designationIds.push({
					"id" : jQuery(element).attr("data-designation_id"),
					"amount": 25 // Amount is required for the embed, defaulting to $25 for now
				});
			})

			return designationIds;
		},

		enableButton: function ( $button ) {
			$button.prop("disabled", false);
			$button.button("enable");
		},

		disableButton: function ( $button ) {
			$button.prop("disabled", true);
			$button.button("disable");
		},

		highlightSelectedCategory: function ( $button, $buttonGroup ) {
			$buttonGroup.removeClass("active");  
			$buttonGroup.removeClass("btn-primary");  
			$buttonGroup.addClass("btn-default");  
			$button.removeClass("btn-default");
			$button.addClass("btn-primary active");
		},

		htmlDecode: function (value) {
			return jQuery("<textarea/>").html(value).text();
		}


	}

})();