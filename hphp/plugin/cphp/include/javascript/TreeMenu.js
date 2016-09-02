function layer_toggle(obj) 
{
    if (obj.style.display == 'none') obj.style.display = '';
    else if (obj.style.display != 'none') obj.style.display = 'none';
}

function onclick_folder(hc) {
        layer_toggle(hc);
}

<!--
// �ɰ汾 MENU 
function change()
{
	if(!document.all) return;

	if (event.srcElement.id == "foldheader") {
		
		var srcIndex = event.srcElement.sourceIndex;
		var nested = document.all[srcIndex+2];
		
		if (nested.style.display == "none") {
			nested.style.display = '';
		} else {
			nested.style.display = "none";
		}
	}
}

//document.onclick = change;
-->