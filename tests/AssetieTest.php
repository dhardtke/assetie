<?php namespace dhardtke\Assetie;

require_once 'fixtures/App.php';

/**
 * @todo implement tests
 * some code borrowed from https://github.com/CodeSleeve/asset-pipeline/tree/master/tests
*/

class AssetieTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $base = __DIR__ . '/fixtures';

        $config = include __DIR__ . '/../src/config/config.php';
        $config['base_path'] = $base;
        $config['environment'] = "local";
        $config['javascript_include_tag'] = $this->getMock('Codesleeve\AssetPipeline\Composers\JavascriptComposer');
        $config['stylesheet_link_tag'] = $this->getMock('Codesleeve\AssetPipeline\Composers\StylesheetComposer');

        $this->base = $base;
        $this->config = $config;
    }

    public function testJavascriptIncludeTag()
    {
        $this->config['javascript_include_tag']->expects($this->once())->method('process');
        $this->pipeline->javascriptIncludeTag('application', array());
    }

    public function testStylesheetLinkTag()
    {
        $this->config['stylesheet_link_tag']->expects($this->once())->method('process');
        $this->pipeline->stylesheetLinkTag('application', array());
    }

    public function testIsJavascript()
    {
        $this->assertNotNull($this->pipeline->isJavascript('application.js'));
        $this->assertNull($this->pipeline->isJavascript('some.swf'));
        $this->assertNull($this->pipeline->isJavascript('application.css'));
    }

    public function testJavascript()
    {
        $output = $this->pipeline->javascript("{$this->base}/app/assets/javascripts/application.js");
        $this->assertNotEmpty($output);
    }

    public function testRegisterAssetPipelineFilters()
    {
        $output = $this->pipeline->registerAssetPipelineFilters();
        $this->assertEquals($output, $this->pipeline);
    }
}