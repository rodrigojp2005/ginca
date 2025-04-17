<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gincaneiros - Adivinhe o local</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-firestore.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-auth.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        .game-container {
            height: calc(100vh - 106px);
            position: relative;
            overflow: hidden;
        }
        
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
            top:50%;
            transform: translateY(-50%);
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
        
        .confirm-btn-container {
            position: absolute;
            bottom: 80px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            z-index: 950;
        }
        
        .confirm-btn {
            background-color: yellow;
            color:black;
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
        
        .app-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: var(--secondary-color);
            color: white;
            text-align: center;
            padding: 10px 0;
            z-index: 1000;
            border-top: 2px solid white;
        }
        
        .social-icons {
            margin-top: 5px;
        }
        
        .social-icons a {
            color: white;
            margin: 0 10px;
            font-size: 1.2rem;
            transition: color 0.3s;
        }
        
        .social-icons a:hover {
            color: var(--accent-color);
        }
        
        @media (max-width: 768px) {
            .map-toggle {
                width: 50px;
                height: 50px;
                right: 15px;
            }
            
            .confirm-btn {
                padding: 10px 25px;
                font-size: 16px;
            }
            
            .game-container {
                height: calc(100vh - 96px);
            }
        }
        
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
        
        .share-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }
        
        .share-btn {
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .whatsapp { background-color: #25D366; }
        .facebook { background-color: #3b5998; }
        .link { background-color: var(--secondary-color); }
        
        .custom-marker {
            position: absolute;
            width: 50px;
            height: 50px;
            z-index: 999;
            pointer-events: none;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body class="bg-gray-100 pt-16">
    <nav class="bg-white shadow-md fixed top-0 left-0 w-full z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                <img src="https://gincaneiros.com/images/gincaneiros_logo.png" class="mr-3 w-10 h-10 sm:h-9" alt="Gincaneiros Logo">
                    <a href="/" class="text-xl font-bold">Gincaneiros</a>
                    <div class="score-display ml-4">
                        <i class="fas fa-trophy me-1"></i> <span id="score">0</span>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    </div>
                </div>
                <div class="flex items-center">
                    <span id="user-info" class="text-gray-500 mr-4"></span>
                    <button id="logout-button" onclick="logout()" class="text-gray-500 hover:text-gray-700" style="display: none;">
                        Logout
                    </button>
                    <button id="mobile-menu-button" class="sm:hidden p-2 text-red-700 hover:text-red-800">
                        <img src="https://media.tenor.com/MG0VpR0F0-sAAAAi/icon-cute.gif" alt="help" style="width: 40px; height: 40px;">
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="sm:hidden hidden text-right pr-4">
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
    </nav>

    <div class="game-container">
        <div id="street-view-container">
            <div id="street-view"></div>
            <div id="street-view-marker" class="custom-marker" style="display: none;">
                <img src="https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExcnc3a3lvcHFrN2ZwZTV2bnJzb3ZrYWJjeTl6ZXB4YzE0N3NkMHU3MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/A5PYmtufdQIjD37IC0/giphy.gif" alt="Marker" style="width: 100%; height: 100%;">
            </div>
            
            <button class="map-toggle" id="mapToggle">
                <img src="https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExbHdyenVlcDR4cXFydHVxb2Q1bW5ibmh1aHplcGFubzRvdWwyYmQydCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/PjTRytCVV9ROhgtEyI/giphy.gif" alt="Mapa" class="map-icon">
            </button>
        </div>
        
        <div id="map-container">
            <div id="map"></div>
            
            <div class="confirm-btn-container">
                <button class="confirm-btn" id="confirmBtn">
                    <i class="fas fa-check-circle"></i> Confirmar Palpite
                </button>
            </div>
        </div>
    </div>

    <footer class="app-footer">
        <div class="container">
            <div class="social-icons">
                © 2025 Gincaneiros:
                <a href="https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}&quote=${encodedText}" id="share-facebook"><i class="fab fa-facebook"></i></a>
                <a href="#" id="share-instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://wa.me/?text=${encodedText}%20${encodedUrl}" id="share-whatsapp"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </footer>

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
                    <p>3. Clique no botão de localização na direita</p>
                    <p>4. Marque seu palpite no mapa e confirme</p>
                    <p>5. Quanto mais perto do local real, mais pontos você ganha!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendi</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="aboutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Sobre o Jogo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Gincaneiros é um jogo de adivinhação de locais aleatórios.</p>
                    <p>Cada rodada mostra uma localização no mundo através do Google Street View.</p>
                    <p>Sua missão é marcar no mapa onde você acha que essa localização está e desafiar algum amigo/familiar.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

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
                    <p><i class="fas fa-envelope me-2"></i> 53 981056952</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        let brazilLocationsUsed = 0;
        
        // Lista atualizada com locais turísticos e urbanos
        const LOCATIONS = [
            // Brasil (locais turísticos e urbanos)
            { lat: -23.5505, lng: -46.6333, name: "Avenida Paulista, São Paulo, Brasil", tourist: true },
            { lat: -22.9111, lng: -43.2054, name: "Praia de Copacabana, Rio de Janeiro, Brasil", tourist: true },
            { lat: -15.7942, lng: -47.8825, name: "Eixo Monumental, Brasília, Brasil", tourist: true },
            { lat: -12.9722, lng: -38.5014, name: "Pelourinho, Salvador, Brasil", tourist: true },
            { lat: -3.7319, lng: -38.5267, name: "Praça do Ferreira, Fortaleza, Brasil", tourist: true },
            { lat: -8.0631, lng: -34.8711, name: "Marco Zero, Recife, Brasil", tourist: true },
            { lat: -25.4296, lng: -49.2719, name: "Rua das Flores, Curitiba, Brasil", tourist: true },
            { lat: -30.0346, lng: -51.2177, name: "Mercado Público, Porto Alegre, Brasil", tourist: true },
            { lat: -23.0056, lng: -43.4384, name: "Praia de Ipanema, Rio de Janeiro, Brasil", tourist: true },
            { lat: -22.9519, lng: -43.2105, name: "Cristo Redentor, Rio de Janeiro, Brasil", tourist: true },
            
            // Internacionais (locais turísticos famosos)
            { lat: 40.7128, lng: -74.0060, name: "Times Square, Nova York, EUA", tourist: true },
            { lat: 48.8566, lng: 2.3522, name: "Torre Eiffel, Paris, França", tourist: true },
            { lat: 35.6762, lng: 139.6503, name: "Shibuya Crossing, Tóquio, Japão", tourist: true },
            { lat: 51.5074, lng: -0.1278, name: "Trafalgar Square, Londres, Reino Unido", tourist: true },
            { lat: 41.9028, lng: 12.4964, name: "Coliseu, Roma, Itália", tourist: true },
            { lat: 52.5200, lng: 13.4050, name: "Portão de Brandemburgo, Berlim, Alemanha", tourist: true },
            { lat: 40.4168, lng: -3.7038, name: "Puerta del Sol, Madri, Espanha", tourist: true },
            { lat: 41.3851, lng: 2.1734, name: "Las Ramblas, Barcelona, Espanha", tourist: true },
            { lat: 37.7749, lng: -122.4194, name: "Golden Gate Bridge, São Francisco, EUA", tourist: true },
            { lat: 34.1341, lng: -118.3215, name: "Hollywood Walk of Fame, Los Angeles, EUA", tourist: true },
            
            // Locais urbanos adicionais (não necessariamente turísticos)
            { lat: -23.5337, lng: -46.6253, name: "Centro de São Paulo, Brasil" },
            { lat: -22.9068, lng: -43.1729, name: "Centro do Rio de Janeiro, Brasil" },
            { lat: -15.7934, lng: -47.8822, name: "Setor Comercial Sul, Brasília, Brasil" },
            { lat: -12.9777, lng: -38.5016, name: "Comércio, Salvador, Brasil" },
            { lat: 40.7306, lng: -73.9352, name: "Manhattan, Nova York, EUA" },
            { lat: 48.8567, lng: 2.3515, name: "Centro de Paris, França" },
            { lat: 35.6828, lng: 139.7594, name: "Centro de Tóquio, Japão" },
            { lat: 51.5156, lng: -0.1181, name: "Centro de Londres, Reino Unido" }
        ];

        setTimeout(showJohnHelpAlert, 1500);

        function showJohnHelpAlert() {
            Swal.fire({
                title: "Onde Estou?",
                text: "Ajude Jhon encontrar-se no mapa.",
                imageUrl: "https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExcnc3a3lvcHFrN2ZwZTV2bnJzb3ZrYWJjeTl6ZXB4YzE0N3NkMHU3MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/A5PYmtufdQIjD37IC0/giphy.gif",
                imageWidth: 300,
                imageHeight: 150,
                imageAlt: "Cadê Jhon?",
                confirmButtonText: "ok. Vamos tentar",
                confirmButtonColor: "#007bff",
            });
        }

        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        function betterLatitudeDistribution() {
            const r = Math.random();
            // Prioriza latitudes onde estão as maiores cidades do mundo
            if (r < 0.7) return Math.random() * 50 - 20;  // Entre -20° e +30° (maioria das grandes cidades)
            if (r < 0.9) return Math.random() * 30 - 50;  // Algumas cidades do sul
            return Math.random() * 170 - 85;              // Raro
        }

        async function getPanoramaData(lat, lng, radius = 50000) {
            const streetViewService = new google.maps.StreetViewService();
            return new Promise((resolve, reject) => {
                streetViewService.getPanorama({
                    location: new google.maps.LatLng(lat, lng),
                    radius: radius,
                    source: google.maps.StreetViewSource.OUTDOOR,
                    preference: google.maps.StreetViewPreference.BEST
                }, (data, status) => {
                    if (status === 'OK') {
                        // Filtro rigoroso para áreas urbanas
                        const isUrban = data.location && 
                                       data.location.description && 
                                       (data.location.description.includes("Av.") ||
                                        data.location.description.includes("Rua") ||
                                        data.location.description.includes("Avenida") ||
                                        data.location.description.includes("Praça") ||
                                        data.location.description.includes("Centro") ||
                                        data.location.description.includes(","));
                        
                        if (isUrban) {
                            resolve(data);
                        } else {
                            reject("Área não urbana");
                        }
                    } else {
                        reject(status);
                    }
                });
            });
        }

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
                
                let city = "";
                let country = "";
                let address = response.formatted_address || "";
                
                for (const component of response.address_components) {
                    if (component.types.includes('locality')) {
                        city = component.long_name;
                    }
                    if (component.types.includes('country')) {
                        country = component.long_name;
                    }
                }
                
                if (city && country) {
                    return `${city}, ${country}`;
                } else if (country) {
                    return country;
                } else if (address) {
                    // Se não encontrar cidade, retorna os primeiros elementos do endereço
                    return address.split(",").slice(0, 2).join(",").trim();
                } else {
                    // Se não conseguir nenhuma informação, retorna as coordenadas
                    return `Local (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                }
            } catch (error) {
                console.error("Erro ao obter nome do local:", error);
                return `Local (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
            }
        }

        async function getRandomLocation() {
            const maxAttempts = 15;
            let attempts = 0;
            
            // Garante que pelo menos um local turístico seja incluído no jogo
            const needsTouristLocation = !currentGameLocations.some(loc => 
                LOCATIONS.some(touristLoc => 
                    touristLoc.tourist && 
                    touristLoc.lat === loc.lat && 
                    touristLoc.lng === loc.lng
                )
            ) && roundsPlayed < (maxRounds - 1);
            
            const needsBrazilLocation = brazilLocationsUsed < 2 && roundsPlayed < (maxRounds - 2);
            
            // Se precisamos de um local turístico, pegue um aleatório da lista
            if (needsTouristLocation) {
                const touristLocations = LOCATIONS.filter(loc => loc.tourist);
                if (touristLocations.length > 0) {
                    const selected = touristLocations[Math.floor(Math.random() * touristLocations.length)];
                    
                    if (selected.name.includes("Brasil")) {
                        brazilLocationsUsed++;
                    }
                    
                    return {
                        lat: selected.lat,
                        lng: selected.lng,
                        name: selected.name
                    };
                }
            }
            
            while (attempts < maxAttempts) {
                let lat, lng;
                
                if (needsBrazilLocation) {
                    // Coordenadas de centros urbanos brasileiros
                    lat = -15 + (Math.random() * 20);
                    lng = -50 + (Math.random() * 30);
                } else {
                    lat = betterLatitudeDistribution();
                    lng = Math.random() * 360 - 180;
                }
                
                try {
                    const panoramaData = await getPanoramaData(lat, lng);
                    const locationName = await getCityName(
                        panoramaData.location.latLng.lat(),
                        panoramaData.location.latLng.lng()
                    );
                    
                    if (locationName.includes("Brasil")) {
                        brazilLocationsUsed++;
                    }
                    
                    return {
                        lat: panoramaData.location.latLng.lat(),
                        lng: panoramaData.location.latLng.lng(),
                        name: locationName
                    };
                } catch (error) {
                    attempts++;
                    await new Promise(resolve => setTimeout(resolve, 100));
                }
            }
            
            // Fallback para locais da lista pré-definida
            const filteredLocations = needsBrazilLocation 
                ? LOCATIONS.filter(loc => loc.name.includes("Brasil"))
                : LOCATIONS;
                
            if (filteredLocations.length > 0) {
                const selected = filteredLocations[Math.floor(Math.random() * filteredLocations.length)];
                
                if (selected.name.includes("Brasil")) {
                    brazilLocationsUsed++;
                }
                
                return {
                    lat: selected.lat,
                    lng: selected.lng,
                    name: selected.name
                };
            }
            
            // Último fallback - coordenadas aleatórias
            return {
                lat: betterLatitudeDistribution(),
                lng: Math.random() * 360 - 180,
                name: `Local (${betterLatitudeDistribution().toFixed(4)}, ${(Math.random() * 360 - 180).toFixed(4)})`
            };
        }

        function shareResult() {
            const shareText = `Acabei de jogar Gincaneiros e marquei ${score} pontos! Tente bater meu recorde! 🌍`;
            const shareUrl = window.location.href;
            
            if (navigator.share) {
                navigator.share({
                    title: 'Gincaneiros',
                    text: shareText,
                    url: shareUrl
                }).catch(err => {
                    console.log('Erro ao compartilhar:', err);
                    showShareFallback(shareText, shareUrl);
                });
            } else {
                showShareFallback(shareText, shareUrl);
            }
        }
        
        function showShareFallback(text, url) {
            const encodedText = encodeURIComponent(text);
            const encodedUrl = encodeURIComponent(url);
            
            Swal.fire({
                title: 'Compartilhar Resultado',
                html: `
                    <p>Compartilhe sua pontuação com os amigos:</p>
                    <div class="share-buttons">
                        <a href="https://wa.me/?text=${encodedText}%20${encodedUrl}" class="share-btn whatsapp" target="_blank">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}&quote=${encodedText}" class="share-btn facebook" target="_blank">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="#" onclick="navigator.clipboard.writeText('${text} ' + '${url}'); Swal.fire('Link copiado!', 'Cole em qualquer lugar para compartilhar.', 'success'); return false;" class="share-btn link">
                            <i class="fas fa-link"></i> Copiar Link
                        </a>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true
            });
        }

        function positionMarkerInStreetView() {
            const markerElement = document.getElementById('street-view-marker');
            markerElement.style.display = 'none';
            
            if (window.streetViewMarker) {
                window.streetViewMarker.setMap(null);
            }
            
            const panoramaPosition = panorama.getPosition();
            const pov = panorama.getPov();

            if (!panoramaPosition || !pov) {
                console.error("Não foi possível obter a posição ou POV do panorama para o marcador.");
                return;
            }
                
            const heading = pov.heading;
            const distance = 15;

            const newPosition = google.maps.geometry.spherical.computeOffset(
                panoramaPosition,
                distance,
                heading
            );

            window.streetViewMarker = new google.maps.Marker({
                position: newPosition,
                map: panorama,
                icon: {
                    url: 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExcnc3a3lvcHFrN2ZwZTV2bnJzb3ZrYWJjeTl6ZXB4YzE0N7NkMHU3MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/A5PYmtufdQIjD37IC0/giphy.gif',
                    scaledSize: new google.maps.Size(80, 120),
                    anchor: new google.maps.Point(40, 120)
                },
                title: "Ponto de Interesse"
            });
        }

        async function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 0, lng: 0 },
                zoom: 2,
                disableDefaultUI: true,
                streetViewControl: false,
                gestureHandling: "greedy",
                fullscreenControl: false,
                mapTypeControl: false,
                zoomControl: false,
            });

            document.getElementById('mapToggle').addEventListener('click', toggleMap);
            document.getElementById('confirmBtn').addEventListener('click', confirmGuess);
            map.addListener("click", function(event) {
                placeMarker(event.latLng);
            });

            document.getElementById('share-facebook').addEventListener('click', function(e) {
                e.preventDefault();
                const url = encodeURIComponent(window.location.href);
                window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
            });
            
            document.getElementById('share-instagram').addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Compartilhar no Instagram',
                    text: 'Para compartilhar no Instagram, você pode postar um story com o link do jogo!',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            });
            
            document.getElementById('share-whatsapp').addEventListener('click', function(e) {
                e.preventDefault();
                const text = encodeURIComponent("Estou jogando Gincaneiros, um jogo incrível de geolocalização! Tente bater meu recorde: ");
                const url = encodeURIComponent(window.location.href);
                window.open(`https://wa.me/?text=${text}${url}`, '_blank');
            });

            await newRound();
        }

        function toggleMap() {
            const mapContainer = document.getElementById('map-container');
            const streetViewContainer = document.getElementById('street-view-container');
            
            if (mapContainer.classList.contains('show')) {
                mapContainer.classList.remove('show');
                streetViewContainer.style.display = 'block';
            } else {
                streetViewContainer.style.display = 'none';
                mapContainer.classList.add('show');
                
                if (marker) {
                    map.setCenter(marker.getPosition());
                }
            }
        }

        async function newRound() {
            document.getElementById('map-container').classList.remove('show');
            document.getElementById('street-view-container').style.display = 'block';

            if (roundsPlayed >= maxRounds) {
                endGame();
                return;
            }
            
            correctLocation = await getRandomLocation();
            currentGameLocations.push({
                lat: correctLocation.lat,
                lng: correctLocation.lng,
                name: correctLocation.name
            });
            
            panorama = new google.maps.StreetViewPanorama(
                document.getElementById("street-view"), {
                    position: correctLocation,
                    pov: { heading: Math.random() * 360, pitch: 5 },
                    zoom: 1,
                    disableDefaultUI: true,
                    showRoadLabels: false,
                    zoomControlOptions: {
                        position: google.maps.ControlPosition.TOP_LEFT,
                    },
                }
            );

            positionMarkerInStreetView();

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
            
            const locationParts = correctLocation.name.split(', ');
            const city = locationParts[0];
            const country = locationParts.length > 1 ? locationParts[locationParts.length - 1] : correctLocation.name;
            
            Swal.fire({
                title: distance < 10 ? 'Incrível! Quase exato!' : 
                      distance < 100 ? 'Muito bom!' : 
                      distance < 500 ? 'Boa tentativa!' : 'Tente novamente!',
                html: `
                    <div style="text-align: left;">
                        <p><b>Rodada:</b> ${roundsPlayed}/${maxRounds}</p>
                        <p><b>Sua distância:</b> ${Math.ceil(distance)} km</p>
                        <p><b>Local correto:</b> ${city}, ${country}</p>
                        <p><b>Pontos ganhos:</b> ${Math.round(points)}</p>
                    </div>
                `,
                icon: distance < 100 ? 'success' : 
                     distance < 1000 ? 'success' : 
                     distance < 10000 ? 'info' : 'error',
                confirmButtonText: 'Próximo Local',
                confirmButtonColor: '#007bff'
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
                showCancelButton: true,
                confirmButtonText: 'Jogar Novamente',
                cancelButtonText: 'Compartilhar',
                footer: '<p class="text-muted">Compartilhe sua pontuação com os amigos!</p>'
            }).then((result) => {
                if (result.isConfirmed) {
                    resetGame();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    shareResult();
                    resetGame();
                }
            });
        }

        function resetGame() {
            score = 0;
            roundsPlayed = 0;
            brazilLocationsUsed = 0;
            gameSeed = Math.floor(Math.random() * 1000000);
            currentGameLocations = [];
            document.getElementById("score").textContent = score;
            newRound();
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3VgDsnZQDKV1w4TM-D19msn3TgOOMuzk&libraries=geometry,streetView&callback=initMap"></script>
</body>
</html>