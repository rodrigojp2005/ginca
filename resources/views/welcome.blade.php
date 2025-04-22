@extends('layouts.app')

@section('content')
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
        <!-- <button class="game-mode-btn active" id="cityMode" onclick="showCityMenu()">
            <i class="fas fa-city mr-1"></i> Minha Cidade
        </button>  -->
        <button class="game-mode-btn" id="localMode" onclick="setGameMode('local')">
            <i class="fas fa-map-marker-alt mr-1"></i> Meu Local
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
@endsection