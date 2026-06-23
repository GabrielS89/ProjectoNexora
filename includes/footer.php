</div>

    </div>

        <footer>
            <p>&copy; 2026 Nexora. Todos los derechos reservados.</p>
            
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
</body>
</html>