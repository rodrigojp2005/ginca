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
            display: block;
        }
        
        #street-view {
            width: 100%;
            height: 100%;
        }
        
        .map-icon {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .map-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            cursor: pointer;
            padding: 0;
            overflow: hidden;
            border: 2px solid var(--primary-color);
        }
        
        /* Map Container */
        #map-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            z-index: 900;
            transform: translateY(100%);
            transition: transform 0.3s ease-out;
            display: none;
        }
        
        #map-container.show {
            transform: translateY(0);
            display: block;
        }
        
        #map {
            width: 100%;
            height: 100%;
        }
        
        /* Confirm Button */
        .confirm-btn-container {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            z-index: 950;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-globe-americas me-2"></i>Gincaneiros
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
                <img src="https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExbHdyenVlcDR4cXFydHVxb2Q1bW5ibmh1aHplcGFubzRvdWwyYmQydCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/PjTRytCVV9ROhgtEyI/giphy.gif" alt="Mapa" class="map-icon">
            </button>
        </div>
        
        <!-- Map Container -->
        <div id="map-container">
            <div id="map"></div>
            
            <!-- Confirm Button -->
            <div class="confirm-btn-container">
                <button class="confirm-btn" id="confirmBtn">
                    <i class="fas fa-check-circle"></i> Confirmar Palpite
                </button>
            </div>
        </div>
    </div>

    <!-- Modais -->
    <!-- Como Jogar -->
    <div class="modal fade" id="howToPlayModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Como Jogar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>1. Você verá uma imagem do Google Street View</p>
                    <p>2. Tente adivinhar onde essa localização está no mapa</p>
                    <p>3. Clique no botão do mapa no canto inferior direito</p>
                    <p>4. Marque seu palpite no mapa e confirme</p>
                    <p>5. Quanto mais perto do local real, mais pontos você ganha!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sobre o Jogo -->
    <div class="modal fade" id="aboutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Sobre o Jogo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Gincaneiros é um jogo de geolocalização onde você testa seu conhecimento geográfico.</p>
                    <p>Cada rodada mostra uma localização aleatória no mundo através do Google Street View.</p>
                    <p>Sua missão é marcar no mapa onde você acha que essa localização está.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contato -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-envelope me-2"></i>Contato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Dúvidas, sugestões ou problemas?</p>
                    <p>Entre em contato conosco:</p>
                    <p><i class="fas fa-envelope me-2"></i> contato@gincaneiros.com</p>
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
        let gameSeed = Math.floor(Math.random() * 1000000);
        
        // Lista de 100 locais diversificados como fallback
        const WORLDWIDE_FALLBACKS = [
            // América do Norte (20 locais)
            { lat: 40.7128, lng: -74.0060, name: "Nova York, EUA" },
            { lat: 34.0522, lng: -118.2437, name: "Los Angeles, EUA" },
            { lat: 41.8781, lng: -87.6298, name: "Chicago, EUA" },
            { lat: 29.7604, lng: -95.3698, name: "Houston, EUA" },
            { lat: 39.9526, lng: -75.1652, name: "Filadélfia, EUA" },
            { lat: 33.4484, lng: -112.0740, name: "Phoenix, EUA" },
            { lat: 32.7157, lng: -117.1611, name: "San Diego, EUA" },
            { lat: 29.4241, lng: -98.4936, name: "San Antonio, EUA" },
            { lat: 32.7767, lng: -96.7970, name: "Dallas, EUA" },
            { lat: 37.7749, lng: -122.4194, name: "San Francisco, EUA" },
            { lat: 43.6532, lng: -79.3832, name: "Toronto, Canadá" },
            { lat: 49.2827, lng: -123.1207, name: "Vancouver, Canadá" },
            { lat: 45.5017, lng: -73.5673, name: "Montreal, Canadá" },
            { lat: 51.0447, lng: -114.0719, name: "Calgary, Canadá" },
            { lat: 53.5461, lng: -113.4938, name: "Edmonton, Canadá" },
            { lat: 19.4326, lng: -99.1332, name: "Cidade do México, México" },
            { lat: 20.6597, lng: -103.3496, name: "Guadalajara, México" },
            { lat: 25.6866, lng: -100.3161, name: "Monterrey, México" },
            { lat: 21.1619, lng: -86.8515, name: "Cancún, México" },
            { lat: 16.8531, lng: -99.8237, name: "Acapulco, México" },
            
            // América do Sul (20 locais)
            { lat: -23.5505, lng: -46.6333, name: "São Paulo, Brasil" },
            { lat: -22.9068, lng: -43.1729, name: "Rio de Janeiro, Brasil" },
            { lat: -15.8267, lng: -47.9218, name: "Brasília, Brasil" },
            { lat: -12.9714, lng: -38.5014, name: "Salvador, Brasil" },
            { lat: -3.7327, lng: -38.5270, name: "Fortaleza, Brasil" },
            { lat: -8.0522, lng: -34.9286, name: "Recife, Brasil" },
            { lat: -25.4296, lng: -49.2719, name: "Curitiba, Brasil" },
            { lat: -30.0346, lng: -51.2177, name: "Porto Alegre, Brasil" },
            { lat: -19.9167, lng: -43.9345, name: "Belo Horizonte, Brasil" },
            { lat: -3.1019, lng: -60.0250, name: "Manaus, Brasil" },
            { lat: -34.6037, lng: -58.3816, name: "Buenos Aires, Argentina" },
            { lat: -31.4167, lng: -64.1833, name: "Córdoba, Argentina" },
            { lat: -24.7883, lng: -65.4106, name: "Salta, Argentina" },
            { lat: -51.6230, lng: -69.2168, name: "Río Gallegos, Argentina" },
            { lat: -33.4489, lng: -70.6693, name: "Santiago, Chile" },
            { lat: -36.8269, lng: -73.0503, name: "Concepción, Chile" },
            { lat: -12.0464, lng: -77.0428, name: "Lima, Peru" },
            { lat: -16.4090, lng: -71.5375, name: "Arequipa, Peru" },
            { lat: 4.7110, lng: -74.0721, name: "Bogotá, Colômbia" },
            { lat: 6.2442, lng: -75.5812, name: "Medellín, Colômbia" },
            
            // Europa (20 locais)
            { lat: 51.5074, lng: -0.1278, name: "Londres, Reino Unido" },
            { lat: 53.3498, lng: -6.2603, name: "Dublin, Irlanda" },
            { lat: 48.8566, lng: 2.3522, name: "Paris, França" },
            { lat: 43.2965, lng: 5.3698, name: "Marselha, França" },
            { lat: 52.5200, lng: 13.4050, name: "Berlim, Alemanha" },
            { lat: 48.1351, lng: 11.5820, name: "Munique, Alemanha" },
            { lat: 41.9028, lng: 12.4964, name: "Roma, Itália" },
            { lat: 45.4642, lng: 9.1900, name: "Milão, Itália" },
            { lat: 40.4168, lng: -3.7038, name: "Madri, Espanha" },
            { lat: 41.3851, lng: 2.1734, name: "Barcelona, Espanha" },
            { lat: 59.3293, lng: 18.0686, name: "Estocolmo, Suécia" },
            { lat: 55.6761, lng: 12.5683, name: "Copenhague, Dinamarca" },
            { lat: 60.1699, lng: 24.9384, name: "Helsinque, Finlândia" },
            { lat: 59.9139, lng: 10.7522, name: "Oslo, Noruega" },
            { lat: 52.2297, lng: 21.0122, name: "Varsóvia, Polônia" },
            { lat: 50.0755, lng: 14.4378, name: "Praga, República Tcheca" },
            { lat: 47.4979, lng: 19.0402, name: "Budapeste, Hungria" },
            { lat: 48.2082, lng: 16.3738, name: "Viena, Áustria" },
            { lat: 50.8503, lng: 4.3517, name: "Bruxelas, Bélgica" },
            { lat: 52.3702, lng: 4.8952, name: "Amsterdã, Holanda" },
            
            // Ásia (20 locais)
            { lat: 35.6762, lng: 139.6503, name: "Tóquio, Japão" },
            { lat: 34.6937, lng: 135.5023, name: "Osaka, Japão" },
            { lat: 37.5665, lng: 126.9780, name: "Seul, Coreia do Sul" },
            { lat: 35.1796, lng: 129.0756, name: "Busan, Coreia do Sul" },
            { lat: 39.9042, lng: 116.4074, name: "Pequim, China" },
            { lat: 31.2304, lng: 121.4737, name: "Xangai, China" },
            { lat: 22.3193, lng: 114.1694, name: "Hong Kong, China" },
            { lat: 25.0330, lng: 121.5654, name: "Taipei, Taiwan" },
            { lat: 1.3521, lng: 103.8198, name: "Singapura" },
            { lat: 3.1390, lng: 101.6869, name: "Kuala Lumpur, Malásia" },
            { lat: 13.7563, lng: 100.5018, name: "Bangkok, Tailândia" },
            { lat: 10.8231, lng: 106.6297, name: "Ho Chi Minh, Vietnã" },
            { lat: 21.0278, lng: 105.8342, name: "Hanói, Vietnã" },
            { lat: 14.5995, lng: 120.9842, name: "Manila, Filipinas" },
            { lat: 6.9271, lng: 79.8612, name: "Colombo, Sri Lanka" },
            { lat: 28.6139, lng: 77.2090, name: "Nova Delhi, Índia" },
            { lat: 19.0760, lng: 72.8777, name: "Mumbai, Índia" },
            { lat: 12.9716, lng: 77.5946, name: "Bangalore, Índia" },
            { lat: 24.7136, lng: 46.6753, name: "Riyadh, Arábia Saudita" },
            { lat: 25.2048, lng: 55.2708, name: "Dubai, Emirados Árabes" },
            
            // África (10 locais)
            { lat: -26.2041, lng: 28.0473, name: "Joanesburgo, África do Sul" },
            { lat: -33.9249, lng: 18.4241, name: "Cidade do Cabo, África do Sul" },
            { lat: 30.0444, lng: 31.2357, name: "Cairo, Egito" },
            { lat: 31.2001, lng: 29.9187, name: "Alexandria, Egito" },
            { lat: 6.5244, lng: 3.3792, name: "Lagos, Nigéria" },
            { lat: 9.0579, lng: 7.4951, name: "Abuja, Nigéria" },
            { lat: -1.2921, lng: 36.8219, name: "Nairóbi, Quênia" },
            { lat: -15.3875, lng: 28.3228, name: "Lusaka, Zâmbia" },
            { lat: -18.6657, lng: 35.5296, name: "Beira, Moçambique" },
            { lat: 12.6392, lng: -8.0029, name: "Bamako, Mali" },
            
            // Oceania (5 locais)
            { lat: -33.8688, lng: 151.2093, name: "Sydney, Austrália" },
            { lat: -37.8136, lng: 144.9631, name: "Melbourne, Austrália" },
            { lat: -27.4698, lng: 153.0251, name: "Brisbane, Austrália" },
            { lat: -36.8485, lng: 174.7633, name: "Auckland, Nova Zelândia" },
            { lat: -17.7333, lng: 168.3167, name: "Port Vila, Vanuatu" },
            
            // Outros locais interessantes (5 locais)
            { lat: 64.1466, lng: -21.9426, name: "Reykjavik, Islândia" },
            { lat: 21.3069, lng: -157.8583, name: "Honolulu, Havaí" },
            { lat: -13.1631, lng: -72.5450, name: "Machu Picchu, Peru" },
            { lat: 27.1751, lng: 78.0421, name: "Taj Mahal, Índia" },
            { lat: 41.8902, lng: 12.4922, name: "Coliseu, Itália" }
        ];

        // Função para selecionar um fallback aleatório
        function getRandomFallback() {
            return WORLDWIDE_FALLBACKS[Math.floor(Math.random() * WORLDWIDE_FALLBACKS.length)];
        }

        // Funções auxiliares
        async function getCityName(lat, lng) {
            try {
                const geocoder = new google.maps.Geocoder();
                const response = await new Promise((resolve, reject) => {
                    geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            resolve(results[0]);
                        } else {
                            reject(status);
                        }
                    });
                });
                
                for (const component of response.address_components) {
                    if (component.types.includes('locality')) {
                        return component.long_name;
                    }
                }
                return "Local desconhecido";
            } catch (error) {
                console.error("Erro ao obter nome da cidade:", error);
                return "Local desconhecido";
            }
        }

        async function getPanoramaData(lat, lng) {
            const streetViewService = new google.maps.StreetViewService();
            return new Promise((resolve, reject) => {
                streetViewService.getPanorama({
                    location: new google.maps.LatLng(lat, lng),
                    radius: 100000,
                    source: google.maps.StreetViewSource.DEFAULT
                }, (data, status) => {
                    if (status === 'OK') resolve(data);
                    else reject(status);
                });
            });
        }

        async function getRandomLocation() {
            const maxAttempts = 10;
            let attempts = 0;
            
            while (attempts < maxAttempts) {
                // Gera coordenadas mais prováveis de ter Street View
                const lat = Math.random() * 160 - 80; // Entre 80°S e 80°N
                const lng = Math.random() * 360 - 180;
                
                try {
                    const panoramaData = await getPanoramaData(lat, lng);
                    const cityName = await getCityName(
                        panoramaData.location.latLng.lat(),
                        panoramaData.location.latLng.lng()
                    );
                    
                    return {
                        lat: panoramaData.location.latLng.lat(),
                        lng: panoramaData.location.latLng.lng(),
                        name: cityName
                    };
                } catch (error) {
                    attempts++;
                }
            }
            
            // Se não encontrar após 10 tentativas, usa um dos 100 fallbacks
            return getRandomFallback();
        }

        // Funções principais
        async function initMap() {
            // Configura o mapa
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 0, lng: 0 },
                zoom: 2,
                streetViewControl: false,
                gestureHandling: "greedy",
                fullscreenControl: false,
                mapTypeControl: false,
                zoomControl: false,
            });

            // Configura os event listeners
            document.getElementById('mapToggle').addEventListener('click', toggleMap);
            document.getElementById('confirmBtn').addEventListener('click', confirmGuess);
            map.addListener("click", function(event) {
                placeMarker(event.latLng);
            });

            // Inicia o jogo
            await newRound();
        }

        function toggleMap() {
            const mapContainer = document.getElementById('map-container');
            const streetViewContainer = document.getElementById('street-view-container');
            
            if (mapContainer.classList.contains('show')) {
                // Esconde o mapa e mostra Street View
                mapContainer.classList.remove('show');
                streetViewContainer.style.display = 'block';
            } else {
                // Esconde Street View e mostra o mapa
                streetViewContainer.style.display = 'none';
                mapContainer.classList.add('show');
                
                // Centraliza o mapa se houver marcador
                if (marker) {
                    map.setCenter(marker.getPosition());
                }
            }
        }

        async function newRound() {
            // Garante que o mapa esteja fechado e Street View visível
            document.getElementById('map-container').classList.remove('show');
            document.getElementById('street-view-container').style.display = 'block';

            if (roundsPlayed >= maxRounds) {
                endGame();
                return;
            }
            
            // Obtém novo local
            correctLocation = await getRandomLocation();
            currentGameLocations.push({
                lat: correctLocation.lat,
                lng: correctLocation.lng,
                name: correctLocation.name
            });
            
            // Configura o Street View
            panorama = new google.maps.StreetViewPanorama(
                document.getElementById("street-view"), {
                    position: correctLocation,
                    pov: { heading: Math.random() * 360, pitch: 0 },
                    zoom: 1,
                    disableDefaultUI: true,
                    showRoadLabels: false,
                    zoomControlOptions: {
                        position: google.maps.ControlPosition.TOP_LEFT,
                    },
                }
            );
            
            // Remove marcador anterior
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
                    text: 'Por favor, marque um local no mapa!',
                    icon: 'warning'
                });
                return;
            }

            const guessedLocation = marker.getPosition();
            const distance = google.maps.geometry.spherical.computeDistanceBetween(
                guessedLocation, new google.maps.LatLng(correctLocation.lat, correctLocation.lng)
            ) / 1000;
            
            const points = Math.max(0, 5000 - Math.floor(distance)) / 100;
            score += Math.round(points);
            document.getElementById("score").textContent = score;
            
            Swal.fire({
                title: distance < 1 ? 'Incrível! Quase exato!' : 
                      distance < 10 ? 'Muito bom!' : 
                      distance < 100 ? 'Boa tentativa!' : 'Tente novamente!',
                html: `
                    <div style="text-align: left;">
                        <p><b>Sua distância:</b> ${distance.toFixed(2)} km</p>
                        <p><b>Local correto:</b> ${correctLocation.name}</p>
                        <p><b>Pontos ganhos:</b> ${Math.round(points)}</p>
                    </div>
                `,
                icon: distance < 1 ? 'success' : 
                     distance < 10 ? 'success' : 
                     distance < 100 ? 'info' : 'error',
                confirmButtonText: 'Próximo Desafio'
            }).then(() => {
                newRound();
            });
        }

        function endGame() {
            Swal.fire({
                title: 'Jogo Finalizado!',
                html: `
                    <div style="text-align: left;">
                        <p>Sua pontuação final é: <b>${score}</b> pontos</p>
                        <p>Locais deste jogo:</p>
                        <ul>
                            ${currentGameLocations.map((loc, index) => 
                                `<li>Rodada ${index + 1}: ${loc.name}</li>`
                            ).join('')}
                        </ul>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Jogar Novamente'
            }).then(() => {
                resetGame();
            });
        }

        function resetGame() {
            score = 0;
            roundsPlayed = 0;
            gameSeed = Math.floor(Math.random() * 1000000);
            currentGameLocations = [];
            document.getElementById("score").textContent = score;
            newRound();
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3VgDsnZQDKV1w4TM-D19msn3TgOOMuzk&libraries=geometry,streetView&callback=initMap"></script>
</body>
</html>