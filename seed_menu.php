<?php
// Seed default categories and menu items
include 'db.php';

$items = [
    'Staple Food' => [
        ['name' => 'Nasi Lemak', 'price' => 6.50],
        ['name' => 'Chicken Rice', 'price' => 7.00],
    ],
    'Drinks' => [
        ['name' => 'Iced Lemon Tea', 'price' => 2.50],
        ['name' => 'Coffee', 'price' => 3.00],
    ],
    'Desserts' => [
        ['name' => 'Pandan Cake', 'price' => 4.00],
        ['name' => 'Mango Pudding', 'price' => 4.50],
    ]
];

foreach($items as $catName => $list){
    $catRes = mysqli_query($conn, "SELECT id FROM categories WHERE name='".mysqli_real_escape_string($conn,$catName)."'");
    if(mysqli_num_rows($catRes) > 0){
        $cat = mysqli_fetch_assoc($catRes);
        $catId = $cat['id'];
    } else {
        mysqli_query($conn, "INSERT INTO categories(name) VALUES('".mysqli_real_escape_string($conn,$catName)."')");
        $catId = mysqli_insert_id($conn);
    }

    foreach($list as $m){
        $name = mysqli_real_escape_string($conn, $m['name']);
        $price = floatval($m['price']);
        $check = mysqli_query($conn, "SELECT id FROM menu WHERE name='$name' AND category_id='$catId'");
        if(mysqli_num_rows($check) == 0){
            mysqli_query($conn, "INSERT INTO menu(category_id,name,price) VALUES('$catId','$name','$price')");
        }
    }
}

echo "Seed complete.";

?>
