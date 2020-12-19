<?php 




// <!-- connect to database -->
//             type DB  where         port        DB name             admin   PW
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
// if error throw exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// $id = $_GET['id'] ?? null;
$id = $_GET['id'] ?? null;

if(!$id) {
    header('Location: index.php');
    exit;

}


$statement = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$statement->bindValue(':id', $id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);
//array for errors msg from form
$errors = [];

//Added so values in input fields are filled with "" and when data is entered these will change to entered data
$title = $product['title'];
$price = $product['price'];
$description = $product['description'];

//check if the method is post so if they submit then they're values.
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    //pushing errors for blank fields
    if(!$title) {
        $errors[] = 'Product title is required';
    }
    if(!$price) {
        $errors[] = 'Product price is required';
    }
    
    // checking if image folder has already been created
    if(!is_dir('images')) {
        mkdir('images');
    }

    //Don't push to database if they're errors
    if(empty($errors)) {
        //upload image Files is superglobal so it grabs the value of whatever is passed
        $image = $_FILES['image'] ?? null;
        $imagePath = $product['image'];

        if($image && $image['tmp_name']) {

            if($product['image']) {
                unlink($product['image']);
            }
            //enter a random file with random 8 chars
            $imagePath = 'images/'.randomString(8).'/'.$image['name'];

            mkdir(dirname($imagePath));
                                // temp location |  new file
            move_uploaded_file($image['tmp_name'], $imagePath);
        }


        $statement = $pdo->prepare("UPDATE products SET title = :title, image = :image, description = :description, price = :price WHERE id = :id");
       
       $statement->bindValue(':title', $title);
       $statement->bindValue(':image', $imagePath);
       $statement->bindValue(':description', $description);
       $statement->bindValue(':price', $price);
       $statement->bindValue(':id', $id);
       $statement->execute();
        header('Location: index.php');
    }
    //dont use exec to change/edit data, security issue
    
    
}

function randomString($n) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';

    for($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($chars) -1);
        $str .= $chars[$index];
    }
    return $str;
}


?>


<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link href="app.css" rel="stylesheet"/>
    <title>Products CRUD</title>
</head>
<body>
<p>
    <a href="index.php" class="btn btn-default">Back to products</a>
</p>
<h1>Update Product: <b><?php echo $product['title'] ?></b></h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <div><?php echo $error ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <?php if ($product['image']): ?>
        <img src="<?php echo $product['image'] ?>" class="product-img-view">
    <?php endif; ?>
    <div class="form-group">
        <label>Product Image</label><br>
        <input type="file" name="image">
    </div>
    <div class="form-group">
        <label>Product title</label>
        <input type="text" name="title" class="form-control" value="<?php echo $title ?>">
    </div>
    <div class="form-group">
        <label>Product description</label>
        <textarea class="form-control" name="description"><?php echo $description ?></textarea>
    </div>
    <div class="form-group">
        <label>Product price</label>
        <input type="number" step=".01" name="price" class="form-control" value="<?php echo $price ?>">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

</body>
</html>