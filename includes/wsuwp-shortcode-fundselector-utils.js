// Does our namespace exist
window.wsuwpUtils = window.wsuwpUtils || {};

(function () {

    window.wsuwpUtils = {

		addListItem: function ( $list, name, designationId ) {
			var html = '<li class="list-group-item col-sm-12" data-designation_id="' + designationId + '"><h3><span class="label label-success pull-left">$25</span></h3>' + name; 
			html += '<a href="#" role="button" class="pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span><span class="sr-only">Remove Fund button</span></a>';
			html += '</li>';
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