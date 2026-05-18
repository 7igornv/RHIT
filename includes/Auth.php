<?php
class Auth {
    private static $users = [
        'admin' => '12345'  // логин => пароль
    ];
    
    private static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function login($username, $password) {
        self::startSession();
        if (isset(self::$users[$username]) && self::$users[$username] === $password) {
            $_SESSION['user'] = $username;
            $_SESSION['logged_in'] = true;
            return true;
        }
        return false;
    }
    
    public static function logout() {
        self::startSession();
        session_destroy();
    }
    
    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: /admin.php?login=required');
            exit();
        }
    }
}
?>