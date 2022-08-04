<?php
/**
 * Class TestSettings
 *
 * @package ColorBasedProductImport
 */

use MerryCode\ColorBasedProductImport\Models\ImportSettings;

/**
 * Testing Settings
 */
class TestSettings extends WP_UnitTestCase
{
	/**
	 * On set up, class instance is set to Import Settings model
	 */
    public function setUp()
    {
        parent::setUp();
        $this->class_instance = new ImportSettings();
    }
    /**
     * Test checks if plugin settings options exist
     */
    public function test_OptionsExist()
    {
        $options = $this->class_instance->get();
        echo "Settings Options Exist";
        $this->assertTrue(!empty($options));
    }
}
