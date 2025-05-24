<?php

require_once 'dbConfig.php';


// User entity

function checkIfUserExists($pdo, $username)
{
	$response = array();
	$sql = "SELECT * FROM fiverr_users WHERE username = ?";
	$stmt = $pdo->prepare($sql);

	if ($stmt->execute([$username])) {

		$userInfoArray = $stmt->fetch();

		if ($stmt->rowCount() > 0) {
			$response = array(
				"result" => true,
				"status" => "200",
				"userInfoArray" => $userInfoArray
			);
		} else {
			$response = array(
				"result" => false,
				"status" => "400",
				"message" => "User doesn't exist from the database"
			);
		}
	}

	return $response;
}

function insertNewUser($pdo, $username, $first_name, $last_name, $password)
{
	$response = array();
	$checkIfUserExists = checkIfUserExists($pdo, $username);

	if (!$checkIfUserExists['result']) {

		$sql = "INSERT INTO fiverr_users (username, first_name, last_name, is_client, password) 
		VALUES (?,?,?,?,?)";

		$stmt = $pdo->prepare($sql);

		if ($stmt->execute([$username, $first_name, $last_name, false, $password])) {
			$response = array(
				"status" => "200",
				"message" => "User successfully inserted!"
			);
		} else {
			$response = array(
				"status" => "400",
				"message" => "An error occured with the query!"
			);
		}
	} else {
		$response = array(
			"status" => "400",
			"message" => "User already exists!"
		);
	}

	return $response;
}

function getAllUsers($pdo)
{
	$sql = "SELECT * FROM fiverr_users";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}


// Gig entity

function getAllGigs($pdo)
{
	$sql = "SELECT
				fiverr_users.username AS username, 
				gigs.gig_id AS gig_id, 
				gigs.gig_title AS title, 
				gigs.gig_description AS description, 
				gigs.date_added AS date_added 
			FROM fiverr_users
			JOIN gigs ON fiverr_users.user_id = gigs.user_id
			ORDER BY date_added DESC
			";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	return $stmt->fetchAll();
}

function getAllInterviewsByUserId($pdo, $user_id)
{
	$sql = "SELECT 
				gigs.gig_title AS title,
				gigs.gig_description AS description,
				fiverr_clients.username AS client_name,
				gig_interviews.gig_interview_id AS gig_interview_id,
				gig_interviews.time_start AS time_start,
				gig_interviews.time_end AS time_end,
				gig_interviews.status AS status
			
			FROM gig_interviews 

			JOIN gigs ON 
				gig_interviews.gig_id = gigs.gig_id 

			JOIN fiverr_users fiverr_freelancers ON 
				gig_interviews.freelancer_id = fiverr_freelancers.user_id  

			JOIN fiverr_users fiverr_clients ON 
				gigs.user_id  = fiverr_clients.user_id 

			WHERE gig_interviews.freelancer_id = ?";

	$stmt = $pdo->prepare($sql);
	$stmt->execute([$user_id]);
	return $stmt->fetchAll();
}

function updateInterviewStatus($pdo, $status, $gig_interview_id)
{
	$sql = "UPDATE gig_interviews SET status = ? WHERE gig_interview_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$status, $gig_interview_id]);
}


// Gig proposal

function getProposalByGig($pdo, $gig_id, $user_id)
{
	$sql = "SELECT * FROM gig_proposals WHERE gig_id = ? AND user_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$gig_id, $user_id]);
	return $stmt->fetch();
}

function checkIfGigProposalAlreadyExists($pdo, $gig_id, $user_id)
{
	$sql = "SELECT * FROM gig_proposals WHERE gig_id = ? AND user_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$gig_id, $user_id]);
	if ($stmt->rowCount() > 0) {
		return true;
	}
}

function insertNewGigProposal($pdo, $gig_proposal_description, $gig_id, $user_id)
{
	if (!checkIfGigProposalAlreadyExists($pdo, $gig_id, $user_id)) {
		$sql = "INSERT INTO gig_proposals (gig_proposal_description, gig_id, user_id) 
		VALUES (?,?,?)";
		$stmt = $pdo->prepare($sql);
		return $stmt->execute([$gig_proposal_description, $gig_id, $user_id]);
	}
}

function updateGigProposal($pdo, $gig_proposal_description, $gig_proposal_id)
{
	$sql = "UPDATE gig_proposals SET gig_proposal_description = ? WHERE gig_proposal_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$gig_proposal_description, $gig_proposal_id]);
}

function deleteGigProposal($pdo, $gig_id)
{
	$sql = "DELETE FROM gig_proposals WHERE gig_proposal_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$gig_id]);
}

function getNumOfPendingInterviews($pdo, $user_id)
{
	$sql = "SELECT 
				COUNT(gig_interview_id) AS pendingCount 
			FROM gig_interviews 
			WHERE status = 'Pending' AND freelancer_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$user_id]);
	return $stmt->fetch();
}

// remove 187 below -----------------------------------

function checkInterviewConflicts($pdo, $client_id, $freelancer_id, $time_start, $time_end)
{
	$sql = "SELECT * FROM gig_interviews 
            WHERE (freelancer_id = ? OR gig_id IN (SELECT gig_id FROM gigs WHERE user_id = ?))
            AND (
                (time_start <= ? AND time_end >= ?) OR  -- New interview starts during existing
                (time_start <= ? AND time_end >= ?) OR  -- New interview ends during existing
                (time_start >= ? AND time_end <= ?)     -- New interview completely within existing
            )";

	$stmt = $pdo->prepare($sql);
	$stmt->execute([
		$freelancer_id,
		$client_id,
		$time_start,
		$time_start,
		$time_end,
		$time_end,
		$time_start,
		$time_end
	]);

	return $stmt->rowCount() > 0;
}

// Add this function to freelancer/core/models.php
function scheduleInterview($pdo, $gig_id, $client_id, $freelancer_id, $time_start, $time_end)
{
	// Validate dates
	$current_time = new DateTime();
	$start_time = new DateTime($time_start);
	$end_time = new DateTime($time_end);

	// Check if dates are in the past
	if ($start_time < $current_time) {
		return ['status' => 'error', 'message' => 'Interview cannot be scheduled in the past'];
	}

	// Check if end time is after start time
	if ($end_time <= $start_time) {
		return ['status' => 'error', 'message' => 'End time must be after start time'];
	}

	// Check for conflicts
	if (checkInterviewConflicts($pdo, $client_id, $freelancer_id, $time_start, $time_end)) {
		return ['status' => 'error', 'message' => 'There is a scheduling conflict with another interview'];
	}

	// If all validations pass, insert the interview
	$sql = "INSERT INTO gig_interviews (gig_id, freelancer_id, time_start, time_end) 
            VALUES (?, ?, ?, ?)";
	$stmt = $pdo->prepare($sql);

	if ($stmt->execute([$gig_id, $freelancer_id, $time_start, $time_end])) {
		return ['status' => 'success', 'message' => 'Interview scheduled successfully'];
	} else {
		return ['status' => 'error', 'message' => 'Failed to schedule interview'];
	}
}

// Add this function to freelancer/core/models.php
function getClientIdByGigId($pdo, $gig_id)
{
	$sql = "SELECT user_id FROM gigs WHERE gig_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$gig_id]);

	if ($stmt->rowCount() > 0) {
		$result = $stmt->fetch();
		return $result['user_id']; // Returns the client's user_id who posted the gig
	}

	return false; // Return false if gig not found
}
