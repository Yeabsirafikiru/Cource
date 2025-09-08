<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>php test</title>
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      }
      body {
        background-color: #f5f5f5;
        color: #333;
        line-height: 1.6;
      }
      header {
        background-color: #4caf50;
        color: white;
        padding: 15px;
        text-align: center;
      }
      .auth-container {
        display: flex;
        justify-content: center;
      }
      .form-container {
        background-color: white;
        padding: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        width: 50%;
        display: flex;
        justify-content: center;
      }
      h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #4caf50;
      }
      .form-group {
        margin-bottom: 20px;
      }
      label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
      }
      input[type="text"],
      input[type="password"],
      input[type="email"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
      }
      button {
        background-color: #4caf50;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
        font-weight: 600;
        transition: background-color 0.3s;
      }
      button:hover {
        background-color: #a04598;
      }
      .message {
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
        text-align: center;
      }
      .error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
      }
      .success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
      }
      .nav {
        display: flex;
        justify-content: right;
        margin-bottom: 10px;
      }
      .nav a {
        background-color: #eff1ef;
        text-decoration: none;
        font-weight: 600;
        padding: 5px 10px;
      }
      .nav a:hover {
        background-color: #f5e8f0;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0px;
        background-color: white;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
      }
      th,
      td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
      }
      th {
        background-color: #4caf50;
        color: white;
      }
      tr:hover {
        background-color: #f5f5f5;
      }
      .action-btn {
        padding: 6px 12px;
        margin-right: 5px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        display: inline-block;
        width: auto;
        color: white;
      }
      .edit {
        background-color: #2196f3;
      }
      .edit:hover {
        background-color: #0b7dda;
      }
      .delete {
        background-color: #f44336;
      }
      .delete:hover {
        background-color: #d32f2f;
      }
      span {
        color: red;
      }
      fieldset {
        padding: 15px;
        border: 1px solid orchid;
        margin-bottom: 15px;
      }
      p {
        text-align: center;
        font-style: italic;
      }
      .toggle-buttons {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
      }
      .toggle-buttons button {
        width: 150px;
        margin: 0 10px;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <?php
      $host = 'localhost';
      $dbname = 'crud_system';
      $username = 'root';
      $password = '';

      try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        echo "<div class='message error'>Connection failed: " . $e->getMessage() . "</div>";
        die();
      }
      
      function initializeDatabase($pdo) {
        $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )"; 
        $pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS items (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        user_id INT(11),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
        )"; 
        $pdo->exec($sql);
      }
      
      initializeDatabase($pdo);
      session_start();

      if(isset($_GET['logout'])){
        session_destroy();
        header("Location: ".strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
      }
      
      $isLoggedIn = isset($_SESSION['user_id']);
      $error = '';

      if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(isset($_POST['register'])){
          $username = trim($_POST['username']);
          $email = trim($_POST['email']);
          $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
          
          try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $password]);
            $_SESSION['message'] = "Registration successful! Please login.";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
          } catch(PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
          }
        }
        elseif (isset($_POST['login'])){
          $username = trim($_POST['username']);
          $password = $_POST['password'];
          $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
          $stmt->execute([$username]);
          $user = $stmt->fetch();
          
          if ($user && password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
          } else {
            $error = "Invalid username or password!";
          }
        }
        elseif (isset($_POST['add_item'])){
          if ($isLoggedIn){
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $user_id = $_SESSION['user_id'];
            
            try{
              $stmt = $pdo->prepare("INSERT INTO items (name, description, user_id) VALUES (?, ?, ?)");
              $stmt->execute([$name, $description, $user_id]);
              $_SESSION['message'] = "Item added successfully!";
              header("Location: ".$_SERVER['PHP_SELF']);
              exit();
            } catch(PDOException $e){
              $error = "Failed to add item: " . $e->getMessage();
            }
          }
        }
        elseif(isset($_POST['update_item'])){
          if($isLoggedIn){
            $id = $_POST['id'];
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $user_id = $_SESSION['user_id'];
            
            $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
            $item = $stmt->fetch();
            
            if ($item){
              try{
                $stmt = $pdo->prepare("UPDATE items SET name = ?, description = ? WHERE id = ?");
                $stmt->execute([$name, $description, $id]);
                $_SESSION["message"] = "Item updated successfully!";
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
              } catch(PDOException $e){
                $error = "Failed to update item: " . $e->getMessage();
              }
            } else {
              $error = "Item not found or you don't have permission to edit it!";
            }
          }
        }
      }
      
      if (isset($_GET['delete'])){
        if($isLoggedIn){
          $id = $_GET['delete'];
          $user_id = $_SESSION['user_id'];
          
          $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
          $stmt->execute([$id, $user_id]);
          $item = $stmt->fetch();
          
          if($item){
            try{
              $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
              $stmt->execute([$id]);
              $_SESSION['message'] = "Item deleted successfully!";
              header("Location: ".$_SERVER['PHP_SELF']);
              exit();
            } catch(PDOException $e){
              $error = "Failed to delete item: " . $e->getMessage();
            }
          } else {
            $error = "Item not found or you don't have permission to delete it!";
          }
        }
      }

      if (isset($_SESSION['message'])){
        $msg = $_SESSION['message'];
        echo "<div class='message success'>" . $msg . "</div>";
        unset($_SESSION['message']);
      }
      
      if(isset($error) && $error != ''){
        echo "<div class='message error'>" . $error . "</div>";
      }
      ?>
      
      <?php if (!$isLoggedIn): ?>
        <div class="auth-container">
          <div class="form-container">
            <div id="loginDiv" style="display: block; width: 100%;">
              <fieldset>
                <legend>Login</legend>
                <form action="" method="POST">
                  <div class="form-group">
                    <label for="login_username">Username <span>*</span></label>
                    <input type="text" name="username" id="login_username" required>
                  </div>
                  <div class="form-group">
                    <label for="login_password">Password <span>*</span></label>
                    <input type="password" name="password" id="login_password" required>
                  </div>
                  <button type="submit" name="login">Login</button>
                  <p>Don't have an account? <a href="#" onclick="toggleAuth()">Register here</a></p>
                </form>
              </fieldset>
            </div>
            
            <div id="registerDiv" style="display: none; width: 100%;">
              <fieldset>
                <legend>Register</legend>
                <form action="" method="POST">
                  <div class="form-group">
                    <label for="reg_username">Username <span>*</span></label>
                    <input type="text" name="username" id="reg_username" required>
                  </div>
                  <div class="form-group">
                    <label for="reg_email">Email <span>*</span></label>
                    <input type="email" name="email" id="reg_email" required>
                  </div>
                  <div class="form-group">
                    <label for="reg_password">Password <span>*</span></label>
                    <input type="password" name="password" id="reg_password" required>
                  </div>
                  <button type="submit" name="register">Register</button>
                  <p>Already have an account? <a href="#" onclick="toggleAuth()">Login here</a></p>
                </form>
              </fieldset>
            </div>
          </div>
        </div>
        
      <?php else: ?>
        <header>
          <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
          <div class="nav">
            <a href="?logout=true">Logout&#x2192;</a>
          </div>
        </header>
        
        <div class="toggle-buttons">
          <button onclick="showAddForm()">Add New Item</button>
          <button onclick="showItemList()">View Items List</button>
        </div>
        
        <div id="addItemDiv" class="form-container" style="display:block;">
          <fieldset>
            <legend><?php echo isset($_GET['edit']) ? 'Edit Item' : 'Add New Item'; ?></legend>
            <form action="" method="POST">
              <?php 
              if (isset($_GET['edit'])) {
                $id = $_GET['edit'];
                $user_id = $_SESSION['user_id'];
                $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
                $stmt->execute([$id, $user_id]);
                $item = $stmt->fetch();
                
                if($item): ?>
                  <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                <?php else: ?>
                  <div class="message error">Item not found!</div>
                <?php endif;
              } ?>
              
              <div class="form-group">
                <label for="name">Name <span>*</span></label>
                <input type="text" name="name" id="name" value="<?php echo isset($item) ? $item['name'] : ''; ?>" required>
              </div>
              <div class="form-group">
                <label for="description">Description <span>*</span></label>
                <input type="text" name="description" id="description" value="<?php echo isset($item) ? $item['description'] : ''; ?>" required>
              </div>
              <button type="submit" name="<?php echo isset($_GET['edit']) ? 'update_item' : 'add_item'; ?>">
                <?php echo isset($_GET['edit']) ? 'Update Item' : 'Add Item'; ?>
              </button>
            </form>
          </fieldset>
        </div>
        
        <div id="itemListDiv" style="display:none; margin:10px;">
          <fieldset>
            <legend>Item List</legend>
            <?php 
            $user_id = $_SESSION['user_id'];
            $stmt = $pdo->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
            $items = $stmt->fetchAll();
            
            if(count($items) > 0): ?>
            <table>
              <thead>
                <tr>
                  <th>S.N</th> 
                  <th>Name</th> 
                  <th>Description</th> 
                  <th>Created At</th> 
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 0;
                foreach ($items as $item): $i++?>
                <tr>
                  <td><?php echo $i; ?></td>
                  <td><?php echo htmlspecialchars($item['name']);?></td>
                  <td><?php echo htmlspecialchars($item['description']);?></td>
                  <td><?php echo date('M j, Y g:i A', strtotime($item['created_at']));?></td>
                  <td>
                    <a href="?edit=<?php echo $item['id']?>" class="action-btn edit">&#x270f; Edit</a>
                    <a href="?delete=<?php echo $item['id']?>" onclick="return confirm('Are you sure?')" class="action-btn delete">&#x1F5D1; Delete</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <?php else: ?>
              <p>No items found</p>
            <?php endif; ?>
          </fieldset>
        </div>
      <?php endif; ?>
    </div>
    
    <script>
      function toggleAuth() {
        const loginDiv = document.getElementById("loginDiv");
        const registerDiv = document.getElementById("registerDiv");
        
        if (loginDiv.style.display === "none") {
          loginDiv.style.display = "block";
          registerDiv.style.display = "none";
        } else {
          loginDiv.style.display = "none";
          registerDiv.style.display = "block";
        }
      }
      
      function showAddForm() {
        document.getElementById("addItemDiv").style.display = "block";
        document.getElementById("itemListDiv").style.display = "none";
      }
      
      function showItemList() {
        document.getElementById("addItemDiv").style.display = "none";
        document.getElementById("itemListDiv").style.display = "block";
      }
      
      <?php if (isset($_GET['edit'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
          showAddForm();
        });
      <?php endif; ?>
    </script>
  </body>
</html>