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
let gameMode = 'city';
let userCity = null;
let cityBounds = null;
let currentDistance = 0;
let attempts = 1;
const maxAttempts = 5;

// Dados das capitais brasileiras
const brazilianCapitals = [
    {name: "Rio Branco", state: "Acre", code: "AC", lat: -9.97499, lng: -67.8243},
    {name: "Maceió", state: "Alagoas", code: "AL", lat: -9.66599, lng: -35.735},
    {name: "Macapá", state: "Amapá", code: "AP", lat: 0.034934, lng: -51.0694},
    {name: "Manaus", state: "Amazonas", code: "AM", lat: -3.11903, lng: -60.0217},
    {name: "Salvador", state: "Bahia", code: "BA", lat: -12.9718, lng: -38.5011},
    {name: "Fortaleza", state: "Ceará", code: "CE", lat: -3.71839, lng: -38.5434},
    {name: "Brasília", state: "Distrito Federal", code: "DF", lat: -15.7797, lng: -47.9297},
    {name: "Vitória", state: "Espírito Santo", code: "ES", lat: -20.3194, lng: -40.3378},
    {name: "Goiânia", state: "Goiás", code: "GO", lat: -16.6864, lng: -49.2643},
    {name: "São Luís", state: "Maranhão", code: "MA", lat: -2.53874, lng: -44.2825},
    {name: "Cuiabá", state: "Mato Grosso", code: "MT", lat: -15.601, lng: -56.0974},
    {name: "Campo Grande", state: "Mato Grosso do Sul", code: "MS", lat: -20.4697, lng: -54.6201},
    {name: "Belo Horizonte", state: "Minas Gerais", code: "MG", lat: -19.9167, lng: -43.9345},
    {name: "Belém", state: "Pará", code: "PA", lat: -1.4554, lng: -48.4898},
    {name: "João Pessoa", state: "Paraíba", code: "PB", lat: -7.1195, lng: -34.845},
    {name: "Curitiba", state: "Paraná", code: "PR", lat: -25.4296, lng: -49.2713},
    {name: "Recife", state: "Pernambuco", code: "PE", lat: -8.05428, lng: -34.8813},
    {name: "Teresina", state: "Piauí", code: "PI", lat: -5.08921, lng: -42.8016},
    {name: "Rio de Janeiro", state: "Rio de Janeiro", code: "RJ", lat: -22.9068, lng: -43.1729},
    {name: "Natal", state: "Rio Grande do Norte", code: "RN", lat: -5.79357, lng: -35.1986},
    {name: "Porto Alegre", state: "Rio Grande do Sul", code: "RS", lat: -30.0318, lng: -51.2065},
    {name: "Porto Velho", state: "Rondônia", code: "RO", lat: -8.76116, lng: -63.9004},
    {name: "Boa Vista", state: "Roraima", code: "RR", lat: 2.82351, lng: -60.6758},
    {name: "Florianópolis", state: "Santa Catarina", code: "SC", lat: -27.5945, lng: -48.5477},
    {name: "São Paulo", state: "São Paulo", code: "SP", lat: -23.5505, lng: -46.6333},
    {name: "Aracaju", state: "Sergipe", code: "SE", lat: -10.9472, lng: -37.0731},
    {name: "Palmas", state: "Tocantins", code: "TO", lat: -10.2499, lng: -48.3243}
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
    
    document.getElementById('cityMode').classList.remove('active');
    document.getElementById('brazilMode').classList.remove('active');
    document.getElementById('worldMode').classList.remove('active');
    document.getElementById(mode + 'Mode').classList.add('active');
    
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

                        // Melhorar com geocodificação reversa
                        const geocoder = new google.maps.Geocoder();
                        const response = await new Promise((resolve, reject) => {
                            geocoder.geocode({ location: highAccuracyLocation }, (results, status) => {
                                status === 'OK' ? resolve(results) : reject(status);
                            });
                        });

                        // Extrair cidade e estado
                        for (const component of response[0].address_components) {
                            if (component.types.includes('locality')) {
                                userCity = component.long_name;
                            } else if (component.types.includes('administrative_area_level_1')) {
                                userState = component.long_name;
                            }
                        }

                        // Definir área de busca (20km ao redor)
                        cityBounds = new google.maps.LatLngBounds();
                        const radius = 0.18; // ~20km
                        cityBounds.extend(new google.maps.LatLng(
                            highAccuracyLocation.lat - radius, 
                            highAccuracyLocation.lng - radius
                        ));
                        cityBounds.extend(new google.maps.LatLng(
                            highAccuracyLocation.lat + radius, 
                            highAccuracyLocation.lng + radius
                        ));

                        // Atualizar UI
                        if (userCity) {
                            document.getElementById('cityMode').innerHTML = `
                                <i class="fas fa-city mr-1"></i> ${userCity}
                            `;
                        }

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

function showCityMenu() {
    const menuHtml = `
        <div id="cityMenu" class="shadow-lg">
            ${brazilianCapitals.map(city => `
                <div onclick="selectCity('${city.name}', '${city.state}', ${city.lat}, ${city.lng})">
                    <i class="fas fa-city text-blue-500 mr-2"></i>
                    ${city.name} - ${city.state}
                </div>
            `).join('')}
        </div>
    `;
    
    const existingMenu = document.getElementById('cityMenu');
    if (existingMenu) existingMenu.remove();
    
    const cityButton = document.getElementById('cityMode');
    cityButton.insertAdjacentHTML('afterend', menuHtml);
    
    setTimeout(() => {
        document.addEventListener('click', closeCityMenu);
    }, 100);
}

function closeCityMenu(e) {
    const cityMenu = document.getElementById('cityMenu');
    const cityButton = document.getElementById('cityMode');
    
    if (cityMenu && e.target !== cityButton && !cityButton.contains(e.target)) {
        cityMenu.remove();
        document.removeEventListener('click', closeCityMenu);
    }
}

function selectCity(cityName, stateName, lat, lng) {
    userCity = cityName;
    document.getElementById('cityMode').innerHTML = `
        <i class="fas fa-city mr-1"></i> ${cityName}
    `;
    document.getElementById('cityMenu').remove();
    
    // Definir área de busca (20km ao redor da capital)
    cityBounds = new google.maps.LatLngBounds();
    const radius = 0.18; // ~20km
    cityBounds.extend(new google.maps.LatLng(lat - radius, lng - radius));
    cityBounds.extend(new google.maps.LatLng(lat + radius, lng + radius));
    
    resetGame();
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
        const MAX_ATTEMPTS = 20;
        let attempts = 0;
        let lastError = null;
    
        while (attempts < MAX_ATTEMPTS) {
            try {
                // 1. Modo Cidade (GPS) - Lógica Aprimorada
                if (gameMode === 'city' && cityBounds) {
                    return await getCityLocation();
                }
    
                // 2. Outros Modos (Brasil/Estado/Mundo)
                const { lat, lng } = getCoordinatesByMode();
                return await processLocation(lat, lng);
    
            } catch (error) {
                lastError = error;
                attempts++;
                console.warn(`Attempt ${attempts}/${MAX_ATTEMPTS} failed:`, error);
                await new Promise(resolve => setTimeout(resolve, 100 + (attempts * 50))); // Backoff progressivo
            }
        }
    
        // 3. Fallback Controlado
        return getFallbackLocation(lastError);
    }

    async function getCityLocation() {
        // Estratégia 1: Busca por lugares específicos (restaurantes, pontos turísticos)
        try {
            const places = await fetchQualityPlaces();
            const randomPlace = selectRandomPlace(places);
            return await validateAndRegisterLocation(randomPlace);
        } catch (error) {
            console.warn("Place search failed, trying random street view:", error);
            
            // Estratégia 2: Busca aleatória dentro dos bounds
            const randomCoords = generateRandomCoordinates(cityBounds);
            return await validateAndRegisterLocation(randomCoords);
        }
    }

    async function fetchQualityPlaces() {
        const center = cityBounds.getCenter();
        const places = await getNearbyPlaces(center.lat(), center.lng(), 20000);
        
        return places.filter(place => 
            place.photos?.length > 0 &&
            place.rating >= 3.5 &&
            place.user_ratings_total >= 10 &&
            place.geometry &&
            cityBounds.contains(place.geometry.location) &&
            !isLocationUsed(place.geometry.location.lat(), place.geometry.location.lng())
        );
    }
    
    function selectRandomPlace(places) {
        if (places.length === 0) throw new Error("No valid places available");
        const randomPlace = places[Math.floor(Math.random() * places.length)];
        return {
            lat: randomPlace.geometry.location.lat(),
            lng: randomPlace.geometry.location.lng(),
            name: randomPlace.name
        };
    }

    async function validateAndRegisterLocation(location) {
        const panoramaData = await getPanoramaData(
            location.lat, 
            location.lng,
            gameMode === 'city' ? 100 : 50000
        );
    
        // Verificação extra para modo cidade
        if (gameMode === 'city' && !cityBounds.contains(panoramaData.location.latLng)) {
            throw new Error("Location outside city bounds");
        }
    
        const locationName = await getLocationName(panoramaData, location);
        registerUsedLocation(panoramaData);
    
        return {
            lat: panoramaData.location.latLng.lat(),
            lng: panoramaData.location.latLng.lng(),
            name: locationName
        };
    }

    async function getLocationName(panoramaData, originalLocation) {
        return originalLocation.name || 
               await getCityName(
                   panoramaData.location.latLng.lat(),
                   panoramaData.location.latLng.lng()
               );
    }
    
    function registerUsedLocation(panoramaData) {
        // Limitar o cache para evitar memory leak
        if (usedLocations.length > 100) {
            usedLocations = usedLocations.slice(-50);
        }
        
        usedLocations.push({
            lat: panoramaData.location.latLng.lat(),
            lng: panoramaData.location.latLng.lng()
        });
    }

    function getFallbackLocation(error) {
        console.error("Fallback triggered:", error);
        
        if (gameMode === 'city' && cityBounds) {
            const coords = generateRandomCoordinates(cityBounds);
            return {
                ...coords,
                name: `Local Aleatório (${coords.lat.toFixed(4)}, ${coords.lng.toFixed(4)})`
            };
        }
        
        const coords = getCoordinatesByMode();
        return {
            ...coords,
            name: `Local Aleatório (${coords.lat.toFixed(4)}, ${coords.lng.toFixed(4)})`
        };
    }

    function getCoordinatesByMode() {
        switch (gameMode) {
            case 'state': return getRandomStateLocation();
            case 'brazil': return getRandomBrazilLocation();
            case 'world': return getRandomWorldLocation();
            default: throw new Error("Modo de jogo inválido");
        }
    }
    
    function generateRandomCoordinates(bounds) {
        return {
            lat: bounds.getSouthWest().lat() + Math.random() * 
                (bounds.getNorthEast().lat() - bounds.getSouthWest().lat()),
            lng: bounds.getSouthWest().lng() + Math.random() * 
                (bounds.getNorthEast().lng() - bounds.getSouthWest().lng())
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
        if (userCity) {
            gameMode = 'city';
            document.getElementById('cityMode').classList.add('active');
        } else {
            // Se não detectar cidade, mostra capitais como fallback
            gameMode = 'city';
            document.getElementById('cityMode').classList.add('active');
        }
    } catch (error) {
        console.error("Could not detect user location:", error);
        // Fallback to Brazil if can't detect state
        gameMode = 'brazil';
        document.getElementById('brazilMode').classList.add('active');
        document.getElementById('cityMode').classList.remove('active');
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

    // Se acertou (ex.: < 1 km)
    if (currentDistance < 1) {
        handleRoundResult(true);
        return;
    }

    // Se ainda tem tentativas
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
    // Normaliza o ângulo para 0-360
    const normalizedHeading = (heading + 360) % 360;
    
    // Determina a direção (0=Norte, 45=Nordeste, etc.)
    const directionIndex = Math.round(normalizedHeading / 45) % 8;
    
    // URLs das imagens GIF das setas
    const directionArrows = [
        'https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExeGh2Zjl5bzA4ejZuNHp2eDl5eGxhZm9xaDd6YzJmdjRmd3p0c2IweiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/KDhyTMJgCWMsBlUDSq/giphy.gif', // Norte ↑
        'https://media0.giphy.com/media/v1.Y2lkPTc5MGI3NjExcTc2dzZ2a3Npa2ljMjNpaWpqbzByZnJhOXN6bzljZnZiNHN5NnV0eSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/uK0YJtha6r7PzsLAwM/giphy.gif', // Nordeste ↗
        'https://media2.giphy.com/media/v1.Y2lkPTc5MGI3NjExeHk1OHcwd3NmZTFpN2lraHVpaTc3dmdvNjF2azh0ODg3aHgzbmN6NSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/CZRaIR4t4yOVGlBsXM/giphy.gif', // Leste →
        'https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExdHlkZmI3ZmdsOHJzMXhnM3AwamRzeDNjd2UzM25uM2Q1ZHo3bWo3MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/Yn5UkZDYJiwCBoaQDN/giphy.gif', // Sudeste ↘
        'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExdnltbWJyMmV2NWJjMTI3dG95aDRqdHozZHF2Z2ozeHhsZ3c4ZDlhayZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/cP53N7RJHVndLfPxdS/giphy.gif', // Sul ↓
        'https://media3.giphy.com/media/v1.Y2lkPTc5MGI3NjExdzBxcm5oajFhN3Z3eG1pajN0MG45bnJ2OXEwZ2xlejFiNjhkMHExYiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/Q7XPCjjc3KMwaTCbLQ/giphy.gif', // Sudoeste ↙
        'https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExYXJidnRkcmh4dHBvdGU1eGhyeXk0MzN4cXhqb2c0em83Ynp5OGN1MyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/ifER5WV3JrB8iKso8Y/giphy.gif', // Oeste ←
        'https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExY3oxcGgzNWtndGYzYmhxejdvZGR6dm52bTd2bmVwaHkzNmpkb3N6ZiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/hU4d5hXTglhL62Tia3/giphy.gif'  // Noroeste ↖
    ];
    
    return directionArrows[directionIndex];
}

function handleRoundResult(isCorrect) {
    const points = calculatePoints(currentDistance, attempts);
    score += Math.round(points);
    document.getElementById("score").textContent = score;

    // Mostra o resultado final da rodada
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
        attempts = 1; // Reseta tentativas para a próxima rodada
        newRound();
    });
}

function calculatePoints(distance, attemptsUsed) {
    const basePoints = Math.max(0, 5000 - Math.floor(distance)) / 100;
    // Penalidade de 20% por tentativa extra
    const penalty = (attemptsUsed - 1) * 0.2;
    return basePoints * (1 - penalty);
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
        
        // Resetar apenas para o modo cidade
        if (gameMode === 'city') {
            usedLocations = [];
        }
        
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