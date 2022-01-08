<?php

class ArticleCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function tryToTest(AcceptanceTester $I)
    {
        $I->wantTo('Open article page');
        $I->amOnPage('/article/');
        $I->see('Статьи');
    }
}
