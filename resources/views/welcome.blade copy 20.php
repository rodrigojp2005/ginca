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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">

    <style>
        :root {
            --primary: #3B82F6;
            --primary-dark: #2563EB;
            --secondary: #1E293B;
            --accent: #F59E0B;
            --success: #10B981;
            --danger: #EF4444;
            --light: #F8FAFC;
            --dark: #0F172A;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--dark);
        }

        .navbar {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--dark) 100%);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logo-text {
            background: linear-gradient(90deg, var(--accent) 0%, #F97316 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 700;
        }

        .score-display {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 9999px;
            padding: 0.25rem 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .game-container {
            height: calc(100vh - 68px);
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

        .map-toggle {
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 100;
            cursor: pointer;
            border: 2px solid var(--primary);
            transition: all 0.3s ease;
        }

        .map-toggle:hover {
            transform: scale(1.1);
        }

        .map-icon {
            width: 70%;
            height: 70%;
            object-fit: contain;
        }

        #map-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            z-index: 50;
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
            bottom: 1.5rem;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            z-index: 60;
        }

        .confirm-btn {
            background: linear-gradient(135deg, var(--accent) 0%, #F97316 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 9999px;
            font-size: 1rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .confirm-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.5);
        }

        .app-footer {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--dark) 100%);
            color: white;
            padding: 0.75rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .social-icons a {
            color: white;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            color: var(--accent);
            transform: scale(1.1);
        }

        .game-mode-selector {
            position: fixed;
            top: 4.5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 40;
            display: flex;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 0.5rem;
            border-radius: 9999px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .game-mode-btn {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            background: transparent;
            color: var(--secondary);
        }

        .game-mode-btn.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        }

        .custom-marker {
            position: absolute;
            width: 3rem;
            height: 3rem;
            z-index: 100;
            pointer-events: none;
            transform: translate(-50%, -50%);
        }

        .distance-meter {
            position: absolute;
            bottom: 6rem;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-weight: 600;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 100;
        }

        .round-indicator {
            position: absolute;
            top: 1rem;
            right: 4rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            color: var(--secondary);
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .mobile-menu {
            position: fixed;
            top: 4.5rem;
            right: 1rem;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow: hidden;
            transform: scale(0.95);
            opacity: 0;
            transform-origin: top right;
            transition: all 0.2s ease;
            pointer-events: none;
        }

        .mobile-menu.show {
            transform: scale(1);
            opacity: 1;
            pointer-events: all;
        }

        .menu-item {
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--secondary);
            font-weight: 500;
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .menu-item:last-child {
            border-bottom: none;
        }

        .menu-item:hover {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary);
        }

        .menu-item i {
            width: 1.25rem;
            text-align: center;
        }

        #stateMenu {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            z-index: 1001;
            max-height: 20rem;
            overflow-y: auto;
            width: 12rem;
        }

        #stateMenu div {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #F1F5F9;
            transition: all 0.2s ease;
        }

        #stateMenu div:hover {
            background: #F8FAFC;
            color: var(--primary);
        }

        #stateMenu div:last-child {
            border-bottom: none;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(5px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            color: white;
        }

        .loading-spinner {
            width: 3rem;
            height: 3rem;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--accent);
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltip-text {
            visibility: hidden;
            width: 10rem;
            background-color: var(--secondary);
            color: white;
            text-align: center;
            border-radius: 0.375rem;
            padding: 0.5rem;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.875rem;
            font-weight: normal;
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        @media (max-width: 640px) {
            .game-mode-selector {
                top: 5rem;
                font-size: 0.875rem;
            }

            .game-mode-btn {
                padding: 0.375rem 0.75rem;
            }

            .confirm-btn {
                padding: 0.625rem 1.5rem;
                font-size: 0.875rem;
            }

            .map-toggle {
                width: 3rem;
                height: 3rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner"></div>
        <p class="text-xl font-semibold mt-4">Carregando o jogo...</p>
        <p class="text-sm opacity-75 mt-2">Por favor, aguarde</p>
    </div>

    <!-- Navigation -->
    <nav class="navbar fixed top-0 left-0 w-full z-30 py-2 px-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="https://gincaneiros.com/images/gincaneiros_logo.png" class="w-10 h-10 rounded-full border-2 border-white bg-white" alt="Gincaneiros Logo">
                <span class="logo-text text-xl">Gincaneiros</span>
                <div class="score-display">
                    <i class="fas fa-trophy text-yellow-400"></i>
                    <span id="score">0</span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span id="user-info" class="text-white text-sm hidden sm:block"></span>
                <div class="tooltip">
                    <button id="mobile-menu-button" class="p-2 text-white">
                        <img src="https://media.tenor.com/MG0VpR0F0-sAAAAi/icon-cute.gif" alt="Menu" class="w-8 h-8">
                    </button>
                    <span class="tooltip-text">Menu do Jogo</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu">
        <div class="menu-item" onclick="showHowToPlay()">
            <i class="fas fa-question-circle text-blue-500"></i>
            Como Jogar
        </div>
        <div class="menu-item" onclick="showAbout()">
            <i class="fas fa-info-circle text-blue-500"></i>
            Sobre o Jogo
        </div>
        <div class="menu-item" onclick="showContact()">
            <i class="fas fa-envelope text-blue-500"></i>
            Contato
        </div>
    </div>

    <!-- Game Mode Selector -->
    <div class="game-mode-selector">
        <button class="game-mode-btn active" id="stateMode" onclick="showStateMenu()">
            <i class="fas fa-map-marker-alt mr-1"></i> Estado
        </button>
        <button class="game-mode-btn" id="brazilMode" onclick="setGameMode('brazil')">
            <i class="flag-icon flag-icon-br mr-1"></i> Brasil
        </button>
        <button class="game-mode-btn" id="worldMode" onclick="setGameMode('world')">
            <i class="fas fa-globe-americas mr-1"></i> Mundo 
        </button>
    </div>

    <!-- Game Container -->
    <div class="game-container">
        <!-- Round Indicator -->
        <div class="round-indicator">
            <span id="roundCounter">1/5</span>
        </div>

        <!-- Street View -->
        <div id="street-view-container">
            <div id="street-view"></div>
            <div id="street-view-marker" class="custom-marker" style="display: none;">
                <img src="https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExcnc3a3lvcHFrN2ZwZTV2bnJzb3ZrYWJjeTl6ZXB4YzE0N3NkMHU3MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/A5PYmtufdQIjD37IC0/giphy.gif" alt="Marker" class="w-full h-full"> 
            </div>

            <!-- Distance Meter (shown after guess) -->
            <div id="distanceMeter" class="distance-meter" style="display: none;">
                <i class="fas fa-map-marked-alt text-blue-500"></i>
                <span id="distanceText">0 km</span>
            </div>

            <!-- Map Toggle Button -->
            <button class="map-toggle" id="mapToggle">
                <img src="https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExbHdyenVlcDR4cXFydHVxb2Q1bW5ibmh1aHplcGFubzRvdWwyYmQydCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/PjTRytCVV9ROhgtEyI/giphy.gif" alt="Mapa" class="map-icon">
            </button>
        </div>

        <!-- Map View -->
        <div id="map-container">
            <div id="map"></div>

            <div class="confirm-btn-container">
                <button class="confirm-btn" id="confirmBtn">
                    <i class="fas fa-check-circle"></i> Confirmar Palpite
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="app-footer fixed bottom-0 left-0 w-full text-center py-2">
        <div class="container">
            <p class="text-sm mb-1">© 2025 Gincaneiros - Adivinhe o local</p>
            <div class="social-icons flex justify-center space-x-4">
                <a href="#" id="share-facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" id="share-instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" id="share-whatsapp"><i class="fab fa-whatsapp"></i></a>
                <a href="#" id="share-twitter"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Game Variables
        let map;
        let marker;
        let panorama;
        let correctLocation;
        let score = 0;
        let roundsPlayed = 0;
        const maxRounds = 5;
        let currentGameLocations = [];
        let gameSeed = Math.floor(Math.random() * 1000000);
        let usedLocations = [];
        let lastLocationRegion = null;
        let gameMode = 'state';
        let userState = null;
        let stateBounds = null;
        let currentDistance = 0;

        // Brazilian States Data
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

        // Place types for nearby search
        const PLACE_TYPES = {
            RESTAURANT: 'restaurant',
            LANDMARK: 'tourist_attraction',
            PARK: 'park'
        };

        // Initialize the game when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(showWelcomeAlert, 1500);
            setupEventListeners();
        });

        function showWelcomeAlert() {
            Swal.fire({
                title: "Bem-vindo ao Gincaneiros!",
                html: `
                    <div class="text-left">
                        <p class="mb-3">Ajude o Jhon a encontrar-se no mapa!</p>
                        <p class="mb-1"><i class="fas fa-map-marker-alt text-blue-500 mr-2"></i> Explore a vista do Street View</p>
                        <p class="mb-1"><i class="fas fa-map-marked text-yellow-500 mr-2"></i> Marque seu palpite no mapa</p>
                        <p class="mb-1"><i class="fas fa-trophy text-yellow-400 mr-2"></i> Ganhe pontos por precisão</p>
                    </div>
                `,
                imageUrl: "https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExcnc3a3lvcHFrN2ZwZTV2bnJzb3ZrYWJjeTl6ZXB4YzE0N3NkMHU3MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/A5PYmtufdQIjD37IC0/giphy.gif",
                imageWidth: 300,
                imageHeight: 150,
                imageAlt: "Cadê Jhon?",
                confirmButtonText: "Vamos começar!",
                confirmButtonColor: "#3B82F6",
            });
        }

        function setupEventListeners() {
            document.getElementById('mobile-menu-button').addEventListener('click', toggleMobileMenu);
            document.getElementById('mapToggle').addEventListener('click', toggleMap);
            document.getElementById('confirmBtn').addEventListener('click', confirmGuess);
            
            document.getElementById('share-facebook').addEventListener('click', (e) => {
                e.preventDefault();
                shareOnFacebook();
            });
            
            document.getElementById('share-instagram').addEventListener('click', (e) => {
                e.preventDefault();
                shareOnInstagram();
            });
            
            document.getElementById('share-whatsapp').addEventListener('click', (e) => {
                e.preventDefault();
                shareOnWhatsApp();
            });
            
            document.getElementById('share-twitter').addEventListener('click', (e) => {
                e.preventDefault();
                shareOnTwitter();
            });
            
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#mobile-menu-button') && !e.target.closest('#mobile-menu')) {
                    document.getElementById('mobile-menu').classList.remove('show');
                }
            });
        }

        function toggleMobileMenu() {
            document.getElementById('mobile-menu').classList.toggle('show');
        }

        function showHowToPlay() {
            Swal.fire({
                title: '<i class="fas fa-question-circle text-blue-500 mr-2"></i> Como Jogar',
                html: `
                    <div class="text-left space-y-3">
                        <div class="flex items-start">
                            <span class="bg-blue-100 text-blue-800 rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">1</span>
                            <p>Você verá uma imagem do Google Street View</p>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-blue-100 text-blue-800 rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">2</span>
                            <p>Tente adivinhar onde essa localização está no mapa</p>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-blue-100 text-blue-800 rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">3</span>
                            <p>Clique no botão de mapa no canto inferior direito</p>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-blue-100 text-blue-800 rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">4</span>
                            <p>Marque seu palpite no mapa e confirme</p>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-blue-100 text-blue-800 rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">5</span>
                            <p>Quanto mais perto do local real, mais pontos você ganha!</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Entendi',
                confirmButtonColor: '#3B82F6',
                customClass: {
                    popup: 'rounded-xl'
                }
            });
            document.getElementById('mobile-menu').classList.remove('show');
        }

        function showAbout() {
            Swal.fire({
                title: '<i class="fas fa-info-circle text-blue-500 mr-2"></i> Sobre o Jogo',
                html: `
                    <div class="text-left space-y-3">
                        <p><span class="font-semibold">Gincaneiros</span> é um jogo de geolocalização divertido e desafiador.</p>
                        
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <h3 class="font-semibold text-blue-800 mb-1">Modos de Jogo:</h3>
                            <ul class="list-disc pl-5 space-y-1">
                                <li><span class="font-medium">Estado:</span> Locais próximos a você</li>
                                <li><span class="font-medium">Brasil:</span> Desafios por todo o país</li>
                                <li><span class="font-medium">Mundo:</span> Explore locais globais</li>
                            </ul>
                        </div>
                        
                        <p>Desenvolvido com ❤️ para amantes de geografia e aventura!</p>
                    </div>
                `,
                confirmButtonText: 'Legal!',
                confirmButtonColor: '#3B82F6'
            });
            document.getElementById('mobile-menu').classList.remove('show');
        }

        function showContact() {
            Swal.fire({
                title: '<i class="fas fa-envelope text-blue-500 mr-2"></i> Contato',
                html: `
                    <div class="text-left space-y-3">
                        <p>Dúvidas, sugestões ou problemas?</p>
                        <p>Entre em contato conosco:</p>
                        
                        <div class="flex items-center">
                            <i class="fas fa-phone-alt text-blue-500 mr-3"></i>
                            <span class="font-medium">(53) 98105-6952</span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-blue-500 mr-3"></i>
                            <span class="font-medium">contato@gincaneiros.com</span>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Fechar',
                confirmButtonColor: '#3B82F6'
            });
            document.getElementById('mobile-menu').classList.remove('show');
        }

        function setGameMode(mode) {
            gameMode = mode;
            
            document.getElementById('stateMode').classList.remove('active');
            document.getElementById('brazilMode').classList.remove('active');
            document.getElementById('worldMode').classList.remove('active');
            document.getElementById(mode + 'Mode').classList.add('active');
            
            resetGame();
        }

        function showStateMenu() {
            const menuHtml = `
                <div id="stateMenu" class="shadow-lg">
                    ${brazilianStates.map(state => `
                        <div onclick="selectState('${state.name}', '${state.code}')">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                            ${state.name}
                        </div>
                    `).join('')}
                </div>
            `;
            
            const existingMenu = document.getElementById('stateMenu');
            if (existingMenu) existingMenu.remove();
            
            const stateButton = document.getElementById('stateMode');
            stateButton.insertAdjacentHTML('afterend', menuHtml);
            
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
            document.getElementById('stateMode').innerHTML = `
                <i class="fas fa-map-marker-alt mr-1"></i> ${stateName}
            `;
            document.getElementById('stateMenu').remove();
            
            setStateBounds(stateCode);
            resetGame();
        }

        function setStateBounds(stateCode) {
            const stateCenters = {
                'AC': {lat: -9.11, lng: -70.52},
                'AL': {lat: -9.57, lng: -36.55},
                'AP': {lat: 1.41, lng: -51.77},
                'AM': {lat: -3.07, lng: -61.66},
                'BA': {lat: -12.96, lng: -38.51},
                'CE': {lat: -3.71, lng: -38.54},
                'DF': {lat: -15.83, lng: -47.86},
                'ES': {lat: -19.18, lng: -40.34},
                'GO': {lat: -16.64, lng: -49.31},
                'MA': {lat: -5.43, lng: -47.52},
                'MT': {lat: -12.64, lng: -55.42},
                'MS': {lat: -20.51, lng: -54.54},
                'MG': {lat: -18.10, lng: -44.38},
                'PA': {lat: -5.53, lng: -52.29},
                'PB': {lat: -7.06, lng: -35.55},
                'PR': {lat: -25.43, lng: -49.27},
                'PE': {lat: -8.28, lng: -35.07},
                'PI': {lat: -8.28, lng: -43.68},
                'RJ': {lat: -22.91, lng: -43.21},
                'RN': {lat: -5.22, lng: -36.52},
                'RS': {lat: -30.02, lng: -53.10},
                'SP': {lat: -23.55, lng: -46.63},
            };
            
            const center = stateCenters[stateCode] || {lat: -15.78, lng: -47.93};
            stateBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(center.lat - 1, center.lng - 1),
                new google.maps.LatLng(center.lat + 1, center.lng + 1)
            );
        }

      async function detectUserLocation() {
        return new Promise((resolve, reject) => {
            const options = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            };

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                        try {
                            // Obter coordenadas com alta precisão
                            const highAccuracyLocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                                accuracy: position.coords.accuracy
                            };

                            // Melhorar com geocodificação reversa (Opção 3)
                            const geocoder = new google.maps.Geocoder();
                            const response = await new Promise((resolve, reject) => {
                                geocoder.geocode({ location: highAccuracyLocation }, (results, status) => {
                                    status === 'OK' ? resolve(results) : reject(status);
                                });
                            });

                            // Extrair cidade e estado
                            let city, state, stateCode;
                            for (const component of response[0].address_components) {
                                if (component.types.includes('locality')) {
                                    city = component.long_name;
                                } else if (component.types.includes('administrative_area_level_1')) {
                                    state = component.long_name;
                                    stateCode = component.short_name;
                                    userState = state;
                                }
                            }

                            // Definir área de busca (50km ao redor)
                            stateBounds = new google.maps.LatLngBounds();
                            const radius = 0.45; // ~50km
                            stateBounds.extend(new google.maps.LatLng(
                                highAccuracyLocation.lat - radius, 
                                highAccuracyLocation.lng - radius
                            ));
                            stateBounds.extend(new google.maps.LatLng(
                                highAccuracyLocation.lat + radius, 
                                highAccuracyLocation.lng + radius
                            ));

                            // Atualizar UI
                            if (state) {
                                document.getElementById('stateMode').innerHTML = `
                                    <i class="fas fa-map-marker-alt mr-1"></i> ${city || state}
                                `;
                            }

                            resolve({ 
                                ...highAccuracyLocation,
                                city, 
                                state,
                                stateCode
                            });

                        } catch (error) {
                            console.error("Error enhancing location:", error);
                            reject(error);
                        }
                    },
                    (error) => {
                        console.error("Geolocation error:", error);
                        reject(error);
                    },
                    options
                );
            } else {
                reject("Geolocation not supported");
            }
        });
    }
        async function getNearbyPlaces(lat, lng, radius = 50000, type = PLACE_TYPES.RESTAURANT) {
            return new Promise((resolve, reject) => {
                const placesService = new google.maps.places.PlacesService(document.createElement('div'));
                
                placesService.nearbySearch({
                    location: { lat, lng },
                    radius: radius,
                    type: type,
                    openNow: true
                }, (results, status) => {
                    if (status === 'OK' && results.length > 0) {
                        const validPlaces = results.filter(place => 
                            place.photos && 
                            place.rating >= 3.5 && 
                            place.user_ratings_total > 10
                        );
                        
                        if (validPlaces.length > 0) {
                            resolve(validPlaces);
                        } else {
                            reject("No suitable places found");
                        }
                    } else {
                        reject(status);
                    }
                });
            });
        }

        function getRandomStateLocation() {
            if (!stateBounds) {
                return getRandomBrazilLocation();
            }
            
            const ne = stateBounds.getNorthEast();
            const sw = stateBounds.getSouthWest();
            
            const lat = sw.lat() + Math.random() * (ne.lat() - sw.lat());
            const lng = sw.lng() + Math.random() * (ne.lng() - sw.lng());
            
            return { lat, lng };
        }

        function getRandomBrazilLocation() {
            const lat = -33 + Math.random() * 26;
            const lng = -74 + Math.random() * 39;
            return { lat, lng };
        }

        function getRandomWorldLocation() {
            const regions = [
                { latRange: [-35, 35], lngRange: [-180, -30] },
                { latRange: [-35, 35], lngRange: [-30, 60] },
                { latRange: [-35, 35], lngRange: [60, 180] },
                { latRange: [35, 70], lngRange: [-10, 40] },
                { latRange: [-60, -20], lngRange: [-80, -30] }
            ];
            
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
                            reject(isGoodLocation ? "Location already used" : "Area not suitable");
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
                    return address.split(",").slice(0, 2).join(",").trim();
                } else {
                    return `Local (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                }
            } catch (error) {
                console.error("Error getting location name:", error);
                return `Local (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
            }
        }
        
        async function getRandomLocation() {
            const maxAttempts = 20;
            let attempts = 0;
            
            while (attempts < maxAttempts) {
                try {
                    // Estratégia diferente para o modo estado com geolocalização
                    if (gameMode === 'state' && stateBounds) {
                        // Tentar primeiro encontrar lugares próximos (restaurantes)
                        try {
                            const center = stateBounds.getCenter();
                            const places = await getNearbyPlaces(center.lat(), center.lng());
                            
                            // Escolher um lugar aleatório
                            const randomPlace = places[Math.floor(Math.random() * places.length)];
                            const placeLocation = randomPlace.geometry.location;
                            
                            // Verificar se o panorama está disponível
                            const panoramaData = await getPanoramaData(
                                placeLocation.lat(), 
                                placeLocation.lng(),
                                100 // Raio menor para lugares específicos
                            );
                            
                            // Obter nome do lugar
                            const locationName = randomPlace.name || await getCityName(
                                placeLocation.lat(), 
                                placeLocation.lng()
                            );
                            
                            // Adicionar ao cache de locais usados
                            usedLocations.push({
                                lat: placeLocation.lat(),
                                lng: placeLocation.lng()
                            });
                            
                            return {
                                lat: placeLocation.lat(),
                                lng: placeLocation.lng(),
                                name: locationName
                            };
                        } catch (placeError) {
                            console.log("Falling back to random street view for state mode:", placeError);
                            // Se falhar, continuar com a abordagem normal
                        }
                    }
                    
                    // Abordagem original para outros modos ou fallback
                    let lat, lng;
                    
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
                    
                    const panoramaData = await getPanoramaData(lat, lng);
                    const locationName = await getCityName(
                        panoramaData.location.latLng.lat(),
                        panoramaData.location.latLng.lng()
                    );
                    
                    // Adicionar ao cache de locais usados
                    usedLocations.push({
                        lat: panoramaData.location.latLng.lat(),
                        lng: panoramaData.location.latLng.lng()
                    });
                    
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
            
            // Fallback extremo
            console.warn("Using random location fallback after", maxAttempts, "attempts");
            const fallbackCoords = gameMode === 'brazil' ? getRandomBrazilLocation() :
                                  gameMode === 'state' ? getRandomStateLocation() : getRandomWorldLocation();
            return {
                lat: fallbackCoords.lat,
                lng: fallbackCoords.lng,
                name: `Local (${fallbackCoords.lat.toFixed(4)}, ${fallbackCoords.lng.toFixed(4)})`
            };
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
                console.error("Could not get panorama position or POV for marker");
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
                    url: 'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExcnc3a3lvcHFrN2ZwZTV2bnJzb3ZrYWJjeTl6ZXB4YzE0N3NkMHU3MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/A5PYmtufdQIjD37IC0/giphy.gif',
                    scaledSize: new google.maps.Size(80, 120),
                    anchor: new google.maps.Point(40, 120)
                },
                title: "Ponto de Interesse"
            });
        }

        async function initMap() {
            // Hide loading overlay
            document.getElementById('loadingOverlay').style.display = 'none';
            
            // First detect user location to set default state
            try {
                await detectUserLocation();
                if (userState) {
                    document.getElementById('stateMode').innerHTML = `
                        <i class="fas fa-map-marker-alt mr-1"></i> ${userState}
                    `;
                }
            } catch (error) {
                console.error("Could not detect user location:", error);
                // Fallback to Brazil if can't detect state
                gameMode = 'brazil';
                document.getElementById('brazilMode').classList.add('active');
                document.getElementById('stateMode').classList.remove('active');
            }
            
            // Initialize map
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: -14.2350, lng: -51.9253 }, // Approximate center of Brazil
                zoom: 4,
                disableDefaultUI: true,
                streetViewControl: false,
                gestureHandling: "greedy",
                fullscreenControl: false,
                mapTypeControl: false,
                zoomControl: false,
            });
            
            // Add click listener for placing markers
            map.addListener("click", function(event) {
                placeMarker(event.latLng);
            });
            
            // Start the game
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
            // Hide map and show street view
            document.getElementById('map-container').classList.remove('show');
            document.getElementById('street-view-container').style.display = 'block';
            
            // Hide distance meter
            document.getElementById('distanceMeter').style.display = 'none';
            
            // Update round counter
            document.getElementById('roundCounter').textContent = `${roundsPlayed + 1}/${maxRounds}`;
            
            if (roundsPlayed >= maxRounds) {
                endGame();
                return;
            }
            
            // Get a new random location
            correctLocation = await getRandomLocation();
            currentGameLocations.push({
                lat: correctLocation.lat,
                lng: correctLocation.lng,
                name: correctLocation.name
            });
            
            // Initialize Street View panorama
            panorama = new google.maps.StreetViewPanorama(
                document.getElementById("street-view"), {
                    position: correctLocation,
                    pov: { heading: Math.random() * 360, pitch: 5 },
                    zoom: 1,
                    disableDefaultUI: true,
                    showRoadLabels: false,
                    zoomControl: true,
                    zoomControlOptions: {
                        position: google.maps.ControlPosition.LEFT_BOTTOM,
                    },
                    motionTracking: false
                }
            );
            
            // Add marker in street view
            positionMarkerInStreetView();
            
            // Clear any existing map marker
            if (marker) {
                marker.setMap(null);
                marker = null;
            }
        }

        function placeMarker(location) {
            if (marker) {
                marker.setPosition(location);
            } else {
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    draggable: true,
                    animation: google.maps.Animation.DROP,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        scaledSize: new google.maps.Size(32, 32)
                    }
                });
                
                // Center map on marker
                map.setCenter(location);
            }
        }

        function confirmGuess() {
            if (!marker) {
                Swal.fire({
                    title: 'Ops!',
                    text: 'Por favor, marque um local no mapa!',
                    icon: 'warning',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }
            
            const guessedLocation = marker.getPosition();
            currentDistance = google.maps.geometry.spherical.computeDistanceBetween(
                guessedLocation, new google.maps.LatLng(correctLocation.lat, correctLocation.lng)
            ) / 1000;
            
            // Calculate points (more points for closer guesses)
            const points = Math.max(0, 5000 - Math.floor(currentDistance)) / 100;
            score += Math.round(points);
            document.getElementById("score").textContent = score;
            
            // Show distance meter
            document.getElementById('distanceText').textContent = `${Math.ceil(currentDistance)} km`;
            document.getElementById('distanceMeter').style.display = 'flex';
            
            // Get location parts for display
            const locationParts = correctLocation.name.split(', ');
            const city = locationParts[0];
            const country = locationParts.length > 1 ? locationParts[locationParts.length - 1] : correctLocation.name;
            
            // Show result with appropriate message based on distance
            let title, icon, message;
            
            if (currentDistance < 1) {
                title = 'Perfeito! 🎯';
                icon = 'success';
                message = 'Você acertou quase exatamente! Impressionante!';
            } else if (currentDistance < 10) {
                title = 'Ótimo trabalho! 👍';
                icon = 'success';
                message = 'Muito perto do local correto!';
            } else if (currentDistance < 100) {
                title = 'Boa tentativa!';
                icon = 'info';
                message = 'Você está na região correta!';
            } else if (currentDistance < 500) {
                title = 'Quase lá!';
                icon = 'info';
                message = 'Você está no caminho certo!';
            } else {
                title = 'Continue tentando!';
                icon = 'error';
                message = 'Tente observar melhor os detalhes na próxima rodada.';
            }
            
            Swal.fire({
                title: title,
                html: `
                    <div class="text-left space-y-2">
                        <p>${message}</p>
                        <p><b>Sua distância:</b> ${Math.ceil(currentDistance)} km</p>
                        <p><b>Local correto:</b> ${city}, ${country}</p>
                        <p><b>Pontos ganhos:</b> ${Math.round(points)}</p>
                        <p class="text-sm text-gray-500">Rodada ${roundsPlayed + 1} de ${maxRounds}</p>
                    </div>
                `,
                icon: icon,
                confirmButtonText: 'Próximo local',
                confirmButtonColor: '#3B82F6',
            }).then((result) => {
                roundsPlayed++;
                newRound();
            });
        }

        function endGame() {
            // Calculate performance rating
            let rating, ratingColor;
            const averageDistance = currentGameLocations.reduce((sum, loc, idx) => {
                if (idx === 0) return 0; // Skip first round as we don't have distance
                return sum + loc.distance;
            }, 0) / (currentGameLocations.length - 1);
            
            if (averageDistance < 50) {
                rating = "Excelente";
                ratingColor = "#10B981"; // Emerald
            } else if (averageDistance < 200) {
                rating = "Bom";
                ratingColor = "#3B82F6"; // Blue
            } else if (averageDistance < 500) {
                rating = "Regular";
                ratingColor = "#F59E0B"; // Amber
            } else {
                rating = "Tente novamente";
                ratingColor = "#EF4444"; // Red
            }
            
            Swal.fire({
                title: 'Jogo Finalizado!',
                html: `
                    <div class="text-left space-y-3">
                        <div class="bg-gray-100 p-3 rounded-lg">
                            <h3 class="font-semibold">Sua pontuação final: <span class="text-2xl" style="color: #F59E0B;">${score}</span></h3>
                            <p class="text-sm">Desempenho: <span style="color: ${ratingColor}; font-weight: 600;">${rating}</span></p>
                        </div>
                        
                        <p>Locais deste jogo:</p>
                        <ul class="list-disc pl-5 space-y-1 text-sm">
                            ${currentGameLocations.map((loc, index) => `
                                <li>Rodada ${index + 1}: ${loc.name}</li>
                            `).join('')}
                        </ul>
                    </div>
                `,
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Jogar Novamente',
                cancelButtonText: 'Compartilhar',
                confirmButtonColor: '#3B82F6',
                cancelButtonColor: '#10B981',
                footer: '<p class="text-sm text-gray-500">Desafie seus amigos a bater seu recorde!</p>'
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
            currentGameLocations = [];
            gameSeed = Math.floor(Math.random() * 1000000);
            document.getElementById("score").textContent = score;
            document.getElementById("roundCounter").textContent = `1/${maxRounds}`;
            newRound();
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
                    console.log('Error sharing:', err);
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
                    <div class="space-y-3">
                        <p>Compartilhe sua pontuação com os amigos:</p>
                        <div class="grid grid-cols-2 gap-2">
                            <a href="https://wa.me/?text=${encodedText}%20${encodedUrl}" class="flex items-center justify-center gap-2 bg-green-500 text-white py-2 px-4 rounded-lg" target="_blank">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}&quote=${encodedText}" class="flex items-center justify-center gap-2 bg-blue-600 text-white py-2 px-4 rounded-lg" target="_blank">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=${encodedText}&url=${encodedUrl}" class="flex items-center justify-center gap-2 bg-blue-400 text-white py-2 px-4 rounded-lg" target="_blank">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                            <a href="#" onclick="navigator.clipboard.writeText('${text} ' + '${url}'); Swal.fire('Link copiado!', 'Cole em qualquer lugar para compartilhar.', 'success'); return false;" class="flex items-center justify-center gap-2 bg-gray-600 text-white py-2 px-4 rounded-lg">
                                <i class="fas fa-link"></i> Copiar Link
                            </a>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true
            });
        }

        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
        }

        function shareOnInstagram() {
            Swal.fire({
                title: 'Compartilhar no Instagram',
                text: 'Para compartilhar no Instagram, você pode postar um story com o link do jogo!',
                icon: 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3B82F6'
            });
        }

        function shareOnWhatsApp() {
            const text = encodeURIComponent("Estou jogando Gincaneiros, um jogo incrível de geolocalização! Tente bater meu recorde: ");
            const url = encodeURIComponent(window.location.href);
            window.open(`https://wa.me/?text=${text}${url}`, '_blank');
        }

        function shareOnTwitter() {
            const text = encodeURIComponent("Acabei de jogar Gincaneiros e marquei " + score + " pontos! Tente bater meu recorde!");
            const url = encodeURIComponent(window.location.href);
            window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB3VgDsnZQDKV1w4TM-D19msn3TgOOMuzk&libraries=geometry,streetView,places&callback=initMap"></script>
</body>
</html>