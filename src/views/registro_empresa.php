<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Empresa</title>
</head>
<body>
    <h1>Registro para Empresas</h1>
    <form action="../controllers/autenticador.php" method="POST">
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="tipo_usuario" value="empresa">

        <h3>Datos de la Cuenta</h3>
        <label>Nombre de la Empresa:</label><br>
        <input type="text" name="nombre_empresa" required><br><br>

        <label>Email de la Empresa:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Número de contacto:</label><br>
        <input type="text" name="telefono"><br><br>

        <h3>Información Corporativa</h3>
        <label>RUT / Identificación Fiscal:</label><br>
        <input type="text" name="rut" required><br><br>

        <label>Dirección:</label><br>
        <input type="text" name="direccion"><br><br>

        <label>Sector o Industria:</label><br>
        <select name="sector">
            <option value="tecnologia">Tecnología</option>
            <option value="comercio">Comercio</option>
            <option value="industria">Industria</option>
        </select><br><br>

        <label>Tamaño de Empresa:</label><br>
        <select name="tamano">
            <option value="pequena">1 a 5 empleados</option>
            <option value="mediana">6 a 20 empleados</option>
            <option value="grande">Más de 20 empleados</option>
        </select><br><br>

        <label>Descripción breve (Objetivos):</label><br>
        <textarea name="descripcion" maxlength="200"></textarea><br><br>

        <h3>Datos del Representante</h3>
        <label>Nombre completo del responsable:</label><br>
        <input type="text" name="rep_nombre"><br><br>

        <label>Cargo:</label><br>
        <input type="text" name="rep_cargo"><br><br>

        <label>Email personal:</label><br>
        <input type="email" name="rep_email"><br><br>

        <label>Teléfono directo:</label><br>
        <input type="text" name="rep_telefono"><br><br>

        <button type="submit">Registrar Empresa</button>
    </form>
    <a href="login.php">Volver al login</a>
</body>
</html>