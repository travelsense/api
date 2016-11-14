<?php
namespace Test;

use Api\Application;
use Api\Mapper\DB\BookingMapper;
use Api\Test\ApiClient;
use Api\Test\DatabaseTrait;
use Api\Test\Mailer;
use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ApplicationTest extends WebTestCase
{
    use DatabaseTrait;

    /**
     * Creates the application.
     *
     * @return HttpKernelInterface
     */
    public function createApplication()
    {
        return new Application('test');
    }

    public function setUp()
    {
        parent::setUp();
        $this->resetDatabase($this->app);
    }

    public function testBookingEmail()
    {
        $this->app['mapper.db.booking'] = $this->getMockBuilder(BookingMapper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $client = $this->createApiClient();
        $this->createAndLoginUser($client);
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

    protected function createAndLoginUser(ApiClient $client)
    {
        $email = 'tester@example.com';
        $password = '123abc';
        $client->post('/user',
            [
                'firstName' => 'Test',
                'lastName'  => 'Tester',
                'email'     => $email,
                'password'  => $password,
            ]
        );
        $client->login($email, $password);
    }

    protected function createApiClient(): ApiClient
    {
        return new ApiClient($this->createClient());
    }
}
