<?php
namespace App;

/**
 * Class SiteConfig
 * @package App
 */
class SiteConfig
{
    ##################
    # WEBSITE CONFIG #
    ##################

    const SITENAME = "SutekinaBox";

    const SITEEMAIL = "sutekinabox@frogg.fr";

    ###############
    # USER CONFIG #
    ###############

    const USERENTITY = "User";

    const USERROLES = ['ROLE_MEMBER','ROLE_MARKETING', 'ROLE_MANAGER', 'ROLE_ADMIN' ];

    ################
    # ADMIN CONFIG #
    ################

    const NBBOXPERPAGE = 3;

    ##################
    # SERVICES MAILS #
    ##################

    const MAILMARKETING = "marketing@frogg.fr";

    const MAILMANAGER = "manager@frogg.fr";

    const MAILPROVIDER = "provider@frogg.fr";

}