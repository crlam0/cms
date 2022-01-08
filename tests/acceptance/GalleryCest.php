<?php

class GalleryCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function tryToTest(AcceptanceTester $I)
    {
        $I->wantTo('Open gallery page');
        $I->amOnPage('/gallery/');
        $I->see('Галерея');
    }
}
