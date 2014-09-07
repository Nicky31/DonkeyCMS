<?php

/*
 * Mini analyseur PHP permettant l'extraction de noms de classes avec leurs namespaces
 * d'un fichier php indiqué par l'utilisateur
 */

class PHPParser
{
    // Chemin absolu du fichier php à parser
    protected $_srcFile       = NULL; 
    // Tokens du fichier php à parser
    protected $_phpTokens     = array();
    // Namespace courant (par rapport à la position du curseur interne du tableau)
    protected $_curNs         = '\\';
    // Tableau des données devant être extraites du fichier par le parser
    protected $_datas         = array();

    public function __construct($srcFile = NULL)
    {
        if($srcFile != NULL)
            $this->setSourceFile($srcFile);
    }

    public function setSourceFile($srcFile)
    {
        $this->_srcFile = $srcFile;
        $this->_phpTokens = token_get_all(file_get_contents($srcFile));
        $this->_curNs = '\\';
    }

    public function extractDatas()
    {
        if(empty($this->_phpTokens))
            return FALSE;

        while(($token = current($this->_phpTokens)) !== FALSE)
        {
            if(!is_array($token))
            {
                next($this->_phpTokens);
                continue;
            }

            $this->parseToken($token);
        }

        return $this->_datas;
    }

    private function parseToken($token)
    {
        switch ($token[0])
        {
            case T_CLASS :
            case T_INTERFACE :
                $this->handleClassToken();
                break;

            case T_NAMESPACE :
                $this->handleNamespaceToken();
                break;
        }

        next($this->_phpTokens);
    }

    private function handleClassToken()
    {
        while(($curToken = next($this->_phpTokens)) !== FALSE)
        {
            if($curToken[0] != T_STRING)
                continue;

            $this->_datas[rtrim($this->_curNs, '\\') . '\\' . $curToken[1]] = $this->_srcFile;
            break;
        }
    }

    // Note : considère les namespace entre accolade comme s'ils n'en avaient pas
    private function handleNamespaceToken()
    {
        $this->_curNs = '\\';
        $retrieved = FALSE;
        while(($curToken = next($this->_phpTokens)) !== FALSE)
        {
            if($curToken[0] == T_STRING)
            {
                $this->_curNs .= $curToken[1];
                $retrieved = TRUE;
            }
            else if($curToken[0] == T_NS_SEPARATOR)
            {
                if($this->_curNs != '\\')
                    $this->_curNs .= '\\';
            }
            else if($curToken[0] == ';') // Fin de la déclaration
                break;
            /*
             * Un identifiant namespace a déjà été récupéré et plus rien ne correspond 
             * à une suite d'identifiant ; on sort de la boucle :
             * En effet, la déclaration d'un namespace peut se faire par accolade => sans ;
             */
            else if($retrieved) 
            {
                prev($this->_phpTokens);
                break;
            }
            else
                continue;
        }
    }
}