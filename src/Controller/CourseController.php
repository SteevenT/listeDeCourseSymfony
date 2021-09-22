<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CourseController extends AbstractController
{
    /**
     * @Route("/", name="course")
     */
    public function index(Request $request, ItemRepository $repo): Response
    {
        $items = $repo->findAll();

        $item = new Item();
        
        $formItem = $this->createForm(ItemType::class,$item);
        $formItem->handleRequest($request);

        // si le form est validé.
        if ($formItem->isSubmitted()){
            $item->setDateAdd(new \DateTime('now'));
            $item->setIsBuy(false);
            if($item->getQuantite() == null || $item->getQuantite() <= 0)
            {
                $item->setQuantite(1);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('course');
        }

        return $this->render('course/index.html.twig',[
            'formItem' => $formItem->createView(),
            'items' => $items,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Item $item, EntityManagerInterface $em): Response
    {
        $item->setIsBuy(true);

        $em->persist($item);
        $em->flush();

        return $this->redirectToRoute('course');
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Item $item, EntityManagerInterface $em): Response
    {
        // $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();
        return $this->redirectToRoute('course');
    }
}
