<?php
session_start();
include('../includes/connection.php');

if (isset($_POST['assign'])) {
    $team_leader_id = $_POST['team_leader_id'];
    $team_member_ids = $_POST['team_member_ids'];

    foreach ($team_member_ids as $member_id) {
        $update_query = "UPDATE tbl_employee SET team_leader_id = ? WHERE id = ?";
        $stmt = $connection->prepare($update_query);
        $stmt->bind_param("ii", $team_leader_id, $member_id);
        $stmt->execute();
    }

    header("Location: assign_team.php?msg=success");
    exit();
}
?>
