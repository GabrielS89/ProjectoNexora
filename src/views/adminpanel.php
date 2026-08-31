<?php
$require_admin = true;
$page_title = 'Área Administrativa - Nexora';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>



<div>
    <h1>Área para Administradores</h1>
    <p>Bienvenido, has accedido con privilegios elevados.</p>
        
    <div>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
        <p><strong>Nivel de Acceso:</strong>
            <?php echo ($_SESSION['usuario_rol'] == 3) ? 'Super Administrador' : 'Administrador'; ?>
        </p>

</div>

<div class="admin-wrapper" id="adminWrapper">

    </div>

    <div>
        <button class="menu-btn" onclick="toggleSidebar()">Menu</button>

        <div class="sidebar" id="sidebar">
            <a href="#" onclick="loadAdminContent(event, 'cotizaciones_panel')">Cotizaciones</a>
            <a href="#" onclick="loadAdminContent(event, 'adminpanelTienda')">Tienda</a>
            <a href="#" onclick="loadAdminContent(event, 'proveedorespanel')">Proveedores</a>
            <a href="#" onclick="loadAdminContent(event, 'usuariospanel')">Usuarios</a>
            
        </div>
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
    </div>




    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("sidebarOverlay");
        sidebar.classList.toggle("active");
        overlay.classList.toggle("active");
    }

    function closeSidebar() {
        document.getElementById("sidebar").classList.remove("active");
        document.getElementById("sidebarOverlay").classList.remove("active");
    }

    function loadAdminContent(event, page) {
        event.preventDefault();
        const wrapper = document.getElementById('adminWrapper');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        fetch(page + '.php?partial=1', { credentials: 'same-origin' })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar el contenido');
                }
                return response.text();
            })
            .then(html => {
                wrapper.innerHTML = html;
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                updateTipoPersonaFields(wrapper.querySelector('#tipo_persona'));
            })
            .catch(error => {
                wrapper.innerHTML = '<p>Error cargando la página: ' + error.message + '</p>';
            });
    }

    function updateTipoPersonaFields(select) {
        if (!select) return;
        const wrapper = document.getElementById('adminWrapper');
        const personaFields = wrapper.querySelector('#personaFields');
        const empresaFields = wrapper.querySelector('#empresaFields');
        if (!personaFields || !empresaFields) return;

        const empresaInputs = empresaFields.querySelectorAll('input, textarea, select');
        if (select.value === 'Empresa') {
            empresaFields.style.display = 'block';
            personaFields.style.display = 'none';
            empresaInputs.forEach(el => {
                el.disabled = false;
                if (el.name) el.required = true;
            });
        } else {
            empresaFields.style.display = 'none';
            personaFields.style.display = 'block';
            empresaInputs.forEach(el => {
                el.disabled = true;
                el.required = false;
            });
        }
    }

    function attachAdminWrapperHandlers() {
        const wrapper = document.getElementById('adminWrapper');
        if (!wrapper) return;

        // Intercepta submit de cualquier form dentro del wrapper
        wrapper.addEventListener('submit', function(event) {
            const form = event.target.closest('form');
            if (!form) return;
            const action = form.getAttribute('action') || '';

            // Forms del panel de cotizaciones van al controller externo —
            // hacer fetch del POST y luego recargar cotizaciones_panel en el wrapper
            if (action.includes('gestionar_plan_custom')) {
                event.preventDefault();
                const formData = new FormData(form);
                fetch(action, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    redirect: 'follow'
                })
                .then(() => {
                    // Recargar el panel de cotizaciones con los mensajes de sesión
                    return fetch('cotizaciones_panel.php?partial=1', { credentials: 'same-origin' });
                })
                .then(r => r.text())
                .then(html => { wrapper.innerHTML = html; })
                .catch(err => { wrapper.innerHTML = '<p>Error: ' + err.message + '</p>'; });
                return;
            }

            // Forms de otros paneles (tienda, proveedores, usuarios) — comportamiento original
            if (!action || action.indexOf('partial=1') === -1) return;
            event.preventDefault();
            const formData = new FormData(form);
            fetch(action, {
                method: form.method || 'POST',
                body: formData,
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) throw new Error('Error al guardar los datos');
                    return response.text();
                })
                .then(html => {
                    wrapper.innerHTML = html;
                    updateTipoPersonaFields(wrapper.querySelector('#tipo_persona'));
                })
                .catch(error => {
                    wrapper.innerHTML = '<p>Error al guardar: ' + error.message + '</p>';
                });
        });

        // Intercepta clicks en enlaces .partial-link dentro del wrapper
        wrapper.addEventListener('click', function(event) {
            const link = event.target.closest('a.partial-link');
            if (!link) return;
            const confirmation = link.dataset.confirm;
            if (confirmation && !window.confirm(confirmation)) return;
            event.preventDefault();

            // Los links del panel de cotizaciones pueden apuntar al controller (rechazar)
            // o a la propia vista (vista=crear). Ambos se manejan igual: fetch + render.
            const href = link.href;
            fetch(href, { credentials: 'same-origin', redirect: 'follow' })
                .then(response => {
                    if (!response.ok) throw new Error('Error al cargar la página');
                    return response.text();
                })
                .then(html => {
                    // Si el controller redirigió al panel, el html ya es el panel
                    // Si es la vista crear, el html es el formulario
                    wrapper.innerHTML = html;
                    updateTipoPersonaFields(wrapper.querySelector('#tipo_persona'));
                })
                .catch(error => {
                    wrapper.innerHTML = '<p>Error cargando la página: ' + error.message + '</p>';
                });
        });

        wrapper.addEventListener('change', function(event) {
            const select = event.target.closest('#tipo_persona');
            if (!select) return;
            updateTipoPersonaFields(select);
        });
    }

    attachAdminWrapperHandlers();
</script>


<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>