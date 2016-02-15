<?php

class MiscTest extends PHPUnit_Framework_TestCase
{
    public function testTranslation()
    {
        $app = Application::createByEnvironment('test');
        /** @var Symfony\Component\Translation\Translator $trans */
        $trans = $app['translator'];
        $this->assertEquals('Account confirmation', $trans->trans('acct_confirmation', [], 'email'));
    }

    public function testTemplates()
    {
        $app = Application::createByEnvironment('test');
        /** @var Symfony\Bridge\Twig\TwigEngine $twig */
        $twig = $app['twig'];
        $this->assertEquals('Account confirmation', $twig->render('email/acct_confirmation_subj.twig', ['token' => 'zzz']));
    }

}
