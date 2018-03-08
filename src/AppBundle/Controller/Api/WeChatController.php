<?php
namespace AppBundle\Controller\Api;

use AppBundle\Services\User\Login;
use AppBundle\Services\User\WeixinApi;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Version;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Version({"v1"})
 * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
 */
class WeChatController extends BaseApiController
{
    //todo wechat mobile login guide
    //http://open.wechat.com/cgi-bin/newreadtemplate?t=overseas_open/docs/mobile/login/guide

    /**
     * @Post("/api/wechat/auth", condition="context.getMethod() in ['POST', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Authenticates a WeChat user.",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned on authentication error"
     *     },
     *  methods="post",
     *  parameters={
     *     {"name"="token", "dataType"="string", "required"=true, "description"="WeChat access token"},
     *     {"name"="open_id", "dataType"="string", "required"=true, "description"="WeChat user's OpenId"},
     *     {"name"="update_profile_image", "dataType"="integer", "required"=false, "description"="Flag to update or not update user profile image. Defaults to 1 (update)"}
     *  },
     *  headers={
     *     {
     *     "name"="Accept",
     *     "description"="Accept header",
     *     "default"="application/yipiao.api+json;version=1"
     *      }
     *  }
     * )
     */

    public function authAction()
    {
        if (!$this->isValid()) return $this->apiError();
        $request = $this->getRequest();
        $response = new Response();

        $code = $request->get('code');
        if (empty($code)) {
            $response->setStatusCode( 401 );
            return $response;
        }
        /** @var WeixinApi $weixinService */
        $weixinService = $this->get('app.user.weixin');
        $weixinJson = $weixinService->getProfileFromWeixin($code, $this->getDevice());

        if (!isset($weixinJson) || property_exists($weixinJson, 'errmsg')) {
            if(!isset($weixinJson)) {
                $response->setContent('{"errors": [{"status": 401, "title": "Weixin Error.", "detail": "Data Error!"}]}');
            } else {
                $response->setContent('{"errors": [{"status": 401, "title": "Weixin Error.", "detail": "'. $weixinJson->errmsg . '"}]}');
            }
            $response->setStatusCode( 401 );
            return $response;
        }

        /** @var Login $loginService */
        $loginService = $this->get('app.user.login');

        $accessToken = $loginService->loginWeixin($weixinJson->openid, $weixinJson);
        if ($accessToken == false) {
            $response->setContent(json_encode(['error' => 'User blocked']));
            $response->setStatusCode( 401 );
        }
        return ['access_token' => $accessToken];
    }
}