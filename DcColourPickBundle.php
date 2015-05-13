<?php

namespace DanielClements\ColourPickerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use DanielClements\ColourPickerBundle\DependencyInjection\DcColourPickExtension;

class DcColourPickBundle extends Bundle
{
    protected $name = 'DcColourPickBundle';

    public function getContainerExtension()
    {
        return new DcColourPickExtension();
    }
}
