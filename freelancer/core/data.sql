CREATE TABLE fiverr_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR (255),
    password TEXT,
    first_name VARCHAR (255),
    last_name VARCHAR (255),
    is_client BOOL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gigs (
    gig_id INT AUTO_INCREMENT PRIMARY KEY,
    gig_title VARCHAR(255),
    gig_description TEXT,
    user_id INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gig_proposals (
    gig_proposal_id INT AUTO_INCREMENT PRIMARY KEY,
    gig_proposal_description TEXT,
    gig_id INT,
    user_id INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE gig_interviews (
    gig_interview_id INT AUTO_INCREMENT PRIMARY KEY,
    gig_id INT,
    freelancer_id INT,
    time_start TIMESTAMP, 
    time_end TIMESTAMP NULL, 
    status VARCHAR(20) DEFAULT 'Pending', 
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);




