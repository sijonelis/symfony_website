<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-05-02
 * Time: 22:50
 */

namespace AppBundle\Controller\Api;


use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Version({"v1"})
 * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
 */
class SearchController extends BaseApiController
{
    /**
     * @Get("/api/search/tea", condition="context.getMethod() in ['GET', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Searches teas by name",
     *  statusCodes={
     *      200="Returned when successful",
     *      204="Returned when tea is not found",
     *      404="Returned when the tea is not found or does not exists"
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
     * @param Request $request
     * @return array
     */
    public function searchAction(Request $request)
    {
        if (!$this->isValid()) return $this->apiError();

        //todo in the future we can implement algolia: https://www.algolia.com/
        $query = $request->query->get('q', null);
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** AppBundle\Repository\TeaFeaturedRepository $rep */
        $rep = $em->getRepository('AppBundle:Tea');
        return $rep->search($query);
    }
}