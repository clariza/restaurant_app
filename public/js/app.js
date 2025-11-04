// import '../../resources/js/bootstrap';

console.log('app.js cargado'); 

// Función de logout
function handleLogout(e) {
    if (e) {
        e.preventDefault();
    }

    console.log('Cerrando sesión...');

    // Limpiar localStorage
    localStorage.removeItem('order');
    localStorage.removeItem('orderType');

    // Obtener el token CSRF
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!token) {
        console.error('Token CSRF no encontrado');
        return;
    }

    // Realizar petición de logout
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
                // Forzar redirección en caso de error
                window.location.href = '/login';
            }
        })
        .catch(error => {
            console.error('Error en fetch de logout:', error);
            // Forzar redirección en caso de error
            window.location.href = '/login';
        });
}
// Función principal de inicialización
function initializeApp() {
    console.log('Inicializando aplicación...');

    setupMenuToggles();
    setupUserMenu();
    setupLogoutHandlers();
    setupPettyCashControl();
    setupFetchInterceptor();
    setupMobileMenu();
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
    console.log('Configurando menús desplegables...'); // Debug
    
    const menuToggles = document.querySelectorAll('.menu-toggle');
    console.log(`Encontrados ${menuToggles.length} menús`); // Debug
    
    menuToggles.forEach((toggle, index) => {
        console.log(`Configurando menú ${index + 1}`, toggle); // Debug
        
        // Remover listeners existentes para evitar duplicados
        const newToggle = toggle.cloneNode(true);
        toggle.parentNode.replaceChild(newToggle, toggle);
        
        newToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const menuId = this.getAttribute('data-menu');
            const submenu = document.getElementById(`${menuId}-submenu`);
            const arrow = this.querySelector('.arrow');
            
            console.log(`Click en menú: ${menuId}`); // Debug
            console.log('Submenu:', submenu); // Debug
            console.log('Arrow:', arrow); // Debug
            
            if (submenu) {
                const isHidden = submenu.classList.contains('hidden');
                console.log(`Submenu hidden: ${isHidden}`); // Debug
                
                // Toggle submenu
                if (isHidden) {
                    submenu.classList.remove('hidden');
                    submenu.style.display = 'block';
                } else {
                    submenu.classList.add('hidden');
                    submenu.style.display = 'none';
                }
                
                // Toggle arrow
                if (arrow) {
                    arrow.classList.toggle('rotate-180');
                }
                
                console.log(`Submenu ahora hidden: ${submenu.classList.contains('hidden')}`); // Debug
            } else {
                console.error(`No se encontró submenu con ID: ${menuId}-submenu`);
            }
        });
    });
    
    // Configurar enlaces de submenús para mantenerlos abiertos
    setupSubmenuLinks();
}
// Función para configurar enlaces de submenús
function setupSubmenuLinks() {
    const submenuLinks = document.querySelectorAll('.submenu a');
    
    submenuLinks.forEach((link) => {
        link.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Mantener abierto el menú padre
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
        // Remover listeners existentes
        const newButton = userMenuButton.cloneNode(true);
        userMenuButton.parentNode.replaceChild(newButton, userMenuButton);

        newButton.addEventListener('click', function (e) {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });

        // Cerrar menú al hacer clic fuera
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

    // Buscar todos los botones de logout
    const logoutButtons = document.querySelectorAll('button[type="submit"]');

    logoutButtons.forEach(button => {
        const buttonText = button.textContent.trim();
        if (buttonText.includes('Cerrar sesión') || buttonText.includes('Logout')) {
            console.log('Botón de logout encontrado:', button);

            // Remover listeners existentes
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

    // También manejar formularios de logout
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


// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    // El DOM ya está listo
    initializeApp();
}

// También inicializar en window.load como respaldo
window.addEventListener('load', function() {
    console.log('Window loaded, verificando inicialización...'); // Debug
    // Solo reinicializar si no se ha hecho antes
    if (!document.querySelector('.menu-toggle[data-initialized]')) {
        initializeApp();
    }
});



if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    // El DOM ya está listo
    initializeApp();
}

// También inicializar en window.load como respaldo
window.addEventListener('load', function() {
    console.log('Window loaded, verificando inicialización...'); // Debug
    // Solo reinicializar si no se ha hecho antes
    if (!document.querySelector('.menu-toggle[data-initialized]')) {
        initializeApp();
    }
});

// Exponer función handleLogout globalmente si es necesaria
window.handleLogout = handleLogout;
window.initializeApp = initializeApp;
// Manejo del menú móvil
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileOverlay = document.getElementById('mobile-overlay');
    
    // Toggle del menú móvil
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (window.innerWidth < 768) {
                // Para móviles, usar el mobile-menu
                if (mobileMenu) {
                    mobileMenu.classList.toggle('show');
                    if (mobileOverlay) {
                        mobileOverlay.classList.toggle('active');
                    }
                }
                // También toggle del sidebar normal por si acaso
                if (sidebar) {
                    sidebar.classList.toggle('show');
                }
            } else {
                // Para tablets, usar el sidebar
                if (sidebar) {
                    sidebar.classList.toggle('show');
                }
            }
        });
    }
    
    // Cerrar menú al hacer click en el overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function() {
            if (mobileMenu) {
                mobileMenu.classList.remove('show');
            }
            if (sidebar) {
                sidebar.classList.remove('show');
            }
            mobileOverlay.classList.remove('active');
        });
    }
    
    // Manejo de submenús
    const menuToggles = document.querySelectorAll('.menu-toggle');
    menuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const menuType = this.getAttribute('data-menu');
            const submenu = document.getElementById(`${menuType}-submenu`);
            const arrow = this.querySelector('.arrow');
            
            if (submenu) {
                submenu.classList.toggle('hidden');
                if (arrow) {
                    arrow.classList.toggle('rotate-180');
                }
            }
        });
    });
    
    // Manejo del menú de usuario
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');
    
    if (userMenuButton && userMenu) {
        userMenuButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });
        
        // Cerrar el menú al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
            }
        });
    }
    
    // Botones de agregar gasto
    const addExpenseButtons = document.querySelectorAll('.add-expense-btn');
    addExpenseButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (typeof addExpense === 'function') {
                addExpense();
            }
        });
    });
    
    // Botones de guardar cierre
    const saveButtons = document.querySelectorAll('.save-btn');
    saveButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (typeof saveClosure === 'function') {
                saveClosure();
            }
        });
    });
    
    // Inputs de denominaciones
    const denominationInputs = document.querySelectorAll('.denomination-input');
    denominationInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (typeof calcularTotal === 'function') {
                calcularTotal();
            }
        });
    });
    
    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('modal');
            if (modal && modal.classList.contains('active')) {
                if (typeof closeModal === 'function') {
                    closeModal();
                }
            }
        }
    });
});

