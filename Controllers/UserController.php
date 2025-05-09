<?php




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
        header('Location: .?action=home');
        exit();
        


    case 'showProfileForm':
        // show the profile‐completion form
        $userId = filter_input(INPUT_GET,'user_id', FILTER_VALIDATE_INT);
        if (!$userId) {
            die('Invalid user_id');
        }
        include 'Views/User/ProfileForm.php';
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
    $loggedIn = ! empty($_SESSION['UserId']);
    $userId   = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT)
              ?? $_SESSION['UserId'];

    if ($loggedIn != $userId) {
        header("Location: .?action=home");
        exit();
    }

    // fetch user, profile, and logs
    $user        = $db->getUserById($userId);
    $profile     = $db->getProfileByUserId($userId);
    $userFoodLog = $db->getFoodLogsByUser($userId);
    date_default_timezone_set('America/Chicago');

    $todayDate = (new DateTime('now', new DateTimeZone('America/Chicago')))
                    ->format('Y-m-d');
   $logsToday = array_filter($userFoodLog, function(array $log) use ($todayDate) {
    // treat eaten_at as local time
    $dt = new DateTime($log['eaten_at']); 
    // no need to convert
    return $dt->format('Y-m-d') === $todayDate;
    });
    $logsToday = array_values($logsToday);
    $ids = array_column($logsToday, 'food_item_id');

    // ── NEW: preload all nutrient arrays by food_item_id ──
    $nutrientsByFood = [];
    if (empty($logsToday)) {
        $userFood = [];
    } else {
        $userFood = $api->getFoods(array_column($logsToday, 'food_item_id'));

        }
    
    
    $allIds = array_unique(array_column($userFoodLog, 'food_item_id'));
    foreach ($allIds as $fid) {
        $info = $api->getFood($fid);
        $nutrientsByFood[$fid] = $info['foodNutrients'] ?? [];
    }

    // set correct timezone for weekday names
    date_default_timezone_set('America/Chicago');

    // ── now group and accumulate ──
    $dailyTotals = [];
    foreach ($userFoodLog as $log) {
        // parse timestamp in UTC and convert to Chicago time
        $dt = new DateTime($log['eaten_at'], new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('America/Chicago'));
        $day = $dt->format('l'); 

        if (! isset($dailyTotals[$day])) {
            $dailyTotals[$day] = [];
        }

        $qty      = $log['quantity'];
        $nutrList = $nutrientsByFood[$log['food_item_id']];

        foreach ($nutrList as $nutr) {
            $name   = $nutr['nutrient']['name'] ?? $nutr['name'];
            $amt    = ($nutr['amount'] ?? $nutr['nutrient']['amount']) * $qty;
            $dailyTotals[$day][$name] = 
                ($dailyTotals[$day][$name] ?? 0)
                + $amt;
        }
    }
    
    $logsWithFood = [];
    foreach ($logsToday as $i => $log) {
        $logsWithFood[] = [
          'logID'       => $log['id'],
          'quantity'    => $log['quantity'],
          'eaten_at'    => $log['eaten_at'],
          'food'        => $userFood[$i] ?? null,  
        ];
    }


    include 'Views/User/ViewUser.php';
    exit();







}
