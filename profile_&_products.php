<?php

include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:index.html'); // Redirect to index.html if the user is not logged in
    exit(); // Ensure no further code executes
}

// Logout functionality
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();  // Destroy the session
    header('location:index.html');  // Redirect to index.html after logout
    exit();  // Ensure no further code executes
}

if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

    if (mysqli_num_rows($select_cart) > 0) {
        $message[] = 'Product already added to cart!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, image, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')") or die('query failed');
        $message[] = 'Product added to cart!';
    }
}

if (isset($_POST['update_cart'])) {
    $update_quantity = $_POST['cart_quantity'];
    $update_id = $_POST['cart_id'];
    mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('query failed');
    $message[] = 'Cart quantity updated successfully!';
}

if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'") or die('query failed');
    header('location:profile_&_products.php');
    exit(); // Ensure no further code executes
}

if (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    header('location:profile_&_products.php');
    exit(); // Ensure no further code executes
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #87CEFA; /* Light Blue Background */
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Container Styles */
        .container {
            width: 80%;
            margin: auto;
            background-color: #ffffff; /* White Background for container */
            border: 1px solid #000000; /* Black Border */
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* User Profile Styles */
        .user-profile {
            background-color: #00BFFF; /* Deep Sky Blue */
            color: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #000000; /* Black Border */
        }

        .user-profile p {
            margin: 10px 0;
        }

        .user-profile .flex {
            display: flex;
            justify-content: space-between;
        }

        .user-profile .btn,
        .user-profile .option-btn,
        .user-profile .delete-btn {
            padding: 10px 20px;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid #000000; /* Black Border */
            margin: 5px;
        }

        .user-profile .btn {
            background-color: #4682B4; /* Steel Blue */
        }

        .user-profile .option-btn {
            background-color: #20B2AA; /* Light Sea Green */
        }

        .user-profile .delete-btn {
            background-color: #DC143C; /* Crimson */
        }

        .user-profile .btn:hover,
        .user-profile .option-btn:hover,
        .user-profile .delete-btn:hover {
            opacity: 0.8;
        }

        /* Products Section Styles */
        .products {
            margin-bottom: 20px;
        }

        .products .heading {
            font-size: 24px;
            color: #00BFFF; /* Deep Sky Blue */
            margin-bottom: 20px;
        }

        .box-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .box {
            background-color: #f0f8ff; /* Alice Blue */
            border: 1px solid #000000; /* Black Border */
            border-radius: 8px;
            padding: 10px;
            width: calc(25% - 20px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .box img {
            width: 100%;
            height: auto;
            border-bottom: 1px solid #000000; /* Black Border */
            margin-bottom: 10px;
        }

        .box .name {
            font-size: 18px;
            color: #333;
        }

        .box .price {
            font-size: 16px;
            color: #20B2AA; /* Light Sea Green */
        }

        .box input[type="submit"] {
            background-color: #4682B4; /* Steel Blue */
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .box input[type="submit"]:hover {
            opacity: 0.8;
        }

        /* Shopping Cart Styles */
        .shopping-cart {
            background-color: #ffffff; /* White Background */
            border: 1px solid #000000; /* Black Border */
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .shopping-cart .heading {
            font-size: 24px;
            color: #00BFFF; /* Deep Sky Blue */
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #000000; /* Black Border */
            text-align: left;
        }

        table th {
            background-color: #20B2AA; /* Light Sea Green */
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f0f8ff; /* Alice Blue */
        }

        .table-bottom {
            font-weight: bold;
        }

        .cart-btn {
            text-align: right;
        }

        .cart-btn .btn {
            background-color: #4682B4; /* Steel Blue */
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        .cart-btn .btn.disabled {
            background-color: #B0C4DE; /* Light Steel Blue */
            cursor: not-allowed;
        }

        .cart-btn .btn:hover {
            opacity: 0.8;
        }

        /* Message Styles */
        .message {
            background-color: #00BFFF; /* Deep Sky Blue */
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .message:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
   
<?php
if (isset($message)) {
    foreach ($message as $message) {
        echo '<div class="message" onclick="this.remove();">' . $message . '</div>';
    }
}
?>

<div class="container">

<div class="user-profile">

    <?php
    $select_user = mysqli_query($conn, "SELECT * FROM `user_info` WHERE id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($select_user) > 0) {
        $fetch_user = mysqli_fetch_assoc($select_user);
    }
    ?>

    <p>Username: <span><?php echo $fetch_user['name']; ?></span></p>
    <p>Email: <span><?php echo $fetch_user['email']; ?></span></p>
    <div class="flex">
        <a href="index.html" class="btn">Home</a> <!-- Link to index.html -->
        <a href="register.php" class="option-btn">Register</a>
        <a href="profile_&_products.php?logout=true" onclick="return confirm('Are you sure you want to logout?');" class="delete-btn">Logout</a>
    </div>

</div>

<div class="products">

    <h1 class="heading">Latest Products</h1>

    <div class="box-container">

    <?php
    $select_product = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
    if (mysqli_num_rows($select_product) > 0) {
        while ($fetch_product = mysqli_fetch_assoc($select_product)) {
    ?>
    <form method="post" class="box" action="">
        <img src="images/<?php echo $fetch_product['image']; ?>" alt="">
        <div class="name"><?php echo $fetch_product['name']; ?></div>
        <div class="price">£<?php echo number_format($fetch_product['price'], 2); ?>/-</div>
        <input type="number" min="1" name="product_quantity" value="1">
        <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
        <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
        <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
        <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
    </form>
    <?php
        }
    }
    ?>

    </div>

</div>

<div class="shopping-cart">

    <h1 class="heading">Shopping Cart</h1>

    <table>
        <thead>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Action</th>
        </thead>
        <tbody>
        <?php
        $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        $grand_total = 0;
        if (mysqli_num_rows($cart_query) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
        ?>
        <tr>
            <td><img src="images/<?php echo $fetch_cart['image']; ?>" height="100" alt=""></td>
            <td><?php echo $fetch_cart['name']; ?></td>
            <td>£<?php echo number_format($fetch_cart['price'], 2); ?>/-</td>
            <td>
                <form action="" method="post">
                    <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                    <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                    <input type="submit" name="update_cart" value="Update" class="option-btn">
                </form>
            </td>
            <td>£<?php echo number_format($sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']), 2); ?>/-</td>
            <td><a href="profile_&_products.php?remove=<?php echo $fetch_cart['id']; ?>" class="delete-btn" onclick="return confirm('Remove item from cart?');">Remove</a></td>
        </tr>
        <?php
        $grand_total += $sub_total;
            }
        } else {
            echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="6">No item added</td></tr>';
        }
        ?>
        <tr class="table-bottom">
            <td colspan="4">Grand Total:</td>
            <td>£<?php echo number_format($grand_total, 2); ?>/-</td>
            <td><a href="profile_&_products.php?delete_all" onclick="return confirm('Delete all from cart?');" class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">Delete All</a></td>
        </tr>
        </tbody>
    </table>

    <div class="cart-btn">  
        <a href="#" class="btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">Proceed to Checkout</a>
    </div>

</div>

</div>

</body>
</html>
