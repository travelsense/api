<?php
namespace Test;

use Api\Application;
use Api\Exception\ApiException;
use Api\Mapper\DB\BookingMapper;
use Api\Test\ApiClient;
use Api\Test\ApiClientException;
use Api\Test\DatabaseTrait;
use Api\Test\Mailer;
use RuntimeException;
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

    public function testImageUpload()
    {
        $dir = '/tmp/images';
        exec("rm -rf $dir && mkdir $dir");
        $this->app['config']['image_upload']['dir'] = $dir;
        $client = $this->createApiClient();
        $this->createAndLoginUser($client);
        $response = $client->rawPost(
            '/image',
            file_get_contents(__DIR__ . '/stub/pic.jpg')
        );
        $this->assertEquals(
            'https://static.hoptrip.us/b2/0e/b20e6e912ef015c7389230a9b8c0ac6959c37fda',
            $response['url']
        );
        $this->assertFileEquals(
            __DIR__ . '/stub/pic.jpg',
            '/tmp/images/b2/0e/b20e6e912ef015c7389230a9b8c0ac6959c37fda'
        );

        /**
         * Invalid mime type
         */

        try {
            $client->rawPost('/image', file_get_contents(__FILE__));
            $this->fail('No exception');
        } catch (ApiClientException $e) {
            $this->assertEquals('Invalid mime type: text/x-php', $e->getMessage());
            $this->assertEquals(ApiException::VALIDATION, $e->getCode());
        }

        /**
         * Unauthorized client
         */

        $anon = $this->createApiClient();
        try {
            $anon->rawPost('/image', file_get_contents(__FILE__));
            $this->fail('No exception');
        } catch (RuntimeException $e) {
            $this->assertEquals(401, $e->getCode());
        }
    }

    protected function createAndLoginUser(ApiClient $client)
    {
        $email = 'tester@example.com';
        $password = '123abc';
        $client->post(
            '/user',
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
