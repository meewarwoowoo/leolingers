
jQuery(document).ready(function(){

    jQuery('ol').find('li').each(function(n){
        jQuery(this).append('<div id="map-'+n+'" style="width: 190px; height:190px; "></div>');
        var mapCanvas = document.getElementById('map-'+n);
        var mapOptions = {
            center: new google.maps.LatLng(jQuery(this).data('lat'), jQuery(this).data('lon')),
            zoom: 20,
            tilt:0,
          mapTypeId: google.maps.MapTypeId.SATELLITE
        }
        var map = new google.maps.Map(mapCanvas, mapOptions);
        });

});
