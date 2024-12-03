<?php

include 'conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_add'])) {
    // Sanitize user inputs
    $title = htmlspecialchars($_POST['title']);
    $author = htmlspecialchars($_POST['author']);
    $publisher = htmlspecialchars($_POST['publisher']);
    $year_published = filter_var($_POST['year_published'], FILTER_VALIDATE_INT);
    $genre = htmlspecialchars($_POST['genre']);

    // Validate inputs
    if (!$title || !$author || !$publisher || !$year_published || !$genre) {
        $_SESSION['error'] = "Invalid input. Please fill out all fields correctly.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    try {
        // Insert data into the database
        $stmt = $pdo->prepare("INSERT INTO books (title, author, publisher, year_published, genre) 
                               VALUES (:title, :author, :publisher, :year_published, :genre)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':publisher', $publisher);
        $stmt->bindParam(':year_published', $year_published);
        $stmt->bindParam(':genre', $genre);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Book added successfully!";
        } else {
            $_SESSION['error'] = "Failed to insert book into the database.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    // Redirect back to the form
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management CRUD Tables with Forms</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        input[type="text"], input[type="number"], input[type="date"], button {
            padding: 8px;
            font-size: 14px;
        }
        .search {
            margin-top: 10px;
        }
    </style>
</head>
<h2>Add New Book</h2>
<form method="POST">
    <input type="text" name="title" placeholder="Title" required>
    <input type="text" name="author" placeholder="Author" required>
    <input type="text" name="publisher" placeholder="Publisher" required>
    <input type="number" name="year_published" placeholder="Year Published" required>
    <input type="text" name="genre" placeholder="Genre" required>
    <button type="submit" name="submit_add">Add Book</button>
</form>

<table class="table table-bordered">
    <thead class="table-primary">
        <tr>
            <th>ID Book</th>
            <th>Title</th>
            <th>Author</th>
            <th>Publisher</th>
            <th>Year Published</th>
            <th>Genre</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $books = [];
        try {
            $stmt = $pdo->query("SELECT * FROM books ORDER BY book_id");
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $message = "Error fetching data: " . $e->getMessage();
        }
        ?>
        
        <?php if (!empty($books)) : ?>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['book_id']); ?></td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo htmlspecialchars($book['publisher']); ?></td>
                    <td><?php echo htmlspecialchars($book['year_published']); ?></td>
                    <td><?php echo htmlspecialchars($book['genre']); ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this book?');" style="display:inline;">
                            <input type="hidden" name="submit_delete" value="1">
                            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">No data available</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


    <!-- Members Form -->
    <h2>Members Table</h2>
    <form id="members-form">
        <input type="text" id="member-name" placeholder="Name" required>
        <input type="text" id="member-email" placeholder="Email" required>
        <input type="text" id="member-phone" placeholder="Phone" required>
        <input type="text" id="member-address" placeholder="Address" required>
        <button type="button" onclick="addRow('members')">Add Member</button>
    </form>
    <input type="text" id="search-members" class="search" onkeyup="searchTable('members')" placeholder="Search Members">
    <table id="members">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Loans Form -->
    <h2>Loans Table</h2>
    <form id="loans-form">
        <input type="number" id="loan-book-id" placeholder="Book ID" required>
        <input type="number" id="loan-member-id" placeholder="Member ID" required>
        <input type="date" id="loan-date" required>
        <input type="date" id="return-date">
        <button type="button" onclick="addRow('loans')">Add Loan</button>
    </form>
    <input type="text" id="search-loans" class="search" onkeyup="searchTable('loans')" placeholder="Search Loans">
    <table id="loans">
        <thead>
            <tr>
                <th>Book ID</th>
                <th>Member ID</th>
                <th>Loan Date</th>
                <th>Return Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        function addRow(tableId) {
            let values = [];
            if (tableId === 'books') {
                values = [
                    document.getElementById('book-title').value,
                    document.getElementById('book-author').value,
                    document.getElementById('book-publisher').value,
                    document.getElementById('book-year').value,
                    document.getElementById('book-genre').value
                ];
            } else if (tableId === 'members') {
                values = [
                    document.getElementById('member-name').value,
                    document.getElementById('member-email').value,
                    document.getElementById('member-phone').value,
                    document.getElementById('member-address').value
                ];
            } else if (tableId === 'loans') {
                values = [
                    document.getElementById('loan-book-id').value,
                    document.getElementById('loan-member-id').value,
                    document.getElementById('loan-date').value,
                    document.getElementById('return-date').value
                ];
            }

            const table = document.getElementById(tableId).querySelector('tbody');
            const row = table.insertRow();
            values.forEach(value => {
                const cell = row.insertCell();
                cell.textContent = value || '-';
            });

            const actionCell = row.insertCell();
            actionCell.innerHTML = `
                <button onclick="deleteRow(this)">Delete</button>
                <button onclick="editRow(this)">Edit</button>
            `;

            document.getElementById(`${tableId}-form`).reset(); // Reset form inputs
        }

        function deleteRow(button) {
            const row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }

        function editRow(button) {
            const row = button.parentNode.parentNode;
            const cells = row.querySelectorAll('td');

            for (let i = 0; i < cells.length - 1; i++) {
                const newValue = prompt(`Edit value (${cells[i].textContent}):`, cells[i].textContent);
                if (newValue !== null) {
                    cells[i].textContent = newValue.trim();
                }
            }
        }

        function searchTable(tableId) {
            const input = document.getElementById(`search-${tableId}`).value.toLowerCase();
            const rows = document.getElementById(tableId).querySelector('tbody').getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length - 1; j++) {
                    if (cells[j].textContent.toLowerCase().includes(input)) {
                        found = true;
                        break;
                    }
                }

                rows[i].style.display = found ? '' : 'none';
            }
        }
    </script>

</body>
</html>
