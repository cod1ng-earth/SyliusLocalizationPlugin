<?php

namespace CodingBerlin\LocalizationPlugin\Command;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneMember;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Intl\Intl;

class SyliusLocalizationCommand extends ContainerAwareCommand {

    public function configure()
    {
        $this->setName("sylius:localization");
        $this->addArgument("countryCode", InputArgument::REQUIRED);
        $this->addUsage("test");
    }

    private $localeConfig = [
        'DE' => [
            'country' => 'DE',
            'currency' => 'EUR',
            'locale' => 'de_DE',
            'zone' => [
                'code' => 'DE',
                'name' => 'Deutschland',
                'scope' => 'all', //shipping & taxes
            ],
            'taxes' => [
                'books' => 7.0,
                'food_general' => 7.0,
                'default' => 19.0
            ]
        ]
    ];

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $countryCode = $input->getArgument('countryCode');
        $config = $this->localeConfig[$countryCode];

        $zone = $this->addZone($config['zone']);
        $locale = $this->addLocale($config['locale']);
        $currency = $this->addCurrency($config['currency']);
        $country = $this->addCountry($config['country']);

        $localeRepo = $this->getContainer()->get('sylius.repository.locale');
        $locales = $localeRepo->findAll();

        foreach($locales as $locale) {
            /** @var $locale Locale */
            $output->writeln($locale->getName());
        }
    }

    protected function addZone($config) {
        $zone = $this->getContainer()->get('sylius.factory.zone')->createTyped('country');
        
        $zone->setCode($config['code']);
        $zone->setName($config['name']);
        $zone->setScope($config['scope']);
        
        /** @var ZoneMember $zoneMember */
        $zoneMember = $this->getContainer()->get('sylius.factory.zone_member')->createNew();
        $zoneMember->setCode($config['code']);
        
        $zone->addMember($zoneMember);

        $this->getContainer()->get('sylius.repository.zone')->add($zone);

        return $zone;
    }

    protected function addLocale($localeCode) {
        $availLocales = Intl::getLocaleBundle()->getLocaleNames();
        /**
         * @var $locale Locale
         */
        $locale = $this->getContainer()->get('sylius.factory.locale')->createNew();
        $locale->setCode($localeCode);

        $this->getContainer()->get('sylius.repository.locale')->add($locale);
        return $locale;
    }

    /**
     * @param $countryCode
     * @return CountryInterface
     */
    protected function addCountry($countryCode) {

        //todo: check that the country doesn't exist and the code is valid
        //$countries = $countryManager->findAll();
        //$availCountries = Intl::getRegionBundle()->getCountryNames();

        /** @var CountryInterface $country */
        $countryFactory = $this->getContainer()->get('sylius.factory.country');
        $country = $countryFactory->createNew();
        $country->setCode($countryCode);

        $countryRepo = $this->getContainer()->get('sylius.repository.country');
        $countryRepo->add($country);

        return $country;
    }

    public function addCurrency($currencyCode) {

        $availCurrencies = Intl::getCurrencyBundle()->getCurrencyNames();

        $currencyRepository = $this->getContainer()->get('sylius.repository.currency');

        $currencies = $currencyRepository->findAll();

        $currencyFactory = $this->getContainer()->get('sylius.factory.currency');

        /**
         * @var Currency $currency
         */
        $currency = $currencyFactory->createNew();
        $currency->setCode('EUR');

        $currencyRepository->add($currency);
        return $currency;
    }
}