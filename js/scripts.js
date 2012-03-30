function lightbox()
{
    var lightbox = document.getElementById('lightbox');
    var popupbox = document.getElementById('popupbox');

    popupbox.innerHTML = '<img src="' + path + '/images/loading.gif"><br>Loading...';
   
    hideSelects('hidden');
            
    lightbox.style.top=document.body.scrollTop;
    lightbox.style.display='block';

    popupbox.style.display='block';
    popupbox.style.top=document.body.scrollTop+200;
    popupbox.style.left='25%';
    popupbox.style.width='50%';

}

function hideLightbox()
{
    hideSelects('visible');
    document.getElementById('lightbox').style.display='none';
    document.getElementById('popupbox').style.display='none';
    document.getElementById('emailRecord').style.display='none';
}

function hideSelects(visibility)
{
    selects = document.getElementsByTagName('select');
    for(i = 0; i < selects.length; i++) {
        selects[i].style.visibility = visibility;
    }
}

function showMenu(elemId)
{
    document.getElementById(elemId).style.display='block';
}
