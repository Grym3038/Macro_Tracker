<?php

class dbAccess
{
    /** The static class wide mysqli connection information 
	for sharing between functions */
    private static $conn;
	private static $hostName = "195.35.61.14";
	private static $databaseName = "u965274750_macro_Tracker";
	private static $userName = "u965274750_Root";
	private static $password = "QaLB/31|";




    private function connect()
    {
        self::$conn = new mysqli(
            self::$hostName,
            self::$userName,
            self::$password,
            self::$databaseName
        );
        if (self::$conn->connect_error) {
            throw new Exception("Connection failed: " . self::$conn->connect_error);
        }
    }
	
    private function close()
    {
        if (self::$conn) {
            self::$conn->close();
            self::$conn = null;
        }
    }

    public function createUser(string $username, string $email, string $password): int
    {
        $this->connect();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = self::$conn->prepare("
            INSERT INTO users (username, email, password_hash)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sss", $username, $email, $hash);
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();
        $this->close();
        return $newId;
    }

    public function getUserByUsername(string $username): ?array
    {
    $this->connect();
    $stmt = self::$conn->prepare(
        "SELECT id, username, email, password_hash
         FROM users
         WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    $this->close();
    return $result;
    }

    public function getUserById(int $id): ?array
    {
        $this->connect();
        $stmt = self::$conn->prepare("
            SELECT id, username, email, password_hash, created_at, updated_at
            FROM users
            WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $this->close();
        return $res ?: null;
    }

    public function updateUser(int $id, string $username, string $email, ?string $password = null): bool
    {
        $this->connect();
        if ($password !== null) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = self::$conn->prepare("
                UPDATE users
                SET username = ?, email = ?, password_hash = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmt->bind_param("sssi", $username, $email, $hash, $id);
        } else {
            $stmt = self::$conn->prepare("
                UPDATE users
                SET username = ?, email = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmt->bind_param("ssi", $username, $email, $id);
        }
        $ok = $stmt->execute();
        $stmt->close();
        $this->close();
        return $ok;
    }

    public function deleteUser(int $id): bool
    {
        $this->connect();
        $stmt = self::$conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        $this->close();
        return $ok;
    }

    /* ------------------------ USER_PROFILES ------------------------ */

    public function createProfile(array $data): int
    {
        $this->connect();
        $stmt = self::$conn->prepare("
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
        $this->close();
        return $newId;
    }

    public function getProfileByUserId(int $userId): ?array
    {
        $this->connect();
        $stmt = self::$conn->prepare("
            SELECT * FROM user_profiles WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $this->close();
        return $res ?: null;
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $this->connect();
        $stmt = self::$conn->prepare("
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
        $this->close();
        return $ok;
    }

    public function deleteProfile(int $userId): bool
    {
        $this->connect();
        $stmt = self::$conn->prepare("DELETE FROM user_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $ok = $stmt->execute();
        $stmt->close();
        $this->close();
        return $ok;
    }

    /* ------------------------ FOOD_ITEMS ------------------------ */

    public function createFoodItem(string $externalFoodId): int
    {
        $this->connect();
        $stmt = self::$conn->prepare("
            INSERT INTO food_items (external_food_id)
            VALUES (?)
        ");
        $stmt->bind_param("s", $externalFoodId);
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();
        $this->close();
        return $newId;
    }

    public function getFoodItemById(int $id): ?array
    {
        $this->connect();
        $stmt = self::$conn->prepare("SELECT * FROM food_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $this->close();
        return $res ?: null;
    }

    public function updateFoodItem(int $id, string $externalFoodId): bool
    {
        $this->connect();
        $stmt = self::$conn->prepare("
            UPDATE food_items
            SET external_food_id = ?
            WHERE id = ?
        ");
        $stmt->bind_param("si", $externalFoodId, $id);
        $ok = $stmt->execute();
        $stmt->close();
        $this->close();
        return $ok;
    }

    public function deleteFoodItem(int $id): bool
    {
        $this->connect();
        $stmt = self::$conn->prepare("DELETE FROM food_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        $this->close();
        return $ok;
    }

    /* ------------------------ FOOD_LOGS ------------------------ */

    public function createFoodLog(array $data): int
    {
        $this->connect();
        $stmt = self::$conn->prepare("
            INSERT INTO food_logs
            (user_id, food_item_id, quantity, unit, eaten_at)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iids s",
            $data['user_id'],
            $data['food_item_id'],
            $data['quantity'],
            $data['unit'],
            $data['eaten_at']
        );
        $stmt->execute();
        $newId = $stmt->insert_id;
        $stmt->close();
        $this->close();
        return $newId;
    }

    public function getFoodLogById(int $id): ?array
    {
        $this->connect();
        $stmt = self::$conn->prepare("SELECT * FROM food_logs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $this->close();
        return $res ?: null;
    }

    public function getFoodLogsByUser(int $userId): array
    {
        $this->connect();
        $stmt = self::$conn->prepare("SELECT * FROM food_logs WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $this->close();
        return $res;
    }

    public function updateFoodLog(int $id, array $data): bool
    {
        $this->connect();
        $stmt = self::$conn->prepare("
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
        $this->close();
        return $ok;
    }

    public function deleteFoodLog(int $id): bool
    {
        $this->connect();
        $stmt = self::$conn->prepare("DELETE FROM food_logs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        $this->close();
        return $ok;
    }
}

?>