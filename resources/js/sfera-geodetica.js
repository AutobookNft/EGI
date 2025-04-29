// Importa Three.js e componenti necessari come moduli ES6
import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';

// Funzione di inizializzazione che verrà esportata
export function initThreeAnimation() {
  // Wait for everything to load
  window.addEventListener('load', () => {
    // Get references to DOM elements
    const container = document.getElementById('dynamic-3d-container');
    const canvas = document.getElementById('webgl-canvas');
    const loading = document.getElementById('loading');

    // Scene setup
    const scene = new THREE.Scene();

    // Create a better, more immersive camera
    const width = container.clientWidth;
    const height = container.clientHeight;
    const camera = new THREE.PerspectiveCamera(70, width / height, 0.1, 1000);
    camera.position.z = 5;

    // Stato dell'interazione con il mouse
    const mouse = new THREE.Vector2();
    const prevMouse = new THREE.Vector2();
    let isMouseDown = false;
    let mouseDownTime = 0;

    // Default rotazione automatica
    let autoRotate = true;
    const autoRotationSpeed = 0.3;

    // Rotazione manuale
    const manualRotation = new THREE.Vector2(0, 0);

    // Create a high-quality renderer
    const renderer = new THREE.WebGLRenderer({
      canvas,
      antialias: true,
      alpha: true,
      powerPreference: "high-performance"
    });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0x000000);

    // Add dramatic lighting
    const ambientLight = new THREE.AmbientLight(0x222222);
    scene.add(ambientLight);

    // Multiple colorful point lights
    const colors = [0x8A2BE2, 0xFF1493, 0x00BFFF, 0x7B68EE];
    const lights = [];

    for (let i = 0; i < 4; i++) {
      const light = new THREE.PointLight(colors[i], 1, 10);
      const angle = (i / 4) * Math.PI * 2;
      light.position.set(
        Math.cos(angle) * 3,
        Math.sin(angle) * 3,
        2
      );
      scene.add(light);
      lights.push(light);
    }

    // Create a beautiful crystalline central object
    const geometries = [
      new THREE.IcosahedronGeometry(1, 1),
      new THREE.OctahedronGeometry(1, 2)
    ];

    // Create a iridescent material with color shifts
    const material = new THREE.MeshPhysicalMaterial({
      color: 0xffffff,
      metalness: 0.9,
      roughness: 0.1,
      iridescence: 0.8,
      iridescenceIOR: 1,
      envMapIntensity: 1,
      transmission: 0.9,
      reflectivity: 1,
      clearcoat: 0.8,
      clearcoatRoughness: 0.2,
      transparent: true,
      opacity: 0.9
    });

    // Create a group for our central NFT token
    const tokenGroup = new THREE.Group();
    scene.add(tokenGroup);

    // Add two overlapping geometries for interesting visual effect
    const mainObject1 = new THREE.Mesh(geometries[0], material);
    const mainObject2 = new THREE.Mesh(geometries[1], material);
    mainObject2.scale.set(0.8, 0.8, 0.8);
    tokenGroup.add(mainObject1, mainObject2);

    // Create outer "aura" wireframe
    const auraGeometry = new THREE.IcosahedronGeometry(1.3, 1);
    const auraMaterial = new THREE.MeshBasicMaterial({
      color: 0x8A2BE2,
      wireframe: true,
      transparent: true,
      opacity: 0.5
    });
    const aura = new THREE.Mesh(auraGeometry, auraMaterial);
    tokenGroup.add(aura);

    // Add floating particles (like stars or energy)
    const particlesCount = 300;
    const particlesGeometry = new THREE.BufferGeometry();
    const particlesPositions = new Float32Array(particlesCount * 3);

    for (let i = 0; i < particlesCount; i++) {
      // Position particles in a large sphere
      const radius = 4 + Math.random() * 8;
      const theta = Math.random() * Math.PI * 2;
      const phi = Math.acos(2 * Math.random() - 1);

      particlesPositions[i * 3] = radius * Math.sin(phi) * Math.cos(theta);
      particlesPositions[i * 3 + 1] = radius * Math.sin(phi) * Math.sin(theta);
      particlesPositions[i * 3 + 2] = radius * Math.cos(phi);
    }

    particlesGeometry.setAttribute('position', new THREE.BufferAttribute(particlesPositions, 3));

    // Create glowing particles
    const particlesMaterial = new THREE.PointsMaterial({
      color: 0xFFFFFF,
      size: 0.05,
      transparent: true,
      blending: THREE.AdditiveBlending,
      sizeAttenuation: true
    });

    const particles = new THREE.Points(particlesGeometry, particlesMaterial);
    scene.add(particles);

    // Create colorful orbiting spheres
    const orbsCount = 12;
    const orbs = [];

    for (let i = 0; i < orbsCount; i++) {
      const size = 0.1 + Math.random() * 0.15;
      const geometry = new THREE.SphereGeometry(size, 16, 16);

      // Get a random color from our palette
      const color = colors[Math.floor(Math.random() * colors.length)];

      const orbMaterial = new THREE.MeshPhysicalMaterial({
        color: color,
        metalness: 0.7,
        roughness: 0.2,
        transmission: 0.5,
        transparent: true,
        opacity: 0.8
      });

      const orb = new THREE.Mesh(geometry, orbMaterial);

      // Random orbit parameters
      const orbitRadius = 2 + Math.random() * 2;
      const orbitSpeed = 0.2 + Math.random() * 0.3;
      const orbitOffset = Math.random() * Math.PI * 2;
      const yOffset = (Math.random() - 0.5) * 2;

      // Store orbit parameters
      orb.userData = { orbitRadius, orbitSpeed, orbitOffset, yOffset };

      scene.add(orb);
      orbs.push(orb);
    }

    // Add some fog for depth
    scene.fog = new THREE.FogExp2(0x000000, 0.03);

    // -------- INTERAZIONE CON MOUSE --------

    // Mouse move event - aggiorna la posizione del mouse
    container.addEventListener('mousemove', (event) => {
      // Salva posizione precedente
      prevMouse.x = mouse.x;
      prevMouse.y = mouse.y;

      // Calcola nuova posizione normalizzata
      mouse.x = (event.clientX / width) * 2 - 1;
      mouse.y = -(event.clientY / height) * 2 + 1;

      // Se il mouse è premuto, ruota il token in base al movimento
      if (isMouseDown) {
        // Calcola la differenza di movimento
        const deltaX = mouse.x - prevMouse.x;
        const deltaY = mouse.y - prevMouse.y;

        // Aggiorna la rotazione manuale
        manualRotation.y += deltaX * 2; // Rotazione orizzontale (Y)
        manualRotation.x += deltaY * 2; // Rotazione verticale (X)

        // Limita la rotazione verticale per evitare capovolgimenti
        manualRotation.x = Math.max(-Math.PI/2, Math.min(Math.PI/2, manualRotation.x));

        // Disattiva l'autorotazione quando l'utente interagisce
        autoRotate = false;
      }
    });

    // Mouse over - attira le particelle verso il mouse
    container.addEventListener('mousemove', (event) => {
      // Creiamo un effetto in cui le particelle sono attratte verso il puntatore
      const mouseX = (event.clientX / window.innerWidth) * 2 - 1;
      const mouseY = -(event.clientY / window.innerHeight) * 2 + 1;

      // Update particle attractions - questo sarà usato nell'animazione
      particlesGeometry.userData = {
        mouseX: mouseX,
        mouseY: mouseY,
        isActive: true,
        intensity: 0.2
      };
    });

    // Mouse down - inizia la rotazione manuale
    container.addEventListener('mousedown', () => {
      isMouseDown = true;
      mouseDownTime = Date.now();
    });

    // Mouse up - termina la rotazione manuale
    container.addEventListener('mouseup', () => {
      isMouseDown = false;
      // Se la durata del click è breve, lo consideriamo un click
      if (Date.now() - mouseDownTime < 200) {
        // Effetto speciale su click: spinge le particelle e le sfere
        const explosionIntensity = 0.5;

        // Applica forza alle particelle
        particlesGeometry.userData = {
          ...particlesGeometry.userData,
          isExplosion: true,
          explosionIntensity
        };

        // Applica forza alle sfere
        orbs.forEach(orb => {
          orb.userData.explosionEffect = explosionIntensity;
        });
      }
    });

    // Mouse leave - termina la rotazione manuale
    container.addEventListener('mouseleave', () => {
      isMouseDown = false;

      // Reset dell'attrazione particelle
      if (particlesGeometry.userData) {
        particlesGeometry.userData.isActive = false;
      }
    });

    // Wheel event - zoom in/out
    container.addEventListener('wheel', (event) => {
      event.preventDefault();

      // Calcola il nuovo valore Z della camera (zoom)
      const zoomSpeed = 0.1;
      const zoomDirection = event.deltaY > 0 ? 1 : -1;
      camera.position.z += zoomDirection * zoomSpeed;

      // Limita lo zoom
      camera.position.z = Math.max(2, Math.min(10, camera.position.z));

      // Disattiva l'autorotazione quando l'utente interagisce
      autoRotate = false;
    });

    // Double click - reset view
    container.addEventListener('dblclick', () => {
      // Reset camera position
      camera.position.z = 5;

      // Reset rotation
      manualRotation.set(0, 0);

      // Re-enable auto-rotation
      autoRotate = true;
    });

    // Animation setup
    const clock = new THREE.Clock();

    function animate() {
      requestAnimationFrame(animate);
      const time = clock.getElapsedTime();
      const delta = clock.getDelta();

      // Applica rotazione automatica o manuale
      if (autoRotate) {
        // Rotazione automatica se non c'è interazione
        tokenGroup.rotation.y = time * autoRotationSpeed;
        tokenGroup.rotation.x = Math.sin(time * 0.2) * 0.2;
      } else {
        // Rotazione manuale da input utente
        tokenGroup.rotation.y = manualRotation.y;
        tokenGroup.rotation.x = manualRotation.x;
      }

      // Make objects within token group move relative to each other
      mainObject2.rotation.x = time * 0.4;
      mainObject2.rotation.z = time * 0.3;

      // Pulsate aura
      const pulse = Math.sin(time * 2) * 0.1 + 1;
      aura.scale.set(pulse, pulse, pulse);

      // Update material color for shimmering effect
      const r = Math.sin(time * 0.3) * 0.2 + 0.8;
      const g = Math.sin(time * 0.5 + 1) * 0.2 + 0.8;
      const b = Math.sin(time * 0.7 + 2) * 0.2 + 0.8;
      material.color.setRGB(r, g, b);

      // Change aura color
      auraMaterial.color.setHSL((time * 0.05) % 1, 0.8, 0.6);

      // Animate lights
      lights.forEach((light, i) => {
        const angle = time * 0.5 + (i / lights.length) * Math.PI * 2;
        light.position.x = Math.cos(angle) * 4;
        light.position.z = Math.sin(angle) * 4;

        // Pulsate light intensity
        light.intensity = 1 + Math.sin(time * 2 + i) * 0.3;
      });

      // Mouse interaction with particles
      if (particlesGeometry.userData && particlesGeometry.userData.isActive) {
        const positions = particlesGeometry.attributes.position.array;
        const mouseX = particlesGeometry.userData.mouseX;
        const mouseY = particlesGeometry.userData.mouseY;
        const intensity = particlesGeometry.userData.intensity || 0.2;

        // Converti le coordinate del mouse in un punto 3D davanti alla telecamera
        const mousePoint = new THREE.Vector3(
          mouseX * 5, // Moltiplica per la distanza della camera
          mouseY * 5,
          0
        );

        // Attrazione verso il mouse
        for (let i = 0; i < particlesPositions.length; i += 3) {
          const particlePos = new THREE.Vector3(
            positions[i],
            positions[i+1],
            positions[i+2]
          );

          // Calcola la distanza tra particella e mouse
          const distanceToMouse = particlePos.distanceTo(mousePoint);

          // Più vicino è il mouse, più forte è l'attrazione
          if (distanceToMouse < 5) {
            const attraction = (1 - distanceToMouse / 5) * intensity;

            // Muovi leggermente verso il mouse
            positions[i] += (mousePoint.x - positions[i]) * attraction * delta;
            positions[i+1] += (mousePoint.y - positions[i+1]) * attraction * delta;
          }
        }

        // Aggiorna le posizioni delle particelle
        particlesGeometry.attributes.position.needsUpdate = true;
      }

      // Explosion effect on click
      if (particlesGeometry.userData && particlesGeometry.userData.isExplosion) {
        const positions = particlesGeometry.attributes.position.array;
        const intensity = particlesGeometry.userData.explosionIntensity;

        // Spinge le particelle verso l'esterno
        for (let i = 0; i < positions.length; i += 3) {
          const particlePos = new THREE.Vector3(
            positions[i],
            positions[i+1],
            positions[i+2]
          );

          const direction = particlePos.normalize();

          // Aggiungi una forza nella direzione dalla particella al centro
          positions[i] += direction.x * intensity;
          positions[i+1] += direction.y * intensity;
          positions[i+2] += direction.z * intensity;
        }

        // Aggiorna le posizioni e disattiva l'esplosione
        particlesGeometry.attributes.position.needsUpdate = true;
        particlesGeometry.userData.isExplosion = false;
      }

      // Move particles slightly for a twinkling effect
      particles.rotation.y = time * 0.02;

      // Update particle colors
      const hue = (time * 0.01) % 1;
      particlesMaterial.color.setHSL(hue, 0.8, 0.5);

      // Animate orbs in different orbits
      orbs.forEach(orb => {
        const { orbitRadius, orbitSpeed, orbitOffset, yOffset } = orb.userData;

        // Se c'è un effetto esplosione, applica una spinta
        if (orb.userData.explosionEffect) {
          const direction = orb.position.clone().normalize();
          orb.position.add(direction.multiplyScalar(orb.userData.explosionEffect));
          orb.userData.explosionEffect *= 0.9; // Riduce l'effetto nel tempo

          // Disattiva l'effetto quando è troppo piccolo
          if (orb.userData.explosionEffect < 0.01) {
            orb.userData.explosionEffect = 0;
          }
        } else {
          // Movimento orbitale normale
          orb.position.x = Math.cos(time * orbitSpeed + orbitOffset) * orbitRadius;
          orb.position.z = Math.sin(time * orbitSpeed + orbitOffset) * orbitRadius;
          orb.position.y = Math.sin(time * orbitSpeed * 0.5 + orbitOffset) + yOffset;
        }

        // Subtle orb rotation
        orb.rotation.x += 0.01;
        orb.rotation.y += 0.01;
      });

      // Subtle camera movement for more dynamic feel, solo se non c'è interazione dell'utente
      if (autoRotate) {
        camera.position.x = Math.sin(time * 0.3) * 0.3;
        camera.position.y = Math.cos(time * 0.4) * 0.3;
      }

      camera.lookAt(0, 0, 0);

      // Render the scene
      renderer.render(scene, camera);
    }

    // Handle window resize
    window.addEventListener('resize', () => {
      const width = container.clientWidth;
      const height = container.clientHeight;

      camera.aspect = width / height;
      camera.updateProjectionMatrix();

      renderer.setSize(width, height);
      renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    });

    // Remove loading screen
    setTimeout(() => {
      if (loading) {
        loading.style.opacity = 0;
        setTimeout(() => {
          if (loading) loading.style.display = 'none';
        }, 1000);
      }

      // Start animation
      animate();
    }, 1000);
  });
}
