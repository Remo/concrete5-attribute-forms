<?php

namespace Concrete\Package\AttributeForms\Service;

use \Concrete\Core\Foundation\Service\Provider as CoreServiceProvider;

/**
 * @property \Concrete\Core\Application\Application $app
 */
class Provider extends CoreServiceProvider
{

    public function register()
    {
        $this->app->bind('helper/excel/export', '\Concrete\Package\AttributeForms\Service\Excel\Export');
    }
}