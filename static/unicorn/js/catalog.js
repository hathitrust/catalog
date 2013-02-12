function upgrade_viewability_old() {

	var total = parseInt($("#bd").data("recordcount"));

	var $div = $("#facet_begin_Viewability");
	var $target = $("#viewability-tabs");
	var $ul = $("<ul></ul>");
	var $filters = $("ul.filters li");

	var active_tab = '.view-all';

	// sequence is full view, limited view, all items
	var tabs = { all : null, full : null, limited : null };

	$div.find("li").each(function() {
		var $this = $(this);
		if ( $this.text().indexOf("Limited") > -1 ) {
			$this.addClass("view-limited");
			tabs.limited = $this.clone();
		} else if ( $this.text().indexOf("Full view") > -1 ) {
			$this.addClass("view-full");
			tabs.full = $this.clone();
		}
	})

	if ( ! tabs.full || ! tabs.limited ) {
		// we're probably filtering...
		var $active;
		if ( $active = $filters.filter(":contains('Full view')") ) {
			tabs.full = $(
				"<li class='view-full active'><a href='{link}'>Full view</a> ({total})</li>".
					replace('{total}', total).
					replace('{link}', $active.find("a").attr("href")));
			var n = 0;
			if ( tabs.limited ) {
				n = tabs.limited.find("span[dir=ltr]").text();
				total += parseInt(n.substr(1, n.length - 1));
			}
			active_tab = '.view-full';
		} else if ( $active = $filters.filter(":contains('Limited')") ) {
			tabs.limited = $("<li class='view-full active'><a href='{link}'>Limited (search only)</a> ({total})</li>".
				replace('{total}', total).
				replace('{link}', $active.find("a").attr("href")));
			var n = tabs.full.find("span[dir=ltr]").text();
			total += parseInt(n.substr(1, n.length - 1));
			active_tab = '.view-limited';
		}
		// if ( $active.length ) {
		// 	$active.remove();
		// 	if ( ! $(".filters li").length ) {
		// 		$("#applied_filters").remove();
		// 	}
		// }
	}

	if ( tabs.limited && tabs.full ) {
		// we're looking at all the results
		tabs.all = $("<li class='view-all'><a href='#'>All items</a> ({total})</li>".replace('{total}', total));
	}

	$ul.append(tabs.full);
	$ul.append(tabs.limited);
	$ul.append(tabs.all);

	$ul.find(active_tab).addClass("active");

	$target.append($ul);
	// var $li = $("<li class='active'><a href='#'>All items</a> (41)</li>").insertBefore($target.find("li:first"));
	// $div.remove();
}

function upgrade_viewability() {

	var total = parseInt($("#bd").data("recordcount"));

	var $div = $("#facet_begin_Viewability");
	var $target = $("#viewability-tabs");
	var $ul = $("<ul></ul>");
	var $filters = $("ul.filters li");

	var active_tab = '.view-all';

	// sequence is full view, limited view, all items
	var tabs = { all : null, full : null, limited : null };

	$div.find("li").each(function() {
		var $this = $(this);
		if ( $this.text().indexOf("Full view") > -1 ) {
			$this.addClass("view-full");
			tabs.full = $this.clone();
		}
	})

	if ( ! tabs.full  ) {
		// we're filtering on full; do a query to find the full count
		// http://roger.catalog.hathitrust.org/Search/Home?filter%5B%5D=ht_availability%3AFull%20text&use_dismax=1
		var $active = $filters.filter(":contains('Full view')")
		var href = $active.find("a").attr("href");
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
		if ( $active.length ) {
			$active.remove();
			if ( ! $(".filters li").length ) {
				$("#applied_filters").remove();
			}
		}
	} else {
		tabs.all = $("<li class='view-all active'><a href='{link}'>All items</a> ({total})</li>".replace('{link}', window.location.href).replace('{total}', total));
	}

	$ul.append(tabs.full);
	$ul.append(tabs.all);

	//$ul.find(active_tab).addClass("active");

	$target.append($ul);
	// var $li = $("<li class='active'><a href='#'>All items</a> (41)</li>").insertBefore($target.find("li:first"));
	$div.remove();
}
upgrade_viewability();