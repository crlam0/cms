<?php

class CatalogCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function tryToTest(AcceptanceTester $I)
    {
        $I->wantTo('Open catalog page');
        $I->amOnPage('/catalog/');
        $I->see('Каталог');
    }
}