// document.addEventListener('DOMContentLoaded', function() {
//     const userMenuButton = document.getElementById('user-menu-button');
//     const userMenu = document.getElementById('user-menu');
    
//     if (userMenuButton && userMenu) {
//         userMenuButton.addEventListener('click', function(e) {
//             e.stopPropagation();
            
//             // Alternar visibilidad
//             const isHidden = userMenu.classList.contains('hidden');
            
//             if (isHidden) {
//                 userMenu.classList.remove('hidden');
//                 userMenu.style.display = 'block';
//             } else {
//                 userMenu.classList.add('hidden');
//                 userMenu.style.display = 'none';
//             }
//         });
        
//         // Cerrar menú al hacer clic fuera
//         document.addEventListener('click', function(e) {
//             if (userMenu && !userMenu.classList.contains('hidden')) {
//                 if (!userMenu.contains(e.target) && !userMenuButton.contains(e.target)) {
//                     userMenu.classList.add('hidden');
//                     userMenu.style.display = 'none';
//                 }
//             }
//         });
//     }
// });


// function closeAllOtherMenus(currentMenu) {
//     document.querySelectorAll('.submenu').forEach(menu => {
//         if (!menu.contains(currentMenu)) {
//             menu.classList.add('hidden');
//         }
//     });
    
