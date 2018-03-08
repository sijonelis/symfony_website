<?php
/**
 * Created by PhpStorm.
 * User: baka3
 * Date: 2/21/2017
 * Time: 9:38 PM
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Services\QiniuUploader;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\Route;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class TeaController extends BaseAdminController
{
    protected function deleteAction() {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** AppBundle\Repository\TeaRepository $rep */
        $teaFeaturedRep = $em->getRepository('AppBundle:TeaFeatured');
        $teaFeaturedRep->deleteTeaFromFeaturedTeas($this->request->query->get('id'));
        return (parent::deleteAction());
    }

    protected function newAction() {
        return (parent::newAction());
    }

    /**
     * @Route("/admin/upload-image", name="upload-image")
     *
     * @param Request $request
     *
     * @return Response|Response
     */
    public function uploadTextImageAction(Request $request) {
        if (empty($_FILES['upload'])) return new Response('', Response::HTTP_BAD_REQUEST);
        $qiniuUploader = $this->container->get('vich.qiniu');
        $response = $qiniuUploader->uploadFile($request->files->get('upload'), 'tea-content', $_FILES['upload']['name']);
        return new Response(json_encode($response), Response::HTTP_OK);

    }
}