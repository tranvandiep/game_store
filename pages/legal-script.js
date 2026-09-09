/**
 * My Kiddy Legal Pages Interactive Script
 * - Handles bilingual toggling (VI / EN)
 * - Table of contents smooth scroll & active spy
 * - Back to top button
 */

(function () {
  'use strict';

  // 1. Language switcher
  function initLanguage() {
    const urlParams = new URLSearchParams(window.location.search);
    const hash = window.location.hash.toLowerCase();
    let defaultLang = 'vi';

    if (urlParams.get('lang') === 'en' || hash === '#en') {
      defaultLang = 'en';
    } else if (urlParams.get('lang') === 'vi' || hash === '#vi') {
      defaultLang = 'vi';
    } else {
      const savedLang = localStorage.getItem('mykiddy_legal_lang');
      if (savedLang === 'en' || savedLang === 'vi') {
        defaultLang = savedLang;
      }
    }

    setLanguage(defaultLang);

    // Bind click events
    document.querySelectorAll('.lang-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const lang = this.getAttribute('data-lang');
        setLanguage(lang);
      });
    });
  }

  function setLanguage(lang) {
    document.body.setAttribute('data-lang', lang);
    localStorage.setItem('mykiddy_legal_lang', lang);

    // Update active button state
    document.querySelectorAll('.lang-btn').forEach(btn => {
      if (btn.getAttribute('data-lang') === lang) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });

    // Update TOC links visibility if separate TOC
    document.querySelectorAll('.toc-link').forEach(link => {
      const linkLang = link.getAttribute('data-lang');
      if (linkLang) {
        if (linkLang === lang) {
          link.style.display = 'block';
        } else {
          link.style.display = 'none';
        }
      }
    });
  }

  // 2. Back to top button
  function initBackToTop() {
    const btn = document.getElementById('btnBackToTop');
    if (!btn) return;

    window.addEventListener('scroll', function () {
      if (window.scrollY > 300) {
        btn.classList.add('visible');
      } else {
        btn.classList.remove('visible');
      }
    });

    btn.addEventListener('click', function () {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // 3. Scroll Spy for Table of Contents
  function initScrollSpy() {
    const cards = document.querySelectorAll('.legal-section-card');
    const tocLinks = document.querySelectorAll('.toc-link');

    if (!cards.length || !tocLinks.length) return;

    window.addEventListener('scroll', function () {
      let currentId = '';
      const scrollPos = window.scrollY + 120;

      cards.forEach(card => {
        const top = card.offsetTop;
        const height = card.offsetHeight;
        if (scrollPos >= top && scrollPos < top + height) {
          currentId = card.id;
        }
      });

      if (currentId) {
        tocLinks.forEach(link => {
          link.classList.remove('active');
          if (link.getAttribute('href') === '#' + currentId) {
            link.classList.add('active');
          }
        });
      }
    });
  }

  // DOM ready
  document.addEventListener('DOMContentLoaded', function () {
    initLanguage();
    initBackToTop();
    initScrollSpy();
  });
})();
