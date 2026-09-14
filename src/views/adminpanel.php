<?php
$require_admin = true;
$page_title = 'Panel de Administración - Nexora';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

$initial_tab = $_GET['tab'] ?? 'usuariospanel';
$valid_tabs = ['usuariospanel', 'adminpanelTienda', 'proveedorespanel', 'cotizaciones_panel'];
if (!in_array($initial_tab, $valid_tabs)) {
    $initial_tab = 'usuariospanel';
}
?>

<div class="admin-container">

    <!-- Encabezado del Panel de Control -->
    <div class="hero-section" style="padding-bottom: 1.5rem;">
        <h1>Área de <span>Administración</span></h1>
        <p class="hero-subtitle">Gestión centralizada de usuarios, inventario de tienda, proveedores y cotizaciones.</p>
    </div>

    <div class="admin-header-card">
        <div>
            <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.25rem;">Sesión iniciada como:</div>
            <div style="font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                <?php echo htmlspecialchars($_SESSION['email'] ?? 'Administrador'); ?>
            </div>
        </div>
        <div>
            <span class="admin-badge-role">
                🛡 <?php echo (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 3) ? 'Super Administrador' : 'Administrador'; ?>
            </span>
        </div>
    </div>

    <!-- Barra de Pestañas de Navegación CRUD -->
    <div class="admin-tabs" id="adminTabs">
        <button type="button" class="admin-tab-btn <?php echo ($initial_tab === 'usuariospanel') ? 'active' : ''; ?>" onclick="switchAdminTab(this, 'usuariospanel')">
            Usuarios
        </button>
        <button type="button" class="admin-tab-btn <?php echo ($initial_tab === 'adminpanelTienda') ? 'active' : ''; ?>" onclick="switchAdminTab(this, 'adminpanelTienda')">
            Artículos de Tienda
        </button>
        <button type="button" class="admin-tab-btn <?php echo ($initial_tab === 'proveedorespanel') ? 'active' : ''; ?>" onclick="switchAdminTab(this, 'proveedorespanel')">
            Proveedores
        </button>
        <button type="button" class="admin-tab-btn <?php echo ($initial_tab === 'cotizaciones_panel') ? 'active' : ''; ?>" onclick="switchAdminTab(this, 'cotizaciones_panel')">
            Cotizaciones & Planes
        </button>
    </div>

    <!-- Contenedor Dinámico donde se cargan los módulos CRUD -->
    <div class="admin-content-box" id="adminWrapper">
        <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
            Cargando panel de administración...
        </div>
    </div>

</div>

<script>
let currentActiveTab = '<?php echo $initial_tab; ?>';

function switchAdminTab(buttonElement, page) {
    currentActiveTab = page;
    
    // Actualizar clase activa en botones
    const tabs = document.querySelectorAll('.admin-tab-btn');
    tabs.forEach(btn => btn.classList.remove('active'));
    if (buttonElement) {
        buttonElement.classList.add('active');
    }

    // Cargar contenido
    loadAdminContent(page);
}

function loadAdminContent(page) {
    const wrapper = document.getElementById('adminWrapper');
    wrapper.innerHTML = '<div style="text-align: center; padding: 2.5rem; color: var(--text-muted);"><span style="font-size:1.5rem; display:block; margin-bottom:0.5rem;">⏳</span>Cargando módulo...</div>';

    fetch(page + '.php?partial=1', { credentials: 'same-origin' })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error de comunicación con el servidor (' + response.status + ')');
            }
            return response.text();
        })
        .then(html => {
            wrapper.innerHTML = html;
            updateTipoPersonaFields(wrapper.querySelector('#tipo_persona'));
        })
        .catch(error => {
            wrapper.innerHTML = '<div class="alert-box alert-danger" style="margin-top: 1rem;"><div><strong>Error al cargar:</strong> ' + error.message + '</div></div>';
        });
}

function updateTipoPersonaFields(select) {
    if (!select) return;
    const wrapper = document.getElementById('adminWrapper');
    if (!wrapper) return;
    const empresaFields = wrapper.querySelector('#empresaFields');
    if (!empresaFields) return;

    if (select.value === 'Empresa') {
        empresaFields.style.display = 'block';
    } else {
        empresaFields.style.display = 'none';
    }
}

function attachAdminWrapperHandlers() {
    const wrapper = document.getElementById('adminWrapper');
    if (!wrapper) return;

    // Intercepta submit de formularios dentro del wrapper para recargar dinámicamente
    wrapper.addEventListener('submit', function(event) {
        const form = event.target.closest('form');
        if (!form) return;
        const action = form.getAttribute('action') || '';

        // Formulario de cotizaciones
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
                return fetch('cotizaciones_panel.php?partial=1', { credentials: 'same-origin' });
            })
            .then(r => r.text())
            .then(html => { wrapper.innerHTML = html; })
            .catch(err => { wrapper.innerHTML = '<p>Error: ' + err.message + '</p>'; });
            return;
        }

        // Formularios CRUD generales (usuarios, tienda, proveedores)
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
                wrapper.innerHTML = '<div class="alert-box alert-danger"><div>Error al guardar: ' + error.message + '</div></div>';
            });
    });

    // Intercepta clicks en enlaces de acciones (Editar, Borrar)
    wrapper.addEventListener('click', function(event) {
        const link = event.target.closest('a.partial-link');
        if (!link) return;
        const confirmation = link.dataset.confirm;
        if (confirmation && !window.confirm(confirmation)) return;
        event.preventDefault();

        const href = link.href;
        fetch(href, { credentials: 'same-origin', redirect: 'follow' })
            .then(response => {
                if (!response.ok) throw new Error('Error al cargar la acción');
                return response.text();
            })
            .then(html => {
                wrapper.innerHTML = html;
                updateTipoPersonaFields(wrapper.querySelector('#tipo_persona'));
            })
            .catch(error => {
                wrapper.innerHTML = '<div class="alert-box alert-danger"><div>Error: ' + error.message + '</div></div>';
            });
    });

    wrapper.addEventListener('change', function(event) {
        const select = event.target.closest('#tipo_persona');
        if (!select) return;
        updateTipoPersonaFields(select);
    });
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    attachAdminWrapperHandlers();
    loadAdminContent(currentActiveTab);
});
</script>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>