<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'library');

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch books along with their category and authors
$sql = "
    SELECT books.id, books.title, books.publication_year, categories.name AS category
    FROM books
    LEFT JOIN categories ON books.category_id = categories.id";

$result = $conn->query($sql);
?>

<!-- Include the index.html file -->
<?php include 'index.html'; ?>

<?php
// Start PHP to generate table rows for the books
if ($result->num_rows > 0) {
    // Output each book
    while ($row = $result->fetch_assoc()) {
        // Fetch authors for the current book
        $book_id = $row['id'];
        $authorQuery = "
            SELECT authors.name 
            FROM authors 
            JOIN book_authors ON authors.id = book_authors.author_id 
            WHERE book_authors.book_id = $book_id";
        $authorResult = $conn->query($authorQuery);

        // Collect authors into a string
        $authors = [];
        while ($authorRow = $authorResult->fetch_assoc()) {
            $authors[] = $authorRow['name'];
        }
        $authorList = implode(', ', $authors);

        // Generate the table row
        echo "
        <script>
            document.getElementById('book-list').innerHTML += `
                <tr>
                    <td>{$row['title']}</td>
                    <td>{$row['publication_year']}</td>
                    <td>{$row['category']}</td>
                    <td>{$authorList}</td>
                    <td>
                        <a href='edit_book.php?id={$row['id']}'>Edit</a> |
                        <a href='delete_book.php?id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this book?\")'>Delete</a>
                    </td>
                </tr>
            `;
        </script>";
    }
} else {
    echo "
    <script>
        document.getElementById('book-list').innerHTML = '<tr><td colspan=\"5\">No books found</td></tr>';
    </script>";
}

// Close the database connection
$conn->close();
?>
