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
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction()
    {
        $user = $this->getUser();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            return $this->render('user/list.html.twig', ['users' => $this->getDoctrine()->getRepository('AppBundle:User')->findAll()]);
        } else {
            return $this->render('user/view.html.twig', ['user' => $this->getDoctrine()->getRepository('AppBundle:User')->find($user->getId())]);
        }
    }
    /**
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request)
    {
        $role = (is_null($this->getUser())) ? [] : $this->getUser()->getRoles();

        $user = new User();

        if (in_array('ROLE_ADMIN',$role)){
            $form = $this->createForm(UserAsAdminType::class, $user);
        } else {
            $form = $this->createForm(UserType::class, $user);
        }

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
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
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(User $user, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $user);

        if (in_array('ROLE_ADMIN',$this->getUser()->getRoles())){
            $form = $this->createForm(UserEditAsAdminType::class, $user);
        } else {
            $form = $this->createForm(UserEditType::class, $user);
        }

        $originalPassword = $user->getPassword();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if (!empty($plainPassword)) {
                $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
            } else {
                $user->setPassword($originalPassword);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");
            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}