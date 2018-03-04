<?php
/**
 * Created by PhpStorm.
 * User: geroduppel
 * Date: 03.03.18
 * Time: 12:49
 */
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
    */
    public function index()
    {
        return [];
    }
}
