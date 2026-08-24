<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($data) {
        try {
            $this->pdo->beginTransaction();

    // Insertar en la tabla USUARIO
    
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $sqlUser = "INSERT INTO USUARIO (Email, Nombre, Telefono, Contrasena, ID_Rol, Tipo_Persona)
                        VALUES (?, ?, ?, ?, 1, 'Empresa')";
            $stmt = $this->pdo->prepare($sqlUser);
            $stmt->execute([$data['email'], $data['nombre_empresa'], $data['telefono'], $hash]);
            
    // Recuperamos el ID generado automáticamente para usarlo en las siguientes tablas
            $idUsuario = $this->pdo->lastInsertId();

    // Insertar en la tabla EMPRESA
            $sqlEmp = "INSERT INTO EMPRESA (ID_Usuario, RUT, Nombre_Empresa, Direccion, Descripcion, Categoria_Empresarial)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmtEmp = $this->pdo->prepare($sqlEmp);
            $stmtEmp->execute([
                $idUsuario,
                $data['rut'],
                $data['nombre_empresa'],
                $data['direccion'],
                $data['descripcion'],
                $data['sector']
            ]);

      // Insertar en la tabla REPRESENTANTE

            $sqlRep = "INSERT INTO REPRESENTANTE (Nombre_Completo, Cargo, Email_Contacto, Telefono_Contacto, ID_Empresa)
                    VALUES (?, ?, ?, ?, ?)";
            $stmtRep = $this->pdo->prepare($sqlRep);
            $stmtRep->execute([
                $data['rep_nombre'],
                $data['rep_cargo'],
                $data['rep_email'],
                $data['rep_telefono'],
                $idUsuario
            ]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    // Login

    public function login($email, $password) {
    try {
    
        $sql = "SELECT ID_Usuario, Email, Contrasena, Tipo_Persona, ID_Rol FROM USUARIO WHERE Email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar la contraseña
        if ($user && password_verify($password, $user['Contrasena'])) {
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            
            $_SESSION['user_id'] = $user['ID_Usuario'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['tipo_usuario'] = $user['Tipo_Persona'];
            
            // Definir Tipo de Usuario (Admin o User)
            $_SESSION['usuario_rol'] = $user['ID_Rol'];
            
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

    // Insertar en la tabla USUARIO

    public function registerUnipersonal($data) {
        try {
            
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $sqlUser = "INSERT INTO USUARIO (Email, Nombre, Telefono, Contrasena, ID_Rol, Tipo_Persona)
                        VALUES (?, ?, ?, ?, 1, 'Persona')";
            $stmt = $this->pdo->prepare($sqlUser);
            $stmt->execute([$data['email'], $data['nombre'], $data['telefono'], $hash]);
            
            $idUsuario = $this->pdo->lastInsertId();

     // Insertar en la tabla Persona

            $sqlUni = "INSERT INTO persona (ID_Usuario) VALUES (?)";
            $stmtUni = $this->pdo->prepare($sqlUni);
            $stmtUni->execute([$idUsuario]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // ==========================================
    // MÉTODOS CRUD ADMINISTRATIVOS (ADMIN & SUPER ADMIN)
    // ==========================================

    // Obtenemos todos los usuarios registrados con su rol
    public function getAllUsers() {
        try {
            $sql = "SELECT u.ID_Usuario, u.Email, u.Nombre, u.Telefono, u.Tipo_Persona, u.ID_Rol, r.TipoRol 
                    FROM usuario u 
                    LEFT JOIN rol r ON u.ID_Rol = r.ID_Rol 
                    ORDER BY u.ID_Usuario ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Obtenemos un usuario específico por ID
    public function getUserById($id) {
        try {
            $sql = "SELECT u.ID_Usuario, u.Email, u.Nombre, u.Telefono, u.Tipo_Persona, u.ID_Rol, r.TipoRol 
                    FROM usuario u 
                    LEFT JOIN rol r ON u.ID_Rol = r.ID_Rol 
                    WHERE u.ID_Usuario = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    // Crear un usuario desde el panel de administración
    public function createUserByAdmin($data) {
        try {
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $sqlUser = "INSERT INTO usuario (Email, Nombre, Telefono, Contrasena, ID_Rol, Tipo_Persona)
                        VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sqlUser);
            $stmt->execute([
                $data['email'],
                $data['nombre'],
                $data['telefono'],
                $hash,
                $data['id_rol'],
                $data['tipo_persona']
            ]);

            $idUsuario = $this->pdo->lastInsertId();

            if ($data['tipo_persona'] === 'Persona') {
                $sqlUni = "INSERT INTO persona (ID_Usuario) VALUES (?)";
                $stmtUni = $this->pdo->prepare($sqlUni);
                $stmtUni->execute([$idUsuario]);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Modificar / Actualizar un usuario existente
    public function updateUserByAdmin($id, $data) {
        try {
            if (!empty($data['password'])) {
                // Si proporcionó nueva contraseña, se actualiza también el hash
                $hash = password_hash($data['password'], PASSWORD_DEFAULT);
                $sql = "UPDATE usuario 
                        SET Email = ?, Nombre = ?, Telefono = ?, Tipo_Persona = ?, ID_Rol = ?, Contrasena = ? 
                        WHERE ID_Usuario = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $data['email'],
                    $data['nombre'],
                    $data['telefono'],
                    $data['tipo_persona'],
                    $data['id_rol'],
                    $hash,
                    $id
                ]);
            } else {
                // Si la contraseña se dejó en blanco, se mantienen los datos sin cambiar la contraseña
                $sql = "UPDATE usuario 
                        SET Email = ?, Nombre = ?, Telefono = ?, Tipo_Persona = ?, ID_Rol = ? 
                        WHERE ID_Usuario = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $data['email'],
                    $data['nombre'],
                    $data['telefono'],
                    $data['tipo_persona'],
                    $data['id_rol'],
                    $id
                ]);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Eliminar usuario por ID
    public function deleteUserByAdmin($id) {
        try {
            $sql = "DELETE FROM usuario WHERE ID_Usuario = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
