<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gincaneiros - Adivinhe o local</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #f1c40f;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 56px;
        }
        
        /* Navbar */
        .navbar {
            background-color: var(--secondary-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: white !important;
        }
        
        .navbar-brand i {
            color: var(--accent-color);
        }
        
        .score-display {
            background-color: var(--accent-color);
            color: var(--secondary-color);
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            margin-left: 10px;
        }
        
        /* Game Container */
        .game-container {
            height: calc(100vh - 56px);
            position: relative;
            overflow: hidden;
        }
        
        /* Street View Container */
        #street-view-container {
            width: 100%;
            height: 100%;
            position: relative;
        }
        
        #street-view {
            width: 100%;
            height: 100%;
        }
        
        /* Map Toggle Button */
        .map-toggle {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 100;
            cursor: pointer;
            animation: pulse 2s infinite;
            border: none;
            outline: none;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Map Container */
        #map-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            z-index: 90;
            transform: translateY(100%);
            transition: transform 0.3s ease-out;
        }
        
        #map-container.show {
            transform: translateY(0);
        }
        
        #map {
            width: 100%;
            height: 100%;
        }
        
        .close-map {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--danger-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            z-index: 95;
            cursor: pointer;
            border: none;
            outline: none;
        }
        
        /* Confirm Button */
        .confirm-btn-container {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            z-index: 95;
        }
        
        .confirm-btn {
            background-color: var(--success-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Mobile Optimization */
        @media (max-width: 768px) {
            .map-toggle {
                width: 50px;
                height: 50px;
                font-size: 20px;
                bottom: 15px;
                right: 15px;
            }
            
            .confirm-btn {
                padding: 10px 25px;
                font-size: 16px;
            }
        }
        
        /* Offcanvas Menu */
        .offcanvas-header {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .offcanvas-title {
            font-weight: 700;
        }
        
        .nav-link {
            color: var(--secondary-color);
            font-weight: 500;
        }
        
        .nav-link:hover {
            color: var(--primary-color);
        }
        
        /* Share Button */
        .share-btn {
            background-color: #9b59b6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            margin-top: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-globe-americas me-2"></i>GeoGuessr
            </a>
            
            <div class="score-display">
                <i class="fas fa-trophy me-1"></i> <span id="score">0</span>
            </div>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title">Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#howToPlayModal">
                                <i class="fas fa-question-circle me-2"></i>Como Jogar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#aboutModal">
                                <i class="fas fa-info-circle me-2"></i>Sobre o Jogo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#contactModal">
                                <i class="fas fa-envelope me-2"></i>Contato
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Game Container -->
    <div class="game-container">
        <!-- Street View -->
        <div id="street-view-container">
            <div id="street-view"></div>
            
            <!-- Map Toggle Button -->
            <button class="map-toggle" id="mapToggle">
                <i class="fas fa-map-marked-alt"></i>
            </button>
        </div>
        
        <!-- Map Container -->
        <div id="map-container">
            <div id="map"></div>
            
            <!-- Close Map Button -->
            <button class="close-map" id="closeMap">
                <i class="fas fa-times"></i>
            </button>
            
            <!-- Confirm Button -->
            <div class="confirm-btn-container">
                <button class="confirm-btn" id="confirmBtn">
                    <i class="fas fa-check-circle"></i> Confirmar Palpite
                </button>
            </div>
        </div>
    </div>

    <!-- How to Play Modal -->
    <div class="modal fade" id="howToPlayModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Como Jogar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ol>
                        <li>Observe atentamente a imagem do Street View</li>
                        <li>Toque no botão do mapa (globo piscante) para abrir o mapa</li>
                        <li>Toque no mapa para marcar seu palpite</li>
                        <li>Toque em "Confirmar Palpite" quando estiver seguro</li>
                        <li>Quanto mais perto do local real, mais pontos você ganha!</li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- About Modal -->
    <div class="modal fade" id="aboutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Sobre o Jogo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>GeoGuessr é um jogo de adivinhação geográfica que desafia seu conhecimento de lugares ao redor do mundo.</p>
                    <p>Você será colocado em um local aleatório através do Google Street View e deverá marcar no mapa onde acredita estar.</p>
                    <p>Desenvolvido para diversão e aprendizado geográfico.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-envelope me-2"></i>Contato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Entre em contato conosco para sugestões ou relatar problemas:</p>
                    <p><i class="fas fa-envelope me-2"></i> contato@geoguessr.com</p>
                    <p><i class="fas fa-phone me-2"></i> (11) 99999-9999</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                </div>
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
        let currentGameLocations = [];
        let gameSeed = generateGameSeed();
        
        // Locais possíveis
        const locations = [
            { lat: -23.55052, lng: -46.633308, name: "São Paulo, Brasil" },
            { lat: 40.712776, lng: -74.005974, name: "Nova York, EUA" },
            { lat: 48.856613, lng: 2.352222, name: "Paris, França" },
            { lat: 35.6761919, lng: 139.6503106, name: "Tóquio, Japão" },
            { lat: -33.8688197, lng: 151.2092955, name: "Sydney, Austrália" },
            { lat: 51.5074, lng: -0.1278, name: "Londres, Reino Unido" },
            { lat: 55.7558, lng: 37.6173, name: "Moscou, Rússia" },
            { lat: 39.9042, lng: 116.4074, name: "Pequim, China" },
            { lat: 19.4326, lng: -99.1332, name: "Cidade do México, México" },
            { lat: 34.6037, lng: -58.3816, name: "Buenos Aires, Argentina" }
        ];

        // Gera um seed único para o jogo
        function generateGameSeed() {
            return Math.floor(Math.random() * 1000000);
        }
        
        // Gera um link compartilhável
        function generateShareLink() {
            const locationsStr = currentGameLocations.map(loc => `${loc.lat},${loc.lng}`).join('|');
            return `${window.location.origin}${window.location.pathname}?seed=${gameSeed}&locations=${encodeURIComponent(locationsStr)}`;
        }
        
        // Inicializa o mapa quando a API do Google é carregada
        function initMap(){
            // Verifica se há um jogo para carregar via URL
            const urlParams = new URLSearchParams(window.location.search);
            const seedParam = urlParams.get('seed');
            const locationsParam = urlParams.get('locations');
            
            if (seedParam && locationsParam) {
                gameSeed = seedParam;
                const locationsCoords = decodeURIComponent(locationsParam).split('|');
                currentGameLocations = locationsCoords.map(coord => {
                    const [lat, lng] = coord.split(',');
                    const location = locations.find(l => l.lat === parseFloat(lat) && l.lng === parseFloat(lng));
                    return location || { lat: parseFloat(lat), lng: parseFloat(lng), name: "Local Desconhecido" };
                });
            }
        
        
            newRound();
            
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 0, lng: 0 },
                zoom: 2,
                streetViewControl: false,
                gestureHandling: "greedy"
            });

            map.addListener("click", function(event) {
                placeMarker(event.latLng);
            });
            
            // Configura os botões de toggle do mapa
            document.getElementById('mapToggle').addEventListener('click', function() {
                document.getElementById('map-container').classList.add('show');
            });
            
            document.getElementById('closeMap').addEventListener('click', function() {
                closeMap();
            });
            
            document.getElementById('confirmBtn').addEventListener('click', function() {
                confirmGuess();
            });
        }

        
        // Fecha o mapa
        function closeMap() {
            document.getElementById('map-container').classList.remove('show');
        }

        // Nova rodada do jogo
        function newRound() {
            // Fecha o mapa se estiver aberto
            closeMap();
            
            if (roundsPlayed >= maxRounds) {
                endGame();
                return;
            }
            
            // Escolhe um local
            if (currentGameLocations.length > 0) {
                // Usa a lista de locais do jogo compartilhado
                correctLocation = currentGameLocations[roundsPlayed];
            } else {
                // Escolhe um local aleatório e armazena para possível compartilhamento
                correctLocation = locations[seededRandom(roundsPlayed)];
                currentGameLocations.push(correctLocation);
            }
            
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
        
        // Gera números aleatórios baseados no seed para consistência
        function seededRandom(index) {
            const x = Math.sin(gameSeed + index) * 10000;
            return Math.floor((x - Math.floor(x)) * locations.length);
        }
        
    
        // Finaliza o jogo
        function endGame() {
            const shareLink = generateShareLink();
            
            Swal.fire({
                title: 'Jogo Finalizado!',
                html: `
                    <div style="text-align: left;">
                        <p>Sua pontuação final é: <b>${score}</b> pontos</p>
                        <p>Locais deste jogo:</p>
                        <ul>
                            ${currentGameLocations.map(loc => `<li>${loc.name}</li>`).join('')}
                        </ul>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Jogar Novamente',
                showDenyButton: true,
                denyButtonText: 'Compartilhar Jogo',
                footer: `<small>Seed do jogo: ${gameSeed}</small>`
            }).then((result) => {
                if (result.isDenied) {
                    // Compartilhar o jogo
                    if (navigator.share) {
                        navigator.share({
                            title: 'Desafie-me no GeoGuessr!',
                            text: `Tente adivinhar estes locais como eu fiz! Pontuação: ${score}`,
                            url: shareLink
                        }).catch(() => {
                            copyToClipboard(shareLink);
                        });
                    } else {
                        copyToClipboard(shareLink);
                    }
                } else {
                    // Reinicia o jogo
                    resetGame();
                }
            });
        }
        
        // Copia para a área de transferência
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    title: 'Link copiado!',
                    text: 'Cole e compartilhe com seus amigos!',
                    icon: 'success'
                });
            });
        }
        
        // Reinicia o jogo
        function resetGame() {
            score = 0;
            roundsPlayed = 0;
            gameSeed = generateGameSeed();
            currentGameLocations = [];
            document.getElementById("score").textContent = score;
            newRound();
        }

        // Coloca um marcador no mapa
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

        // Confirma o palpite do jogador
        function confirmGuess() {
            if (!marker) {
                Swal.fire({
                    title: 'Ops!',
                    text: 'Por favor, marque um local no mapa!',
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
                newRound();
            });
        }
    </script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3VgDsnZQDKV1w4TM-D19msn3TgOOMuzk&libraries=geometry&callback=initMap"></script>
</body>
</html>
