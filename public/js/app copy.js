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
let gameMode = 'local';
let userLocation = null;
let localBounds = null;
let currentDistance = 0;
let attempts = 1;
const maxAttempts = 5;

// Place types for nearby search
const PLACE_TYPES = {
    RESTAURANT: 'restaurant',
    LANDMARK: 'tourist_attraction',
    PARK: 'park'
};


// Função para verificação segura de elementos
function getSafeElement(id) {
    const element = document.getElementById(id);
    if (!element) {
        console.warn(`Elemento com ID ${id} não encontrado`);
        return null;
    }
    return element;
}
// Initialize the game when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Verificação segura de elementos antes de usar
    const loadingOverlay = getSafeElement('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    } else {
        console.warn('Elemento loadingOverlay não encontrado');
    }

    setTimeout(showWelcomeAlert, 1500);
    setupEventListeners();
    initMap();
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
                        <li><span class="font-medium">Local:</span> Locais próximos a você</li>
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
    
    // Remove a classe 'active' de todos os botões de modo
    ['localMode', 'brazilMode', 'worldMode'].forEach(id => {
        const element = document.getElementById(id);
        if (element) element.classList.remove('active');
    });
    
    // Adiciona 'active' apenas no botão selecionado
    const activeBtn = document.getElementById(`${mode}Mode`);
    if (activeBtn) activeBtn.classList.add('active');
    
    resetGame();
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
                        const highAccuracyLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        };

                        const geocoder = new google.maps.Geocoder();
                        const response = await new Promise((resolve, reject) => {
                            geocoder.geocode({ location: highAccuracyLocation }, (results, status) => {
                                status === 'OK' ? resolve(results) : reject(status);
                            });
                        });

                        let locationName = "Sua Região";
                        if (response[0]?.formatted_address) {
                            locationName = response[0].formatted_address.split(',')[0];
                        }

                        // Definir área de busca (20km ao redor)
                        localBounds = new google.maps.LatLngBounds();
                        const radius = 0.18; // ~20km
                        localBounds.extend(new google.maps.LatLng(
                            highAccuracyLocation.lat - radius, 
                            highAccuracyLocation.lng - radius
                        ));
                        localBounds.extend(new google.maps.LatLng(
                            highAccuracyLocation.lat + radius, 
                            highAccuracyLocation.lng + radius
                        ));

                        userLocation = {
                            coords: highAccuracyLocation,
                            name: locationName
                        };

                        resolve(highAccuracyLocation);

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

