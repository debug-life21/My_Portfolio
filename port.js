// Subtle scroll reveal + back-to-top + mobile nav + scrollspy.

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

  // Close on outside click / Esc (mobile)
  document.addEventListener("click", (e) => {
    if (!links.classList.contains("open")) return;
    const t = e.target;
    if (!(t instanceof Element)) return;
    if (links.contains(t) || toggle.contains(t)) return;
    links.classList.remove("open");
    toggle.setAttribute("aria-expanded", "false");
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    if (!links.classList.contains("open")) return;
    links.classList.remove("open");
    toggle.setAttribute("aria-expanded", "false");
    toggle.focus();
  });
}

// Reveal on scroll
const revealEls = $$(".reveal");
const io = new IntersectionObserver(
  (entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) {
        const el = e.target;
        const delay = el.getAttribute("data-delay") || "0";
        setTimeout(() => {
          el.classList.add("in-view");
        }, Number(delay));
      }
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

// Active nav link (scrollspy)
const sectionIds = ["services", "experience", "skills", "projects", "contact"];
const sections = sectionIds
  .map((id) => document.getElementById(id))
  .filter((el) => el instanceof HTMLElement);

const navAnchors = new Map(
  sectionIds
    .map((id) => [id, document.querySelector(`.nav-links a[href="#${id}"]`)])
    .filter(([, el]) => el instanceof HTMLAnchorElement)
);

const setActive = (id) => {
  navAnchors.forEach((a, key) => {
    if (!(a instanceof Element)) return;
    a.classList.toggle("active", key === id);
    if (key === id) a.setAttribute("aria-current", "page");
    else a.removeAttribute("aria-current");
  });
};

if (sections.length) {
  const spy = new IntersectionObserver(
    (entries) => {
      const visible = entries
        .filter((e) => e.isIntersecting)
        .sort((a, b) => (a.intersectionRatio > b.intersectionRatio ? -1 : 1))[0];
      if (!visible) return;
      const id = visible.target.id;
      if (id) setActive(id);
    },
    { rootMargin: "-20% 0px -70% 0px", threshold: [0.05, 0.2, 0.35] }
  );

  sections.forEach((s) => spy.observe(s));
}

// Contact form: open default email client (no backend needed)
const contactForm = $("#contactForm");
if (contactForm) {
  contactForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const fd = new FormData(contactForm);
    const name = String(fd.get("name") || "").trim();
    const email = String(fd.get("email") || "").trim();
    const message = String(fd.get("message") || "").trim();

    const subject = encodeURIComponent(`Portfolio message from ${name || "Someone"}`);
    const body = encodeURIComponent(
      `Name: ${name}\nEmail: ${email}\n\nMessage:\n${message}\n`
    );

    window.location.href = `mailto:debuglife1221@gmail.com?subject=${subject}&body=${body}`;
  });
}