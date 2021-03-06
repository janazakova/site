<?php

require 'include/start.php';

$message = '';
if (!empty($_GET['id'])) {
    $personId = $_GET['id'];
} else {
    exit("Parameter 'id' is missing.");
}

if (!empty($_POST['save'])) {
    // user clicked on the save button
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['nickname'])) {
        $message = 'Please fill in both names and nickname';
    } elseif (empty($_POST['gender']) || ($_POST['gender'] != 'male' && $_POST['gender'] != 'female')) {
        $message = 'Gender must be either "male" or "female"';
    } else {
        // everything filled
        try {
            $stmt = $db->prepare(
                "UPDATE person SET first_name = :first_name, last_name = :last_name, 
				nickname = :nickname, birth_day = :birth_day, gender = :gender, height = :height
				WHERE id_person = :id_person"
            );
            $stmt->bindValue(':id_person', $personId);
            $stmt->bindValue(':first_name', $_POST['first_name']);
            $stmt->bindValue(':last_name', $_POST['last_name']);
            $stmt->bindValue(':nickname', $_POST['nickname']);
            $stmt->bindValue(':gender', $_POST['gender']);

            if (empty($_POST['birth_day'])) {
                $stmt->bindValue(':birth_day', null);
            } else {
                $stmt->bindValue(':birth_day', $_POST['birth_day']);
            }

            if (empty($_POST['height']) || empty(intval($_POST['height']))) {
                $stmt->bindValue(':height', null);
            } else {
                $stmt->bindValue(':height', intval($_POST['height']));
            }
            $stmt->execute();
            $message = "Person updated";
        } catch (PDOException $e) {
            $message = "Failed to update person (" . $e->getMessage() . ")";
        }
    }
}

try {
    $stmt = $db->prepare("SELECT * FROM person WHERE id_person = :id_person");
    $stmt->bindValue(':id_person', $personId);
    $stmt->execute();
    $tplVars['person'] = $stmt->fetch();
    if (!$tplVars['person']) {
        exit("Cannot find person with ID: $personId");
    }
} catch (PDOException $e) {
    exit("Cannot get person " . $e->getMessage());
}

$tplVars['operation'] = 'Update Person';
$tplVars['message'] = $message;
$latte->render('templates/person-update-4.latte', $tplVars);
