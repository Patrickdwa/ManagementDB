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

    if (isset($_POST['submit_delete'])) {
        $id = htmlspecialchars($_POST['book_id']);
    
        try {
            // Prepare statement untuk menghapus data
            $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
            // Eksekusi query
            $stmt->execute();
    
            // Pesan sukses
            $_SESSION['success'] = "Data deleted successfully!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error deleting data: " . $e->getMessage();
        }
    
        // Redirect untuk mencegah form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['submit_edit'])) {
            $id = htmlspecialchars($_POST['book_id']);
            $title = htmlspecialchars($_POST['title']);
            $author = htmlspecialchars($_POST['author']);
            $publisher = htmlspecialchars($_POST['publisher']);
            $year_published = htmlspecialchars($_POST['year_published']);
            $genre = htmlspecialchars($_POST['genre']);
    
            try {
                $stmt = $pdo->prepare("UPDATE books SET title = :title, author = :author, publisher = :publisher, year_published = :year_published, genre = :genre WHERE book_id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':author', $author);
                $stmt->bindParam(':publisher', $publisher);
                $stmt->bindParam(':year_published', $year_published, PDO::PARAM_INT);
                $stmt->bindParam(':genre', $genre);
                $stmt->execute();
                $_SESSION['success'] = "Book updated successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error updating book: " . $e->getMessage();
            }
    
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }



    // MEMBERS //
    

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle Add Member
        if (isset($_POST['submit_add_member'])) {
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $phone = htmlspecialchars($_POST['phone']);
            $address = htmlspecialchars($_POST['address']);
    
            if (!$name || !$email || !$phone || !$address) {
                $_SESSION['error'] = "Invalid input. Please fill out all member fields correctly.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
    
            try {
                $stmt = $pdo->prepare("INSERT INTO members (name, email, phone, address) VALUES (:name, :email, :phone, :address)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);
    
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Member added successfully!";
                } else {
                    $_SESSION['error'] = "Failed to insert member into the database.";
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Database error: " . $e->getMessage();
            }
    
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    
        // Handle Delete Member
        if (isset($_POST['submit_delete_member'])) {
            $id = htmlspecialchars($_POST['member_id']);
    
            try {
                $stmt = $pdo->prepare("DELETE FROM members WHERE member_id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = "Member deleted successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error deleting member: " . $e->getMessage();
            }
    
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    
        // Handle Edit Member
        if (isset($_POST['submit_edit_member'])) {
            $id = htmlspecialchars($_POST['member_id']);
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $phone = htmlspecialchars($_POST['phone']);
            $address = htmlspecialchars($_POST['address']);
    
            try {
                $stmt = $pdo->prepare("UPDATE members SET name = :name, email = :email, phone = :phone, address = :address WHERE member_id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);
                $stmt->execute();
                $_SESSION['success'] = "Member updated successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error updating member: " . $e->getMessage();
            }
    
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // loan
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle Add Loan
        if (isset($_POST['submit_add_loan'])) {
            $book_id = htmlspecialchars($_POST['book_id']);
            $member_id = htmlspecialchars($_POST['member_id']);
            $loan_date = htmlspecialchars($_POST['loan_date']);
            $return_date = htmlspecialchars($_POST['return_date']);
    
            if (!$book_id || !$member_id || !$loan_date || !$return_date) {
                $_SESSION['error'] = "Invalid input. Please fill out all member fields correctly.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
    
            try {
                $stmt = $pdo->prepare("INSERT INTO loans (book_id, member_id, loan_date, return_date) VALUES (:book_id, :member_id, :loan_date, :return_date)");
                $stmt->bindParam(':book_id', $book_id);
                $stmt->bindParam(':member_id', $member_id);
                $stmt->bindParam(':loan_date', $loan_date);
                $stmt->bindParam(':return_date', $return_date);
    
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Member added successfully!";
                } else {
                    $_SESSION['error'] = "Failed to insert member into the database.";
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Database error: " . $e->getMessage();
            }
    
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    
        // Handle Delete Member
        if (isset($_POST['submit_delete_member'])) {
            $id = htmlspecialchars($_POST['member_id']);
    
            try {
                $stmt = $pdo->prepare("DELETE FROM members WHERE member_id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['success'] = "Member deleted successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error deleting member: " . $e->getMessage();
            }
    
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    
        // Handle Edit Member
        if (isset($_POST['submit_edit_member'])) {
            $id = htmlspecialchars($_POST['member_id']);
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $phone = htmlspecialchars($_POST['phone']);
            $address = htmlspecialchars($_POST['address']);
    
            try {
                $stmt = $pdo->prepare("UPDATE members SET name = :name, email = :email, phone = :phone, address = :address WHERE member_id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);
                $stmt->execute();
                $_SESSION['success'] = "Member updated successfully!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error updating member: " . $e->getMessage();
            }
    
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
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
    <input type="text" id="search-books" class="search" onkeyup="searchTable('books')" placeholder="Search Books">
    <table class="table table-bordered" id="books">
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
                            <form method="POST" style="display:inline;">
                            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                            <button type="button" class="btn btn-warning btn-sm" onclick="showEditForm(this)">Edit</button>
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
    <div id="edit-form" style="display:none;">
    <h3>Edit Book</h3>
    <form method="POST">
        <input type="hidden" name="book_id" id="edit-book-id">
        <input type="text" name="title" id="edit-title" placeholder="Title" required>
        <input type="text" name="author" id="edit-author" placeholder="Author" required>
        <input type="text" name="publisher" id="edit-publisher" placeholder="Publisher" required>
        <input type="number" name="year_published" id="edit-year" placeholder="Year Published" required>
        <input type="text" name="genre" id="edit-genre" placeholder="Genre" required>
        <button type="submit" name="submit_edit" class="btn btn-primary">Save Changes</button>
    </form>
</div>


        <!-- Members Form -->
        <h2>Add New Member</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone" required>
            <input type="text" name="address" placeholder="Address" required>
            <button type="submit" name="submit_add_member">Add Member</button>
        </form>

<input type="text" id="search-members" class="search" onkeyup="searchTable('members')" placeholder="Search Members">

<table id="members">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $members = [];
        try {
            $stmt = $pdo->query("SELECT * FROM members ORDER BY member_id");
            $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Error fetching members: " . $e->getMessage();
        }
        ?>

        <?php if (!empty($members)) : ?>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td><?php echo htmlspecialchars($member['member_id']); ?></td>
                    <td><?php echo htmlspecialchars($member['name']); ?></td>
                    <td><?php echo htmlspecialchars($member['email']); ?></td>
                    <td><?php echo htmlspecialchars($member['phone']); ?></td>
                    <td><?php echo htmlspecialchars($member['address']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="submit_delete_member" value="1">
                            <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($member['member_id']); ?>">
                            <button type="submit">Delete</button>
                        </form>
                        <button type="button" onclick="showEditFormMember(this)">Edit</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No members available.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="edit-form-member" style="display:none;">
    <h3>Edit Member</h3>
    <form method="POST">
        <input type="hidden" name="member_id" id="edit-member-id">
        <input type="text" name="name" id="edit-name" placeholder="Name" required>
        <input type="email" name="email" id="edit-email" placeholder="Email" required>
        <input type="text" name="phone" id="edit-phone" placeholder="Phone" required>
        <input type="text" name="address" id="edit-address" placeholder="Address" required>
        <button type="submit" name="submit_edit_member">Save Changes</button>
    </form>
</div>


        <!-- Loans Form -->
        <h2>Loans Table</h2>
        <form id="loans-form">
            <input name="book_id" type="number" id="loan-book-id" placeholder="Book ID" required>
            <input name="member_id" type="number" id="loan-member-id" placeholder="Member ID" required>
            <input name="loan_date" type="date" id="loan-date" required>
            <input name="return_date" type="date" id="return-date">
            <button name="submit_add_loan" type="submit">Add Loan</button>
        </form>
        <input type="text" id="search-loans" class="search" onkeyup="searchTable('loans')" placeholder="Search Loans">
        <table id="loans">
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Book ID</th>
                    <th>Member ID</th>
                    <th>Loan Date</th>
                    <th>Return Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $loans = [];
            try {
                $stmt = $pdo->query("SELECT * FROM loans ORDER BY loan_id");
                $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $message = "Error fetching data: " . $e->getMessage();
            }
            ?>
            
            <?php if (!empty($loans)) : ?>
                <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($loan['loan_id']); ?></td>
                        <td><?php echo htmlspecialchars($loan['book_id']); ?></td>
                        <td><?php echo htmlspecialchars($loan['member_id']); ?></td>
                        <td><?php echo htmlspecialchars($loan['loan_date']); ?></td>
                        <td><?php echo htmlspecialchars($loan['return_date']); ?></td>

                        <td>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this loan?');" style="display:inline;">
                                <input type="hidden" name="submit_delete" value="1">
                                <input type="hidden" name="loan_id" value="<?php echo htmlspecialchars($loan['loan_id']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            <form method="POST" style="display:inline;">
                            <input type="hidden" name="loan_id" value="<?php echo htmlspecialchars($loan['loan_id']); ?>">
                            <button type="button" class="btn btn-warning btn-sm" onclick="showEditFormLoan(this)">Edit</button>
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

        <div id="edit-form-loan" style="display:none;">
            <h3>Edit Loan</h3>
            <form method="POST">
                <input type="number" id="loan-book-id" placeholder="Book ID" required>
                <input type="number" id="loan-member-id" placeholder="Member ID" required>
                <input type="date" id="loan-date" required>
                <input type="date" id="return-date">
                <button type="submit" name="submit_edit_loan">Save Changes</button>
            </form>
        </div>

        <script>

        function showEditForm(button) {
            const row = button.closest('tr');
            const cells = row.querySelectorAll('td');

            document.getElementById('edit-book-id').value = cells[0].textContent.trim();
            document.getElementById('edit-title').value = cells[1].textContent.trim();
            document.getElementById('edit-author').value = cells[2].textContent.trim();
            document.getElementById('edit-publisher').value = cells[3].textContent.trim();
            document.getElementById('edit-year').value = cells[4].textContent.trim();
            document.getElementById('edit-genre').value = cells[5].textContent.trim();

            document.getElementById('edit-form').style.display = 'block';
        }

        function showEditFormMember(button) {
            const row = button.closest('tr');
            const cells = row.querySelectorAll('td');

            document.getElementById('edit-member-id').value = cells[0].textContent.trim();
            document.getElementById('edit-name').value = cells[1].textContent.trim();
            document.getElementById('edit-email').value = cells[2].textContent.trim();
            document.getElementById('edit-phone').value = cells[3].textContent.trim();
            document.getElementById('edit-address').value = cells[4].textContent.trim();

            document.getElementById('edit-form-member').style.display = 'block';
        }

        function showEditFormLoan(button) {
            const row = button.closest('tr');
            const cells = row.querySelectorAll('td');

            document.getElementById('loan-book-id').value = cells[0].textContent.trim();
            document.getElementById('loan-member-id').value = cells[1].textContent.trim();
            document.getElementById('loan-date').value = cells[2].textContent.trim();
            document.getElementById('return-date').value = cells[3].textContent.trim();

            document.getElementById('edit-form-loan').style.display = 'block';
        }
        function searchTable(tableId) {
                const input = document.getElementById(`search-${tableId}`).value.toLowerCase();
                const rows = document.getElementById(tableId).querySelector('tbody').getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    const cells = rows[i].getElementsByTagName('td');
                    let found = false;

                    for (let j = 0; j < cells.length; j++) { // Include all columns in the search
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
