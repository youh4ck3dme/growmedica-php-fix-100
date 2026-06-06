$("#map").gmap3({
    map: {
        options: {
            center: [48.8620722, 2.352047],
            zoom: 11,
            mapTypeId: google.maps.MapTypeId.HYBRID, // MapTypeId.ROADMAP, MapTypeId.SATELLITE, MapTypeId.HYBRID, MapTypeId.TERRAIN
            mapTypeControl: false,
            navigationControl: true,
            draggable: true,
            scrollwheel: false
        }
    },
    marker: {
        values: [
            {latLng: [48.8620722, 2.352047], data: "Paris !"},
            {address: "86000 Poitiers, France", data: "Poitiers : great city !"},
            {address: "66000 Perpignan, France", data: "Perpignan ! GO USAP !", options: {icon: "http://maps.google.com/mapfiles/marker_green.png"}}
        ],
        options: {
            draggable: false
        },
        events: {
            mouseover: function(marker, event, context) {
                var map = $(this).gmap3("get"),
                        infowindow = $(this).gmap3({get: {name: "infowindow"}});
                if (infowindow) {
                    infowindow.open(map, marker);
                    infowindow.setContent(context.data);
                } else {
                    $(this).gmap3({
                        infowindow: {
                            anchor: marker,
                            options: {content: context.data}
                        }
                    });
                }
            },
            mouseout: function() {
                var infowindow = $(this).gmap3({get: {name: "infowindow"}});
                if (infowindow) {
                    infowindow.close();
                }
            }
        }
    }
});