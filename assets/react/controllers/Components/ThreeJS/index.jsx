import { useEffect } from "react";
import * as THREE from "three";
import { GLTFLoader } from "three/addons/loaders/GLTFLoader.js";
import {OrbitControls} from "three/addons/controls/OrbitControls";

export default function monkey(width, height, ref, location, interactiveCamera=false) {
  const path = location === "header" ? "/animation/monkey_header.glb" : "/animation/monkey_footer.glb";

  const scene = new THREE.Scene();
  scene.background = null; // Fond transparent

  const light = new THREE.AmbientLight(0xffffff); // Lumière blanche
  scene.add(light);

  const camera = new THREE.PerspectiveCamera(30, width / height, 0.01, 2000);
  camera.position.set(0, 0, 6);

  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true }); // Fond transparent
  renderer.setSize(width, height);
  renderer.setAnimationLoop(animation);
  renderer.shadowMap.enabled = true; // Ombres

  if(interactiveCamera) {
    const controls = new OrbitControls( camera, renderer.domElement );
    controls.update();
  }

  let monkey;
  let pivot;

  useEffect(() => {
    ref.current.appendChild(renderer.domElement);

    const loader = new GLTFLoader();
    loader.load(path, (gltf) => {
      monkey = gltf.scene;

      const boundingBox = new THREE.Box3().setFromObject(monkey);
      const center = boundingBox.getCenter(new THREE.Vector3());
      monkey.position.sub(center);

      // Traverse through the model's children and apply a standard material with shadows
      monkey.traverse((node) => {
        if (node.isMesh) {
          node.castShadow = true;
          node.receiveShadow = true;
          // You may also set other material properties if needed, e.g., node.material = new THREE.MeshStandardMaterial({ color: 0xffffff });
        }
      });

      // Add lights for shadows
      const directionalLight = new THREE.DirectionalLight(0xffffff, 0.5);
      directionalLight.position.set(5, 5, 5);
      scene.add(directionalLight);

      const ambientLight = new THREE.AmbientLight(0x404040); // Soft white light
      scene.add(ambientLight);

      pivot = new THREE.Group();
      pivot.add(monkey);

      scene.add(pivot);
    });

    return () => {
      scene.dispose?.();
      renderer.dispose?.();
    };
  }, [renderer]);

  function animation() {
    if (pivot) {
      pivot.rotation.y += 0.01;
    }

    renderer.render(scene, camera);
  }
}
