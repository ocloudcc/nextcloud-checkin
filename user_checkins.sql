CREATE TABLE oc_user_checkins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(64) NOT NULL,
    checkin_count INT DEFAULT 0
);
