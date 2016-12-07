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

        $payload = json_decode(file_get_contents(__DIR__ . '/stub/booking_request.json'), true);

        $client = $this->createApiClient('testtoken');
        $client->registerBooking(42, $payload);

        /** @var Mailer $mailer */
        $mailer = $this->app['mailer'];
        $messages = $mailer->getLoggedMessages();
        $last_message = array_pop($messages);
        $this->assertEquals('HopTrip Booking Request', $last_message->getSubject());
        $this->assertEquals('text/html', $last_message->getChildren()[0]->getContentType());
        $this->assertEquals('application/pdf', $last_message->getChildren()[1]->getContentType());
    }
}
