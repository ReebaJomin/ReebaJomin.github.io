<?php
$conn = new mysqli('localhost', 'root', '', 'library');

// Fetch categories and authors for dropdown
$categories = $conn->query("SELECT * FROM categories");
$authors = $conn->query("SELECT * FROM authors");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission
    $title = $_POST['title'];
    $year = $_POST['year'];
    $category = $_POST['category'];
    $author_ids = $_POST['authors'];
    
    // Insert into books
    $conn->query("INSERT INTO books (title, publication_year, category_id) VALUES ('$title', '$year', '$category')");
    $book_id = $conn->insert_id;

    // Insert into book_authors
    foreach ($author_ids as $author_id) {
        $conn->query("INSERT INTO book_authors (book_id, author_id) VALUES ('$book_id', '$author_id')");
    }
    
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
</head>
<body>
<h1>Add Book</h1>
<form method="POST">
    <label>Title:</label>
    <input type="text" name="title" required><br>
    
    <label>Year:</label>
    <input type="number" name="year" required><br>
    
    <label>Category:</label>
    <select name="category" required>
        <?php while ($row = $categories->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
        <?php endwhile; ?>
    </select><br>
    
    <label>Authors:</label>
    <select name="authors[]" multiple required>
        <?php while ($row = $authors->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
        <?php endwhile; ?>
    </select><br>
    
    <button type="submit">Add Book</button>
</form>
</body>
</html>
