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

    /**
     * name of the website
     * @const string
     */
    const SITENAME = "SutekinaBox";

    /**
     * Web site default email (security mails : confirm,recover ...)
     * @const string
     */
    const SITEEMAIL = "sutekinabox@frogg.fr";

    ################
    # ADMIN CONFIG #
    ################

    /**
     * number of box displayed in bloxes list
     * @const int
     */
    const NBBOXPERPAGE = 3;

    ###############
    # USER CONFIG #
    ###############

    /**
     * name of security Entity
     * @const string
     */
    const USERENTITY = "User";

    /**
     * list of available roles
     * @const array
     */
    const USERROLES = ['ROLE_MEMBER','ROLE_MARKETING', 'ROLE_MANAGER', 'ROLE_ADMIN' ];

    ##################
    # SERVICES MAILS #
    ##################

    /**
     * marketing email for workflow alerts
     * @const string
     */
    const MAILMARKETING = "marketing@frogg.fr";

    /**
     * manager email for workflow alerts
     * @const string
     */
    const MAILMANAGER = "manager@frogg.fr";

    /**
     * provider email for workflow alerts
     * @const string
     */
    const MAILPROVIDER = "provider@frogg.fr";

    ############
    # PRODUCTS #
    ############

    /**
     * name of the php product database YAML format
     * @const string
     */
    const PRODUCTFILE = 'products.yaml';

    /**
     * name of the php cached file in var/cache/(+dev on dev env)
     * @const string
     */
    const PRODUCTCACHEFILE = 'cache-products.php';
}
