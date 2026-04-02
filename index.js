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

const highResMapUrl = "./texture/8k_earth_daymap.webp";
const highResNightMapUrl = "./texture/8k_earth_nightmap.webp";

// ON NE LANCE LA 3D QUE SI LE CONTENEUR EXISTE
if (container) {
    let targetCameraPos = new THREE.Vector3();
    let isZooming = false;
    const ZOOM_DISTANCE = 2.0;
    let cityToSearch = null;

    let w = container.clientWidth;
    let h = container.clientHeight || 500;

    // --- SETUP SCENE ---
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(25, w / h, 0.1, 1000);
    camera.position.z = 5;

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(w, h);
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.outputColorSpace = THREE.LinearSRGBColorSpace;
    container.appendChild(renderer.domElement);

    // Configuration du CSS2DRenderer pour les labels HTML
    const labelRenderer = new CSS2DRenderer();
    labelRenderer.setSize(w, h);
    labelRenderer.domElement.style.position = 'absolute';
    labelRenderer.domElement.style.top = '0px';
    labelRenderer.domElement.style.pointerEvents = 'none';
    container.appendChild(labelRenderer.domElement);

    // Groupe principal
    const earthGroup = new THREE.Group();
    earthGroup.rotation.z = -23.4 * Math.PI / 180;
    scene.add(earthGroup);

    // Contrôles
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.enablePan = false;
    controls.enableZoom = false;
    controls.addEventListener('start', () => {
        isZooming = false;
        cityToSearch = null;
    });

    // --- MATÉRIAUX & GÉOMÉTRIE (ALLÉGÉS) ---
    const loader = new THREE.TextureLoader();
    const geometry = new THREE.SphereGeometry(1, 64, 64);
    
    // Matériel Terre (sans bump ni specular pour optimiser)
    const earthMat = new THREE.MeshPhongMaterial({
        map: loader.load("./texture/8081_earthmap2k.webp")
    });
    const earthMesh = new THREE.Mesh(geometry, earthMat);
    earthGroup.add(earthMesh);

    // Matériel Lumières de Nuit
    const lightsMat = new THREE.MeshBasicMaterial({
        map: loader.load("./texture/8081_earthlights2k.webp"),
        blending: THREE.AdditiveBlending,
        transparent: true,
    });
    const lightsMesh = new THREE.Mesh(geometry, lightsMat);
    earthGroup.add(lightsMesh);

    // Matériel Nuages
    cloudsMat = new THREE.MeshStandardMaterial({
        map: loader.load("./texture/earthcloudmap.webp"),
        transparent: true,
        opacity: 0.8,
        blending: THREE.AdditiveBlending,
        alphaMap: loader.load('./texture/earthcloudmaptrans.webp'),
    });
    const cloudsMesh = new THREE.Mesh(geometry, cloudsMat);
    cloudsMesh.scale.setScalar(1.003);
    earthGroup.add(cloudsMesh);

    // Effet Atmosphère (Fresnel)
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

    // --- PRÉCHARGEMENT ASYNCHRONE DES TEXTURES HD ---
    // Off-thread decoding : Prévient le freeze du thread principal.
    function preloadHighResTextures() {
        const bitmapLoader = new THREE.ImageBitmapLoader();
        bitmapLoader.setOptions({ imageOrientation: 'flipY' }); // Indispensable pour ThreeJS

        bitmapLoader.load(highResMapUrl, (imageBitmap) => {
            highResDayTexture = new THREE.Texture(imageBitmap);
            highResDayTexture.colorSpace = THREE.LinearSRGBColorSpace;
            highResDayTexture.needsUpdate = true;
        });

        bitmapLoader.load(highResNightMapUrl, (imageBitmap) => {
            highResNightTexture = new THREE.Texture(imageBitmap);
            highResNightTexture.colorSpace = THREE.LinearSRGBColorSpace;
            highResNightTexture.needsUpdate = true;
        });
    }

    // On lance le préchargement en arrière-plan 2 secondes après l'initialisation
    // pour ne pas ralentir le chargement initial de la page web.
    setTimeout(preloadHighResTextures, 2000);

    // --- APPLICATION DES TEXTURES HD (AU CLIC) ---
    function applyHighResTextures() {
        if (isHighResApplied) return;
        
        if (highResDayTexture && highResNightTexture) {
            // Sauvegarde des anciennes textures pour les nettoyer de la VRAM
            const oldDayMap = earthMesh.material.map;
            const oldNightMap = lightsMesh.material.map;

            earthMesh.material.map = highResDayTexture;
            earthMesh.material.needsUpdate = true;

            lightsMesh.material.map = highResNightTexture;
            lightsMesh.material.needsUpdate = true;

            // Libération de la mémoire de la carte graphique
            if (oldDayMap) oldDayMap.dispose();
            if (oldNightMap) oldNightMap.dispose();

            isHighResApplied = true;
            console.log("Textures HD appliquées instantanément sans freeze.");
        } else {
            // Si l'utilisateur clique trop vite avant la fin du preload, on retente
            console.log("Les textures HD chargent encore, on retente...");
            setTimeout(applyHighResTextures, 500);
        }
    }

    // --- UTILITAIRES ET LOGIQUE DES LABELS ---
    function latLongToVector3(lat, lon, radius) {
        const phi = (90 - lat) * (Math.PI / 180);
        const theta = (lon + 180) * (Math.PI / 180);
        const x = -(radius * Math.sin(phi) * Math.cos(theta));
        const z = (radius * Math.sin(phi) * Math.sin(theta));
        const y = (radius * Math.cos(phi));
        return new THREE.Vector3(x, y, z);
    }

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

        const label = new CSS2DObject(div);
        const position = latLongToVector3(lat, lon, 1).multiplyScalar(1.02);
        label.position.copy(position);

        earthGroup.add(label);
        labelsList.push({ labelObject: label, htmlElement: div, type: type });

        div.addEventListener('click', (e) => {
            e.stopPropagation();
            
            // On applique la texture HD lors du premier clic
            applyHighResTextures();
            
            const labelWorldPos = new THREE.Vector3();
            label.getWorldPosition(labelWorldPos);
            const earthCenter = new THREE.Vector3();
            earthGroup.getWorldPosition(earthCenter);
            const direction = new THREE.Vector3().subVectors(labelWorldPos, earthCenter).normalize();
            
            const zoomDist = (type === 'pays') ? ZOOM_DISTANCE : 1.5; 
            targetCameraPos.copy(earthCenter).add(direction.multiplyScalar(zoomDist));
            
            cityToSearch = (type === 'ville') ? rawName : null;
            isZooming = true;
        });
    }

    // --- FETCH DATA ---
    fetch('api_map.php')
        .then(response => response.json())
        .then(data => {
            data.pays.forEach(pays => {
                createLabel(`${pays.nom} (${pays.offres})`, pays.lat, pays.lon, 'pays', pays.nom);
            });
            data.villes.forEach(ville => {
                createLabel(`${ville.nom} (${ville.offres})`, ville.lat, ville.lon, 'ville', ville.nom);
            });
        })
        .catch(error => console.error("Erreur API :", error));

    // --- GESTION DES CLICS VS DRAG ---
    let pointerDownPos = new THREE.Vector2();

    renderer.domElement.addEventListener('pointerdown', (e) => {
        pointerDownPos.set(e.clientX, e.clientY);
    });

    renderer.domElement.addEventListener('pointerup', (e) => {
        const distance = pointerDownPos.distanceTo(new THREE.Vector2(e.clientX, e.clientY));
        if (distance < 5) {
            const currentDirection = camera.position.clone().normalize();
            targetCameraPos.copy(currentDirection.multiplyScalar(5));
            isZooming = true;
            cityToSearch = null;
        }
    });

    // --- BOUCLE D'ANIMATION ---
    function animate() {
        requestAnimationFrame(animate);

        if (isZooming) {
            camera.position.lerp(targetCameraPos, 0.15); 
            if (camera.position.distanceTo(targetCameraPos) < 0.05) {
                isZooming = false;
                if (cityToSearch) {
                    window.location.href = `index.php?page=offres&ville=${encodeURIComponent(cityToSearch)}`;
                }
            }
        }

        controls.update();

        const cameraPos = new THREE.Vector3();
        camera.getWorldPosition(cameraPos);
        const earthPos = new THREE.Vector3();
        earthGroup.getWorldPosition(earthPos);
 
        const distanceCamera = camera.position.distanceTo(earthPos);
        const isZoomedIn = distanceCamera < 3.8;
    
        if (lightsMesh) {
            const isDark = localStorage.getItem('theme') === 'dark';
            if (isDark) {
                lightsMesh.material.opacity = THREE.MathUtils.lerp(1.0, 0.5, (distanceCamera - 1.5) / 3.5);
            } else {
                lightsMesh.material.opacity = 0.2;
            }
        }

        labelsList.forEach(item => {
            const labelPos = new THREE.Vector3();
            item.labelObject.getWorldPosition(labelPos);
            const normal = labelPos.clone().sub(earthPos).normalize();
            const viewVector = cameraPos.clone().sub(labelPos).normalize();
            const dotProduct = normal.dot(viewVector);

            if (dotProduct > -0.05) {
                if (item.type === 'pays' && !isZoomedIn) {
                    item.htmlElement.style.opacity = '1';
                    item.htmlElement.style.pointerEvents = 'auto';
                } else if (item.type === 'ville' && isZoomedIn) {
                    item.htmlElement.style.opacity = '1';
                    item.htmlElement.style.pointerEvents = 'auto';
                } else {
                    item.htmlElement.style.opacity = '0';
                    item.htmlElement.style.pointerEvents = 'none';
                }
            } else {
                item.htmlElement.style.opacity = '0';
                item.htmlElement.style.pointerEvents = 'none';
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
      if (!sunLight || !cloudsMat || !ambientLight) return;
      if (isDark) {
          // Mode sombre : Lumière générale plus forte, soleil plus doux
          sunLight.intensity = 0.4; 
          sunLight.color.setHex(0xbaccff);
          ambientLight.intensity = 1.5;    // Éclaire uniformément la planète
          ambientLight.color.setHex(0x555577); // Garde une teinte bleutée/nuit
          cloudsMat.opacity = 0.4;
      } else {
          // Mode clair : Ambiance dominante très forte pour éviter la "tache" claire (hotspot)
          sunLight.intensity = 0.6;        // Soleil réduit fortement
          sunLight.color.setHex(0xfffaf0); 
          ambientLight.intensity = 1.5;    // Lumière globale fortement augmentée (luminosité homogène)
          ambientLight.color.setHex(0xfffaf0); 
          cloudsMat.opacity = 0.8;
      }
    }

    const themeSauvegarde = localStorage.getItem('theme');
    updateEarthTheme(themeSauvegarde === 'dark');

    window.addEventListener('themeChanged', (e) => {
        updateEarthTheme(e.detail.isDark);
    });
}