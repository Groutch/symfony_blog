<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Comments;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PostsController extends AbstractController
{
    /**
     * @Route("/posts", name="posts")
     */
    public function index()
    {
        $list = $this->getDoctrine()
            ->getRepository(Posts::class)
            ->findAll();
        return $this->render('posts/index.html.twig', [
            'controller_name' => 'PostsController', 'list' => $list
        ]);
    }
    
    /**
    * @Route("/posts/create", name="postCreate")
    */
    public function store(Request $request)
    {
        $post = new Posts();
        $post->setTitle("Article ".random_int(0,100));
        $post->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse malesuada dui id nisi hendrerit, a faucibus mauris commodo. Aenean vel tincidunt leo. Ut in purus posuere, scelerisque eros ac, consectetur dolor.');
        $post->setAuthor("Groutch");
        $post->setCreatedAt(new \DateTime('now'));
        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('author', TextType::class)
            //->add('trucmuch',CheckboxType::class, array('label' => 'tag1'))
            ->add('save', SubmitType::class, array('label' => 'Créer Post'))
            ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute('posts');
        }
        
        return $this->render('posts/create.html.twig', array('form'=>$form->createView()));
    }
    
    /**
    * @Route("/posts/edit/{id}")
    */
    public function edit(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Posts::class)->find($id);
        $post->setUpdatedAt(new \DateTime('now'));
        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('author', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Modifier Post'))
            ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $postu = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($postu);
            $entityManager->flush();
            return $this->redirectToRoute('posts');
        }
        return $this->render('posts/update.html.twig', array('form'=>$form->createView()));
        
    }
    
    /**
    * @Route("/posts/delete/{id}")
    */
    public function delete($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Posts::class)->find($id);
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->redirectToRoute('posts');
    }
    
    /**
    * @Route("/posts/display/{id}", name="postDisplay")
    */
    public function display($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Posts::class)->find($id);
        return $this->render('posts/display.html.twig', array('post'=>$post));
    }
    
    /**
    * @Route("/posts/addcomment/{id}")
    */
    public function addComment(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Posts::class)->find($id);
        $comment = new Comments();
        $comment->setPost($post);
        $comment->setCreatedAt(new \DateTime('now'));
        $form = $this->createFormBuilder($comment)
            ->add('content', TextareaType::class)
            ->add('author', TextType::class)
            
            ->add('save', SubmitType::class, array('label' => 'Créer commentaire'))
            ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comm = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comm);
            $entityManager->flush();
            return $this->display($id);
            //return $this->redirectToRoute('postDisplay/'.$id);
        }
        return $this->render('posts/addcomment.html.twig', array('form'=>$form->createView()));
    }
}
