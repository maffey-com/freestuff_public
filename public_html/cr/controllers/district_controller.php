<?
class DistrictController extends _Controller {
    public function index() {


        BreadcrumbHelper::addBreadcrumbs("Districts", APP_URL . 'distric');

        TemplateHandler::setSideMenu("Admin", "Districts");


        TemplateHandler::setMainView("views/district/index.php");
        TemplateHandler::setPageTitle("District", "List of District");
        require_once('templates/standard.php');
    }

    public function edit($district_id) {
        $district = new District();
        $district->retrieveFromId($district_id);

        TemplateHandler::setMainView("views/district/edit.php");
        TemplateHandler::setPageTitle("District", "Edit District");

        require_once('templates/standard.php');
    }

    public function add() {
        $district = new District();

        TemplateHandler::setMainView("views/district/edit.php");
        TemplateHandler::setPageTitle("District", "Add District");

        require_once('templates/standard.php');
    }

    public function save() {
        $district = new District();
        $district->buildFromPost();
		$district->save();

		if (hasErrors()) {
		    echo json_encode(getErrors());
		} else {
		    MessageHelper::setSessionSuccessMessage("Content Updated");
		    echo 1;
		}

    }

    public function delete($district_id) {
        echo District::delete($district_id);
    }

}

?>
