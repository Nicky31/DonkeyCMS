<!DOCTYPE html>
<html>
    <head>
        <title>DonkeyCMS</title>
        <meta charset="utf8" />
        <style>
            header { border-radius:40px 40px 0px 0px; margin:auto; text-align:center; height:100px; background-color: rgba(100,100,100,0.5); }
            #menu { display:inline-block; vertical-align: top; width:200px; background-color: rgba(0,255,0,0.5); }
            #content { background-color: rgba(0,0,255,0.5);display:inline-block; vertical-align: top; width: 80%; padding:5px; }
            footer { border-radius: 0px 0px 100px 100px; text-align: center; background-color: rgba(255,0,255,0.5); }
        </style>
    </head>
    
    <body>
        <header>Custom theme</header>
        <div style="width:1100px; margin:auto;">
            <div id="menu">
                <ul>
                    <li><?php echo url('English', 'home/home/selectLang','lang=en');?></li>
                    <li><?php echo url('French', 'home/home/selectLang','lang=fr');?></li>
                    <li><?php echo url('Theme custom', 'home/home/selectTheme','theme=custom');?></li>
                    <li><?php echo url('Theme par defaut', 'home/home/selectTheme','theme=default');?></li>
                    <li><?php echo url('Controller Home');?></li>
                    <li><?php echo url('Controller Test','home/test');?></li>
                </ul>
            </div>

            <div id="content">
                <?php echo $content; ?>
            </div>
        </div>
        <footer>
            Footer
        </footer>
        
    </body>
    
</html>