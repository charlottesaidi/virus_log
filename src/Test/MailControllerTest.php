<?php

namespace App\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MailControllerTest extends WebTestCase
{
    public function testMailIsSentAndContentIsOk()
    {
        $this->assertEmailCount(1, 'smtp://localhost:1025');

        $email = $this->getMailerMessage();

        $this->assertEmailHtmlBodyContains($email, 'Welcome');
        $this->assertEmailTextBodyContains($email, 'Welcome');
    }
}