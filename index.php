<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'library');

// Fetch books with category and authors
$sql = "SELECT books.id, books.title, books.publication_year, categories.name AS category
        FROM books
        LEFT JOIN categories ON books.category_id = categories.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Management System</title>
</head>
<body>
<h1>Books</h1>
<a href="add_book.php">Add New Book</a>
<table border="1">
    <tr>
        <th>Title</th>
        <th>Publication Year</th>
        <th>Category</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['title'] ?></td>
        <td><?= $row['publication_year'] ?></td>
        <td><?= $row['category'] ?></td>
        <td>
            <a href="edit_book.php?id=<?= $row['id'] ?>">Edit</a>
            <a href="delete_book.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
