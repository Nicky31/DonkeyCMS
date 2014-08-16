<?php

/*
 * Gestion des exceptions
 */

abstract class ExceptionManager
{
    private static $_exception = NULL;
    // Observers : Classes appellées à chaque exception pour divers traitement (logs)
    private static $_observers = array();
    // Manager activé ? 
    private static $_enable    = TRUE;
    // Dossier des logs d'exception
    const          LOGS_DIR    = 'exceptions';

    public static function init()
    {
        // Traductions des exceptions
        Lang::loadTranslations('exceptions', 'system/langs');

        // Load observers
        self::attach(new FileWriter(LOG_PATH . self::LOGS_DIR . SEP));
    }
    
    public static function enable($enable = TRUE)
    {
        self::$_enable = $enable;
    }
    
    public static function disable()
    {
        self::enable(FALSE);
    }
    
    public static function attach($observer)
    {
        self::$_observers[] = $observer;
    }

    public static function getException()
    {
        return self::$_exception;
    }

    public static function notify()
    {
        foreach (self::$_observers as $observer)
        {
            $observer->update(__CLASS__);
        }
    }

    public static function handleException(Exception $e)
    {     
        // Traitement de l'exception si le manager est actif pour ne pas réveler certaines données en production
        if(self::$_enable)
        {
            self::printException($e);
            
            if(AntiFlood::isFlood() === FALSE)
            {
                // Ne log l'exception que s'il ne s'agit pas d'un flood destiné a saturer le serveur :
                self::notify(); 
            }
        }
        
        exit;
    }
    
    public static function printException($e)
    {
        $traceArray = $e->getTrace();
        array_unshift($traceArray, array(
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ));

        $trace = '<ol style="list-style-type:arabic-numbers;">' . PHP_EOL;
        foreach($traceArray as $v)                      
        {

            $trace .= '<li> <pre>';
            if(!empty($v['line']) && !empty($v['file']))
                $trace .= 'Ligne <b>' . $v['line'] .'</b> du fichier <b>' . $v['file'] .'</b>';
            else
                $trace .= '<b>PHP</b>';
            
            if(!empty($v['function']))
            {
                $class = !empty($v['class']) ? ' méthode ' . $v['class'] .'::' : ' fonction ';
                $trace .= ' : appel de la <b>' . $class . $v['function'];
                if(!empty($v['args']))
                {
                    $trace .= '(';
                    $first = TRUE;
                    foreach($v['args'] as $arg)
                    {
                        if(!$first)
                            $trace .= ', ';
                        else
                            $first = FALSE;

                        if(is_array($arg))
                            $trace .= 'Array';
                        else if(is_string($arg) || is_int($arg))
                            $trace .= $arg;
                        else if(is_object($arg))
                            $trace .= 'Object::' . get_class($arg);
                        else
                            $trace .= 'Unknow Type';
                    }
                    $trace .= ')';
                }
                $trace .= '</b>';
            }
            $trace .= '</pre>' . PHP_EOL;
        }
        $trace .= '</ol>'; 

        ob_start();
        eval(file_get_contents(__FILE__, FALSE, NULL, __COMPILER_HALT_OFFSET__));    
        self::$_exception = ob_get_clean() . "\n\n";
        echo self::$_exception;
    }
}

/*
 * Observer permettant la sauvegarde des exceptions dans des fichiers htmls
 */
class FileWriter
{
    private $_dirPath = NULL;
    
    public function __construct($fullPath)
    {
        $this->_dirPath = $fullPath;
    }
    
    /*
     * Fonction appellée dès lors de la rencontre d'une exception
     * $subjectClassName = Nom de la classe ExceptionManager > Simple soucis de souplesse
     */
    public function update($subjectClassName)
    {
        $this->write($subjectClassName::getException(), $this->_dirPath.date('d-m-Y-H:i:s').'.html');
    }
    
    public function write($content, $fileName)
    {
        file_put_contents($fileName, $content, FILE_APPEND);
    }
}

__halt_compiler();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf8" />
        <title>Exception rencontrée</title>
    </head>

    <body>
        <fieldset style="background-color: #F6DDDD; border: 1px solid #FD1717; color: #8C2E0B; padding: 10px;">
            <legend><h4>Exception survenue</h4></legend>

            <table style="width:100%; border-collapse: collapse;">
                <tr style="border: 1px solid #FD1717;">
                    <td style="border: 1px solid black; background-color: #E0C9C9;"><b>Message : </b></td>
                    <td style="text-align:center;">
                        <?php echo $e->getMessage(); ?>
                    </td>
                </tr>
                
                <tr style="border: 1px solid #FD1717;">
                    <td style="border: 1px solid black; background-color: #E0C9C9;"><b>Appel : </b></td>
                    <td style="">
                        <?php echo $trace; ?>
                    </td>
                </tr>
            </table>
            
        </fieldset>
    </body>
</html>