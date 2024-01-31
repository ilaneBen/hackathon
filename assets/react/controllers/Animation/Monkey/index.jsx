import React, { useRef, useEffect } from "react";
import * as THREE from "three";
import { GLTFLoader } from "three/addons/loaders/GLTFLoader.js";

export default function Monkey() {
  const width = window.innerWidth,
    height = window.innerHeight;

  const scene = new THREE.Scene();

  const light = new THREE.AmbientLight(0xffffff); // Lumière blanche
  scene.add(light);

  const camera = new THREE.PerspectiveCamera(70, width / height, 0.01, 10);
  camera.position.z = 5;
  camera.position.x = 5;

  const renderer = new THREE.WebGLRenderer({ antialias: true });
  renderer.setSize(width, height);
  renderer.setAnimationLoop(animation);

  const container = useRef();
  useEffect(() => {
    container.current.appendChild(renderer.domElement);

    // Charger le modèle glTF
    const loader = new GLTFLoader();
    loader.load("animation/jiggly_pudding.glb", (gltf) => {
      const monkey = gltf.scene;
      scene.add(monkey);
    });

    // Nettoyer la scène et le rendu lors du démontage du composant
    return () => {
      scene.dispose();
      renderer.dispose();
    };
  }, [renderer]);

  function animation(time) {
    // Ajouter ici vos logiques d'animation si nécessaire
    renderer.render(scene, camera);
  }

  return <div ref={container}></div>;
}
