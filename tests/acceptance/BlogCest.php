<?php

class BlogCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function tryToTest(AcceptanceTester $I)
    {
        $I->wantTo('Open blog page');
        $I->amOnPage('/blog/');
        $I->see('Блог');
    }
}
