// Subtle scroll reveal + back-to-top + mobile nav + theme toggle.

const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
const $ = (sel, root = document) => root.querySelector(sel);

const year = $("#year");
if (year) year.textContent = String(new Date().getFullYear());

// Mobile nav
const toggle = $(".nav-toggle");
const links = $("#nav-links");
if (toggle && links) {
  toggle.addEventListener("click", () => {
    const isOpen = links.classList.toggle("open");
    toggle.setAttribute("aria-expanded", String(isOpen));
  });

  // Close on link click (mobile)
  $$("#nav-links a").forEach((a) => {
    a.addEventListener("click", () => {
      links.classList.remove("open");
      toggle.setAttribute("aria-expanded", "false");
    });
  });
}

// Reveal on scroll
const revealEls = $$(".reveal");
const io = new IntersectionObserver(
  (entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) e.target.classList.add("in-view");
    });
  },
  { threshold: 0.12 }
);

revealEls.forEach((el) => io.observe(el));

// Back to top
const backToTop = $("#backToTop");
const onScroll = () => {
  if (!backToTop) return;
  const show = window.scrollY > 600;
  backToTop.classList.toggle("show", show);
};
window.addEventListener("scroll", onScroll, { passive: true });
onScroll();

if (backToTop) {
  backToTop.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

// Theme toggle (stores preference)
const themeToggle = $("#themeToggle");
const root = document.documentElement;

const saved = localStorage.getItem("theme");
if (saved === "light" || saved === "dark") {
  root.dataset.theme = saved;
}

if (themeToggle) {
  themeToggle.addEventListener("click", () => {
    const next = root.dataset.theme === "light" ? "dark" : "light";
    root.dataset.theme = next;
    localStorage.setItem("theme", next);
  });
}