<?php

class dbAccess
{
    /** The static class wide mysqli connection information 
	for sharing between functions */
    private static $conn;
	private static $hostName = "localhost";
	private static $databaseName = "u965274750_macro_Tracker";
	private static $userName = "u965274750_Root";
	private static $password = "QaLB/31|";



    private static function connect()
    {
         self::$conn = new mysqli(
             self::$hostName,
             self::$userName,
             self::$password,
             self::$databaseName
        );
        
        if ( self::$conn->connect_error) {
            throw new Exception("Connection failed: " .  self::$conn->connect_error);
        }
        self::$conn->query(
            "SET time_zone = '" . date('P') . "'"
        );
        
    }
	
    private static function close()
    {
        if ( self::$conn) {
             self::$conn->close();
             self::$conn = null;
        }
    }

    public static function createUser(string $username, string $email, string $password): int
    {
         self::connect();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt =  self::$conn->prepare("
            INSERT INTO users (username, email, password_hash)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sss", $username, $email, $hash);
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();
         self::close();
        return $newId;
    }

    public static function getUserByUsername(string $username): ?array
    {
     self::connect();
    $stmt =  self::$conn->prepare(
        "SELECT id, username, email, password_hash
         FROM users
         WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
     self::close();
    return $result;
    }

    public static function getUserById(int $id): ?array
    {
         self::connect();
        $stmt =  self::$conn->prepare("
            SELECT id, username, email, password_hash, created_at, updated_at
            FROM users
            WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
         self::close();
        return $res ?: null;
    }

    public static function updateUser(int $id, string $username, string $email, ?string $password = null): bool
    {
         self::connect();
        if ($password !== null) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt =  self::$conn->prepare("
                UPDATE users
                SET username = ?, email = ?, password_hash = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmt->bind_param("sssi", $username, $email, $hash, $id);
        } else {
            $stmt =  self::$conn->prepare("
                UPDATE users
                SET username = ?, email = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmt->bind_param("ssi", $username, $email, $id);
        }
        $ok = $stmt->execute();
        $stmt->close();
         self::close();
        return $ok;
    }

    public static function deleteUser(int $id): bool
    {
         self::connect();
        $stmt =  self::$conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
         self::close();
        return $ok;
    }

    /* ------------------------ USER_PROFILES ------------------------ */

    public static function createProfile(array $data): int
    {
         self::connect();
        $stmt =  self::$conn->prepare("
            INSERT INTO user_profiles
            (user_id, first_name, last_name, dob, gender, height_cm, weight_kg, goal)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "issssdds",
            $data['user_id'],
            $data['first_name'],
            $data['last_name'],
            $data['dob'],
            $data['gender'],
            $data['height_cm'],
            $data['weight_kg'],
            $data['goal']
        );
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();
         self::close();
        return $newId;
    }

    public static function getProfileByUserId(int $userId): ?array
    {
         self::connect();
        $stmt =  self::$conn->prepare("
            SELECT * FROM user_profiles WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
         self::close();
        return $res ?: null;
    }

    public static function updateProfile(int $userId, array $data): bool
    {
         self::connect();
        $stmt =  self::$conn->prepare("
            UPDATE user_profiles
            SET first_name = ?, last_name = ?, dob = ?, gender = ?, height_cm = ?, weight_kg = ?, goal = ?
            WHERE user_id = ?
        ");
        $stmt->bind_param(
            "sssssdds i",
            $data['first_name'],
            $data['last_name'],
            $data['dob'],
            $data['gender'],
            $data['height_cm'],
            $data['weight_kg'],
            $data['goal'],
            $userId
        );
        $ok = $stmt->execute();
        $stmt->close();
         self::close();
        return $ok;
    }

    public static function deleteProfile(int $userId): bool
    {
         self::connect();
        $stmt =  self::$conn->prepare("DELETE FROM user_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $ok = $stmt->execute();
        $stmt->close();
         self::close();
        return $ok;
    }


    /* ------------------------ FOOD_LOGS ------------------------ */

    public static function createFoodLog(array $data): int
    {
         self::connect();
        $stmt =  self::$conn->prepare("
            INSERT INTO food_logs
            (user_id, food_item_id, quantity, eaten_at)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iids",
            $data['user_id'],
            $data['food_item_id'],
            $data['quantity'],
            $data['eaten_at']
        );
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();
         self::close();
        return $newId;
    }

    public static function getFoodLogById(int $id): ?array
    {
         self::connect();
        $stmt =  self::$conn->prepare("SELECT * FROM food_logs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
         self::close();
        return $res ?: null;
    }

    public static function getFoodLogsByUser(int $userId): array
    {
         self::connect();
        $stmt =  self::$conn->prepare("SELECT * FROM food_logs WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
         self::close();
        return $res;
    }

    public static function updateFoodLog(int $id, array $data): bool
    {
         self::connect();
        $stmt =  self::$conn->prepare("
            UPDATE food_logs
            SET user_id = ?, food_item_id = ?, quantity = ?, unit = ?, eaten_at = ?
            WHERE id = ?
        ");
        $stmt->bind_param(
            "iids si",
            $data['user_id'],
            $data['food_item_id'],
            $data['quantity'],
            $data['unit'],
            $data['eaten_at'],
            $id
        );
        $ok = $stmt->execute();
        $stmt->close();
         self::close();
        return $ok;
    }

    public static function deleteFoodLog(int $id): bool
    {
         self::connect();
        $stmt =  self::$conn->prepare("DELETE FROM food_logs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
         self::close();
        return $ok;
    }
}

?>