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

        .game-mode-selector {
            position: fixed;
            top: 70px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 999; /* Alterado para 999 para ficar abaixo do menu móvel */
            display: flex;
            gap: 10px;
            background-color: white;
            padding: 8px 15px;
            border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .game-mode-btn {
            padding: 5px 15px;
            border-radius: 20px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap; /* Evita quebra de linha */
        }

        .game-mode-btn.active {
            background-color: var(--primary-color);
            color: white;
        }

        /* Ajuste para o menu móvel */
        #mobile-menu {
            z-index: 1000; /* Garante que fique acima do seletor de modo */
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-top: 10px;
        }

        #stateMenu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 1001;
    max-height: 300px;
    overflow-y: auto;
    width: 200px;
}

#stateMenu div {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

#stateMenu div:hover {
    background: #f5f5f5;
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

    <div class="game-mode-selector">
        <!-- <button class="game-mode-btn active" id="stateMode" onclick="setGameMode('state')">Estado</button> -->
        <button class="game-mode-btn active" id="stateMode" onclick="showStateMenu()">Estado</button>
        <button class="game-mode-btn" id="brazilMode" onclick="setGameMode('brazil')">Brasil</button>
        <button class="game-mode-btn" id="worldMode" onclick="setGameMode('world')">Mundo</button>
    </div>

    <div class="game-container">
        <div id="street-view-container">
            <div id="street-view"></div>
            <div id="street-view-marker" class="custom-marker" style="display: none;">
                <img src="https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExcnc3a3lvcHFrN2ZwZTV2bnJzb3ZrYWJjeTl6ZXB4YzE0N3NkMHU3MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/A5PYmtufdQIjD37IC0/giphy.gif" alt="Marker" style="width: 100%; height: 100%;">
            </div>
            
            <button class="map-toggle" id="mapToggle">
                <img src="https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExbHdyenVlcDR4cXFydHVxb2Q1bW5ibmh1aHplcGFubzRvdWwyYmQydCZlcD12MV9pbnRlrm5hbF9naWZfYnlfaWQmY3Q9cw/PjTRytCVV9ROhgtEyI/giphy.gif" alt="Mapa" class="map-icon">
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
                    <p>3. Clique no botão de localização à direita</p>
                    <p>4. Marque seu palpite no mapa e confirme</p>
                    <p>5. Quanto mais perto do local real, mais pontos você ganha!</p>
                    <p>6. Escolha entre os modos: Estado, Brasil ou Mundo</p>
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
                    <p>1. Gincaneiros é um jogo para quem gosta de descobrir locais aleatórios.</p>
                    <p>2. Cada rodada mostra uma localização no mundo através do Google Street View.</p>
                    <p>3. Seu objetivo é marcar no mapa onde você acha que essa localização está. Depois, desafiar seus amigos/familiares.</p>
                    <p>4. Escolha entre três modos de jogo: Estado (focado no seu estado), Brasil ou Mundo.</p>
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
                    <p><i class="fas fa-envelope me-2"></i> 53 98105.6952</p>
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
        let usedLocations = []; // Cache de locais já usados
        let lastLocationRegion = null; // Para garantir diversidade geográfica
        let gameMode = 'state'; // Padrão: estado
        let userState = null; // Estado do usuário será detectado
        let stateBounds = null; // Limites do estado do usuário

        setTimeout(showJohnHelpAlert, 1500);

        function showJohnHelpAlert() {
            Swal.fire({
                title: "Onde Estou?",
                text: "Ajude Jhon a encontrar-se no mapa.",
                imageUrl: "https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExcnc3a3lvcHFrN2ZwZTV2bnJzb3ZrYWJjeTl6ZXB4YzE0N3NkMHU3MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/A5PYmtufdQIjD37IC0/giphy.gif",
                imageWidth: 300,
                imageHeight: 150,
                imageAlt: "Cadê Jhon?",
                confirmButtonText: "Ok, vou tentar!",
                confirmButtonColor: "#007bff",
            });
        }

        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        function setGameMode(mode) {
            gameMode = mode;
            
            // Atualiza a aparência dos botões
            document.getElementById('stateMode').classList.remove('active');
            document.getElementById('brazilMode').classList.remove('active');
            document.getElementById('worldMode').classList.remove('active');
            document.getElementById(mode + 'Mode').classList.add('active');
            
            // Reinicia o jogo com o novo modo
            resetGame();
        }

            // Lista de todos os estados brasileiros
            const brazilianStates = [
                {name: "Acre", code: "AC"},
                {name: "Alagoas", code: "AL"},
                {name: "Amapá", code: "AP"},
                {name: "Amazonas", code: "AM"},
                {name: "Bahia", code: "BA"},
                {name: "Ceará", code: "CE"},
                {name: "Distrito Federal", code: "DF"},
                {name: "Espírito Santo", code: "ES"},
                {name: "Goiás", code: "GO"},
                {name: "Maranhão", code: "MA"},
                {name: "Mato Grosso", code: "MT"},
                {name: "Mato Grosso do Sul", code: "MS"},
                {name: "Minas Gerais", code: "MG"},
                {name: "Pará", code: "PA"},
                {name: "Paraíba", code: "PB"},
                {name: "Paraná", code: "PR"},
                {name: "Pernambuco", code: "PE"},
                {name: "Piauí", code: "PI"},
                {name: "Rio de Janeiro", code: "RJ"},
                {name: "Rio Grande do Norte", code: "RN"},
                {name: "Rio Grande do Sul", code: "RS"},
                {name: "Rondônia", code: "RO"},
                {name: "Roraima", code: "RR"},
                {name: "Santa Catarina", code: "SC"},
                {name: "São Paulo", code: "SP"},
                {name: "Sergipe", code: "SE"},
                {name: "Tocantins", code: "TO"}
            ];

            // Função para mostrar o menu de estados
            function showStateMenu() {
                const menuHtml = `
                    <div id="stateMenu" style="position: absolute; top: 100%; left: 0; background: white; 
                        border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                        z-index: 1001; max-height: 300px; overflow-y: auto; width: 200px;">
                        ${brazilianStates.map(state => `
                            <div style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;
                                &:hover { background: #f5f5f5; }"
                                onclick="selectState('${state.name}', '${state.code}')">
                                ${state.name}
                            </div>
                        `).join('')}
                    </div>
                `;
                
                // Remove menu existente se houver
                const existingMenu = document.getElementById('stateMenu');
                if (existingMenu) existingMenu.remove();
                
                // Adiciona novo menu
                const stateButton = document.getElementById('stateMode');
                stateButton.insertAdjacentHTML('afterend', menuHtml);
                
                // Fecha o menu ao clicar fora
                setTimeout(() => {
                    document.addEventListener('click', closeStateMenu);
                }, 100);
            }
            
            function closeStateMenu(e) {
                const stateMenu = document.getElementById('stateMenu');
                const stateButton = document.getElementById('stateMode');
                
                if (stateMenu && e.target !== stateButton && !stateButton.contains(e.target)) {
                    stateMenu.remove();
                    document.removeEventListener('click', closeStateMenu);
                }
            }
            
            function selectState(stateName, stateCode) {
                userState = stateName;
                document.getElementById('stateMode').textContent = stateName;
                document.getElementById('stateMenu').remove();
                
                // Define os limites aproximados do estado (simplificado)
                // Na prática, você precisaria de coordenadas exatas para cada estado
                setStateBounds(stateCode);
                
                // Reinicia o jogo com o novo estado
                resetGame();
            }
            
            // Função simplificada para definir os limites do estado
            function setStateBounds(stateCode) {
                // Centraliza no estado com um raio de ~100km
                const stateCenters = {
                    'AC': {lat: -9.11, lng: -70.52},
                    'AL': {lat: -9.57, lng: -36.55},
                    // Adicione coordenadas aproximadas para todos os estados...
                    'SP': {lat: -23.55, lng: -46.63} // Exemplo: São Paulo
                };
                
                const center = stateCenters[stateCode] || {lat: -15.78, lng: -47.93}; // Fallback: Brasília
                stateBounds = new google.maps.LatLngBounds(
                    new google.maps.LatLng(center.lat - 1, center.lng - 1), // ~100km
                    new google.maps.LatLng(center.lat + 1, center.lng + 1)
                );
            }

         // Modificação da função detectUserLocation para usar raio de 100km
            async function detectUserLocation() {
                return new Promise((resolve, reject) => {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            async (position) => {
                                const lat = position.coords.latitude;
                                const lng = position.coords.longitude;
                                
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
                                    
                                    // Encontrar a cidade e estado
                                    let city, state;
                                    for (const component of response.address_components) {
                                        if (component.types.includes('locality')) {
                                            city = component.long_name;
                                        } else if (component.types.includes('administrative_area_level_1')) {
                                            state = component.long_name;
                                            userState = state;
                                        }
                                    }
                                    
                                    // Definir raio de 100km ao redor da cidade
                                    stateBounds = new google.maps.LatLngBounds();
                                    const radius = 100 / 111.32; // Graus aproximados para 100km
                                    stateBounds.extend(new google.maps.LatLng(lat - radius, lng - radius));
                                    stateBounds.extend(new google.maps.LatLng(lat + radius, lng + radius));
                                    
                                    resolve({ lat, lng, city, state });
                                } catch (error) {
                                    console.error("Erro ao geocodificar:", error);
                                    reject(error);
                                }
                            },
                            (error) => {
                                console.error("Erro ao obter localização:", error);
                                reject(error);
                            }
                        );
                    } else {
                        reject("Geolocalização não suportada");
                    }
                });
            }

        function getRandomStateLocation() {
            if (!stateBounds) {
                // Fallback para Brasil se não conseguir detectar o estado
                return getRandomBrazilLocation();
            }
            
            const ne = stateBounds.getNorthEast();
            const sw = stateBounds.getSouthWest();
            
            const lat = sw.lat() + Math.random() * (ne.lat() - sw.lat());
            const lng = sw.lng() + Math.random() * (ne.lng() - sw.lng());
            
            return { lat, lng };
        }

        function getRandomBrazilLocation() {
            // Gera coordenadas dentro do Brasil
            const lat = -33 + Math.random() * 26; // Entre -33° e -7°
            const lng = -74 + Math.random() * 39; // Entre -74° e -35°
            return { lat, lng };
        }

        function getRandomWorldLocation() {
            // Gera coordenadas em diferentes regiões do mundo
            const regions = [
                { latRange: [-35, 35], lngRange: [-180, -30] }, // Américas
                { latRange: [-35, 35], lngRange: [-30, 60] },   // África/Europa
                { latRange: [-35, 35], lngRange: [60, 180] },   // Ásia/Oceania
                { latRange: [35, 70], lngRange: [-10, 40] },    // Europa do Norte
                { latRange: [-60, -20], lngRange: [-80, -30] }  // Cone Sul
            ];
            
            // Evita repetir a mesma região consecutivamente
            let region;
            do {
                region = regions[Math.floor(Math.random() * regions.length)];
            } while (region === lastLocationRegion && Math.random() > 0.3);
            
            lastLocationRegion = region;
            
            const lat = region.latRange[0] + Math.random() * (region.latRange[1] - region.latRange[0]);
            const lng = region.lngRange[0] + Math.random() * (region.lngRange[1] - region.lngRange[0]);
            
            return { lat, lng };
        }

        function isLocationUsed(lat, lng) {
            // Verifica se o local já foi usado (com uma margem de erro)
            return usedLocations.some(loc => 
                Math.abs(loc.lat - lat) < 0.1 && 
                Math.abs(loc.lng - lng) < 0.1
            );
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
                        // Filtros rigorosos para garantir locais identificáveis
                        const isGoodLocation = data.location && 
                            data.location.description &&
                            !data.location.description.includes("Indoor") &&
                            !data.location.description.includes("Museum") &&
                            !data.location.description.includes("Shopping") &&
                            !data.location.description.includes("Airport") &&
                            !data.location.description.includes("Station") &&
                            (data.location.description.includes("Av.") ||
                             data.location.description.includes("Rua") ||
                             data.location.description.includes("Avenida") ||
                             data.location.description.includes("Praça") ||
                             data.location.description.includes("Street") ||
                             data.location.description.includes("Avenue") ||
                             data.location.description.includes("Road") ||
                             data.location.description.includes(","));
                        
                        if (isGoodLocation && !isLocationUsed(data.location.latLng.lat(), data.location.latLng.lng())) {
                            resolve(data);
                        } else {
                            reject(isGoodLocation ? "Local já usado" : "Área não adequada");
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
                let state = "";
                let address = response.formatted_address || "";
                
                // Extrai informações do endereço
                for (const component of response.address_components) {
                    if (component.types.includes('locality')) {
                        city = component.long_name;
                    } else if (component.types.includes('sublocality')) {
                        city = component.long_name;
                    } else if (component.types.includes('administrative_area_level_2')) {
                        if (!city) city = component.long_name;
                    } else if (component.types.includes('administrative_area_level_1')) {
                        state = component.long_name;
                    } else if (component.types.includes('country')) {
                        country = component.long_name;
                    }
                }
                
                // Tenta criar um nome significativo
                if (city && state && country) {
                    return `${city}, ${state}, ${country}`;
                } else if (city && state) {
                    return `${city}, ${state}`;
                } else if (city && country) {
                    return `${city}, ${country}`;
                } else if (city) {
                    return city;
                } else if (state) {
                    return state;
                } else if (country) {
                    return country;
                } else if (address) {
                    // Pega os dois primeiros elementos do endereço
                    return address.split(",").slice(0, 2).join(",").trim();
                } else {
                    // Último recurso - coordenadas
                    return `Local (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                }
            } catch (error) {
                console.error("Erro ao obter nome do local:", error);
                return `Local (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
            }
        }

        async function getRandomLocation() {
            const maxAttempts = 20;
            let attempts = 0;
            
            while (attempts < maxAttempts) {
                let lat, lng;
                
                // Escolhe a estratégia de geração de coordenadas baseada no modo de jogo
                switch (gameMode) {
                    case 'state':
                        const stateCoords = getRandomStateLocation();
                        lat = stateCoords.lat;
                        lng = stateCoords.lng;
                        break;
                    case 'brazil':
                        const brazilCoords = getRandomBrazilLocation();
                        lat = brazilCoords.lat;
                        lng = brazilCoords.lng;
                        break;
                    case 'world':
                        const worldCoords = getRandomWorldLocation();
                        lat = worldCoords.lat;
                        lng = worldCoords.lng;
                        break;
                }
                
                try {
                    const panoramaData = await getPanoramaData(lat, lng);
                    const locationName = await getCityName(
                        panoramaData.location.latLng.lat(),
                        panoramaData.location.latLng.lng()
                    );
                    
                    // Adiciona ao cache de locais usados
                    usedLocations.push({
                        lat: panoramaData.location.latLng.lat(),
                        lng: panoramaData.location.latLng.lng()
                    });
                    
                    // Limita o cache para não crescer indefinidamente
                    if (usedLocations.length > 50) {
                        usedLocations.shift();
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
            
            // Fallback extremo - coordenadas aleatórias (muito raro chegar aqui)
            console.warn("Usando fallback de localização aleatória após", maxAttempts, "tentativas");
            const fallbackCoords = gameMode === 'brazil' ? getRandomBrazilLocation() : 
                                 gameMode === 'state' ? getRandomStateLocation() : getRandomWorldLocation();
            return {
                lat: fallbackCoords.lat,
                lng: fallbackCoords.lng,
                name: `Local (${fallbackCoords.lat.toFixed(4)}, ${fallbackCoords.lng.toFixed(4)})`
            };
        }

        function shareResult() {
            const shareText = `Acabei de jogar Gincaneiros no modo ${gameMode === 'state' ? 'Estado' : gameMode === 'brazil' ? 'Brasil' : 'Mundo'} e marquei ${score} pontos! Tente bater meu recorde! 🌍`;
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
            // Primeiro detecta a localização do usuário para definir o estado padrão
            try {
                await detectUserLocation();
                if (userState) {
                    document.getElementById('stateMode').textContent = userState;
                }
            } catch (error) {
                console.error("Não foi possível detectar a localização do usuário:", error);
                // Fallback para Brasil se não conseguir detectar o estado
                gameMode = 'brazil';
                document.getElementById('brazilMode').classList.add('active');
                document.getElementById('stateMode').classList.remove('active');
            }

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
                      distance < 300 ? 'Boa tentativa!' : 'Continue tentando!',
                html: `
                    <div style="text-align: left;">
                        <p><b>Rodada:</b> ${roundsPlayed}/${maxRounds}</p>
                        <p><b>Sua distância:</b> ${Math.ceil(distance)} km</p>
                        <p><b>Local correto:</b> ${city}, ${country}</p>
                        <p><b>Pontos ganhos:</b> ${Math.round(points)}</p>
                    </div>
                `,
                icon: distance < 10 ? 'success' : 
                     distance < 100 ? 'success' : 
                     distance < 300 ? 'info' : 'error',
                confirmButtonText: 'Próximo local',
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