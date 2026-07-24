<?php
$require_admin = true;
$page_title = 'Área Administrativa - PoisonIvy';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>

<style>
        .menu-btn {
            position: fixed;
            top: 15%;
            left: 10px;
            background-color: var(--secondary-green);
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 16px;
            z-index: 1003;
        }

    
        .sidebar {
            position: fixed;
            top: 10%;
            left: 0;
            width: 220px;
            height: 80%;
            background-color: #f1f1f1;
            color: #333;
            box-shadow: 2px 0 8px rgba(0,0,0,0.35);
            display: flex;
            flex-direction: column;
            transform: translateX(-110%);
            transition: transform 0.28s ease, visibility 0s linear 0.28s, opacity 0.28s ease;
            padding-top: 70px;
            z-index: 1004;
            border-right: 1px solid rgba(0,0,0,0.06);
            visibility: hidden;
            opacity: 0;
            pointer-events: none;
        }

        .sidebar a {
            padding: 15px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }

        .sidebar a:hover { background-color: rgba(0,0,0,0.04); }

        
        .sidebar.active { transform: translateX(0); visibility: visible; opacity: 1; pointer-events: auto; transition-delay: 0s; }

        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.35);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.28s ease, visibility 0.28s ease;
            z-index: 1002;
        }

        .sidebar-overlay.active,
        .sidebar.active ~ .sidebar-overlay {
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 768px) {
            .menu-btn { top: 10px; left: 10px; }

            .sidebar {
                width: 100%;
                left: 0;
                top: 0;
                height: 100%;
                transform: translateX(-100%);
            }

            .sidebar.active { transform: translateX(0); }

            .sidebar a { padding: 18px 20px; font-size: 1rem; }
        }

        </style>

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
        <button class="menu-btn" onclick="toggleSidebar()">☰</button>

        <div class="sidebar" id="sidebar">
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

        fetch(page + '.php?partial=1')
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
            })
            .catch(error => {
                wrapper.innerHTML = '<p>Error cargando la página: ' + error.message + '</p>';
            });
    }
</script>


<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>