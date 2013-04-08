

function upgrade_viewability() {

	var total = parseInt($("#bd").data("recordcount"));

	var $div = $("#facet_begin_Viewability");
	var $target = $("#viewability-tabs");
	var $ul = $("<ul></ul>");
	var $filters = $("ul.filters li");

	var active_tab = '.view-all';

	// sequence is full view, limited view, all items
	var tabs = { all : null, full : null, limited : null };

  if ( $div.length ) {
		// have viewability facets
		$div.find("li").each(function() {
			var $this = $(this);
      if ( $this.text().indexOf("Full view") > -1 ) {
				$this.addClass("view-full");
				tabs.full = $this.clone();
			}
		})
	}

  if ( ! tabs.full  ) {
    
		// we're filtering on full; do a query to find the full count
		// http://roger.catalog.hathitrust.org/Search/Home?filter%5B%5D=ht_availability%3AFull%20text&use_dismax=1
		var $active = $filters.filter(":contains('Full view')")
    alert("SHould be showing fulltext and computing all");
      // if ( $active.length  ) {
  			// filtering on full, find "all" count
        // var href = $active.find("a").attr("href");
    if (window.location.href.indexOf('ft=ft') > -1) {
      href = window.location.href.replace('ft=ft', 'ft=').replace('&htftonly=true', '').replace('&htftonly=false', '');
      alert(href);
			// var href = window.location.href;
			// href = href.replace('%5B%5D=ht_availability%3AFull%20text', '').replace('filter&', '');
			$.ajax({
				url : href,
				dataType : 'html',
				async: false,
				success : function(data, textStatus, xhr) {
					var $doc = $(data);
					var all_total = $doc.find("#bd").data('recordcount');
					tabs.all = $("<li class='view-all'><a href='{href}'>All items</a> ({total})</li>".replace('{href}', href).replace('{total}', all_total));
					console.log("TOTAL:", total);
				}
			})
			tabs.full = $(
				"<li class='view-full active'><a href='{link}'>Full view</a> ({total})</li>".
					replace('{total}', total).
					replace('{link}', window.location.href));
				active_tab = '.view-full';

			$active.remove();
			if ( ! $(".filters li").length ) {
				$("#applied_filters").remove();
			}

		} else {
			// we're looking at "all"
      
			tabs.all = $(
				"<li class='view-all active'><a href='{link}'>All items</a> ({total})</li>".
					replace('{total}', total).
					replace('{link}', window.location.href));
				active_tab = '.view-all';
		}
	} else {
    alert("SHould be showing all and computing fulltext");
    href=window.location.href;
    
		tabs.all = $("<li class='view-all active'><a href='{link}'>All items</a> ({total})</li>".replace('{link}', window.location.href.replace('ft=', 'ft=ft') ).replace('{total}', total));
	}

	if ( tabs.full ) {
		$ul.append(tabs.full);		
	}
	$ul.append(tabs.all);


	$target.append($ul);
	$div.remove();
}
upgrade_viewability();