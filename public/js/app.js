console.log('app.js cargado');

// Función de logout
function handleLogout(e) {
    if (e) {
        e.preventDefault();
    }

    console.log('Cerrando sesión...');
    localStorage.removeItem('order');
    localStorage.removeItem('orderType');

    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!token) {
        console.error('Token CSRF no encontrado');
        return;
    }

    fetch('/logout', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (response.ok || response.redirected) {
                window.location.href = '/login';
            } else {
                console.error('Error en logout:', response.status);
                window.location.href = '/login';
            }
        })
        .catch(error => {
            console.error('Error en fetch de logout:', error);
            window.location.href = '/login';
        });
}

// Función principal de inicialización
function initializeApp() {
    console.log('Inicializando aplicación...');

    // ✅ PREVENIR INICIALIZACIÓN MÚLTIPLE
    if (document.body.dataset.appInitialized === 'true') {
        console.log('⚠️ App ya inicializada, saltando...');
        return;
    }

    setupMenuToggles();
    setupUserMenu();
    setupLogoutHandlers();
    setupPettyCashControl();
    setupFetchInterceptor();
    setupMobileMenu();

    // ✅ MARCAR COMO INICIALIZADO
    document.body.dataset.appInitialized = 'true';
    console.log('✅ Aplicación inicializada correctamente');
}

// Configurar menú móvil
function setupMobileMenu() {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileOverlay = document.getElementById('mobile-overlay');

    if (menuToggle) {
        menuToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (window.innerWidth < 768) {
                if (mobileMenu) {
                    mobileMenu.classList.toggle('show');
                }
                if (mobileOverlay) {
                    mobileOverlay.classList.toggle('active');
                }
            }

            if (sidebar) {
                sidebar.classList.toggle('show');
            }
        });
    }

    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function () {
            if (mobileMenu) {
                mobileMenu.classList.remove('show');
            }
            if (sidebar) {
                sidebar.classList.remove('show');
            }
            mobileOverlay.classList.remove('active');
        });
    }
}

// Función para configurar menús desplegables
function setupMenuToggles() {
    console.log('Configurando menús desplegables...');

    const menuToggles = document.querySelectorAll('.menu-toggle');
    console.log(`Encontrados ${menuToggles.length} menús`);

    menuToggles.forEach((toggle, index) => {
        // ✅ VERIFICAR SI YA ESTÁ INICIALIZADO
        if (toggle.dataset.initialized === 'true') {
            console.log(`⚠️ Menú ${index + 1} ya inicializado, saltando...`);
            return;
        }

        console.log(`Configurando menú ${index + 1}`, toggle);

        const newToggle = toggle.cloneNode(true);
        toggle.parentNode.replaceChild(newToggle, toggle);

        // ✅ MARCAR COMO INICIALIZADO
        newToggle.dataset.initialized = 'true';

        newToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const menuId = this.getAttribute('data-menu');
            const submenu = document.getElementById(`${menuId}-submenu`);
            const arrow = this.querySelector('.arrow');

            console.log(`Click en menú: ${menuId}`);

            if (submenu) {
                const isHidden = submenu.classList.contains('hidden');

                if (isHidden) {
                    submenu.classList.remove('hidden');
                    submenu.style.display = 'block';
                } else {
                    submenu.classList.add('hidden');
                    submenu.style.display = 'none';
                }

                if (arrow) {
                    arrow.classList.toggle('rotate-180');
                }
            } else {
                console.error(`No se encontró submenu con ID: ${menuId}-submenu`);
            }
        });
    });

    setupSubmenuLinks();
}

// Función para configurar enlaces de submenús
function setupSubmenuLinks() {
    const submenuLinks = document.querySelectorAll('.submenu a');

    submenuLinks.forEach((link) => {
        link.addEventListener('click', function (e) {
            e.stopPropagation();

            const menuParent = this.closest('.relative');
            if (menuParent) {
                const submenu = menuParent.querySelector('.submenu');
                const arrow = menuParent.querySelector('.arrow');

                if (submenu) {
                    submenu.classList.remove('hidden');
                    submenu.style.display = 'block';
                }

                if (arrow) {
                    arrow.classList.add('rotate-180');
                }
            }
        });
    });
}

// Función para configurar menú de usuario
function setupUserMenu() {
    console.log('Configurando menú de usuario...');

    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');

    if (userMenuButton && userMenu) {
        // ✅ VERIFICAR SI YA ESTÁ INICIALIZADO
        if (userMenuButton.dataset.initialized === 'true') {
            console.log('⚠️ Menú de usuario ya inicializado, saltando...');
            return;
        }

        const newButton = userMenuButton.cloneNode(true);
        userMenuButton.parentNode.replaceChild(newButton, userMenuButton);

        // ✅ MARCAR COMO INICIALIZADO
        newButton.dataset.initialized = 'true';

        newButton.addEventListener('click', function (e) {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function (e) {
            if (userMenu && !userMenu.classList.contains('hidden')) {
                if (!userMenu.contains(e.target) && !newButton.contains(e.target)) {
                    userMenu.classList.add('hidden');
                }
            }
        });
    }
}

// Función para configurar manejadores de logout
function setupLogoutHandlers() {
    console.log('Configurando handlers de logout...');

    const logoutButtons = document.querySelectorAll('button[type="submit"]');

    logoutButtons.forEach(button => {
        const buttonText = button.textContent.trim();
        if (buttonText.includes('Cerrar sesión') || buttonText.includes('Logout')) {
            console.log('Botón de logout encontrado:', button);

            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);

            newButton.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Click en logout detectado');
                handleLogout(e);
            });
        }
    });

    const logoutForms = document.querySelectorAll('form[action*="logout"]');
    logoutForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            console.log('Submit de form logout detectado');
            handleLogout(e);
        });
    });
}

// Configurar control de caja chica
function setupPettyCashControl() {
    if (typeof window.pettyCashData !== 'undefined' &&
        !window.pettyCashData.hasOpenPettyCash &&
        window.pettyCashData.currentRoute !== 'petty-cash.create') {

        console.log('Configurando control de caja chica...');

        const blockedRoutes = ['menu', 'sales'];
        const allLinks = document.querySelectorAll('a');

        allLinks.forEach(link => {
            const shouldBlock = blockedRoutes.some(route => {
                return link.href.includes(route.replace('.', '/'));
            });

            if (shouldBlock) {
                link.addEventListener('click', function (e) {
                    if (this.href !== window.location.href) {
                        e.preventDefault();

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Apertura de Caja Requerida',
                                text: 'Debe abrir una caja chica para acceder a las funciones de ventas',
                                confirmButtonText: 'Abrir Caja',
                                confirmButtonColor: '#203363'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = window.routes?.pettyCashCreate || '/petty-cash/create';
                                }
                            });
                        }
                    }
                });

                link.style.opacity = '0.6';
                link.style.cursor = 'not-allowed';
            }
        });
    }
}

// Función para configurar interceptor de fetch
function setupFetchInterceptor() {
    const originalFetch = window.fetch;
    window.fetch = async function (...args) {
        const response = await originalFetch(...args);
        if (response.status === 401) {
            console.log('Sesión expirada, limpiando datos...');
            localStorage.removeItem('order');
            localStorage.removeItem('orderType');
            window.location.href = '/login';
        }
        return response;
    };
}

// ✅ INICIALIZACIÓN ÚNICA
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    initializeApp();
}

// Exponer funciones globalmente
window.handleLogout = handleLogout;
window.initializeApp = initializeApp;