CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    team_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS password_resets (
    user_id INT PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bingo_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    icon VARCHAR(100) DEFAULT 'check',
    points INT DEFAULT 10,
    active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS bingo_game_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS bingo_game_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_period_id INT NOT NULL,
    task_id INT NOT NULL,
    FOREIGN KEY (game_period_id) REFERENCES bingo_game_periods(id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES bingo_tasks(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bingo_user_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_task_id INT NOT NULL,
    completed TINYINT(1) DEFAULT 0,
    counter INT DEFAULT 0,
    UNIQUE KEY unique_user_task (user_id, game_task_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (game_task_id) REFERENCES bingo_game_tasks(id) ON DELETE CASCADE
);

INSERT INTO teams (name) VALUES ('Team Alpha'), ('Team Beta'), ('Team Gamma');

INSERT INTO bingo_tasks (title, description, icon, points, active) VALUES
('Coffee Break', 'Take a 15-minute coffee break with a colleague', '☕', 10, 1),
('Walk & Talk', 'Hold a 1-on-1 meeting while walking outdoors', '🚶', 20, 1),
('Inbox Zero', 'Clear your email inbox before Friday', '📧', 15, 1),
('Kudos Given', 'Give public praise to a teammate', '👏', 10, 1),
('Hydration Hero', 'Drink 2 liters of water during the workday', '💧', 10, 1),
('Desk Stretch', 'Do 5 minutes of stretching at your desk', '🧘', 10, 1),
('Lunch Away', 'Eat lunch away from your computer screen', '🥗', 15, 1),
('Help a Peer', 'Assist a colleague with a tricky problem', '🤝', 20, 1),
('Clean Desk', 'Tidy up your workspace before logging off', '✨', 10, 1),
('No-Meeting Block', 'Protect 2 hours of deep focus work', '🎧', 15, 1),
('New Face', 'Chat with someone from another team', '👋', 15, 1),
('Early Logoff', 'Finish on time and enjoy your evening', '🏁', 10, 1);
