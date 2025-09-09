
<?php
require_once 'auth_config.php';
require_once 'config.php';
requireAuth(); // Require authentication to access this page

// Handle delete action
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM vital_events WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        header('Location: display.php?deleted=1');
        exit;
    } catch(PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Fetch all registrations
try {
    $stmt = $pdo->query("SELECT * FROM vital_events ORDER BY registration_date DESC");
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vital Events - View Registrations</title>
    <link rel="stylesheet" href="styles.evers.css">
    <style>
        .container {
            max-width: 1200px;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .btn-edit {
            background: #f39c12;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-delete {
            background: #e74c3c;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        .btn-delete:hover, .btn-edit:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #2c3e50;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #7f8c8d;
            font-style: italic;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }
            .actions {
                flex-direction: column;
            }
            .header-actions {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h2>Vital Events Registration System</h2>
            <p>View and manage all registered vital events</p>
        </header>

        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="logout.php" style="color: #e74c3c; text-decoration: none;">Logout</a>
        </div>

        <div class="header-actions">
            <h3>Registered Events</h3>
            <div>
                <a href="index.html" style="text-decoration: none;">
                    <button style="background: #2ecc71;">Register New Event</button>
                </a>
            </div>
        </div>

        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
            <div class="alert alert-success">
                Record has been successfully deleted.
            </div>
        <?php endif; ?>

        <?php if (count($registrations) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Event Type</th>
                        <th>Event Date</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $registration): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registration['id']); ?></td>
                        <td><?php echo htmlspecialchars($registration['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($registration['event_type']); ?></td>
                        <td><?php echo htmlspecialchars($registration['event_date']); ?></td>
                        <td><?php echo htmlspecialchars($registration['registration_date']); ?></td>
                        <td class="actions">
                            <a href="edit.php?id=<?php echo $registration['id']; ?>" class="btn-edit">Edit</a>
                            <form action="display.php" method="GET" style="display: inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $registration['id']; ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>No registrations found. <a href="index.html">Register a new vital event</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>