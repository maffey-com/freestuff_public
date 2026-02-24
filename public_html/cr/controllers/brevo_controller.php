<?php

class BrevoController extends _Controller {

    public function index() {

        BreadcrumbHelper::addBreadcrumbs("Users", APP_URL . 'user');

        TemplateHandler::setSideMenu("Admin", "Users");
        TemplateHandler::setPageTitle("Users");
        TemplateHandler::setMainView("views/user/list.php");

        include("templates/standard.php");
    }

    public function send1() {
        $sql = "select * from user where email like '%@maffey.com'";
        $users = runQueryGetAll($sql);

        foreach ($users as $user) {
            Brevo::sendUserToBrevo($user["user_id"]);
        }

    }

    public function send2() {
        $sql = "select user_id from user where user_listing_count > 4
and email_validated is not null
and brevo_id is null";
        $users = runQueryGetAll($sql);

        foreach ($users as $user) {
            Brevo::sendUserToBrevo($user["user_id"]);
        }

    }

    public function brevo1() {
        Brevo::sendUserToBrevo(69);
    }

    public function update1() {
        Brevo::updateUser(17);
    }


    public function runUpdateNeedingUpdate() {
        // Get the initial count of users needing update
        $count = $this->getUsersNeedingUpdateCount();

        // Loop until the count hits zero
        while ($count > 0) {
            // Call the updateNeedingUpdate function
            $this->updateNeedingUpdate();

            // Get the updated count of users needing update
            $count = $this->getUsersNeedingUpdateCount();

            // Print the remaining count to the console
            echo "Remaining users needing update: $count\n";
        }

        echo "Update completed.\n";
    }


    public function tTest() {
        Brevo::tTest();
    }
}

