<?php
namespace Concrete\Package\AttributeForms\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\PageController;

class Forms extends PageController
{
    public function on_start()
    {
        $this->redirect($this->action('types'));
    }
}