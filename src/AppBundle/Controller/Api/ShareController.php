<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2018-01-21
 * Time: 14:23
 */

namespace AppBundle\Controller\Api;


use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShareController extends BaseApiController
{
    /**
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @Post("/api/share", condition="context.getMethod() in ['PATCH', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Tracks user sharing data.",
     *  statusCodes={
     *      204="Returned when successful",
     *      400="Returned when something bad happens",
     *     },
     *  methods={"post"},
     *  headers={
     *     {
     *     "name"="Accept",
     *     "description"="Accept header",
     *     "default"="application/yipiao.api+json;version=1"
     *      },
     *     {
     *     "name"="Authorization",
     *     "description"="Necessary for this api. If missing user should be processed online or directed to login, as the api will return an error."
     *      }
     *  }
     * )
     * @param Request $request
     * @return Response
     * @internal param $id
     */
    public function syncUserDataAction(Request $request) {
        if (!$this->isValid()) return $this->apiError();
        $this->
        $result = $this->get('app.user.sync')->execute($this->getUser(), $request->request->all());
        //todo check httpOK vs httpNoContent
        return empty($result) ? new Response(json_encode($result), Response::HTTP_BAD_REQUEST) : $result;
    }

}