function getRandomLocalLocation() {
    if (!localBounds) {
        return getRandomBrazilLocation();
    }
    
    const ne = localBounds.getNorthEast();
    const sw = localBounds.getSouthWest();
    
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

async function getLocationName(lat, lng) {
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
    const MAX_ATTEMPTS = 20;
    let attempts = 0;
    let lastError = null;

    while (attempts < MAX_ATTEMPTS) {
        try {
            let coords;
            switch (gameMode) {
                case 'local':
                    coords = getRandomLocalLocation();
                    break;
                case 'brazil':
                    coords = getRandomBrazilLocation();
                    break;
                case 'world':
                    coords = getRandomWorldLocation();
                    break;
                default:
                    coords = getRandomBrazilLocation();
            }

            const panoramaData = await getPanoramaData(coords.lat, coords.lng);
            const locationName = await getLocationName(
                panoramaData.location.latLng.lat(),
                panoramaData.location.latLng.lng()
            );

            // Registrar local usado
            if (usedLocations.length > 100) {
                usedLocations = usedLocations.slice(-50);
            }
            
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
            lastError = error;
            attempts++;
            console.warn(`Attempt ${attempts}/${MAX_ATTEMPTS} failed:`, error);
            await new Promise(resolve => setTimeout(resolve, 100 + (attempts * 50)));
        }
    }

    // Fallback
    console.error("Fallback triggered:", lastError);
    const coords = gameMode === 'local' ? getRandomLocalLocation() : 
                  gameMode === 'brazil' ? getRandomBrazilLocation() : 
                  getRandomWorldLocation();
    
    return {
        ...coords,
        name: `Local Aleatório (${coords.lat.toFixed(4)}, ${coords.lng.toFixed(4)})`
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

// Função initMap revisada
async function initMap() {
    try {
        await detectUserLocation();
        
        // Ativação segura dos modos
        const localModeElement = getSafeElement('localMode');
        const brazilModeElement = getSafeElement('brazilMode');
        
        if (gameMode === 'local' && localModeElement) {
            localModeElement.classList.add('active');
        } else if (brazilModeElement) {
            gameMode = 'brazil'; // Fallback seguro
            brazilModeElement.classList.add('active');
        }
    } catch (error) {
        console.error("Erro na detecção de localização:", error);
        const brazilModeElement = getSafeElement('brazilMode');
        if (brazilModeElement) {
            gameMode = 'brazil';
            brazilModeElement.classList.add('active');
        }
    }

    // Inicialização do mapa com verificação segura
    const mapElement = getSafeElement('map');
    if (!mapElement) {
        console.error('Elemento do mapa não encontrado');
        return;
    }

    map = new google.maps.Map(mapElement, {
        center: { lat: -14.2350, lng: -51.9253 },
        zoom: 4,
        disableDefaultUI: true,
        streetViewControl: false,
        gestureHandling: "greedy",
        fullscreenControl: false,
        mapTypeControl: false,
        zoomControl: true,
    });

    map.addListener("click", function(event) {
        placeMarker(event.latLng);
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
    document.getElementById('distanceMeter').style.display = 'none';
    
    document.getElementById('roundCounter').textContent = `${roundsPlayed + 1}/${maxRounds}`;
    
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
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_BOTTOM,
            },
            motionTracking: false
        }
    );
    
    positionMarkerInStreetView();
    
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
        
        map.setCenter(location);
    }
}

async function confirmGuess() {
    if (!marker) {
        Swal.fire({
            title: 'Ops!',
            text: 'Por favor, marque um local no mapa!',
            icon: 'warning',
            confirmButtonColor: '#3B82F6'
        });
        return;
    }

    const guessedPos = marker.getPosition();
    const correctPos = new google.maps.LatLng(correctLocation.lat, correctLocation.lng);
    currentDistance = google.maps.geometry.spherical.computeDistanceBetween(guessedPos, correctPos) / 1000;

    if (currentDistance < 1) {
        handleRoundResult(true);
        return;
    }

    if (attempts < maxAttempts) {
        const heading = google.maps.geometry.spherical.computeHeading(guessedPos, correctPos);
        const direction = getDirectionFromHeading(heading);

        await Swal.fire({
            title: 'Quase lá!',
            html: `
                <div class="text-center">
                    <p>Seu palpite está a <b>${Math.ceil(currentDistance)} km</b> do local</p>
                    <img src="${direction}" class="w-32 h-32 mx-auto my-2" alt="Direção">
                    <p>Mova nesta direção!</p>
                    <small>Tentativa ${attempts}/${maxAttempts}</small>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Tentar Novamente',
            confirmButtonColor: '#3B82F6'
        });

        attempts++;
    } else {
        handleRoundResult(false);
    }
}

function getDirectionFromHeading(heading) {
    const normalizedHeading = (heading + 360) % 360;
    const directionIndex = Math.round(normalizedHeading / 45) % 8;
    
    const directionArrows = [
        'https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExeGh2Zjl5bzA4ejZuNHp2eDl5eGxhZm9xaDd6YzJmdjRmd3p0c2IweiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/KDhyTMJgCWMsBlUDSq/giphy.gif',
        'https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExcTc2dzZ2a3Npa2ljMjNpaWpqbzByZnJhOXN6bzljZnZiNHN5NnV0eSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/uK0YJtha6r7PzsLAwM/giphy.gif',
        'https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExeHk1OHcwd3NmZTFpN2lraHVpaTc3dmdvNjF2azh0ODg3aHgzbmN6NSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/CZRaIR4t4yOVGlBsXM/giphy.gif',
        'https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExdHlkZmI3ZmdsOHJzMXhnM3AwamRzeDNjd2UzM25uM2Q1ZHo3bWo3MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/Yn5UkZDYJiwCBoaQDN/giphy.gif',
        'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExdnltbWJyMmV2NWJjMTI3dG95aDRqdHozZHF2Z2ozeHhsZ3c4ZDlhayZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/cP53N7RJHVndLfPxdS/giphy.gif',
        'https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExdzBxcm5oajFhN3Z3eG1pajN0MG45bnJ2OXEwZ2xlejFiNjhkMHExYiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/Q7XPCjjc3KMwaTCbLQ/giphy.gif',
        'https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExYXJidnRkcmh4dHBvdGU1eGhyeXk0MzN4cXhqb2c0em83Ynp5OGN1MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/ifER5WV3JrB8iKso8Y/giphy.gif',
        'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExY3oxcGgzNWtndGYzYmhxejdvZGR6dm52bTd2bmVwaHkzNmpkb3N6ZiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/hU4d5hXTglhL62Tia3/giphy.gif'
    ];
    
    return directionArrows[directionIndex];
}

function handleRoundResult(isCorrect) {
    const points = calculatePoints(currentDistance, attempts);
    score += Math.round(points);
    document.getElementById("score").textContent = score;

    const locationParts = correctLocation.name.split(', ');
    const city = locationParts[0];
    const country = locationParts.length > 1 ? locationParts[locationParts.length - 1] : correctLocation.name;

    Swal.fire({
        title: isCorrect ? 'Acertou!' : 'Rodada Finalizada',
        html: `
            <div class="text-left space-y-2">
                <p><b>Local correto:</b> ${city}, ${country}</p>
                <p><b>Sua distância:</b> ${Math.ceil(currentDistance)} km</p>
                <p><b>Pontos ganhos:</b> ${Math.round(points)}</p>
                <p><b>Tentativas:</b> ${attempts}/${maxAttempts}</p>
                <p class="text-sm text-gray-500">Rodada ${roundsPlayed + 1} de ${maxRounds}</p>
            </div>
        `,
        icon: isCorrect ? 'success' : 'info',
        confirmButtonText: 'Próximo local',
        confirmButtonColor: '#3B82F6',
    }).then(() => {
        roundsPlayed++;
        attempts = 1;
        newRound();
    });
}

function calculatePoints(distance, attemptsUsed) {
    const basePoints = Math.max(0, 5000 - Math.floor(distance)) / 100;
    const penalty = (attemptsUsed - 1) * 0.2;
    return basePoints * (1 - penalty);
}

function endGame() {
    let rating, ratingColor;
    const averageDistance = currentGameLocations.reduce((sum, loc, idx) => {
        if (idx === 0) return 0;
        return sum + loc.distance;
    }, 0) / (currentGameLocations.length - 1);
    
    if (averageDistance < 50) {
        rating = "Excelente";
        ratingColor = "#10B981";
    } else if (averageDistance < 200) {
        rating = "Bom";
        ratingColor = "#3B82F6";
    } else if (averageDistance < 500) {
        rating = "Regular";
        ratingColor = "#F59E0B";
    } else {
        rating = "Tente novamente";
        ratingColor = "#EF4444";
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
    const shareText = `Acabei de jogar Gincaneiros no modo ${gameMode === 'local' ? 'Local' : gameMode === 'brazil' ? 'Brasil' : 'Mundo'} e marquei ${score} pontos! Tente bater meu recorde! 🌍`;
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