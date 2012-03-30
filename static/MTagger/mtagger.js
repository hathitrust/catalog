function disableAddButton(form_id) {
    document.getElementById(form_id).saveButton.disabled = true;
    $('#workingImage').show();  
    return(true);
}
  
function closeWindow() {
    self.close();
    /*if (window.opener && !window.opener.closed)
      self.close();
    else
      document.location = '/mtagger/tags/';*/
}

// equivalent of php's ltrim and rtrim
function trim(str) { return str.replace(/^\s+|\s+$/g, '') };
