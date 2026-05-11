<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($data) {
        try {
            $this->pdo->beginTransaction();

            // 1. Insertar en la tabla USUARIO
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $sqlUser = "INSERT INTO USUARIO (Email, Nombre, Telefono, Contrasena, ID_Rol, Tipo_Persona) 
                        VALUES (?, ?, ?, ?, 1, 'Empresa')";
            $stmt = $this->pdo->prepare($sqlUser);
            $stmt->execute([$data['email'], $data['nombre_empresa'], $data['telefono'], $hash]);
            
            // Recuperamos el ID generado automáticamente para usarlo en las siguientes tablas
            $idUsuario = $this->pdo->lastInsertId();

            // 2. Insertar en la tabla EMPRESA
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

            // 3. Insertar en la tabla REPRESENTANTE
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

    public function login($email, $password) {
        try {
            $sql = "SELECT ID_Usuario, Email, Contrasena, Tipo_Persona FROM USUARIO WHERE Email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['Contrasena'])) {
                session_start();
                $_SESSION['usuario_id'] = $user['ID_Usuario'];
                $_SESSION['usuario_email'] = $user['Email'];
                $_SESSION['usuario_tipo'] = $user['Tipo_Persona'];
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function registerUnipersonal($data) {
        try {
            // Insertar en la tabla USUARIO
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $sqlUser = "INSERT INTO USUARIO (Email, Nombre, Telefono, Contrasena, ID_Rol, Tipo_Persona) 
                        VALUES (?, ?, ?, ?, 2, 'Unipersonal')";
            $stmt = $this->pdo->prepare($sqlUser);
            $stmt->execute([$data['email'], $data['nombre'], $data['telefono'], $hash]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>







?>



