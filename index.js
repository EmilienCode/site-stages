import * as THREE from "three";
import { OrbitControls } from 'jsm/controls/OrbitControls.js';
import { getFresnelMat } from "./assets/js/getFresnelMat.js";
import { CSS2DRenderer, CSS2DObject } from 'jsm/renderers/CSS2DRenderer.js';

const container = document.querySelector('.planete');

// 2. On déclare ces variables en dehors pour que la fonction updateEarthTheme puisse y accéder
let ambientLight;

// ON NE LANCE LA 3D QUE SI LE CONTENEUR EXISTE (Page d'accueil)
if (container) {
    let targetCameraPos = new THREE.Vector3();
    let isZooming = false;
    const ZOOM_DISTANCE = 2.0;
    const defaultCameraPos = new THREE.Vector3(0, 0, 5);

    let w = container.clientWidth;
    let h = container.clientHeight || 500;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(25, w / h, 0.1, 1000);
    camera.position.z = 5;

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(w, h);
    container.appendChild(renderer.domElement);

    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.outputColorSpace = THREE.LinearSRGBColorSpace;

    //Configuration du CSS2DRenderer
    const labelRenderer = new CSS2DRenderer();
    labelRenderer.setSize(w, h);
    labelRenderer.domElement.style.position = 'absolute';
    labelRenderer.domElement.style.top = '0px';
    labelRenderer.domElement.style.pointerEvents = 'none'; // Laisse passer la souris pour l'OrbitControls en dessous
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
    });

    const loader = new THREE.TextureLoader();
    const geometry = new THREE.SphereGeometry(1, 64, 64);
    
    const material = new THREE.MeshPhongMaterial({
        map: loader.load("./texture/8081_earthmap2k.jpg"),
        specularMap: loader.load("./texture/8081_earthspec2k.jpg"),
        bumpMap: loader.load("./texture/8081_earthbump1k.jpg"),
        bumpScale: 0.04,
    });

    const earthMesh = new THREE.Mesh(geometry, material);
    earthGroup.add(earthMesh);

    const lightsMat = new THREE.MeshBasicMaterial({
        map: loader.load("./texture/8081_earthlights2k.jpg"),
        blending: THREE.AdditiveBlending,
    });
    const lightsMesh = new THREE.Mesh(geometry, lightsMat);
    earthGroup.add(lightsMesh);

    const cloudsMat = new THREE.MeshStandardMaterial({
        map: loader.load("./texture/earthcloudmap.jpg"),
        transparent: true,
        opacity: 0.8,
        blending: THREE.AdditiveBlending,
        alphaMap: loader.load('./texture/earthcloudmaptrans.jpg'),
    });
    const cloudsMesh = new THREE.Mesh(geometry, cloudsMat);
    cloudsMesh.scale.setScalar(1.003);
    earthGroup.add(cloudsMesh);

    const fresnelMat = getFresnelMat();
    const glowMesh = new THREE.Mesh(geometry, fresnelMat);
    glowMesh.scale.setScalar(1.01);
    earthGroup.add(glowMesh);

    const sunLight = new THREE.DirectionalLight(0xffffff, 2.0);
    sunLight.position.set(-2, 0.5, 1.5);
    scene.add(sunLight);
    ambientLight = new THREE.AmbientLight(0xffffff, 0.2); 
    scene.add(ambientLight);

    //Fonction mathématique pour placer les points
    function latLongToVector3(lat, lon, radius) {
        // La géométrie standard Three.js a le Y en haut. 
        // On ajuste les angles pour correspondre au mapping UV de la texture de la Terre.
        const phi = (90 - lat) * (Math.PI / 180);
        const theta = (lon + 180) * (Math.PI / 180);

        const x = -(radius * Math.sin(phi) * Math.cos(theta));
        const z = (radius * Math.sin(phi) * Math.sin(theta));
        const y = (radius * Math.cos(phi));

        return new THREE.Vector3(x, y, z);
    }

    const labelsList = [];

    function createLabel(text, lat, lon) {
        const div = document.createElement('div');
        div.className = 'planet-label';
        div.textContent = text;
        // --- NOUVEAU : Une transition douce pour l'apparition/disparition ---
        div.style.transition = 'opacity 0.3s ease'; 

        const label = new CSS2DObject(div);
        const position = latLongToVector3(lat, lon, 1).multiplyScalar(1.02);
        label.position.copy(position);

        earthGroup.add(label);

        labelsList.push({ labelObject: label, htmlElement: div });

        div.addEventListener('click', (e) => {
            e.stopPropagation(); // Empêche le clic de traverser l'étiquette et de toucher le canvas en dessous

            const labelWorldPos = new THREE.Vector3();
            label.getWorldPosition(labelWorldPos);

            const earthCenter = new THREE.Vector3();
            earthGroup.getWorldPosition(earthCenter);

            const direction = new THREE.Vector3().subVectors(labelWorldPos, earthCenter).normalize();
            targetCameraPos.copy(earthCenter).add(direction.multiplyScalar(ZOOM_DISTANCE));

            isZooming = true;
        });
    }

    //Ajout villes et pays
    createLabel('France', 45, 2.3522);
    createLabel('Etats Unis', 40.7128, -84.0060);
    createLabel('Japon', 35.6895, 139.6917);
    createLabel('Australie', -28.8688, 135.2093);

    // --- NOUVEAU : Détecter un clic (et non un drag) pour dézoomer ---
    let pointerDownPos = new THREE.Vector2();

    // Quand on appuie sur le clic
    renderer.domElement.addEventListener('pointerdown', (e) => {
        pointerDownPos.set(e.clientX, e.clientY);
    });

    // Quand on relâche le clic
    renderer.domElement.addEventListener('pointerup', (e) => {
        const pointerUpPos = new THREE.Vector2(e.clientX, e.clientY);
        
        // On calcule la distance parcourue par la souris pendant le clic
        const distance = pointerDownPos.distanceTo(pointerUpPos);

        // Si la distance est très petite (moins de 5 pixels), c'est un vrai clic, pas une rotation
        if (distance < 5) {
            // On renvoie la caméra à sa position de départ
            targetCameraPos.copy(defaultCameraPos);
            isZooming = true;
        }
    });

    function animate() {
        requestAnimationFrame(animate);

        if (isZooming) {
            // La caméra glisse vers la cible (0.05 est la vitesse/fluidité du mouvement)
            camera.position.lerp(targetCameraPos, 0.05);

            // Si la caméra est arrivée très près de la cible, on arrête le lerp
            if (camera.position.distanceTo(targetCameraPos) < 0.05) {
                isZooming = false;
            }
        }

        controls.update();
        // Récupérer la position exacte de la caméra dans le monde 3D
        const cameraPos = new THREE.Vector3();
        camera.getWorldPosition(cameraPos);

        // Récupérer la position exacte du centre de la Terre
        const earthPos = new THREE.Vector3();
        earthGroup.getWorldPosition(earthPos);

        labelsList.forEach(item => {
            // Récupérer la position exacte de cette étiquette
            const labelPos = new THREE.Vector3();
            item.labelObject.getWorldPosition(labelPos);

            // Vecteur A : Du centre de la Terre vers l'étiquette
            const normal = labelPos.clone().sub(earthPos).normalize();
            
            // Vecteur B : De l'étiquette vers la caméra
            const viewVector = cameraPos.clone().sub(labelPos).normalize();

            // Le fameux produit scalaire
            const dotProduct = normal.dot(viewVector);

            // Si dotProduct est > 0, l'étiquette est sur l'hémisphère face à nous
            // On utilise -0.1 (au lieu de 0) pour cacher l'étiquette un poil *avant* l'horizon brutal
            if (dotProduct > -0.05) {
                item.htmlElement.style.opacity = '1';
                item.htmlElement.style.pointerEvents = 'auto'; // Rendre cliquable
            } else {
                item.htmlElement.style.opacity = '0';
                item.htmlElement.style.pointerEvents = 'none'; // Désactiver le clic quand c'est caché
            }
        });
        renderer.render(scene, camera);
        labelRenderer.render(scene, camera);
    }

    animate();

    function handleWindowResize() {
        w = container.clientWidth;
        h = container.clientHeight || 500;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    }
    window.addEventListener('resize', handleWindowResize, false);

    // --- GESTION DU THÈME SOMBRE DE LA PLANÈTE ---
    
    function updateEarthTheme(isDark) {
      // SÉCURITÉ : On vérifie que nos lumières existent
      if (!sunLight || !cloudsMat || !ambientLight) return;

      if (isDark) {
          // --- MODE SOMBRE ---
          // Soleil : Doux et bleuté (façon clair de lune)
          sunLight.intensity = 0.8; 
          sunLight.color.setHex(0x99aaff); 
          
          // Ambiance : Presque éteinte pour laisser briller les "lumières des villes" (lightsMesh)
          ambientLight.intensity = 0.1;    
          ambientLight.color.setHex(0x222244); 
          
          // Nuages : Plus transparents la nuit
          cloudsMat.opacity = 0.3;
      } else {
          // --- MODE CLAIR ---
          // Soleil : Baissé à 1.5 (au lieu de 2.5) pour éviter l'effet flashbang
          sunLight.intensity = 0.8;        
          sunLight.color.setHex(0x8a9ec2); 
          
          // Ambiance : Montée à 0.6 pour bien éclairer la face cachée de la Terre
          ambientLight.intensity = 0.8;    
          // Couleur : Un bleu/gris clair très doux pour simuler la lumière de l'espace/atmosphère
          ambientLight.color.setHex(0xffffff); 
          
          // Nuages : Bien visibles
          cloudsMat.opacity = 0.7;
      }
  }

    // 1. On l'applique au chargement initial
    const themeSauvegarde = localStorage.getItem('theme');
    updateEarthTheme(themeSauvegarde === 'dark');

    // 2. On écoute le changement de thème déclenché par le bouton
    window.addEventListener('themeChanged', function(e) {
        updateEarthTheme(e.detail.isDark);
    });
}