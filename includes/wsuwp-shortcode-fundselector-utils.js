// Does our namespace exist
window.wsuwpUtils = window.wsuwpUtils || {};

(function () {

    window.wsuwpUtils = {

		addListItem: function ( $list, name, designationId ) {
			var html = '<li class="list-group-item" data-designation_id="' + designationId + '">' + name + '<a href="#" role="button" class="pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span><span class="sr-only">Remove Fund button</span></a></li>'; 
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

		htmlDecode: function (value) {
			return jQuery("<textarea/>").html(value).text();
		}


	}

})();