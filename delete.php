<?php 

// <!-- connect to database -->
//             type DB  where         port        DB name             admin   PW
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');

// if error throw exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



// $id = $_GET['id'] ?? null;
$id = $_POST['id'] ?? null;

if(!$id) {
    header('Location: index.php');
    exit;

}

$statement = $pdo->prepare('DELETE FROM products WHERE id = :id');
$statement->bindValue(':id', $id);

$statement->execute();

header("Location: index.php");