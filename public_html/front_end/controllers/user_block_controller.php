<?php

class UserBlockController extends _Controller {

    public function blockUserModal($other_user_id) {
        $other_user_id = (int)$other_user_id;

        if (SecurityHelper::isLoggedIn()) {
            ModalHelper::setViews("views/user_block/block_user_modal.php");
        } else {
            ModalHelper::setViews("views/report/form_login_required.php");
        }

        include("templates/modal_layout.php");
        exit();
    }

    public function blockUser($other_user_id) {
        $other_user_id = (int)$other_user_id;

        $hide_messages = paramFromPost('hide_messages', 'n');
        if (!in_array($hide_messages, array('y', 'n'))) {
            $hide_messages = 'n';
        }

        $user = User::instanceFromId($other_user_id);
        if (!$user) {
            return json_encode(['success' => false]);
        }

        if (UserBlocked::blockUser(SESSION_USER_ID, $other_user_id, $hide_messages)) {
            $success = true;
        } else {
            $success = false;
        }

        echo json_encode(['success' => $success]);
    }

    public function unblockUser($other_user_id) {
        $other_user_id = (int)$other_user_id;

        $user = User::instanceFromId($other_user_id);
        if (!$user) {
            return;
        }

        if (UserBlocked::unblockUser(SESSION_USER_ID, $other_user_id)) {
            $success = true;
        } else {
            $success = false;
        }

        echo json_encode(['success' => $success]);
    }

}
