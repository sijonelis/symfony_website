<?php
/**
 * Created by PhpStorm.
 * User: baka3
 * Date: 11/28/2016
 * Time: 9:23 PM
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Repository\TeaFeaturedRepository;
use AppBundle\Services\User\Exposure;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Version({"v1"})
 * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
 */
class NewsfeedController extends BaseApiController
{
    /**
     * @Get("/api/newsfeed/today", condition="context.getMethod() in ['GET', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Gets the contents for a single tea.",
     *  statusCodes={
     *      200="Returned when successful",
     *      204="Returned when tea of the day is missing",
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTodayAction()
    {
        if (!$this->isValid()) return $this->apiError();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var TeaFeaturedRepository $rep */
        $rep = $em->getRepository('AppBundle:TeaFeatured');
        $tea = $rep->getCurrentTea($this->getUser());

        $this->getExposureTracker()->logNewsfeed($tea['tea']['id'], $tea['newsfeed']['id']);
        $this->getExposureTracker()->logTeaShow([$tea['tea']['id']], \AppBundle\Entity\Log\Exposure::TEA_EXPOSURE_NEWSFEED);

        return $tea;
    }

    /**
     * @Get("/api/newsfeed/get/{id}", condition="context.getMethod() in ['GET', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Gets the contents for a single featured tea.",
     *  statusCodes={
     *      200="Returned when successful",
     *      204="Returned when tea of the day is missing",
     *      404="Returned when the tea is not found or does not exists"
     *     },
     *  parameters={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="news-feed item id"}
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
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneAction($id)
    {
        if (!$this->isValid()) return $this->apiError();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var TeaFeaturedRepository $rep */
        $rep = $em->getRepository('AppBundle:TeaFeatured');
        $tea = $rep->getTeaById($id, $this->getUser());
        if (!$tea)
            $tea = $rep->getCurrentTea($this->getUser());

        $this->getExposureTracker()->logNewsfeed($tea['tea']['id'], $tea['newsfeed']['id']);
        $this->getExposureTracker()->logTeaShow([$tea['tea']['id']], \AppBundle\Entity\Log\Exposure::TEA_EXPOSURE_NEWSFEED);

        return $tea;
    }
}