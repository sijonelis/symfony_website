<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-10-20
 * Time: 12:08
 */

namespace AppBundle\Controller\Api;


use AppBundle\Entity\Communication\PushNotification;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends BaseApiController
{
    /**
     * @Post("/api/notification/send", condition="context.getMethod() in ['POST', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Creates a note for a particular tea. Notify boolean param controls whether the note will   be visible to the Yipiao staff.",
     *  statusCodes={
     *      204="Returned when successful",
     *      404="Returned when the user authentication fails",
     *      400="Returned when the tea is not found or note validation fails",
     *      415="Returned when the Content-Type header is not set to application/json"
     *     },
     *  input="json representation of the note model(id, note, tea->id, notify)",
     *  methods={"post"},
     *  headers={
     *     {
     *     "name"="Accept",
     *     "description"="Accept header",
     *     "default"="application/yipiao.api+json;version=1"
     *      },
     *     {
     *     "name"="Authorization",
     *     "description"="User access token if logged in, not sent otherwise"
     *      },
     *     {
     *     "name"="Content-Type",
     *     "default"="application/json",
     *     "description"="Content type"
     *     }
     *  }
     * )
     * @return Response
     */
    public function writeAction()
    {
        if (!$this->isValid()) return $this->apiError();

        $oneSignal = $this->get('app.onesignal');
        $entity = new PushNotification();
        $entity->setText("TEST TEXT");
        $entity->setTitle("TEST_TITLE");
        $entity->setSendToIos(true);
        $entity->setSendToAndroid(true);
        $entity->setId(13);
        $entity->setUrl('yipiao://tea/12');
        $asd = $oneSignal->newMessage($entity);
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}