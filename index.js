import * as THREE from "three";
import { OrbitControls } from 'jsm/controls/OrbitControls.js';
import { getFresnelMat } from "./assets/js/getFresnelMat.js";
import { CSS2DRenderer, CSS2DObject } from 'jsm/renderers/CSS2DRenderer.js';

const container = document.querySelector('.planete');

// Variables globales
let ambientLight, sunLight, cloudsMat;
let highResDayTexture = null;
let highResNightTexture = null;
let isHighResApplied = false;
let currentThemeIsDark = localStorage.getItem('theme') === 'dark'; // CACHE du thème

const highResMapUrl = "./texture/8k_earth_daymap.webp";
const highResNightMapUrl = "./texture/8k_earth_nightmap.webp";

// ON NE LANCE LA 3D QUE SI LE CONTENEUR EXISTE
if (container) {
    let targetCameraPos = new THREE.Vector3();
    let isZooming = false;
    const ZOOM_DISTANCE = 1.5;
    let cityToSearch = null;

    let w = container.clientWidth;
    let h = container.clientHeight || 500;

    // --- FONCTIONS UTILITAIRES ---
    function latLongToVector3(lat, lon, radius) {
        const phi = (90 - lat) * (Math.PI / 180);
        const theta = (lon + 180) * (Math.PI / 180);
        const x = -(radius * Math.sin(phi) * Math.cos(theta));
        const z = (radius * Math.sin(phi) * Math.sin(theta));
        const y = (radius * Math.cos(phi));
        return new THREE.Vector3(x, y, z);
    }

    // --- SETUP SCENE ---
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(25, w / h, 0.1, 1000);
    
    // Position initiale au-dessus de la France (Lat: 46.2, Lon: 2.2), à une distance de 5
    const startPos = latLongToVector3(46.2, 2.2, 5);
    // On applique la même inclinaison que la Terre (-23.4°) à la position de départ de la caméra
    startPos.applyAxisAngle(new THREE.Vector3(0, 0, 1), -23.4 * Math.PI / 180);
    camera.position.copy(startPos);

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(w, h);
    // OPTIMISATION: Limiter la résolution sur les écrans très haute densité (Retina/4K)
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2)); 
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.outputColorSpace = THREE.LinearSRGBColorSpace;
    container.appendChild(renderer.domElement);

    const labelRenderer = new CSS2DRenderer();
    labelRenderer.setSize(w, h);
    labelRenderer.domElement.style.position = 'absolute';
    labelRenderer.domElement.style.top = '0px';
    labelRenderer.domElement.style.pointerEvents = 'none';
    container.appendChild(labelRenderer.domElement);

    const earthGroup = new THREE.Group();
    earthGroup.rotation.z = -23.4 * Math.PI / 180;
    scene.add(earthGroup);

    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.enablePan = false;
    controls.enableZoom = false;
    controls.addEventListener('start', () => {
        isZooming = false;
        cityToSearch = null;
    });

    // --- MATÉRIAUX & GÉOMÉTRIE ---
    const loader = new THREE.TextureLoader();
    // OPTIMISATION: 48x48 suffit amplement et réduit considérablement la charge GPU par rapport à 64x64
    const geometry = new THREE.SphereGeometry(1, 48, 48); 
    
    // OPTIMISATION: Lambert est plus rapide à calculer que Phong (on n'a pas besoin de reflet spéculaire)
    const earthMat = new THREE.MeshLambertMaterial({
        map: loader.load("./texture/8081_earthmap2k.webp")
    });
    const earthMesh = new THREE.Mesh(geometry, earthMat);
    earthGroup.add(earthMesh);

    const lightsMat = new THREE.MeshBasicMaterial({
        map: loader.load("./texture/8081_earthlights2k.webp"),
        blending: THREE.AdditiveBlending,
        transparent: true,
    });
    const lightsMesh = new THREE.Mesh(geometry, lightsMat);
    earthGroup.add(lightsMesh);

    // OPTIMISATION: Lambert est BEAUCOUP plus rapide que Standard pour les nuages
    cloudsMat = new THREE.MeshLambertMaterial({
        map: loader.load("./texture/earthcloudmap.webp"),
        transparent: true,
        opacity: 0.8,
        blending: THREE.AdditiveBlending,
        alphaMap: loader.load('./texture/earthcloudmaptrans.webp'),
    });
    const cloudsMesh = new THREE.Mesh(geometry, cloudsMat);
    cloudsMesh.scale.setScalar(1.003);
    earthGroup.add(cloudsMesh);

    const fresnelMat = getFresnelMat();
    const glowMesh = new THREE.Mesh(geometry, fresnelMat);
    glowMesh.scale.setScalar(1.01);
    earthGroup.add(glowMesh);

    // --- LUMIÈRES ---
    sunLight = new THREE.DirectionalLight(0xffffff, 2.0);
    sunLight.position.set(-2, 0.5, 1.5);
    scene.add(sunLight);
    
    ambientLight = new THREE.AmbientLight(0xffffff, 0.2); 
    scene.add(ambientLight);

    // --- PRÉCHARGEMENT ---
    function preloadHighResTextures() {
        const bitmapLoader = new THREE.ImageBitmapLoader();
        bitmapLoader.setOptions({ imageOrientation: 'flipY' });

        let texturesLoaded = 0;

        function onTextureReady() {
            texturesLoaded++;
            // Quand les 2 textures 8K sont chargées en tâche de fond
            if (texturesLoaded === 2) {
                // OPTIMISATION CRITIQUE : Forcer le transfert vers la VRAM (Carte graphique) immédiatement
                renderer.initTexture(highResDayTexture);
                renderer.initTexture(highResNightTexture);
                
                // On applique directement la 8K de manière silencieuse sur la planète
                // Ainsi, tout est 100% prêt avant même que l'utilisateur ne clique !
                applyHighResTextures();
            }
        }

        bitmapLoader.load(highResMapUrl, (imageBitmap) => {
            highResDayTexture = new THREE.Texture(imageBitmap);
            highResDayTexture.colorSpace = THREE.LinearSRGBColorSpace;
            onTextureReady();
        });

        bitmapLoader.load(highResNightMapUrl, (imageBitmap) => {
            highResNightTexture = new THREE.Texture(imageBitmap);
            highResNightTexture.colorSpace = THREE.LinearSRGBColorSpace;
            onTextureReady();
        });
    }

    setTimeout(preloadHighResTextures, 2000);

    function applyHighResTextures() {
        if (isHighResApplied) return;
        if (highResDayTexture && highResNightTexture) {
            const oldDayMap = earthMesh.material.map;
            const oldNightMap = lightsMesh.material.map;

            earthMesh.material.map = highResDayTexture;
            earthMesh.material.needsUpdate = true;

            lightsMesh.material.map = highResNightTexture;
            lightsMesh.material.needsUpdate = true;

            if (oldDayMap) oldDayMap.dispose();
            if (oldNightMap) oldNightMap.dispose();

            isHighResApplied = true;
        } else {
            setTimeout(applyHighResTextures, 500);
        }
    }

    // --- LABELS ---
    const labelsList = [];

    function createLabel(text, lat, lon, type, rawName = '') {
        const div = document.createElement('div');
        div.className = 'planet-label';
        
        if (type === 'pays') {
            div.style.fontWeight = 'bold';
            div.style.fontSize = '14px';
        } else {
            div.style.fontSize = '11px';
            div.style.backgroundColor = 'rgba(220, 53, 69, 0.8)';
        }
        
        div.textContent = text;
        div.style.transition = 'opacity 0.3s ease'; 
        div.style.cursor = 'pointer';
        div.style.opacity = '0'; // Caché par défaut
        div.style.pointerEvents = 'none';

        const label = new CSS2DObject(div);
        const position = latLongToVector3(lat, lon, 1).multiplyScalar(1.02);
        label.position.copy(position);

        earthGroup.add(label);
        
        // On stocke l'état isVisible pour éviter les accès DOM inutiles
        labelsList.push({ labelObject: label, htmlElement: div, type: type, isVisible: false });

        div.addEventListener('click', (e) => {
            e.stopPropagation();
            if (isZooming) return; // BOUCLIER : Ignore les clics si on est déjà en train de zoomer

            applyHighResTextures();
            
            const labelWorldPos = new THREE.Vector3();
            label.getWorldPosition(labelWorldPos);
            
            // SIMPLIFICATION: La Terre est en 0,0,0. La direction depuis le centre est juste la position normalisée !
            const direction = labelWorldPos.clone().normalize();
            
            const zoomDist = (type === 'pays') ? ZOOM_DISTANCE : 1.5; 
            targetCameraPos.copy(direction.multiplyScalar(zoomDist));
            
            cityToSearch = (type === 'ville') ? rawName : null;
            isZooming = true;
            controls.enabled = false; // DESACTIVE LA ROTATION MANUELLE
        });
    }

    // --- FETCH DATA ---
    fetch('api_map.php')
        .then(response => response.json())
        .then(data => {
            data.pays.forEach(pays => createLabel(`${pays.nom} (${pays.offres})`, pays.lat, pays.lon, 'pays', pays.nom));
            data.villes.forEach(ville => createLabel(`${ville.nom} (${ville.offres})`, ville.lat, ville.lon, 'ville', ville.nom));
        })
        .catch(error => console.error("Erreur API :", error));

    let pointerDownPos = new THREE.Vector2();

    renderer.domElement.addEventListener('pointerdown', (e) => {
        if (isZooming) return; // BOUCLIER
        pointerDownPos.set(e.clientX, e.clientY);
    });

    renderer.domElement.addEventListener('pointerup', (e) => {
        if (isZooming) return; // BOUCLIER

        const distance = pointerDownPos.distanceTo(new THREE.Vector2(e.clientX, e.clientY));
        if (distance < 5) {
            const currentDirection = camera.position.clone().normalize();
            targetCameraPos.copy(currentDirection.multiplyScalar(5));
            isZooming = true;
            controls.enabled = false; // DESACTIVE LA ROTATION MANUELLE PENDANT LE DEZOOM
            cityToSearch = null;
        }
    });

    // --- OPTIMISATION CRITIQUE : Pré-allocation des vecteurs hors de la boucle ---
    // Cela empêche le Garbage Collector de geler l'écran en détruisant des milliers d'objets par seconde.
    const _labelPos = new THREE.Vector3();
    const _normal = new THREE.Vector3();
    const _viewVector = new THREE.Vector3();

    // --- OPTIMISATION : Pause au scroll ---
    let isVisible = true;
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const wasVisible = isVisible;
            isVisible = entry.isIntersecting;
            // On relance l'animation uniquement au moment où ça redevient visible
            if (isVisible && !wasVisible) {
                animate();
            }
        });
    }, { threshold: 0.05 }); // Se déclenche quand au moins 5% est visible
    
    observer.observe(renderer.domElement);

    // --- BOUCLE D'ANIMATION ---
    function animate() {
        // Stop la boucle de rendu pour économiser la batterie si on a scrollé plus bas
        if (!isVisible) return; 

        requestAnimationFrame(animate);

        if (isZooming) {
            camera.position.lerp(targetCameraPos, 0.15); 
            if (camera.position.distanceTo(targetCameraPos) < 0.05) {
                isZooming = false;
                
                // --- MODIFICATION ICI ---
                // On vérifie la distance de la caméra (par rapport au centre). 
                // Si la cible était éloignée (dézoom > 4.5), on réactive la rotation manuelle.
                // Sinon (zoom sur un pays/ville), les contrôles restent bloqués.
                if (targetCameraPos.length() > 4.5) {
                    controls.enabled = true; 
                } else {
                    controls.enabled = false; 
                }

                if (cityToSearch) {
                    window.location.href = `index.php?page=offres&ville=${encodeURIComponent(cityToSearch)}`;
                }
            }
        }

        controls.update();

        // SIMPLIFICATION: La caméra est déjà dans l'espace monde. La Terre est au centre absolu (0,0,0).
        // Plus besoin de _cameraPos et _earthPos, on utilise camera.position.length() !
        const distanceCamera = camera.position.length();
        const isZoomedIn = distanceCamera < 3.8;
    
        // Géré via le cache currentThemeIsDark au lieu d'un localStorage sync très lent
        if (lightsMesh) {
            if (currentThemeIsDark) {
                lightsMesh.material.opacity = THREE.MathUtils.lerp(1.0, 0.5, (distanceCamera - 1.5) / 3.5);
            } else {
                lightsMesh.material.opacity = 0.2;
            }
        }

        labelsList.forEach(item => {
            item.labelObject.getWorldPosition(_labelPos);
            
            // SIMPLIFICATION: La normale depuis le centre absolu (0,0,0) est juste le vecteur position
            _normal.copy(_labelPos).normalize();
            
            // Vecteur de vue entre la caméra et le label
            _viewVector.subVectors(camera.position, _labelPos).normalize();
            const dotProduct = _normal.dot(_viewVector);

            let shouldBeVisible = false;

            // Détermination de la visibilité théorique
            if (dotProduct > -0.05) {
                if (item.type === 'pays' && !isZoomedIn) {
                    shouldBeVisible = true;
                } else if (item.type === 'ville' && isZoomedIn) {
                    shouldBeVisible = true;
                }
            }

            // OPTIMISATION: Ne toucher au DOM que s'il y a un réel changement d'état
            if (item.isVisible !== shouldBeVisible) {
                item.isVisible = shouldBeVisible;
                item.htmlElement.style.opacity = shouldBeVisible ? '1' : '0';
                item.htmlElement.style.pointerEvents = shouldBeVisible ? 'auto' : 'none';
            }
        });

        renderer.render(scene, camera);
        labelRenderer.render(scene, camera);
    }

    animate();

    // --- RESIZE ---
    window.addEventListener('resize', () => {
        w = container.clientWidth;
        h = container.clientHeight || 500;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
        labelRenderer.setSize(w, h);
    }, false);

    // --- GESTION DU THÈME ---
    function updateEarthTheme(isDark) {
      currentThemeIsDark = isDark; // Mise à jour du cache
      if (!sunLight || !cloudsMat || !ambientLight) return;
      
      if (isDark) {
          sunLight.intensity = 0.4; 
          sunLight.color.setHex(0xbaccff);
          ambientLight.intensity = 1.5;    
          ambientLight.color.setHex(0x555577); 
          cloudsMat.opacity = 0.4;
      } else {
          sunLight.intensity = 0.6;        
          sunLight.color.setHex(0xfffaf0); 
          ambientLight.intensity = 1.5;    
          ambientLight.color.setHex(0xfffaf0); 
          cloudsMat.opacity = 0.8;
      }
    }

    updateEarthTheme(currentThemeIsDark);

    window.addEventListener('themeChanged', (e) => {
        updateEarthTheme(e.detail.isDark);
    });
}