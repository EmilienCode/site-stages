import * as THREE from "three";
import { OrbitControls } from 'jsm/controls/OrbitControls.js';
import { getFresnelMat } from "./assets/js/getFresnelMat.js";

const container = document.querySelector('.planete');

// 2. On déclare ces variables en dehors pour que la fonction updateEarthTheme puisse y accéder
let sunLight;
let cloudsMat;
let ambientLight;

// ON NE LANCE LA 3D QUE SI LE CONTENEUR EXISTE (Page d'accueil)
if (container) {
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

    const earthGroup = new THREE.Group();
    earthGroup.rotation.z = -23.4 * Math.PI / 180;
    scene.add(earthGroup);

    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.enablePan = false;
    controls.enableZoom = false;

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

    function animate() {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
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