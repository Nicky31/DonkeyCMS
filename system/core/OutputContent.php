<?php

/*
 * Représente un contenu html manipulé par la classe Output pour être envoyé au navigateur
 * Facilite les traitements sur le contenu
 */

class OutputContent
{
    // Différentes positions possibles au sein de l'arbre html
    const POS_AT         = 0, // Indique directement la position ciblé
          POS_MOVE       = 1, // Déplace le curseur de X caractères. Param néfatif pour reculer
          POS_FIRST_TAG  = 2, // Position de la première occurence de la balise indiquée
          POS_LAST_TAG   = 3, // Position de la dernière occurence de la balise indiquée
          POS_START      = 4, // Début du contenu
          POS_END        = 5; // Fin du contenu
    // Curseur interne : n° du caractère
    private        $_cursor       = 0;
    // Variables utilisables dans tous les templates
    private static $_globalParams = array();
    // Contenu html
    private        $_content      = '';

    public function __construct($templatePath = NULL, $params = array())
    {
        if($templatePath)
        {
            $this->loadTemplate($templatePath, $params);
        }

        return $this;
    }


    public function loadTemplate($templatePath, $params = array())
    {
        if(!file_exists(appendExt($templatePath)))
        {
            throw new DkException('template.inexistant', appendExt($templatePath));
        }

        $this->_content = substr_replace($this->_content, $this->generateHtml($templatePath, $params), $this->_cursor, 0);
        $this->_cursor = strlen($this->_content);

        return $this;
    }

    /*
     * Positionne le curseur interne à une position du contenu actuel afin d'y faciliter les opérations
     */
    public function at($position, $param = NULL)
    {
        switch($position)
        {
            case self::POS_AT:
                $this->_cursor = $param;
            break;
            case self::POS_MOVE:
                $this->_cursor += $param;
            break;
            case self::POS_FIRST_TAG:
                $this->_cursor = (int)strpos($this->_content, $param);
            break;
            case self::POS_LAST_TAG:
                $this->_cursor = (int)strrpos($this->_content, $param);
            break;
            case self::POS_START:
                $this->_cursor = 0;
            break;
            case self::POS_END:
                $this->_cursor = strlen($this->_content);
            break;
        }

        if($this->_cursor > strlen($this->_content))
            $this->_cursor = strlen($this->_content);
        else if($this->_cursor < 0)
            $this->_cursor = 0;
        
        return $this;
    }

    /*
     * Ajoute le contenu html (ou autre) $content directement sans passer par des templates
     */
    public function insertContent($content, $evalPhp = FALSE, $params = array())
    {
        if($evalPhp)
        {
            extract($params);
            extract(self::$_globalParams);
            ob_start();
            eval($content);
            $content = ob_get_clean();
        }

        $this->_content = substr_replace($this->_content, $content, $this->_cursor, 0);
        $this->_cursor = strlen($this->_content);
        return $this;
    }

    private function generateHtml($templatePath, $params = array())
    {
        extract(self::$_globalParams);
        extract($params);
        ob_start();
        include $templatePath;
        $content = ob_get_clean();
        return $content;
    }

    public static function assignGlobal($k, $v)
    {
        self::$_globalParams[$k] = $v;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function __toString()
    {
        return $this->getContent();
    }

    public function clear()
    {
        $this->_content = '';
        $this->_cursor = 0;
        return $this;
    }
}