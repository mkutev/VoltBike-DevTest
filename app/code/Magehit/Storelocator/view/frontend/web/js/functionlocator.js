

         
    var map;
    var markers = [];
    var infoWindow;
    var locationDiv;
    
    function onMouseover(markerNum) {
        markers[markerNum].setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(function(){ markers[markerNum].setAnimation(null); }, 750);
    }
    
    function onMouseout(markerNum) {
        markers[markerNum].setAnimation(null);
    }
    
    function loadMap(center_lat,center_lng,initial_zoom,baseurl) {
        //initMap();
        map = new google.maps.Map(document.getElementById("map"), {
            center: new google.maps.LatLng(center_lat,center_lng),
            zoom: initial_zoom,
            mapTypeId: 'roadmap',
            mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
        });
//  
        infoWindow = new google.maps.InfoWindow();

        locationDiv = document.getElementById("location");
        input = document.getElementById('address');

        var searchBox = new google.maps.places.SearchBox(input);
        
        var searchUrl = baseurl+'storelocator/index/search';
        downloadUrl(searchUrl, function(data) {
            var xml = parseXml(data);
            var markerNodes = xml.documentElement.getElementsByTagName("marker");

            for (var i = 0; i < markerNodes.length; i++) {
                var latlng = new google.maps.LatLng(
                    parseFloat(markerNodes[i].getAttribute("lat")),
                    parseFloat(markerNodes[i].getAttribute("lng")));

                var marker = new google.maps.Marker({
                    map: map,
                    position: latlng
                });
                
                markers.push(marker);
            }
        });
   }
   function initMap() {

      // Try HTML5 geolocation.
      if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
            var geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

            geocoder.geocode({'latLng': latlng}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        document.getElementById('address').value = results[1].formatted_address;
                    } else {
                        alert('No results found');
                    }
                } else {
                    alert('Geocoder failed due to: ' + status);
                }
            }.bind(this));

           // document.getElementById("latitude").value = position.coords.latitude;
