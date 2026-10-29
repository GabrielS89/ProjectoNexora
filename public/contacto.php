<?php
$page_title = 'Contacto - Proyecto 3ro MA';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>

<div class="registro-shell contacto-shell">
    <h1 class="titulo-admin contacto-title">Contacto</h1>
    <p class="texto-sutil contacto-subtitle">¿Tienes alguna pregunta o consulta? Déjanos un mensaje y te responderemos a la brevedad.</p>

    <form action="#" method="POST" class="contacto-form" onsubmit="event.preventDefault(); alert('¡Gracias por tu mensaje! Te contactaremos pronto.');">
        <label for="nombre">Nombre completo:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required>

        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>

        <label for="mensaje">Mensaje:</label>
        <textarea id="mensaje" name="mensaje" rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>

        <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
    </form>
</div>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>