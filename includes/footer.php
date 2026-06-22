</div>

<<<<<<< HEAD
    </div>

        <footer>
            <p>&copy; 2026 PoisonIvy. Todos los derechos reservados.</p>
            
            <p>

                <?php if (isUserLoggedIn()):
                    $currentUser = $_SESSION['user'] ?? null;
                    if ($currentUser): ?>
                    Usuario: <strong><?php echo htmlspecialchars($currentUser['email']); ?></strong>
                <?php endif; endif; ?>

            </p>

        <a href="contacto.php">Contacto</a>
        </footer>

<!-- Cerrar Body en footer-->
=======
<footer>
    <h2>Nexora</h2>
    <p>Servicio integral de infraestructura y tecnología para tu oficina.</p>

    <h3>Navegación</h3>
        <ul>
        <li><a href="index.php#servicios">Servicios</a></li>
        <li><a href="index.php#contacto">Contacto</a></li>
    </ul>
    </p>

    <p>
        contacto@nexora.com<br>
        +598 123 456 789<br>
        Colonia, Uruguay
    </p>

    <p>&copy; 2026 Nexora. Todos los derechos reservados.</p>
</footer>
>>>>>>> 26cd8b2aaedbc48ab6bbd8d7dc673d2b1a447097
</body>
</html>