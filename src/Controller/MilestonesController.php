<?php

namespace App\Controller;

use App\Entity\Milestones;
use App\Entity\Task;
use App\Form\MilestonesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MilestonesController extends AbstractController
{
    /**
     * @Route("/milestones", name="milestones")
     */
    public function index(): Response
    {
        $repo = $this->getDoctrine()->getRepository(Milestones::class);
        return $this->render('milestones/index.html.twig', [
            'milestones' => $repo->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_EMPLOYER")
     * @Route("/milestones/add", name="milestone_add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        $milestone = new Milestones();
        $form = $this->createForm(MilestonesType::class, $milestone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($milestone);
            $em->flush();
            $this->addFlash('success', 'Pomyślnie dodano wydarzenie');

            return $this->redirectToRoute('milestones');
        }

        return $this->render('milestones/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/milestones/description/{milestone}", name="milestone_description")
     * @param Request $request
     * @param Milestones $milestone
     * @return Response
     */
    public function show(Request $request, Milestones $milestone)
    {
        $repo = $this->getDoctrine()->getRepository(Milestones::class);
        $repo2 = $this->getDoctrine()->getRepository(Task::class);
        return $this->render('milestones/description.html.twig', [
            'milestone' => $repo->find($milestone),
            'tasks' => $repo2->findBy(['milestone' => $milestone]),
        ]);
    }

    /**
     * @IsGranted("ROLE_EMPLOYER")
     * @Route("/milestones/edit/{milestone}", name="milestone_edit")
     * @param Request $request
     * @param Milestones $milestone
     * @return Response
     */
    public function edit(Request $request, Milestones $milestone)
    {
        $form = $this->createForm(MilestonesType::class, $milestone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'Pomyślnie zmieniono kamień milowy');

            return $this->redirectToRoute('milestones');
        }

        return $this->render('milestones/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
