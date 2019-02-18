
function getElem(id)
{
    if (document.getElementById) {
        return document.getElementById(id);
    } else if (document.all) {
        return document.all[id];
    }
}


function showThese(elemId)
{
   getElem('facet_end_' + elemId).style.display='block';
   getElem('more_' + elemId).style.display='none';
}

function hideThese(elemId)
{
   getElem('facet_end_' + elemId).style.display='none';
   getElem('more_' + elemId).style.display='block';
}
