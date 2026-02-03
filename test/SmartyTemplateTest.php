<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class VolumesApiTest extends TestCase
{
    /**
     * Test to check one template e.g. oclchtml.tpl can be compiled and rendered
     * Test that oclcscrape route can render Smarty template with Smarty 4.x
     * @runInSeparateProcess
     */
    public function test_oclcscrape_smarty_template_rendering(): void
    {
        // Setup environment
        $_SERVER['HTTP_HOST'] = 'localhost';

        // Verify Smarty 4.x class is available (no namespace)
        $this->assertTrue(class_exists('Smarty'), 'Smarty class should be available without namespace');

        // Create Smarty instance using Smarty 4.x syntax
        $interface = new Smarty();
        $interface->setCompileDir(__DIR__ . '/../interface/compile');
        $interface->setTemplateDir(__DIR__ . '/../static/api/templates');

        // Mock data structure matching volumes.php
        $mockDoc = [
            'titles' => ['Test Book Title'],
            'oclcs' => ['12345678']
        ];
        $mockData = [
            'records' => ['test-id' => $mockDoc],
            'items' => [
                ['itemURL' => 'https://localhost:8080/item', 'enumcron' => 'v.1', 'usRightsString' => 'Full view', 'orig' => 'Test Library']
            ]
        ];

        $interface->assign('data', $mockData);
        $interface->assign('doc', $mockDoc);

        // Fetch template (returns string without outputting)
        $output = $interface->fetch('volumes/oclchtml.tpl');

        // Verify template rendered correctly
        $this->assertStringContainsString('Test Book Title', $output);
        $this->assertStringContainsString('12345678', $output);
        $this->assertStringContainsString('https://localhost:8080/item', $output);
    }
}
?>