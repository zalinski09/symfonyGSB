<?php


namespace GSB\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GSB\AppBundle\Entity\LigneFraisHorsForfait;
use Symfony\Component\HttpFoundation\Request;

class FraisHorsForfaitController extends Controller {

    /**
     * Action de controller qui récupère les lignesFraisHorsForfait
     * en fonction de la date et du visiteur authentifié. Rendering dans
     * la vue correspondante.
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeAction(Request $request)
    {
        $dateActuelle = new \DateTime('now');
        $dateActuelleFormat = $dateActuelle->format('Ym');

        $visiteur = $this->getUser();
        $suppression = true;

        $lignesFraisHorsForfait = $this->get('doctrine')
            ->getManager()
            ->getRepository('GSBAppBundle:Lignefraishorsforfait')
            ->findBy(array('mois' => $dateActuelleFormat,'visiteur'=> $visiteur));

        return $this->render('GSBAppBundle::dataTablesDossierHorsForfait.html.twig', array(
            'suppression' => $suppression,
            'lignesfraishorsforfait'=>$lignesFraisHorsForfait));
    }

    /**
     * Action de controller qui prend un paramètre
     * un id de ligneFraisHorsForfait grâce auquel
     * on va supprimer la ligneFraisHorsForfait correspondante.
     * Redirect sur la saisie des frais.
     * @param $id
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id, Request $request)
    {
        //On contrôle quand même qu'on a bien une lignefraishorsforfait
        if (!$id) {
            throw $this->createNotFoundException('Rien a supprimer');
        }
            $em = $this->get('doctrine')->getManager();
            $ligneFraisHorsForfait = $em->getRepository('GSBAppBundle:LigneFraisHorsForfait')->find($id);
            $em->remove($ligneFraisHorsForfait);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Frais hors forfait bien supprimé');

            return $this->redirect($this->generateUrl('gsb_app_indexFrais'));
        }
}