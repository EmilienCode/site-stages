import * as THREE from "three";
import { OrbitControls } from 'jsm/controls/OrbitControls.js';

import { getFresnelMat } from "./assets/js/getFresnelMat.js";

// 1. Cibler le conteneur en premier pour obtenir ses dimensions
const container = document.querySelector('.planete');
let w = container.clientWidth;
let h = container.clientHeight || 500; // Hauteur par défaut si 0

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(23.5, w / h, 0.1, 1000);
camera.position.z = 5;

// alpha: true permet au fond du canvas d'être transparent pour mieux s'intégrer
const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
renderer.setSize(w, h);
container.appendChild(renderer.domElement);

renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.outputColorSpace = THREE.LinearSRGBColorSpace;

const earthGroup = new THREE.Group();
earthGroup.rotation.z = -23.4 * Math.PI / 180;
scene.add(earthGroup);

// 2. Configuration d'OrbitControls pour une meilleure intégration
const controls = new OrbitControls(camera, renderer.domElement);
controls.enableDamping = true; // Rend la rotation manuelle fluide et agréable
controls.dampingFactor = 0.05;
controls.enablePan = false; // Empêche de décaler la planète sur le côté
controls.enableZoom = false; // Désactive le zoom molette pour ne pas gêner le scroll de la page

const detail = 12;
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

function animate() {
  requestAnimationFrame(animate);

  // 3. Les lignes de rotation automatique ont été supprimées ici 
  // pour que la planète soit fixe de base.

  controls.update(); // Nécessaire pour le "enableDamping"
  renderer.render(scene, camera);
}

animate();

function handleWindowResize () {
  // 4. Mettre à jour avec les dimensions du conteneur parent au lieu du window
  w = container.clientWidth;
  h = container.clientHeight || 500;
  
  camera.aspect = w / h;
  camera.updateProjectionMatrix();
  renderer.setSize(w, h);
}
window.addEventListener('resize', handleWindowResize, false);

function updateEarthTheme(isDark) {
    // 1. Ajuster l'intensité du soleil
    // Mode sombre : lumière plus douce/bleutée, Mode clair : pleine puissance
    sunLight.intensity = isDark ? 0.7 : 2.5;
    sunLight.color.setHex(isDark ? 0xccccff : 0xffffff);

    // 2. Optionnel : Ajuster l'opacité des nuages pour qu'ils soient moins "blancs" la nuit
    cloudsMat.opacity = isDark ? 0.4 : 0.8;
}