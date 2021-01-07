<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use mysql_xdevapi\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    /**
     * @Route("/team", name="team")
     */
    public function index(): Response
    {
        $repo=$this->getDoctrine()->getRepository(Team::class);
        return $this->render('team/index.html.twig', [
            'team' => $repo->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_EMPLOYER")
     * @Route("/team/add", name="team_add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response{
        $team=new Team();
        $form=$this->createForm(TeamType::class,$team);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            $this->addFlash('success','Pomyślnie dodano drużyne');
            return $this->redirectToRoute('team');
        }

        return $this->render('team/add.html.twig',[
           'form'=>$form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_EMPLOYER")
     * @Route("/team/{team}/edit",name="team_edit")
     * @param Request $request
     * @param Team $team
     * @return Response
     */
    public function edit(Request $request, Team $team): Response
    {
        $form=$this->createForm(TeamType::class,$team);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success','Pomyślnie zmieniono zespół');
            return $this->redirectToRoute('team');
        }

        return $this->render('team/edit.html.twig',[
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_EMPLOYER")
     * @Route("/team/{team}/delete", name="team_delete",methods={"POST"})
     * @param Team $team
     * @return Response
     */
    public function delete(Team $team):Response
    {
        try{
            $em=$this->getDoctrine()->getManager();
            $em->remove($team);
            $em->flush();
        }catch (Exception $exception){
            $this->addFlash('error', 'Wystąpił błąd podczas usuwania zespołu');
            return new JsonResponse($exception->getMessage(),500);
        }
        $this->addFlash('success','Pomyślnie usunięto zespół');
        return new JsonResponse('Pomyślnie usunięto team',200);


    }
}