//     document.querySelectorAll('.menu-toggle .arrow').forEach(arrow => {
//         if (!arrow.closest('.menu-toggle').contains(currentMenu)) {
//             arrow.classList.remove('rotate-180');
//         }
//     });
// }

// const menuToggles = document.querySelectorAll('.menu-toggle');
// menuToggles.forEach((toggle) => {
//     toggle.addEventListener('click', (e) => {
//         e.preventDefault();
//         e.stopPropagation();
        
//         const menuId = toggle.getAttribute('data-menu');
//         const submenu = document.getElementById(`${menuId}-submenu`);
//         const arrow = toggle.querySelector('.arrow');

//         // Alternar visibilidad del submenú actual
//         submenu.classList.toggle('hidden');
//         arrow.classList.toggle('rotate-180');
//     });
// });


// document.querySelectorAll('.submenu a').forEach((link) => {
//     link.addEventListener('click', (e) => {
//         e.stopPropagation();
        
//         const menuParent = link.closest('.relative');
//         if (menuParent) {
//             const allSubmenus = menuParent.querySelectorAll('.submenu');
//             allSubmenus.forEach((submenu) => {
//                 submenu.classList.remove('hidden');
//             });
            
//             const allArrows = menuParent.querySelectorAll('.arrow');
//             allArrows.forEach((arrow) => {
//                 arrow.classList.add('rotate-180');
//             });
//         }
//     });
// });


// document.addEventListener('DOMContentLoaded', function() {
//     if (window.pettyCashData && !window.pettyCashData.hasOpenPettyCash && 
//         window.pettyCashData.currentRoute !== 'petty-cash.create') {
        
//         const blockedRoutes = ['menu', 'sales'];
        
//         const allLinks = document.querySelectorAll('a');
//         allLinks.forEach(link => {
//             const shouldBlock = blockedRoutes.some(route => {
//                 return link.href.includes(route.replace('.', '/'));
//             });
            
//             if (shouldBlock) {
//                 link.addEventListener('click', function(e) {
//                     if (this.href !== window.location.href) {
//                         e.preventDefault();
//                         Swal.fire({
//                             icon: 'warning',
//                             title: 'Apertura de Caja Requerida',
//                             text: 'Debe abrir una caja chica para acceder a las funciones de ventas',
//                             confirmButtonText: 'Abrir Caja',
//                             confirmButtonColor: '#203363'
//                         }).then((result) => {
//                             if (result.isConfirmed) {
//                                 window.location.href = window.routes.pettyCashCreate;
//                             }
//                         });
//                     }
//                 });
                
//                 link.style.opacity = '0.6';
//                 link.style.cursor = 'not-allowed';
//             }
//         });
        
//         if (['menu.index', 'sales.index'].includes(window.pettyCashData.currentRoute)) {
//             Swal.fire({
//                 icon: 'warning',
//                 title: 'Apertura de Caja Requerida',
//                 text: 'Debe abrir una caja chica para acceder a esta función',
//                 confirmButtonText: 'Abrir Caja',
//                 confirmButtonColor: '#203363'
//             }).then((result) => {
//                 if (result.isConfirmed) {
//                     window.location.href = window.routes.pettyCashCreate;
//                 }
//             });
//         }
//     }
// });


// document.addEventListener('DOMContentLoaded', function() {
//     const logoutForm = document.querySelector('form[action*="logout"]');
//     if (logoutForm) {
//         logoutForm.addEventListener('submit', function(e) {
//             localStorage.removeItem('order');
//             localStorage.removeItem('orderType');
//         });
//     }

//     if (!window.isAuthenticated) {
//         localStorage.removeItem('order');
//         localStorage.removeItem('orderType');
//     }
// });


// window.addEventListener('load', function() {
//     const originalFetch = window.fetch;
//     window.fetch = async function(...args) {
//         const response = await originalFetch(...args);
//         if (response.status === 401) {
//             localStorage.removeItem('order');
//             localStorage.removeItem('orderType');
//             window.location.reload();
//         }
//         return response;
//     };
// });
