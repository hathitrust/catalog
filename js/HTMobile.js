


//depends on jQuery
jq(document).ready(function() {


	jq("#moresearchresults a").html('Load More');

    jq("#moresearchresults a").click(function() {
		try{
			jq.ajax({
				url:jq(this).attr('href'),
				cache:true,
				success:function(data) {
					if(data){
						var d="<div>" + data + "</div>";
						jq("#searchResults").html( jq("#searchResults").html() + jq(d).find("#searchResults").html() );
						if( ! jq(d).find("#moresearchresults")[0]){
							jq("#moresearchresults").remove();
						}else{
							jq("#moresearchresults a").attr('href',jq(d).find("#moresearchresults a").attr('href'));
							jq("#moresearchresults a").html('Load More');
						}
					}else{
						cosole.log("Load More Results Failed, no data returned: " + jq(this).attr('href'))
					}
				},
		    	error:function(){
		    		cosole.log("Load More Results Failed: " + jq(this).attr('href'))
		    	}
			});
		}catch(err){
			alert(err.toString());
		}finally{
			return false;
		}
    });
});

function lessDetails(){
	try{
		hideElement('addlinfo');
		showElement('moredetails');
	}catch(err){
		alert(err.toString());
	}finally{
		return false;
	}
}

function moreDetails(){
	try{
		hideElement('moredetails');
		showElement('addlinfo');
	}catch(err){
		alert(err.toString());
	}finally{
		return false;
	}
}

function htToggleFacetView(more, e1,e2,e3){
	if(more){
		jq(e1).css('display','none');
		jq(e2).css('display','');
		jq(e3).css('display','');
	}else{
		jq(e2).css('display','none');
		jq(e3).css('display','none');	
		jq(e1).css('display','');
	}
}

function ShowHideEmail(id,arrow){
	jq("#" + id).animate({"height": "toggle"}, { duration: 500 });
	if(jq(arrow).hasClass("emailopen")){
		jq(arrow).removeClass("emailopen");
	}else{
		jq(arrow).addClass("emailopen");
	}
}

function inputFocus(i){
    if(i.value==i.title){ 
    	console.log("Input Focus, clearing. Value: " + i.value + "; Default Value: " + i.defaultValue); 
    	i.value=""; 
    	i.style.color="#000";
    }else{
		console.log("Input Focus, not clearing. Value: " + i.value + "; Default Value: " + i.defaultValue);
		i.style.color="#000";  
    }
}
function inputBlur(i){
    if(i.value==""){ console.log("Input Blur"); i.value=i.title; i.style.color="#888"; }
}

