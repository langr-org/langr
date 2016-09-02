function SelectAll(obj){
  for(var i=0; i<obj.elements.length; i++){
    var e=obj.elements[i];
    if(e.name != 'allbox') e.checked=obj.allbox.checked;
  }
}