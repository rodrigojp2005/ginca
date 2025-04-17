<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

    </head>
    <body class="antialiased">
    
        <div class="container text-center">
            <h1>Jogo de Adivinhação Geográfica</h1>
            <p>Tente adivinhar onde está esta imagem do Street View!</p>
            
            <div id="street-view" style="width: 100%; height: 400px;"></div>
            <div id="map" style="width: 100%; height: 400px;"></div>
            
            <button class="btn btn-primary mt-3" onclick="confirmGuess()">Confirmar Palpite</button>
            <p id="result" class="mt-3"></p>
        </div>

    </body>
</html>
<script>
    let map;
    let marker;
    let panorama;
    let correctLocation = { lat: -23.55052, lng: -46.633308 }; // Exemplo (São Paulo)

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 0, lng: 0 },
            zoom: 2,
        });

        panorama = new google.maps.StreetViewPanorama(
            document.getElementById("street-view"), {
                position: correctLocation,
                pov: { heading: 165, pitch: 0 },
                zoom: 1,
            }
        );

        map.addListener("click", function (event) {
            placeMarker(event.latLng);
        });
    }

    function placeMarker(location) {
        if (marker) {
            marker.setPosition(location);
        } else {
            marker = new google.maps.Marker({
                position: location,
                map: map,
            });
        }
    }

    function confirmGuess() {
        if (!marker) {
            alert("Escolha um local no mapa!");
            return;
        }

        let guessedLocation = marker.getPosition();
        let distance = google.maps.geometry.spherical.computeDistanceBetween(
            guessedLocation, new google.maps.LatLng(correctLocation)
        ) / 1000; // Convertendo para km
        
        document.getElementById("result").innerText = `Você está a ${distance.toFixed(2)} km do local correto!`;
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3VgDsnZQDKV1w4TM-D19msn3TgOOMuzk&libraries=geometry&callback=initMap"></script>