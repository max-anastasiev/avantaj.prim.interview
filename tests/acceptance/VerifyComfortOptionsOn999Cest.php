<?php

class VerifyComfortOptionsOn999Cest
{
    public $countPage = 2;
    public $langToTest = 'ro';
    public $langs = ['ru' => ['search' => 'Комфорт','expected' => ['Люк', 'Кондиционер', 'Усилитель руля']],
        'ro' =>['search' => 'Comfort','expected' => ['Trapă', 'Aer condiționat', 'Servodirecție']]];
    public const URLWITHFILTERS = "/%s/list/transport/cars?applied=1&show_all_checked_childrens=no&sort_type=date_desc&ef=1&ef=260&ef=6&r_6_2_from=&r_6_2_to=&r_6_2_unit=eur&r_6_2_negotiable=yes&ef=4112&ef=2029&ef=1279&ef=5&ef=1077&f_1077=130&f_1077=132&f_1077=135&page=";
    public const CARLINKS = '//div[@class="items__list "]//a[contains(@href,"/%s/") and not(contains(@href,"/transport/"))]';
    public const COMFORTOPTIONS = '//h2[contains(text(),"%s")]//following:: span[@class=" adPage__content__features__key  "]' ;

    public function Test(AcceptanceTester $I)
    {
        $I->amOnPage('/ru/list/transport/cars');
        $I->see("Легковые автомобили");
        $urlList = [];
        for ($i = 1; $i <= $this->countPage; $i++) {
            $I->amOnPage( sprintf(self::URLWITHFILTERS. $i, $this->langToTest) );
            $urlList = $I->grabMultiple(sprintf(self::CARLINKS, $this->langToTest), 'href');
        }

        $urlLister = [];
        foreach ($urlList as $url) {
            $I->amOnPage($url);
            $features = $I->grabMultiple(sprintf(self::COMFORTOPTIONS, $this->langs[$this->langToTest]["search"]));

            $result = [];
            foreach ($this->langs[$this->langToTest]["expected"] as $item) {
                if (array_search($item, $features) != false){
                    $result = $item;
                }
            }
            assert($result == $this->langs[$this->langToTest]["expected"]);
            array_push($features, 'https://999.md' . $url);
            $urlLister[$url] = $features;
        }

        $fp = fopen('result.csv', 'w');
        foreach ($urlLister as $fields) {
            fputcsv($fp, $fields);
        }
    }
}
