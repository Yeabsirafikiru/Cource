<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fullName = htmlspecialchars($_POST['fullName']);
    $dateOfBirth = htmlspecialchars($_POST['dateOfBirth']);
    $gender = htmlspecialchars($_POST['gender']);
    $nationality = htmlspecialchars($_POST['nationality']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $eventType = htmlspecialchars($_POST['eventType']);
    $eventDate = htmlspecialchars($_POST['eventDate']);
    $eventLocation = htmlspecialchars($_POST['eventLocation']);

    $errors = [];

    if (empty($fullName)) $errors[] = 'Full name is required';
    if (empty($dateOfBirth)) $errors[] = 'Date of birth is required';
    if (empty($gender)) $errors[] = 'Gender is required';
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

    if (!empty($errors)){
        echo '<h2>Error</h2>';
        echo '<ul>';
        foreach ($errors as $error){
            echo "<li>$error</li>";
        }
        echo '</ul>';
        echo '<a href="javascript:history.back()">Go back and try again</a>';
        exit;
    }

    try{
        $stmt = $pdo->prepare("INSERT INTO vital_events(full_name, date_of_birth, gender, nationality, phone, email, event_type, event_date, event_location, registration_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
     
        $stmt->execute([
            $fullName, $dateOfBirth, $gender, $nationality, $phone, $email, $eventType, $eventDate, $eventLocation
        ]);
    } catch(PDOException $e){
        die("DATABASE error: " . $e->getMessage());
    }
    
    header('Location: success.php');
    exit;
     
} else{
    header('Location: index.html');
    exit;
}
?>