<?php
/**
 * Created by PhpStorm.
 * User: maggie
 * Date: 30/08/2016
 * Time: 12:29 PM
 */
class EmailTemplateController extends _Controller
{
    public function index() {
        $emails = EmailTemplate::$available_emails;

        $sql = "SELECT * 
                FROM email_templates 
                ORDER BY name";
        $result = runQuery($sql);
        while ($row = fetchSQL($result)) {
            if (array_key_exists($row["name"], $emails)) {
                $emails[$row["name"]]["email_template_id"] = (int)$row["email_template_id"];
                $emails[$row["name"]]["subject"] = $row["subject"];
                $emails[$row["name"]]["count"] = $row["count"];
                $emails[$row["name"]]["to_address"] = $row["to_name"] . "<br />" . $row["to_address"];
                $emails[$row["name"]]["from_address"] = $row["from_name"] . "<br />" . $row["from_address"];
                $emails[$row["name"]]["reply_to_address"] = $row["reply_to_name"] . "<br />" . $row["reply_to_address"];
                $emails[$row["name"]]["bcc_list"] = $row["bcc"];
            }
        }
        ksort($emails);

        BreadcrumbHelper::addBreadcrumbs("Email templates", APP_URL . 'email_template');

        TemplateHandler::setSideMenu("Admin", "Email templates");
        TemplateHandler::setPageTitle("Email templates");
        TemplateHandler::setMainView("views/email_template/list.php");

        include("templates/standard.php");
    }

    public function edit($email_template_id) {
        BreadcrumbHelper::addBreadcrumbs("Email templates", APP_URL . 'email_template');
        BreadcrumbHelper::addBreadcrumbs("Edit");

        TemplateHandler::setSideMenu("Admin", "Email templates");
        TemplateHandler::setPageTitle("Email templates", 'Edit email template');
        TemplateHandler::setMainView("views/email_template/edit.php");

        $et = EmailTemplate::instanceFromId($email_template_id);

        include("templates/standard.php");
    }

    public function add($template_name) {
        $template = new EmailTemplate();

        if ($template->insert($template_name)) {
            MessageHelper::setSessionSuccessMessage('Template has been created');
            redirect(APP_URL . 'email_template/edit/' . $template->email_template_id);

        } else {
            MessageHelper::setSessionErrorMessage('Fail to insert template');
            $this->index();
        }
    }

    public function update() {
        $template = new EmailTemplate();
        $template->buildFromPost();

        if ($template->update()) {
            MessageHelper::setSessionSuccessMessage('Template has been updated');
            echo '1';
            die();
        }

        echo json_encode(getErrors());
        die();
    }
}