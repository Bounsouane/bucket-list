<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishFormType;
use App\Repository\WishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/wish', name: 'wish_')]

class WishController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->findBy(["isPublished" => true],["dateCreated" =>"DESC"]);

        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes
        ]);
    }

    #[Route('/{id}', name: 'detail', requirements: ["id"=>"\d+"])]
    public function detail(int $id): Response
    {
        dump($id);
        //TODO renvoyer le détail sur une idée de choses à faire
        return $this->render('wish/detail.html.twig');
    }

    #[Route('/{id}', name: 'show', requirements: ["id"=>"\d+"])]
    public function show(int $id, WishRepository$wishRepository): Response
    {
        $wish=$wishRepository->find($id);
        return $this->render('wish/show.html.twig', [
            'wish'=>$wish
        ]);
    }

    #[Route('/add', name:'add')]
    public function add(Request $request, WishRepository $wishRepository) : Response
    {
        $wish = new Wish();
        $wishform= $this->createForm(WishFormType::class, $wish);

        $wishform->handleRequest($request);

        if($wishform->isSubmitted() && $wishform->isValid()){

            $wish->setDateCreated(new \DateTime);
            $wish->setIsPublished(true);

            $wishRepository->save($wish, true);

            $this->addFlash('success',"Wish added !");

            return $this->redirectToRoute('wish_show',['id'=>$wish->getId()]);
        }

        return $this->render('wish/add.html.twig',[
        'wishForm' => $wishform->createView()
        ]);
    }
}
