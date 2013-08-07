<?php
require('simpletest_inc.php');
require('../system/core/Finder.php');

define('BASE_URL', 'localhost/DonkeyCMS/');

class FinderTest extends UnitTestCase
{
	function testInit()
    {
    	Finder::init(realpath('..'), BASE_URL, array(
    		'lib' => array('system/libraries/'),
    		'helper'   => array('system/helpers/')
		));
    }

    function testAddType()
    {
    	Finder::addFileType('view', array('modules/%s/themes/%s/views/'));
    	Finder::addFileType('helper', array('inc/helpers/'));
    	
    	$this->assertTrue( count(Finder::$_fileTypes) == 3, 'Ajout de nouveau fileType manuellement');
    	$this->assertTrue( count(Finder::$_fileTypes['helper'] == 2), 'Ajout de chemin sur fileType existant');

    	Finder::addFileType('test', array('tests/'));
    }

    function testGet()
    {
    	$this->assertTrue( Finder::testPath('simpletest_inc.php') == realpath('simpletest_inc.php'), 'Recherche de chemin de fichier');
        $this->assertTrue( Finder::testUrl('simpletest_inc.php') == BASE_URL.'tests/simpletest_inc.php', 'Recherche d\'url de fichier');
    }

    function testGeneratedFunctions()
    {
        Finder::generateAccessFunctions();
        $this->assertTrue( testPath('simpletest_inc.php') == realpath('simpletest_inc.php'), 'Recherche de chemin de fichier par fonction générée');
        $this->assertTrue( testUrl('simpletest_inc.php') == BASE_URL.'tests/simpletest_inc.php', 'Recherche d\'url de fichier par fonction générée');
    }
}

$test = new FinderTest();
$test->run(new HtmlReporter());