<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-05-01
 * Time: 14:43
 */

namespace AppBundle\Controller\Api;


use AppBundle\Entity\Note;
use AppBundle\Repository\NoteRepository;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ReflectionClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Version({"v1"})
 * @Security("is_granted('ROLE_USER')")
 */
class NoteController extends BaseApiController
{
    /**
     * @Post("/api/note/write", condition="context.getMethod() in ['POST', 'HEAD'] and request.attributes.get('version') == 1")
     * @ParamConverter("note", converter="fos_rest.request_body")
     * @ApiDoc(
     *  resource=true,
     *  description="Creates a note for a particular tea. Notify boolean param controls whether the note will be visible to the Yipiao staff. Has an url ecoded param ?s: api call source. 0: tea details, 1: profile",
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
     * @param Note $note
     * @return Response
     */
    public function writeAction(Note $note)
    {
        if (!$this->isValid()) return $this->apiError();

        /** @var NoteRepository $nr */
        $nr = $this->getDoctrine()->getRepository("AppBundle:Note");
        $note = $nr->writeNote($note, $this->getUser());
        return new Response('', empty($note) ? Response::HTTP_BAD_REQUEST : Response::HTTP_NO_CONTENT);
    }

    /**
     * @Get("/api/note/get/{id}", condition="context.getMethod() in ['GET', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Gets the tea note.",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the user is not authorized",
     *      404="Returned when a note does not belong to the user or is missing"
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
     * @param string $id
     * @return mixed|object
     */
    public function getAction($id) {
        if (!$this->isValid()) return $this->apiError();

        $nr = $this->getDoctrine()->getRepository("AppBundle:Note");
        return $nr->getNote($id, $this->getUser());
    }
}