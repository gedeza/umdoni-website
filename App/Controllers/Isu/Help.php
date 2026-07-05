<?php

namespace App\Controllers\Isu;

use Core\View;

/**
 * Help — ISU console guides / onboarding.
 *
 * A single reference page explaining each section and the common workflows,
 * so first-time admins can get productive quickly.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class Help extends Guarded
{
    public function indexAction()
    {
        View::render('isu/help/index.php', [
            'page_title' => 'Help & Guides',
            'page_desc'  => 'What each part of the console does and how to use it.',
        ], 'isu');
    }
}
