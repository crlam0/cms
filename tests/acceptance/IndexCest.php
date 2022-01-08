<?php

class IndexCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function tryToTest(AcceptanceTester $I)
    {
        $I->wantTo('Open index page');
        $I->amOnPage('/');
        $I->see('Главная');
    }
}
