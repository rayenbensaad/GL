<?php
namespace AppBundle\Controller\Api;
use AppBundle\AppBundle;
use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\Validate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
class PublicationController extends Controller
{
    /**
     * @ApiDoc(
     *      resource=true,
     *     description="Get one single post",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The post unique identifier."
     *         }
     *     },
     *     section="publication"
     * )
     * @Route("/api/publication/{id}",name="show_publication")
     * @Method({"GET"})
     */
    public function showPublication($id)
    {
        $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->find($id);
        if (empty($publication)) {
            $response = array(
                'code' => 1,
                'message' => 'publication not found',
                'error' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $data = $this->get('jms_serializer')->serialize($publication, 'json');
        $response = array(
            'code' => 0,
            'message' => 'success',
            'errors' => null,
            'result' => json_decode($data)
        );
        return new JsonResponse($response, 200);
    }





    /**
     * @ApiDoc(
     * description="Create a new post",
     *
     *    statusCodes = {
     *        201 = "Creation with success",
     *        400 = "invalid form"
     *    },
     *    responseMap={
     *         201 = {"class"=Post::class},
     *
     *    },
     *     section="posts"
     *
     *
     * )
     *
     * @param Request $request
     * @param Validate $validate
     * @return JsonResponse
     * @Route("/api/publications",name="create_publication")
     * @Method({"POST"})
     */
    public function createPublication(Request $request,Validate $validate)
    {
        $data = $request->getContent();
        $publication = $this->get('jms_serializer')->deserialize($data, 'AppBundle\Entity\Publication', 'json');
        $reponse = $validate->validateRequest($publication);
        if (!empty($reponse)) {
            return new JsonResponse($reponse, Response::HTTP_BAD_REQUEST);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($publication);
        $em->flush();
        $response = array(
            'code' => 0,
            'message' => 'Publication created!',
            'errors' => null,
            'result' => null
        );
        return new JsonResponse($response, Response::HTTP_CREATED);


    }




    /**
     * @Route("/api/publication", name="article_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $publication = $this->getDoctrine()->getRepository('AppBundle:Publication')->findAll();
        $data = $this->get('jms_serializer')->serialize($publication, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }



    /**
     * @param Request $request
     * @param $id
     * @Route("/api/publication/{id}",name="update_post")
     * @Method({"POST"})
     * @return JsonResponse
     */
    public function updatePublication(Request $request,$id,Validate $validate)
    {
        $publication=$this->getDoctrine()->getRepository('AppBundle:Publication')->find($id);
//        if (empty($publication))
//        {
//            $response=array(
//                'code'=>1,
//                'message'=>'Publication Not found !',
//                'errors'=>null,
//                'result'=>null
//            );
//            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
//        }
//        $body=$request->getContent();
        $data= [
            'title' => $request->request->get('title'),
            'description' => $request->request->get('description'),
            'category' => $request->request->get('category'),
            'city' => $request->request->get('city'),
            'phoneNumber' => $request->request->get('phoneNumber'),
            'picture' => $request->request->get('picture'),
        ];

        var_dump($data);
//        $reponse= $validate->validateRequest($data);
//        if (!empty($reponse))
//        {
//            return new JsonResponse($reponse, Response::HTTP_BAD_REQUEST);
//        }
        $publication->setTitle($data['title']);
        $publication->setDescription($data['description']);
        $publication->setCategory($data['category']);
        $publication->setCity($data['city']);
        $publication->setPhoneNumber($data['phoneNumber']);
        $publication->setPicture($data['picture']);


        $em=$this->getDoctrine()->getManager();
        $em->merge($publication);
        $em->flush();

        $response=array(
            'code'=>0,
            'message'=>'Publication updated!',
            'errors'=>null,
            'result'=>null
        );
        return new JsonResponse($response,200);
    }








    /**
     * @Route("/api/publication/{id}",name="delete_post")
     * @Method({"DELETE"})
     */
    public function deletePublication($id)
    {
        $publication=$this->getDoctrine()->getRepository('AppBundle:Publication')->find($id);
        if (empty($publication)) {
            $response=array(
                'code'=>1,
                'message'=>'publication Not found !',
                'errors'=>null,
                'result'=>null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        $em=$this->getDoctrine()->getManager();
        $em->remove($publication);
        $em->flush();
        $response=array(
            'code'=>0,
            'message'=>'publication deleted !',
            'errors'=>null,
            'result'=>null
        );
        return new JsonResponse($response,200);
    }




}