<?php
namespace LocalbrandingDe\SiteTeamBundle\Module;
use  LocalbrandingDe\SiteTeamBundle\Classes\Backup;

class BackupButtonModule extends \Module
{
    
    /**
     * @var string
     */
    protected $strTemplate = 'backupButton';
    
    /**
     * Displays a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        
        if (TL_MODE == 'BE') {
            $template = new \BackendTemplate('be_wildcard');
            
            $template->wildcard = '### '.utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['helloWorld'][0]).' ###';
            //$template->title = $this->headline;
            $template->title = "BackupButton";
            $template->id = $this->id;
            $template->link = $this->name;
            $template->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;
            
            return $template->parse();
        }
        
        return parent::generate();
    }
    /**
     * Generates the module.
     */
    protected function compile()
    {
        
        /** @var \PageModel $objPage */
        global $objPage;
        
        
        
        if (isset($_GET['type'])) {
            
            /**
             * if $_POST['action']) is set then we have to handle ajax calls
             *
             * We check if the given $type is an existing method
             * - if yes then call the function
             * - if no just do nothing right now (for the moment)
             */
            $type = $_GET['type'];
            if (method_exists($this, $type)) {
                
                //  $g=new AsyncOperation($this,$type);
                //   file_put_contents("start","");
                $this->$type();
            }
        } else {
            
            // calendar-extended-bundel assets
            $assets_path = '/assets';
            $bundle_path = '/bundles/siteteam';
            // JS files
            //  $GLOBALS['TL_JAVASCRIPT'][] = $assets_path. '/js/lb_fe_bundleButton.js';
            // fullcalendar 3.9.0
            //$assets_fc = '/fullcalendar-3.9.0';
            // font-awesome 4.7.0
            //$assets_fa = '/font-awesome-4.7.0';
            if ($objPage->hasJQuery !== '1') {
                $GLOBALS['TL_JAVASCRIPT'][] = $assets_path .'assets/jquery/js/jquery.min.js';
            }
            $GLOBALS['TL_JAVASCRIPT'][] = $bundle_path .'/js/languageChange.js';
            // Load jQuery if not active
            if ($objPage->hasJQuery !== '1')
            {
                //  $GLOBALS['TL_JAVASCRIPT'][] = $assets_path . $assets_fc . '/lib/jquery.min.js|static';
            }
            //  $GLOBALS['TL_CSS'][] = 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css';
            
            
            // $this->Template->token = $this->generateToken();
            
            
            $bundlePath = "../vendor/localbranding-de/siteteam-bundle/src/";
            
            $this->Template = new \FrontendTemplate($this->strTemplate);
            $lang=$GLOBALS['TL_LANGUAGE'];
            if(isset($_SESSION['LB']['lang']))
            {    $lang=$_SESSION['LB']['lang'];
            
            }
            $this->Template->lang= $lang;
            file_put_contents("asas","asdas");
            
        }
    }
        private function doBackup()
        {
            $button = new Backup();
            $button->doBackup();
        }
    
    
}