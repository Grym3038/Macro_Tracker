-- 1) Users & Login
CREATE TABLE users (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  username         VARCHAR(50)  NOT NULL UNIQUE,
  email            VARCHAR(100) NOT NULL UNIQUE,
  password_hash    VARCHAR(255) NOT NULL,
  created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME     NOT NULL
                      DEFAULT CURRENT_TIMESTAMP
                      ON UPDATE CURRENT_TIMESTAMP
);

-- 2) User Profile / Account Info
CREATE TABLE user_profiles (
  user_id    INT          PRIMARY KEY,
  first_name VARCHAR(50),
  last_name  VARCHAR(50),
  dob        DATE,
  gender     ENUM('male','female','other'),
  height_cm  INT,           -- store height in cm
  weight_kg  DECIMAL(5,2),  -- store weight in kg
  goal       TEXT,          -- e.g. "lose 5 lbs"
  FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
);

-- 3) Food items cache (optional)
--    If you want to dedupe repeated lookups of the same API ID:
CREATE TABLE food_items (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  external_food_id    VARCHAR(100) NOT NULL UNIQUE,
  first_seen_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 4) Food log: what users ate & when
CREATE TABLE food_logs (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  user_id          INT         NOT NULL,
  food_item_id     INT         NOT NULL,      -- FK to food_items.id
  quantity         DECIMAL(6,2) NOT NULL DEFAULT 1,
  unit             VARCHAR(50)  NOT NULL DEFAULT 'serving',
  eaten_at         DATETIME     NOT NULL,     -- when they ate it
  logged_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)      REFERENCES users(id)
    ON DELETE CASCADE,
  FOREIGN KEY (food_item_id) REFERENCES food_items(id)
    ON DELETE CASCADE,
  INDEX idx_user_eaten_at (user_id, eaten_at)
);
