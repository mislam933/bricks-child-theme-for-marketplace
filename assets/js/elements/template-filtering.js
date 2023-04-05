(function($) {
	"use strict";
	$( document ).ready(function() {
		var $filterCheckboxes = $('input[type="checkbox"]');
		var filterFunc = function () {
		  var selectedFilters = {};

		  $filterCheckboxes.filter(":checked").each(function () {
		    if (!selectedFilters.hasOwnProperty(this.name)) {
		      selectedFilters[this.name] = [];
		    }

		    selectedFilters[this.name].push(this.value);
		  });

		  // create a collection containing all of the filterable elements
		  var flowerselector = $(".flower");
		  var $filteredResults = $(".flower");
		  // loop over the selected filter name -> (array) values pairs
		  if (selectedFilters['fl-category']) {
		  	$(".flower").hide();
			$.each(selectedFilters, function (name, filterValues) {
				// filter each .flower element
				$filteredResults = $filteredResults.filter(function () {
				  var matched = false,
				    currentFilterValues = $(this).data("category").split(" ");
				    if (findCommonElement(currentFilterValues, selectedFilters['fl-category'])) {
				    	$(this).show();
				    }
				});
			});
		  }else {
			$(".flower").show();
		  }
		};

		$filterCheckboxes.on("change", filterFunc);
	});

    // Function definition with passing two arrays
    function findCommonElement(array1, array2) {
         
        // Loop for array1
        for(let i = 0; i < array1.length; i++) {
             
            // Loop for array2
            for(let j = 0; j < array2.length; j++) {
                 
                // Compare the element of each and
                // every element from both of the
                // arrays
                if(array1[i] === array2[j]) {
                 
                    // Return if common element found
                    return true;
                }
            }
        }
         
        // Return if no common element exist
        return false;
    }


   $("#bcm-demo-cat-search").on('keyup', function(){
      var value = $(this).val().toLowerCase();
      $(".flower").each(function () {
         if ($(this).data("category").toLowerCase().search(value) > -1) {
            $(this).show();
         } else {
            $(this).hide();
         }
      });
   })
   
})(jQuery);