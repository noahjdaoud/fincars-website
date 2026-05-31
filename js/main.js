// Mobile nav toggle
const hamburger = document.getElementById('hamburger');
const mobileNav = document.getElementById('mobileNav');

if (hamburger && mobileNav) {
  hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('open');
    mobileNav.classList.toggle('open');
  });

  // Sluit menu bij link klik
  mobileNav.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      hamburger.classList.remove('open');
      mobileNav.classList.remove('open');
    });
  });
}

// Contact formulier AJAX
const contactForm = document.getElementById('contactForm');
if (contactForm) {
  contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const btn = contactForm.querySelector('button[type="submit"]');
    const status = document.getElementById('formStatus');

    btn.disabled = true;
    btn.textContent = 'Versturen...';
    status.className = 'form-status';
    status.style.display = 'none';

    try {
      const res = await fetch('contact.php', {
        method: 'POST',
        body: new FormData(contactForm)
      });

      const data = await res.json();

      status.textContent = data.message;
      status.className = 'form-status ' + (data.success ? 'success' : 'error');

      if (data.success) {
        contactForm.reset();
      }
    } catch {
      status.textContent = 'Er is een technische fout opgetreden. Probeer het later opnieuw.';
      status.className = 'form-status error';
    }

    btn.disabled = false;
    btn.textContent = 'Verstuur bericht';
    status.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  });
}
