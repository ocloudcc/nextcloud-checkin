CREATE TABLE oc_user_checkins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(64) NOT NULL,
    checkin_date DATE NOT NULL,
    extra_space TEXT DEFAULT NULL,
    total_extra_space TEXT DEFAULT NULL
);
