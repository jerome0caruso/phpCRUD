<?php 



// <!-- connect to database -->
//             type DB  where         port        DB name             admin   PW
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
// if error throw exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// echo'<pre>';
// var_dump($_FILES);
// echo'</pre>';
// exit;

//array for errors msg from form
$errors = [];

//Added so values in input fields are filled with "" and when data is entered these will change to entered data
$title = '';
$price = '';
$description = '';

//check if the method is post so if they submit then they're values.
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $date = date('Y-m-d H:i:s');

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
        $imagePath = '';
        

        if($image && $image['tmp_name']) {
            //enter a random file with random 8 chars
            $imagePath = 'images/'.randomString(8).'/'.$image['name'];

            mkdir(dirname($imagePath));
                                // temp location |  new file
            move_uploaded_file($image['tmp_name'], $imagePath);
        }


        $statement = $pdo->prepare("INSERT INTO products (title, image, description, price, create_date)
        VALUES (:title, :image, :description, :price, :date)");
       
       $statement->bindValue(':title', $title);
       $statement->bindValue(':image', $imagePath);
       $statement->bindValue(':description', $description);
       $statement->bindValue(':price', $price);
       $statement->bindValue(':date', $date);
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="app.css">
    <title>Products Crud 1</title>
  </head>
  <body>

    <h1 class="create-H1">Create new Product</h1>
    <div class="container">
    <!-- error box  -->
   <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach($errors as $error): ?>
            <div><?php echo $error ?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

                                            <!-- added to allow file upload -->
    <form action="create.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Image</label>
            <br>
            <input type="file" name="image">
        </div>
        <div class="form-group">
            <label>Product Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo $title?>">
        </div>
        <div class="form-group">
            <label>Product Description</label>
            <textarea style="resize: none;" name="description" class="form-control" value="<?php echo $description?>"></textarea>
        </div>
        <div class="form-group">
            <label>Product Price</label>
            <input type="number" step=".01" name="price" class="form-control"value="<?php echo $price?>"/>
        </div>
  
        <button type="submit" class="btn btn-primary">Submit</button>

    </form>
   
  </body>
</html>