<?php

require 'Models/dbAccess.php';
$db = new dbAccess();
function filter_string_polyfill(string $string): string
{
    $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
    return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
}


// determine which action to take
$action = filter_string_polyfill(isset($_GET['action']) ? (string)$_GET['action'] : '');

switch ($action) {

    /* -------------------------------------------------------------
     |  USER REGISTRATION & PROFILE
     ------------------------------------------------------------- */
    case 'showRegisterForm':
        include 'Views/User/RegisterForm.php';
        exit();

    case 'Register': 
        // 1) create user
        $username = filter_string_polyfill($_POST['username']);
        $email    = filter_string_polyfill($_POST['email']);
        $password = $_POST['password']; 
        
        $newId = $db->createUser($username, $email, $password);
        $_SESSION['UserId']   = $newId;
        $_SESSION['Username'] = $username;
        // 2) immediately send to profile form
        header("Location: .?action=showProfileForm&user_id={$newId}");
        exit();

    case 'showLoginForm':
        // just render the login form
        include 'Views/User/LoginForm.php';
        exit();
    
    case 'Login':
        $username = filter_string_polyfill($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
    
        // fetch user by username
        $user = $db->getUserByUsername($username);
    
        if ($user && password_verify($password, $user['password_hash'])) {
            // success! store in session
            $_SESSION['UserId']   = $user['id'];
            $_SESSION['Username'] = $user['username'];
    
            // go to profile or dashboard
            header("Location: .?action=viewUser&user_id={$user['id']}");
            exit();
        } else {
            // failure: show form again with error
            $error = "Invalid username or password.";
            include 'Views/User/LoginForm.php';
            exit();
        }
        
    case 'Logout':
        // destroy everything:
        session_unset();
        session_destroy();
        header('Location: .?action=listArtists');
        exit();
        


    case 'showProfileForm':
        // show the profile‐completion form
        $userId = filter_input(INPUT_GET,'user_id', FILTER_VALIDATE_INT);
        if (!$userId) {
            die('Invalid user_id');
        }
        include 'views/User/ProfileForm.php';
        exit();

    case 'SaveProfile':
        // save the profile after register
        $userId = filter_input(INPUT_POST,'user_id', FILTER_VALIDATE_INT);
        $data = [
            'user_id'    => $userId,
            'first_name' => filter_input(INPUT_POST,'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'last_name'  => filter_input(INPUT_POST,'last_name',  FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'dob'        => $_POST['dob'] ?: null,
            'gender'     => $_POST['gender'] ?: null,
            'height_cm'  => $_POST['height_cm'] !== '' ? floatval($_POST['height_cm']) : null,
            'weight_kg'  => $_POST['weight_kg'] !== '' ? floatval($_POST['weight_kg']) : null,
            'goal'       => filter_input(INPUT_POST,'goal', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: null,
        ];
        $db->createProfile($data);
        header("Location: .?action=viewUser&user_id={$userId}");
        exit();

    case 'viewUser':
        // show user + profile
        $userId = filter_input(INPUT_GET,'user_id', FILTER_VALIDATE_INT)
                ?? $_SESSION['UserId'];
        $user    = $db->getUserById($userId);
        $profile = $db->getProfileByUserId($userId);
        include 'views/User/ViewUser.php';
        exit();


    /* -------------------------------------------------------------
     |  FOOD ITEMS (cached from external API)
     ------------------------------------------------------------- */
    case 'listFoodItems':
        $items = $db->displayRecords('food_items');
        include 'views/FoodItem/List.php';
        exit();

    case 'addFoodItemForm':
        include 'views/FoodItem/AddForm.php';
        exit();

    case 'addFoodItem':
        $external = filter_input(INPUT_POST,'external_food_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $db->createFoodItem($external);
        header("Location: .?action=listFoodItems");
        exit();


    /* -------------------------------------------------------------
     |  FOOD LOGS
     ------------------------------------------------------------- */
    case 'listFoodLogs':
        // optionally filter by user_id (or show all)
        $userId = filter_input(INPUT_GET,'user_id', FILTER_VALIDATE_INT);
        if ($userId) {
            $logs = $db->getFoodLogsByUser($userId);
        } else {
            $logs = $db->displayRecords('food_logs');
        }
        include 'views/FoodLog/List.php';
        exit();

    case 'addFoodLogForm':
        include 'views/FoodLog/AddForm.php';
        exit();

    case 'addFoodLog':
        $data = [
            'user_id'      => filter_input(INPUT_POST,'user_id', FILTER_VALIDATE_INT),
            'food_item_id' => filter_input(INPUT_POST,'food_item_id', FILTER_VALIDATE_INT),
            'quantity'     => floatval($_POST['quantity']),
            'unit'         => filter_input(INPUT_POST,'unit', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'eaten_at'     => $_POST['eaten_at'],
        ];
        $db->createFoodLog($data);
        header("Location: .?action=listFoodLogs&user_id={$data['user_id']}");
        exit();


    default:
        echo "Unknown action: {$action}";
        exit();
}
