<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'library');

// Check if the connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the book ID from the URL
$book_id = $_GET['id'];

// Fetch the book's existing details
$bookQuery = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($bookQuery);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$bookResult = $stmt->get_result();
$book = $bookResult->fetch_assoc();

// Fetch categories for the dropdown
$categoryQuery = "SELECT * FROM categories";
$categories = $conn->query($categoryQuery);

// Fetch authors for the dropdown
$authorQuery = "SELECT * FROM authors";
$authors = $conn->query($authorQuery);

// Fetch selected authors for this book (to mark as selected in the form)
$selectedAuthorsQuery = "SELECT author_id FROM book_authors WHERE book_id = ?";
$stmt = $conn->prepare($selectedAuthorsQuery);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$selectedAuthorsResult = $stmt->get_result();
$selectedAuthors = [];
while ($row = $selectedAuthorsResult->fetch_assoc()) {
    $selectedAuthors[] = $row['author_id'];
}

// Handle form submission for updating the book
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated details from the form
    $title = $_POST['title'];
    $year = $_POST['year'];
    $category = $_POST['category'];
    $author_ids = $_POST['authors'];

    // Update the book's details in the 'books' table
    $updateBookQuery = "UPDATE books SET title = ?, publication_year = ?, category_id = ? WHERE id = ?";
    $stmt = $conn->prepare($updateBookQuery);
    $stmt->bind_param("ssii", $title, $year, $category, $book_id);
    $stmt->execute();

    // Update the authors in the 'book_authors' pivot table
    // First, delete existing authors for this book
    $conn->query("DELETE FROM book_authors WHERE book_id = $book_id");

    // Then, insert the selected authors
    foreach ($author_ids as $author_id) {
        $insertAuthorQuery = "INSERT INTO book_authors (book_id, author_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertAuthorQuery);
        $stmt->bind_param("ii", $book_id, $author_id);
        $stmt->execute();
    }

    // Redirect back to the book list after successful update
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
</head>
<body>
<h1>Edit Book</h1>
<form method="POST">
    <label>Title:</label>
    <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required><br>

    <label>Year:</label>
    <input type="number" name="year" value="<?= htmlspecialchars($book['publication_year']) ?>" required><br>

    <label>Category:</label>
    <select name="category" required>
        <option value="" disabled>Select a category</option>
        <?php while ($row = $categories->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= $row['id'] == $book['category_id'] ? 'selected' : '' ?>>
                <?= $row['name'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <label>Authors:</label>
    <select name="authors[]" multiple required>
        <?php while ($row = $authors->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= in_array($row['id'], $selectedAuthors) ? 'selected' : '' ?>>
                <?= $row['name'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    <button type="submit">Update Book</button>
</form>
</body>
</html>
