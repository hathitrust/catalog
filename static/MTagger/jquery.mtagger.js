jQuery.openTag = function (parameters) {
  default_parameters = { mode: 'popup', name: 'tagger', description: 'location=0,scrollbars=0,toolbar=0,status=0,resizeable=1,width=605,height=535' };
  if( parameters ) {
    if(!parameters.mode) parameters.mode = default_parameters.mode;
    if(!parameters.name) parameters.name = default_parameters.name;
    if(!parameters.description) parameters.description = default_parameters.description;
  }
 

  switch(parameters.mode) {
    case 'popup':
      tagwindow = window.open('http://mirlyn.lib.umich.edu/services/MTagger/Login.php' +
                               '?url='+encodeURIComponent(parameters.url) +
                               '&title='+encodeURIComponent(parameters.title),
                               parameters.name,
                               parameters.description);
      /*tagwindow.document.writeln('<html><head></head><body><div></div></body></html>');
      x=tagwindow.document.getElementsByTagName('div');
      jQuery(x).html("Loading page ...");
      jQuery.getJSON(
        "http://www.lib.umich.edu/mtagger/items/add_api?URL="+encodeURIComponent(url)+
                         "&title="+encodeURIComponent(object_title) +
                         "&source=cloud&export=jsonp&callback=?" ,
        function(data) {
          if(data) {
            if(data.loggedin == 0) {
              jQuery(x).html('<iframe onunload="alert(\'unload!\');" src="https://login.umdl.umich.edu/cgi/cosign/proxy?http://www.lib.umich.edu/"></iframe>');
            } else {
              jQuery(x).html();
            }
          } else {
            jQuery(x).html('Failed to load page.');
          }
        }
      ); */
      
      break;
    case 'lightbox':
      break;
  }
  //return this;
}
jQuery.fn.addCloud = function (parameters) {
    default_parameters = {   url: document.location.href, 
                           title: document.title, 
                        tag_link: 'Tag this record ',
                       separator: ', '
                         };
  if(parameters) {
    if(parameters.title) {
      default_parameters.title = parameters.title;
    }
    if(parameters.tag_link) {
      default_parameters.tag_link = parameters.tag_link;
    }
    if(parameters.separator) {
      default_parameters.separator = parameters.separator;
    }
  }
  return this.each( function () { 
    x = this;
    if(x.id) { 
      url = x.id;
    } else {
      url = default_parameters.url;
    }

    if( x.title ) {
      object_title = this.title;
    } else {
      object_title = default_parameters.title;
    }

    if( x.separator ) {
      separator = x.separator;
    } else {
      separator = default_parameters.separator;
    }

    if( x.tag_link ) {
      tag_link = x.tag_link;
    } else {
      tag_link = default_parameters.tag_link;
    }
    jQuery(x).text('Loading tags ...');
    jQuery.getJSON(
      "http://dev.lib.umich.edu/mtagger/tags/getTagCloud?tagDisplay=all&export=jsonp&callback=?&URL="+encodeURIComponent(url),
      function(data) {
        list = new Array;
        tag_this   = '<span class="mtagger_tag_this"><a href="javascript:window.open('+
                       "'http://dev.lib.umich.edu/mtagger/items/add?URL="+encodeURIComponent(encodeURIComponent(url))+
                         "&title="+encodeURIComponent(object_title) +
                         "&source=cloud','tagger','location=0,scrollbars=0,toolbar=0,status=0,resizeable=1,width=605,height=535'"+
                    ");%20void(0);"+
                     '">'+tag_link+'</a></span>';
        tag_this = '<span class="mtagger_tag_this"><a href="javascript:$.openTag({url:\''+url+'\',title: \''+object_title+'\'})"%20void(0);">'+tag_link+'</a></span>';
        if(data) {
          max=0;
          for(i=0; i< data['Tag'].length; i++) {
            if(max < data['Tag'][i]['count'])
              max = data['Tag'][i]['count'];
          }
          for(i=0; i < data['Tag'].length; i++) {
            if(data['Tag'][i]['count'] > (2/3)*max) {
              size='large';
            } else if (data['Tag'][i]['count'] > max/3){
              size='medium';
            } else {
              size='small';
            }
            tag_open   = '<span class="mtagger_tag mtagger_'+size+'">';
            count_open = '<span class="mtagger_count mtagger_'+size+'">';
            sep_open   = '<span class="mtagger_separator mtagger_'+size+'">';
            close      = '</span>';
            list.push( tag_open+data['Tag'][i]['name']+close+count_open+data['Tag'][i]['count']+close );
            h = tag_this + list.join(sep_open+separator+close);
          }
          jQuery(x).html(h);
        } else {
          jQuery(x).html(tag_this+'<span class="mtagger_tag mtagger_medium">This item has not yet been tagged.  Be the first to tag it!</span>');
        }
      }
    ); 
  //alert(this.id); 
    return x;
  });
};
