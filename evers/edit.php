[file name]: edit.php
[file content begin]
<?php
require_once 'auth_config.php';
require_once 'config.php';
requireAuth(); // Require authentication to access this page

// Initialize variables
$error = '';
$success = '';
$eventData = [];

// Check if ID parameter is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: display.php');
    exit;
}

$eventId = (int)$_GET['id'];

// Fetch event data
try {
    $stmt = $pdo->prepare("SELECT * FROM vital_events WHERE id = ?");
    $stmt->execute([$eventId]);
    $eventData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$eventData) {
        header('Location: display.php?error=not_found');
        exit;
    }
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $fullName = htmlspecialchars(trim($_POST['fullName']));
    $dateOfBirth = htmlspecialchars(trim($_POST['dateOfBirth']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    $addressLine1 = htmlspecialchars(trim($_POST['addressLine1']));
    $addressLine2 = htmlspecialchars(trim($_POST['addressLine2']));
    $city = htmlspecialchars(trim($_POST['city']));
    $postalCode = htmlspecialchars(trim($_POST['postalCode']));
    $nationality = htmlspecialchars(trim($_POST['nationality']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = htmlspecialchars(trim($_POST['email']));
    $eventType = htmlspecialchars(trim($_POST['eventType']));
    $eventDate = htmlspecialchars(trim($_POST['eventDate']));
    $eventLocation = htmlspecialchars(trim($_POST['eventLocation']));
    $eventDescription = htmlspecialchars(trim($_POST['eventDescription']));
    
    // Validate required fields
    $errors = [];
    
    if (empty($fullName)) $errors[] = 'Full name is required';
    if (empty($dateOfBirth)) $errors[] = 'Date of birth is required';
    if (empty($gender)) $errors[] = 'Gender is required';
    if (empty($addressLine1)) $errors[] = 'Address Line 1 is required';
    if (empty($city)) $errors[] = 'City is required';
    if (empty($postalCode)) $errors[] = 'Postal Code is required';
    if (empty($nationality)) $errors[] = 'Nationality is required';
    if (empty($phone)) $errors[] = 'Phone number is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($eventType)) $errors[] = 'Event type is required';
    if (empty($eventDate)) $errors[] = 'Event date is required';
    if (empty($eventLocation)) $errors[] = 'Event location is required';
    
    // Email validation
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($errors)) {
        try {
            // Update event in database
            $stmt = $pdo->prepare("UPDATE vital_events SET 
                full_name = ?, 
                date_of_birth = ?, 
                gender = ?, 
                address_line1 = ?, 
                address_line2 = ?, 
                city = ?, 
                postal_code = ?, 
                nationality = ?, 
                phone = ?, 
                email = ?, 
                event_type = ?, 
                event_date = ?, 
                event_location = ?, 
                event_description = ? 
                WHERE id = ?");
            
            $stmt->execute([
                $fullName, $dateOfBirth, $gender, $addressLine1, $addressLine2, 
                $city, $postalCode, $nationality, $phone, $email, $eventType, 
                $eventDate, $eventLocation, $eventDescription, $eventId
            ]);
            
            $success = 'Event updated successfully!';
            // Refresh event data
            $stmt = $pdo->prepare("SELECT * FROM vital_events WHERE id = ?");
            $stmt->execute([$eventId]);
            $eventData = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Vital Events</title>
    <link rel="stylesheet" href="styles.evers.css">
    <style>
        .user-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #3498db;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h2>Vital Events Registration System</h2>
            <p>Edit Event Registration</p>
        </header>

        <div class="user-info">
            <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <div>
                <a href="display.php" style="margin-right: 15px; color: #3498db; text-decoration: none;">View Events</a>
                <a href="logout.php" style="color: #e74c3c; text-decoration: none;">Logout</a>
            </div>
        </div>

        <a href="display.php" class="back-link">&larr; Back to Events</a>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form id="editEventForm" method="POST" action="">
            <!-- Step 1: Personal Information -->
            <fieldset>
                <legend><b><i>Personal Information</i></b></legend>

                <div class="form-group">
                    <label for="fullName">Full Name <span style="color: red">*</span></label>
                    <input type="text" id="fullName" name="fullName" required 
                           value="<?php echo htmlspecialchars($eventData['full_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="gender">Gender <span style="color: red">*</span></label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo (isset($eventData['gender']) && $eventData['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo (isset($eventData['gender']) && $eventData['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo (isset($eventData['gender']) && $eventData['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="dateOfBirth">Date of Birth <span style="color: red">*</span></label>
                    <input type="date" id="dateOfBirth" name="dateOfBirth" required 
                           value="<?php echo htmlspecialchars($eventData['date_of_birth'] ?? ''); ?>">
                </div>
            </fieldset>

            <!-- Step 2: Address Information -->
            <fieldset>
                <legend><b><i>Address Information</i></b></legend>

                <div class="form-group">
                    <label for="addressLine1">Address Line 1 <span style="color: red">*</span></label>
                    <input type="text" id="addressLine1" name="addressLine1" required 
                           value="<?php echo htmlspecialchars($eventData['address_line1'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="addressLine2">Address Line 2</label>
                    <input type="text" id="addressLine2" name="addressLine2" 
                           value="<?php echo htmlspecialchars($eventData['address_line2'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="city">City <span style="color: red">*</span></label>
                    <input type="text" id="city" name="city" required 
                           value="<?php echo htmlspecialchars($eventData['city'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="postalCode">Postal Code <span style="color: red">*</span></label>
                    <input type="text" id="postalCode" name="postalCode" required 
                           value="<?php echo htmlspecialchars($eventData['postal_code'] ?? ''); ?>">
                </div>
            </fieldset>

            <!-- Step 3: Contact Information -->
            <fieldset>
                <legend><b><i>Contact Information</i></b></legend>

                <div class="form-group">
                    <label for="nationality">Nationality <span style="color: red">*</span></label>
                    <select id="nationality" name="nationality" required>
                        <option value="">Select Nationality</option>
                        <option value="Ethiopia" <?php echo (isset($eventData['nationality']) && $eventData['nationality'] == 'Ethiopia') ? 'selected' : ''; ?>>Ethiopia</option>
                        <option value="Botswana" <?php echo (isset($eventData['nationality']) && $eventData['nationality'] == 'Botswana') ? 'selected' : ''; ?>>Botswana</option>
                        <option value="Burundi" <?php echo (isset($eventData['nationality']) && $eventData['nationality'] == 'Burundi') ? 'selected' : ''; ?>>Burundi</option>
                        <option value="Cameroon" <?php echo (isset($eventData['nationality']) && $eventData['nationality'] == 'Cameroon') ? 'selected' : ''; ?>>Cameroon</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number <span style="color: red">*</span></label>
                    <input type="tel" id="phone" name="phone" required 
                           value="<?php echo htmlspecialchars($eventData['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address <span style="color: red">*</span></label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($eventData['email'] ?? ''); ?>">
                </div>
            </fieldset>

            <!-- Step 4: Event Details -->
            <fieldset>
                <legend><b><i>Event Details</i></b></legend>

                <div class="form-group">
                    <label for="eventType">Event Type <span style="color: red">*</span></label>
                    <select id="eventType" name="eventType" required>
                        <option value="">Select Event Type</option>
                        <option value="Birth" <?php echo (isset($eventData['event_type']) && $eventData['event_type'] == 'Birth') ? 'selected' : ''; ?>>Birth</option>
                        <option value="Marriage" <?php echo (isset($eventData['event_type']) && $eventData['event_type'] == 'Marriage') ? 'selected' : ''; ?>>Marriage</option>
                        <option value="Divorce" <?php echo (isset($eventData['event_type']) && $eventData['event_type'] == 'Divorce') ? 'selected' : ''; ?>>Divorce</option>
                        <option value="Death" <?php echo (isset($eventData['event_type']) && $eventData['event_type'] == 'Death') ? 'selected' : ''; ?>>Death</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="eventDate">Event Date <span style="color: red">*</span></label>
                    <input type="date" id="eventDate" name="eventDate" required 
                           value="<?php echo htmlspecialchars($eventData['event_date'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="eventLocation">Event Location <span style="color: red">*</span></label>
                    <input type="text" id="eventLocation" name="eventLocation" required 
                           value="<?php echo htmlspecialchars($eventData['event_location'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="eventDescription">Event Description</label>
                    <textarea id="eventDescription" name="eventDescription" rows="4"><?php echo htmlspecialchars($eventData['event_description'] ?? ''); ?></textarea>
                </div>
            </fieldset>

            <div class="button-group">
                <a href="display.php" class="btn-prev" style="text-decoration: none; text-align: center;">Cancel</a>
                <button type="submit" class="btn-submit">Update Event</button>
            </div>
        </form>
    </div>

    <script>
        // Basic form validation
        document.getElementById('editEventForm').addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = this.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            // Email validation
            const emailField = document.getElementById('email');
            if (emailField.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value)) {
                isValid = false;
                emailField.style.borderColor = '#e74c3c';
                alert('Please enter a valid email address');
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    </script>
</body>
</html>
[file content end]