<?php

namespace PaulMaxwell\BlogBundle\Tests\Twig\Extension;

use PaulMaxwell\BlogBundle\Twig\Extension\BlogExtension;

class BlogExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testTagWeightPercentFilter()
    {
        $extension = new BlogExtension(null);

        $this->assertEquals(200, $extension->tagWeightPercentFilter(20,20));
        $this->assertEquals(50, $extension->tagWeightPercentFilter(0,20));
    }
}
