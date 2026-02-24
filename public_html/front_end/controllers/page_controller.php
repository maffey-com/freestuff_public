<?
class PageController extends _Controller {
    public function __construct()
    {
        PageHelper::setMinifyPageCssName('page');
    }

    public function terms() {
        BreadcrumbHelper::addBreadcrumbs('Terms And Conditions');

        PageHelper::setViews("views/page/terms.php");

        include("templates/main_layout.php");
    }

    public function privacy() {
        BreadcrumbHelper::addBreadcrumbs('Privacy Statement');

        PageHelper::setViews("views/page/privacy.php");

        include("templates/main_layout.php");
    }

    public function faq() {
        BreadcrumbHelper::addBreadcrumbs('Frequently Asked Questions');

        PageHelper::setMinifyPageCssName('faq');
        PageHelper::setViews("views/page/faq.php");

        include("templates/main_layout.php");
    }

    public function howitworks() {
        PageHelper::setMinifyPageCssName('how_it_works.css');
        PageHelper::addPageStylesheetFile('css/how_it_works.css', 'all', array('../img/' => '../../img/'));

        PageHelper::setViews("views/page/how_it_works_banner.php", "views/page/how_it_works.php");

        include("templates/main_layout.php");
    }

    public function feedback() {
        BreadcrumbHelper::addBreadcrumbs('Feedback');

        PageHelper::setViews("views/page/feedback.php");

        include("templates/main_layout.php");
    }

    public function requestCredit() {
        if (SecurityHelper::isLoggedIn()) {
            $user = new User();
            $user->retrieveFromId(SESSION_USER_ID);
        }

        BreadcrumbHelper::addBreadcrumbs('Request Credits');

        PageHelper::setViews("views/page/request_credit.php");

        include("templates/main_layout.php");
    }

    public function blockedUsers() {
        if (SecurityHelper::isLoggedIn()) {
            $blocked = UserBlocked::getBlockedForUser(SESSION_USER_ID);
            $blocked_users = [];
            foreach ($blocked as $other_user_id => $hide_messages) {
                $other_user = User::instanceFromId($other_user_id);
                $blocked_users[] = $other_user;
            }
            if (empty($blocked_users)) {
                redirect(APP_URL . 'my_freestuff');
            }
        }

        BreadcrumbHelper::addBreadcrumbs('Blocked Users');

        PageHelper::setViews("views/page/blocked_users.php");

        include("templates/main_layout.php");
    }
}

