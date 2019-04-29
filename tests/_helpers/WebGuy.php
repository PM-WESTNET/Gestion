<?php

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class WebGuy extends \Codeception\Actor {

    use _generated\WebGuyActions;

    /**
     * @param string $username
     * @param string $password
     */
    public function login($username, $password) {
        $I = $this;

        $I->amOnPage('/index-test.php');

        if (method_exists($I, 'wait')) {
            $I->wait(1); // only for selenium
        }

        $I->fillField('input[name="LoginForm[username]"]', $username);
        $I->fillField('input[name="LoginForm[password]"]', $password);
        $I->click('Login');
        if (method_exists($I, 'wait')) {
            $I->wait(2); // only for selenium
        }
    }

    public function loginAsSuperadmin() {
        $this->login('superadmin', 'superadmin');
    }

    public function loginAsAdmin() {
        $this->login('admin', 'admin');
    }

    public function loginAsUser() {
        $this->login('user', 'user');
    }

    public function loginAsVendor() {
        $this->login('vendor', 'vendor');
    }

    /**
     * @param string $mainMenuItem Text of main menu entry
     * @param string $secondaryMenuItem Text of menu entry child of main menu entry
     * @param string $tercearyMenuItem Text of menu entry child of previous menu entry
     */
    public function clickMainMenu($mainMenuItem, $secondaryMenuItem = "", $tercearyMenuItem = "") {
        $I = $this;

        $mainAnchor = "//div[@id='wide-navbar']/ul/li/a[contains(text(), '$mainMenuItem')]";
        $I->click($mainAnchor);

        if ($secondaryMenuItem !== "") {
            $secondaryAnchor = "$mainAnchor/../ul/li/a[contains(text(), '$secondaryMenuItem')]";
            $I->click($secondaryAnchor);
        }

        if ($tercearyMenuItem !== "") {
            $tercearyAnchor = "$secondaryAnchor/../ul/li/a[contains(text(), '$tercearyMenuItem')]";
            $I->click($tercearyAnchor);
        }
        $I->wait(1);
    }

    /**
     * @param WebGuy $I WebGuy object used in codeception test
     * @param string $mainMenuItem Text of main menu entry
     * @param string $secondaryMenuItem Text of menu entry child of main menu entry
     * @param string $tercearyMenuItem Text of menu entry grand child of main menu entry
     * @param string $checkTitles string expected to be visible in title
     * @param string $inPageTag Tag of string expected to be visible in page
     */
    function checkMenuItem($mainMenuItem, $secondaryMenuItem, $tercearyMenuItem = "", $checkTitles = true, $inPageTag = 'h1') {
        $I = $this;

        $I->clickMainMenu($mainMenuItem, $secondaryMenuItem, $tercearyMenuItem);

        if ($checkTitles) {
            if ($tercearyMenuItem != '') {
                $I->seeInTitle($tercearyMenuItem);
                $I->see($tercearyMenuItem, $inPageTag);
            } else {
                $I->seeInTitle($secondaryMenuItem);
                $I->see($secondaryMenuItem, $inPageTag);
            }
        }
    }

    public function selectOptionForSelect2($select, $option) {
        $this->click("//select[@name='$select']/../span/span[@class='selection']/span/span[@class='select2-selection__arrow']");
        $this->waitForElement('.select2-search__field');
        $this->presskey('.select2-search__field', $option);
        $this->wait(1);
        $this->presskey('.select2-search__field', WebDriverKeys::ENTER);
        $this->wait(1);
    }

    public function addOptionForSelect2($select, $option) {
        $this->click("//select[@name='$select']/../span/span[@class='selection']/span[@class='select2-selection select2-selection--multiple']");
        $this->waitForElement('.select2-search__field');
        $this->presskey("//select[@name='$select']/../span/span[@class='selection']/span/ul/li/input", $option);
        $this->wait(1);
        $this->presskey("//select[@name='$select']/../span/span[@class='selection']/span/ul/li/input", WebDriverKeys::ENTER);
        $this->wait(1);
    }

    public function clearForSelect2($select) {
        $this->wait(1);
        $this->click("//select[@name='$select']/../span/span[@class='selection']/span/ul/span[@class='select2-selection__clear']");
        $this->wait(1);
    }

    public function fillColumnSearchField($select, $value) {
        $this->fillField("//input[@name='$select']", $value);
        $this->pressKey("//input[@name='$select']", WebDriverKeys::ENTER);
        $this->wait(1);
    }

    public function getStocks($product) {
        $stocks = [];

        $this->clickMainMenu("Productos", "Productos");
        if (Yii::$app->params['companies']['enabled']) {
            $this->selectOption('ProductSearch[stock_company_id]', 'ACME');
        }
        $this->wait(1); // only for selenium
        $this->fillField('#search_text', $product);
        $this->pressKey('#search_text', WebDriverKeys::ENTER);
        $this->wait(1); // only for selenium

        $stock_text = $this->grabTextFrom(".//*[@id='grid-container']/table/tbody/tr/td[4]");
        $stock_pieces = explode('|', $stock_text);
        $stocks['company_primary'] = FormatHelper::GetFloat($stock_pieces[0]);
        if (isset($stock_pieces[1])) {
            $stocks['company_secondary'] = FormatHelper::GetFloat($stock_pieces[1]);
        }
        $stock_text = $this->grabTextFrom(".//*[@id='grid-container']/table/tbody/tr/td[5]");
        $stock_pieces = explode('|', $stock_text);

        $stocks['available_primary'] = FormatHelper::GetFloat($stock_pieces[0]);
        if (isset($stock_pieces[1])) {
            $stocks['available_secondary'] = FormatHelper::GetFloat($stock_pieces[1]);
        }

        return $stocks;
    }

}
