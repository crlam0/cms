<?php

class MediaCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function tryToTest(AcceptanceTester $I)
    {
        $I->wantTo('Open media page');
        $I->amOnPage('/media/');
        $I->see('Файлы');
    }
}
