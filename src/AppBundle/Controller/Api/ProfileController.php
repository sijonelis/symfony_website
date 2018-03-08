<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-04-09
 * Time: 12:09
 */
namespace AppBundle\Controller\Api;

use AppBundle\Entity\Log\Engagement;
use AppBundle\Entity\Log\Exposure;
use AppBundle\Entity\TeaSuggest;
use AppBundle\Entity\User;
use AppBundle\Repository\TeaRepository;
use AppBundle\Repository\TeaSuggestRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Tools\EntityGenerator;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Version;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Version({"v1"})
 * @Security("is_granted('ROLE_USER')")
 */
class ProfileController extends BaseApiController
{
    /**
     * @Get("/api/profile/get", condition="context.getMethod() in ['GET', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Gets base user profile.",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the user is not found"
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
     */
    public function getAction()
    {
        if (!$this->isValid()) return $this->apiError();

        /** @var User $user */
        $user = $this->getUser();

        $orm = $this->getDoctrine()->getManager();
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository('AppBundle:User');

        $userData = [
            'nickname' => $user->getNickname(),
            'avatar' => $user->getAvatar(),
            'fav_tea_count' => $userRepo->getFavouriteTeaCount($user),
            'note_count' => $userRepo->getUserNoteCount($user)
        ];

        $this->getExposureTracker()->logProfileShow();

        return $userData;
    }

    /**
     * @Get("/api/profile/notes", condition="context.getMethod() in ['GET', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Gets user notes.",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the user is not found"
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
     */
    public function notesAction() {
        if (!$this->isValid()) return $this->apiError();

        /** @var UserRepository $notesRepo */
        $notesRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');

        $userNotes = $notesRepo->getUserNotes($this->getUser());

        if (is_array($userNotes) && array_key_exists('notes', $userNotes) && is_array($userNotes['notes'])) {
            $this->getExposureTracker()->logNoteShow($userNotes['notes']);
        }

        return $userNotes;
    }

