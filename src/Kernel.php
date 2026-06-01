<?php

// src/Kernel.php
// Noyau (Kernel) de l'application Symfony, responsable du chargement de la configuration, des bundles et de l'initialisation du framework.

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
