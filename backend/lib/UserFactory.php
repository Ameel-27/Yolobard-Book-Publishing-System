<?php
class UserFactory {
    public static function create($role, $userId) {
        switch ($role) {
            case 'author':
                return new Author($userId);
            case 'editor':
                return new Editor($userId);
            case 'admin':
                return new Admin($userId);
            case 'customer':
                return new Customer($userId);
            default:
                throw new Exception("Invalid role: $role");
        }
    }
}

abstract class User {
    protected $userId;
    public function __construct($userId) { $this->userId = $userId; }
    abstract public function getNavItems();
}

class Author extends User {
    public function getNavItems() {
        return [
            'Home' => '/yolobard/frontend/index.php',
            'Dashboard' => '/yolobard/frontend/author-page.php',
            'Book Store' => '/yolobard/frontend/bookstore.php',
            'About Us' => '/yolobard/frontend/about.php',
            'Logout' => '/yolobard/backend/api/auth/logout.php'
        ];
    }
}
class Editor extends User {
    public function getNavItems() {
        return [
            'Home' => '/yolobard/frontend/index.php',
            'Dashboard' => '/yolobard/frontend/editor-page.php',
            'Book Store' => '/yolobard/frontend/bookstore.php',
            'About Us' => '/yolobard/frontend/about.php',
            'Logout' => '/yolobard/backend/api/auth/logout.php'
        ];
    }
}
class Admin extends User {
    public function getNavItems() {
        return [
            'Home' => '/yolobard/frontend/index.php',
            'Dashboard' => '/yolobard/frontend/admin-page.php',
            'Book Store' => '/yolobard/frontend/bookstore.php',
            'About Us' => '/yolobard/frontend/about.php',
            'Logout' => '/yolobard/backend/api/auth/logout.php'
        ];
    }
}
class Customer extends User {
    public function getNavItems() {
        return [
            'Home' => '/yolobard/frontend/index.php',
            'Book Store' => '/yolobard/frontend/bookstore.php',
            'About Us' => '/yolobard/frontend/about.php',
            'Logout' => '/yolobard/backend/api/auth/logout.php'
        ];
    }
}
?>