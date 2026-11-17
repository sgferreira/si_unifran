(() => {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('#main-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const open = toggle.getAttribute('aria-expanded') !== 'true';
      toggle.setAttribute('aria-expanded', String(open));
      nav.classList.toggle('open', open);
    });
    nav.addEventListener('click', event => {
      if (event.target.closest('a')) {
        toggle.setAttribute('aria-expanded', 'false');
        nav.classList.remove('open');
      }
    });
  }
  const checkout = document.querySelector('#checkout-form');
  if (checkout) checkout.addEventListener('submit', () => {
    if (!checkout.checkValidity()) return;
    const button = checkout.querySelector('button[type="submit"]');
    if (button) { button.disabled = true; button.textContent = 'Registrando seu pedido…'; }
  });
})();