    /**
     * @Get("/api/profile/favourites", condition="context.getMethod() in ['GET', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Gets user notes.",
     *  statusCodes={
     *      200="Returned when successful",
     *      401="Returned when the user is not found"
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
     */
    public function favouritesAction() {
        if (!$this->isValid()) return $this->apiError();

        $orm = $this->getDoctrine()->getManager();
        /** @var UserRepository $userRepo */
        $userRepo = $orm->getRepository('AppBundle:User');

        $favTeaList = $userRepo->getFavouriteTeas($this->getUser());

        if (is_array($favTeaList) && array_key_exists('teas', $favTeaList) && is_array($favTeaList['teas'])) {
            $this->getExposureTracker()->logTeaShow($favTeaList['teas'], Exposure::TEA_EXPOSURE_PROFILE);
        }

        return $favTeaList;
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     * @Patch("/api/profile/tea-like/{id}", condition="context.getMethod() in ['PATCH', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Likes or dislikes a tea.",
     *  statusCodes={
     *      204="Returned when successful",
     *      400="Returned when something bad happens",
     *      404="Returned when the tea is not found or does not exists"
     *     },
     *  parameters={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="tea id"}
     *     },
     *  methods={"patch"},
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
     * @param $id
     * @return Response
     */
    public function favouriteTeaAction($id)
    {
        if (!$this->isValid()) return $this->apiError();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var TeaRepository $rep */
        $rep = $em->getRepository('AppBundle:Tea');

        $result = $rep->favouriteTea($id, $this->getUser());

        if (!is_array($result))
            $this->getEngagementTracker()->logTeaFavourite($id, $result);

        //todo check httpOK vs httpNoContent
        return !is_array($result) ? new Response('', Response::HTTP_NO_CONTENT) : new Response(json_encode($result), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     * @Post("/api/profile/update-image", condition="context.getMethod() in ['POST', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Uploads new user image. Param: avatar - image file; Must be done with a form type post. Returns json with keys (avatar (string), uploaded (bool)",
     *  statusCodes={
     *      204="Returned when successful",
     *      400="Returned when something bad happens",
     *      404="Returned when the tea is not found or does not exists"
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
     *  },
     * )
     * @param Request $request
     * @return array|Response
     * @throws OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateImageAction(Request $request)
    {
        if (!$this->isValid()) return $this->apiError();

        /** @var User $user */
        $user = $this->getUser();
        $uploadedfile = $request->files->get('avatar');
        if (!$uploadedfile) return  new Response(json_encode(['errors' => ['Image not supported']]), Response::HTTP_BAD_REQUEST);
        $uploader = $this->get('vich.qiniu');
        $uploadedAvatar = $uploader->uploadFile($uploadedfile, 'usr',  $user->getAvatar());
        if ($uploadedAvatar == null) {
            new Response('', Response::HTTP_BAD_REQUEST);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var UserRepository $rep */
        $rep = $em->getRepository('AppBundle:User');
        $result = $rep->updateUserImage($user, $uploadedAvatar);
        if (!empty($result)) {
            $this->getEngagementTracker()->logProfileUpdate(Engagement::TYPE_PROFILE_IMAGE);
        }
        //todo check httpOK vs httpNoContent
        return empty($result) ? new Response('', Response::HTTP_BAD_REQUEST) : $result;
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     * @Patch("/api/profile/update", condition="context.getMethod() in ['PATCH', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Likes or dislikes a tea.",
     *  statusCodes={
     *      204="Returned when successful",
     *      400="Returned when something bad happens",
     *      404="Returned when the tea is not found or does not exists"
     *     },
     *  methods={"patch"},
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
     * @return array|Response
     * @throws OptimisticLockException
     * @throws \Doctrine\ORM\ORMException* @internal param $id
     */
    public function updateProfileAction(Request $request)
    {
        if (!$this->isValid()) return $this->apiError();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var UserRepository $rep */
        $rep = $em->getRepository('AppBundle:User');
        $result = $rep->updateUser($this->getUser(), $request->request->all());
        if (!empty($result))
            $this->getEngagementTracker()->logProfileUpdate(Engagement::TYPE_PROFILE_DETAILS);
        //todo check httpOK vs httpNoContent
        return empty($result) ? new Response(json_encode($result), Response::HTTP_BAD_REQUEST) : $result;
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     * @Patch("/api/profile/sync", condition="context.getMethod() in ['PATCH', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Syncs favourite teas and notes when a guest user registers. In case any data overlaps, the data kept in the server is being prioritized over device data.",
     *  statusCodes={
     *      204="Returned when successful",
     *      400="Returned when something bad happens",
     *     },
     *  methods={"patch"},
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

        $result = $this->get('app.user.sync')->execute($this->getUser(), $request->request->all());
        //todo check httpOK vs httpNoContent
        return empty($result) ? new Response(json_encode($result), Response::HTTP_BAD_REQUEST) : $result;
    }


    /**
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @Post("/api/profile/suggest-tea", condition="context.getMethod() in ['POST', 'HEAD'] and request.attributes.get('version') == 1")
     * @ApiDoc(
     *  resource=true,
     *  description="Persists user's tea suggestion. Accepts source (?s) parametes where ?s=0 - profile, ?s=1 - search",
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
     *     "description"="Not necessary for this api. If missing user should be processed online or directed to login, as the api will return an error."
     *      }
     *  },
     * )
     * @param Request $request
     * @return array|Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function suggestTeaAction(Request $request)
    {
        if (!$this->isValid()) return $this->apiError();

        $teaSuggest = new TeaSuggest();
        $teaSuggest->setIsOpen(true);
        $teaSuggest->setSuggestion($request->get('suggestion'));
        $teaSuggest->setUser($this->getUser());

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $em->persist($teaSuggest);
        try {
            $em->flush($teaSuggest);
        } catch (OptimisticLockException $e) {
        }

        //todo check httpOK vs httpNoContent
        return new Response('', empty($teaSuggest->getId()) ? Response::HTTP_BAD_REQUEST : Response::HTTP_NO_CONTENT);
    }
}