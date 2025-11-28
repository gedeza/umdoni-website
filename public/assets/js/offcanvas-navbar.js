(() => {
    'use strict'

    const navbarToggle = document.querySelector('#navbarSideCollapse');
    if (navbarToggle) {
      navbarToggle.addEventListener('click', () => {
        const offcanvasCollapse = document.querySelector('.offcanvas-collapse');
        if (offcanvasCollapse) {
          offcanvasCollapse.classList.toggle('open');
        }
      })
    }
  })()