//            document.getElementById("longitude").value = position.coords.longitude;
        }.bind(this), function(error) {
        }.bind(this));
      }
   }

    function searchLocation(val) {
        clearLocations();    
        document.getElementById("loading_mask_loader").style.display = '';
    
        var address = document.getElementById("address").value;
        var radius_search = document.getElementById("radius_search").value;
        if(address == '' || address == null){
            alert('Please enter a location.');
            document.getElementById("loading_mask_loader").style.display = 'none';
            return false;        
        }
        if(radius_search == '' || radius_search == null){
            alert('Please enter a radius.');
            document.getElementById("loading_mask_loader").style.display = 'none';
            return false;        
        }
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({address: address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {            
                var latlng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                var marker = new google.maps.Marker({
                    map: map,
                    position: latlng,
                    icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|6fba33' 
                });
                markers.push(marker);
                
                searchLocationsNear(results[0].geometry.location,val);
            } else {
                document.getElementById("loading_mask_loader").style.display = 'none';
                map.setZoom(initial_zoom);                
                alert(address + ' is not found'); 
            }

        });
    }
    
    function searchStoresByProducts(){
        clearLocations();
        var bounds = new google.maps.LatLngBounds();
        document.getElementById("loading_mask_loader").style.display = '';
        var search_text = document.getElementById("product_search").value;
        var search_type = document.getElementById("typeSelect").value;
        if(search_text == '' || search_text == null){
            alert('Please enter a name or sku or id.');
            document.getElementById("loading_mask_loader").style.display = 'none';
            return false;        
        }else{
            var searchUrl = baseurl+'storelocator/index/searchproduct?search_text=' + search_text + '&search_type='+search_type;
            downloadUrl(searchUrl, function(data) {
                var xml = parseXml(data);
                var markerNodes = xml.documentElement.getElementsByTagName("marker");
                if (markerNodes.length == 0) {
                    document.getElementById("loading_mask_loader").style.display = 'none';
                    var query_search = document.getElementById("product_search").value;
                    alert('There is no store contain product '+search_type+' is: ' + query_search);
                    return;
                }           
                
                          
                for (var i = 0; i < markerNodes.length; i++) {
                    var id = markerNodes[i].getAttribute("id");
                    console.log(markerNodes[i].getAttribute("lat"));
                    var name = markerNodes[i].getAttribute("name");
                    var address = markerNodes[i].getAttribute("address");
                    var distance = parseFloat(markerNodes[i].getAttribute("distance"));
                    var latlng = new google.maps.LatLng(
                        parseFloat(markerNodes[i].getAttribute("lat")),
                        parseFloat(markerNodes[i].getAttribute("lng")));

                    listItem(id, name, address, "", i);
                    createMarker(latlng, name, address, i);
                    bounds.extend(latlng);
                }
                
                map.fitBounds(bounds);
                var zoom = map.getZoom();
                zoom = Math.min(zoom, 12);
                map.setZoom(zoom);
                   
                document.getElementById("loading_mask_loader").style.display = 'none';
                jQuery("#location").owlCarousel({
                    autoPlay: 3000, //Set AutoPlay to 3 seconds
                  items : 3,
                  navigation : true,
                  itemsDesktop : [1199,3],
                  itemsDesktopSmall : [979,3]
              });
            });   
        }        
        return true;
    }
   
    function clearLocations() {
        infoWindow.close();
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        markers.length = 0;
     
        locationDiv.innerHTML = "";
    }

    function searchLocationsNear(center,val) {
        var bounds = new google.maps.LatLngBounds();
        bounds.extend(center);
        map.setCenter(new google.maps.LatLng(center.lat(), center.lng())); 
        map.setZoom(val);
                
        var radius = document.getElementById('radius_search').value;
        var searchUrl = baseurl+'storelocator/index/search?lat=' + center.lat() + '&lng=' + center.lng() + '&radius=' + radius;
        downloadUrl(searchUrl, function(data) {
            var xml = parseXml(data);
            var markerNodes = xml.documentElement.getElementsByTagName("marker");
            if (markerNodes.length == 0) {
    
                
                document.getElementById("loading_mask_loader").style.display = 'none';
                var address = document.getElementById("address").value;
                alert("There is no store near with "+ address);
                return;
            }
                                
            for (var i = 0; i < markerNodes.length; i++) {
                var id = markerNodes[i].getAttribute("id");
                var name = markerNodes[i].getAttribute("name");
                var address = markerNodes[i].getAttribute("address");
                var distance = parseFloat(markerNodes[i].getAttribute("distance"));
                var latlng = new google.maps.LatLng(
                    parseFloat(markerNodes[i].getAttribute("lat")),
                    parseFloat(markerNodes[i].getAttribute("lng")));

                listItem(id, name, address, distance, i);
                createMarker(latlng, name, address, i);
                bounds.extend(latlng);
            }
            
            map.fitBounds(bounds);
            var zoom = map.getZoom();
            zoom = Math.min(zoom, 12);
            map.setZoom(zoom);
               
            document.getElementById("loading_mask_loader").style.display = 'none';
            jQuery("#location").owlCarousel({
                    autoPlay: 3000, //Set AutoPlay to 3 seconds
                  items : 3,
                  navigation : true,
                  pagination : false,
                  navigationText : ["<", ">"],
              });
        });
    }

    function createMarker(latlng, name, address, num) {
        var html = "<b>" + name + "</b> <br/>" + address;
        var marker = new google.maps.Marker({
            map: map,
            position: latlng,
            icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter_withshadow&chld=' + (num + 1) + '|FF0000|0000FF'  
        });
        google.maps.event.addListener(marker, 'click', function() {
            infoWindow.setContent(html);
            infoWindow.open(map, marker);
        });
        markers.push(marker);
    }

    function listItem(id, name, address, distance, num) {
        num = num + 1;
            
        element = document.createElement("div");
        if(distance != ""){
            var att = document.createAttribute("onmouseover");
            att.value = "onMouseover(" + num + ")";
            element.setAttributeNode(att);
        
            att = document.createAttribute("onmouseout");
            att.value = "onMouseout(" + num + ")";
            element.setAttributeNode(att); 
        }
        var att = document.createAttribute('class');
        att.value = "item";
        element.setAttributeNode(att); 
        
        element.innerHTML = '<h3 style="color:#DE5400">'+ name + '</h3>';
        element.innerHTML += '<div class="clear"></div>';
        element.innerHTML += '<p>' + address + '</p>';
        element.innerHTML += '<a class="linkdetailstore" href="'+currenturl+'?id=' + id + '">Details and contact with store</a>';
        locationDiv.appendChild(element);
    }

    function downloadUrl(url, callback) {
        var request = window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest;

        request.onreadystatechange = function() {
            if (request.readyState == 4) {
                request.onreadystatechange = doNothing;
                callback(request.responseText, request.status);
            }
        };

        request.open('GET', url, true);
        request.send(null);
    }

    function parseXml(str) {
        if (window.ActiveXObject) {
            var doc = new ActiveXObject('Microsoft.XMLDOM');
            doc.loadXML(str);
            return doc;
        } else if (window.DOMParser) {
            return (new DOMParser).parseFromString(str, 'text/xml');
        }
    }

    function doNothing() {alert('123444');}

