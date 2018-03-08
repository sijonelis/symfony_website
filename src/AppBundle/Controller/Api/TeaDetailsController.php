<?php
/**
 * Created by PhpStorm.
 * User: baka3
 * Date: 11/26/2016
 * Time: 8:26 PM
 */

namespace AppBundle\Controller\Api;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Version({"v1"})
 * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
 */
class TeaDetailsController extends BaseApiController
{
    /**
     * @Get("/api/tea/get/{id}", condition="context.getMethod() in ['GET', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Gets the contents for a single tea.",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the tea is not found or does not exists"
     *     },
     *  parameters={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="tea id"}
     *     },
     *  methods="get",
     *  headers={
     *     {
     *     "name"="Accept",
     *     "description"="Accept header",
     *     "default"="application/yipiao.api+json;version=1"
     *      },
     *     {
     *     "name"="Authorization",
     *     "description"="User access token if logged in, not sent otherwise"
     *      }
     *  }
     * )
     * @param $id
     * @return mixed
     */
    public function getTeaAction($id)
    {
        if (!$this->isValid()) return $this->apiError();

        $userId = empty($this->getDevice()->getUser()) ? 0 : $this->getDevice()->getUser()->getId();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** AppBundle\Repository\TeaRepository $rep */
        $rep = $em->getRepository('AppBundle:Tea');
        $rep->incrementViewCount($id);
        $tea = $rep->displayTeaById($id, $userId);
        //['tea'] part removes unnecessary layer in json resposne
        return isset($tea) ? $tea['tea'] : new Response('{"errors": [{"status": 404, "title": "Content Error.", "detail": "Tea not found."}]}', Response::HTTP_NOT_FOUND);
    }
}