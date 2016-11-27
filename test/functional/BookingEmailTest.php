<?php
namespace Test;


use Api\Model\User;
use Api\Test\ApplicationTestCase;
use Api\Test\Mailer;

class BookingEmailTest extends ApplicationTestCase
{
    public function testBookingEmail()
    {
        $this->appExpectTokens(['testtoken' => 1]);
        $this->appExpectUsers([
            $this->createConfiguredMock(User::class, ['getId' => 1])
        ]);

        $client = $this->createApiClient('testtoken');
        $client->post(
            '/travel/42/book',
            json_decode(file_get_contents(__DIR__ . '/stub/booking_request.json'))
        );

        /** @var Mailer $mailer */
        $mailer = $this->app['mailer'];
        $messages = $mailer->getLoggedMessages();
        $last_message = array_pop($messages);
        $this->assertEquals('HopTrip Booking Request', $last_message->getSubject());
        $this->assertEquals('application/pdf', $last_message->getChildren()[0]->getContentType());
    }
}
