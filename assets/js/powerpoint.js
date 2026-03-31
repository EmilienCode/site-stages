document.addEventListener("DOMContentLoaded", () => {
    gsap.registerPlugin(ScrollTrigger);

    // 1 à 4
    gsap.to(".hero-title", { y: -20, opacity: 1, duration: 1, ease: "power3.out", delay: 0.2 });
    gsap.to(".hero-subtitle", { y: -10, opacity: 1, duration: 1, ease: "power3.out", delay: 0.5 });

    gsap.to(".reveal-text", { scrollTrigger: { trigger: ".text-reveal-scene", start: "top top", end: "+=1500", pin: true, scrub: 1 }, opacity: 1, color: "#ffffff", stagger: 0.5 });

    const horizontalContainer = document.querySelector(".horizontal-container");
    const panels = gsap.utils.toArray(".panel");
    gsap.to(panels, { xPercent: -100 * (panels.length - 1), ease: "none", scrollTrigger: { trigger: ".horizontal-scene", pin: true, scrub: 1, end: () => "+=" + horizontalContainer.offsetWidth } });

    gsap.to(".diagram-card", { scrollTrigger: { trigger: ".diagrams-scene", start: "top 50%" }, y: -30, opacity: 1, stagger: 0.2, duration: 0.8, ease: "back.out(1.5)" });

    // --- 5 : EPIC SCENE INNOVATION (ÉVENTAIL + FLY-BY EXTRÊME) ---
    const tlEpic1 = gsap.timeline({ scrollTrigger: { trigger: ".epic-scene-1", start: "top top", end: "+=3000", pin: true, scrub: 1 } });
    
    // PHASE 1 : Ouverture de l'éventail 
    tlEpic1.to(".box-3d-wrapper", { rotationX: 0, rotationZ: 0, duration: 1, ease: "power2.out" }, 0)
           .to(".layer-mid", { yPercent: -10, z: 50, scale: 1.05, duration: 1, ease: "power2.out" }, 0)
           .to(".layer-back", { xPercent: -110, yPercent: 15, z: -100, rotationZ: -10, duration: 1, ease: "power2.out" }, 0)
           .to(".layer-front", { xPercent: 110, yPercent: 15, z: -100, rotationZ: 10, duration: 1, ease: "power2.out" }, 0)
           .to(".epic-desc", { y: 20, opacity: 1, duration: 0.5 }, 0.5);

    // Pause pour lire
    tlEpic1.to(".box-3d-wrapper", { rotationY: 0, duration: 0.5 });

    // PHASE 2 : LE FLY-BY DE FOU (Zoom massif + Effet d'accélération)
    tlEpic1.to(".layer-back", { xPercent: -400, yPercent: -50, z: 2500, scale: 4, opacity: 0, duration: 1.5, ease: "power3.in" }, 1.5)
           .to(".layer-front", { xPercent: 400, yPercent: -50, z: 2500, scale: 4, opacity: 0, duration: 1.5, ease: "power3.in" }, 1.5)
           // La carte du milieu te rentre littéralement dedans en plein centre
           .to(".layer-mid", { z: 3000, scale: 5, opacity: 0, duration: 1.5, ease: "power3.in" }, 1.5)
           .to(".epic-desc", { opacity: 0, scale: 2, duration: 1, ease: "power3.in" }, 1.5);

    // --- 6 à 8 ---
    const tlMvc = gsap.timeline({ scrollTrigger: { trigger: ".mvc-mcd-scene", start: "top top", end: "+=2000", pin: true, scrub: 1 } });
    tlMvc.fromTo(".mvc-panel", { opacity: 0, scale: 0.8, y: 100 }, { opacity: 1, scale: 1, y: 0, duration: 1 })
         .to(".mvc-panel", { opacity: 1, duration: 0.5 })
         .to(".mvc-panel", { opacity: 0, scale: 1.2, y: -100, duration: 1 })
         .fromTo(".mcd-panel", { opacity: 0, scale: 0.8, y: 100 }, { opacity: 1, scale: 1, y: 0, duration: 1 }, "-=0.5")
         .to(".mcd-panel", { opacity: 1, duration: 0.5 });

    const tlWow = gsap.timeline({ scrollTrigger: { trigger: ".experience-wow-scene", start: "top top", end: "+=1500", pin: true, scrub: 1 } });
    tlWow.to(".bg-massive-text", { color: "rgba(250, 128, 114, 0.8)", textShadow: "0 0 40px rgba(250, 128, 114, 0.6)", scale: 1.05, duration: 1 }, 0)
         .fromTo(".reveal-portal", { clipPath: "circle(0% at 50% 50%)", webkitClipPath: "circle(0% at 50% 50%)" }, { clipPath: "circle(150% at 50% 50%)", webkitClipPath: "circle(150% at 50% 50%)", duration: 1.5, ease: "power2.inOut" }, 0.5)
         .from(".brand-title", { scale: 0.8, opacity: 0, duration: 0.8, ease: "back.out(1.5)" }, 1)
         .from(".brand-slogan", { y: 30, opacity: 0, duration: 0.8 }, 1.3);

    const tlFinal = gsap.timeline({ scrollTrigger: { trigger: ".final-smooth-scene", start: "top top", end: "+=1500", pin: true, scrub: 1 } });
    tlFinal.to(".grid-overlay", { opacity: 0.2, duration: 0.5 }, 0)
           .fromTo(".smooth-word", { scale: 2.5, opacity: 0, y: 50, z: 100 }, { scale: 1, opacity: 1, y: 0, z: 0, duration: 1.5, ease: "power2.out", stagger: 0.4 }, 0.2)
           .to(".smooth-words-container", { scale: 0.85, y: -30, duration: 1 }, "+=0.2")
           .fromTo(".final-btn", { y: 50, opacity: 0 }, { y: 0, opacity: 1, duration: 1, ease: "power2.out" }, "<");
});