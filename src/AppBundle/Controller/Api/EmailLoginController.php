<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-09-08
 * Time: 16:00
 */

namespace AppBundle\Controller\Api;


use AppBundle\Services\User\Login;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Version({"v1"})
 * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
 */

class EmailLoginController extends BaseApiController
{
    /**
     * @Post("/api/email/login", condition="context.getMethod() in ['POST', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Logs in user or returns an error.",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when user credentials are invalid"
     *     },
     *  methods="post",
     *  headers={
     *     {
     *     "name"="Accept",
     *     "description"="Accept header",
     *     "default"="application/yipiao.api+json;version=1"
     *      },
     *     {
     *     "name"="Content-Type",
     *     "default"="application/json",
     *     "description"="Content type"
     *     }
     *  }
     * )
     * @param Request $request
     * @return Response
     */
    public function loginAction(request $request) {
        if (!$this->isValid()) return $this->apiError();
        $this->setRequest($request);

        $response = new Response();

        $email = $request->get('email');
        $password = $request->get('password');
        if (empty($email) || empty($password)) {
            $response->setContent(json_encode(['error' => 'Email or password missing']));
            $response->setStatusCode( 401 );
            return $response;
        }

        /** @var Login $loginService */
        $loginService = $this->get('app.user.login');

        $accessToken = $loginService->loginEmail($email, $password);
        if ($accessToken == false) {
            $response->setContent(json_encode(['error' => 'Login failed']));
            $response->setStatusCode( 401 );
            return $response;
        }
        $response->setContent(json_encode(['access_token' => $accessToken]));
        $response->setStatusCode( 200 );
        return $response;
    }

    /**
     * @Post("/api/email/register", condition="context.getMethod() in ['POST', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Logs in user or returns an error.",
     *  statusCodes={
     *      200="Returned when successful",
     *      204="Returned when tea of the day is missing",
     *      404="Returned when the tea is not found or does not exists"
     *     },
     *  methods="post",
     *  headers={
     *     {
     *     "name"="Accept",
     *     "description"="Accept header",
     *     "default"="application/yipiao.api+json;version=1"
     *      },
     *     {
     *     "name"="Content-Type",
     *     "default"="application/json",
     *     "description"="Content type"
     *     }
     *  }
     * )
     * @param Request $request
     * @return Response
     */
    public function registerAction(request $request) {
        if (!$this->isValid()) return $this->apiError();

        $response = new Response();

        $email = $request->get('email');
        $password = $request->get('password');
        if (empty($email) || empty($password)) {
            $response->setContent(json_encode(['error' => 'Email or password missing']));
            $response->setStatusCode( 401 );
            return $response;
        }

        /** @var Login $loginService */
        $loginService = $this->get('app.user.login');

        $accessToken = $loginService->registerEmail($email, $password);
        if ($accessToken == false) {
            $response->setContent(json_encode(['error' => 'Registration failed']));
            $response->setStatusCode( 401 );
            return $response;
        }
        $response->setContent(json_encode(['access_token' => $accessToken]));
        $response->setStatusCode( 200 );
        return $response;
    }

    /**
     * @Post("/api/email/reset", condition="context.getMethod() in ['POST', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Logs in user or returns an error.",
     *  statusCodes={
     *      200="Returned when successful",
     *      204="Returned when tea of the day is missing",
     *      404="Returned when the tea is not found or does not exists"
     *     },
     *  methods="post",
     *  headers={
     *     {
     *     "name"="Accept",
     *     "description"="Accept header",
     *     "default"="application/yipiao.api+json;version=1"
     *      },
     *     {
     *     "name"="Content-Type",
     *     "default"="application/json",
     *     "description"="Content type"
     *     }
     *  }
     * )
     * @param Request $request
     * @return Response
     */
    public function resetAction(request $request) {
        if (!$this->isValid()) return $this->apiError();

        $response = new Response();

        $email = $request->get('email');
        if (!$email) {
            $response->setContent(json_encode(['error' => 'Email missing']));
            $response->setStatusCode( 401 );
            return $response;
        }

        /** @var Login $loginService */
        $loginService = $this->get('app.user.login');

        $loginService->sendResetEmailRequest($email, $this->get('swiftmailer.mailer'), $this->get('templating'));

        $response->setContent(json_encode(['status' => 'Email Sent']));
        $response->setStatusCode(200 );
        return $response;
    }

    /**
     * @Post("/api/email/change-password", condition="context.getMethod() in ['POST', 'HEAD']")
     * @Get("/change-password", condition="context.getMethod() in ['GET', 'HEAD']")
     * @ApiDoc(
     *  resource=true,
     *  description="Logs in user or returns an error.",
     *  statusCodes={
     *      200="Returned when password successfully changed or when password change form is sent",
     *      401="Returned when token or user does not exists or the token has expired"
     *     },
     *  methods="post",
     * )
     * @param Request $request
     * @return Response
     */
    public function changePasswordAction(request $request) {
        if (!$this->isValid()) return $this->apiError();
//        $this->setRequest($request);

        $response = new Response();

        $token = $request->get('token');
        $newPassword = $request->get('new_password');
        $loginService = $this->get('app.user.login');

        if ($token) {

            $accessToken = $loginService->changePassword($token, $newPassword);
            if ($accessToken == false) {
                $response->setContent(json_encode(['errors' => ['Invalid token']]));
                $response->setStatusCode( 401 );
            } else {
                $response->setContent(json_encode(['access_token' => $accessToken]));
                $response->setStatusCode(200);
            }
        }
        elseif (array_key_exists('token', $_GET)){
            if ($loginService->verifyResetToken($_GET['token'])) {
                $response->setContent(json_encode(['token' => $_GET['token']]));
                $response->setStatusCode(200);
            }
            else {
                $response->setContent(json_encode(['error' => 'Invalid token']));
                $response->setStatusCode( 401 );
            }
            //render some form in app
        } else {
            $response->setContent(json_encode(['error' => 'Invalid token']));
            $response->setStatusCode( 401 );
        }
        return $response;
    }
}