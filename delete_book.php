<?php
$conn = new mysqli('localhost', 'root', '', 'library');

// Delete book and associated authors
$book_id = $_GET['id'];
$conn->query("DELETE FROM books WHERE id = $book_id");
$conn->query("DELETE FROM book_authors WHERE book_id = $book_id");

header('Location: index.php');
?>
