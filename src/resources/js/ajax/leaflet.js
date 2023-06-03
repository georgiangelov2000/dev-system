var map = null;

export function initializeMap() {
    if ($('#mapWrapper').length) {
        let customerAddress = $('#mapWrapper').attr('customer-address');

        let geocodeUrl = 'https://nominatim.openstreetmap.org/search';
        $.ajax({
            url: geocodeUrl,
            method: 'GET',
            data: {
                q: customerAddress,
                format: 'json',
                addressdetails: 1,
                limit: 1
            },
            success: function (response) {
                if (response.length > 0) {
                    let latitude = parseFloat(response[0].lat);
                    let longitude = parseFloat(response[0].lon);
                    map = L.map('map', {
                        center: [latitude, longitude],
                        zoom: 15
                    });

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                        maxZoom: 15,
                    }).addTo(map);

                } else {
                    console.log('No coordinates found for the address');
                    setDefaultMapView();
                }
            },
            error: function (error) {
                console.log('Failed to geocode the address');
                console.log(error);
                setDefaultMapView();
            }
        });
    } else {
        setDefaultMapView();
    }
}

export function setDefaultMapView(latitude = 51.5074, longitude = -0.1278) {
    setMapView([latitude, longitude], 15);
}


export function setMapView(center, zoom) {
    if (map === null) {
        map = L.map('map', {
            center: center,
            zoom: zoom
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
            maxZoom: 15,
        }).addTo(map);
    } else {
        map.setView(center, zoom);
    }
}
