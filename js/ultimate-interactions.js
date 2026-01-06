/**
 * HTU MARTIAL ARTS - REFINED INTERACTIONS
 * Clean, smooth, professional
 */

(function() {
  'use strict';
  
  // ============================================
  // NAVBAR SCROLL EFFECT
  // ============================================
  const navbar = document.querySelector('.navbar');
  
  if (navbar) {
    let lastScroll = 0;
    
    window.addEventListener('scroll', () => {
      const currentScroll = window.pageYOffset;
      
      if (currentScroll > 50) {
        navbar.classList.add('navbar-scrolled');
      } else {
        navbar.classList.remove('navbar-scrolled');
      }
      
      lastScroll = currentScroll;
    });
  }
  
  // ============================================
  // MOBILE MENU
  // ============================================
  const navbarToggler = document.querySelector('.navbar-toggler');
  const navbarNav = document.querySelector('.navbar-nav');
  
  if (navbarToggler && navbarNav) {
    navbarToggler.addEventListener('click', (e) => {
      e.stopPropagation();
      navbarToggler.classList.toggle('active');
      navbarNav.classList.toggle('active');
      document.body.style.overflow = navbarNav.classList.contains('active') ? 'hidden' : '';
    });
    
    // Close on link click
    navbarNav.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        navbarToggler.classList.remove('active');
        navbarNav.classList.remove('active');
        document.body.style.overflow = '';
      });
    });
    
    // Close on outside click
    document.addEventListener('click', (e) => {
      if (navbarNav.classList.contains('active') && 
          !navbarNav.contains(e.target) && 
          !navbarToggler.contains(e.target)) {
        navbarToggler.classList.remove('active');
        navbarNav.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
    
    // Close on escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && navbarNav.classList.contains('active')) {
        navbarToggler.classList.remove('active');
        navbarNav.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  }
  
  // ============================================
  // SMOOTH SCROLL
  // ============================================
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href === '#' || href === '#!') return;
      
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        const navHeight = navbar ? navbar.offsetHeight : 0;
        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navHeight;
        
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    });
  });
  
  // ============================================
  // SCROLL REVEAL
  // ============================================
  const revealElements = document.querySelectorAll('.reveal');
  
  if (revealElements.length > 0 && 'IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
          revealObserver.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    });
    
    revealElements.forEach(el => revealObserver.observe(el));
  }
  
  // ============================================
  // LAZY LOAD IMAGES
  // ============================================
  const lazyImages = document.querySelectorAll('img[loading="lazy"]');
  
  if ('IntersectionObserver' in window && lazyImages.length > 0) {
    const imageObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (img.dataset.src) {
            img.src = img.dataset.src;
          }
          img.classList.add('loaded');
          imageObserver.unobserve(img);
        }
      });
    }, { rootMargin: '50px' });
    
    lazyImages.forEach(img => imageObserver.observe(img));
  }
  
  // ============================================
  // FORM ENHANCEMENTS
  // ============================================
  document.querySelectorAll('form').forEach(form => {
    const inputs = form.querySelectorAll('.form-control, .form-select');
    
    inputs.forEach(input => {
      input.addEventListener('invalid', (e) => {
        e.preventDefault();
        input.style.borderColor = '#EF4444';
      });
      
      input.addEventListener('input', () => {
        if (input.validity.valid) {
          input.style.borderColor = '';
        }
      });
    });
  });

  // ============================================
  // CLASS FILTER
  // ============================================
  const scheduleFilterButtons = document.querySelectorAll('.schedule-filters .filter-btn');
  const scheduleSlots = document.querySelectorAll('.schedule-grid .class-slot');

  if (scheduleFilterButtons.length && scheduleSlots.length) {
    const applyScheduleFilter = (value) => {
      const normalizedFilter = (value || 'all').trim().toLowerCase();
      scheduleSlots.forEach(slot => {
        const artValue = (slot.dataset.art || '').trim().toLowerCase();
        const matches = normalizedFilter === 'all' || artValue === normalizedFilter;
        slot.classList.toggle('hidden', !matches);
      });
    };

    scheduleFilterButtons.forEach(button => {
      button.addEventListener('click', () => {
        scheduleFilterButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        applyScheduleFilter(button.dataset.filter || 'all');
      });
    });

    const initialFilter = document.querySelector('.schedule-filters .filter-btn.active');
    applyScheduleFilter((initialFilter && initialFilter.dataset.filter) || 'all');
  }
  
  console.log('🥋 HTU Martial Arts - Refined theme loaded');
  
})();
