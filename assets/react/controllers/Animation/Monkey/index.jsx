import React, { useRef, useEffect } from "react";
import * as THREE from "three";
import { GLTFLoader } from "three/addons/loaders/GLTFLoader.js";

export default function Monkey() {
  const width = 500,
    height = 500;

  const scene = new THREE.Scene();
  scene.background = null; // Fond transparent

  const light = new THREE.AmbientLight(0xffffff); // Lumière blanche
  scene.add(light);

  const camera = new THREE.PerspectiveCamera(70, width / height, 0.01, 10);
  camera.position.set(0, 0, 6);

  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true }); // Fond transparent
  renderer.setSize(width, height);
  renderer.setAnimationLoop(animation);
  renderer.shadowMap.enabled = true; // Ombres

  const container = useRef();
  let monkey;
  let pivot;

  useEffect(() => {
    container.current.appendChild(renderer.domElement);

    const loader = new GLTFLoader();
    loader.load("animation/monkey_chirurgie.glb", (gltf) => {
      monkey = gltf.scene;
      console.log(monkey);
      const boundingBox = new THREE.Box3().setFromObject(monkey);
      const center = boundingBox.getCenter(new THREE.Vector3());
      monkey.position.sub(center);

      monkey.map = THREE.LinearFilter; // Anti-aliasing

      pivot = new THREE.Group();
      pivot.add(monkey);

      scene.add(pivot);
    });

    return () => {
      scene.dispose();
      renderer.dispose();
    };
  }, [renderer]);

  function animation(time) {
    if (pivot) {
      pivot.rotation.y += 0.01;
    }
    renderer.render(scene, camera);
  }

  return <div ref={container}></div>;
}
