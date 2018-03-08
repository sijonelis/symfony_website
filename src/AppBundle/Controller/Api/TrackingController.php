<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2018-01-24
 * Time: 13:55
 */

namespace AppBundle\Controller\Api;



use AppBundle\Entity\Log\LogDataObject;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Version;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Version({"v1"})
 * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
 */
class TrackingController extends BaseApiController
{
    /**
     * @Post("/api/tracking", condition="context.getMethod() in ['POST', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Processes tracking data.",
     *  statusCodes={
     *      200="Returned when successful",
     *     },
     *  methods="post",
     *  headers={
     *     {
     *     "name"="Accept",
     *     "description"="Accept header",
     *     "default"="application/yipiao.api+json;version=1"
     *      }
     *  }
     * )
     */
    public function trackingAction(Request $request)
    {

        if (!$this->isValid()) return $this->apiError();

        $trackingData = $request->get("data");

        if (is_array($trackingData) && !empty($trackingData)) {
            $this->getEngagementTracker()->logGeneralEngagement($trackingData);
        }
        return null;
    }
}