<?php
namespace Gurucomkz\Watermark\Tests;

use Gurucomkz\Watermark\ImageExtension;
use Gurucomkz\Watermark\SiteConfigExtension;
use SilverStripe\Assets\Dev\TestAssetStore;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Assets\Image;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Parsers\ShortcodeParser;

class WatermarkTest extends FunctionalTest
{

    protected $usesDatabase = true;

    protected function setUp(): void
    {
        parent::setUp();

        TestAssetStore::activate('ClientsTest');
    }

    public function configWatermark()
    {
        /** @var Image|ImageExtension */
        $wmImage = Image::create();
        $wmImage->setFromLocalFile('tests/fixtures/watermark.png');
        $wmImage->write();

        /** @var SiteConfig|SiteConfigExtension */
        $siteConfig = SiteConfig::current_site_config();
        $siteConfig->WatermarkMaxWidth = 100;
        $siteConfig->WatermarkMaxHeight = 100;
        $siteConfig->WatermarkImage = $wmImage;
        $siteConfig->write();
    }

    public function testWatermark()
    {
        $this->configWatermark();

        /** @var Image|ImageExtension */
        $image = Image::create();
        $image->setFromLocalFile('tests/fixtures/somepic.jpg');
        $image->write();

        $wm = $image->Watermark();

        $this->assertNotNull($wm);

        $link = $wm->Link();
        $this->assertStringContainsString('somepic__Watermark', $link);
    }

    public function testNotConfigured()
    {
        /** @var Image|ImageExtension */
        $image = Image::create();
        $image->setFromLocalFile('tests/fixtures/somepic.jpg');
        $image->write();

        $wm = $image->Watermark();

        $this->assertNotNull($wm);

        $link = $wm->Link();
        $this->assertStringNotContainsString('somepic__Watermark', $link);
    }

    public function testNoContent()
    {
        /** @var Image|ImageExtension */
        $image = Image::create();
        $image->write();

        $wm = $image->Watermark();

        $this->assertNotNull($wm);

        $link = $wm->Link();
        $this->assertNull($link);
    }

    public function testShortcode()
    {
        $this->configWatermark();

        /** @var Image|ImageExtension */
        $image = Image::create();
        $image->setFromLocalFile('tests/fixtures/somepic.jpg');
        $image->write();

        $text = 'some text [watermark file=somepic.jpg] and something else';

        $parsed = ShortcodeParser::get('default')->parse($text);

        
        $this->assertStringContainsString('some text <img ', $parsed);
        $this->assertStringContainsString('somepic__Watermar', $parsed);
    }

    public function testShortcodeLeft()
    {
        $this->configWatermark();

        /** @var Image|ImageExtension */
        $image = Image::create();
        $image->setFromLocalFile('tests/fixtures/somepic.jpg');
        $image->write();

        $text = 'some text [watermark file=somepic.jpg position=TopLeft] and something else';

        $parsed = ShortcodeParser::get('default')->parse($text);

        
        $this->assertStringContainsString('some text <img ', $parsed);
        $this->assertStringContainsString('somepic__Watermar', $parsed);
    }

    public function testShortcodeNoFileParam()
    {
        $this->configWatermark();

        $text = 'some text [watermark ] and something else';

        $parsed = ShortcodeParser::get('default')->parse($text);
        
        $this->assertEquals('some text  and something else', $parsed);
    }

    public function testShortcodeEmpty()
    {
        $this->configWatermark();

        $text = 'some text [watermark file=somepic.jpg] and something else';

        $parsed = ShortcodeParser::get('default')->parse($text);
        
        $this->assertEquals('some text  and something else', $parsed);
    }
}
