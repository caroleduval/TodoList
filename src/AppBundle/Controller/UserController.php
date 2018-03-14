<?php
namespace AppBundle\Controller;
use AppBundle\Entity\User;
use AppBundle\Form\UserAsAdminType;
use AppBundle\Form\UserType;
use AppBundle\Form\UserEditAsAdminType;
use AppBundle\Form\UserEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
class UserController extends Controller
{
    /**
     * @Route("/users", name="user_list")
     */
    public function listAction()
    {
        $users=$this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        return $this->render('user/list.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (is_null($user->getPassword())){
                throw new \Exception('Password can\'t be null !');
            }
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");
            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }
    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function editAction(User $user, Request $request)
    {
        $form = $this->createForm(UserEditType::class, $user);

        $currentPassword = $user->getPassword();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();
            if (!empty($newPassword)) {
                $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
            } else {
                $user->setPassword($currentPassword);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");
            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}