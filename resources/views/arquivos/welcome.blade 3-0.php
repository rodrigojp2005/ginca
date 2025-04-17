<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GeoGuessr - Jogo de Adivinhação Geográfica</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .game-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 30px;
            margin-bottom: 30px;
        }
        
        .game-title {
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .game-description {
            color: #7f8c8d;
            margin-bottom: 30px;
        }
        
        .view-container {
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        #street-view, #map {
            height: 400px;
            width: 100%;
        }
        
        .btn-guess {
            background-color: #3498db;
            border: none;
            padding: 12px 30px;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-guess:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .score-display {
            background-color: #f1c40f;
            color: #2c3e50;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        @media (max-width: 768px) {
            #street-view, #map {
                height: 300px;
            }
            
            .game-container {
                padding: 15px;
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="game-container">
            <h1 class="game-title text-center"><i class="fas fa-globe-americas"></i> GeoGuessr Challenge</h1>
            <p class="game-description text-center">Explore a imagem do Street View e marque no mapa onde você acha que está localizado!</p>
            
            <div class="text-center mb-4">
                <div class="score-display">
                    <i class="fas fa-trophy"></i> Pontuação: <span id="score">0</span> pontos
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <h4 class="text-center mb-3"><i class="fas fa-street-view"></i> Vista do Street View</h4>
                    <div class="view-container">
                        <div id="street-view"></div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <h4 class="text-center mb-3"><i class="fas fa-map-marked-alt"></i> Mapa para Palpite</h4>
                    <div class="view-container">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <button class="btn btn-primary btn-guess" onclick="confirmGuess()">
                    <i class="fas fa-check-circle"></i> Confirmar Palpite
                </button>
                <button class="btn btn-secondary ms-2" onclick="newRound()">
                    <i class="fas fa-sync-alt"></i> Novo Jogo
                </button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let map;
        let marker;
        let panorama;
        let correctLocation;
        let score = 0;
        let roundsPlayed = 0;
        const maxRounds = 5;
        
        // Locais possíveis (você pode adicionar mais)
        const locations = [
            { lat: -23.55052, lng: -46.633308, name: "São Paulo, Brasil" },
            { lat: 40.712776, lng: -74.005974, name: "Nova York, EUA" },
            { lat: 48.856613, lng: 2.352222, name: "Paris, França" },
            { lat: 35.6761919, lng: 139.6503106, name: "Tóquio, Japão" },
            { lat: -33.8688197, lng: 151.2092955, name: "Sydney, Austrália" }
        ];

        function initMap() {
            // Inicializa com um local aleatório
            newRound();
            
            // Configura o mapa para palpites
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 0, lng: 0 },
                zoom: 2,
                streetViewControl: false
            });

            map.addListener("click", function(event) {
                placeMarker(event.latLng);
            });
        }

        function newRound() {
            if (roundsPlayed >= maxRounds) {
                Swal.fire({
                    title: 'Jogo Finalizado!',
                    html: `Sua pontuação final é: <b>${score}</b> pontos`,
                    icon: 'success',
                    confirmButtonText: 'Jogar Novamente'
                }).then(() => {
                    // Reinicia o jogo
                    score = 0;
                    roundsPlayed = 0;
                    document.getElementById("score").textContent = score;
                    newRound();
                });
                return;
            }
            
            // Escolhe um local aleatório
            correctLocation = locations[Math.floor(Math.random() * locations.length)];
            
            // Configura a visualização do Street View
            panorama = new google.maps.StreetViewPanorama(
                document.getElementById("street-view"), {
                    position: correctLocation,
                    pov: { heading: Math.random() * 360, pitch: 0 },
                    zoom: 1,
                    disableDefaultUI: true,
                    showRoadLabels: false
                }
            );
            
            // Remove o marcador anterior se existir
            if (marker) {
                marker.setMap(null);
                marker = null;
            }
            
            roundsPlayed++;
        }

        function placeMarker(location) {
            if (marker) {
                marker.setPosition(location);
            } else {
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    draggable: true,
                    animation: google.maps.Animation.DROP
                });
            }
        }

        function confirmGuess() {
            if (!marker) {
                Swal.fire({
                    title: 'Ops!',
                    text: 'Por favor, clique no mapa para marcar seu palpite!',
                    icon: 'warning'
                });
                return;
            }

            let guessedLocation = marker.getPosition();
            let distance = google.maps.geometry.spherical.computeDistanceBetween(
                guessedLocation, new google.maps.LatLng(correctLocation)
            ) / 1000; // Convertendo para km
            
            // Calcula pontos (quanto mais perto, mais pontos)
            let points = Math.max(0, 5000 - Math.floor(distance)) / 100;
            score += Math.round(points);
            document.getElementById("score").textContent = score;
            
            // Determina o ícone e cor com base na distância
            let icon, color, title;
            
            if (distance < 1) {
                icon = 'success';
                color = '#27ae60';
                title = 'Incrível! Quase exato!';
            } else if (distance < 10) {
                icon = 'success';
                color = '#2ecc71';
                title = 'Muito bom!';
            } else if (distance < 100) {
                icon = 'info';
                color = '#f39c12';
                title = 'Boa tentativa!';
            } else {
                icon = 'error';
                color = '#e74c3c';
                title = 'Tente novamente!';
            }
            
            // Mostra o SweetAlert com o resultado
            Swal.fire({
                title: title,
                html: `
                    <div style="text-align: left;">
                        <p><b>Sua distância:</b> ${distance.toFixed(2)} km</p>
                        <p><b>Local correto:</b> ${correctLocation.name}</p>
                        <p><b>Pontos ganhos:</b> ${Math.round(points)}</p>
                    </div>
                `,
                icon: icon,
                confirmButtonColor: color,
                confirmButtonText: 'Próximo Desafio'
            }).then(() => {
                // Só avança para o próximo round quando o usuário clicar
                newRound();
            });
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3VgDsnZQDKV1w4TM-D19msn3TgOOMuzk&libraries=geometry&callback=initMap"></script>
</body>
</html>
