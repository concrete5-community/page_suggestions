<?php 
namespace Concrete\Package\PageSuggestions;

use BlockType;
use Package;

class Controller extends Package
{
    protected $pkgHandle = 'page_suggestions';
    protected $appVersionRequired = '5.7.4';
    protected $pkgVersion = '1.0';

    public function getPackageDescription()
    {
        return t("Show a list of page suggestions based on the current URL.");
    }

    public function getPackageName()
    {
        return t("Page Suggestions");
    }

    public function install()
    {
        $pkg = parent::install();

        BlockType::installBlockType('page_suggestions', $pkg);
    }
}