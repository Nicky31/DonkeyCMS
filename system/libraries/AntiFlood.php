<?php

/*
 * Classe filtrant le spam sur une ressource précise du CMS : 
 * Anti-spam avant un stockage de logs , etc...
 */

abstract class AntiFlood
{
    const FLOOD_INTERVAL = 60;
    const LOGS_DIR       = 'AntiFlood';

    public static function isFlood($interval = self::FLOOD_INTERVAL)
    {
        $isFlood = FALSE;
        list($callContext) = debug_backtrace();
        $logsPath = LOG_PATH . self::LOGS_DIR . SEP . getIp() . '.txt';
        
        if(file_exists($logsPath) && (time() - filemtime($logsPath)) < $interval)
        {
            $logsArray = explode(PHP_EOL, file_get_contents($logsPath));
            // Le visiteur a déjà accédé à la ressource protégée il y a moins de $interval secondes
            foreach($logsArray as $logs)
            {
                if(substr_count($logs, '@') < 2)
                    continue;
                
                list($fileName, $line, $time) = explode('@', $logs);
                if($fileName == $callContext['file'] && $line == $callContext['line'] && (time() - $time) < $interval)
                {
                    $isFlood = TRUE;
                    break;
                }
            }
        }
        
        self::addLog($logsPath, $callContext);
        return $isFlood;
    }
    
    private static function addLog($fileName, $context)
    {
        file_put_contents($fileName, PHP_EOL . $context['file'] .'@'. $context['line'] .'@'. time(), FILE_APPEND);
    }
}
 