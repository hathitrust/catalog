  function lbclose() {
    jq.fn.fancybox.close();
  }
  
  
  function sendSMS(id) {
    var fw = jq('#fancy_wrap');    
    provider = jq(':input[name=provider]', fw).val();
    phonenumber = jq('input[name=to]', fw).val();
    pn = phonenumber.replace(/\D/g, '');
    if (!pn.match(/^\d{10}$/)) {
      jq("div.erError",fw).text('"' + phonenumber + '" is not a valid ten-digit phone number').css('color', 'red');
      return false;      
    }
    if (provider == '') {
      jq("div.erError",fw).text('Choose a cell-phone service provider').css('color', 'red');
      return false;
    }
    jq.post(
      '/Search/SearchExport',
      {
        method: 'sendSMS',
        handpicked: id,
        id: id,
        phonenumber: phonenumber,
        provider: provider
      },
      function (data) {
        if (data['error'] != undefined) {
          jq("div.erError",fw).text(data['error']).css('color', 'red');
          return false;
        }
        if (data['success'] != undefined) {
          jq("div.erError",fw).text(data['success']).css('color', 'green');
          setTimeout('jq.fn.fancybox.close()', 1500);   
          return false;      
        }
      },
      'json'
    );
    
    
  }

  function exportTempToRefworks(whatToExport) {
    var fw = jq('#fancy_wrap');    
    var val = jq('input[name=campus]:checked', fw).val();
    if (!val) {
     alert("Choose a campus proxy to proceed");
     return false;
   }
    
    url = '/Search/SearchExport?method=refworksRedirect&tempset=1&' + whatToExport + '&campus=' + val;
    var win = window.open("", 'RefWorksMain');
    win.location.href = url;
    setTimeout('jq.fn.fancybox.close()', 1500);
  }

  function replaceLightbox(id) {
    jq('#fancy_div').html(jq('#' + id).html());
  }
 
  function clearTempItems() {
    jq('button.temptoggle').attr('disabled', 'disabled');
    jq("div.erError", jq('#fancy_wrap')).css('color', 'black').html("Clearing selected items...");
    jq.post(
      '/Tags/TagInterface',
      {
        method: 'clearTemp'
      },
      function(data) {
        updateTemp(0);
        setTimeout("jq.fn.fancybox.close()", 1500);
        // jq('.tempSelected').each(function(){
        //   id = jq(this).attr('id');
        //   id = id.replace('saveLink_', '');
        //   turnOnSelect(id);
        // });
        if (isSelectedItemsPage) {
          window.location.reload();
        }
        
        jq('input.selectedCheckbox:checked').each(function() {
          finalLabel = 'Select';
          finalLabelClass = 'unselectedCheckboxLabel';
          cb = jq(this);
          label = jq('label[for=' + cb.attr('id') + ']', cb.parent());
          label.html(finalLabel).removeClass('unselectedCheckboxLabel').removeClass('selectedCheckboxLabel').addClass(finalLabelClass);
          cb.removeAttr('checked');
        });
        
        jq('button.temptoggle').removeAttr('disabled');
      },
      'json'
    );
  }
  
  
  function favoritesToggle(checkbox) {
    checkbox = jq(checkbox);
    var myid = checkbox.attr('id').replace(/^inFavorites_(.*)$/, '$1');
    var recordheader = jq('#recordheader');
    var recordtools = jq('#recordTools');
    var record_metadata = jq("#record_metadata");
    var content = jq("#content div.record");
    
    if (checkbox.attr('checked') || myid == 'tempset') {
      favoritesAdd(myid);
    } else {
      favoritesDelete(myid, function(data) {
        removeFavoritesNotation([myid]);
      });
    }

    if (document.all && recordheader) { // Only in IE
      jq("#content").empty();
      jq('#content').append(recordheader);
      jq('#content').append(recordtools);
      jq('#content').append(content);
      // jq('#content').append(recordheader);
      // jq('#content').append(recordtools);
      // jq('#content').append(record_metadata);
    }
  }
  
  function favoritesAdd(myid) {
    var fw = jq('#fancy_wrap');
    var options = {};
    if (myid == 'tempset') {
      options['method'] = 'tempToFavorites';
      tryToAdd = jq('.tempcount:first').text();
      title = "Save Selected Items to <span class=\"favorites\">Favorites</span>";
    } else {
      options['method'] = 'addToFavorites';
      options['ids'] = myid;
      tryToAdd = 1;
      title = "Save record to <span class=\"favorites\">Favorites</span>";
    }
    
    stflink = jq('#saveToFavoritesLink');
    if (isFavoritesPage != undefined && isFavoritesPage) {
      jq(stflink).fancybox({
        hideOnContentClick: false, 
        overlayShow: true, 
        frameHeight: 410,
        callbackOnClose: function() {window.location.reload();}
      });
    } else {
      jq(stflink).fancybox({
        hideOnContentClick: false, 
        overlayShow: true, 
        frameHeight: 410
      });      
    }
    
    stflink.click();
    
    if (username.length == 0) {
      return;
    }

    jq('.fbTitle', fw).html(title);

    jq.post(
       '/Tags/TagInterface',
       options,
       function (data) {
         numSaved = data.newFavorites.length;
         alreadyFaves = tryToAdd - numSaved;
         
         // Make the display nice and pretty
         alreadyFavesDisplay = alreadyFaves
         denom = numSaved == 1? 'item' : 'items';
         if (alreadyFaves == 1) {
           verb = 'was';
           if (tryToAdd == 1) {
             alreadyFavesDisplay = 'it';
           }
         } else {
           verb = 'were';
           if (numSaved == 0) {
             alreadyFavesDisplay = 'all'
             if (alreadyFaves == 2) {
               alreadyFavesDisplay = 'both';
             }
           }
         }
         response = "Save successful.";
         if (numSaved != tryToAdd) {
           response = 'Saved ' + numSaved + ' of ' + tryToAdd + " items";
         }
         if (alreadyFaves > 0) {
           response += '<br>(' + alreadyFavesDisplay  + ' ' + verb + ' already in your Favorites)';
         }
         jq('.favoritesDialogStatus', fw).html(response).addClass("success");
         
         // Show the undo if we actually did anything
         if (numSaved > 0) {
           jq('button[name=undoList]', fw).attr('ref', data.newFavorites.join(','));
           jq('.undoForm', fw).show();
         }
         
         // Add the list of all attempted ids to the extratagging button
         jq('button.addExtraTags', fw).attr('ref', data.attemptedFavorites.join(','));

         
         // Show the optional tags form
         jq('.favoritesDialogFinished', fw).show();
         
         // Add the favorites label to everything that's been favorited
         addFavoritesNotation(data.newFavorites);         
       },
       'json'
     );    
     return false;
  }
  
  
  function removeFavoritesNotation(ids) {
    jq.each(ids, function() {
      jq('#favorite_' + this).html('');
      jq('label[for=inFavorites_' + this + ']')
        .html('Add to <span class="favorites">Favorites</span>')
        .removeClass('favoritedCheckboxLabel')
        .addClass('unfavoritedCheckboxLabel');
      jq('#inFavorites_' + this).removeAttr('checked');
    });
  }
  
  function addFavoritesNotation(ids) {
    jq.each(ids, function() {
      jq('#favorite_' + this).html('<span class="favorites">Favorite</span>');
      jq('label[for=inFavorites_' + this + ']')
        .html('<span class="favorites">Favorites</span>')
        .removeClass('unfavoritedCheckboxLabel')
        .addClass('favoritedCheckboxLabel');
      jq('#inFavorites_' + this).attr('checked', true);
    });
    
  }
  
  function favoritesDelete(idlist, callback) {
    jq.post(
      '/Tags/TagInterface',
      {
        method: 'removeFromFavorites',
        ids: idlist
      },
      callback,
      'json'
    );
    return false;
  }
  
  function favoritesUndo(button) {
    var fw = jq('#fancy_wrap');    
    button = jq(button);
    var idlist = button.attr('ref');
    var ids = idlist.split(/\s*,\s*/);
    jq('.favoritesDialogFinished', fw).hide();
    button.html("Undoing...");
    var callback =  function(data) {
        button.html("Undone.");
        removeFavoritesNotation(data.deletedIDs);
         setTimeout("jq.fn.fancybox.close()", 1500);     
      };
    favoritesDelete(idlist, callback);
  }

  function addExtraTags(button) {
    var fw = jq('#fancy_wrap');    
    button = jq(button);
    var idlist = button.attr('ref');
    var ids = idlist.split(/\s*,\s*/);
    var tags = jq('input[name=additional_tags]', fw).val().toLowerCase().split(/\s*,\s*/);    
    jq.post(
      '/Tags/TagInterface',
      {
        method: 'tagIDs',
        ids: ids.join(','),
        tags: tags.join(',')
      },
      function (data) {
        jq("div.erError", fw).css('color', 'green').html('Success!');
        setTimeout("jq.fn.fancybox.close()", 1500);     
      },
      'json'
    );
        
  }



  function editFavoriteForm(button) {
    button = jq(button);
    form = button.parent();
    var fw = jq('#fancy_wrap');        
    taglist = jq('input[name=tags]', form).val();
    var tags = [];
    if (taglist.match(/\S/)) {
      tags = taglist.toLowerCase().replace(/^\s*(.*?)\s*$/, '$1').trim().split(/\s*,\s*/).sort();
    }
    
    myid = jq('input[name=id]', form).val();
    title = jq('input[name=title]', form).val();
    jq('#openEditFavorite').click();
    jq('span.favFormTitle', fw).text(title);
    jq('input[name=id]', fw).val(myid);
    
    
    if (tags.length > 0) {
      tableBody = jq('tbody.currentFavoritesCheckboxes', fw);
       var i = 0;
       jq.each(tags, function() {
         i++;
         var cb = '<input id="oldtags_' + i + '" type="checkbox" checked="checked" name="oldtags" value="' + this + '">';
         var label = '<label for="oldtags_' + i + '">' + this + '</label>';
         tableBody.append('<tr><td>' + cb + '</td><td>' + label + '</td></tr>');
       });      
    } else {
      jq('div.currentFavorites', fw).hide();
    }
 
  }
  
  function openDeleteFavorite(datadiv) {
    var myid = jq('input[name=id]', datadiv).val();
    var title = jq('span.favFormTitle').text();    
    replaceLightbox('deleteFavorite');
    var fw = jq('#fancy_wrap');            
    var button =jq('button.deleteButton', fw);
    jq('span.favFormTitle', fw).text(title);
    button.click(function() {deleteFavorite(myid, button)});
  }
  
  function deleteFavorite(id, button) {
    callback = function(data) {
      button.text("Deleting...");      
      if (data.favoriteCount == 0) {
         window.location.reload();
       }
       jq('#record_' + id).remove();
       ceop = jq('#currentEndOfPageCount');
       tc = jq('#favcount');
       tc.text(data.favoriteCount);
       if (ceop) {
         x = parseInt(ceop.text());
         ceop.text(x - 1);
       }


      jq('#favoritesLinks').load('/Tags/TagInterface', {method: 'tagsAndCountsSnippet'});      
      setTimeout("jq.fn.fancybox.close()", 1500);
    };  
    favoritesDelete(id, callback);
    if (is_ie6) {
      alert("In IE6");
      window.location.reload();
    }
    return false;
  }
  
  function editFavorite(datadiv) {
    // Need to build an array by iterating and using push
    var oldtags = [];
    jq('input[name=oldtags]:checked', datadiv).each(function() {
      oldtags.push(jq(this).val());
    });
    var myid = jq('input[name=id]', datadiv).val();
    var taglist = jq('input[name=newtags]', datadiv).val();
    var newtags = [];

    jq.each(taglist.toLowerCase().replace(/^\s*(.*?)\s*$/, '$1').trim().split(/\s*,\s*/).sort(), 
        function() {
          if (this.match(/\S/)) {
            newtags.push(this);
          }
        });
    jq.post(
      '/Tags/TagInterface',
      {
        method: 'editFavorite',
        oldtags: oldtags.join(','),
        newtags: newtags.join(','),
        id: myid
      },
      function(data) {
        //...but we really want to just fix the tags.
        taglist  = data.tags.join(',');
        rec = jq('#record_' + myid);
        jq('input[name=tags]', rec).val(taglist);
        newtags = [];
        jq.each(data.tags.sort(), function() {
          newtags.push('<a href="/MyReseach/Favorites?tag=' + escape(this) + '">' + this + "</a>");
        });
        
        tagLinks = jq('.taglist', rec);
        tagLinks.empty();
        tagLinks.append(newtags.join("<br>"));
        jq('#favoritesLinks').load('/Tags/TagInterface', {method: 'tagsAndCountsSnippet'});
        jq.fn.fancybox.close()        
      }, 
      'json'
    
    );
  }  

  function tagIDs(idlist, taglist, callback) {
    jq.post(
      '/Tags/TagInterface',
      {
        method: tagIDs,
        ids: idlist,
        tags: taglist
      },
      callback,
      'json'
    );
    return false;
  }  
  
  function dump(arr,level) {
  	var dumped_text = "";
  	if(!level) level = 0;

  	//The padding given at the beginning of the line.
  	var level_padding = "";
  	for(var j=0;j<level+1;j++) level_padding += "    ";

  	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
  		for(var item in arr) {
  			var value = arr[item];

  			if(typeof(value) == 'object') { //If it is an array,
  				dumped_text += level_padding + "'" + item + "' ...\n";
  				dumped_text += dump(value,level+1);
  			} else {
  				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
  			}
  		}
  	} else { //Stings/Chars/Numbers etc.
  		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
  	}
  	return dumped_text;
  }
  
  
  function selectedToggle(cb) {
    cb = jq(cb);
    label = jq('label[for=' + cb.attr('id') + ']', cb.parent());
    idstring = cb.attr('id');
    myid = idstring.toString().replace(/inSelected_(.*)/, '$1');

    
    if (cb.attr('checked')) {
      method = 'tagAsTemp';
      message = 'Selecting...';
      finalLabel = 'Selected';
      finalLabelClass = 'selectedCheckboxLabel';
    } else {
      method = 'removeFromTemp';
      message = 'Unselecting';
      finalLabel = 'Select';
      finalLabelClass = 'unselectedCheckboxLabel';
    }
    
    jq('input.selectedCheckbox').attr('disabled', 'disabled');
    label.html(message);
    jq.post(
      '/Tags/TagInterface',
      {
        method: method,
        id:     myid
      },
      function (data) {
        updateTemp(data.numItems);
        label.html(finalLabel).removeClass('unselectedCheckboxLabel').removeClass('selectedCheckboxLabel').addClass(finalLabelClass);
        if (isSelectedItemsPage) {
          if (data.numItems == 0) {
            window.location.reload();
          }
          jq('#record_' + myid).remove();
          ceop = jq('#currentEndOfPageCount');
          if (ceop) {
            x = parseInt(ceop.text());
            ceop.text(x - 1);
          }
        }
        jq('input.selectedCheckbox').removeAttr('disabled');        
      },
      'json'
    );    
  }
  
 
  function updateTemp(numItems) {
    jq('button.temptoggle').attr('disabled', 'disabled');
    
    var old = jq('.tempcount:first').text();
    if (numItems == 0) {
      jq('#tempFolder').hide();
      jq('#tempFolderEmpty').show();
    } else {
      jq('#tempFolder').show();
      jq('#tempFolderEmpty').hide();
      
    }
    if (numItems == 1) {
      jq('.tempdenom').text('item');
    } else {
        jq('.tempdenom').text('items');
    }   
    jq('.tempcount').text(numItems);
    jq('button.temptoggle').removeAttr('disabled');
  }    
  
  function updateTempCountFromServer() {
    jq.post(
      '/Tags/TagInterface',
      {
        method: 'tempCount'
      },
      function(data) {
        updateTemp(data.numItems);
        
      },
      'json'
    );
  }  
    
  function emailSelectedRecords(options) {
    jq('button.temptoggle').attr('disabled', 'disabled');
    var url = "/Search/SearchExport?";
     options['method'] =  'emailTempRecords';
     var fw = jq('#fancy_wrap');
     jq("[name=to],[name=from],[name=message]", fw).each(function(i){
        ibox = jq(this);
        options[ibox.attr('name')] = ibox.val();
     });
     jq("div.erError", fw).css('color', 'black').html("Sending...");
     jq.post(url, options, function(data, status) {
       if (data.mailerror) {
         jq("div.erError", fw).css('color', 'red').html(data.mailerror);
       } else {
         jq("div.erError", fw).css('color', 'green').html('Records successfully emailed to ' + options['to']);
         setTimeout("jq.fn.fancybox.close()", 1500);
       }
     }, 'json');
     jq('button.temptoggle').removeAttr('disabled');
     
  }

  function emailSearch(f) {
    var url = "/Search/SearchExport?" + jssearchcomps;
    var options = {method: 'emailSearch'};
    var fw = jq('#fancy_wrap');
    jq("[name=to],[name=from],[name=message]", fw).each(function(i){
       ibox = jq(this);
       options[ibox.attr('name')] = ibox.val();
    });
    jq("div.erError", fw).css('color', 'black').html("Sending...");
    jq.post(url, options, function(data, status) {
      if (data.mailerror) {
        jq("div.erError", fw).css('color', 'red').html(data.mailerror);
      } else {
        jq("div.erError", fw).css('color', 'green').html('Records successfully emailed to ' + options['to']);
        setTimeout("jq.fn.fancybox.close()", 1500);
      }
    }, 'json');
    
  }


  jq(document).ready(function() {updateTempCountFromServer();});


// Dealing with clicklogging

  function clickpostlog(a, args) {
    a = jq(a);
    if (!args) {
      args = a.attr('ref').split('|');
    }
    jq.post(
      '/Log/LogIt',
      {
        'lc' : args[0] || '',
        'lv1': args[1] || '',
        'lv2': args[2] || '',
        'lv3': args[3] || '',
        'lv4': args[4] || ''
      }
    );
    // f = jq('#clickpostlogForm');
    // jq('input[name=lc]', f).val(args[0]);
    // jq('input[name=lv1]', f).val(args[1]);
    // jq('input[name=lv2]', f).val(args[2]);
    // jq('input[name=lv3]', f).val(args[3]);
    // jq('input[name=oob]', f).val(args[4]);
    // f.attr('action', a.attr('href'));
    // f.attr('action', 'http://waffle.umdl.umich.edu/perl/env.pl');
    // alert(f.attr('action') + " : " + f.attr('method'));
    // f.trigger('submit');
  }
  
  jq(document).ready(function() {
    jq('a.clickpostlog').live('click', function(e) {
      clickpostlog(this);
    });
  });
