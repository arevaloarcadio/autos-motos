<template>
  <h4>Hola</h4>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Data',
  data () {
    return {
      marcas : [], 
      marks : [],
      models : [],
    }
  },
  created() {
    this.loadDoc()
  },

  methods:{
     loadDoc() {
      var self = this
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        self.myFunction(this);
        }
      };
      xhttp.open("GET", "data.xml", true);
      xhttp.send();
    },
 myFunction(xml) {
  var i;
  var xmlDoc = xml.responseXML;
  this.marcas = this.xmlToJson(xmlDoc)

  this.marcas.brands.brand.forEach(marca => {
    var mark_id = marca.id['#text']
    console.log(mark_id)
    console.log(marca)
    if (Object.prototype.toString.call(marca.models.model) === '[object Array]') {
      marca.models.model.forEach(model =>{
        this.models.push({ id :model.id['#text'], name :model.name['#text'] , mark_id : mark_id})
      })
    }else{
      this.models.push({ id : marca.models.model.id['#text'], name : marca.models.model.name['#text'] , mark_id : mark_id})
    }
  })
  
  console.log(this.models)

    axios.post('/models',{models : this.models })
    .then(resp => {
      console.log(resp)
    })
    .catch(err => {
      console.log(err)
    });

  /*console.log(xmlDoc)
  var table="<tr><th>Title</th><th>Artist</th></tr>";
  var x = xmlDoc.getElementsByTagName("CD");
  for (i = 0; i <x.length; i++) {
    table += "<tr><td>" +
    x[i].getElementsByTagName("TITLE")[0].childNodes[0].nodeValue +
    "</td><td>" +
    x[i].getElementsByTagName("ARTIST")[0].childNodes[0].nodeValue +
    "</td></tr>";
  }
  document.getElementById("demo").innerHTML = table;*/
},

xmlToJson( xml ) {
 
  // Create the return object
  var obj = {};
 
  if ( xml.nodeType == 1 ) { // element
    // do attributes
    if ( xml.attributes.length > 0 ) {
    obj["@attributes"] = {};
      for ( var j = 0; j < xml.attributes.length; j++ ) {
        var attribute = xml.attributes.item( j );
        obj["@attributes"][attribute.nodeName] = attribute.nodeValue;
      }
    }
  } else if ( xml.nodeType == 3 ) { // text
    obj = xml.nodeValue;
  }
 
  // do children
  if ( xml.hasChildNodes() ) {
    for( var i = 0; i < xml.childNodes.length; i++ ) {
      var item = xml.childNodes.item(i);
      var nodeName = item.nodeName;
      if ( typeof(obj[nodeName] ) == "undefined" ) {
        obj[nodeName] =this.xmlToJson( item );
      } else {
        if ( typeof( obj[nodeName].push ) == "undefined" ) {
          var old = obj[nodeName];
          obj[nodeName] = [];
          obj[nodeName].push( old );
        }
        obj[nodeName].push( this.xmlToJson( item ) );
      }
    }
  }
  return obj;
}
  }
}
</script>
 